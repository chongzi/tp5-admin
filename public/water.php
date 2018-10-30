<?php
    $save_file_name = $_POST['save_file_name'];
    $username = $_POST['username'];
    $score = $_POST['score'];
    $text = json_decode($_POST['text'],true);
    if(!empty($text)){
        if($save_file_name){
            $filename = $_POST['file_name'] ? $_POST['file_name'] : 'images/test.png';
            imageText2($filename,$save_file_name,$text,$username,$score);
            echo json_encode(['state'=>'success','imgUrl'=>$save_file_name]);
        }else{
            echo json_encode(['msg'=>'无图片名']);
        }
    }else{
        echo json_encode(['msg'=>'无文字']);
    }
    
    //文字水印
    function imageText2($src,$filename = null,$text,$username,$score){
        if(!file_exists($src)){
            echo '图片不存在！';exit;
        }
        $info = getimagesize($src);
        //获取图片扩展名
        $type = image_type_to_extension($info[2], false);
        //动态的把图片导入内存中
        $fun = "imagecreatefrom{$type}";
        $image = $fun($src);
        //指定字体颜色
        $col = imagecolorallocatealpha($image, 255, 255, 255, 0);
        $col1 = imagecolorallocatealpha($image, 0, 0, 0, 0);
        $col2 = imagecolorallocatealpha($image, 229, 0, 0, 0);
        //给图片添加文字
        $fontfile = $_SERVER['DOCUMENT_ROOT'].'/fonts/MSYH.TTF';
        $fontfilescore = $_SERVER['DOCUMENT_ROOT'].'/fonts/STXINGKA_0.TTF';
         // $fontfile = "C:\WINDOWS\Fonts\SIMHEI.TTF";
        // $str = '是粉色';
        foreach ($text as $key => $value) {
            ImageTTFText($image, 18, 0, $value['x'], $value['y'] , $col,$fontfile, $value['name']);
            ImageTTFText($image, 18, 0, $value['x1'], $value['y1'], $col1,$fontfile, $value['content']);
            ImageTTFText($image, 18, 0, $value['x1'], $value['y1'] - 30, $col1,$fontfile, $value['score'].'分');
        }
        ImageTTFText($image, 25, 0, 122,165 , $col1,$fontfile, $username);
        ImageTTFText($image, 50, 0, 450,175 , $col2,$fontfilescore, $score);
        //指定输入类型
        header('Content-type:' . $info['mime']);
        //动态的输出图片到浏览器中
        $func = "image{$type}";
        if($filename != null){
         $func($image,$filename);
        }else{
         $func($image);
        }
    }