<?php
namespace app\model;

class CmsMessage extends BaseModel
{
    protected $table = 'cms_message';

    public function add(array $data): int
    {
        $data['create_time'] = $data['create_time'] ?? time();
        $data['update_time'] = $data['update_time'] ?? time();
        $data['ip'] = $_SERVER['REMOTE_ADDR'] ?? '';
        return $this->insert($data);
    }
}
