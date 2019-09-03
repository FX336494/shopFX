<?php
/*
	*短信相关模型
*/

namespace common\models;
use Yii;
// use yii\base\Model;
use common\models\DB;
use yii\db\Query;

class SmsModel extends \yii\db\ActiveRecord
{

	/**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%member_sms}}';
    }	

	//添加短信日志
	public static function addSmsLog($mobile,$code,$msg,$res,$type,$memberId=0)
	{
		$data = array(
			'mobile'	=> $mobile,
			'captcha'	=> $code,
			'ip'		=> Yii::$app->request->userIP,
			'msg'		=> $msg,
			'type'		=> $type,
			'create_time' => date('Y-m-d H:i:s'),
			'member_id' => $memberId,
			'res'		=> json_encode($res,JSON_UNESCAPED_UNICODE)
		);
		return DB::insert('sms_log',$data);
	}

	//将验证码存入表中用于验证
	public static function updateMemberSms($mobile,$code,$memberId=0)
	{
		$query = new Query;
		$where = [];
		if(!$memberId) 
			$where['mobile'] = $mobile;		
		else
			$where['member_id'] = $memberId;

		$res  = $query->from('member_sms')->where(['member_id'=>$memberId])->one();

		$data = array(
			'auth_code'	=> $code, 
			'send_time' => time(),
			'send_times' => 1,
			'expire_time' => time()+(30*60),  //过期时间
		);

		if($res)
		{
			$data['send_times'] += $res['send_times'];
			return DB::update('member_sms',$data,$where);
		}else
		{
			$data['mobile'] = $mobile;
			$data['member_id'] = $memberId;
			return DB::insert('member_sms',$data);
		}
	}   


	/*
		短信验证
		* member  电话 或 是 member_id
		* code    输入的验证码
		* type     类型  1 注册
	*/
	public static function validataVcode($member,$code,$type)
	{
		$where = [];
		if($type=='1') 
			$where['mobile'] = $member; 
		else
			$where['member_id'] = $member;

		$res = self::find()->where($where)->one(); 
		if(!$res) return false;

		if($res['auth_code']==$code && $res['expire_time']>=time())
			return true;

		return false;

	}

}