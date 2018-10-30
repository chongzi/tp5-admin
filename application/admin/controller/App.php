<?php
namespace app\admin\controller;
use think\Cache;
use think\Controller;
use think\Db;
class App extends BaseController {
	function __construct() {
		header('Content-Type: text/html; charset=utf-8');
		parent::__construct();
	}

	public function index() {
		$search['title'] = input("param.title",'');
		$list = logic($this->model)->search($search);
		$this->data["search"] = $search;
		$this->data["list"] = $list;
		$this->data["app_opt_status"] = logic('App')->opt_status;
		$this->data["count"] = count($list);
		return view("index", $this->data);
	}



	public function edit() {
		$id = input('param.id', '0');
		// if ($id) {
		$info = logic($this->model)->info($id);
		$info['app_gift_id'] = explode(',',$info['app_gift_id']);
		$info['more_app_id'] = explode(',',$info['more_app_id']);
		$info['sign_arr'] = json_decode($info['sign_arr'],true);
		$info['share_title'] = explode(',',$info['share_title']);
		$info['share_ico'] = json_decode($info['share_ico'],true);
		$info['answer_time_arr'] = json_decode($info['answer_time_arr'],true);
		// var_dump($info['share_ico']);exit;
		// var_dump($info['sign_arr']);exit;
		$giftList = logic('gift')->search();
		$moreApp = logic('more_app')->search();
		$shareCount = count(logic('share_info')->listBy(['app_id'=>$id],'id'));
		$playCount = count(logic('user_play')->listBy(['app_id'=>$id],'id'));
		$this->data["info"] = $info;
		$this->data["type"] = $id == 0 ? 'add' : 'edit';
		$this->data["giftList"] = $giftList;
		$this->data["moreApp"] = $moreApp;
		$this->data["shareCount"] = $shareCount;
		$this->data["playCount"] = $playCount;
		$this->data['opt_status'] = logic('App')->opt_status;
		// }
		return view("edit", $this->data);
	}

	public function upd() {	
		$id = input('post.id', '0');
		$data = request()->post();

		foreach ($data['sign_type'] as $key => $value) {
			$sign[$key] = ['sign_type'=>$value,'sign_value'=>$data['sign_value'][$key]];
		}
		foreach ($data['answer_no'] as $key => $value) {
			$answer[$key] = ['answer_no'=>$value,'answer_time'=>$data['answer_time'][$key]];
		}
		$data['sign_arr'] = json_encode($sign);			//签到json
		$data['answer_time_arr'] = json_encode($answer);			//答题时间json
		if($data['share_title'])
			$data['share_title'] = implode(',',$data['share_title']);
		if ($id) {
			// var_dump($data['key']);exit;
			$files = request()->file('share_ico');
			foreach ($data['key'] as $key => $value) {
				if($value !== '') $k[] = $value;
			}
			if($files){
				  // 获取表单上传文件
			    foreach($files as $key => $file){
			        // 移动到框架应用根目录/public/uploads/ 目录下 
			       	$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads'. DS .$this->model);
					if($info){
						$data['share_ico'][$k[$key]] = config('http_type').$_SERVER['HTTP_HOST'].'/uploads/'.$this->model.'/'.str_replace('\\', '/', $info->getSaveName()); 
					}else{
				            // 上传失败获取错误信息
						$this->error($file->getError());exit;
					}
			    }
			}
			if(!empty($data['share_ico'])){
				$data['share_ico'] = json_encode($data['share_ico']);
			}
			$data['app_gift_id'] = implode(',',$data['gift']);
			$data['more_app_id'] = implode(',',$data['more_app_id']);
			$res = logic($this->model)->upd($data,false);
			if ($res !== false) {
				$info = logic($this->model)->info($id);
				Cache::set('wx_app_id_' . $id, $info['wx_app_id']);
				Cache::set('wx_app_secret_' . $id, $info['wx_app_secret']);
				  // 移动到框架应用根目录/public/uploads/ 目录下
				$this->success('修改成功');
			} else {
				$msg = logic($this->model)->getError();
				$this->error($msg);
			}
		} else {
			$res = logic($this->model)->add($data);
			if ($res) {
				Cache::set('wx_app_id_' . $id, $res['wx_app_id']);
				Cache::set('wx_app_secret_' . $id, $res['wx_app_secret']);
				$this->success("添加成功", url("index"));
			} else {
				$this->error(logic($this->model)->getError());
			}
		}
	}

	function apiList(){
		return view();
	}

	//发送模板消息
	function sendMsg(){
		$list = logic('user_app')->getNoSignUser();
		if(!empty($list)){
			// var_dump($list);exit;
			foreach ($list as $key => $value) {
				$sql = 'select id from s_user_sign_in where status = 1 and user_id = '.$value['user_id'].' and app_id = '.$value['app_id'];
				$user_info = logic('user_app')->infoBy(['user_id'=>$value['user_id'],'app_id'=>$value['app_id']],'app_id,open_id,form_id');
				$app_info = logic('app')->infoBy(['id'=>$user_info['app_id']],'title,template_id');
				if($app_info['template_id'] == false || $app_info['template_id'] == 'the formId is a mock one'){
					continue;
				}
				$sign_info = model('app')->infoBy(['id'=>$value['app_id']],'sign_arr');
        		$sign_arr = json_decode($sign_info['sign_arr'],true);
				$data['uid'] = $value['user_id'];
				$data['app_id'] = $value['app_id'];
				$data['num'] = count(Db::query($sql));
				$data['templateId'] = $app_info['template_id'];
		        $data['openid'] = $user_info['open_id'];
		        $data['page'] = 'pages/index/index';
		        $data['form_id'] = $user_info['form_id'];
		        $data['name'] = '你今天还没签到哦，快去补到吧！';
		        $dongxi = $sign_arr[$data['num']];
		        if($dongxi['sign_type'] == 'money'){
		        	$data['wawa'] = '继续签到赠送金币'.$dongxi['sign_value'].'个';
		        }else if($dongxi['sign_type'] == 'played_num'){
		        	$data['wawa'] = '继续签到赠送挑战次数'.$dongxi['sign_value'].'次';
		        }
		        $data['time'] = date('Y-m-d H:i:s');
		        $result = sendMoban($data);
		        if($result['errcode'] == 0 && $result['errmsg'] == 'ok'){
		            $info['user_id'] = $value['user_id'];
		            $info['send_time'] = time();
		            $info['app_id'] = $value['app_id'];
		            $info['send_gift'] = $data['wawa'];
		            $info['sign_num'] = $data['num'];
		            logic('user_app')->updBy(['is_send'=>1],['user_id'=>$value['user_id'],'app_id'=>$value['app_id']]);
		            Db::table('s_template_msg')->insert($info);
		        }else{
		            logic('user_app')->updBy(['is_send'=>1],['id'=>$id]);
		        }
			}
			
		}
		
	}
}