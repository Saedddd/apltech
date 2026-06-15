<?php

$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'api-app',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    
    'as cors' => [
        'class' => \yii\filters\Cors::class,
        'cors' => [
            'Origin' => explode(',', getenv('CORS_ORIGINS') ?: 'https://apltech-front-eight.vercel.app'),
            'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
            'Access-Control-Request-Headers' => ['*'],
            'Access-Control-Allow-Credentials' => true,
        ],
    ],

    'components' => [
        'request' => [
            'cookieValidationKey' => getenv('COOKIE_VALIDATION_KEY') ?: 'super-secret-key',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableSession' => false,
            'loginUrl' => null,
        ],
        'response' => [
            'class' => 'yii\web\Response',
            'format' => \yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false, 
            'rules' => [
                // OPTIONS
                'OPTIONS api/<any:.+>' => 'api/options',
                
                // Auth
                'POST api/auth/login' => 'auth/login',
                
                // Products
                'GET api/products' => 'product/index',
                'GET products' => 'product/index',  
                'GET api/product/<id:\d+>' => 'product/view',
                'GET product/<id:\d+>' => 'product/view', 
                'POST api/product/create' => 'product/create',
                'PATCH api/product/update/<id:\d+>' => 'product/update',
                'GET api/product/brand/<name:\w+>' => 'product/brand',
                'DELETE api/product/delete/<id:\d+>' => 'product/delete',
            ],
        ],
        'db' => $db,
    ],
];

return $config;