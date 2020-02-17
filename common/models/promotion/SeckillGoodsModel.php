<?php
namespace common\models\promotion;
use Yii;
use common\models\BaseModel;

/*
	拼团实例模型
*/
class SeckillGoodsModel extends BaseModel
{
	public static function tableName()
	{
		return '{{%seckill_goods}}';
	}

	public function rules()
	{
		return [
			[['instance_id','goods_commonid','goods_id','seckill_price'],'required'],			
			[['instance_id','goods_commonid','goods_id'],'integer'],
			[['seckill_price'],'number'],	
		];
	}

	/*
		拼团实例商品列表
	*/
	public static function instanceGoodsList($params)
	{
		$where = ['a.instance_id'=>$params['instance_id']];
		$field = ['a.*','b.goods_name','b.goods_image','b.goods_price'];
		$offset = ($params['page']-1)*$params['limit'];
		$model = self::find()->select($field)->alias('a')
							->leftJoin('goods_common as b','a.goods_commonid=b.goods_commonid')
							->where($where);
		self::$pages = $model->count();
		$data = $model->groupBy('a.goods_commonid')->offset($offset)->limit($params['limit'])->asarray()->all();
		return $data;	
	}

	/*
		获取秒杀的产品
	*/
	public static function getSeckillGoods($goodsId)
	{
		return self::find()->where(['goods_id'=>$goodsId])->asarray()->one();
	}

	//获取一个默认秒杀产品
	public static function getMinSeckillGoods($goodsCommonid)
	{
		$field = ['a.seckill_price','b.start_time','b.trailer_time','b.end_time'];
		$data  =  self::find()->select($field)->alias('a')
				->leftJoin('seckill_instance as b','a.instance_id=b.instance_id')
				->where(['a.goods_commonid'=>$goodsCommonid])
				->orderBy('a.seckill_price asc')
				->asarray()->one();
		if(!$data) return [];

		
		if($data['start_time']>time())
		{
			//即将开始 开始倒计时
			$data['seckill_status'] = '1';  
			$data['countdown'] = $data['start_time'];
			$data['seckill_text'] = '即将开始'; 
			$data['time'] = time();

		}else if($data['start_time']<=time() && time()<$data['end_time'])
		{
			//进行中  结束倒计时
			$data['seckill_status'] = '2';  
			$data['countdown'] = $data['end_time'];
			$data['seckill_text'] = '即将结束'; 
		}else{
			//已结束
			$data['seckill_status'] = '3';
			$data['seckill_text'] = '已结束';   
			$data['countdown'] = 0;
		}
		return $data;
	}

	/*
		通过商品获取所属的秒杀实例
	*/
	public static function getInstanceByGoods($goodsId)
	{
		$field = ["a.goods_id",'b.*'];
		$data = self::find()->select($field)->alias('a')
				->leftJoin('seckill_instance as b','a.instance_id=b.instance_id')
				->where(['a.goods_id'=>$goodsId])
				->asarray()->one();
		return $data;
	}	


}