<?php
namespace app\admin\model;

use think\Model;
use think\Db;
class User extends BaseModel
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

    public function friendsPhb($type_id,$user_id,$page,$page_size)
    {
        $params = array();
        if($type_id == 1)
        {
            $sql = "select a.id,a.name,a.nick_name,a.face,a.point,a.music_num as num from s_user as a ,s_friends as b where a.id=b.friend_id and b.user_id=$user_id and music_num>0 order by music_num desc,point desc,a.id asc" ;
        }
        else
        {
            $sql = "select a.id,a.name,a.nick_name,a.face,a.point,a.singer_num as num from s_user as a ,s_friends as b where a.id=b.friend_id and b.user_id=$user_id and singer_num>0 order by singer_num desc,point desc,a.id asc" ;    
        }
        
        $offset = $page_size*($page - 1);
        $sql .= " limit $offset,$page_size" ;  
        $list = Db::query($sql);
        if($list)
            $list = collection($list)->toArray();
        return $list ;
    }
    public function myPhbInFriends($type_id,$user_id,$num)
    {
        $params = array();
        if($type_id == 1)
        {
            $sql = "select count(1) as count from s_user as a ,s_friends as b where a.id=b.friend_id and b.user_id=$user_id and music_num>$num order by music_num desc,point desc,a.id asc" ;
        }
        else
        {
            $sql = "select count(1) as count from s_user as a ,s_friends as b where a.id=b.friend_id and b.user_id=$user_id and singer_num>$num order by singer_num desc,point desc,a.id asc" ;    
        }
        
        //echo $sql;
        $res = Db::query($sql);
        $count = $res[0]["count"];
        return $count;
    }


    /**   
    * 根据条件来检索数据，并排序分页 
    * 
    * @access public 
    * @param $where array 搜索条件
    * @param $order string 排序字段，多个用英文逗号隔开，如'id desc,name asc'
    * @param $page int 页码
    * @param $page_size int 每页显示记录数
    * @param $count int 总记录数
    * @return array 符合条件的数据集
    */  
    public function search($where=null,$order='id desc',$page=1,$page_size=1000,&$count)
    {
        foreach ($where as $key => $value) 
        {
            if(!$value)
            {
                unset($where[$key]);
            }
        }

        $limit = ($page-1)*$page_size;
        $m = new $this->class();
        $res = $m->where($where)->limit($limit,$page_size)->order($order)->select(); 
        $count = $m->where($where)->count();
        if($res)
            return collection($res)->toArray();
        else
            return null;
    }
}