<?php

$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'api-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'components' => [
        'db' => $db,
    ],
];

return $config;