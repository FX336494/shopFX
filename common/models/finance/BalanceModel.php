<?php
/*
   帐户余额模型
*/
namespace common\models\finance;
use common\models\BaseModel;
use yii\db\Query;
use common\models\finance\BalanceLogModel;

class BalanceModel extends BaseModel
{
	public static function tableName()
	{
		return "{{%balance}}";
	}

	public function rules() 
	{
		return [
			[['member_id','balance_type','balance','total_balance'],'required'],
			[['is_tixian','is_transfer'],'string'],
			[['balance','total_balance','use_balance'],'double'],
			[['member_id','create_time','update_time','end_time'],'integer'],
		];
	}


   	/*
     	获取帐本余额
     	* memberId 会员ID
     	* bType 账本类型
   	*/	
    public static function getBalance($memberId,$bType)
    {
        $data = (new Query)
		            ->select("a.*,b.type_name")
		            ->from('balance as a')
		            ->leftJoin('balance_type as b','a.balance_type=b.type')
		            ->where(['a.member_id'=>$memberId,'a.balance_type'=>$bType])
		            ->one();
        if(!$data){
            $res = self::balanceTypeName(1);
            if(!$res) return $data;
            $data = ['type_name'=>$res['type_name'],'balance_type'=>$res['type'],'balance'=>0];
        }
        return $data;       	
    }


    /*
        * 增加账本余额
        * mid       会员id
        * btype  账本类型
        * totalFee  金额
        * sType     操作类型  表balance_log的source_type定义
        * sourceId  与类型对应，对应的日志或订单id
        * from      分红的来源 （比如是来自某个人）
        * reback    是否为驳回增加，默认为否      
    */  

    public static function addBalance($mid,$btype,$totalFee,$sType='',$sourceId=0,$from='',$reback=false)
    {
    	  if($totalFee<=0) return array("state"=>false,"msg"=>'金额不能小于0');
    	  $curBalance = self::getBalance($mid,$btype);
        if(!$curBalance)
        {
           $res = self::createNewBalance($mid,$btype);
           if(!$res['state']) return array("state"=>false,"msg"=>$res['msg']); 
           $curBalance = $res['cur_balance'];
        } 
        $data = array(
        	  'balance'       => $curBalance['balance'] + $totalFee,
           	'total_balance' => $curBalance['total_balance'] + $totalFee,
            'update_time'   => time(),
        );
       	//驳回
       	if($reback){
           	$data['total_balance'] = $curBalance['total_balance'];
           	$data['use_balance']   = $curBalance['use_balance'] - $totalFee;
           	$data['balance']       = $curBalance['balance'] + $totalFee;
       	}
        $where = array("id"=>$curBalance['id']);
        $flag  = self::updateAll($data,$where); 
        if(!$flag) return array("state"=>false,"msg"=>'增加帐本失败'); 
                        	        
       	$note = "【".$curBalance['type_name']."】"."增加+".round($totalFee,4);
       	$flag  = self::addBalanceLog($curBalance,$totalFee,$sType,1,$sourceId,$from,$note);
       	return array("state"=>true,"msg"=>'增加帐本成功');
    }

    /*
        * 减少余额
        * mid 会员id
        * typeId   会员类型
        * bType    账本类型
        * money    金额
        * sType    操作类型  表balance_log的source_type定义
        * sourceId 与类型对应，对应的日志或订单id
    */

    public static function reduceBalance($mid,$btype,$totalFee,$sType='',$sourceId=0,$from='')
    {
        if($totalFee<=0) return array("state"=>false,"msg"=>'金额不能小于0');

        $curBalance =self::getBalance($mid,$btype); 
        if(!$curBalance)
            return array('state'=>0,'msg'=>'帐本不存在');

        if($curBalance['balance']<$totalFee)
            return array('state'=>0,'msg'=>'余额不足'); 

        $data = array(
            'balance'       => $curBalance['balance'] - $totalFee,
            'use_balance'   => $curBalance['use_balance'] + $totalFee,
            'update_time'   => time(),
        );

        $where = ["id"=>$curBalance['id']];
        $flag  = self::updateAll($data,$where);

        if(!$flag)
            return array("state"=>false,"msg"=>'减少帐本失败');

        $note = "【".$curBalance['type_name']."】"."减少-".round($totalFee,4);
        $flag  = self::addBalanceLog($curBalance,$totalFee,$sType,0,$sourceId,$from,$note);
        return array("state"=>true,"msg"=>'减少帐本成功');
    }


    /*
        创建一个帐本
    */
    private static function createNewBalance($mid,$btype)
    {
        //有没有创建该帐本类型
        $typeInfo = (new Query)->from('balance_type')->where(["type"=>$btype])->one();
        if(!$typeInfo) return array("state"=>0,"msg"=>'没有可用帐本');
        $data = array(
           	'member_id'         => $mid,
           	"balance"           => 0,
           	"total_balance"     => 0,
           	"balance_type"      => $btype,
           	"use_balance"       => 0,
           	"is_tixian"         => $typeInfo['is_tixian'],
           	"is_transfer"       => $typeInfo['is_transfer'],
           	"end_time"          => $typeInfo['end_time'],
           	"update_time"       => time(),
           	"create_time"       => time()
        );
        $model = new self;
        if(!$model->load($data,'') || !$model->save())
        	return self::outError($model->errors);
        $id = \Yii::$app->db->getLastInsertID();
        $data['id'] = $id;
        $data['type_name'] = $typeInfo['type_name'];
        return array('state'=>1,'msg'=>'创建帐本成功！','cur_balance'=>$data);
    }

    /*
		记录日志
    */
    public static function addBalanceLog($curBalance,$totalFee,$sType,$type,$sourceId,$from,$note)
    {
        $data = array(
            'member_id'         => $curBalance['member_id'],
            "balance"           => $totalFee,
            "balance_id"        => $curBalance['id'],
            "balance_type"      => $curBalance['balance_type'],
            "source_type"       => $sType,
            "log_type"          => $type,
            "source_id"         => $sourceId,
            "old_balance"       => $curBalance['balance'],
            "note"              => $note,
            'bonus_from'        => $from,
            "create_time"       => time()
        );
        $model = new BalanceLogModel;
        return ($model->load($data,'')&&$model->save());
    }


    /*
      账本类型
    */
    public static function balanceTypeName($type=0)
    {
        if($type>0){
            return (new Query)->from('balance_type')->where(['type'=>$type])->one();
        }

        $typeInfo = (new Query)->from('balance_type')->where([])->all();
        $data = array();
        foreach($typeInfo as $val)
        {
          $data[$val['type']] = $val['type_name'];
        }      
        return $data;
    }
}
