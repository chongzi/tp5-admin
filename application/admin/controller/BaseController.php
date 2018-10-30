<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class BaseController extends Base {

	public $model;
	public $page_size = 20;
	function __construct() {
		$this->model = request()->controller();
		parent::__construct();
	}

	public function succ($msg) {
		/*$data["msg"] = $msg;
			        $data["def_url"] = $_SERVER['HTTP_REFERER'];

			        $data["urls"] = $urls;
		*/

		$this->data["msg"] = "test";
		$this->data["def_url"] = $_SERVER['HTTP_REFERER'];
		$this->data["urls"] = $urls;
		return view("base/succ", $this->data);
	}

	public function index() {
		$list = logic($this->model)->search($search);
		$this->data["list"] = $list;
		return view("index", $this->data);
	}

	public function edit() {
		$id = input('param.id', '0');
		if ($id) {
			$info = logic($this->model)->info($id);
			$this->data["info"] = $info;
		}
		return view("edit", $this->data);
	}

	public function upd() {
		$id = input('post.id', '0');
		if ($id) {
			$res = logic($this->model)->upd();
			if ($res !== false) {
				$this->success('修改成功');
			} else {
				$msg = logic($this->model)->getError();
				$this->error($msg);
			}
		} else {
			$res = logic($this->model)->add();
			if ($res) {
				$this->success("添加成功", url("index"));
			} else {
				$this->error(logic($this->model)->getError());
			}
		}
	}

	public function setFlag() {
		$ids = input('id/a');
		$flag = input('flag_set', 0);
		if (!$ids) {
			$this->success('请选择要设置的内容');
			exit();
		}
		if (!$flag) {
			$this->error('请选择操作');
			exit();
		}
		$res = logic($this->model)->setFlag($ids, $flag);
		if ($res) {
			$this->success('设置成功');
		} else {
			$this->error('设置失败');
		}
	}
	public function unsetFlag() {
		$ids = input('id/a');
		$flag = input('flag_unset', 0);
		if (!$ids) {
			$this->success('请选择要取消设置的内容');
			exit();
		}
		if (!$flag) {
			$this->error('请选择操作');
			exit();
		}

		$res = logic($this->model)->unsetFlag($ids, $flag);
		if ($res !== false) {
			$this->success('取消设置成功');
		} else {
			$this->error('取消设置失败');
		}
	}

	public function del() {
		$ids = input('id/a');
		$res = logic($this->model)->del($ids);
		if ($res) {
			$this->success('删除成功');
		} else {
			$this->error('删除失败');
		}
	}
	public function updSort() {
		$sorts = input('sort/a');
		$res = logic($this->model)->updSort($sorts);
		if ($res) {
			$this->success('排序成功');
		} else {
			$this->error('排序失败');
		}
	}

	//排序 ajax
	public function sort(){
        $data = $this->request->post();
        $res = logic($this->model)->updAttr($data['id'],'sort',$data['sort']);
        if($res)
            return json(['state'=>'success']);
    	}

	public function updStatus() {
		$ids = input('id/a');
		$status = input('status', 0);
		if (!$ids) {
			$this->success('请选择要设置的内容');
			exit();
		}
		// if ($status) {
		// 	$this->success('请选择操作');
		// 	exit();
		// }
		
		$res = logic($this->model)->updAttr($ids, "status", $status);
		if ($res) {
			$this->success('操作成功');
		} else {
			$this->error('操作失败');
		}
	}

	//修改、添加 带图片
	public function updImg() {
		$file = request()->file('ico') ? request()->file('ico') : request()->file('img');

		$id = input('post.id', '0');
		$data = request()->post();
		if($file){
			$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads'. DS .$this->model);
			if($info){
				$data['img'] = config('http_type').$_SERVER['HTTP_HOST'].'/uploads/'.$this->model.'/'.str_replace('\\', '/', $info->getSaveName()); 
			}else{
		            // 上传失败获取错误信息
				$this->error($file->getError());exit;
			}
			
		}
		if ($id) {
			$res = logic($this->model)->upd($data,false);
			if ($res !== false) {
				$this->success('修改成功');
			} else {
				$msg = logic($this->model)->getError();
				$this->error($msg);
			}
		}else{
			$data['add_time'] = time();
			$res = logic($this->model)->add($data,false);
			if ($res !== false) {
				$this->success('添加成功');
			} else {
				$msg = logic($this->model)->getError();
				$this->error($msg);
			}
		}
	}

	//静态上传图片
	public function ajaxImg() {
		$file = request()->file('ico') ? request()->file('ico') : request()->file('img');
		if($file){
			$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads'. DS .$this->model);
			if($info){
				$arr['img'] = config('http_type').$_SERVER['HTTP_HOST'].'/uploads/'.$this->model.'/'.str_replace('\\', '/', $info->getSaveName()); 
				$arr['status'] = 'success';
			}else{
		            // 上传失败获取错误信息
				$arr['msg'] = $file->getError();
			}
			
		}
		echo json_encode($arr);
	}

}