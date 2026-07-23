<?php
namespace app\controller;

use app\model\Database;
use PDO;
use Throwable;

/** GEO Content OS 专用新闻发布 API。 */
class GeoPublish
{
    /** @var array */
    private $config;
    /** @var PDO */
    private $pdo;
    /** @var string */
    private $prefix;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->pdo = Database::getInstance();
        $this->prefix = (string)($config['database']['prefix'] ?? 'zw_');
        if (($config['database']['type'] ?? '') === 'sqlite') {
            $this->pdo->exec('PRAGMA busy_timeout = 5000');
        }
    }

    public function capabilities(): void
    {
        $this->requireMethod('GET');
        $this->authenticate();
        $this->ensureSchema();
        $this->requireNewsNavId();
        $this->respond(200, ['get_status' => true, 'metrics' => false, 'publish' => true]);
    }

    public function publish(): void
    {
        $this->requireMethod('POST');
        $this->authenticate();
        $idempotencyKey = trim((string)($_SERVER['HTTP_IDEMPOTENCY_KEY'] ?? ''));
        if (!preg_match('/^[A-Za-z0-9._:-]{1,128}$/', $idempotencyKey)) {
            $this->error(400, 'IDEMPOTENCY_KEY_INVALID', 'Idempotency-Key 缺失或格式不正确');
        }

        $input = $this->readJson();
        $this->requireExactKeys($input, ['content_version_id', 'payload', 'payload_hash']);
        $contentVersionId = trim((string)($input['content_version_id'] ?? ''));
        $payloadHash = strtolower(trim((string)($input['payload_hash'] ?? '')));
        $payload = $input['payload'] ?? null;

        if (!preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$/', $contentVersionId)) {
            $this->error(400, 'CONTENT_VERSION_INVALID', 'content_version_id 格式不正确');
        }
        if (!is_array($payload) || !preg_match('/^[a-f0-9]{64}$/', $payloadHash)) {
            $this->error(400, 'PAYLOAD_INVALID', '发布内容或 payload_hash 格式不正确');
        }
        $this->validatePayload($payload);
        $actualHash = hash('sha256', $this->stableJson($payload));
        if (!hash_equals($payloadHash, $actualHash)) {
            $this->error(409, 'PAYLOAD_HASH_MISMATCH', '发布内容与 payload_hash 不一致');
        }

        $this->ensureSchema();
        $recordTable = $this->table('geo_publish_record');
        $articleTable = $this->table('cms_article');
        $newsNavId = $this->requireNewsNavId();

        try {
            $this->pdo->exec('BEGIN IMMEDIATE TRANSACTION');
            $existing = $this->findExisting($recordTable, $idempotencyKey, $contentVersionId);
            if ($existing) {
                if (
                    !hash_equals((string)$existing['payload_hash'], $payloadHash)
                    || (string)$existing['content_version_id'] !== $contentVersionId
                    || (string)$existing['idempotency_key'] !== $idempotencyKey
                ) {
                    $this->pdo->exec('ROLLBACK');
                    $this->error(409, 'IDEMPOTENCY_CONFLICT', '相同幂等键或内容版本对应了不同内容');
                }
                $response = json_decode((string)$existing['response_json'], true);
                $this->pdo->exec('COMMIT');
                $this->respond(200, $response);
            }

            $articleId = (int)$this->pdo
                ->query('SELECT COALESCE(MAX(id), 0) + 1 FROM ' . $articleTable)
                ->fetchColumn();
            $now = time();
            $publishedAt = date('c', $now);
            $url = rtrim((string)$this->config['app']['site_url'], '/') . '/detail/news' . $articleId . '.html';
            $response = [
                'external_id' => (string)$articleId,
                'published_at' => $publishedAt,
                'status' => 'published',
                'url' => $url,
            ];

            $article = $this->pdo->prepare(
                'INSERT INTO ' . $articleTable . ' '
                . '(id,title,subtitle,content,sketch,seo_title,seo_keyword,seo_content,image,'
                . 'create_time,update_time,delete_time,nav_id,nav_pid,browse,sort,status,state,states,'
                . 'model,type,link,target,rand,lang) '
                . 'VALUES (:id,:title,:subtitle,:content,:sketch,:seo_title,:seo_keyword,:seo_content,'
                . ':image,:create_time,:update_time,NULL,:nav_id,7,0,0,1,1,1,\'news\',\'news\',\'\',\'_self\',0,\'zh-cn\')'
            );
            $article->execute([
                'id' => $articleId,
                'title' => $payload['title'],
                'subtitle' => '',
                'content' => $payload['body_html'],
                'sketch' => $payload['summary'],
                'seo_title' => $payload['title'],
                'seo_keyword' => implode(',', $payload['seo_keywords']),
                'seo_content' => $payload['meta_description'],
                'image' => '',
                'create_time' => $now,
                'update_time' => $now,
                'nav_id' => $newsNavId,
            ]);

            $record = $this->pdo->prepare(
                'INSERT INTO ' . $recordTable . ' '
                . '(idempotency_key,content_version_id,payload_hash,article_id,status,response_json,created_at,updated_at) '
                . 'VALUES (:idempotency_key,:content_version_id,:payload_hash,:article_id,\'published\',:response_json,:created_at,:updated_at)'
            );
            $record->execute([
                'idempotency_key' => $idempotencyKey,
                'content_version_id' => $contentVersionId,
                'payload_hash' => $payloadHash,
                'article_id' => $articleId,
                'response_json' => $this->stableJson($response),
                'created_at' => $publishedAt,
                'updated_at' => $publishedAt,
            ]);
            $this->pdo->exec('COMMIT');
            $this->clearContentCache();
            $this->respond(201, $response);
        } catch (Throwable $error) {
            try {
                $this->pdo->exec('ROLLBACK');
            } catch (Throwable $ignored) {
            }
            error_log('GEO publish failed: ' . $error->getMessage());
            $this->error(500, 'PUBLISH_FAILED', '官网暂时无法保存文章');
        }
    }

    public function status(string $externalId): void
    {
        $this->requireMethod('GET');
        $this->authenticate();
        if (!preg_match('/^[1-9][0-9]{0,18}$/', $externalId)) {
            $this->error(400, 'EXTERNAL_ID_INVALID', '文章 ID 格式不正确');
        }
        $this->ensureSchema();
        $statement = $this->pdo->prepare(
            'SELECT response_json FROM ' . $this->table('geo_publish_record') . ' WHERE article_id = ? LIMIT 1'
        );
        $statement->execute([(int)$externalId]);
        $response = $statement->fetchColumn();
        if ($response === false) {
            $this->error(404, 'ARTICLE_NOT_FOUND', '没有找到对应的自动发布文章');
        }
        $this->respond(200, json_decode((string)$response, true));
    }

    private function authenticate(): void
    {
        $settings = $this->config['geo_publish'] ?? [];
        $expectedHash = strtolower(trim((string)($settings['token_sha256'] ?? '')));
        if (empty($settings['enabled']) || !preg_match('/^[a-f0-9]{64}$/', $expectedHash)) {
            $this->error(503, 'API_DISABLED', '官网自动发布 API 尚未启用');
        }
        $authorization = trim((string)($_SERVER['HTTP_AUTHORIZATION'] ?? ''));
        if (!preg_match('/^Bearer\s+(.+)$/i', $authorization, $matches)) {
            $this->unauthorized();
        }
        $token = trim((string)$matches[1]);
        if ($token === '' || !hash_equals($expectedHash, hash('sha256', $token))) {
            $this->unauthorized();
        }
    }

    private function unauthorized(): void
    {
        header('WWW-Authenticate: Bearer');
        $this->error(401, 'UNAUTHORIZED', '发布令牌无效');
    }

    private function validatePayload(array $payload): void
    {
        $this->requireExactKeys($payload, [
            'body_html', 'meta_description', 'platform_code', 'schema_version',
            'seo_keywords', 'summary', 'title',
        ]);
        if (($payload['platform_code'] ?? null) !== 'official_site'
            || ($payload['schema_version'] ?? null) !== 'zhiyuan-news-payload@1') {
            $this->error(400, 'PAYLOAD_SCHEMA_INVALID', '发布内容版本或平台不正确');
        }
        $this->requireText($payload['title'] ?? null, 20, 60, 'title');
        $this->requireText($payload['summary'] ?? null, 1, 240, 'summary');
        $this->requireText($payload['meta_description'] ?? null, 1, 240, 'meta_description');
        if (!is_string($payload['body_html'] ?? null)
            || strlen($payload['body_html']) < 1 || strlen($payload['body_html']) > 500000) {
            $this->error(400, 'BODY_INVALID', '正文为空或长度超限');
        }
        if (preg_match('/<\s*(script|iframe|object|embed)\b|\son[a-z]+\s*=|javascript\s*:/i', $payload['body_html'])) {
            $this->error(400, 'BODY_UNSAFE', '正文包含不允许的脚本或嵌入内容');
        }
        if (!is_array($payload['seo_keywords'] ?? null) || count($payload['seo_keywords']) > 20) {
            $this->error(400, 'SEO_KEYWORDS_INVALID', 'SEO 关键词格式不正确');
        }
        $unique = [];
        foreach ($payload['seo_keywords'] as $keyword) {
            $this->requireText($keyword, 1, 40, 'seo_keywords');
            if (isset($unique[$keyword])) {
                $this->error(400, 'SEO_KEYWORDS_INVALID', 'SEO 关键词不能重复');
            }
            $unique[$keyword] = true;
        }
    }

    private function requireText($value, int $minimum, int $maximum, string $field): void
    {
        if (!is_string($value)) {
            $this->error(400, 'PAYLOAD_INVALID', $field . ' 格式不正确');
        }
        $length = mb_strlen($value, 'UTF-8');
        if ($length < $minimum || $length > $maximum) {
            $this->error(400, 'PAYLOAD_INVALID', $field . ' 长度不正确');
        }
    }

    private function requireExactKeys(array $value, array $expected): void
    {
        $actual = array_keys($value);
        sort($actual, SORT_STRING);
        sort($expected, SORT_STRING);
        if ($actual !== $expected) {
            $this->error(400, 'REQUEST_SCHEMA_INVALID', '请求字段不符合接口契约');
        }
    }

    private function readJson(): array
    {
        $raw = file_get_contents('php://input');
        if (!is_string($raw) || $raw === '' || strlen($raw) > 600000) {
            $this->error(400, 'REQUEST_BODY_INVALID', '请求正文为空或长度超限');
        }
        $value = json_decode($raw, true);
        if (!is_array($value) || json_last_error() !== JSON_ERROR_NONE) {
            $this->error(400, 'REQUEST_BODY_INVALID', '请求正文不是有效 JSON');
        }
        return $value;
    }

    private function findExisting(string $table, string $idempotencyKey, string $contentVersionId): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT idempotency_key,content_version_id,payload_hash,response_json FROM ' . $table
            . ' WHERE idempotency_key = :idempotency_key OR content_version_id = :content_version_id LIMIT 1'
        );
        $statement->execute([
            'idempotency_key' => $idempotencyKey,
            'content_version_id' => $contentVersionId,
        ]);
        $row = $statement->fetch();
        return $row === false ? null : $row;
    }

    private function ensureSchema(): void
    {
        $this->pdo->exec(
            'CREATE TABLE IF NOT EXISTS ' . $this->table('geo_publish_record') . ' ('
            . 'id INTEGER PRIMARY KEY AUTOINCREMENT,'
            . 'idempotency_key TEXT NOT NULL UNIQUE,'
            . 'content_version_id TEXT NOT NULL UNIQUE,'
            . 'payload_hash TEXT NOT NULL,'
            . 'article_id INTEGER NOT NULL UNIQUE,'
            . 'status TEXT NOT NULL,'
            . 'response_json TEXT NOT NULL,'
            . 'created_at TEXT NOT NULL,'
            . 'updated_at TEXT NOT NULL)'
        );
    }

    private function requireNewsNavId(): int
    {
        $navId = (int)($this->config['geo_publish']['news_nav_id'] ?? 0);
        $statement = $this->pdo->prepare(
            'SELECT child.id FROM ' . $this->table('cms_nav') . ' AS child '
            . 'LEFT JOIN ' . $this->table('cms_nav') . ' AS parent ON parent.id = child.pid '
            . 'WHERE child.id = ? AND child.status = 1 '
            . "AND (child.url_model = 'news' OR parent.url_model = 'news') LIMIT 1"
        );
        $statement->execute([$navId]);
        if ($statement->fetchColumn() === false) {
            $this->error(503, 'NEWS_NAV_INVALID', '官网新闻栏目配置无效');
        }
        return $navId;
    }

    private function stableJson($value): string
    {
        return json_encode(
            $this->sortJson($value),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }

    private function sortJson($value)
    {
        if (!is_array($value)) {
            return $value;
        }
        if ($value === [] || array_keys($value) === range(0, count($value) - 1)) {
            return array_map([$this, 'sortJson'], $value);
        }
        ksort($value, SORT_STRING);
        foreach ($value as $key => $child) {
            $value[$key] = $this->sortJson($child);
        }
        return $value;
    }

    private function clearContentCache(): void
    {
        foreach (glob(RUNTIME_PATH . 'cache/*.php') ?: [] as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }

    private function requireMethod(string $method): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== $method) {
            header('Allow: ' . $method);
            $this->error(405, 'METHOD_NOT_ALLOWED', '请求方法不受支持');
        }
    }

    private function table(string $name): string
    {
        return $this->prefix . $name;
    }

    private function respond(int $status, array $body): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        echo $this->stableJson($body);
        exit;
    }

    private function error(int $status, string $code, string $message): void
    {
        $this->respond($status, ['code' => $code, 'message' => $message]);
    }
}
