<?php
namespace app\controller;

use app\service\GeoNewsPublisher;
use app\service\GeoPublishException;

class GeoPublishApi extends BaseController
{
    public function capabilities(): void
    {
        $this->requireMethod('GET');
        $publisher = $this->publisher();
        $this->authenticate($publisher);
        $this->json($publisher->capabilities());
    }

    public function publish(): void
    {
        $this->requireMethod('POST');
        $publisher = $this->publisher();
        $this->authenticate($publisher);
        try {
            $result = $publisher->publish($this->jsonBody(), $this->header('Idempotency-Key'));
            $this->json($result['response'], $result['created'] ? 201 : 200);
        } catch (GeoPublishException $error) {
            $this->error($error);
        }
    }

    public function status(string $id): void
    {
        $this->requireMethod('GET');
        $publisher = $this->publisher();
        $this->authenticate($publisher);
        try {
            $this->json($publisher->status($id));
        } catch (GeoPublishException $error) {
            $this->error($error);
        }
    }

    private function publisher(): GeoNewsPublisher
    {
        return new GeoNewsPublisher(null, $this->config['geo_publish_api'] ?? []);
    }

    private function authenticate(GeoNewsPublisher $publisher): void
    {
        try {
            $publisher->authenticate($this->header('Authorization'));
        } catch (GeoPublishException $error) {
            $this->error($error);
        }
    }

    private function jsonBody(): array
    {
        $maximum = (int)($this->config['geo_publish_api']['max_body_bytes'] ?? 1048576);
        $contentLength = (int)($_SERVER['CONTENT_LENGTH'] ?? 0);
        if ($contentLength > $maximum) {
            $this->error(new GeoPublishException('REQUEST_TOO_LARGE', 'Request body is too large.', 413));
        }
        $body = file_get_contents('php://input', false, null, 0, $maximum + 1);
        if ($body === false || strlen($body) > $maximum) {
            $this->error(new GeoPublishException('REQUEST_TOO_LARGE', 'Request body is too large.', 413));
        }
        $decoded = json_decode($body, true);
        if (!is_array($decoded) || json_last_error() !== JSON_ERROR_NONE) {
            $this->error(new GeoPublishException('REQUEST_INVALID', 'Request body must be a JSON object.', 422));
        }
        return $decoded;
    }

    private function requireMethod(string $expected): void
    {
        if (strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET')) !== $expected) {
            header('Allow: ' . $expected);
            $this->error(new GeoPublishException('METHOD_NOT_ALLOWED', 'HTTP method is not allowed.', 405));
        }
    }

    private function header(string $name): string
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        if ($name === 'Authorization') {
            return (string)($_SERVER[$key] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');
        }
        return (string)($_SERVER[$key] ?? '');
    }

    private function error(GeoPublishException $error): void
    {
        $requestId = preg_replace('/[^A-Za-z0-9._:-]/', '', $this->header('X-Request-Id')) ?: bin2hex(random_bytes(12));
        $this->json([
            'error' => ['code' => $error->errorCode(), 'message' => $error->getMessage()],
            'request_id' => $requestId,
        ], $error->httpStatus());
    }
}
