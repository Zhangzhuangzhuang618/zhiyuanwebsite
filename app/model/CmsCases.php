<?php
namespace app\model;

class CmsCases extends BaseModel
{
    protected $table = 'cms_cases';

    public function getListByNav(int $navId, int $page = 1, int $pageSize = 12): array
    {
        return $this->paginate(
            ['nav_id' => $navId, 'status' => 1],
            $page, $pageSize,
            'id, title, sketch, image, link, target, nav_id, create_time, seo_title, seo_keyword, seo_content',
            'sort ASC, create_time DESC'
        );
    }

    public function getLatest(int $limit = 8, int $navId = 0): array
    {
        if ($navId === 0) {
            return $this->select(
                ['nav_id' => 10, 'status' => 1],
                'id, title, image, sketch, link, target, create_time',
                'sort ASC, id ASC',
                $limit
            );
        }

        return $this->select(
            ['nav_id' => $navId, 'status' => 1, 'state' => 1],
            'id, title, image, sketch, link, target, create_time',
            'sort ASC, id ASC',
            $limit
        );
    }

    public function getPrevNext(int $id, int $navId): array
    {
        $result = ['prev' => null, 'next' => null];
        foreach (['prev' => ['<', 'DESC'], 'next' => ['>', 'ASC']] as $key => [$operator, $order]) {
            $sql = 'SELECT id, title, link, target FROM ' . $this->table()
                . " WHERE nav_id = ? AND status = 1 AND id {$operator} ? ORDER BY id {$order} LIMIT 1";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([$navId, $id]);
            $result[$key] = $stmt->fetch();
        }
        return $result;
    }

    public function getRelated(int $navId, int $excludeId, int $limit = 8): array
    {
        $sql = 'SELECT id, title, link, target FROM ' . $this->table()
            . ' WHERE nav_id = ? AND id != ? AND status = 1 ORDER BY sort ASC, id ASC LIMIT ?';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$navId, $excludeId, $limit]);
        return $stmt->fetchAll();
    }
}
