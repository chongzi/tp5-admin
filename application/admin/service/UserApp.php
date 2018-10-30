<?php
namespace app\admin\service;

use think\Model;
use think\Db;
class UserApp extends BaseService
{

    /**
     * 通关后奖励平台币
     * @AuthorHTL
     * @DateTime  2018-05-30T11:40:11+0800
     * @param     int                  $app_id [应用id]
     * @return    [float]                      [奖励的金额]
     */
    private function getSuccMoney($app_id)
    {
    	$money = 0;
    	$app_info = logic("app")->info($app_id);
    	if($app_info)
    	{
    		$moneyArr = explode('-',$app_info["succ_money"]);
    		if(count($moneyArr) > 1){
    			$money = randomFloat($moneyArr[0],$moneyArr[1]);
    		}else{
    			$money = $moneyArr[0];
    		}
    	}
    	return (float)$money;
    }

    /**
     * 提交分数
     * @AuthorHTL
     * @DateTime  2018-05-30T11:42:12+0800
     * @param     [data]                   $data [description]
     * @return    [type]                         [description]
     */
	public function postScore($data)
	{
		$app_id = $data["app_id"];
		$user_id  = $data["user_id"];
		$score = $data["score"];
		$is_succ = $data["is_succ"];//是否通关
		$data['is_help'] = $data['is_help'] == 1 ? 1 : 0;
		$where["app_id"] = $app_id ;
		$where["user_id"] = $user_id ;
		$user_app_info = logic("user_app")->infoBy($where);
		$user_info = $this->checkUserId($user_id);
		$app_info = $this->checkAppId($app_id);
		if(!$app_info){	//验证应用id
			return false;exit;
		}
		if(!$user_info){	//验证用户id
			return false;exit;
		}
		if($app_info['user_play_num'] > 0){
			if($user_app_info['playable_num'] <= 0){	//挑战次数
				$this->error = '挑战次数不够';
				return false;exit;
			}
		}
		
		$money = $this->getSuccMoney($app_id);//根据配置获得通关后的奖励
		if(!empty($user_app_info)){		//判断该应用 该玩家有无数据,有更新，无添加
			if($data['is_help'] != 1){	//求助不扣次数
				$user_app_info["played_num"] +=1;//玩的次数+1
				if($app_info['user_play_num'] != 0){
					$user_app_info["playable_num"] -=1;//玩的剩余次数-1
				}
			}
			
			if($user_app_info["max_score"]<$score)//更新最高分数
			{
				$user_app_info["max_score"] = $score;
			}
			if($is_succ)
			{
				$user_app_info["succ_num"] += 1;
				$user_app_info["money"] += $money;
			}
			$user_app_info["upd_time"] = time();
			$user_app_info['form_id'] = $data['form_id'];
			$user_app_info['is_send'] = 0;
			$res = logic("user_app")->upd($user_app_info);
			$info = $user_app_info;
		}else{ 	//添加玩家数据
			if($is_succ){
				$info['succ_num'] = 1;
			}else{
				$info['succ_num'] = 0;
			}
			$info['user_id'] = $user_id;
			$info['app_id'] = $app_id;
			$info['open_id'] = $user_info['union_id'];
			$info['playable_num'] = $app_info['user_play_num'];
			$info['played_num'] = 1;
			$info['max_score'] = $score;
			$info['money'] = $money;
			$info['form_id'] = $data['form_id'];
			$info['add_time'] = time();
			$res = logic("user_app")->add($info,false);
		}
		
		if($res !== false)
		{	
			$d['user_id'] = $user_id;
			$d['app_id'] = $app_id;
			$d['score'] = $score;
			$d['is_succ'] = $is_succ ? $is_succ : 0;
			$d['addtime'] = time();
			$d['ip'] = $data['ip'];
			logic("user_play")->add($d,false);
			$app_info = logic("app")->infoBy(['id'=>$app_id]);
			return ['succ_money'=>(int)$app_info['succ_money'],'max_score'=>(int)$info['max_score'],'money'=>$info['money'],'played_num'=>$info['played_num'],'succ_num'=>$info['succ_num'],'playable_num'=>$info['playable_num']];
		}else{
			$this->error = "更新数据失败";
			return false;
		}
	}


	 /**
     * 获取用户信息
     * @AuthorHTL
     * @DateTime  2018-05-30T11:42:12+0800
     * @param     $app_id  $user_id 应用id和用户id
     * @return    array
     */
    public function getUserInfo($data){
    	$app_id = $data["app_id"];
		$user_id  = $data["user_id"];
		if(!$this->checkAppId($app_id)){
			return false;exit;
		}
		if(!$this->checkUserId($user_id)){
			return false;exit;
		}
		$where["app_id"] = $app_id ;
		$where["user_id"] = $user_id ;
		$user_app_info = logic("user_app")->getUserInfo($where);
		if($user_app_info){
			return $user_app_info;
		}else{
			$this->error = "获取数据失败";
			return false;
		}
    }

     /**
     * 获取排行
     * @AuthorHTL
     * @DateTime  2018-05-30T11:42:12+0800
     * @param     $app_id 应用id
     * @return    array
     */
    public function topList($data){
    	$app_id = $data["app_id"];
    	if(!$this->checkAppId($app_id)){
			return false;exit;
		}
		$where['app_id'] = $app_id;
		$topList = logic("user_app")->viewListBy($where,$data);
		if($topList){
			return $topList;
		}else{
			$this->error = "获取数据失败";
			return false;
		}
    }


      /**
     * 签到
     * @AuthorHTL
     * @DateTime  2018-05-30T11:42:12+0800
     * @param     $app_id  $user_id 应用id和用户id
     * @return    array
     */
    public function userSignIn($data){
    	if(!$this->checkAppId($data["app_id"])){
			return false;exit;
		}
		if(!$this->checkUserId($data["user_id"])){
			return false;exit;
		}
		$sign = logic("user_app")->userSignIn($data);
		if($sign){
			return $sign;
		}else{
			$this->error = "签到失败";
			return false;
		}
    }

       /**
     * 签到详情
     * @AuthorHTL
     * @DateTime  2018-05-30T11:42:12+0800
     * @param     $app_id  $user_id 应用id和用户id
     * @return    array
     */
    public function userSignInfo($data){
    	if(!$this->checkAppId($data["app_id"])){
			return false;exit;
		}
		if(!$this->checkUserId($data["user_id"])){
			return false;exit;
		}
		$signInfo = logic("user_app")->userSignInfo($data);
		if($signInfo){
			return $signInfo;
		}else{
			$this->error = "获取失败";
			return false;
		}
    }

}