<?php
namespace app\admin\controller;
use think\Controller;
use base\Lists;
use base\RedisCache;
use struct\Tree;
use think\Request;
class UserApp extends BaseController
{
    function __construct()
    {
        parent::__construct();
    }

    public function index(Request $Request) {
        $search['title'] = input("param.title",'');
        $search['app_id'] = input("param.app_id",'');  
        $search['order'] = input("param.order",'');  
        $list = logic($this->model)->search($search);
        $app_list = logic('app')->listBy();
        $this->data["list"] = $list;
        $this->data["app_list"] = $app_list;
        $this->data["search"] = $search;
        $this->data["count"] = logic($this->model)->search($search,true);
        return view("index", $this->data);

    }

    public function edit() {
        $id = input('param.id', '0');
        if ($id) {
            $info = logic($this->model)->info($id);
            $share_info = logic('share_info')->listBy(['user_id'=>$info['info']['user_id'],'app_id'=>$info['info']['app_id']]);
            $sign_info = logic('user_app')->getSignInfo(['user_id'=>$info['info']['user_id'],'app_id'=>$info['info']['app_id']]);
            $template_info = logic('user_app')->getTemplateInfo(['user_id'=>$info['info']['user_id'],'app_id'=>$info['info']['app_id']]);
            $this->data["info"] = $info['info'];
            $this->data["list"] = $info['list'];
            $this->data["share_info"] = $share_info;
            $this->data["sign_info"] = $sign_info;
            $this->data["template_info"] = $template_info;
        }
        return view("edit", $this->data);
    }
}