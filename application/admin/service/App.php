<?php
namespace app\admin\service;

use think\Model;
use think\Db;
use wxapp\Wxapp;

class App extends BaseService
{
       /**
     * 获取应用信息
     * @AuthorHTL
     * @DateTime  2018-05-30T11:42:12+0800
     * @param     $app_id 应用id
     * @return    array
     */
       public function getAppInfo($data){
           $app_id = $data["app_id"];
                if(!$this->checkAppId($app_id)){
                return false;exit;
           }

           $app_info = logic("app")->viewInfo($app_id,'id,title,share_title,share_ico,app_desc,more_app_id,status');
           $app_info['ico'] = json_decode($app_info['share_ico'],true);
           $app_info['share_ico'] = $app_info['ico'];
           $app_info['share_title'] = explode(',',$app_info['share_title']);
           if($app_info['more_app_id']){
                $app_info['more_app_info'] = logic("more_app")->viewListBy($app_info['more_app_id']);
           }else{
                $app_info['more_app_info'] = [];
           }
           $app_info['play_total_num'] = (int)model("user_app")->getTotalNum(['app_id'=>$data['app_id']]);
           unset($app_info['more_app_id']);
           return $app_info;
       }

       /**
     * 投诉
     * @AuthorHTL
     * @DateTime  2018-05-30T11:42:12+0800
     * @param     $app_id $user_id应用id和用户id，$title 投诉内容
     * @return    array
     */
       public function tousu($data){
        $app_id = $data["app_id"];
        $user_id = $data['user_id'];
        if(!$this->checkAppId($app_id)){
            return false;exit;
        }
        if(!$this->checkUserId($user_id)){  //验证用户id
            return false;exit;
        }
        if(!$data['title']){  //验证用户id
            $this->error = '投诉标题不能为空';
            return false;exit;
        }
        $info['user_id'] = $user_id;
        $info['app_id'] = $app_id;
        $info['title'] = $data['title'];
        $info['add_time'] = time();
        $info['ip'] = $data['ip'];
        $res = logic("tousu")->add($info);
        if($res){
            return ['ip'=>$data['ip'],'add_time'=>time()];
        }else{
            $this->error = "添加数据失败";
            return false;
        }
    }

      /**
     * 分享
     * @AuthorHTL
     * @DateTime  2018-05-30T11:42:12+0800
     * @param     $app_id $user_id应用id和用户id
     * @return    array
     */
      public function share($data){
        $app_id = $data["app_id"];
        $user_id = $data['user_id'];
        if(!$this->checkAppId($app_id)){    //验证应用id
            return false;exit;
        }
        if(!$this->checkUserId($user_id)){  //验证用户id
            return false;exit;
        }
        $info['user_id'] = $user_id;
        $info['app_id'] = $app_id;
        $info['qun_id'] = $data['qun_id'];
        $info['add_time'] = time();
        $info['ip'] = $data['ip'];
        $res = logic("share_info")->add($info);
        if($res){
            return ['ip'=>$data['ip'],'add_time'=>time()];
        }else{
            $this->error = "添加数据失败";
            return false;
        }
    }

   /**
     * 解密分享群数据
     * @AuthorHTL
     * @DateTime  2018-05-30T11:42:12+0800
     * @param     $iv $encryptedData $session_key $app_id
     * @return    array
     */
   public function getShareQunInfo($data){
        $app_id = $data["app_id"];
        $user_id = $data['user_id'];
        if(!$this->checkAppId($app_id)){    //验证应用id
            return false;exit;
        }
        if(!$this->checkUserId($user_id)){  //验证用户id
            return false;exit;
        }
        $app_info = logic('app')->info($data['app_id']);
        $wxapp = new Wxapp($app_info['wx_app_id'], $app_info['wx_app_secret']);
        $eData_byte = $data['encryptedData'];
        $iv_byte = $data['iv'];
        $session_key = $data['session_key'];

        if($eData_byte && $iv_byte && $session_key){
            $result = json_decode($wxapp->decryptData($session_key, $iv_byte, $eData_byte),true); 
            $res = logic('share_info')->shareQun($data,$result['openGId']);
            $result['playable_num'] = $res;
            return $result;
        }else{
            $res = logic('share_info')->shareQun($data,0);
            return $res;
        }

    }
    
}