<?php

namespace app\api\controller;
use think\Controller;
use wxapp\Transfers;
use wxapp\Wxapi;
class Wx extends Controller{
    protected $appid = 'wxdd3ed0683b4e99e4';
    protected $appsecret = '3ab25c3d7d2f7af118d96e1a968d1b65';
    protected $appkey = 1495755242;
    protected $apikey = '4gz3PyAYGeaCOYfuc2IvPFEjjHiTaJ7T';
    protected $app_id = 0;
    public static $app_info = [];
    public function __construct(){
    	$this->app_id = (int)input('app_id');
    	self::$app_info = model('app')->infoBy(['id'=>(int)input('app_id')]);
    }

    public function token(){
        define("TOKEN", "yangcheng");
        // file_put_contents('api.log',json_encode(input('app_id')),FILE_APPEND);
        $echoStr = $_GET["echostr"];
        if (!isset($echoStr)) {
            $this->responseMsg();
        }else{
            $this->valid($echoStr);
        }
        
    }

    public function valid($echoStr){
        if($this->checkSignature()){  
            echo $echoStr;  
            exit;  
        } 
    }
    public function checkSignature(){  
        $signature = $_GET["signature"];  
        $timestamp = $_GET["timestamp"];  
        $nonce = $_GET["nonce"];      
        
        $token = TOKEN;  
        $tmpArr = array($token, $timestamp, $nonce);  
        sort($tmpArr);  
        $tmpStr = implode( $tmpArr );  
        $tmpStr = sha1( $tmpStr );  
          
        if( $tmpStr == $signature ){  
            return true;  
        }else{  
            return false;  
        }  
    } 


