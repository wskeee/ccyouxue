<?php
return [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '',
        ],
       'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                '<controller:\w+>s' => '<controller>/index',
            ],
        'wechat' => [
            'class' => 'callmez\wechat\sdk\MpWechat',
            'appId' => 'wx24215bea8bd3a41d',
            'appSecret' => '51884380b8bdcb04b7ff451598ef33ff',
            'token' => 'ccyouxue',
            'encodingAesKey' => 'wskeee'
        ],
        ],
    ],
];
