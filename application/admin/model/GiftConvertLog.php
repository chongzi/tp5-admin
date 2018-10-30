<?php
namespace app\admin\model;

use think\Model;
use think\Db;
class GiftConvertLog extends BaseModel
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
    public function search($search,$nolimit = false){
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
             if($search['phone']){
                if($where){
                    $where .= ' and a.phone = '.$search['phone'];
                }else{
                    $where .= 'a.phone = '.$search['phone'];
                }
            }
             if($search['name']){
                if($where){
                    $where .= " and a.name = '".$search['name']."'";
                }else{
                    $where .= "a.name = '".$search['name']."'";
                }
            }
             if($search['gift_name']){
                if($where){
                    $where .= " and a.gift_name = '".$search['gift_name']."'";
                }else{
                    $where .= "a.gift_name = '".$search['gift_name']."'";
                }
            }
            if($search['status']){
                if($where){
                    $where .= " and a.status = ".$search['status'];
                }else{
                    $where .= "a.status = ".$search['status'];
                }
            }
        }else{
            $where = '';
        }
        if($nolimit){
             return Db::table('s_gift_convert_log a')->field('a.id')->join('s_app b','a.app_id = b.id')->join('s_user c','a.user_id = c.id')->where($where)->count();exit;
        }else{
             return Db::table('s_gift_convert_log a')->field('a.*,b.title,c.nick_name,c.face')->join('s_app b','a.app_id = b.id')->join('s_user c','a.user_id = c.id')->order('a.id desc')->where($where)->paginate(config('admin_pages_num'),false,['query'=>request()->param()]);exit;
        }
      
   }

    public function info($id){
        return Db::table('s_gift_convert_log a')->field('a.*,b.title,b.convert_gift_rate,c.nick_name,c.face,c.reg_ip')->join('s_app b','a.app_id = b.id')->join('s_user c','a.user_id = c.id')->order('a.id desc')->where('a.id = '.$id)->find();
    }
}