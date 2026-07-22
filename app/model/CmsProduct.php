<?php
namespace app\model;

/**
 * 产品/服务模型
 */
class CmsProduct extends BaseModel
{
    protected $table = 'cms_product';

    /**
     * 获取产品列表（按分类）
     */
    public function getListByNav(int $navId, int $page = 1, int $pageSize = 12): array
    {
        return $this->paginate(
            ['nav_id' => $navId, 'status' => 1],
            $page,
            $pageSize,
            'id, title, subtitle, sketch, image, link, target, nav_id, create_time, browse, seo_title, seo_keyword, seo_content'
        );
    }

    /**
     * 获取推荐产品
     */
    public function getFeatured(int $limit = 8, int $navId = 0): array
    {
        $where = ['status' => 1, 'top_status' => 1];
        if ($navId > 0) {
            $where['nav_id'] = $navId;
        }
        return $this->select($where, 'id, title, subtitle, sketch, image, nav_id', 'sort ASC, id DESC', $limit);
    }

    /**
     * 获取服务产品（用于首页展示，带缓存）
     */
    public function getHomeServices(): array
    {
        $cacheFile = RUNTIME_PATH . 'cache/home_services_' . __LANG__ . '.php';
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < 3600)) {
            return include $cacheFile;
        }

        $services = $this->select(
            ['status' => 1],
            'id, title, subtitle, sketch, image, nav_id, link, target',
            'sort ASC, id ASC',
            8
        );

        if (!is_dir(dirname($cacheFile))) {
            mkdir(dirname($cacheFile), 0755, true);
        }
        file_put_contents($cacheFile, '<?php return ' . var_export($services, true) . ';');

        return $services;
    }

    /**
     * 获取相关产品
     */
    public function getRelated(int $navId, int $excludeId, int $limit = 4): array
    {
        $sql = "SELECT id, title, image, sketch FROM " . $this->table()
            . " WHERE nav_id = ? AND id != ? AND status = 1"
            . " ORDER BY sort ASC, id DESC LIMIT ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$navId, $excludeId, $limit]);
        return $stmt->fetchAll();
    }
}
