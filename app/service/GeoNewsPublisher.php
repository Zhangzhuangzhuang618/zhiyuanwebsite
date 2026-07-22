<?php
namespace app\service;

use app\model\Database;

class GeoNewsPublisher
{
    private const ALLOWED_TAGS = [
        'p', 'h2', 'h3', 'ul', 'ol', 'li', 'blockquote', 'strong', 'em', 'a',
        'section', 'aside', 'figure', 'figcaption',
    ];

    private $pdo;
    private $config;
    private $manualTransaction = false;

    public function __construct(?\PDO $pdo = null, array $config = [])
    {
        $this->pdo = $pdo ?: Database::getInstance();
        $siteConfig = $GLOBALS['config'] ?? [];
        $defaults = [
            'enabled' => filter_var(getenv('GEO_PUBLISH_API_ENABLED') ?: '0', FILTER_VALIDATE_BOOLEAN),
            'token_sha256' => getenv('GEO_PUBLISH_TOKEN_SHA256') ?: '',
            'max_body_bytes' => 1024 * 1024,
            'target_nav_id' => (int)(getenv('GEO_PUBLISH_TARGET_NAV_ID') ?: 11),
            'site_url' => (string)($siteConfig['app']['site_url'] ?? 'https://www.zhiyuanbj.cn'),
        ];
        $configured = $config ?: ($siteConfig['geo_publish_api'] ?? []);
        $this->config = array_merge($defaults, $configured);
    }

    public function authenticate(string $authorization): void
    {
        if (empty($this->config['enabled'])) {
            $this->fail('AUTH_REQUIRED', 'GEO publish API is disabled.', 401);
        }
        if (!preg_match('/^Bearer\s+(.+)$/i', trim($authorization), $match)) {
            $this->fail('AUTH_REQUIRED', 'Bearer token is required.', 401);
        }
        $expected = strtolower(trim((string)($this->config['token_sha256'] ?? '')));
        if (!preg_match('/^[a-f0-9]{64}$/', $expected)) {
            $this->fail('AUTH_INVALID', 'GEO publish API token is not configured.', 401);
        }
        $actual = hash('sha256', trim($match[1]));
        if (!hash_equals($expected, $actual)) {
            $this->fail('AUTH_INVALID', 'Bearer token is invalid.', 401);
        }
    }

    public function capabilities(): array
    {
        return ['publish' => true, 'get_status' => true, 'metrics' => false];
    }

