<?php

namespace app\admin\model;

use think\Model;

class Config extends Model
{
    use TextParserTrait;

    protected $insert = ['status' => 1];

    public function group()
    {
        $groups = config('config_group_list');
        return $groups[$this->group];
    }

    public function type()
    {
        $types = config('config_type_list');
        return $types[$this->data['type']];
    }
}
