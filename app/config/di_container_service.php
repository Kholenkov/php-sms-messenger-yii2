<?php

use app\vendors;
use app\vo\VendorType;
use Kholenkov\ProstorSmsSdk;

return [
    'definitions' => [],
    'singletons' => [
        vendors\Contract\Messenger::class . '__' . VendorType::ProstorSms->value => [
            'class' => vendors\ProstorSms::class,
        ],
        vendors\Contract\Messenger::class . '__' . VendorType::Stub->value => [
            'class' => vendors\Stub::class,
        ],
        vendors\Contract\MessengerSelector::class => function () {
            return new vendors\MessengerSelector(Yii::$container);
        },

        ProstorSmsSdk\Configuration\Configuration::class => function () {
            return new Kholenkov\ProstorSmsSdk\Configuration\Configuration(
                new ProstorSmsSdk\Configuration\ApiAccess(
                    (string) env('PROSTOR_SMS_API_BASE_URL'),
                    (string) env('PROSTOR_SMS_API_LOGIN'),
                    (string) env('PROSTOR_SMS_API_PASSWORD'),
                ),
                new ProstorSmsSdk\Configuration\Logger(
                    true,
                    true,
                ),
            );
        },
        ProstorSmsSdk\Interfaces\HttpClient::class => [
            'class' => ProstorSmsSdk\Dependency\CurlHttpClient::class,
        ],
        ProstorSmsSdk\Interfaces\Messages::class => [
            'class' => ProstorSmsSdk\Messages::class,
        ],
    ],
];
