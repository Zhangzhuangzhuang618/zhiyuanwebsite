<?php
namespace app\core;

/**
 * 应用核心类 - 路由分发和请求处理
 */
class App
{
    protected $config;
    protected $routes = [];

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * 运行应用
     */
    public function run()
    {
        // 解析URL
        $path = $this->parseUrl();

        // 加载路由配置
        $this->loadRoutes();

        // 匹配路由
        $handler = $this->matchRoute($path);

        if ($handler) {
            $this->dispatch($handler);
        } else {
            // 默认路由: controller/action/id
            $this->defaultDispatch($path);
        }
    }

    /**
     * 解析请求URL
     */
    protected function parseUrl(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = trim($uri, '/');
        if (empty($uri)) {
            $uri = '/';
        }
        // 处理 .html 后缀
        $uri = preg_replace('/\.html$/', '', $uri);
        return '/' . $uri;
    }

    /**
     * 加载路由配置
     */
    protected function loadRoutes()
    {
        // 从数据库动态加载SEO路由
        try {
            $configModel = new \app\model\SystemConfig();
            $routeConfig = $configModel->getRouteConfig();
            if ($routeConfig) {
                foreach ($routeConfig as $key => $value) {
                    if (!empty($value)) {
                        $this->routes[$value . '/<id>'] = 'index/index/detail_' . str_replace('detail/', '', $value);
                    }
                }
            }
        } catch (\Exception $e) {
            // 数据库不可用时使用静态路由
        }

        // 静态路由表
        $adminPath = trim((string)($this->config['app']['admin_path'] ?? 'webadmini'), '/');
        $staticRoutes = [
            '/'                    => 'index/index/index',
            '/index'               => 'index/index/index',
            '/index/:city'         => 'index/index/index',
            '/new'                 => 'index/index/index',
            '/about/:id'           => 'index/index/about',
            '/about'               => 'index/index/about',
            '/event'               => 'index/index/event',
            '/page/:id'            => 'index/page/page',
            '/news/:id'            => 'index/news/index',
            '/news'                => 'index/news/index',
            '/problem/:id'         => 'index/news/problem',
            '/photo/:id'           => 'index/photo/index',
            '/photo'               => 'index/photo/index',
            '/contact/:id'         => 'index/index/contact',
            '/contact'             => 'index/index/contact',
            '/products/:id'        => 'index/products/index',
            '/products'            => 'index/products/index',
            '/business/:id'        => 'index/products/business',
            '/migration/:id'       => 'index/products/index',
            '/cases/:id'           => 'index/cases/index',
            '/cases'               => 'index/cases/index',
            '/message/:id'         => 'index/message/index',
            '/message'             => 'index/message/index',
            '/verify'              => 'index/verify/index',
            '/GetSmsCode'          => 'index/message/sendSms',
            '/GoAssess'            => 'index/message/assess',
            '/languageGo'          => 'index/index/changeLanguage',
            '/search/:model'       => 'index/search/index',
            '/search'              => 'index/search/index',
            '/faq'                 => 'index/faq/index',
            '/sitemap.xml'         => 'index/sitemap/index',
            '/PageSearch'          => 'index/search/productSearch',
            '/GetList'             => 'index/products/productSearch',
            // GEO Content OS 专用新闻发布 API。
            '/api/geo/v1/capabilities' => 'index/geoPublish/capabilities',
            '/api/geo/v1/publish'      => 'index/geoPublish/publish',
            '/api/geo/v1/status/:id'   => 'index/geoPublish/status',
            // 详情页路由 (URL格式: /detail/news123)
            '/detail/news:id'      => 'index/news/detail',
            '/detail/products:id'  => 'index/products/detail',
            '/detail/case:id'      => 'index/cases/detail',
            '/detail_cases:id'     => 'index/cases/detail',
        ];

        // 轻量后台：入口由 site.php 的 app.admin_path 控制。
        $staticRoutes['/' . $adminPath]                    = 'index/admin/login';
        $staticRoutes['/' . $adminPath . '/login']         = 'index/admin/login';
        $staticRoutes['/' . $adminPath . '/logout']        = 'index/admin/logout';
        $staticRoutes['/' . $adminPath . '/dashboard']     = 'index/admin/dashboard';
        $staticRoutes['/' . $adminPath . '/messages']      = 'index/admin/messages';
        $staticRoutes['/' . $adminPath . '/messageStatus'] = 'index/admin/messageStatus';
        $staticRoutes['/' . $adminPath . '/messageDelete'] = 'index/admin/messageDelete';
        $staticRoutes['/' . $adminPath . '/articles']      = 'index/admin/articles';
        $staticRoutes['/' . $adminPath . '/articleEdit']   = 'index/admin/articleEdit';
        $staticRoutes['/' . $adminPath . '/articleSave']   = 'index/admin/articleSave';
        $staticRoutes['/' . $adminPath . '/articleDelete'] = 'index/admin/articleDelete';
        $staticRoutes['/' . $adminPath . '/casesManage']   = 'index/admin/casesManage';
        $staticRoutes['/' . $adminPath . '/caseEdit']      = 'index/admin/caseEdit';
        $staticRoutes['/' . $adminPath . '/caseSave']      = 'index/admin/caseSave';
        $staticRoutes['/' . $adminPath . '/caseDelete']    = 'index/admin/caseDelete';
        $staticRoutes['/' . $adminPath . '/settings']      = 'index/admin/settings';
        $staticRoutes['/' . $adminPath . '/settingsSave']  = 'index/admin/settingsSave';
        $staticRoutes['/' . $adminPath . '/upload']        = 'index/admin/upload';

        foreach ($staticRoutes as $route => $handler) {
            $this->routes[$route] = $handler;
        }
    }

