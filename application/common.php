<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

// Route::domain('admin','admin');
error_reporting(E_ERROR | E_PARSE);

/**
 * 逻辑层操作方法
 *
 * @param $name string 模块名称
 * @return
 */
function logic($name) {
	$is_exists = strpos($name, "admin/") === 0;
	if (!$is_exists) {
		$name = "admin/" . $name;
	}

	return think\Loader::model($name, "logic", false);
}

function loadActionName($name)
{
	$is_exists = stripos($name, "admin/") === 0;
	if($is_exists)
	{
		$name = str_ireplace("admin/","",$name);
	}
	$list = explode("_",$name);
	foreach ($list as $row) {
		$res .= ucfirst($row);
	}
	return $res;
}

/**
 * 服务层操作方法
 *
 * @param $name string 模块名称
 * @param $v string 应用id
 * @return
 */
function service($name,$v='') {
	if(!$v)
		$v = input("app_id",'');
	if($v)
		$v = "app".$v;
	$name = loadActionName($name);
	$class = "app\admin\service\\".$v."\\".$name;
	if(class_exists($class))
	{
		$name = "admin/".$v."/".$name;
	}
	else
	{
		$name = "admin/".$name;
	}
	return think\Loader::model($name, "service", false);
}

function validate($name) {
	$is_exists = strpos($name, "admin/") === 0;
	if (!$is_exists) {
		$name = "admin/" . $name;
	}
	echo $name;exit();
	return think\Loader::model($name, "validate", false);
}

/**
 * 数据层操作方法
 *
 * @param $name string 模块名称
 * @return
 */
function model($name) {
	$is_exists = strpos($name, "admin/") === 0;
	if (!$is_exists) {
		$name = "admin/" . $name;
	}
	return think\Loader::model($name, "model", false);
}

/**
 * 将对象转成数组
 *
 * @param $obj obj 对象
 * @return array 返回数组
 */
function object_to_array($obj) {
	if (is_object($obj)) {
		$array = (array) $obj;
	}
	if (is_array($obj)) {
		foreach ($obj as $key => $value) {
			$array[$key] = object_to_array($value);
		}
	}
	return $array;
}

/**
 * get方式curl请求
 * @param $url string 请求地址
 * @return string 返回请求结果
 */
function curl_get($url) {
	$UserAgent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; SLCC1; .NET CLR 2.0.50727; .NET CLR 3.0.04506; .NET CLR 3.5.21022; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_HEADER, 0); //0表示不输出Header，1表示输出
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_ENCODING, '');
	curl_setopt($curl, CURLOPT_USERAGENT, $UserAgent);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	$data = curl_exec($curl);
	curl_close($curl);
	//echo $data;
	return $data;
}

/**
 * post方式curl请求
 *
 * @param $url string 请求地址
 * @param $data array 请求数据
 * @return string 返回请求结果
 */
function curl_post($url, $post_data) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	// post数据
	curl_setopt($ch, CURLOPT_POST, 1);
	// post的变量
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
	$output = curl_exec($ch);
	curl_close($ch);
	return $output;
}

// rsa 加密
function rsa_encode($data, $pub_key) {

	// 加密分片长度
	$rsa_len = (int) C('RSA_LEN');
	$max = $rsa_len / 8 - 11;

	$pu_key = openssl_pkey_get_public($pub_key);
	$arr = array();
	$len = strlen($data);
	if ($len > $max) {
		while (strlen($data) > $max) {
			$pick = substr($data, 0, $max);
			$data = substr($data, $max);
			openssl_public_encrypt($pick, $crypted, $pu_key);

			$arr[] = $crypted;
		}
	}
	openssl_public_encrypt($data, $crypted, $pu_key);
	$arr[] = $crypted;

	$crypted = implode('', $arr);

	return gzencode(base64_encode($crypted), 9);
}

