<?php
namespace api\modules\models\promotion;
use Yii;
use common\models\promotion\CouponsModel;
use api\modules\models\promotion\CouponsInstance;

/*
	优惠券实例模型
*/
class Coupons extends CouponsModel
{

	/*
		获取用户下单时可用的优惠券
	*/
	public static function availableCoupons($goodsInfo,$mid)
	{
		$curT = time();
		$where = ['a.member_id'=>$mid,'a.state'=>'1'];
		$and = ['>','a.end_time',$curT];
		$coupons = self::find()->alias('a')
					->select(['a.*','b.*'])
					->leftJoin('coupons_instance as b','a.coupons_instance_id=b.instance_id')
					->where($where)
					->andWhere($and)
					->asarray()->all();
		if(!$coupons) return [];

		$totalFee = 0;
		$classIdArr = $goodsIdArr = [];
		foreach($goodsInfo as $val)
		{
			$totalFee += $val['goods_price']*$val['goods_num'];
			$classIdArr[] = $val['gc_id1'];
			$classIdArr[] = $val['gc_id2'];
			$classIdArr[] = $val['gc_id3'];
			$goodsIdArr[] = $val['goods_commonid'];
		}


		$list = array();
		foreach($coupons as $val)
		{
			if($totalFee<$val['consume_amount']) continue;

			//通用券
			if($val['use_type']=='1')
			{
				//获取优惠金额
				$val['coupons_money'] = self::getCouponMoney($goodsInfo,$val,$totalFee);
				$list[] = $val;
				continue;
			}

			//分类券
			if($val['use_type']=='2' && in_array($val['type_id'],$classIdArr))
			{
				//获取优惠金额
				$val['coupons_money'] = self::getCouponMoney($goodsInfo,$val,$totalFee);
				$list[] = $val;
				continue;
			}

			//单品通用
			if($val['use_type']=='3' && in_array($val['type_id'],$goodsIdArr))
			{
				//获取优惠金额
				$val['coupons_money'] = self::getCouponMoney($goodsInfo,$val,$totalFee);
				$list[] = $val;
				continue;
			}
		}
		return $list;
	}

	//获取每张优惠券可优惠金额
	private static function getCouponMoney($goodsInfo,$val,$totalFee)
	{
		//满减券直接返回面额
		if($val['type']=='1') return $val['coupons_money'];

		//通用券
		if($val['use_type']=='1') return round($totalFee*(10-$val['discount'])/10,2);

		//分类下使用
		if($val['use_type']=='2')
		{
			$total = 0;
			foreach($goodsInfo as $goods)
			{
				$classIdArr = array($goods['gc_id1'],$goods['gc_id2'],$goods['gc_id3']);
				if(in_array($val['type_id'],$classIdArr))
					$total += $goods['goods_price']*$goods['goods_num'];
			}
			return round($total*(10-$val['discount'])/10,2);
		}

		//单品下使用
		if($val['use_type']=='3')
		{
			$total = 0;
			foreach($goodsInfo as $goods)
			{
				if($val['type_id']==$goods['goods_commonid'])
					$total += $goods['goods_price']*$goods['goods_num'];
			}
			return round($total*(10-$val['discount'])/10,2);
		}
		return 0;
	}

	/*
		获取优惠券
	*/
	public static function getCoupon($couponId)
	{
		$coupon = self::find()->alias('a')
				->select(['a.*','b.*'])
				->leftJoin('coupons_instance as b','a.coupons_instance_id=b.instance_id')
				->where(['a.id'=>$couponId])
				->asarray()->one();
		if(!$coupon) return [];
		return $coupon;
	}

	/*
		计算订单可使用金额
		* goods_amount 
		* goods_info
	*/
	public static function calculateCouponMoney($couponId,$listData)
	{
		$data = [];
		$coupon = self::getCoupon($couponId);
		if(!$coupon) return $data;

		$couponMoney = self::getCouponMoney($listData['goods'],$coupon,$listData['goods_amount']);

		$data['coupon_id'] = $couponId;
		$data['instance_id'] = $coupon['instance_id'];
		$data['coupon_money'] = $couponMoney;
		return $data;
	}


	/*
		我的优惠券
	*/
	public static function myConpons($mid)
	{
		$coupons = self::find()->alias('a')
				->select(['a.*','a.end_time as endtime','a.state as coupon_state','b.*'])
				->leftJoin('coupons_instance as b','a.coupons_instance_id=b.instance_id')
				->where(['a.member_id'=>$mid])
				->asarray()->all();	
		foreach($coupons as &$val)
		{
			$val['coupons_desc'] = '满'.$val['consume_amount'].'使用';
			$val['time_desc'] = '';
			if($val['time_type']=='1')
				$val['time_desc'] = date("Y.m.d",$val['start_time']).'-'.date("Y.m.d",$val['end_time']);
			if($val['time_type']=='2')
				$val['time_desc'] = date("Y.m.d",$val['endtime']).'前使用';
			$val['type_text'] = CouponsInstance::$typeText[$val['type']];
			$val['btn_desc'] = Coupons::$couponStateText[$val['coupon_state']];
		}

		return $coupons;
	}


}