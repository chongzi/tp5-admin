<?php
namespace app\admin\validate;

use think\Validate;

class News extends Validate
{
    protected $rule =   [
        ['title', 'require|unique:news', '标题不能为空|该新闻已存在']
    ];
    

    protected $scene = [

    ];
    
    /*//验证唯一
    protected function chkTitle($value,$rule,$data)
    {
    	$where["title"] = $data["title"];
    	$info = model("News")->infoBy($where);
    	if($info)
    	{
    		return false;
    	}
    	return true;
    }*/
}