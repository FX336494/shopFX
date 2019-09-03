<?php
return [
    'adminEmail' => '2252599083@qq.com',
    'user.passwordResetTokenExpire' => 3600,
    //七牛云图片上传配置
    'qiniu' => array(
        'open'  => false,  //是否上传到七牛
    	'ak'	=> 'kHQw4KExxxxxxxfCBdMI-xdMos',   //填写你自己申请的七牛的配置
    	'sk'    => 'aFz6hxxxxxxxxxkqEbDM',
    	'bucket'=> 'fx-img',
    	'zone'  => 'south_china',
    	'domain'=> 'images.tinakj.com',   //这个是你自己指向的域名
    ),
    'orderState' => ['已取消','待付款','待发货','待收货','待评价'],
    'orderType'  => array('1'=>'普通订单','2'=>'拼团订单','3'=>'自提订单'),
    'payType'   => array('balance_pay'=>'余额支付','weixin'=>'微信支付'),   
    'sourceTypeText' => array(
        '1' => '购买产品','2'=>'后台操作',
    ),
];