// rsa解密
function rsa_decode($data, $private_key) {
	$data = gzinflate(substr($data, 10, -8));
	//api_log($data,'rsa_decode',false);
	$data = base64_decode($data);
	//解密分片长度
	$rsa_len = (int) C('RSA_LEN');
	$max = $rsa_len / 8;

	$check = false;
	// 获取到请求流数据，需要判断解密是否正确
	if ($data) {
		$check = true;
	}

	$pi_key = openssl_pkey_get_private($private_key); //这个函数可用来判断私钥是否是可用的，可用返回资源id Resource id
	$arr = array();
	$len = strlen($data);
	if (strlen($data) > $max) {
		while (strlen($data) > $max) {
			$pick = substr($data, 0, $max);
			$data = substr($data, $max);
			openssl_private_decrypt($pick, $decrypted, $pi_key);

			$arr[] = $decrypted;
		}
	}
	openssl_private_decrypt($data, $decrypted, $pi_key);
	$arr[] = $decrypted;

	$decrypted = implode('', $arr);

	// 数据流有效，解密出错，公钥不正确
	if ($check && strlen($decrypted) == 0) {
		return json_encode(array('error' => 'public_key_error'));
	}

	return $decrypted;
}

//压缩并加密
function compression($str) {
	$arr = fixedArr();
	$str = base64_encode($str);
	$str = encode($str, $arr);
	return gzencode($str, 9);
}

//解压并解密
function decompression($str) {
	$arr = fixedArr();
	$tmp = gzinflate(substr($str, 10, -8));
	$tmp = decode($tmp, $arr);
	return base64_decode($tmp);
}

//加密
function encryption($str) {
	$arr = fixedArr();
	$str = base64_encode($str);
	return encode($str, $arr);
}
function fixedArr() {
	$arr = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
		'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l',
		'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y',
		'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L',
		'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y',
		'Z', '*', '!',
	);

	return $arr;
}

/**
 *
 * 加密函数
 *
 * $str 加密的字符串
 * $arr 固定数组
 */
function encode($str, $arr) {
	if ($str == null) {
		return "";
	}

	$rsstr = "x";
	$toarr = str_split($str);
	$arrlenght = count($arr);
	for ($i = 0; $i < count($toarr); $i++) {
		$string = ord($toarr[$i]) + ord($arr[$i % $arrlenght]);
		$rsstr .= $string . "_";
	}

	$rsstr = substr($rsstr, 0, -1);
	$rsstr .= "y";
	return $rsstr;
}

/**
 *
 * 解密函数
 *
 * $str 解密的字符串
 * $arr 固定数组
 */
function decode($str, $arr) {
	if ($str == '') {
		return '';
	}

	$first = substr($str, 0, 1);
	$end = substr($str, -1);

	if ($first == 'x' && $end == 'y') {
		$str = substr($str, 1, -1);
		$toarr = explode("_", $str);
		$arrlenght = count($arr);
		$rsstr = '';
		for ($i = 0; $i < count($toarr); $i++) {
			$string = $toarr[$i] - ord($arr[$i % $arrlenght]);
			$rsstr .= chr($string);
		}

		return $rsstr;
	} else {
		return "";
	}
}

/**
 * 记录日志
 *
 * @param $data array 要写到日志里的数据
 * @param $filename string 日志文件名
 * @param $json 保存格式，默认为json
 */
function mylog($data, $filename, $json = true) {
	$log_path = config('LOG_PATH') . '../' . date('y_m_d') . '_' . $filename . '.log';
	if ($json) {
		$data = json_encode($data);
	}
	\think\Log::write($data, 'Log', '', $log_path);
}

/**
 * 复制文件夹
 *
 * @param $src string 源文件夹
 * @param $dst array 目标文件夹
 */
function copy_dir($src, $dst) {
	$dir = opendir($src);
	@mkdir($dst);
	while (false !== ($file = readdir($dir))) {
		if (($file != '.') && ($file != '..')) {
			if (is_dir($src . '/' . $file)) {
				copy_dir($src . '/' . $file, $dst . '/' . $file);
				continue;
			} else {
				copy($src . '/' . $file, $dst . '/' . $file);
			}
		}
	}
	closedir($dir);
}

/**
 * 把数组元素转成为字符串，类似implode
 *
 * @param $char string 规定数组元素之间放置的内容，如","
 * @param $arr array 数组，如['热门','推荐']
 * @param $is_unique bool 是否过滤重复的内容
 * @return 字符串 如"热门,推荐"
 */
