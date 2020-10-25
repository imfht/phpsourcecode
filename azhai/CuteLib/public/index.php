<?php
defined('CUTE_ROOT') or define('CUTE_ROOT', dirname(__DIR__));
defined('APP_ROOT') or define('APP_ROOT', CUTE_ROOT . '/apps/Blog');
defined('SRC_ROOT') or define('SRC_ROOT', CUTE_ROOT . '/src');
defined('VENDOR_ROOT') or define('VENDOR_ROOT', CUTE_ROOT . '/vendor');
require_once(SRC_ROOT . '/bootstrap.php');

$settings = new \Cute\Cache\FileCache('settings', APP_ROOT);
$app = new \Cute\Web\Site($settings->readData());
$app->importStrip('Cute\\Contrib', CUTE_ROOT . '/contrib');

$app->route('/', function () {
    $app = app();
    $tpl = $app->load('tpl', 'default');
    $tpl->updateGlobals([
        'site_domain' => 'http://' . $_SERVER['HTTP_HOST'],
        'site_title'  => $app->getConfig('site_title'),
        'url_prefix'  => $app->getConfig('url_prefix'),
        'asset_url'   => $app->getConfig('asset_url'),
    ]);
    return $tpl->render('catalog.php');
});

$app->expose(__DIR__, '*.php');
$app->expose(__DIR__, '*/*.php');
$app->run();
