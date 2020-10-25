<?php
/**
 * Created by PhpStorm.
 * @author Luficer.p <81434146@qq.com>
 * Date: 16/11/3
 * Time: 上午11:23
 */

namespace LuciferP\TinyMvc\app;


use LuciferP\Base\ApplicationRegistry;
use LuciferP\Base\Instance;
use LuciferP\Router\Base\RouterFactory;
use LuciferP\TinyMvc\base\AppException;
use LuciferP\TinyMvc\base\Config;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class ApplicationHelper extends Instance
{
    protected static $instance;


    private $config_path ;

    /**
     * @throws AppException
     */

    public function init()
    {
        date_default_timezone_set("PRC");
        $this->config_path = BASE_DIR . '/config';
        if (TINYMVC_DEBUG) {
            $whoops = new \Whoops\Run();
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
            $whoops->register();
        }


        $main_conf = ApplicationRegistry::getConfig();
        if (is_null($main_conf)) {
            $this->getOptions();
        }
        $this->setDb();
        $this->setLog();

        return;


    }

    /**
     * @throws AppException
     */
    private function getOptions()
    {
        $config = new Config($this->config_path);
        if (!($config['main'])) {
            throw new AppException("config file not exists");
        }
        ApplicationRegistry::setConfig($config['main']);


    }

    /**
     * @throws AppException
     */
    private function setDb()
    {
        $db_conf = ApplicationRegistry::getConfig()['db'];
        if (!$db_conf) {
            throw new AppException("db config not exists");
        }
        \LuciferP\Orm\base\Registry::set("db_conf", $db_conf);
    }

    /**
     *
     */
    private function setLog()
    {
        $log_path = ApplicationRegistry::getConfig()['logs'];
        $log = new Logger("tinymvc");
        $log->pushHandler(new StreamHandler($log_path['debug'], Logger::DEBUG));
        $log->pushHandler(new StreamHandler($log_path['warning'], Logger::WARNING));
        ApplicationRegistry::setValue("log", $log);
    }


    /**
     * @throws AppException
     */
    public function runRouter()
    {
        $controller_namespace = ApplicationRegistry::getConfig()['controller']['namespace'];
        $router = RouterFactory::getRouter();
        $router->setControllerNameSpace($controller_namespace);

        $error_page = ApplicationRegistry::getConfig()['error'];
        $router->setErrorPage($error_page['page'], $error_page['code'], $error_page['message']);


        $routers = ApplicationRegistry::getConfig()['routers'];
        foreach ($routers as $file) {
            if (!file_exists($file)) {
                throw new AppException("router file not exists :{$file}");
            }
            require_once $file;
        }
        $router->run();

    }
}