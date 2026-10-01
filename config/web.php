<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'name' => 'sisoeva_perfume',
    'language' => 'ru-RU',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'ваш_секретный_ключ_здесь', // ← ДОБАВЬТЕ ЭТО!
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [ 
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'enableSession' => false, 
            'loginUrl' => null, 
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                if ($response->data !== null && $response->statusCode == 401) {
                    $response->data = [
                        'error' => [
                            'code' => 401,
                            'message' => 'Unauthorized',
                            'errors' => ['phone' => 'Неверный номер телефона или пароль']
                        ]
                    ];
                }
                // Добавляем CORS заголовки
                $response->headers->set('Access-Control-Allow-Origin', '*');
                $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
                $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            },
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
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
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [

                'POST register' => 'user/create',
                'POST login' => 'user/login',

                'GET fragrances/<id:\d+>' => 'fragrances/view',
                'GET fragrances' => 'fragrances/search',


                'POST orders'=> 'orders/create',
                'GET orders' => 'orders/index',
            'GET orders/<id:\d+>' => 'orders/view',
            'PUT orders/<id:\d+>/status' => 'orders/update-status',


            'GET posts' => 'post/index',
            'POST posts' => 'post/create',
            'GET posts/<id:\d+>' => 'post/view',
            'DELETE aposts/<id:\d+>' => 'post/delete',
                
                ['class' => 'yii\rest\UrlRule', 'controller' => 'orders'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'post'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'user', 'except' => ['create', 'login']],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'fragrances', 'except' => ['search']],
                
              
                'OPTIONS <action:[\w\-]+>' => 'site/options',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ]
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '*'],
        'panels' => [
            'user' => [
                'class' => 'yii\debug\panels\UserPanel',
                'ruleUserSwitch' => [
                    'allowedIPs' => ['127.0.0.1', '::1']
                ]
            ]
        ]
    ];
    
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['*', '::1'],
    ];
}

return $config;