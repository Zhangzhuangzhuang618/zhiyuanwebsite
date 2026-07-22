<?php
namespace app\model;

class CmsArticle extends BaseModel
{
    protected $table = 'cms_article';

    public function getListByNav(int $navId, int $page = 1, int $pageSize = 12): array
    {
        return $this->paginate(
            ['nav_id' => $navId, 'status' => 1],
            $page, $pageSize,
            'id, title, subtitle, sketch, image, link, target, nav_id, create_time, browse, seo_title, seo_keyword, seo_content'
        );
    }

    public function getLatest(int $limit = 6, int $navId = 0): array
    {
        $where = ['status' => 1];
        if ($navId > 0) $where['nav_id'] = $navId;
        return $this->select($where, 'id, title, sketch, image, link, target, create_time, browse', 'id DESC', $limit);
    }

    public function incrementBrowse(int $id): void
    {
        $sql = "UPDATE " . $this->table() . " SET browse = browse + 1 WHERE id = ?";
        $this->connection->prepare($sql)->execute([$id]);
    }

    public function getPrevNext(int $id, int $navId = 0): array
    {
        $result = ['prev' => null, 'next' => null];
        $where = 'status = 1';
        $params = [];
        if ($navId > 0) { $where .= ' AND nav_id = ?'; $params[] = $navId; }

        $sql = "SELECT id, title FROM " . $this->table() . " WHERE {$where} AND id < ? ORDER BY id DESC LIMIT 1";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(array_merge($params, [$id]));
        $result['prev'] = $stmt->fetch();

        $sql = "SELECT id, title FROM " . $this->table() . " WHERE {$where} AND id > ? ORDER BY id ASC LIMIT 1";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(array_merge($params, [$id]));
        $result['next'] = $stmt->fetch();

        return $result;
    }

    public function getRelated(int $navId, int $excludeId, int $limit = 8): array
    {
        $sql = 'SELECT id, title, link, target FROM ' . $this->table()
            . ' WHERE nav_id = ? AND id != ? AND status = 1 ORDER BY id DESC LIMIT ?';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$navId, $excludeId, $limit]);
        return $stmt->fetchAll();
    }
}
