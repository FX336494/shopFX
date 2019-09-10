<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/params.php')
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'defaultRoute'=>'v1/',
    'language' => 'zh-CN',
    'controllerNamespace' => 'api\controllers',
    'modules' => [
        'v1' => [
            'class' => 'api\modules\v1\Module',
        ],
        'doc' => [
            'class' => 'api\modules\doc\module',
            'modules' => [ // 需要生成文档的模块命名空间
                'api\modules\v1\module'
            ]
        ]        
    ],      
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '7EmI1aODEGmCo7LwyKBilJ3WKe45oMsv',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true, // 美化url==ture
            'rules' => [
            ]
        ],   
    ],
    'params' => $params,
];
