<?php
namespace app\model;

/**
 * 导航菜单模型
 */
class CmsNav extends BaseModel
{
    protected $table = 'cms_nav';

    /**
     * 获取导航树（带缓存）
     * @param string $cacheKey 缓存标识
     * @param int $limit 数量限制
     */
    public function getNavTree(string $cacheKey = 'header', int $limit = 0): array
    {
        $cacheFile = RUNTIME_PATH . 'cache/nav_' . $cacheKey . '_' . __LANG__ . '.php';
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < 3600)) {
            return include $cacheFile;
        }

        // 获取顶级导航
        if ($cacheKey === 'main_nav') {
            $sql = 'SELECT * FROM ' . $this->table()
                . ' WHERE pid = 0 AND status = 1 AND (states = 1 OR id = 1)'
                . ' ORDER BY sort ASC, id ASC';
            if ($limit > 0) {
                $sql .= ' LIMIT ' . $limit;
            }
            $topNavs = $this->connection->query($sql)->fetchAll();
        } else {
            $topNavs = $this->select(
                ['pid' => 0, 'states' => 1],
                '*',
                'sort ASC, id ASC',
                $limit
            );
        }

        $result = [];
        foreach ($topNavs as $nav) {
            // 获取子导航
            $children = $this->select(
                ['pid' => $nav['id'], 'state' => 1],
                '*',
                'sort ASC, id ASC'
            );

            $nav['children'] = $children ?: [];
            $result[] = $nav;
        }

        // 写入缓存
        if (!is_dir(dirname($cacheFile))) {
            mkdir(dirname($cacheFile), 0755, true);
        }
        file_put_contents($cacheFile, '<?php return ' . var_export($result, true) . ';');

        return $result;
    }

    /**
     * 获取顶部主导航
     */
    public function getMainNav(): array
    {
        return $this->getNavTree('main_nav');
    }

    /**
     * 获取底部导航
     */
    public function getFooterNav(): array
    {
        return $this->getNavTree('footer_nav', 6);
    }

    /**
     * 根据URL模型获取导航信息
     */
    public function getByUrlModel(string $urlModel)
    {
        return $this->findWhere(['url_model' => $urlModel, 'states' => 1]);
    }

    /**
     * 获取面包屑路径
     */
    public function getBreadcrumb(int $navId): array
    {
        $breadcrumb = [];
        $current = $this->find($navId);
        while ($current) {
            array_unshift($breadcrumb, $current);
            if ($current['pid'] == 0) break;
            $current = $this->find($current['pid']);
        }
        return $breadcrumb;
    }
}
