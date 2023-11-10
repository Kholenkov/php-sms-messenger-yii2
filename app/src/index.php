<?php

use Dotenv\Dotenv;
use yii\web\Application;

require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/../config/env.php';
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

defined('YII_DEBUG') || define('YII_DEBUG', (bool) env('YII_DEBUG'));
defined('YII_ENV') || define('YII_ENV', env('YII_ENV'));
date_default_timezone_set('Europe/Moscow');

require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';

(new Application($config))->run();
