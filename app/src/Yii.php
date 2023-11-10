<?php

use yii\BaseYii;

class Yii extends BaseYii
{
    public static $app;
}

abstract class BaseApplication extends yii\base\Application
{
}

class WebApplication extends yii\web\Application
{
}

class ConsoleApplication extends yii\console\Application
{
}

spl_autoload_register(['Yii', 'autoload'], true, true);
Yii::$classMap = require __DIR__ . '/../vendor/yiisoft/yii2/classes.php';
Yii::$container = new yii\di\Container();
