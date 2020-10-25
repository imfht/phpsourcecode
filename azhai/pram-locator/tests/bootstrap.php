#!/usr/bin/env phpunit
<?php
defined('TEST_ROOT') or define('TEST_ROOT', __DIR__);
defined('APP_ROOT') or define('APP_ROOT', dirname(TEST_ROOT));
defined('VENDOR_ROOT') or define('VENDOR_ROOT', APP_ROOT . '/vendor');

require_once APP_ROOT . '/src/Pram/Locator.php';
$locator = \Pram\Locator::getInstance();
$locator->addNamespace('PramTest', TEST_ROOT);

//运行测试，相当于命令行下 phpunit -c config.xml
if ($_SERVER['argc'] <= 2) { #/usr/local/bin/phpunit ./bootstrap.php
    $phpunit = new \PHPUnit_TextUI_Command();
    $phpunit->run(array('-c', __DIR__ . '/config.xml'));
}
