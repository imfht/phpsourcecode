<?php

spl_autoload_register(function ($className) {
    $classNamePath = str_replace('\\', '/', $className);
    $the_path = __DIR__ . '/../' . $classNamePath . '.php';
    if (file_exists($the_path)) {
        require $the_path;
        return true;
    }
    return false;
});
$loader = new \Phalcon\Loader();
$loader->register();

use Phalcon\Loader;
use Core\Config;
use Core\Flash;
use Core\Plugin;
use Core\Security;
use Core\Url;
use Core\View;
use Phalcon\Crypt;
use Phalcon\DI\FactoryDefault;
use Phalcon\Flash\Session as Sflash;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Model\Manager as ModelsManager;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\View\Engine\Volt;
use Phalcon\Session\Adapter\Files as Session;

$args = getopt('c:');
print_r($args);
// 初始化设置组件
define('WEB_CODE', $args['c']);
define('ADMIN_PREFIX', 'admin');
define('QUEUE_SALT', '455645ww');
define('ROOT_DIR', __DIR__ . '/../../');
define('MODULES_DIR', ROOT_DIR . 'Modules/');
define('THEMES_DIR', ROOT_DIR . 'THEMES/');
define('WEB_DIR', ROOT_DIR . WEB_CODE . '/');
define('CACHE_DIR', ROOT_DIR . 'web/' . WEB_CODE . '/cache/');
define('DOWNLOAD_DIR', ROOT_DIR . 'web/' . WEB_CODE . '/cache/download/');
$config = Config::get('config');

// 是否在开发者模式下运行
if ($config['debug'] == true) {
    ini_set("display_errors", "On");
    error_reporting(E_ALL | E_STRICT);
}

date_default_timezone_set($config['timezone']);
//
$di = new FactoryDefault();

// 注册数据库服务
$dbClass = 'Phalcon\Db\Adapter\Pdo\\' . $config['db']['adapter'];
unset($config['db']['adapter']);
$db = new $dbClass($config['db']);
// 注册session服务
$di->setShared('session', function () {
    ini_set("session.cookie_domain", 'mxsimu.com');
    $session = new Session();
    //$session->destroy(true);
    $session->start();
    return $session;
});
// 註冊消息提醒服務
$di->setShared('flashSession', function () {
    return new Sflash(array(
        'error' => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice' => 'alert alert-info',
    ));
});

// 注册事件管理器
$plugin = new Plugin();
$di->setShared('plugin', $plugin);
// 实例化路由
$router = new Router(false);

// 实例化view服务
$view = new View();

// 注册加密组件
$di->setShared('crypt', function () use ($config) {
    $crypt = new Crypt();
    // 设置全局加密密钥
    $crypt->setKey($config['cryptEncode']);
    return $crypt;
});
// 注册volt服务
$di->set('volt', function () use ($config, $view, $di) {
    $volt = new Volt($view, $di);
    $volt->setOptions(array(
        'compiledPath' => __DIR__ . '/../../web/' . WEB_CODE . '/cache/volt/',
    ));
    $viewstags = Config::cache('viewTags');
    $compiler = $volt->getCompiler();
    $compiler->addExtension(new \Core\Library\ViewFunctionExtension());
    foreach ($viewstags as $vKey => $vValue) {
        if ($vValue['type'] == 'extension') {
            $compiler->addExtension(new $vValue['function']());
        } elseif ($vValue['type'] == 'function') {
            $compiler->addFunction($vKey, function ($resolvedArgs, $exprArgs) use ($vValue) {
                return $vValue['fun'] . '(' . $resolvedArgs . ')';
            });
        } elseif ($vValue['type'] == 'anonymous_filter') {
            $compiler->addFunction($vKey, function ($resolvedArgs, $exprArgs) use ($vValue) {
                return $vValue['function']($resolvedArgs, $exprArgs);
            });
        } elseif ($vValue['type'] == 'filter') {
            $compiler->addFilter($vKey, $vValue['function']);
        }
    }
    return $volt;
});

$dispatcher = new Dispatcher();
$security = new Security();
$eventsManager = $di->getShared('eventsManager');
$eventsManager->attach('dispatch', $security);
// 注册模型管理服务
$di->set('modelsManager', function () {
    $modelsManager = new ModelsManager();
    return $modelsManager;
});
$modulesList = Config::get('modules', array());
$themesList = Config::get('themes',array());

foreach ($modulesList as $value) {
    require_once __DIR__ . '/../../Modules/' . $value . '/Module.php';
}
foreach ($themesList as $value) {
    require_once __DIR__ . '/../../Themes/' . $value . '/Theme.php';
}

$di->setShared('db', $db);
// 加载路由和命名空间
$routes = Config::cache('routes');
// 注册消息提醒服务
$di->setShared('flash', function () {
    return new Flash(array(
        'error' => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice' => 'alert alert-info',
    ));
});
//$router->notFound($routes['notFound']);
foreach ($routes as $key => $value) {
    if (isset($value['pattern'])) {
        $router->add($value['pattern'], $value['paths'], $value['httpMethods'])->setName($key);
    }
}
$router->removeExtraSlashes(true);
$di->setShared('router', $router);
$di->setShared('view', $view);
$url = new Url();
$di->setShared('url', $url);
// 注册调度器服务
echo 'success';
