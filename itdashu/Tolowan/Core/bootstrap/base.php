<?php
use Core\Config;
use Core\Flash;
use Core\Mvc\Application;
use Core\Mvc\View;
use Core\Security;
use Core\Cache;
use Phalcon\Cache\Backend\File as FileBackend;
use Phalcon\Cache\Frontend\Data as FrontData;
use Phalcon\Cache\Frontend\Output as OutputFrontend;
use Phalcon\Crypt;
use Phalcon\Di\FactoryDefault;
use Phalcon\Flash\Session as FlashSession;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Session\Adapter\Files as Session;

define('WEB_CODE', $site[$_SERVER['HTTP_HOST']]);
define('ROOT_DIR', __DIR__ . '/../../');
define('MODULES_DIR', ROOT_DIR . 'Modules/');
define('THEMES_DIR', ROOT_DIR . 'Themes/');
define('WEB_DIR', ROOT_DIR . WEB_CODE . '/');
define('CACHE_DIR', ROOT_DIR . 'Web/' . WEB_CODE . '/cache/');
define('CONFIG_DIR', ROOT_DIR . 'Web/' . WEB_CODE . '/config/');
define('DOWNLOAD_DIR', ROOT_DIR . 'Web/' . WEB_CODE . '/cache/download/');
// 初始化设置组件
$config = Config::get('config');
define('ADMIN_PREFIX', $config['adminPrefix']);
define('DEBUG', $config['debug']);
// 是否在开发者模式下运行
if (DEBUG == true) {
    ini_set("display_errors", "On");
    error_reporting(E_ALL | E_STRICT);
}

date_default_timezone_set($config['timezone']);

include __DIR__ . '/../common.php';

$di = new FactoryDefault();
$di->setShared('translate', 'Core\Translate');
$di->setShared('eventsManager', 'Phalcon\Events\Manager');
$di->setShared('url', 'Core\Mvc\Url');
$di->setShared('request', 'Phalcon\Http\Request');
$di->setShared('response', 'Phalcon\Http\Response');
$di->setShared('cookies', 'Phalcon\Http\Response\Cookies');
$di->setShared('tag', 'Phalcon\Tag');
$di->setShared('escaper', 'Phalcon\Escaper');
$di->setShared('annotations', 'Phalcon\Annotations\Adapter\Memory');
$di->setShared('modelsManager', 'Phalcon\Mvc\Model\Manager');
$di->setShared('modelsMetadata', 'Phalcon\Mvc\Model\MetaData\Memory');
$di->setShared('transactionManager', 'Phalcon\Mvc\Model\Transaction\Manager');
$di->setShared('assets', 'Core\Assets\Manager');
$di->setShared('filter', 'Core\Filter');
// 注册volt服务
$di->setShared('volt', 'Core\Mvc\View\Volt');
$di->setShared('view', 'Core\Mvc\View');
// 实例化路由
$di->setShared('router', 'Core\Mvc\Router');

// 注册数据库服务
foreach ($config['dbs'] as $dbName => $dbInfo) {
    $di->set($dbName, function () use ($dbName, $dbInfo) {
        $dbClass = '\Phalcon\Db\Adapter\Pdo\\' . $dbInfo['adapter'];
        $connection = new $dbClass($dbInfo);
        return $connection;
    });
}
// 注入模型缓存服务
if ($config['modelsCache']) {
    $di->setShared('modelsCache', function () use ($config) {
        $cache = new Cache();
        return $cache->getCache($config['modelsCache'], 'data');
    });
}
// 注入视图缓存服务
if ($config['viewCache']) {
    $di->setShared('viewCache', function () use ($config) {
        $cache = new Cache();
        return $cache->getCache($config['viewCache'], 'output');
    });
}
// 注册session服务
$di->setShared('session', function () use ($config) {
    if (!isset($config['session'])) {
        $config['session'] = [
            'adapter' => 'Files',
            'config' => []
        ];
    }
    $sessionAdapter = '\Phalcon\Session\Adapter\\' . $config['session']['adapter'];
    $session = new $sessionAdapter($config['session']['config']);
    $session->start();
    return $session;
});
// 注册消息提醒服务
$di->setShared('flash', function () use ($config) {
    return new Flash($config['flash']);
});
// 註冊消息提醒服務
$di->setShared('flashSession', function () use ($config) {
    return new FlashSession($config['flash']);
});

// 注册加密组件
$di->setShared('crypt', function () use ($config) {
    $crypt = new Crypt();
    // 设置全局加密密钥
    $crypt->setKey($config['cryptEncode']);
    return $crypt;
});

// 加载模块
$modulesList = Config::get('modules');
foreach ($modulesList as $value) {
    require_once __DIR__ . '/../../Modules/' . ucfirst($value) . '/Module.php';
}
// 注册调度器服务
$security = new Security();
$di->setShared('security', $security);
$di->getShared('eventsManager')->attach('dispatch', $security);
$dispatcher = new Dispatcher();
$dispatcher->setEventsManager($di->getShared('eventsManager'));
$di->setShared('dispatcher', $dispatcher);
try {
    // 创建应用
    $application = new Application($di);
    echo $application->handle()->getContent();
} catch (\Exception $e) {
    echo $e->getMessage();
}
