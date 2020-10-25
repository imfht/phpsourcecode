<?php
namespace Wpf\Core;
class Init
{
    public function run(){
        global $di;

        $this->systemConstant();
        $this->registerAutoloaders();        
        if(IS_CLI){
            $di = new \Phalcon\DI\FactoryDefault\CLI();
        }else{
            $di = new \Phalcon\DI\FactoryDefault();
        }
        $this->registerServices();
        if(IS_CLI){
            $arguments = array();
            foreach ($argv as $k => $arg) {
                if ($k == 1) {
                    $arguments['task'] = $arg;
                } elseif ($k == 2) {
                    $arguments['action'] = $arg;
                } elseif ($k >= 3) {
                    $arguments['params'][] = $arg;
                }
            }
        }
        
        /**
         * Handle the request
         */
        if(IS_CLI){
            $application = new \Phalcon\CLI\Console();
            $application->setDI($di);
            // 定义全局的参数， 设定当前任务及动作
            define('CURRENT_TASK',   (isset($argv[1]) ? $argv[1] : null));
            define('CURRENT_ACTION', (isset($argv[2]) ? $argv[2] : null));
        }else{
            $application = new \Phalcon\Mvc\Application($di);
        }
        
        
         // 注册模块
        $application->registerModules(
            array(
                'www' => array(
                    'className' => 'Wpf\App\Www\Module',
                    'path'      => APP_PATH.'/www/Module.php',
                ),
                'admin' => array(
                    'className' => 'Wpf\App\Admin\Module',
                    'path'      => APP_PATH.'/admin/Module.php',
                ),
                'files' => array(
                    'className' => 'Wpf\App\Files\Module',
                    'path'      => APP_PATH.'/files/Module.php',
                ),
                'cron' => array(
                    'className' => 'Wpf\App\Cron\Module',
                    'path'      => APP_PATH.'/cron/Module.php',
                ),
            )
        );
        
        require_once(FUNCTIONS_PATH."/functions.php");
        
        //try {
            if(IS_CLI){
                $application->handle($arguments);
            }else{
                //var_dump($application->handle()->getContent());
                echo $application->handle()->getContent();
            }
            
        //} catch(\Exception $e) {
        //     echo "PhalconException: ", $e->getMessage();
        //}

    }
    
    /**
     * 加载配置并注册服务
     * Init::registerServices()
     * 
     * @param mixed $di
     * @return void
     */
    public function registerServices(){
        global $di;
        
        
        $config = require_once(CONFIG_PATH . "/config.php");

        $di->set('config', function() use ($config){        
            return $config;
        });
        
        $di->set('db', function() use ($config) {
            return new \Phalcon\Db\Adapter\Pdo\Mysql($config->database->toArray());
        });
        
        
        $di->set('dbSlave', function() use ($config) {
            
            $connection =  new \Phalcon\Db\Adapter\Pdo\Mysql($config->Slavedatabase->toArray());
            return $connection;
        });
        
        $di->set('dbMaster', function() use ($config) {
            $connection =  new \Phalcon\Db\Adapter\Pdo\Mysql($config->Masterdatabase->toArray());
            return $connection;
        });
        
        
        // Setup a base URI so that all generated URIs include the "tutorial" folder
        $di->set('url', function(){
            $module_domain = $_SERVER['HTTP_HOST'];
            $url = new \Phalcon\Mvc\Url();
            $url->setBaseUri("http://".$module_domain."/");
            return $url;
        });
        
        $di->set('modelsCache', function() use ($config) {
        
            // 默认缓存时间为一天
            $frontCache = new \Phalcon\Cache\Frontend\Data($config->cache_lifttime->toArray());
        
            // Memcached连接配置 这里使用的是Memcache适配器
            //$cache = new \Phalcon\Cache\Backend\Libmemcached($frontCache, $config->memcache->toArray());
            
            $cache = new \Phalcon\Cache\Backend\Redis($frontCache,$config->redis->toArray());
        
            return $cache;
        });
        
        
        $di->set('cache',function() use ($config) {
            $fileFrontCache = new \Phalcon\Cache\Frontend\Data($config->cache_lifttime->toArray());
            $redisFrontCache = new \Phalcon\Cache\Frontend\Data($config->cache_lifttime->toArray());
            
            
            
            if(!is_dir($config->filecache->cacheDir)){
                mkdir($config->filecache->cacheDir,0755,true);
            }
            
            $cache = new \Phalcon\Cache\Multiple(array(
                new \Phalcon\Cache\Backend\Redis($redisFrontCache, $config->redis->toArray()),
                new \Phalcon\Cache\Backend\File($fileFrontCache,$config->filecache->toArray())
            ));
            
            return $cache;
            
        });
        
        if(! APP_DEBUG){
            $di->set('modelsMetadata',function(){            
                if(!is_dir($config->modelsMetadata->metaDataDir)){
                    mkdir($config->modelsMetadata->metaDataDir,0755,true);
                }
                $metaData = new \Phalcon\Mvc\Model\MetaData\Files($config->modelsMetadata->toArray());
                return $metaData;
            });
        }
        
        
        
        $di->setShared('MobileDetect',function(){
            $MobileDetect = new \MobileDetect();
            return $MobileDetect;
        });
        
        
        $di->setShared('session', function() use ($config) {
            $session = new \Phalcon\Session\Adapter\Memcache($config->memcache->toArray());
            $session->start();
            return $session;
        });
        
        
        //加密解密
        $di->setShared('crypt', function() use ($config) {
            $crypt = new \Phalcon\Crypt();
            $crypt->setKey($config->cryptKey); // 使用你自己的key！
            return $crypt;
        });
        
        
        if(! IS_CLI){
            $di->set('router', function () {
                
                $router = new \Phalcon\Mvc\Router();
            
                $router->setDefaultModule(_MODULE_);
                $router->setDefaultNamespace('Wpf\\App\\'.ucfirst(_MODULE_).'\\Controllers');
            
                return $router;
            });
            
            
            $di->set('dispatcher', function() {

                //创建一个事件管理
                $eventsManager = new \Phalcon\Events\Manager();
            
                //附上一个侦听者
                $eventsManager->attach("dispatch:beforeDispatchLoop", function($event, $dispatcher) {
            
                    $keyParams = array();
                    $params = $dispatcher->getParams();
            
                    //用奇数参数作key，用偶数作值
                    foreach ($params as $number => $value) {
                        if ($number & 1) {
                            $_GET[$params[$number - 1]] = $_REQUEST[$params[$number - 1]] = $keyParams[$params[$number - 1]] = $value;
                        }
                    }
            
                    //重写参数
                    $dispatcher->setParams($keyParams);
                });
                
                
                if(_MODULE_ == "files"){
                    //附上一个侦听者
                    $eventsManager->attach("dispatch:beforeException", function($event, $dispatcher, $exception) {
                
                        //处理404异常
                        if ($exception instanceof \Phalcon\Mvc\Dispatcher\Exception) {
                            $dispatcher->forward(array(
                                'controller' => 'Index',
                                'action' => 'index'
                            ));
                            return false;
                        }
                
                        //代替控制器或者动作不存在时的路径
                        if ($event->getType() == 'beforeException') {
                            switch ($exception->getCode()) {
                                case \Phalcon\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                                case \Phalcon\Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                                    $dispatcher->forward(array(
                                        'controller' => 'Index',
                                        'action' => 'index'
                                    ));
                                    return false;
                            }
                        }
                    });
                }
                
                $dispatcher = new \Phalcon\Mvc\Dispatcher();
                $dispatcher->setEventsManager($eventsManager);
                return $dispatcher;
            });
            
        }
        
        
    }
    