    /**
     * 匹配路由
     */
    protected function matchRoute(string $path): ?array
    {
        foreach ($this->routes as $pattern => $handler) {
            // 将路由模式转换为正则
            $regex = '#^' . preg_replace('/:([^\/]+)/', '([^\/]+)', $pattern) . '$#';
            $regex = str_replace('/<id>', '/(\d+)', $regex);

            if (preg_match($regex, $path, $matches)) {
                array_shift($matches); // 移除完整匹配
                return [
                    'handler' => $handler,
                    'params'  => $matches,
                ];
            }

            // 精确匹配
            if ($pattern === $path) {
                return [
                    'handler' => $handler,
                    'params'  => [],
                ];
            }
        }
        return null;
    }

    /**
     * 分发路由处理
     */
    protected function dispatch(array $handler)
    {
        $parts = explode('/', $handler['handler']);
        // format: module/controller/action
        if (count($parts) >= 3) {
            $module     = $parts[0];
            $controller = $parts[1];
            $action     = $parts[2];
        } else {
            $module     = 'index';
            $controller = $parts[0] ?? 'index';
            $action     = $parts[1] ?? 'index';
        }

        $class = "\\app\\controller\\" . ucfirst($controller);
        if (!class_exists($class)) {
            $class = "\\app\\controller\\Index";
            $action = 'index';
        }

        $instance = new $class($this->config);
        call_user_func_array([$instance, $action], $handler['params']);
    }

    /**
     * 默认分发
     */
    protected function defaultDispatch(string $path)
    {
        $parts = explode('/', trim($path, '/'));
        $controller = !empty($parts[0]) ? ucfirst($parts[0]) : 'Index';
        $action     = $parts[1] ?? 'index';
        $params     = array_slice($parts, 2);

        $class = "\\app\\controller\\" . $controller;
        if (!class_exists($class)) {
            // 404
            header('HTTP/1.1 404 Not Found');
            echo '<h1>404 - 页面未找到</h1>';
            echo '<p><a href="/">返回首页</a></p>';
            return;
        }

        $instance = new $class($this->config);
        call_user_func_array([$instance, $action], $params);
    }
}
