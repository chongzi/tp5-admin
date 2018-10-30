<?php
namespace app\admin\controller;
use think\Controller;
use base\ModuleFile;
use think\Request;

class Module extends BaseController
{
	public function index()
    {
        $search["table_name"] = input("get.table_name");
        if($search["table_name"])
        {
            $arr_table[] = $search["table_name"];
        }
        else
        {
            $list = logic("admin")->tableList() ;
            $arr_table = $this->get_array_value($list);
        }
        $list = ModuleFile::lists($arr_table);
        
        $this->data["list"] = $list;
        $this->data["search"] = $search;
        return view("index", $this->data);
    }
    
    private function get_array_value($list)
    {
        $model_filter_list = explode(",",config("model_filter"));
        foreach ($list as $row) 
        {
            foreach ($row as $key => $value) 
            {
                if(!in_array($value,$model_filter_list))
                {
                    $new_list[] = $value;    
                }
            }
        }
        return $new_list;
    }

    public function creates()
    {
        $arr_type = input('post.type_name/a');
        if(ModuleFile::creates($arr_type))
        {
            $this->success('生成成功！');
        }
        else
        {
            $this->error('生成失败！');
        }
    }
}