<?php

use yii\caching\FileCache;
use yii\db\Connection;
use yii\helpers\ArrayHelper;
use yii\log\Dispatcher;
use yii\log\FileTarget;
use yii\web\JsonParser;
use yii\web\UrlNormalizer;

return [
    'id' => 'test',
    'basePath' => __DIR__ . '/../src/',
    'bootstrap' => ['log'],
    'components' => [
        'cache' => [
            'class' => FileCache::class,
        ],
        'db' => [
            'charset' => (string) env('DB_CHARSET'),
            'class' => Connection::class,
            'dsn' => env('DB_SCHEMA') . ':host=' . env('DB_HOST') . ';dbname=' . env('DB_NAME'),
            'enableSchemaCache' => !env('YII_DEBUG'),
            'password' => (string) env('DB_PASSWORD'),
            'username' => (string) env('DB_USER'),
        ],
        'log' => [
            'class' => Dispatcher::class,
            'targets' => [
                [
                    'class' => FileTarget::class,
                ],
            ],
            'traceLevel' => YII_DEBUG ? 3 : 0,
        ],
        'request' => [
            'enableCsrfValidation' => false,
            'parsers' => [
                'application/json' => JsonParser::class,
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'normalizer' => [
                'class' => UrlNormalizer::class,
            ],
            'rules' => [
                'GET api/message/get-status/<uuid:\w+-\w+-\w+-\w+-\w+>' => 'message-api/get-status',
                'POST api/message/send' => 'message-api/send',
            ],
            'showScriptName' => false,
        ],
    ],
    'container' => ArrayHelper::merge(
        require __DIR__ . '/di_container_common.php',
        require __DIR__ . '/di_container_service.php',
    ),
    'controllerNamespace' => 'app\\controllers',
    'language' => 'ru-RU',
    'runtimePath' => __DIR__ . '/../runtime/',
    'sourceLanguage' => 'ru-RU',
    'timezone' => 'Europe/Moscow',
];
