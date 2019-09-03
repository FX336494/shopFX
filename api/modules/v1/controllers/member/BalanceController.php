<?php
namespace api\modules\v1\controllers\member;
use api\modules\v1\controllers\CoreController;
use Yii;
use api\modules\models\member\Member;
use api\modules\models\finance\BalanceLog;
use api\modules\models\finance\Balance;

/*
	账本(积分) 相关控制器
*/

class BalanceController extends CoreController
{

	/*
		获取用户余额
		btype  账本类型
	*/
	public function actionGet_balance()
	{
		//账本类型
		$btype = $this->request('btype',1);
		$data = Balance::getBalance($this->_memberId,$btype);
		$this->out('账本信息',$data);
	}



	/*
		积分明细
		page 当前页
		page_size 每页数量
	*/
	public function actionBalance_log()
	{
		//账本类型
		$btype = $this->request('btype',1);

		$whereArr = array();
		$where = ['member_id'=>$this->_memberId,'balance_type'=>$btype];
		$whereArr['where'] = $where;

		$params = array(
			'field'	=> ['*'],
			'page'  => $this->request('page',1),
			'limit' => $this->request('page_size',10),
			'order' => 'id desc'
		);
		$data = BalanceLog::balanceLogList($whereArr,$params,array('member'));
		$pages = BalanceLog::$pages;
		$this->out('积分明细',$data,array('total_num'=>$pages));
	}
}