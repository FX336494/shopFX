<?php
namespace common\utils;
use yii\base\Object;
use Yii;

/*
	* 导出表格
*/
namespace common\utils;

class OutputExecl
{

    /*
        * 导出execl
        * headerData  表头信息  形如 array('A1'=>'编号','B1'=>'会员ID','C1'=>'电话');
        * data        导入数据   这个要与表头顺序一致
        * 返回一个execl下载地址
    */	

	public function output($headData,$data,$filename='')
	{
		$objPHPExcel = new \PHPExcel();

        //设置表头的信息
        $headObj = $objPHPExcel->setActiveSheetIndex(0);			
        foreach($headData as $key=>$val)
        {
        	$headObj->setCellValue($key,$val);
        }
        $i=2;

        //写入数据
        $contentObj = $objPHPExcel->getActiveSheet();
        $listks    = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        foreach ($data as $key => $value) 
        {
        	$l = 0;
        	foreach($value as $val)
        	{
        		//给表的单元格设置数据
        		$contentObj->setCellValue($listks[$l].$i,$val);
        		$l++;
        	}
            $i++;
        }        

        //设置sheet页标题
        $objPHPExcel->getActiveSheet()->setTitle('表格导出数据');

		$objPHPExcel->setActiveSheetIndex(0);

        if(!$filename)
            $filename = time().rand(1000,9999).'.xls';


        $dir = '/data/execl/';
        $basePath = $_SERVER['DOCUMENT_ROOT'];
        if(!is_dir($basePath.$dir)) mkdir($basePath.$dir,0777,true);

        $dirFile = $dir.$filename;
        //1,直接生成一个文件
        $objWriter =\PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save( $basePath.$dirFile);
        $host = \Yii::$app->request->hostInfo;
  		return $host.$dirFile;       
	}

}