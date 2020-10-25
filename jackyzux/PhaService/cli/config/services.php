<?php

use Phalcon\Cli\Dispatcher;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;

/**
 * Shared configuration service
 */
$di->setShared('config', function () {
    return include BASE_PATH . '/cli/config/config.php';
});


/**
 * The logger
 */
//$di->setShared('logger', function () {
//    $loggerFile = sprintf('%s/var/log/%d.log', BASE_PATH, date('ymd'));
//    if (!is_dir(dirname($loggerFile))) @mkdir(dirname($loggerFile), 0755, TRUE);
//
//    return new \Phalcon\Logger\Adapter\File($loggerFile, ['mode' => 'a']);
//});

$di->setShared('logger', function () {
    $logDir = BASE_PATH . '/var/log/cli/' . date('ymd') . '/';
    if (!is_dir($logDir)) @mkdir($logDir, 0755, TRUE);
    return new Phalcon\Logger\Adapter\File\Multiple($logDir, [
        'prefix' => date('ymd'),
    ]);
});


/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->setShared('modelsMetadata', function () {
    return new MetaDataAdapter();
});


/**
 * Redis Service
 */
$di->setShared('redis', function () use ($di) {
    $opts = $di['config']['redis'];

    $frontendOpts = new \Phalcon\Cache\Frontend\Data([
        "lifetime" => $opts['lifetime'],
    ]);
    $backendRedis = new Phalcon\Cache\Backend\Redis($frontendOpts, $opts);

    return $backendRedis;
});


/**
 * Starts the session the first time some component requests the session service
 */
$di->setShared('session', function () use ($di) {
//    $session = new Phalcon\Session\Adapter\Files();
//    $session->start();
    $opts             = $di['config']['redis'];
    $opts['uniqueId'] = 'cn.qhbit.passport';
    $opts['index']    = 1;
    $opts['lifetime'] = 604800; //3600*24*7;
    $opts['prefix']   = 's_';
    $session          = new \Phalcon\Session\Adapter\Redis($opts->toArray());
    $session->setName('JSESSIONID');
    if ($session->isStarted() == FALSE) {
        $session->start();
    }

    return $session;
});


/**
 * Starts the cache service
 */
$di->setShared('cache', function () use ($di) {
    $client        = $di['redis'];
    $opts          = $client->getOptions();
    $opts['index'] = 2;
    $client->setOptions($opts);

    return $client;
});


/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function () {
    $config = $this->getConfig();

    if (defined('APP_DEBUGGER')) {
        // 分析底层sql性能，并记录日志
        $eventsManager = new \Phalcon\Events\Manager();
        $profiler      = new Phalcon\Db\Profiler();
        $eventsManager->attach('db', function ($event, $connection) use ($profiler) {
            if ($event->getType() == 'beforeQuery') {
                //在sql发送到数据库前启动分析
                $profiler->startProfile($connection->getSQLStatement());
            }
            if ($event->getType() == 'afterQuery') {
                //在sql执行完毕后停止分析
                $profiler->stopProfile();
                //获取分析结果
                $profile = $profiler->getLastProfile();
                $sql     = $profile->getSQLStatement();
                $params  = $connection->getSqlVariables();
                (is_array($params) && count($params)) && $params = json_encode($params);
                $executeTime = $profile->getTotalElapsedSeconds();

                //日志记录
                //$logger = $this->getLogger();
                $MySQLLoggerFile = sprintf('%s/var/log/%d.mysql.log', BASE_PATH, date('ymd'));
                if (!is_dir(dirname($MySQLLoggerFile))) @mkdir(dirname($MySQLLoggerFile), 0755, TRUE);
                $logger = new \Phalcon\Logger\Adapter\File($MySQLLoggerFile, ['mode' => 'a']);
                $logger->debug("{$sql}|{$params}|{$executeTime}");
            }
        });
    }

    $class  = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
    $params = [
        'host'     => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname'   => $config->database->dbname,
        'charset'  => $config->database->charset,
    ];


    if ($config->database->adapter == 'Postgresql') {
        unset($params['charset']);
    }

    $connection = new $class($params);

    if (defined('APP_DEBUGGER')) {
        /* 注册监听事件 */
        $connection->setEventsManager($eventsManager);
    }

    return $connection;
});


/**
 * Message Queue
 */
$di->setShared('beanstalk', function () {
    $config = $this->getConfig();
    //return new Phalcon\Queue\Beanstalk($config->beanstalk->toArray());
    return new Beanspeak\Client($config->beanstalk->toArray());
});


/**
 * Set the default namespace for dispatcher
 */
$di->setShared('dispatcher', function () {
    $dispatcher = new Dispatcher();
    //$dispatcher->setDefaultNamespace('PhaService\Tasks');
    return $dispatcher;
});

