<?php

namespace app\admin\model;

use think\Model;

class Admin extends BaseModel
{
    use TextParserTrait;
    
    protected $readonly = ['name'];

    //新增和更新自动完成
    protected $auto = [];
    protected $insert = ['status' => 1];
    protected $update = [];  

    protected function initialize($model='',$class='')
    {
        $arr = explode("\\", __CLASS__);
        //$model = "admin/".$arr[count($arr)-1];//获取当前模型
        $model = $arr[count($arr)-1];//获取当前模型
        parent::initialize($model,__CLASS__);
    }

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