    public function publish(array $request, string $idempotencyKey): array
    {
        $idempotencyKey = trim($idempotencyKey);
        if (!preg_match('/^[A-Za-z0-9._:-]{1,128}$/', $idempotencyKey)) {
            $this->fail('REQUEST_INVALID', 'Idempotency-Key is invalid.', 422);
        }
        $validated = $this->validateRequest($request);
        $payload = $validated['payload'];
        $payloadHash = hash('sha256', self::canonicalJson($payload));
        if (!hash_equals($validated['payload_hash'], $payloadHash)) {
            $this->fail('PAYLOAD_HASH_MISMATCH', 'Payload hash does not match payload.', 422);
        }

        $this->beginWriteTransaction();
        try {
            $byKey = $this->findReceipt('idempotency_key', $idempotencyKey);
            if ($byKey) {
                $result = $this->replayOrConflict($byKey, $validated, $payloadHash);
                $this->commit();
                return $result;
            }
            $byVersion = $this->findReceipt('content_version_id', $validated['content_version_id']);
            if ($byVersion) {
                $result = $this->replayOrConflict($byVersion, $validated, $payloadHash);
                $this->commit();
                return $result;
            }

            $nav = $this->requireNewsCategory((int)($this->config['target_nav_id'] ?? 0));
            $articleId = (int)$this->pdo
                ->query('SELECT COALESCE(MAX(id), 0) + 1 FROM ' . $this->table('cms_article'))
                ->fetchColumn();
            $publishedAt = time();
            $url = rtrim((string)($this->config['site_url'] ?? ''), '/') . '/detail/news' . $articleId . '.html';
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                $this->fail('ARTICLE_WRITE_FAILED', 'Public site URL is not configured.', 500);
            }
            $bodyHtml = self::sanitizeHtml($payload['body_html']);
            $this->insertArticle($articleId, $nav, $payload, $bodyHtml, $publishedAt);

            $response = [
                'external_id' => (string)$articleId,
                'status' => 'published',
                'url' => $url,
                'published_at' => gmdate('c', $publishedAt),
            ];
            $statement = $this->pdo->prepare(
                'INSERT INTO ' . $this->table('geo_publish_receipts') .
                ' (idempotency_key, content_version_id, payload_hash, article_id, article_url, published_at, response_json, created_at)'
                . ' VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $statement->execute([
                $idempotencyKey,
                $validated['content_version_id'],
                $payloadHash,
                $articleId,
                $url,
                $publishedAt,
                json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                $publishedAt,
            ]);
            $this->commit();
            return ['created' => true, 'response' => $response];
        } catch (GeoPublishException $error) {
            $this->rollback();
            throw $error;
        } catch (\Throwable $error) {
            $this->rollback();
            throw new GeoPublishException('ARTICLE_WRITE_FAILED', 'Unable to publish the article.', 500);
        }
    }

    public function status(string $articleId): array
    {
        if (!preg_match('/^[1-9][0-9]{0,18}$/', $articleId)) {
            $this->fail('REQUEST_INVALID', 'Article id is invalid.', 422);
        }
        $receipt = $this->findReceipt('article_id', $articleId);
        if (!$receipt) $this->fail('RESOURCE_NOT_FOUND', 'Publication was not found.', 404);
        return $this->decodeResponse($receipt);
    }

    public static function canonicalJson($value): string
    {
        $normalized = self::sortJson($value);
        $json = json_encode($normalized, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) throw new \InvalidArgumentException('Value cannot be encoded as JSON.');
        return $json;
    }

    public static function sanitizeHtml(string $html): string
    {
        if (preg_match('/<(?:script|style|iframe|object|embed)\b|\son[a-z]+\s*=|javascript\s*:/i', $html)) {
            throw new GeoPublishException('REQUEST_INVALID', 'body_html contains unsafe markup.', 422);
        }
        $allowed = '<' . implode('><', self::ALLOWED_TAGS) . '>';
        $html = strip_tags($html, $allowed);
        $html = preg_replace_callback('/<([a-z0-9]+)([^>]*)>/i', function (array $match): string {
            $tag = strtolower($match[1]);
            if (!in_array($tag, self::ALLOWED_TAGS, true)) return '';
            if ($tag !== 'a') return '<' . $tag . '>';
            if (!preg_match('/\shref\s*=\s*(["\'])(.*?)\1/i', $match[2], $hrefMatch)) return '<a>';
            $href = html_entity_decode($hrefMatch[2], ENT_QUOTES, 'UTF-8');
            if (!preg_match('#^https?://#i', $href) || !filter_var($href, FILTER_VALIDATE_URL)) return '<a>';
            return '<a href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '" rel="noopener noreferrer">';
        }, $html);
        $clean = trim((string)$html);
        if ($clean === '' || trim(strip_tags($clean)) === '') {
            throw new GeoPublishException('REQUEST_INVALID', 'body_html is empty.', 422);
        }
        return $clean;
    }

    private function validateRequest(array $request): array
    {
        $allowedTop = ['content_version_id', 'payload_hash', 'payload'];
        if (array_diff(array_keys($request), $allowedTop) || array_diff($allowedTop, array_keys($request))) {
            $this->fail('REQUEST_INVALID', 'Request fields are invalid.', 422);
        }
        $contentVersionId = (string)$request['content_version_id'];
        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $contentVersionId)) {
            $this->fail('REQUEST_INVALID', 'content_version_id is invalid.', 422);
        }
        $payloadHash = strtolower((string)$request['payload_hash']);
        if (!preg_match('/^[a-f0-9]{64}$/', $payloadHash) || !is_array($request['payload'])) {
            $this->fail('REQUEST_INVALID', 'payload_hash or payload is invalid.', 422);
        }
        $payload = $request['payload'];
        $required = ['schema_version', 'platform_code', 'title', 'summary', 'body_html', 'seo_keywords', 'meta_description'];
        if (array_diff(array_keys($payload), $required) || array_diff($required, array_keys($payload))) {
            $this->fail('REQUEST_INVALID', 'Payload fields are invalid.', 422);
        }
        if ($payload['schema_version'] !== 'zhiyuan-news-payload@1' || $payload['platform_code'] !== 'official_site') {
            $this->fail('REQUEST_INVALID', 'Payload schema or platform is invalid.', 422);
        }
        $this->assertText($payload['title'], 20, 60, 'title');
        $this->assertText($payload['summary'], 1, 240, 'summary');
        $this->assertText($payload['body_html'], 1, 500000, 'body_html');
        $this->assertText($payload['meta_description'], 1, 240, 'meta_description');
        if (!is_array($payload['seo_keywords']) || count($payload['seo_keywords']) > 20) {
            $this->fail('REQUEST_INVALID', 'seo_keywords is invalid.', 422);
        }
        $unique = [];
        foreach ($payload['seo_keywords'] as $keyword) {
            $this->assertText($keyword, 1, 40, 'seo_keywords');
            if (isset($unique[$keyword])) $this->fail('REQUEST_INVALID', 'seo_keywords contains duplicates.', 422);
            $unique[$keyword] = true;
        }
        return [
            'content_version_id' => strtolower($contentVersionId),
            'payload_hash' => $payloadHash,
            'payload' => $payload,
        ];
    }

    private function assertText($value, int $minimum, int $maximum, string $field): void
    {
        if (!is_string($value)) $this->fail('REQUEST_INVALID', $field . ' must be a string.', 422);
        $length = mb_strlen($value, 'UTF-8');
        if ($length < $minimum || $length > $maximum) {
            $this->fail('REQUEST_INVALID', $field . ' length is invalid.', 422);
        }
    }

    private function requireNewsCategory(int $id): array
    {
        if ($id < 1) $this->fail('CATEGORY_INVALID', 'Target news category is not configured.', 422);
        $statement = $this->pdo->prepare(
            'SELECT id, pid, title, url_model, status FROM ' . $this->table('cms_nav') . ' WHERE id = ? LIMIT 1'
        );
        $statement->execute([$id]);
        $nav = $statement->fetch(\PDO::FETCH_ASSOC);
        if (!$nav || (int)$nav['status'] !== 1 || $nav['url_model'] !== 'news') {
            $this->fail('CATEGORY_INVALID', 'Target category is not an active news category.', 422);
        }
        return $nav;
    }

    private function insertArticle(int $id, array $nav, array $payload, string $html, int $now): void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO ' . $this->table('cms_article') . ' ('
            . 'id,title,subtitle,content,sketch,seo_title,seo_keyword,seo_content,image,'
            . 'create_time,update_time,delete_time,nav_id,nav_pid,browse,sort,status,state,states,'
            . 'model,type,link,target,rand,lang) VALUES (' . implode(',', array_fill(0, 25, '?')) . ')'
        );
        $statement->execute([
            $id,
            $payload['title'],
            '',
            $html,
            $payload['summary'],
            $payload['title'],
            implode('，', $payload['seo_keywords']),
            $payload['meta_description'],
            '',
            $now,
            $now,
            null,
            (int)$nav['id'],
            (int)$nav['pid'],
            0,
            0,
            1,
            1,
            1,
            'news',
            'default',
            '/detail/news' . $id . '.html',
            '_self',
            0,
            'zh-cn',
        ]);
    }

    private function findReceipt(string $field, string $value): ?array
    {
        if (!in_array($field, ['idempotency_key', 'content_version_id', 'article_id'], true)) {
            throw new \LogicException('Invalid receipt lookup field.');
        }
        $statement = $this->pdo->prepare(
            'SELECT * FROM ' . $this->table('geo_publish_receipts') . ' WHERE ' . $field . ' = ? LIMIT 1'
        );
        $statement->execute([$value]);
        $row = $statement->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    private function replayOrConflict(array $receipt, array $request, string $payloadHash): array
    {
        if (
            !hash_equals((string)$receipt['payload_hash'], $payloadHash) ||
            (string)$receipt['content_version_id'] !== $request['content_version_id']
        ) {
            $this->fail('IDEMPOTENCY_CONFLICT', 'The idempotency key or content version has another payload.', 409);
        }
        return ['created' => false, 'response' => $this->decodeResponse($receipt)];
    }

    private function decodeResponse(array $receipt): array
    {
        $response = json_decode((string)$receipt['response_json'], true);
        if (!is_array($response)) $this->fail('ARTICLE_WRITE_FAILED', 'Stored publication response is invalid.', 500);
        return $response;
    }

    private function beginWriteTransaction(): void
    {
        if ($this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME) === 'sqlite') {
            $this->pdo->exec('BEGIN IMMEDIATE');
            $this->manualTransaction = true;
        } else {
            $this->pdo->beginTransaction();
        }
    }

    private function commit(): void
    {
        if ($this->manualTransaction) {
            $this->pdo->exec('COMMIT');
            $this->manualTransaction = false;
            return;
        }
        $this->pdo->commit();
    }

    private function rollback(): void
    {
        if ($this->manualTransaction) {
            $this->pdo->exec('ROLLBACK');
            $this->manualTransaction = false;
            return;
        }
        if ($this->pdo->inTransaction()) $this->pdo->rollBack();
    }

    private function table(string $name): string
    {
        $prefix = (string)($GLOBALS['config']['database']['prefix'] ?? 'zw_');
        return $prefix . $name;
    }

    private static function sortJson($value)
    {
        if (!is_array($value)) return $value;
        if (array_keys($value) === range(0, count($value) - 1)) {
            return array_map([self::class, 'sortJson'], $value);
        }
        ksort($value, SORT_STRING);
        foreach ($value as $key => $item) $value[$key] = self::sortJson($item);
        return $value;
    }

    private function fail(string $code, string $message, int $status): void
    {
        throw new GeoPublishException($code, $message, $status);
    }
}
