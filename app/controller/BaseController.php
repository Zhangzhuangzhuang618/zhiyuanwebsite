<?php
namespace app\controller;

use app\model\CmsNav;
use app\model\CmsExpand;
use app\model\CmsProduct;
use app\model\CmsArticle;
use app\model\SystemConfig;
use app\model\CmsBanner;

/**
 * 控制器基类 - 提供公共视图赋值和工具方法
 */
abstract class BaseController
{
    protected $config;
    protected $siteConfig;

    public function __construct(array $config)
    {
        $this->config = $config;
        // 延迟加载配置，避免递归
    }

    protected function initConfig(): void
    {
        if ($this->siteConfig !== null) return;
        $configModel = new SystemConfig();
        $this->siteConfig = $configModel->getAllConfig();
    }

    /**
     * 渲染视图
     */
    protected function render(string $view, array $data = []): void
    {
        $this->initConfig();
        // 公共变量
        $commonData = $this->getCommonData();
        $data = array_merge($commonData, $data);
        $data['canonical_url'] = $data['canonical_url'] ?? $this->currentUrl();
        $pageSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $data['page_title'] ?? $this->siteConfig['company_name'] ?? '广州志远搬家服务有限公司',
            'url' => $data['canonical_url'],
            'inLanguage' => 'zh-CN',
            'isPartOf' => ['@id' => $this->siteUrl('/#website')],
        ];
        $data['structured_data'] = array_merge(
            $this->organizationSchemas(),
            [$pageSchema],
            $data['structured_data'] ?? []
        );
        extract($data);

