<?php

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => false,
            'showScriptName' => false,
            'rules' => [
                '<controller:\w+>s' => '<controller>/index',
            ],
        ],
        'wechat' => [
            'class' => 'maxwen\easywechat\Wechat',
            // 'userOptions' => []  # user identity class params
            // 'sessionParam' => '' # wechat user info will be stored in session under this key
            // 'returnUrlParam' => '' # returnUrl param stored in session
        ],
        'alidayu' => [
            'class' => 'chocoboxxf\Alidayu\Alidayu',
            'appKey' => '23538420', // 淘宝开发平台App Key
            'appSecret' => '1045553417ff3b614e531ff1953cf346', // 淘宝开发平台App Secret
            'partnerKey' => '', // 阿里大鱼提供的第三方合作伙伴使用的KEY
            'env' => 'production', // 使用环境，分sandbox（测试环境）, production（生产环境）
        ],
    ],
    'modules' => [
        'game' => [
            'class' => 'frontend\modules\game\Module',
        ],
        'vcode' => [
            'class' => 'frontend\modules\vcode\Module',
        ],
    ],
];

if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;
