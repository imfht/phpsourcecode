#!/usr/bin/env php
<?php
defined('APP_ROOT') or define('APP_ROOT', __DIR__);
defined('CUTE_ROOT') or define('CUTE_ROOT', dirname(dirname(APP_ROOT)));
defined('SRC_ROOT') or define('SRC_ROOT', CUTE_ROOT . '/src');
defined('VENDOR_ROOT') or define('VENDOR_ROOT', CUTE_ROOT . '/vendor');
require_once(SRC_ROOT . '/bootstrap.php');

$settings = new \Cute\Cache\FileCache('settings', APP_ROOT);
$app = new \Cute\Shell\Console($settings->readData());
$app->importStrip('Cute\\Contrib', CUTE_ROOT . '/contrib');

$app->mount(CUTE_ROOT . '/contrib/Command');
$app->mount(APP_ROOT . '/commands');
$app->setAutoNS(true)->run();

