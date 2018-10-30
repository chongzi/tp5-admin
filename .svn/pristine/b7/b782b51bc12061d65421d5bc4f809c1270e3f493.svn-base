<?php
namespace app\admin\model;

use think\Model;
use think\Db;
class UserLoginLog extends BaseModel
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

    public function search($search, $order_by, $page, $page_size, &$count)
    {
        $params    = array();
        $sql       = "select a.*,b.nick_name,b.face from s_user_login_log a inner join s_user b on a.user_id = b.id where 1";
        $sql_count = "select count(1) as count from s_user_login_log a inner join s_user b on a.user_id = b.id where 1";

        $sql_where = "";
        $params = array();
        if ($search["start_time"] && $search['end_time']) {
            $start_time = date('Ymd', strtotime($search["start_time"]));
            $end_time   = date('Ymd', strtotime($search["end_time"]));
            $sql_where .= " and (a.add_date between ? and ?)";
            $params[] = $start_time;
            $params[] = $end_time;
        }
        if($search["id"]){
            $sql_where .= " and a.user_id = ?";
            $params[] = $search['id'];
        } 
        if($search["name"]){
            $sql_where .= " and b.name = ?";
            $params[] = $search['name'];
        }
        if($search["mobile"]){
            $sql_where .= " and b.mobile = ? ";
            $params[] = $search["mobile"];
        }
        $sql .= $sql_where;
        $sql_count .= $sql_where;

        if ($order_by) {
            $sql_order = " order by " . $order_by;
        } else {
            $sql_order = " order by a.id desc ";
        }
        $sql .= $sql_order;
        $count = 0;
        if ($page > 0 && $page_size > 0) //如果要分页
        {   
            $info  = Db::query($sql_count, $params);
            $count = $info[0]["count"];
            $offset = $page_size * ($page - 1);
            $sql .= " limit ?,?";
            $params[] = $offset;
            $params[] = $page_size;
        }

        $list = Db::query($sql, $params);
        if ($list) {
            $list = collection($list)->toArray();
        }
        return $list;
    }    
}