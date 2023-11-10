<?php

declare(strict_types=1);

namespace app\controllers;

use BadMethodCallException;
use Yii;
use yii\web\Controller;
use yii\web\Request;

class BaseController extends Controller
{
    protected function getRequest(): Request
    {
        if (!(Yii::$app->request instanceof Request)) {
            throw new BadMethodCallException('Bad request');
        }
        return Yii::$app->request;
    }

    protected function getRequestBodyParameters(): array
    {
        return (array) $this->getRequest()->getBodyParams();
    }

    protected function getRequestQueryParameters(): array
    {
        return $this->getRequest()->getQueryParams();
    }
}
