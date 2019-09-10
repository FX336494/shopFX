<?php

/*
	*买家收货地址相关模型
*/
namespace api\modules\models\order;
use Yii;

class Address extends \yii\db\ActiveRecord
{

	public static function tableName()
	{
		return "{{%address%}}";
	}


	public function rules()
	{
		return [
			[['true_name','area_id','city_id','province_id','area_info','address','mob_phone'],'required','on'=>['Reg','Edit']],
			//[['mob_phone'],'match','pattern','/^((1)[0-9]{1}\d{9})$/','message'=>'手机号码格式错误']
		];
	}


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'address_id' => '主键ID',
            'true_name' => '真实姓名',  //微信开放平台ID unionid
            'mob_phone' => '电话号码',  //微信openID 唯一标识
            'province_id' => '省ID',
            'city_id' => '市ID',   // 默认微信昵称
            'area_id' => '区id',
            'area_info' => '地区',
            'address' => '详细信息',
            'is_default' => '是否默认',
            'tel_phone' => '座机电话', 
        ];
    } 


	public static function  updateAddress($data,$where)
	{
		return self::updateAll($data,$where);
	}



	public static function addressList($memberId)
	{
		$where = array('member_id'=>$memberId);
		return self::find()->where($where)->orderBy('is_default DESC')->asarray()->all();
	}

	public static function getAddress($where)
	{
		return self::find()->where($where)->orderBy('is_default DESC')->asarray()->one();
	}


	/*
		地区相关
		根据所传参数获取对应地区列表 
	*/
	public static function areaList($parentId,$deep='1')
	{
		$query = new yii\db\Query();
		$data  = $query->select("area_name,area_id")->from('area')->where(['parent_id'=>$parentId,'area_deep'=>$deep])->all();
		return $data;
	}



}