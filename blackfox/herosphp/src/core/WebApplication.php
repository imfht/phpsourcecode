<?php
/**
 * HerosPHP 应用程序实例类,单例模式
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2016-11-16 v1.2.1
 */

namespace herosphp\core;

use herosphp\core\interfaces\IApplication;
use herosphp\exception\FileNotFoundException;
use herosphp\exception\HeroException;
use herosphp\http\HttpRequest;

class WebApplication implements IApplication {

    /**
     * http 请求对象
     * @var \herosphp\http\HttpRequest
     */
    private $httpRequest;

    /**
     * 系统配置信息
     * @var array
     */
    private $configs = array();

    /**
     * action 实例
     * @var Object
     */
    private $actionInstance = null;

    /**
     * 应用程序监听器
     * @var array(IWebApplicationListener)
     */
    private $listeners = array();

    /**
     * 应用程序错误对象
     * @var \herosphp\core\AppError
     */
    private $appError = null;

    /**
     * 应用程序唯一实例
     * @var WebApplication
     */
    private static $_INSTANCE = null;

    private function __construct() {
        //加载应用程序的全局监听器
        if (file_exists(APP_PATH."modules/DefaultWebappListener.php")) {
            $lisennerClassName = APP_NAME."\\DefaultWebappListener";
            try {
                $reflect = new \ReflectionClass($lisennerClassName);
                $this->listeners[] = $reflect->newInstance();
            } catch (\Exception $exception) {}
        }

        $this->appError = new AppError();
    }

    /**
     * 执行应用程序
     * @param $configs
     * @throws HeroException
     * @param param 系统配置信息 $array
     */
    public function execute($configs) {

        $this->setConfigs($configs);

        //请求初始化
        $this->requestInit();

        //检查当前模块下是否有监听器，如果有则加载监听器
        $lisennerClassName = APP_NAME."\\".$this->httpRequest->getModule()."\\ModuleListener";
        try {
            $reflect = new \ReflectionClass($lisennerClassName);
            $this->listeners[] = $reflect->newInstance();
        } catch (\Exception $exception) {
            //__print($exception);die();
        }
        //如果是单元测试，则直接返回
		if(defined("PHP_UNIT") && PHP_UNIT == true) return;

        //invoker 方法调用
        try {
            $this->actionInvoke();
        } catch(HeroException $e) {
            //记录日志
            Log::error($e->toString());
            if ( APP_DEBUG ) { //如果是调试模式就抛出异常
                throw $e;
            } else {
                die("Hacker Attempt!");
            }
        }

        //发送响应
        $this->sendResponse();

    }

    public static function getInstance() {

        if ( self::$_INSTANCE == null ) {
            self::$_INSTANCE = new self();
        }
        return self::$_INSTANCE;
    }

    /**
     * @see \herosphp\core\interfaces\IApplication::requestInit()
     */
    public function requestInit()
    {
        //调用生命周期监听器
        if ( !empty($this->listeners) ) {
            foreach ( $this->listeners as $listener ) {
                $listener->beforeRequestInit();
            }
        }
        $this->httpRequest = new HttpRequest();
        $this->httpRequest->parseURL();
    }

    /**
     * @see \herosphp\core\interfaces\IApplication::actionInvoke()
     */
    public function actionInvoke()
    {
        //调用生命周期监听器，方法调用之前
        if ( !empty($this->listeners) ) {
            foreach ( $this->listeners as $listener ) {
                if ($listener->isListening($this->httpRequest)) {
                    $listener->beforeActionInvoke($this->httpRequest);
                }
            }
        }

        //加载控制器Action文件
        $module = $this->httpRequest->getModule();
        $action = $this->httpRequest->getAction();
        $method = $this->httpRequest->getMethod();

        $actionDir = APP_PATH."modules/{$module}/action/";
        $filename = $actionDir.ucfirst($action).'Action.php';
        if ( !file_exists($filename) ) {
            throw new FileNotFoundException($filename, FileNotFoundException::ERROR_CODE);
        }
        $className = APP_NAME."\\{$module}\\action\\".ucfirst($action)."Action";
        $reflect = new \ReflectionClass($className);
        $this->actionInstance = $reflect->newInstance();

        //调用初始化方法
        if ( $reflect->hasMethod('C_start') ) {
            $reflect->getMethod('C_start')->invoke($this->actionInstance);
        }

        //根据动作去找对应的方法
        if ( $reflect->hasMethod($method) ) {
            $reflect->getMethod($method)->invoke($this->actionInstance, $this->httpRequest);
        } else {
            throw new \BadMethodCallException("Method {$className}::{$method} not found!");
        }

    }

    /**
     * @see \herosphp\core\interfaces\IApplication::sendResponse()
     */
    public function sendResponse()
    {
        //调用响应发送前生命周期监听器
        if ( !empty($this->listeners) ) {
            foreach ( $this->listeners as $listener ) {
                $listener->beforeSendResponse($this->httpRequest, $this->actionInstance);
            }
        }

        //加载并显示视图
        $this->actionInstance->display($this->actionInstance->getView());

        //调用响应发送后生命周期监听器
        if ( !empty($this->listeners) ) {
            foreach ( $this->listeners as $listener ) {
                if ($listener->isListening($this->httpRequest)) {
                    $listener->afterSendResponse($this->actionInstance);
                }
            }
        }
    }

    /**
     * @param array $configs
     */
    public function setConfigs($configs)
    {
        $this->configs = $configs;
    }

    /**
     * @return array
     */
    public function getConfigs()
    {
        return $this->configs;
    }

    /**
     * 获取指定key的配置值
     * @param string $key 配置key
     * @return mixed
     */
    public function getConfig( $key ) {
        return $this->configs[$key];
    }

    /**
     * @param \herosphp\http\HttpRequest $httpRequest
     */
    public function setHttpRequest($httpRequest)
    {
        $this->httpRequest = $httpRequest;
    }

    /**
     * @return \herosphp\http\HttpRequest
     */
    public function getHttpRequest()
    {
        return $this->httpRequest;
    }

    /**
     * @return AppError
     */
    public function getAppError() {
        return $this->appError;
    }

    /**
     * @return array
     */
    public function getListeners()
    {
        return $this->listeners;
    }

}
