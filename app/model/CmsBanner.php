<?php
namespace app\model;

class CmsBanner extends BaseModel
{
    protected $table = 'cms_banner';

    public function getBanners(string $position = 'home'): array
    {
        if ($position !== 'home') {
            return [];
        }

        $sql = 'SELECT id, title, image, url AS link, sort FROM ' . $this->table()
            . " WHERE class = '电脑端Banner' AND status = 1 AND delete_time IS NULL"
            . ' ORDER BY sort ASC, id DESC';
        return $this->connection->query($sql)->fetchAll();
    }
}
