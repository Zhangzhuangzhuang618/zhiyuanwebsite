<?php
namespace app\model;

class CmsLink extends BaseModel
{
    protected $table = 'cms_link';

    public function getAll(int $limit = 20): array
    {
        return $this->select(['status' => 1], 'id, title, url AS link', 'sort ASC, id DESC', $limit);
    }
}
