<?php

namespace app\admin\model;

use think\Model;

class AdminGroup extends Model
{
    use TextParserTrait;
    
    protected $readonly = ['name'];

    protected $insert = ['status' => 1];

    public function setPwdAttr($password)
    {
        return password_hash($password, PASSWORD_DEFAULT, [
            'cost' => 10,
        ]);
    }

    public function checkPwd($password)
    {
        return password_verify($password, $this->pwd);
    }

    public function avatar()
    {
        return isset($this->data['avatar']) ? $this->data['avatar'] : '/static/none.jpg';
    }
}
