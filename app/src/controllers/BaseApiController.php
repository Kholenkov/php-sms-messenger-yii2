<?php

declare(strict_types=1);

namespace app\controllers;

use BadMethodCallException;
use Yii;
use yii\web\Response;

class BaseApiController extends BaseController
{
    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);

        if (!(Yii::$app->response instanceof Response)) {
            throw new BadMethodCallException('Bad response');
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
    }

    protected function responseError(string $errorMessage, int $statusCode = 400): array
    {
        if (!(Yii::$app->response instanceof Response)) {
            throw new BadMethodCallException('Bad response');
        }
        Yii::$app->response->statusCode = $statusCode;
        Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'result' => 'error',
            'payload' => [
                'errorMessage' => $errorMessage,
                'statusCode' => $statusCode,
            ],
        ];
    }

    protected function responseSuccess(array $payload, int $statusCode = 200): array
    {
        if (!(Yii::$app->response instanceof Response)) {
            throw new BadMethodCallException('Bad response');
        }
        Yii::$app->response->statusCode = $statusCode;
        Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'result' => 'success',
            'payload' => $payload,
        ];
    }
}
