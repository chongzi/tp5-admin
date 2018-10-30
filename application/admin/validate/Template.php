<?php
namespace app\admin\validate;

use think\Validate;

class {module_name} extends Validate
{

	/*protected $rule =   [
        'email'              => "require|email|unique:user|checkUserEmail:net.com",  //必填。验证邮箱、验证是否是内部邮箱.验证邮箱在user表中唯一性
        'code'               => 'require|number|length:6',    //验证码 必填 整数 长度是6位
        'sex'                => 'number|in:0,1',
        'name'               => 'require|unique:user',
        'pwd'           => 'require|length:6,20',
        'pwd2'          => 'require|confirm:password',  //必填。验证字段和password字段的值相等
        'clause'             => 'accepted',
    ];
    //验证不符返回msg
    protected $message  =   [
        'email.require'      => '账号必须',
        'email.email'       => '请输入正确邮箱地址！',
        'email.unique'      => '用户名已存在',
        'code.require'      => '验证码必填',    
        'code.number'       => '验证码格式为数字',
        'code.length'       =>'验证码格式为6位的数字',
        'name.require' => '用户名必填',
        'name.unique' => '用户名已存在',
        'pwd.require'       => '密码必填',
        'pwd.length'       => '密码应在6-20之间',
        'pwd2.require'   => '确认密码必填',
        'pwd2.confirm'   => '确认密码与密码内容不一致',        
        'sex.number'    => '性别必须是数字',
        'sex.in'   => '性别必须是0-1之间',
        'clause.accepted' => '请同意协议',
    ];
    //需要指定验证位置 和字段
    protected $scene = [
        'add'                 =>  ['email','password'],
        'edit'                =>  ['name'],
    ];*/

    protected $rule =   [
        
    ];
    
    protected $message =   [
        
    ];

    protected $scene = [
        
    ];   
    
    /*protected function chkTitle($value,$rule,$data)
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