<?php
namespace api\modules\models\promotion;
use Yii;
use common\models\promotion\PintuanOpenOrderModel;

/*
	拼团开团订单模型
*/
class PintuanOpenOrder extends PintuanOpenOrderModel
{
		
	/*
		获取开团列表
	*/
	public static function pintuanOpenOrderList($goodsCommonid)
	{
		$field = ['a.*','b.member_name','b.member_avatar'];
		$where = [];
		$where['a.goods_commonid'] = $goodsCommonid;
		$where['a.pay_state'] = '1';
		$where['a.state'] = '1';
		$and1 = ['<','a.end_time',time()];
		$data = self::find()->select($field)->alias('a')
				->leftJoin('member as b','a.open_member_id=b.member_id')
				->where($where)
				->asarray()->all();
		if(!$data) return [];
		foreach($data as &$val)
		{
			$val['countdown'] = $val['end_time'] - time();
		}
		return $data;
	}

}
