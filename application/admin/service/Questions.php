<?php
namespace app\admin\service;

use think\Model;
use think\Db;
class Questions extends BaseService
{


    //获取问题列表
	public function getQuestionList($data)
	{
		$app_id = $data["app_id"];
		$user_id = $data['user_id'];
		$app_info = $this->checkAppId($app_id,'question_num');
		if(!$app_info){	//验证应用id
			return false;exit;
		}
		if(!$this->checkUserId($user_id)){  //验证用户id
			return false;exit;
		}
		$where["app_id"] = $app_id;
		$where['status'] = 1;
		$where['user_id'] = $user_id;
		$num = $app_info['question_num'];
		$res = logic("questions")->viewListBy($where,'id,name,difficulty,options');
		$list = $res['list'];
		$b = logic('questions')->uniqueQuestion($res['questions_id']);		//获得不重复题目
		shuffle($list);		//打乱
		$n = 0;
		$q_list = [];
		$succ_num = logic('user_app')->infoBy(['user_id'=>$user_id,'app_id'=>$app_id],'succ_num')['succ_num'];
		$answer_time = json_decode(logic('app')->infoBy(['id'=>$app_id],'answer_time_arr')['answer_time_arr'],true);
		// if(count($list) >= $num + count($b)){		//重复值未超出题目总数处理
		// 	foreach ($list as $key => $value) {
		// 		if(!in_array($value['id'],$b)){
		// 			if($n < $num){
		// 				if($value['name']){
		// 					$answers = json_decode($value['options'],true);
		// 					if($succ_num < 2){					//通关二次加大难度
		// 						if($n < 5){
		// 							$dn = 1;
		// 						}else if($n >=5 && $n < 10){
		// 							$dn = 2;
		// 						}else{
		// 							$dn = 0;
		// 						}
		// 					}else{
		// 						$dn = 0;
		// 					}	

		// 					$answers = logic('questions')->handleAnswers($answers,$dn);		//开启答案数量调整
		// 					foreach ($answer_time as $k => $v) {
		// 						$no_start = explode('-',$v['answer_no'])[0];
		// 						$no_end = explode('-',$v['answer_no'])[1];
		// 						if($no_start <= $n + 1 && $no_end >= $n + 1){
		// 							$q_list[$n]['time'] = (int)$v['answer_time'];
		// 						}
		// 					}
		// 					$q_list[$n]['options'] = $answers;
		// 					$q_list[$n]['name'] = $value['name'];
		// 					$q_list[$n]['difficulty'] = $value['difficulty'];
		// 					$idArr[$n] = $value['id'];
		// 					$n++;	
		// 				}
		// 			}else{
		// 				break;
		// 			}
		// 		}
		// 	}
		// }else{
			$q_list = array_slice($list,0,$num);		//获取指定条数
			foreach ($q_list as $key => $value) {
				if($value['name']){
					$answers = json_decode($value['options'],true);
					if($succ_num < 2){				//通关二次加大难度
						if($key < 5){
							$dn = 1;
						}else if($key >=5 && $key < 10){
							$dn = 2;
						}else{
							$dn= 0;
						}
					}else{
						$dn= 0;
					}
					foreach ($answer_time as $k => $v) {
						$no_start = explode('-',$v['answer_no'])[0];
						$no_end = explode('-',$v['answer_no'])[1];
						if($no_start <= $key + 1 && $no_end >= $key + 1){
							$q_list[$key]['time'] = (int)$v['answer_time'];
						}
					}
					$answers = logic('questions')->handleAnswers($answers,$dn);				//开启答案调整
					$q_list[$key]['options'] = $answers;
					$q_list[$key]['name'] = $value['name'];
					$q_list[$key]['difficulty'] = $value['difficulty'];
					$idArr[] = $value['id'];
				}
			}	
		// }
		if($q_list){
			// if(count($list) >= $num + count($b)){
			// 	model('questions')->saveQuestionsInfo($user_id,$app_id,json_encode($idArr));
			// }
			return $q_list;
		}else{
			$this->error = "获取数据失败";
			return false;
		}
	}


	  //获取问题列表
	public function getQuestionMoney($data)
	{
		
		$app_id = $data["app_id"];
		$user_id = $data['user_id'];
		$app_info = $this->checkAppId($app_id,'question_num');
		if(!$app_info){	//验证应用id
			return false;exit;
		}
		if(!$this->checkUserId($user_id)){  //验证用户id
			return false;exit;
		}
		$num = $app_info['question_num'];
		$questions_log = Db('user_questions')->field('questions,upd_time')->where(['user_id'=>$user_id,'app_id'=>$app_id])->find();
		if($questions_log['questions']){
			$count = count(explode(',',$questions_log['questions']));
		}else{
			$count = 1;
		}
		if($questions_log){
			
			$answer_time = json_decode($app_info['answer_time_arr'],true);
			foreach ($answer_time as $key => $value) {
				$answer_no = explode('-',$value['answer_no']);
				if((int)$answer_no[0] <= $count && $count <= (int)$answer_no[1]){
					$time = $value['answer_time'];
				}
			}
			if($count >= $num){
				model('questions')->emptyQuestion($data);
				$questions_log['questions'] = '';
			}
			if(time() - $questions_log['upd_time'] > (int)$time){
				model('questions')->emptyQuestion($data);
				$questions_log['questions'] = '';
			}
			$question = model('questions')->selQuestionInfo($questions_log['questions'],$data);
			
		}else{
			$question = model('questions')->randQuestionInfo($data);
		}
		$options = json_decode($question['options'],true);
		foreach ($options as $key => $value) {
			unset($options[$key]['is_answer']);
		}
		unset($question['answer']);
		unset($question['id']);
		$question['options'] = $options;
		return $question;
	}

	public function getAnswerMoney($data){
		$app_id = $data["app_id"];
		$user_id = $data['user_id'];
		$ans = $data['answer'];
		$app_info = $this->checkAppId($app_id,'question_num');
		if(!$app_info){	//验证应用id
			return false;exit;
		}
		if(!$this->checkUserId($user_id)){  //验证用户id
			return false;exit;
		}
		if(!$ans){
			$this->error = '无参数answer';
			return false;exit;
		}
		$return = ['is_final'=>0,'is_answer'=>0];
		$num = $app_info['question_num'];
		$questions = Db('user_questions')->field('questions')->where(['user_id'=>$user_id,'app_id'=>$app_id])->find()['questions'];
		$count = count(explode(',',$questions));
		$answer_str = model('questions')->getAnswerMoney($data);
		$answer_arr = explode(',',$answer_str);
		$answer_id = $answer_arr[count($answer_arr)-1];
		$answer = model('questions')->info($answer_id,'answer')['answer'];
		if($count >= $num){
			$return['is_final'] = 1;
		}
		if($ans == $answer){
			$return['is_answer'] = 1;
		}
		return $return;
	}
}