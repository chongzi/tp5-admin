<?php
namespace app\admin\model;

use think\Model;
use think\Db;
class UserPlay extends BaseModel
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
            if(is_numeric($search['title'])){
                $where = 'a.id = '.$search['title'];
            }else{
                $where = "name like '%".$search['title']."%'";
            }
        }else{
            $where = '';
        }
       return Db::table('s_user_app a')->field('a.*,b.nick_name,b.face,c.title,c.ico')->join('s_user b','a.user_id = b.id')->join('s_app c','a.app_id = c.id')->paginate(config('admin_pages_num'));
    }

    public function phb($app_id,$field_order="max_score",$page=1,$page_size=1000,&$count)
    {
        $app_id = intval($app_id);
        $sql = "select a.id , a.name,a.face,".$field_order." from s_user as a ,s_user_app as b where a.id=b.user_id and app_id=".$app_id;
        $sql_count = "select count(1) as count from s_user as a ,s_user_app as b where a.id=b.user_id and app_id=".$app_id;

        $sql .= " order by ".$field_order." desc";

        if( $page_size > 0 ){
            $offset = ($page - 1) * $page_size;
            $sql .= " LIMIT $offset,$page_size";
        }
        //echo $sql;exit();
        $res = Db::query($sql_count);
        $count = $res[0]["count"];
        $res = Db::query($sql);
        return $res;
    }

    // public function getUserInfo($where){
    //     return Db::table('s_user_app a')->field('a.playable_num,a.played_num,a.succ_num,a.max_score,a.money,b.nick_name,b.face')->join('s_user b','a.user_id = b.id')->where('a.app_id = '.$where['app_id'].' and a.user_id = '.$where['user_id'])->select()[0];
    // }

    // public function viewListBy($where=null)
    // {
            
    //     return Db::table('s_user_app a')->field('a.id,a.played_num,a.succ_num,a.max_score,b.nick_name,b.face')->join('s_user b','a.user_id = b.id')->where('a.app_id = '.$where['app_id'])->order($where['order'])->select();
    // }
}