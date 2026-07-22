<?php
namespace app\model;

class CmsExpand extends BaseModel
{
    protected $table = 'cms_expand';

    public function getByParent(int $parentId, string $cacheKey = '', int $limit = 0): array
    {
        if ($cacheKey) {
            $cacheFile = RUNTIME_PATH . 'cache/expand_' . $cacheKey . '_' . __LANG__ . '.php';
            if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < 3600)) {
                return include $cacheFile;
            }
        }

        $result = $this->select(
            ['pid' => $parentId, 'status' => 1],
            '*',
            'sort ASC, id ASC',
            $limit
        );

        if ($cacheKey) {
            if (!is_dir(dirname($cacheFile))) mkdir(dirname($cacheFile), 0755, true);
            file_put_contents($cacheFile, '<?php return ' . var_export($result, true) . ';');
        }

        return $result;
    }
}
