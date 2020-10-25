#!/usr/bin/env phpunit
<?php
defined('TEST_ROOT') or define('TEST_ROOT', __DIR__);
defined('CUTE_ROOT') or define('CUTE_ROOT', dirname(TEST_ROOT));
defined('APP_ROOT') or define('APP_ROOT', CUTE_ROOT . '/apps/Blog');
defined('SRC_ROOT') or define('SRC_ROOT', CUTE_ROOT . '/src');
defined('VENDOR_ROOT') or define('VENDOR_ROOT', CUTE_ROOT . '/vendor');
require_once(SRC_ROOT . '/bootstrap.php');

class TestApp extends \Cute\Application
{

    public function initiate()
    {
        parent::initiate();
        $root = \Cute\Web\Router::getCurrent();
        $this->installRef($root, ['dispatch', 'abort', 'redirect']);
        $this->installRef(\Cute\Web\Router::$current, ['route', 'expose']);
        $this->install('\\Cute\\Web\\Input', [
            'getClientIP', 'input' => 'getInstance',
        ]);
        return $this;
    }

    public function route()
    {
        $router = \Cute\Web\Router::getCurrent();
        $args = func_get_args();
        return exec_method_array($router, 'route', $args);
    }

    public function expose($directory, $wildcard = '*.php')
    {
        $router = \Cute\Web\Router::getCurrent();
        $router->expose($directory, $wildcard);
        return $this;
    }

}

$app = new TestApp([]);
$app->importStrip('Cute\\Contrib', CUTE_ROOT . '/contrib');
$app->import('Cutest', TEST_ROOT);
