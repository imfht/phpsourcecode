<?php

/**
 * @name Bootstrap
 * @author seiven-com-pc\user
 * @desc 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * @see http://www.php.net/manual/en/class.yaf-bootstrap-abstract.php
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends Yaf_Bootstrap_Abstract
{
    private $config = null;

    public function _initSession($dispatcher)
    {
        Yaf_Session::getInstance()->start();
        define('REQUEST_METHOD', strtoupper($dispatcher->getRequest()->getMethod()));
    }

    public function _initConfig()
    {
        // 把配置保存起来
        $arrConfig = Yaf_Application::app()->getConfig();
        $this->config = $arrConfig;
        Yaf_Registry::set('config', $arrConfig);
    }

    public function _initLoader()
    {
        if (file_exists(APPLICATION_PATH . "/vendor/autoload.php")) Yaf_Loader::import(APPLICATION_PATH . "/vendor/autoload.php");
    }

    public function _initDb(Yaf_Dispatcher $dispatcher)
    {
        ActiveRecord\Config::initialize(function ($cfg) {
            $cfg->set_model_directory(APPLICATION_PATH . '/application/models');
            $cfg->set_connections(array(
                'development' => $this->config->database->development,
                'production' => ($this->config->database->production->master)?$this->config->database->production->master:$this->config->database->production
            ));
            $cfg->set_default_connection($this->config->database->default_connection);
        });
    }

    public function _initPlugin(Yaf_Dispatcher $dispatcher)
    {
        // 注册一个插件
        // $objSamplePlugin = new SamplePlugin();
        // $dispatcher->registerPlugin($objSamplePlugin);
    }

    public function _initRoute(Yaf_Dispatcher $dispatcher)
    {
        // 在这里注册自己的路由协议,默认使用简单路由
    }

    public function _initView(Yaf_Dispatcher $dispatcher)
    {
        // 在这里注册自己的view控制器，例如smarty,firekylin
        if (REQUEST_METHOD != 'CLI') {
            $smarty = new SmartyAdapter(null, $this->config->smarty);
            // $smarty->registerFunction('function','checkRight',array());
            $dispatcher->setView($smarty);
        }
    }
}
