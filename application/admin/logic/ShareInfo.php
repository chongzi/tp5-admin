<?php
namespace app\admin\logic;
use Think\Request;
use think\Db;
use think\Model;

class ShareInfo extends BaseLogic
{
    /*******************************基础配置开始*******************************/
    
    public $flag_opt = array();
    public $id_alias = "gift_id";//主表与其他表关联的字段
    private $flag_model = "";//标记模型
    private $slave_model = "";//从表模型
    private $slave_relation_field = "";//从表和主表关联字段

    /*******************************基础配置结束*******************************/
    public $opt_status = array(
        1 => '<span style="color:green">上架</span>',
        0 => '<span style="color:#CAA">下架</span>',
    );
    
    function __construct()
    {
        $arr = explode("\\", __CLASS__);
        $model = $arr[count($arr)-1];//获取当前模型
        $mp["id_alias"] = $this->id_alias;
        $mp["slave_model"] = $this->slave_model;
        $mp["slave_relation_field"] = $this->slave_relation_field;
        $mp["flag_model"] = $this->flag_model;
        $mp["flag_opt"] = $this->flag_opt;
        parent::__construct($model,$mp);
    }

    //分享 判断是否同一个群 加挑战数
    public function shareQun($data,$qun_id = 0){
        $user_app_info = model('user_app')->infoBy(['app_id'=>$data['app_id'],'user_id'=>$data['user_id']],'playable_num');
        $app_info = model('app')->infoBy(['id'=>$data['app_id']],'share_num');
        $add_num = 1;
        $share_num = $app_info['share_num'];
        $num = $user_app_info['playable_num'];
        $share_n = model('share_info')->getShareNum($data);
        $is_help = $data['is_help'] == 1 ? 1 : 0;
        if($is_help == 0){
            if($share_n >= $share_num){
                $this->error = '每天最多分享获得'.$share_num.'次挑战机会，明天再来分享吧！';
            }else{
                $result = model('share_info')->checkQunday($data,$qun_id);
                if($result == 0){
                   $app_info = model('app')->info($data['app_id']);
                   if($app_info['user_play_num'] > 0){
                        model('user_app')->updBy(['playable_num'=>$user_app_info['playable_num']+$add_num],['app_id'=>$data['app_id'],'user_id'=>$data['user_id']]);
                   }
                   $num = $user_app_info['playable_num']+$add_num; 
                }else{
                    $this->error = '每个群每天只有第一次分享才是有效的哦，换个群试试吧！';
                }
            }      
        }else{
            $this->error = '求助成功！';
        }
        $info['user_id'] = $data['user_id'];
        $info['app_id'] = $data['app_id'];
        $info['qun_id'] = $qun_id;
        $info['add_time'] = time();
        $info['ip'] = $data['ip'];
        $res = model('share_info')->add($info);
        return $num;
    }
}