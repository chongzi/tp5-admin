<?php
namespace app\admin\controller;
use think\Controller;
use base\Lists;
use base\RedisCache;
use base\struct\Tree;

class User extends BaseController
{
    function __construct()
    {
        parent::__construct();
    }

    public function index()
	{
		$search["stype"] = input("get.stype",2) ;
		$search["sval"] = trim(input("get.sval")) ;
		if($search["stype"] && $search["sval"])
		{
			if($search["stype"] == 1)
				$search["id"] = $search["sval"];
			else if($search["stype"] == 2)
				$search["name"] = $search["sval"];
			else if($search["stype"] == 3)
				$search["mobile"] = $search["sval"];
		}
        $search['start_time'] = input("get.start_time");
        $search['end_time'] = input("get.end_time");

        if($search['start_time'] && $search['end_time'])
        {
        	$search["reg_date"] = array(between,array(to_ndate($search['start_time']),to_ndate($search['end_time'])));
        }
        $search["app_id"] = input("get.app_id") ;
        $search["agent_id"] = input("get.agent_id") ;
        if($search["agent_id"])
        {
        	$info = logic("agent")->info($search["agent_id"]);
        	if($info && $info["pid"] == 0)
        	{
        		$search["agent_pid"] = $agent_id;
        	}
        	else
        	{
        		$search["agent_id"] = $agent_id;
        	}
        }

        $page = input("param.p",1) ;
        $page_size = 10;
        $mysearch = del_arr_by_keys($search,"stype,sval,start_time,end_time");
        $list = logic("user")->search($mysearch,"id desc",$page,$page_size,$count) ;
        $page_html = get_page_html($count,$page_size);
        $this->data["list"] = $list;
        $this->data["search"] = $search;
        $this->data["page_html"] = $page_html;  

        $app_list = logic("app")->listBy() ;
        $agent_list = logic("agent")->listBy() ;

        $tree = new Tree($agent_list,"title");
        $agent_list = $tree->getArrTreeSortAdorn();

        $this->data["app_list"] = $app_list;
        $this->data["agent_list"] = $agent_list;

        if(!empty($search) && is_array($search)){
            $this->data = array_merge($this->data,$search);
        }
		return view("index", $this->data);
	}

    public function edit()
    {
        $id = input('param.id','0') ;
        if($id)
        {
            $info = logic($this->model)->info($id);

            $this->data["info"] = $info;
        }
        $app_list = logic('App')->listBy(null);
        $this->data['app_list'] = $app_list;
        return view("edit", $this->data);
    }
}