<?php
namespace api\modules\v1\controllers\member; 
use api\modules\v1\controllers\CoreController;
use api\modules\models\member\Member;
use common\models\SmsModel;
use Yii;

/*
	* 会员登录 注册 相关控制器
*/

class ConnectController extends CoreController
{

	public function actionTest()
	{
		echo  Yii::$app->security->generateRandomString();
	}


	/*
		*会员手机号码注册
	*/
	public function actionReg()
	{
		$model = $this->model('member\Member',$this->request,'Reg');
		$model->loginpwd = md5($model->loginpwd);
		$model->paypwd = md5($model->paypwd);  

		//条件验证
		$this->regCondition();

		$transaction = Yii::$app->db->beginTransaction();
		try{
			if(!$model->save())
				throw new \Exception($this->model_errors($model->errors));//抛出异常
			
			$memberId = Yii::$app->db->getLastInsertID();

			if(!$this->regAfter($model,$memberId))
				throw new \Exception('后续操作失败');//抛出异常  

			$transaction->commit(); 
			$this->out('注册成功');    

		} catch(\Exception $e){
			$transaction->rollBack();
			$this->error($e->getMessage());
		}
	}

	//注册条件判断
	private function regCondition()
	{
		//短信验证
		// $mobile = $this->request['member_mobile']; 
		// $code   = $this->request['captcha'];
		// if(!SmsModel::validataVcode($mobile,$code,1))
		// 	$this->error('验证码错误或是已经过期');

		return true;
	}


	//注册后续操作
	private function regAfter($params,$memberId)
	{
		return true;
	}


	//用户手机号登录
	public function actionLogin()
	{

		$mobile = $this->request['mobile'];
		$member = Member::find()->where(['member_mobile'=>$mobile])->one();
		if(!$member) $this->error('用户不存在');
		if(!$member['state']) $this->error('你已经被冻结，暂不能登录');
		if($member['loginpwd']!=md5($this->request['password'])) $this->error('密码错误');

		$member->auth_key = Member::generateAuthKey();
		$flag = $member->save(false);

		if(!$flag) $this->error('更新失败');
		$this->out('登录成功',$member->toarray());

	}

	/*
		退出登录
	*/
	public function actionLogout() 
	{
		$authKey = $this->request['auth_key'];
		$member = Member::findOne(['auth_key'=>$authKey]);

		$member->auth_key = Member::generateAuthKey();
		$flag = $member->save(false);
		$this->out($flag);
	}

	



}