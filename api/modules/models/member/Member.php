<?php
namespace api\modules\models\member;
use Yii;
use common\models\member\MemberModel;

/**
 * This is the model class for table "member".
 */

class Member extends MemberModel
{
    /*
      * 通过authkey获取用户信息
    */
    public static function loginByAuthkey($authKey)
    {
        return self::findOne(['auth_key' => $authKey]);
    }
 
    /*
        *通过ID或者电话获取用户
    */
    public static function getMemberByIdOrMobile($member)
    {
        if(!$member) return false; 
        $data = self::find()->where(['or','id='.$member,"member_mobile='".$member."'"])->one();      
        if($data) $data = $data->toArray(); 
        return $data;
    } 



    
}
