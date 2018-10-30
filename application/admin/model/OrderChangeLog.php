<?php
namespace app\admin\model;

use think\Model;
use think\Db;
class OrderChangeLog extends BaseModel
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

    public function viewListBy($where=null,$field='',$limit=100,$order='id desc')
    {
        $giftId = Db::table('s_app')->field('app_gift_id')->where('id = '.$where['app_id'])->find()['app_gift_id'];
        if($giftId){
            $res = Db::table('s_gift')->field($field)->where('status = 1 and id in ('.$giftId.')')->order('sort desc')->select();
        }else{
            $res = [];
        }
        
        return $res;
    }

    //列表 带分页
    public function search($search){
        if(!empty($search)){
            if(is_numeric($search['title'])){
                $where = 'id = '.$search['title'];
            }else{
                $where = "name like '%".$search['title']."%'";
            }
        }else{
            $where = '';
        }
       return Db::table('s_gift')->order('id desc')->where($where)->paginate(config('admin_pages_num'));exit;
   }
}