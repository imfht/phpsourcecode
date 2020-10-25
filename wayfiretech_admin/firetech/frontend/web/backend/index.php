<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-25 23:48:12
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-08-18 17:25:49
 */
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__.'/../../../vendor/autoload.php';
require __DIR__.'/../../../vendor/yiisoft/yii2/Yii.php';
require __DIR__.'/../../../common/config/bootstrap.php';
require __DIR__.'/../../../backend/config/bootstrap.php';

$config = yii\helpers\ArrayHelper::merge(
    require __DIR__.'/../../../common/config/main.php',
    require __DIR__.'/../../../common/config/main-local.php',
    require __DIR__.'/../../../backend/config/main.php',
    require __DIR__.'/../../../backend/config/main-local.php'
);

/**
 * 打印.
 *
 * @param $array
 */
function p(...$array)
{
    echo '<pre>';

    if (count($array) == 1) {
        print_r($array[0]);
    } else {
        print_r($array);
    }

    echo '</pre>';
}

(new yii\web\Application($config))->run();
