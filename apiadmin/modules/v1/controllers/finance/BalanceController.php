<?php
namespace apiadmin\modules\v1\controllers\finance;
use \Yii;
use apiadmin\modules\v1\controllers\CoreController;
use apiadmin\modules\models\finance\Balance;
use apiadmin\modules\models\finance\BalanceOperate;
use apiadmin\modules\models\finance\BalanceLog;
/*
	会员账本相关控制器
*/
class BalanceController extends CoreController
{
	/*
		会员账本列表
	*/
	public function actionBalance_list()
	{
		$whereArr = $this->formartWhere();
		$params = array(
			'field'	=> ['*'],
			'order' => 'id desc',
			'page'	=> $this->request('page',10),
			'limit' => $this->request('page_size',10),			
		);
		$data = Balance::balanceList($whereArr,$params,['member','type_name']);
		$pages = Balance::$pages;
		$typeName = Balance::balanceTypeName();
		$this->out('会员账本列表',$data,array('pages'=>$pages,'type_name'=>$typeName));
	}
	/*
		列表 条件组装
	*/
	public function formartWhere()
	{
		$where = [];
		$whereAnd = [];
		$searchKeys = json_decode($this->request('search'),1);
		foreach($searchKeys as $k=>$val)
		{
			if($val=='') continue;
			if($k=='date')
			{
				if(!$val['0'] || !$val['1']) continue;
				$whereAnd[] = ['between', 'create_time', strtotime($val[0]),strtotime($val[1])]; 
			}else
			{
				$where[$k] = $val;
			}
		}
		return array('where'=>$where,'whereAnd'=>$whereAnd);
	}

	/*
		获取系统的账本类型
	*/
	public function actionBalance_type()
	{
		$typeName = Balance::balanceTypeName();
		$this->out('账本类型',$typeName);
	}

	
	/*
		操作账本
	*/
	public function actionBalance_op()
	{
		try
		{
			$transaction = Yii::$app->db->beginTransaction();

			$data = $this->request;
			unset($data['auth_key']);
			if($data['member_id']<=0 || !$data['member_id'])
				throw new \Exception("会员信息错误");
			if($data['nums']<=0 || !$data['nums'])
				throw new \Exception("金额数量不能小于0");
			if($data['balance_type']<=0)
				throw new \Exception("账本类型错误");

			$model = new BalanceOperate;
			if(!$model->load($data,'') || !$model->validate())
				throw new \Exception($this->model_errors($model->errors));

			$model->create_time = time();
			$model->operator = $this->_user['user_name']; 
			if(!$model->save())
				throw new \Exception("操作失败");
			$logId = Yii::$app->db->getLastInsertID();

			if($data['type']=='1')
				$res = Balance::addBalance($data['member_id'],$data['balance_type'],$data['nums'],1,$logId,'后台操作');
			else
				$res = Balance::reduceBalance($data['member_id'],$data['balance_type'],$data['nums'],1,$logId,'后台操作');
			if(!$res['state'])
				throw new \Exception($res['msg']);

			$transaction->commit();
			$this->out('操作成功');
		}catch(\Exception $e)
		{
			$transaction->rollback();
			$errMsg = $e->getMessage();
			$this->error($errMsg);
		}
	}

	/*
		账本操作列表
	*/
	public function actionBalance_op_list()
	{
		$where = [];
		$params = array(
			'field'	=> ['*'],
			'order' => 'id desc',
			'page'	=> $this->request('page',10),
			'limit' => $this->request('page_size',10),			
		);		
		$data = BalanceOperate::opList($where,$params);
		$pages = BalanceOperate::$pages;
		$typeName = Balance::balanceTypeName();
		$this->out('账本操作列表',$data,array('pages'=>$pages,'type_name'=>$typeName));
	}


	/*
		账本日志明细
	*/
	public function actionBalance_log()
	{
		$searchKeys = json_decode($this->request('search'),1);
		$where = [];
		if($searchKeys){
			foreach($searchKeys as $key=>$val){
				if($val=='') continue;
				$where['where'][$key] = $val;
			}
		}
		$params = array(
			'field'	=> ['*'],
			'order' => 'id desc',
			'page'	=> $this->request('page',10),
			'limit' => $this->request('page_size',10),			
		);	
		$list = BalanceLog::balanceLogList($where,$params,array('member'));
		$pages = BalanceLog::$pages;
		$typeName = Balance::balanceTypeName();		
		$this->out('日志列表',$list,array('pages'=>$pages,'type_name'=>$typeName));
	}

}