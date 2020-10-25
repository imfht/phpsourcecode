<?php

use Phalcon\Mvc\View\Simple as View;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\DI\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Mvc\Model\Transaction\Manager as TxManager;
require APP_PATH . '/service/securimage/securimage.php';
require APP_PATH . '/service/uploadFileManager.php';
require APP_PATH . '/service/message.php';
require APP_PATH . '/service/MCODE.php';
require APP_PATH . '/service/utility.php';
$di = new FactoryDefault();

/**
 * Sets the view component
 */
$di->setShared('session', function() {
    $session = new Phalcon\Session\Adapter\Files();
    if(!isset($_SESSION)){
        session_start();
    }
    return $session;
});
$di['view'] = function () use ($config) {
    $view = new View();
    $view->setViewsDir(APP_PATH . $config->application->viewsDir);
    return $view;
};

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di['url'] = function () use ($config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);
    return $url;
};

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di['db'] = function () use ($config) {
    return new DbAdapter(array(
        "host" => $config->database->host,
        "username" => $config->database->username,
        "password" => $config->database->password,
        "dbname" => $config->database->dbname
    ));
};

$di['ICODE'] = function() use($di) {
    return new Securimage();

};
$di['UFM'] = function(){
    return new uploadFileManager();
};

$di->setShared('Message',function()use($config){
    return new Message($config->message->user_id,$config->message->account,$config->message->password);
});

$di['MCODE'] = function()use($di){
    return new MCODE($di->getShared('session'),$di->getShared('Message'));
};





$di->setShared('TxManager',function(){
    return new TxManager();
});


$di->setShared('utility',function(){
    return new utility();
});

