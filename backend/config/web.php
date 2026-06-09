<?php

$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'api-app',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'container' => [
        'definitions' => [
            'app\repositories\ProductRepositoryInterface' => 'app\repositories\ProductRepository',
            'app\services\ProductService' => 'app\services\ProductService',
        ],
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'super-secret-validation-key',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableSession' => false, 
            'loginUrl' => null,
        ],
        'jwt' => [
            'class' => \sizeg\jwt\Jwt::class,
            'key' => 'secret_key_with_at_least_32_characters_long_12345',
            'jwtValidationData' => [
                'class' => \sizeg\jwt\JwtValidationData::class,
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => [
                'POST api/auth/login' => 'auth/login',
                'GET api/products' => 'product/index',
                'GET api/product/<id:\d+>' => 'product/view',
                'POST api/product/create' => 'product/create',
                'PATCH api/product/update/<id:\d+>' => 'product/update',
                'GET api/product/brand/<name:\w+>' => 'product/brand',
            ],
        ],
        'db' => $db,
    ],
];

return $config;