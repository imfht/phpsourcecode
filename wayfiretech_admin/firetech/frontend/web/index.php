<?php
/***
 * @开源软件: 店滴AI-基于AI的软硬件开源解决方案
 * @官方地址: http://www.wayfirer.com/
 * @版本: 1.0
 * @邮箱: 2192138785@qq.com
 * @作者: Wang Chunsheng
 * @Date: 2020-02-28 22:38:41
 * @LastEditTime: 2020-04-25 02:51:08
 */

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__.'/../../vendor/autoload.php';
require __DIR__.'/../../vendor/yiisoft/yii2/Yii.php';
require __DIR__.'/../../common/config/bootstrap.php';
require __DIR__.'/../config/bootstrap.php';

$config = yii\helpers\ArrayHelper::merge(
    require __DIR__.'/../../common/config/main.php',
    require __DIR__.'/../../common/config/main-local.php',
    require __DIR__.'/../config/main.php',
    require __DIR__.'/../config/main-local.php'
);

(new yii\web\Application($config))->run();
