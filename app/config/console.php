<?php

use yii\db\Connection;
use yii\helpers\ArrayHelper;

return [
    'id' => 'test',
    'basePath' => __DIR__ . '/../src/',
    'bootstrap' => ['log'],
    'components' => [
        'db' => [
            'charset' => (string) env('DB_CHARSET'),
            'class' => Connection::class,
            'dsn' => env('DB_SCHEMA') . ':host=' . env('DB_HOST') . ';dbname=' . env('DB_NAME'),
            'enableSchemaCache' => !env('YII_DEBUG'),
            'password' => (string) env('DB_PASSWORD'),
            'username' => (string) env('DB_USER'),
        ],
    ],
    'container' => ArrayHelper::merge(
        require __DIR__ . '/di_container_common.php',
        require __DIR__ . '/di_container_service.php',
    ),
    'controllerNamespace' => 'app\\console',
    'runtimePath' => __DIR__ . '/../runtime/',
];
