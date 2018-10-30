<?php
namespace app\admin\model;

use think\Model;
use think\Db;
class App extends BaseModel {
	//新增和更新自动完成
	protected $auto = [];
	protected $insert = [];
	protected $update = [];

	protected function initialize($model = '', $class = '') {
		$arr = explode("\\", __CLASS__);
		//$model = "admin/".$arr[count($arr)-1];//获取当前模型
		$model = $arr[count($arr) - 1]; //获取当前模型
		parent::initialize($model, __CLASS__);
	}


	public function viewInfoBy($ids){
		$sql = "select id,name,img,`desc` ,`button_name` ,`url`,click_num,xcx_img from s_more_app where id in(".$ids.') order by sort desc';
		return Db::query($sql);
	}

	//列表 带分页
    public function search($search){
        if(!empty($search)){
            if(is_numeric($search['title'])){
                $where = 'id = '.$search['title'];
            }
        }else{
            $where = '';
        }
        $arr = Db::table('s_app')->order('id desc')->where($where)->paginate(10,false,['query'=>request()->param()]);
       	return $arr;
   }
}