    /**
     * 自动加载
     * Init::registerAutoloaders()
     * 
     * @return void
     */
    public function registerAutoloaders(){
        
        $loader = new \Phalcon\Loader();
        $loader->registerDirs(array(
            ROOT_PATH."/",
            LIBS_PATH."/",
        ));
        
        $loader->registerNamespaces(
            array(
               'Wpf' => ROOT_PATH."/",
               'Wpf\Common' => COMMON_PATH."/",
               'Wpf\Libs' => LIBS_PATH."/",
               'Phalcon' => ROOT_PATH.'/Phalcon/'
            )
        );
        
        
        if(IS_CLI){
            $loader->registerDirs(
                array(
                    APP_PATH.'/cron/Controllers/',
                    APP_PATH.'/cron/Models/',
                    //LIBS_PATH."/",
                )
            ,true);
        
            $loader->registerNamespaces(
                array(
                    'Wpf\App\Cron\Controllers' => APP_PATH.'/cron/Controllers/',
                    'Wpf\App\Cron\Models'      => APP_PATH.'/cron/Models/',
                )
            ,true); 
        }
        $loader->register();
        
    }
    
    /**
     * 定义系统常量
     * Init::systemConstant()
     * 
     * @return void
     */
    public function  systemConstant(){
        define('IS_CGI',(0 === strpos(PHP_SAPI,'cgi') || false !== strpos(PHP_SAPI,'fcgi')) ? 1 : 0 );
        define('IS_WIN',strstr(PHP_OS, 'WIN') ? 1 : 0 );
        define('IS_CLI',PHP_SAPI=='cli'? 1   :   0);
        
        
        define('ROOT_PATH', realpath('../'));
        define('PUBLIC_PATH',ROOT_PATH."/public");
        define('APP_PATH',ROOT_PATH."/app");
        define('CACHE_PATH',ROOT_PATH."/cache");
        define('FUNCTIONS_PATH',ROOT_PATH."/functions");
        define('LOGS_PATH',ROOT_PATH."/logs");
        define('CONFIG_PATH',ROOT_PATH."/config");
        define('LIBS_PATH',ROOT_PATH."/libs");
        define('COMMON_PATH',ROOT_PATH."/common");
        
        $arr = explode(".",$_SERVER['HTTP_HOST']);
        $domain = implode(".",array_slice($arr, -2));
        
        define('_DOMAIN_',$domain);
        
        
        $module = strstr($_SERVER['HTTP_HOST'],".",true);
        
        define('_DOMAIN_PRE_',$module);
        
        
        switch($module){
            case "admin":
                $module = "admin";
                break;
            case "files":
                $module = "files";
                break;
            default:
                $module = "www";
        }
        
        define('_MODULE_',$module);
        
        define('WWW_URL','http://www.'._DOMAIN_);
        define('STATIC_URL','http://files.'._DOMAIN_);
        define('ADMIN_URL','http://admin.'._DOMAIN_);
    }   
}