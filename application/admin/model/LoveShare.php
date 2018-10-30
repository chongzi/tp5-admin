<?php
namespace app\admin\model;

use think\Model;
use think\Db;
class LoveShare extends BaseModel
{
    //新增和更新自动完成
    protected $auto = [];
    protected $insert = [];  
    protected $update = [];  
    protected $field = true;
    protected function initialize($model='',$class='')
    {
        $arr = explode("\\", __CLASS__);
        //$model = "admin/".$arr[count($arr)-1];//获取当前模型
        $model = $arr[count($arr)-1];//获取当前模型
        parent::initialize($model,__CLASS__);
    }

    //列表 带分页
    public function search($search){
        if(!empty($search)){
            if(is_numeric($search['title'])){
                $where = 'id = '.$search['title'];
            }else{
                $where = "title like '%".$search['title']."%'";
            }
        }else{
            $where = '';
        }
       return Db::table('s_love_resource')->order('id desc')->where($where)->paginate(config('admin_pages_num'));exit;
   }

   
}