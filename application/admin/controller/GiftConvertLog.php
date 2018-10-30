<?php
namespace app\admin\controller;
use think\Controller;
use base\Lists;
use base\RedisCache;
use struct\Tree;
class GiftConvertLog extends BaseController
{
    function __construct()
    {   
        header('Content-Type: text/html; charset=utf-8');
        parent::__construct();
    }

    public function index() {
        $search['title'] = input("param.title",'');
        $search['gift_name'] = input("param.gift_name",'');
        $search['phone'] = input("param.phone",'');
        $search['name'] = input("param.name",'');
        $search['app_id'] = input("param.app_id",'');
        $search['status'] = input("param.status",'');
        $app_info = logic('app')->listBy();
		$list = logic('gift_convert_log')->search($search);
		$this->data["list"] = $list;
        $this->data["phone"] = $this->getUnique($list,'phone');
        $this->data["app_id"] = $this->getUnique($list,'app_id');
        $this->data["name"] = $this->getUnique($list,'name');
        $this->data["gift_name"] = $this->getUnique($list,'gift_name');
		$this->data["search"] = $search;
        $this->data["count"] = logic('gift_convert_log')->search($search,true);
        foreach ($app_info as $key => $value) {
            $app[$value['id']] = $value['title'];
        }
        $this->data["app_info"] = $app;
		return view("index", $this->data);
	}

    public function edit() {
        $id = input('param.id', '0');
        if ($id) {
            $info = logic($this->model)->info($id);;
            $this->data["info"] = $info;
            $this->data['gift_state'] = config('gift_state');
        }
        return view("edit", $this->data);
    }

    public function upd(){
        $id = input('param.id', '');
        if($id){
             $info['status'] = input('param.status', 1);
            if($info['status'] == 2){
                $info['send_time'] = time();             
            }else if($info['status'] == 99){
                $info['get_time'] = time();
            }
            $res = logic($this->model)->updBy($info,['id'=>$id]);
            if($res){
                $this->success('修改成功');
            }else{
                $this->error('无修改');
            }
        }
    }

    //数组去重
    function getUnique($list,$param){
        foreach ($list as $key => $value) {
            if($value[$param])
                $arr[] = $value[$param];
        }
        return array_unique($arr);
    }
}