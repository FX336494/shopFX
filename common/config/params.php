<?php
return [
    'adminEmail' => '2252599083@qq.com',
    'user.passwordResetTokenExpire' => 3600,
    //七牛云图片上传配置
    'qiniu' => array(
        'open'  => true,  //是否上传到七牛
    	'ak'	=> 'kHQw4KEPVbc9kgchV364B3J-B2ZPfCBdMI-xdMos',   //填写你自己申请的七牛的配置 用的话记得自己去申请 不要用我的
    	'sk'    => 'aFz6hJOblcSf8mi_VUFv5Jzajb4GM41_VlkqEbDM',
    	'bucket'=> 'fx-img',
    	'zone'  => 'south_china',
    	'domain'=> 'images.tinakj.com',   //这个是你自己指向的域名   
    ),
    'orderState' => ['已取消','待付款','待发货','待收货','待评价'],
    'orderType'  => array('1'=>'普通订单','2'=>'拼团订单','3'=>'秒杀订单'),
    'payType'   => array('balance_pay'=>'余额支付','weixin'=>'微信支付'),   
    'sourceTypeText' => array(
        '1' => '购买产品','2'=>'后台操作','3'=>'拼团退款'
    ),
    'promotionType' => array('1'=>'普通产品','2'=>'拼团产品','3'=>'秒杀产品'),
    'pintuanState'=>array('0'=>'拼团未付款','1'=>'拼团中','2'=>'拼团完成','3'=>'拼团失败','4'=>'拼团失败已退款'),

];