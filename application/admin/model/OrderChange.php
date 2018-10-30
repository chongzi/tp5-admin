<?php
namespace app\admin\model;

use think\Model;
use think\Db;
class OrderChange extends BaseModel
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
            $where = '';
            if(is_numeric($search['title'])){
                $where = 'a.id = '.$search['title'];
            }
            if($search['app_id']){
                if($where){
                    $where .= ' and a.app_id = '.$search['app_id'];
                }else{
                    $where .= 'a.app_id = '.$search['app_id'];
                }
            }
            
            if($search['status'] != -1 && $search['status'] != ''){
                if($where){
                    $where .= " and a.status = ".(int)$search['status'];
                }else{
                    $where .= "a.status = ".(int)$search['status'];
                }
            }
        }else{
            $where = '';
        }
       return Db::table('s_order_change a')->field('a.*,b.title,c.nick_name,c.face')->join('s_app b','a.app_id = b.id')->join('s_user c','a.user_id = c.id')->order('a.id desc')->where($where)->paginate(config('admin_pages_num'));exit;
   }

   public function info($id){
        return Db::table('s_order_change a')->field('a.*,b.title,c.nick_name,c.face,c.reg_ip')->join('s_app b','a.app_id = b.id')->join('s_user c','a.user_id = c.id')->order('a.id desc')->where('a.id = '.$id)->find();
    }
}