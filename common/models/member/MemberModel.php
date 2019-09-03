<?php
namespace common\models\member;
use Yii;
use common\models\BaseModel;

/**
	* 会员member共用模型
 */

class MemberModel extends BaseModel
{
    public static function tableName()
    {
        return '{{%member}}';
    }

    public function rules()
    {
        return [
            [['member_name','loginpwd','paypwd','member_mobile'],'required','on'=>'Reg'],
            [['invite_id', 'create_time', 'update_time','grade'], 'integer'],
            [['member_name'], 'string', 'max' => 64],
            [['member_mobile'], 'string', 'max' => 13],
            [['loginpwd', 'paypwd', 'auth_key'], 'string', 'max' => 32],
            [['openid', 'member_avatar'], 'string', 'max' => 128],
            [['state'], 'string', 'max' => 1],
            [['member_mobile'],'unique','on'=>'Reg'],
            [['member_mobile'],'unique','on'=>'Edit'],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'member_id' => '会员ID',
            'member_name' => '会员姓名',  //微信开放平台ID unionid
            'member_mobile' => '会员电话',  //微信openID 唯一标识
            'loginpwd' => '登录密码',
            'paypwd' => '支付密码',   // 默认微信昵称
            'auth_key' => '会员令牌',
            'invite_id' => '推荐人',
            'openid' => '微信openid',
            'member_avatar' => '用户头像',
            'grade' => '用户等级',  
            'state' => '用户状态',  //  1正常 0冻结
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }    

    /*
        通过id获取用户
    */
    public static function getMemberById($memberId,$field=['*'])
    {
        if(!$memberId) return false;
        return self::find()->select($field)->where(['member_id' => $memberId])->asarray()->one();
        return self::findOne(['member_id' => $memberId]);
    }

    /*
        通过id 或是 电话等信息查询用户
    */
    public static function queryMember($memberKey,$field)
    {
        if(!$memberKey) return false;
        $where = ['or','member_id='.$memberKey,'member_mobile="'.$memberKey.'"'];
        return self::find()->select($field)->where($where)->asarray()->one();
    }


    //生成auth_key
    public static function generateAuthKey()
    {
        return Yii::$app->security->generateRandomString();

    }    

    
}
