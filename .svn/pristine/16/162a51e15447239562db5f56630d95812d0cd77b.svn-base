<?php
namespace app\index\controller;
use base\payment\Pay;
use base\Lcs;
use pscws\Pscws;
use wxapp\Wxapp;
use wxapp\WxBizDataCrypt;
use base\Img;
class Index
{



    public function index()
    {
        /*$img = new Img();
        $img->test();*/
        exit();
        $pscws = new Pscws();
        $word_list = $pscws->separate('黄冈 小状元 数学 六年级 下册');
        dump($word_list);
        $book_names = config("myconfig.book_names");

        foreach ($book_names as $name) 
        {
            $score=0;
            $i=100;
            foreach ($word_list as $word) 
            {
                if(strpos($name, $word) !== false)
                {
                    $score = $score+1+$i/10000;
                }
                $i--;
            }
            $temp["name"] = $name;
            $temp["score"] = $score;
            if($score>1)
            {
                $arr[] = $temp;    
            }
        }
        array_multisort(array_column($arr,'score'),SORT_DESC,$arr);
        //$arr = array_slice ($arr,0,40);
        dump($arr);exit();
        //arsort($arr);
        //dump($arr);
        //exit();

        //print_r($result);  
        //exit();
  
/** 
 * UTF-8版 中文二元分词 
 */  



        /*$data["id"] = 1;
        $data["name"] = "shing";
        $data["sex"] = "nan";
        $data["sign"] = md5("id=1&name=shing&key=".config("myconfig.md5_key"));
        if(!chk_md5_sign($data,["id","name"],config("myconfig.md5_key")))
        {
            echo '<br>error';
            exit();
        }
        else
        {
            echo 'succ';
            exit();
        }
        exit();*/

    	/*$data["goods_ids_nums"] = [
    		["id"=>1,"num"=>2],
    		["id"=>2,"num"=>3],
    	];
    	//$data["goods_nums"] = [2,3];
    	$data["user_id"] = 1;
    	$data["app_id"] = 1;
    	$data["payway_name"] = "wxpay";
    	$data["imeil"] = "imeil_123456";
    	$data["coupon_money"] = 1;
    	$res = logic("orders")->pay($data);
    	if(!$res)
    	{
    		echo logic("orders")->getError();
    	}
    	dump($res);*/
        
        /*$res = logic("orders")->notifySucc("201801181345385858");
    	if(!$res)
    	{
    		echo logic("orders")->getError();
    	}*/


    	/*$pay = new Pay("wxpay");
    	$order_info = logic("orders")->infoByOrdersn("201801162114315343");
    	$pay->init($order_info);
        $mydata = $pay->native();
        $mydata = $pay->notifyUrl();*/

        /*$res = logic("goods_type")->ownerListByName("wk");
        dump($res);*/
        //$res = logic("user_goods")->isOwn(1,"wk",0);
        /*$data["title"] = "";
        $data["body"] = "aaaa";
        $res = logic("news")->add($data);
        if(!$res)
        {
            $msg = logic("news")->getError();    
            echo $msg;
        }
        dump($res);*/
        
        /*$obj = '{"iss":"John Wu JWT","iat":1441593502,"exp":1441594722,"aud":"www.example.com","sub":"jrocket@example.com","from_user":"B","target_user":"A"}';
        $str = base64_encode($obj);
        echo $str;*/
         $lcs = new Lcs();
        $book_names = config("myconfig.book_names");
        //dump($book_names);
        $i =0;

        foreach ($book_names as $name) {
            $res = $lcs->getSimilar($name,'黄冈小状元第');
            if($res>0.2)
            {
                $arr[$res*1000000] = $name;
            }
            //echo "$i:";

        }
       
        arsort($arr);
        dump($arr);
    }

    public function test($code='')
    {
        $wxapp = new Wxapp("wx59219a90869dea00","a02f9af1ce9553b7ddf6d212de478a52");
        $res = $wxapp->getPhoneNumber("013fAJy70DIMrH1or6y703qNy70fAJy5");
        dump($res);
        echo 'test';
    }

    public function test2()
    {
        $appid = 'wx4f4bc4dec97d474b';
    $sessionKey = 'tiihtNczf5v6AKRyjwEUhQ==';

$encryptedData="CiyLU1Aw2KjvrjMdj8YKliAjtP4gsMZM
                QmRzooG2xrDcvSnxIMXFufNstNGTyaGS
                9uT5geRa0W4oTOb1WT7fJlAC+oNPdbB+
                3hVbJSRgv+4lGOETKUQz6OYStslQ142d
                NCuabNPGBzlooOmB231qMM85d2/fV6Ch
                evvXvQP8Hkue1poOFtnEtpyxVLW1zAo6
                /1Xx1COxFvrc2d7UL/lmHInNlxuacJXw
                u0fjpXfz/YqYzBIBzD6WUfTIF9GRHpOn
                /Hz7saL8xz+W//FRAUid1OksQaQx4CMs
                8LOddcQhULW4ucetDf96JcR3g0gfRK4P
                C7E/r7Z6xNrXd2UIeorGj5Ef7b1pJAYB
                6Y5anaHqZ9J6nKEBvB4DnNLIVWSgARns
                /8wR2SiRS7MNACwTyrGvt9ts8p12PKFd
                lqYTopNHR1Vf7XjfhQlVsAJdNiKdYmYV
                oKlaRv85IfVunYzO0IKXsyl7JCUjCpoG
                20f0a04COwfneQAGGwd5oa+T8yO5hzuy
                Db/XcxxmK01EpqOyuxINew==";

$iv = 'r7BXXKkLb8qrSNn05n0qiA==';

$pc = new WxBizDataCrypt($appid, $sessionKey);
$errCode = $pc->decryptData($encryptedData, $iv, $data );
dump($data);
    }

    public function xsd()
    {
        /*
        echo 'aaa';
        $names = config("myconfig.book_names");
        dump($names);
        */


    }
}
