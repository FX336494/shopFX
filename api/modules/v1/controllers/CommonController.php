<?php

/*
	存放一些公共调用、和不需要登录验证的接口
*/

namespace api\modules\v1\controllers;

use Yii;
use api\modules\models\member\Member;
use common\utils\ChuangnanSms;
use yii\db\Query;
use common\models\DB;
use common\models\SmsModel;
use common\utils\FileUpload;
use crazyfd\qiniu\Qiniu;

class CommonController extends CoreController
{
	//通过id或者电话获取用户
	public function actionGetmember()
	{
		$member = Member::getMemberByIdOrMobile($this->request['member']);
		if($member) $this->out('用户信息',$member);
		$this->error('无此人信息');
	}


	/*
		* 发送短信验证码
		* mobile  手机号
		* type   短信类型  1：验证码
	*/
	public function actionSendsms()
	{
		if(!$mobile = $this->request['mobile'])
			$this->error('手机号不能为空');
		$type = $this->request['type']?$this->request['type']:1;
		$code = rand(100000,999999);
		$msg  = '';
		$memberId = 0;
		if($type=='1')
			$msg = '您好，你的验证码为：'.$code.'，请不要轻易告诉别人！';

		$smsObj = new ChuangnanSms;
		$res    = $smsObj->sendSMS($mobile,$msg);
		$res    = json_decode($res,1);
		//添加日志
		SmsModel::addSmsLog($mobile,$code,$msg,$res,$type,$memberId);	
		if(!$res['code'])
		{
			//发送成功
			SmsModel::updateMemberSms($mobile,$code,$memberId); 
			$this->out('发送成功');
		}else
		{
			$this->error($res['errorMsg']); 
		}
	
	}


	/*
		*同意协议
	*/
	public function actionAgreement() 
	{
		$html = '<p>同意注册，后果自行承担</p>';
		$title = '注册协议';
		$this->out('协议',array('content'=>$html,'title'=>$title));
	}


	//图片上传
	public function actionUpload() 
	{
		$qiNiuOpen 	= Yii::$app->params['qiniu']['open'];
		if($qiNiuOpen)
			$this->qinuiUpload();
		else
			$this->fileUpload();
	}

	//本地上传
	private function fileUpload()
	{
		$basePath = $_SERVER['DOCUMENT_ROOT'];
		$filePath = $this->getFilePath($this->request('uptype'));
		
		$upload = new FileUpload();
		$res = $upload->upload('file',$basePath.'/'.$filePath);
		if($res){
			$fileName = $upload->getFileName();
			$data = array('url'=>'http://'.$_SERVER['HTTP_HOST'].'/'.$filePath.'/'.$fileName);
			$this->out('上传成功',$data);
		}
		$this->error('上传失败');		
	}

	//七牛上传
	private function qinuiUpload()
	{
		$qiNiuConf 	= Yii::$app->params['qiniu'];
		$ak 		= $qiNiuConf['ak'];
		$sk 		= $qiNiuConf['sk'];
		$domain 	= $qiNiuConf['domain'];
		$bucket 	= $qiNiuConf['bucket'];
		$zone 		= $qiNiuConf['zone'];	
		$qiniu 		= new Qiniu($ak, $sk,$domain, $bucket,$zone);
		$key 		= time();
		$key 		.= strtolower(strrchr($_FILES['file']['name'], '.'));
		$filePath 	= $this->getFilePath($this->request('uptype'));
		$key 		= $filePath.$key;

		$res = $qiniu->uploadFile($_FILES['file']['tmp_name'],$key);
		$data = array('url'=>'http://'.$domain.'/'.$key); 
		$this->out('上传结果:'.json_encode($res),$data);
	}

	//图片存放路径
	private function getFilePath($upType)
	{
		switch ($upType) {
			case '1':
				$filePath = 'upload/avatar';	
				break;
			
			default:
				$filePath = 'upload/avatar';	
				break;
		}
		return $filePath;
			
	}




}

