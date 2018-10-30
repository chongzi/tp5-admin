<?php

namespace app\api\controller;

trait Crypt
{
    protected $isCrypt = true;

    private $private_key;
    private $rsa_len;

    /**
     * 获取RSA请求数据
     * @Author   slpi1
     * @Email    365625906@gmail.com
     * @DateTime 2018-03-02T11:49:38+0800
     * @return   void
     */
    protected function rsaData()
    {
        $this->log('rsa');

        $this->private_key = file_get_contents($this->private_key_file);

        //解密分片长度
        $this->rsa_len = 4096;

        //$urldata = isset($GLOBALS["HTTP_RAW_POST_DATA"]) ? $GLOBALS["HTTP_RAW_POST_DATA"] : '';
        $urldata = file_get_contents('php://input');
        if (strlen($urldata) > 0) {
            $this->data = json_decode($this->rsaDecode($urldata), true);
        } else {
            $this->data = [];
        }

        $this->log('getData:' . json_encode($this->data));
        if (isset($this->data['public_key'])) {
            $this->apkReturn($this->data);
        }
    }

    /**
     * 数据解密
     * @Author   slpi1
     * @Email    365625906@gmail.com
     * @DateTime 2018-03-02T11:50:04+0800
     * @param    mix                   $data phpinput
     * @return   array
     */
    protected function rsaDecode($data)
    {
        $data = gzinflate(substr($data, 10, -8));
        $this->log($data, 'debug');

        $data = base64_decode($data);
        $max = $this->rsa_len / 8;

        $check = false;
        // 获取到请求流数据，需要判断解密是否正确
        if ($data) {
            $check = true;
        }

        $pi_key = openssl_pkey_get_private($this->private_key); //这个函数可用来判断私钥是否是可用的，可用返回资源id Resource id
        $arr = array();
        $len = strlen($data);
        if ($len > $max) {
            while (strlen($data) > $max) {
                $pick = substr($data, 0, $max);
                $data = substr($data, $max);
                $result = openssl_private_decrypt($pick, $decrypted, $pi_key);

                $arr[] = $decrypted;
            }
        }
        openssl_private_decrypt($data, $decrypted, $pi_key);
        $arr[] = $decrypted;
        $decrypted = implode('', $arr);

        // 数据流有效，解密出错，公钥不正确
        if ($check && strlen($decrypted) == 0) {
            return json_encode([
                'public_key' => file_get_contents(APP_PATH . 'api' . DS . 'config' . DS . 'rsa_public_key.pem'),
            ]);
        }

        return $decrypted;
    }

    protected function compression($str)
    {
        $arr = $this->fixedArr();
        $str = base64_encode($str);
        $str = $this->encode($str, $arr);
        return gzencode($str, 9);
    }

    protected function fixedArr()
    {
        $arr = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l',
            'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y',
            'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L',
            'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y',
            'Z', '*', '!',
        );

        return $arr;
    }

    protected function encode($str, $arr)
    {
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

    protected function decode($str, $arr)
    {
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
}
