<?php
namespace app\api\controller;
use think\Controller;
use think\Loader;
use PHPExcel_IOFactory;
use PHPExcel;
use think\Db;

class Excel extends Controller {
	
	//简单的保存Excel文件demo
	public function index(){
		$path = dirname(__FILE__); //找到当前脚本所在路径
		$PHPExcel = new PHPExcel();
		$PHPSheet = $PHPExcel->getActiveSheet(); //获得当前活动sheet的操作对象
		$PHPSheet->setTitle('demo'); //给当前活动sheet设置名称
		$PHPSheet->setCellValue('A1','姓名')->setCellValue('B1','分数');//给当前活动sheet填充数据，数据填充是按顺序一行一行填充的，假如想给A1留空，可以直接setCellValue('A1','');
		$PHPSheet->setCellValue('A2','张三')->setCellValue('B2','50');
		$PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel,'Excel2007');//按照指定格式生成Excel文件，'Excel2007'表示生成2007版本的xlsx，
		$PHPWriter->save($path.'/demo.xlsx'); //表示在$path路径下面生成demo.xlsx文件
	}

	//导出Excel，浏览器本地保存
	public function test(){
		$path = dirname(__FILE__); //找到当前脚本所在路径
		$PHPExcel = new PHPExcel(); //实例化PHPExcel类，类似于在桌面上新建一个Excel表格
		$PHPSheet = $PHPExcel->getActiveSheet(); //获得当前活动sheet的操作对象
		$PHPSheet->setTitle('demo'); //给当前活动sheet设置名称
		$PHPSheet->setCellValue('A1','姓名')->setCellValue('B1','分数');//给当前活动sheet填充数据，数据填充是按顺序一行一行填充的，假如想给A1留空，可以直接setCellValue('A1','');
		$PHPSheet->setCellValue('A2','张三')->setCellValue('B2','50');
		$PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel,'Excel2007');//按照指定格式生成Excel文件，'Excel2007'表示生成2007版本的xlsx，'Excel5'表示生成2003版本Excel文件
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//告诉浏览器输出07Excel文件
		//header('Content-Type:application/vnd.ms-excel');//告诉浏览器将要输出Excel03版本文件
		header('Content-Disposition: attachment;filename="01simple.xlsx"');//告诉浏览器输出浏览器名称
		header('Cache-Control: max-age=0');//禁止缓存
		$PHPWriter->save("php://output");
	}

	public function test2(){
		set_time_limit(0);
		$excelFile = "F:\\tp5demo\\1.xlsx";
		// $PHPExcel = $reader->load($excelfilename); // 文档名称
		// // $PHPExcel = new PHPExcel();
		// // $PHPExcel = $PHPExcel->load($file);
		// $PHPSheet = $PHPExcel->getActiveSheet();
		// dump($PHPSheet);
		$excelReader = \PHPExcel_IOFactory::createReader("Excel2007");
	    $excelReader->setReadDataOnly(true);
	 	$startRow = 2;
	 	$endRow = 3926;
	    //如果有指定行数，则设置过滤器
	    if ($startRow && $endRow) {
	        $perf           = new \PHPExcelReadFilter();
	        $perf->startRow = $startRow;
	        $perf->endRow   = $endRow;
	        $excelReader->setReadFilter($perf);
	    }
	 
	    $phpexcel    = $excelReader->load($excelFile);
	    $activeSheet = $phpexcel->getActiveSheet();
	    if (!$endRow) {
	        $endRow = $activeSheet->getHighestRow(); //总行数
	    }
	 	$db = Db::connect('mysql://root:Fytest_10@192.168.80.10:3306/wapp_answer'); 
	 	$row = $db->table('s_book_tmp')->find();
	    $highestColumn      = $activeSheet->getHighestColumn(); //最后列数所对应的字母，例如第2行就是B
	    $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn); //总列数
	    $data = array();
	    $sql = "INSERT INTO s_book_tmp(id,bookId,bookName,ibsn,cover_img,bookVersion,bookGrade,bookSubject,bookPress,answerImgs,answer_imgs) value(?,?,?,?,?,?,?,?,?,?,?)";
	    
	    for ($row = $startRow; $row <= $endRow; $row++) {
	    	$params = [];
	        for ($col = 0; $col < $highestColumnIndex; $col++) {
	            $params[] = (string) $activeSheet->getCellByColumnAndRow( $col,$row)->getValue();
	        }
	        $db->execute($sql,$params);
	    }
	    dump($data);
	}
}