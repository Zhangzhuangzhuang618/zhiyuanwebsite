<?php
namespace app\model;

class CmsPhoto extends BaseModel
{
    protected $table = 'cms_photo';

    public function getListByNav(int $navId, int $page = 1, int $pageSize = 20): array
    {
        return $this->paginate(
            ['nav_id' => $navId, 'status' => 1],
            $page, $pageSize,
            'id, title, image, sketch, create_time'
        );
    }
}
