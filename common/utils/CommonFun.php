<?php
namespace common\utils;
use yii\base\Object;
use Yii;

/*
    公共类
*/


class CommonFun extends Object
{

    //记入日志
    public static function writeLog($content,$tempDir='')
    {
        if(is_array($content) || is_object($content))
            $content = json_encode($content,JSON_UNESCAPED_UNICODE);
        
        $content = "[".date('Y-m-d H:i:s')."]".$content."\r\n";
        $dir = rtrim(str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']),'/').'/logs';
        if($tempDir) $dir .= '/'.$tempDir;
        if(!is_dir($dir)){
            mkdir($dir,0777,true);
        }
        $path = $dir.'/'.date('Y-m-d').'.txt';
        file_put_contents($path,$content,FILE_APPEND); 
    } 

    /**
     * 生成随机编号(两位随机 + 从2000-01-01 00:00:00 到现在的秒数+微秒+会员ID%1000)
     * 长度 =2位 + 10位 + 3位 + 3位  = 18位
     * 1000个会员同一微秒提订单，重复机率为1/100
     * @return string
     */
    public static function makeRandSn($memberId) {
        return mt_rand(10,99)
              . sprintf('%010d',time() - 946656000)
              . sprintf('%03d', (float) microtime() * 1000)
              . sprintf('%03d', (int) $memberId % 1000);
    }

    
}
