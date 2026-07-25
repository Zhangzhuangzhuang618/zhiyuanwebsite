<?php
namespace app\service;

/**
 * 百度普通收录 URL 推送。
 * 配置中的 api_url 由百度搜索资源平台生成，包含站点和令牌，不能提交到仓库。
 */
class BaiduUrlPush
{
    private $config;
    private $transport;

    public function __construct(array $config = [], ?callable $transport = null)
    {
        $this->config = array_merge([
            'enabled' => false,
            'api_url' => '',
            'timeout' => 5,
        ], $config);
        $this->transport = $transport;
    }

    public function submit(string $url): array
    {
        if (empty($this->config['enabled'])) {
            return ['attempted' => false, 'success' => false, 'message' => '百度推送未启用。'];
        }

        $apiUrl = trim((string)$this->config['api_url']);
        if (!filter_var($url, FILTER_VALIDATE_URL) || !$this->isBaiduApiUrl($apiUrl)) {
            return ['attempted' => false, 'success' => false, 'message' => '百度推送配置无效。'];
        }

        try {
            $response = $this->transport
                ? call_user_func($this->transport, $apiUrl, $url)
                : $this->post($apiUrl, $url);
        } catch (\Throwable $error) {
            return ['attempted' => true, 'success' => false, 'message' => '百度推送请求失败。'];
        }

        $status = (int)($response['status'] ?? 0);
        $body = (string)($response['body'] ?? '');
        $data = json_decode($body, true);
        if ($status !== 200 || !is_array($data) || empty($data['success'])) {
            return ['attempted' => true, 'success' => false, 'message' => '百度未接受该链接。'];
        }

        return [
            'attempted' => true,
            'success' => true,
            'submitted' => (int)$data['success'],
            'remaining' => isset($data['remain']) ? (int)$data['remain'] : null,
            'message' => '百度推送成功。',
        ];
    }

    private function post(string $apiUrl, string $url): array
    {
        if (!function_exists('curl_init')) {
            throw new \RuntimeException('PHP cURL extension is unavailable.');
        }

        $handle = curl_init();
        curl_setopt_array($handle, [
            CURLOPT_URL => $apiUrl,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $url,
            CURLOPT_HTTPHEADER => ['Content-Type: text/plain'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT => max(1, (int)$this->config['timeout']),
        ]);
        $body = curl_exec($handle);
        $status = (int)curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);
        if ($body === false) throw new \RuntimeException('Baidu API request failed.');

        return ['status' => $status, 'body' => $body];
    }

    private function isBaiduApiUrl(string $apiUrl): bool
    {
        $parts = parse_url($apiUrl);
        return is_array($parts)
            && in_array(strtolower((string)($parts['scheme'] ?? '')), ['http', 'https'], true)
            && strtolower((string)($parts['host'] ?? '')) === 'data.zz.baidu.com'
            && ($parts['path'] ?? '') === '/urls'
            && !empty($parts['query']);
    }
}