        $viewFile = VIEW_PATH . $view . '.php';
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            header('HTTP/1.1 404 Not Found');
            echo '<h1>模板不存在: ' . htmlspecialchars($view) . '</h1>';
        }
    }

    /**
     * 获取所有页面公共数据
     */
    protected function getCommonData(): array
    {
        $navModel = new CmsNav();
        $expandModel = new CmsExpand();
        $productModel = new CmsProduct();
        $bannerModel = new CmsBanner();
        $config = $this->siteConfig;

        return [
            'site' => [
                'name'        => $config['company_name'] ?? $config['site_name'] ?? '广州志远搬家服务有限公司',
                'phone'       => $config['web_call'] ?? '020-85627757',
                'mobile'      => $config['web_mobile'] ?? $config['web_phone'] ?? '18924177677',
                'email'       => $config['web_email'] ?? '',
                'address'     => $config['web_address'] ?? '',
                'copyright'   => $config['site_copyright'] ?? '',
                'icp'         => $config['site_beian'] ?? '',
                'logo'        => $config['web_logo'] ?? $config['web_logo_image'] ?? '/upload/20250316/343c6ff6bcb0d1dd7a9a4989741d35ea.png',
                'foot_logo'   => $config['foot_logo'] ?? '',
                'wechat_code' => $config['web_wechat'] ?? $config['wechat_code'] ?? '/upload/20250119/aac926eb7ad1fa7d016177b812a7ccee.jpg',
                'wechat_code2'=> $config['web_wechat2'] ?? $config['wechat_code2'] ?? '/upload/20250119/aac926eb7ad1fa7d016177b812a7ccee.jpg',
                'domain'      => $config['web_domain'] ?? $config['site_url'] ?? '',
                'default_img' => $config['default_image'] ?? '',
                'hot_keywords'=> isset($config['hot_keyword']) ? explode('，', $config['hot_keyword']) : [],
                'online_chat' => $config['web_online'] ?? $config['online_chat'] ?? '',
            ],
            'nav'        => $navModel->getMainNav(),
            'foot_nav'   => $navModel->getFooterNav(),
            'banners'    => $bannerModel->getBanners('home'),
            'phone_list' => $expandModel->getByParent(2, 'phone_list'),
            'home_features' => $expandModel->getByParent(10, 'home_features'),
            'footer_services' => $productModel->select(['status' => 1], 'id, title, link, target', 'sort ASC, id DESC', 6),
            'city_list'  => json_decode($config['son_domain_list'] ?? '', true) ?: ($this->config['city_domains'] ?? []),
            'service_cities' => $this->config['service_cities'] ?? [],
            'now_city'   => __CITY__,
            'now_lang'   => __LANG__,
            'config'     => $this->config,
        ];
    }

    /** Build the parent/children structure used by the original front-end templates. */
    protected function getExpandSection(int $id): array
    {
        $model = new CmsExpand();
        $section = $model->find($id) ?: [];
        $section['child_id'] = $model->getByParent($id);
        return $section;
    }

    /** Data shared by the original news and cases sidebar. */
    protected function getNewsSidebar(): array
    {
        $articleModel = new CmsArticle();
        $productModel = new CmsProduct();

        return [
            'CJWT' => $this->getExpandSection(52),
            'left_hot' => $productModel->select(
                ['status' => 1],
                'id, title, subtitle, image, link, target',
                'sort ASC, id ASC',
                8
            ),
            'ranking_news' => $articleModel->getLatest(8),
        ];
    }

    protected function seoTitle(string $title): string
    {
        return $title . '_广州天河搬家公司_工厂搬家_仓库搬家_日式搬家-广州志远搬家';
    }

    /** Canonical URLs always use the configured public domain, never an untrusted Host header. */
    protected function siteUrl(string $path = '/'): string
    {
        $this->initConfig();
        $domain = trim((string)($this->siteConfig['web_domain'] ?? 'www.zhiyuanbj.cn'));
        $domain = preg_replace('#^https?://#i', '', $domain);
        return 'https://' . rtrim($domain, '/') . '/' . ltrim($path, '/');
    }

    protected function currentUrl(): string
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        return $this->siteUrl($path);
    }

    protected function absoluteUrl(string $url): string
    {
        if ($url === '' || preg_match('#^https?://#i', $url)) return $url;
        return $this->siteUrl($url);
    }

    /** Schema shared by every public page. Keep claims limited to configured business details. */
    private function organizationSchemas(): array
    {
        $this->initConfig();
        $name = $this->siteConfig['company_name'] ?? '广州志远搬家服务有限公司';
        $organization = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            '@id' => $this->siteUrl('/#organization'),
            'name' => $name,
            'alternateName' => '志远搬家',
            'url' => $this->siteUrl('/'),
            'logo' => $this->absoluteUrl($this->siteConfig['web_logo'] ?? '/upload/20250316/343c6ff6bcb0d1dd7a9a4989741d35ea.png'),
            'telephone' => '+86-' . ($this->siteConfig['web_call'] ?? '020-85627757'),
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $this->siteConfig['web_address'] ?? '广州市天河区棠东东路御富科贸园',
                'addressLocality' => '广州',
                'addressRegion' => '广东省',
                'addressCountry' => 'CN',
            ],
        ];
        if (!empty($this->siteConfig['web_email'])) $organization['email'] = $this->siteConfig['web_email'];

        return [$organization, [
            '@context' => 'https://schema.org',
            '@type' => 'MovingCompany',
            '@id' => $this->siteUrl('/#moving-company'),
            'name' => $name,
            'url' => $this->siteUrl('/'),
            'telephone' => '+86-' . ($this->siteConfig['web_call'] ?? '020-85627757'),
            'areaServed' => ['@type' => 'City', 'name' => '广州市'],
            'parentOrganization' => ['@id' => $this->siteUrl('/#organization')],
        ]];
    }

    protected function breadcrumbSchema(array $items): array
    {
        $list = [];
        foreach ($items as $position => $item) {
            if (empty($item['name']) || empty($item['url'])) continue;
            $list[] = [
                '@type' => 'ListItem',
                'position' => $position + 1,
                'name' => $item['name'],
                'item' => $this->absoluteUrl($item['url']),
            ];
        }
        return $list ? [[
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $list,
        ]] : [];
    }

    /**
     * JSON响应
     */
    protected function json(array $data, int $code = 200): void
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($code);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * 重定向
     */
    protected function redirect(string $url, int $code = 302): void
    {
        header('Location: ' . $url, true, $code);
        exit;
    }

    /**
     * 获取GET参数
     */
    protected function get(string $key, $default = '')
    {
        return $_GET[$key] ?? $default;
    }

    /**
     * 获取POST参数
     */
    protected function post(string $key, $default = '')
    {
        return $_POST[$key] ?? $default;
    }

    /**
     * 安全过滤输入
     */
    protected function filterInput(string $value): string
    {
        $value = strip_tags($value);
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        return trim($value);
    }
}
