<?php
/*
	帐本操作
*/
namespace common\models\finance;
use Yii;
use yii\db\Query;
use common\models\DB;

class Balance extends \yii\db\ActiveRecord
{
	/**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%balance}}';
    }


    /*
		获取帐本余额
    */
	public static function getBalance($memberId,$bType)
	{

		$data = (new Query)
					->select("a.*,b.type_name")
					->from('account_balance_type as a')
					->leftJoin('account_balance as b','a.type=b.balance_type')
					->where(['a.member_id'=>$memberId,'b.type'=>$bType])
					->one();
		return $data;		
	}

    /*
		* 增加账本余额
		* mid  		会员id
		* btype 	账本类型
		* totalFee 	金额
		* sType    	操作类型  表account_balance_log的source_type定义
		* sourceId 	与类型对应，对应的日志或订单id
		* from     	分红的来源 （比如是来自某个人）
		* reback   	是否为驳回增加，默认为否		
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
        );

        //驳回
        if($reback){
            $data['total_balance'] = $curBalance['total_balance'];
            $data['use_balance']   = $curBalance['use_balance'] - $totalFee;
            $data['balance']       = $curBalance['balance'] + $totalFee;
        }

        $where = array("member_id"=>$mid,'balance_type'=>$btype);
        $flag  = DB::update('account_balance',$data,$where);

        if(!$flag)
            return array("state"=>false,"msg"=>'增加帐本失败');

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
        * sType    操作类型  表account_balance_log的source_type定义
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
        );

        $where = array("member_id"=>$mid,'balance_type'=>$btype);
        $flag  = DB::update('account_balance',$data,$where);

        if(!$flag)
            return array("state"=>false,"msg"=>'减少帐本失败');

        $note = "【".$curBalance['type_name']."】"."减少-".round($totalFee,4);
        $flag  = self::addBalanceLog($curBalance,$totalFee,$sType,0,$sourceId,$from,$note);
        return array("state"=>true,"msg"=>'减少帐本成功');
    }



    /*
        创建一个帐本无账目数据
    */
    private static function createNewBalance($mid,$btype)
    {
        //有没有创建该帐本类型
        $typeInfo = (new Query)->from('account_balance_type')->where(["type"=>$btype])->one();
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

        $flag = DB::insert('account_balance',$data);

        if(!$flag) return array('state'=>0,'msg'=>'创建帐本失败');
        $data['id'] = $flag;
        $data['type_name'] = $typeInfo['type_name'];
        return array('state'=>1,'msg'=>'创建帐本成功！','cur_balance'=>$data);
    }

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
        return DB::insert('account_balance_log',$data);        
    }    



}