      public function responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);
            $fromUsername = $postObj->FromUserName; //发送方微信号
            $toUsername = $postObj->ToUserName; //  开发者微信公众帐号
            switch ($RX_TYPE)
            {
                case "text":
                    $resultStr = $this->receiveText($postObj);
                    break;
                case "event":
                    $resultStr = $this->receiveEvent($postObj);
                    break;
                case "location":
                    $word = '停车场';          
                    $Location_X = $postObj->Location_X;
                    $Location_Y = $postObj->Location_Y;
                    $Label = $postObj->Label;
                    $locationArr = $this->getLocationArr();
                    $locationArr[$fromUsername.'_x'] = $Location_X;
                    $locationArr[$fromUsername.'_y'] = $Location_Y;
                    file_put_contents($this->locationFile,json_encode($locationArr));
            
                    break;
                case "image":
                    //$keyword = trim($postObj->Content); //用户发送的消息内容
                    $PicUrl = $postObj->PicUrl; //存储用户发来的图片链接地址,通过这个地址可以将图片另存为本地。
                    $MsgType = $postObj->MsgType;   //消息的类型
                    $MediaID = $postObj->MediaId;   //图片消息媒体ID，根据这个值，可以发送图片信息
                    $CreateTime = intval($postObj->CreateTime); //消息的创建时间,并且把这个时间转换成整数。
                    $formTime = date("Y-m-d H:i:s",$CreateTime);
                    $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        <FuncFlag>0</FuncFlag>
                        </xml>";
                    $time = time();
                    if ($MsgType == "image") {
                        // $GLOBALS['wx']->wxKfSendImgMsg($fromUsername,$PicUrl,$PicUrl,'还给你');
                        break;
                    }   
                default:
                    $resultStr = "";
                    break;
                }
            echo $resultStr;
        }else {
            echo "";
            exit;
        }
    }


     private function receiveText($object){   
        $fromUsername = $object->FromUserName;
        $toUsername = $object->ToUserName;
        $str = trim($object->Content);
        $time = time();
        if($str != ''){
            if(strlen($str) == 14 && mb_substr($str,0,1,"UTF-8") == 'T'){
                $order_info = model('order_change')->infoBy(['uniqid'=>$str,'status'=>0]);
                if(!empty($order_info)){
                	$allow_change_money = explode('/',self::$app_info['allow_change_money']);
                	if(count($allow_change_money) > 1){
                		$limit = (float)$allow_change_money[$order_info['change_num']-1];
                	}else{
                		$limit = (float)$allow_change_money[0];
                	}
                   	if($order_info['money'] <= $limit){
                		$danhao = date('Ymd') .$order_info['id']. str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
                		$wxPay  = new Transfers(self::$app_info['app_key'],self::$app_info['app_id'],self::$app_info['app_secret'],self::$app_info['pay_key']);
                		$result = $wxPay->createJsBizPackage($fromUsername,$order_info['money'],$danhao,'');
                		file_put_contents('api.log',json_encode($result),FILE_APPEND);
                		if($result['code'] == 1){
                			$data['order_sn'] = $danhao;
                			$data['openid'] = $fromUsername;
                			$data['change_time'] = $time;
                			$data['msg_id'] = $object->MsgId;
                			$data['status'] = 9;
                			$data['id'] = $order_info['id'];
                			$re = model('order_change')->updBy($data,['id'=>$order_info['id']]);
                			if($re){
                				$msg = '提现成功';
                			}else{
                				$res = model('order_change')->updBy($data,['id'=>$order_info['id']]);
                				$msg = '提现成功';
                			}
	                    }else{
	                        $msg = $result['errMsg'];
	                    }
	                   	$d['order_sn'] = $danhao;
						$d['openid'] = $fromUsername;
				        $d['order_id'] = $order_info['id'];
				        $d['add_time'] = $time;
				        $d['money'] = $order_info['money'];
				        $d['uniqid'] = $str;
				        $d['msg'] = $msg;
				        model('order_change_log')->add($d);
	                    file_put_contents('api.log',json_encode($result),FILE_APPEND);
                	}else{
                		$msg = '提现金额超过限定值';
                	}
                }else{
                    $msg = '无效的提现码';
                }
            }else{
                $msg = '感谢关注 '.self::$app_info['title']."\n"."输入“提现码”可以进行提现,如有疑问联系客服！";
            }
            $textTpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            <FuncFlag>%d</FuncFlag>
            </xml>";
            $resultStr = sprintf($textTpl,$fromUsername, $toUsername,$time, $msg, 0);
        }
        return $resultStr;

    }

    public function test(){

    	$num = 0.1 + mt_rand() / mt_getrandmax() * (0.1);
    	
        $danhao = date('Ymd') .$order_info['id']. str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        $data['order_sn'] = $danhao;
        $data['change_time'] = time();
        $data['msg_id'] = 1;
        $data['status'] = 9;
        $data['id'] = 1;
        $re = model('order_change')->updBy($data,['id'=>1]);
        
        $danhao = date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        $wxPay  = new Transfers($this->appkey,$this->appid,$this->appsecret,$this->apikey);
        $re = $wxPay->createJsBizPackage('oxoLP1DKTA1CIlqxjNOj_6CYvTfU',0.3,$danhao,'');
        var_dump($re);
    }

    public function receiveEvent($object){  
        $contentStr = "";
        switch ($object->Event){ 
            case "SCAN":
                $contentStr[] = array("Title" =>"感谢扫码", 
                "Description" =>self::$app_info['title'], 
                "PicUrl" =>self::$app_info['ico'], 
                "Url" =>self::$app_info['ico']);
                break;
            case "subscribe":
                $contentStr = '感谢关注 '.self::$app_info['title']."\n"."输入“提现码”可以进行提现,如有疑问联系客服！";
                break;
            case "unsubscribe":
                break;
            case "memberCard":
                $contentStr[] = array("Title" =>"感谢扫码", 
                "Description" =>self::$app_info['title'], 
                "PicUrl" =>self::$app_info['ico'], 
                "Url" =>self::$app_info['ico']);
                break;
            break;
            default:
                $contentStr = '感谢关注 '.self::$app_info['title']."\n"."输入“提现码”可以进行提现,如有疑问联系客服！";
                break;      

        }
        if (is_array($contentStr)){
            $resultStr = $this->transmitNews($object, $contentStr);
        }else{
            $resultStr = $this->transmitText($object, $contentStr);
        }
        return $resultStr;
    }
    
     private function transmitNews($object, $arr_item, $funcFlag = 0){
        //首条标题28字，其他标题39字
        if(!is_array($arr_item))
            return;

        $itemTpl = "<item>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <PicUrl><![CDATA[%s]]></PicUrl>
        <Url><![CDATA[%s]]></Url>
        </item>";
        $item_str = "";
        foreach ($arr_item as $item)
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);

        $newsTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[news]]></MsgType>
        <Content><![CDATA[]]></Content>
        <ArticleCount>%s</ArticleCount>
        <Articles>
        $item_str</Articles>
        <FuncFlag>%s</FuncFlag>
        </xml>";

        $resultStr = sprintf($newsTpl, $object->FromUserName, $object->ToUserName, time(), count($arr_item), $funcFlag);
        return $resultStr;
    }

     private function transmitText($object, $content, $funcFlag = 0){
        $textTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[text]]></MsgType>
        <Content><![CDATA[%s]]></Content>
        <FuncFlag>%d</FuncFlag>
        </xml>";
        $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content, $funcFlag);
        return $resultStr;
    }

    public function menu(){
    	$wx = new Wxapi($this->appid,$this->appsecret);
    	$data= '{
		      "button":[
			     {    
			          "type":"view",
			          "name":"提现流程",
			          "url":"https://www.baidu.com"
			      },
			      {
			           "name":"更多福利",
			           "sub_button":[
			           {    
			               "type":"click",
			               "name":"游戏福利",
			               "key":"game01"
			            },
			            {
			               "type":"click",
			               "name":"娃娃福利",
			               "key":"game02"
			            }]
			       }
			   ]
		 }';

 	 	$re=  $wx->wxMenuCreate($data);
    	var_dump($re);exit;
    }

}
