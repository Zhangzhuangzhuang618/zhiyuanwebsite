<?php
namespace app\model;

/**
 * 系统配置模型
 */
class SystemConfig extends BaseModel
{
    protected $table = 'system_config';

    /**
     * 获取所有网站配置（带缓存）
     */
    public function getAllConfig(): array
    {
        $cacheFile = RUNTIME_PATH . 'cache/config_' . __LANG__ . '.php';
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < 3600)) {
            return include $cacheFile;
        }

        $rows = $this->select(['origin' => 'web'], 'name, value');
        $config = [];
        foreach ($rows as $row) {
            $config[$row['name']] = $row['value'];
        }

        // 写入缓存
        if (!is_dir(dirname($cacheFile))) {
            mkdir(dirname($cacheFile), 0755, true);
        }
        file_put_contents($cacheFile, '<?php return ' . var_export($config, true) . ';');

        return $config;
    }

    /**
     * 获取路由配置
     */
    public function getRouteConfig(): array
    {
        $routes = [];
        $rows = $this->select(['name' => 'detail_route'], 'value');
        foreach ($rows as $row) {
            $routes = json_decode($row['value'], true) ?: [];
        }
        return $routes;
    }

    /**
     * 获取指定配置项
     */
    public function get(string $name, $default = '')
    {
        $row = $this->findWhere(['name' => $name], 'value');
        return $row['value'] ?? $default;
    }

    /**
     * 获取配置组
     */
    public function getGroup(string $group): array
    {
        $rows = $this->select(['group' => $group], 'name, value, title, type');
        return $rows;
    }
}
