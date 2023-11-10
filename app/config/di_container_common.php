<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use yii\db\Connection;

return [
    'definitions' => [],
    'singletons' => [
        Connection::class => function () {
            return new Connection(
                [
                    'charset' => (string) env('DB_CHARSET'),
                    'dsn' => env('DB_SCHEMA') . ':host=' . env('DB_HOST') . ';dbname=' . env('DB_NAME'),
                    'password' => (string) env('DB_PASSWORD'),
                    'username' => (string) env('DB_USER'),
                ]
            );
        },
        LoggerInterface::class => function () {
            $logger = new Logger('sms-messenger');
            $logger->pushHandler(new StreamHandler(__DIR__ . '/../runtime/monolog.log'));
            return $logger;
        },
    ],
];