function myimplode($char, $arr, $is_unique = true) {
	if ($arr) {
		if ($is_unique) {
			$arr = array_unique($arr);
		}
		$res = implode($char, $arr);
	}
	return $res;
}

/**
 * 获取分页的html代码
 *
 * @param $count int 总记录数
 * @param $pagesize int 没页显示条数
 * @return 分页的html代码
 */
function get_page_html($count, $pagesize = 10) {
	$p = new \base\Page($count, $pagesize);
	$p->lastSuffix = false;
	$p->setConfig('header', '<span class="current">共%TOTAL_ROW%条&nbsp;共%TOTAL_PAGE%页</span>');
	$p->setConfig('prev', '上一页');
	$p->setConfig('next', '下一页');
	$p->setConfig('last', '末页');
	$p->setConfig('first', '首页');
	$p->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
	$p->parameter = input("param.");
	return $p->show();
}

/**
 * 根据某一字段的键值和另外字段的键获取另外字符的值
 *
 * @access public
 * @param $data arr 数组，如[["name"=>'zms',"sex"=>1,'age'=>18]]
 * @param @column_key 要获取值的键名,如'age'
 * @param @other_column_key 另外字段的键，如'name'
 * @param @other_column_val 另外字段的值，如'zms'
 * @return  返回某一列的值，如：18
 */
function get_colval($list, $column_key, $other_column_key, $other_column_val) {
	if ($list) {
		foreach ($list as $row) {
			if ($row[$other_column_key] == $other_column_val) {
				$res = $row[$column_key];
				break;
			}
		}
	}
	return $res;
}

/**
 * 验证md5签名是否正确
 *
 * @access public
 * @param $data arr 数组，如["name"=>'zms',"sex"=>1,'age'=>18,'sign'=>'xxxxxxxxxxxxxxx']
 * @param keys string 要进行加密的列，如"name,sex"
 * @return bool 签名正确返回true,签名错误返回false
 */
function chk_md5_sign($data, $keys, $md5_key) {
	$arr_key = explode(",", $keys);
	foreach ($data as $key => $value) {
		if (in_array($key, $arr_key)) {
			$mydata[$key] = $value;
		}
	}
	if (!$mydata) {
		return false;
	}

	ksort($mydata);
	$str = to_url_params($mydata);
	if (!$str) {
		return false;
	}

	$str = $str . "&key=" . $md5_key;
	$str = md5($str);
	if (strtolower($data["sign"]) == strtolower($str)) {
		return true;
	} else {
		return false;
	}
}

/**
 * 将数组转成url参数格式的字符串
 *
 * @access public
 * @param $data arr 数组，如["name"=>'zms',"sex"=>1,'age'=>18]
 * @return 返回字符串 如：name=zms&sex=1&age=18
 */
function to_url_params($data) {
	$buff = "";
	if ($data) {
		foreach ($data as $key => $value) {
			$buff .= $key . "=" . $value . "&";
		}
	}
	$buff = trim($buff, "&");
	return $buff;
}

/**
 * 将字符串日期转成数字格式日期
 *
 * @access public
 * @param $arr arr 数组，如["name"=>'zms',"sex"=>1,'age'=>18]
 * @param $keys str 要删除掉的key，如"sex,age"
 * @param $char str 将$keys分割成数组的字符串
 * @return 返回数组 如：["name"=>'zms']
 */
function del_arr_by_keys($arr, $keys, $char = ',') {
	$arr_key = explode($char, $keys);
	if (!$arr_key) {
		return $arr;
	}
	foreach ($arr as $key => $value) {
		if (in_array($key, $arr_key)) {
			unset($arr[$key]);
		}
	}
	return $arr;
}

/**
 * 将字符串日期转成数字格式日期
 *
 * @access public
 * @param $to_ndate str 日期，如：2018-2-1，2017-12-11
 * @return 返回数字可是日期 如：20180201，20171211
 */
function to_ndate($str_date = '') {
	if ($str_date) {
		$time = strtotime($str_date);
	} else {
		$time = time();
	}

	return date("Ymd", $time);
}

/**
 * 将字符串转成数组
 *
 * @access public
 * @param $str str 如：这是测试
 * @return array ['这','是','测','试']
 */
