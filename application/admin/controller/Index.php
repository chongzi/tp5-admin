<?php

namespace app\admin\controller;

class Index extends Base
{

    //图片混淆，改变图片指纹
    public function jpgMix()
    {
        dump(exif_read_data("1.jpg"));
        dump(exif_read_data("2.jpg"));
        dump(exif_read_data("3.jpg"));
        exit();
        $file_name = "1.jpg";
        $fp = fopen($file_name ,'r+');

        fseek($fp,-2,SEEK_END);    
        $iend=fread($fp,2);
        $unp = unpack('n',$iend);
        
        if($unp[1] == 65497)//对应16进制：0xffd9，jpg图片结尾
        {
            //echo 'end';
            $num = rand(1000,5000);
            $comment = $this->randStr($num);

            fseek($fp,-2,SEEK_END);
            $bytes="\xFF\xFE#{$comment}\xff\xd9";
            fwrite($fp,$bytes);
            flush();
        }
    }

    public function pngMix()
    {
        exec("strings -a -t x 1.png |grep IEND",$output,$status);
        dump($status);
        dump($output);exit();
        $status= 0 ;
        $output[0] = "270 IEND";
        if($status == 0)
        {
            $last = $output[0];//如：270 IEND
            $rule = "/(\w+)\s+IEND$/";
            preg_match($rule,$last,$arr);
            $last_index = hexdec(("0x".$arr[1]));//如0x270的10进制624
            //echo $last_index;
            $fp = fopen("1.png" ,'r+');
            fseek($fp,$last_index-4);
            $end = fread($fp,4); 
            if($end == "\0\0\0\0")
            {
                $num = rand(1000,50000);
                $comment = $this->randStr($num);

                fseek($fp,$last_index);
                $bytes="\x00{$comment}";
                fwrite($fp,$bytes);
                flush();
                echo 'succ';
            }
            else
            {
                echo 'fail';
            }
        }
    }

    public function test()
    {
        $fp = fopen("1.txt" ,'r+');
        fseek($fp,5,SEEK_SET);
        fwrite($fp,'bbb');
        flush();
    }

    public function randStr($len)
    {
        $list = [a,b,c,d,e,f,g,h,i,j,k,l,m,n,0,1,2,3,4,5,6,7,8,9];
        for($i=0;$i<$len;$i++)
        {
            $index = rand(0,count($list)-1);
            $char_list .= $list[$index];
        }
        return $char_list;
        /*foreach ($char_list as &$char) {
            $char = ord($char);
        }
        foreach ($char_list as $char) {
            $res = pack('c*',$char);
            dump($res);
        }
        $res = pack('c*',$char_ord);
        echo($res);echo "<br>";
        return $res;*/
    }

    public function index()
    {
        return view('app/index', $this->data);
        //$this->pngMix();
        //$this->test();
    }



    public function profile()
    {
        return view('profile', $this->data);
    }

    public function update()
    {
        $avatar = $this->request->post('avatar');
        $nick_name = $this->request->post('nick_name');
        $new_password = $this->request->post('new_password');
        $re_password = $this->request->post('re_password');
        $password = $this->request->post('password');

        $validate = $this->validate([
            'password' => $password,
            'nick_name' => $nick_name,
            'pwd' => $new_password,
        ], [
            'password' => 'require',
            'nick_name' => 'max:25',
            'pwd' => 'length:6,25',
        ]);

        if ($validate !== true) {
            $this->error('密码必填');
        }

        if ($this->admin->checkPwd($password)) {
            $this->admin->nick_name = $nick_name;
            $this->admin->avatar = $avatar;

            if ($new_password) {
                if ($new_password == $re_password) {
                    $this->admin->pwd = $new_password;
                } else {
                    $this->error('两次密码不一致');
                }
            }

            if (false !== $this->admin->save()) {
                $this->success('更新成功');
            } else {
                $this->error('更新失败');
            }
        } else {
            $this->error('密码错误');
        }
    }

    public function upload()
    {
        $files = request()->file();
        $res = array();
        foreach($files as $key => $file){
            if($file){
                $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                if($info){
                    $res[$key]['url'] = '/uploads/' . $info->getSaveName();
                }
            }
        }
        return $res;
    }

}
