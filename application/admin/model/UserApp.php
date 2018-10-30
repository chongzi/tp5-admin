<?php
namespace app\admin\model;

use think\Model;
use think\Db;
class UserApp extends BaseModel
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
        // var_dump(Db::query('call play(3358,1)'));exit;
        if(!empty($search)){
            $where = '';
            if((int)$search['title']){
                $where .= 'a.id = '.$search['title'];
            }

            if($search['app_id']){
                if($where != ''){
                    $where .= ' and a.app_id = '.$search['app_id'];
                }else{
                    $where .= 'a.app_id = '.$search['app_id'];
                } 
            }
            if($search['order']){
                $order = 'a.'.$search['order'].' desc';
            }else{
                $order = 'a.id desc';
            }

        }else{
            $where = '';
        }
        if($nolimit){
            return Db::table('s_user_app a')->field('a.id')->join('s_user b','a.user_id = b.id')->join('s_app c','a.app_id = c.id')->where($where)->count();
        }else{
         return Db::table('s_user_app a')->field('a.*,b.nick_name,b.face,c.title,c.ico')->join('s_user b','a.user_id = b.id')->join('s_app c','a.app_id = c.id')->where($where)->order($order)->paginate(config('admin_pages_num'),false,['query'=>request()->param()]);   
        }

    }
    
   //edit
    public function info($id){
        $arr['info'] = Db::table('s_user_app a')->field('a.*,b.nick_name,b.face,c.title,c.ico')->join('s_user b','a.user_id = b.id')->join('s_app c','a.app_id = c.id')->where('a.id = '.$id)->find();
        $arr['list'] = Db::table('s_user_play a')->field('a.*,b.title')->join('s_app b','a.app_id = b.id')->where('a.user_id = '.$arr['info']['user_id'].' and a.app_id = '.$arr['info']['app_id'])->select();
        return $arr;
    }

    public function getUserInfo($where){
        return Db::table('s_user_app a')->field('a.playable_num,a.played_num,a.succ_num,a.max_score,a.money,b.nick_name,b.face')->join('s_user b','a.user_id = b.id')->where('a.app_id = '.$where['app_id'].' and a.user_id = '.$where['user_id'])->select()[0];
    }

    public function getTotalNum($where){
        return Db::table('s_user_app')->field('sum(played_num) total_num')->where($where)->find()['total_num'];
    }

    public function viewListBy($where=null,$data)
    {     
        $p = $data['p'];
        $num = $data['num'];
        $o = $data['order'];
        if($o == false)
            $o = 3;
        if($num == false)
            $num = 20;
        //调用存储过程
        $res['list'] = Db::query('call play('.$where['app_id'].','.$o.','.$p.','.$num.')')[0];
        $res['total_page'] = ceil(Db::table('s_user_app')->field('a.id')->where('app_id = '.$where['app_id'])->count()/$num);
        return $res;
    }


    //签到列表
    public function sign_list($app_id,$user_id){
        return Db::table('s_user_sign_in')->where(['app_id'=>$app_id,'user_id'=>$user_id,'status'=>1])->order('id asc')->select();
    }

    //标记过期签到记录
    public function emptySign($app_id,$user_id){
        return Db::table('s_user_sign_in')->where(['app_id'=>$app_id,'user_id'=>$user_id])->update(['status'=>0]);
    }

    //模板消息详情
    public function template_msg_list($data){
        return Db::table('s_template_msg')->where(['app_id'=>$data['app_id'],'user_id'=>$data['user_id']])->order('id desc')->select();
    }
}