function str_to_array($str) {
	$length = mb_strlen($str, 'utf-8');
	$array = [];
	for ($i = 0; $i < $length; $i++) {
		$array[] = mb_substr($str, $i, 1, 'utf-8');
	}

	return $array;
}

function set_def_val($p, $def_val = null, $debug_val = null) {
	/*if(is_null($is_debug))
		    {
		        $is_debug = Env::get('database.is_debug',false);
	*/
	$is_debug = input("param.debug");
	if ($is_debug) {
		if (!isset($p) && !is_null($debug_val)) {
			$p = $debug_val;
		}
	} else {
		if (!isset($p) && !is_null($def_val)) {
			$p = $def_val;
		}
	}
	return $p;
}

function NDateToDate($date, $char = "-") {
	$arr = str_split($date);
	if (count($arr) != 8) {
		return 0;
	}
	for ($i = 0; $i < count($arr); $i++) {
		$res .= $arr[$i];
		if ($i == 3 || $i == 5) {
			$res .= $char;
		}
	}
	return $res;
}

function get_arrAttrVal($name, $val, $attr_name, $arr) {
	foreach ($arr as $row) {
		if ($row[$name] == $val) {
			$res = $row[$attr_name];
			break;
		}
	}
	return $res;
}

/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true) {
	if (function_exists("mb_substr")) {
		$slice = mb_substr($str, $start, $length, $charset);
	} elseif (function_exists('iconv_substr')) {
		$slice = iconv_substr($str, $start, $length, $charset);
		if (false === $slice) {
			$slice = '';
		}
	} else {
		$re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($re[$charset], $str, $match);
		$slice = join("", array_slice($match[0], $start, $length));
	}
	return $suffix ? $slice . '...' : $slice;
}

function toPercent($num) {
	return $num * 100;
}

// 用户表（user）pwd字段加密方式
function auth_code($string, $operation = 'DECODE', $key = '', $expiry = 0) {
	$ckey_length = 0;

	$key = md5($key ? $key : '9e13yK8RN2M0lKP8CLRLhGs468d1WMaSlbDeCcI');
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya . md5($keya . $keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);
	//$box = 100;

	$rndkey = array();
	for ($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for ($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for ($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if ($operation == 'DECODE') {
		if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc . str_replace('=', '', base64_encode($result));
	}
}


	function apkReturn($data, $code = 1, $msg = '') {
		$return = [
			'code' => $code,
			'msg' => $msg,
			'data' => '',
		];
		if ($code == 1) {
			$return['data'] = $data;
		} else {
			$return['msg'] = $data;
		}
		echo json_encode($return);
	}

	//发送模板消息
	function sendMoban($data){
         $moban = '{
          "touser": "'.$data['openid'].'",  
          "template_id": "'.$data['templateId'].'", 
          "page": "'.$data['page'].'",          
          "form_id": "'.$data['form_id'].'",         
          "data": {
              "keyword1": {
                  "value": "'.$data['name'].'", 
                  "color": "#173177"
              }, 
              "keyword2": {
                  "value": "'.$data['num'].'", 
                  "color": "#173177"
              }, 
              "keyword3": {
                  "value": "'.$data['wawa'].'", 
                  "color": "#173177"
              } , 
              "keyword4": {
                  "value": "'.$data['time'].'", 
                  "color": "#173177"
              }  
            
          },
          "emphasis_keyword": "" 
        }';   
        
        $access_token = returnToken($data['app_id'])['access_token'];
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=".$access_token;
        $data = returnApi($url,$moban);
        return json_decode($data,true);
    }


	function returnToken($app_id){
		$app_info = model('app')->info($app_id);
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$app_info['wx_app_id']."&secret=".$app_info['wx_app_secret'];
        $data = returnApi($url);
        return json_decode($data,true);
    }

    function returnApi($url,$data = null){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
                curl_setopt($ch,CURLOPT_SAFE_UPLOAD, false);
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

   	function randomFloat($min = 0, $max = 1) {
	    $num =  $min + mt_rand() / mt_getrandmax() * ($max - $min);
	    return sprintf("%.2f",$num);
	}