<?php
namespace api\modules\v1\controllers\member;
use api\modules\v1\controllers\CoreController;
use Yii;
use api\modules\models\member\Member;
use api\modules\models\order\Orders;
/*
	会员相关控制器
*/


class MemberController extends CoreController
{
	/*
		会员首页 信息
	*/
	public function actionInfo() 
	{
		unset($this->_member['loginpwd']);
		unset($this->_member['paypwd']);
		$member = $this->_member;

		//订单信息
		$orderInfo = Orders::getOrdersByGroupState($this->_memberId);
		$orderNums = array();
		foreach($orderInfo as $order){
			$orderNums[$order['order_state']] = $order['total'];
		}

		$res = Orders::getOrderCount($this->_memberId,'4');
		$orderNums['4'] = $res['total']?$res['total']:'';

		$this->out('用户信息',$member,array('order'=>$orderNums));
	}

	//修改用户信息
	public function actionEdit_info()
	{
		$data = array();
		if($this->request('member_avatar'))
			$data['member_avatar'] = $this->request('member_avatar');
		if($this->request('member_name'))
			$data['member_name'] = $this->request('member_name');

		$flag = Member::updateAll($data,['member_id'=>$this->_memberId]);
		if($flag) $this->out('修改成功');
		$this->error('修改失败');
	}

	
}