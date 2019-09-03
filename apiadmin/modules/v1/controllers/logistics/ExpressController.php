<?php
namespace apiadmin\modules\v1\controllers\logistics; 
use apiadmin\modules\v1\controllers\CoreController;
use \Yii;
use apiadmin\modules\models\logistics\Express;

/**
* 物流相关控制器
*/

class ExpressController extends CoreController
{

	/*
		物流公司列表
	*/
	public function actionExpress_list()
	{
		$data = Express::getExpress(['e_state'=>'1']);
		$this->out('物流公司',$data);
	}

	//设置选择的物流公司
	public function actionExpress_set()
	{
		$chose = $this->request('chose');
		$chose = json_decode($chose,1);

		try{
			$flag = Express::updateAll(['e_chose'=>'0'],[]);
				
			$flag2 = Express::updateAll(['e_chose'=>'1'],['in','id',$chose]);

			$this->out('设置成功');

		}catch(\Exception $e)
		{
			$this->error($e->getMessage());
		}
	}

	//获取选择的物流公司
	public function actionExpress_chose()
	{
		$data = Express::getExpress(['e_state'=>'1','e_chose'=>'1']);
		$this->out('物流公司',$data);		
	}
}