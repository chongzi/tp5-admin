<?php
namespace app\admin\model;

use think\Model;
use think\Db;
class MoreApp extends BaseModel
{
    //新增和更新自动完成
    protected $auto = [];
    protected $insert = [];  
    protected $update = [];  

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
            $where = '';
            if((int)$search['title']){
                $where .= 'id = '.$search['title'];
            }
        }else{
            $where = '';
        }

        return Db::table('s_more_app')->where($where)->order('id desc')->paginate(config('admin_pages_num'));   

    }

    public function appClick($id){

       $sql = "update s_more_app set click_num = click_num + 1 where id = ".$id;
       return Db::execute($sql);
   }
}