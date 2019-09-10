<?php
/*
	* 快递鸟物流
*/
namespace common\utils;

class Kdniao
{
    //填写你自己申请的快递鸟的配置
	private $EBusinessID = '000000';
	private $AppKey = 'xxxxxxxxxxxxxx';
	private $ReqURL = 'http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx';


	//nums  物流单号 
	//code  快递公司编号 如 SF 
	public function deliver_search($nums,$code)
	{
        $requestData = array(
            'ShipperCode'   => $code,  
            'LogisticCode'  => $nums,
            'PayType'	=> '1',
            'ExpType'	=> '1',
            'IsNotice'	=> '0'
        );
        $params = json_encode($requestData);

        $datas = array(
            'EBusinessID' => $this->EBusinessID,
            'RequestType' => '1002',
            'RequestData' => urlencode($params) ,
            'DataType' => '2',
        );
        $datas['DataSign'] = $this->encrypt($params, $this->AppKey);
        $result = $this->sendPost($this->ReqURL, $datas);
        return json_decode($result,1);
	}


   /**
     *  post提交数据 
     * @param  string $url 请求Url
     * @param  array $datas 提交的数据 
     * @return url响应返回的html
     */
    private function sendPost($url, $datas) {
        $temps = array();   
        foreach ($datas as $key => $value) {
            $temps[] = sprintf('%s=%s', $key, $value);      
        }   
        $post_data = implode('&', $temps);
        $url_info = parse_url($url);
        if(empty($url_info['port']))
        {
            $url_info['port']=80;   
        }
        $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
        $httpheader.= "Host:" . $url_info['host'] . "\r\n";
        $httpheader.= "Content-Type:application/x-www-form-urlencoded\r\n";
        $httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";
        $httpheader.= "Connection:close\r\n\r\n";
        $httpheader.= $post_data;
        $fd = fsockopen($url_info['host'], $url_info['port']);
        fwrite($fd, $httpheader);
        $gets = "";
        $headerFlag = true;
        while (!feof($fd)) {
            if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
                break;
            }
        }
        while (!feof($fd)) {
            $gets.= fread($fd, 128);
        }
        fclose($fd);  
        
        return $gets;
    }

    /**
     * 电商Sign签名生成
     * @param data 内容   
     * @param appkey Appkey
     * @return DataSign签名
     */
    private function encrypt($data, $appkey) 
    {
        return urlencode(base64_encode(md5($data.$appkey)));
    }




}