<?php
namespace app\model;

class CmsCountry extends BaseModel
{
    protected $table = 'cms_country';

    public function getAll(): array
    {
        return $this->select(['status' => 1], '*', 'sort ASC, id ASC');
    }
}
