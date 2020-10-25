<?php
namespace workerbase\classs;

/**
 * 程序运行流程
 * Class App
 * @method static end($exit=true) 结束程序
 * @method static run() 开始运行程序
 * @package workerbase\classs
 */
class App
{
    private static $_instance;

    //执行过结束事件
    protected $_ended = false;

    /**
     * 获取单例
     * @return App
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new App();
        }
        return self::$_instance;
    }


    public function _end($exit=true)
    {
        //主动退出，异步处理剩余任务
        if ($exit && function_exists('fastcgi_finish_request')) {
            //将用户请求返回给客户端
            fastcgi_finish_request();
        }

        //执行请求结束前任务
        if (!$this->_ended && AttachEvent::hasEventHandler('onEndRequest')) {
            $this->_ended = true;
            AttachEvent::onEndRequest();
        }

        if($exit) {
            exit(0);
        }
    }

    public function _run()
    {
        $this->_ended = false;
        //执行请求开始前任务
        if (AttachEvent::hasEventHandler('onBeginRequest')) {
            AttachEvent::onBeginRequest();
        }
        AttachEvent::clearEvent();
    }

    /**
     * 静态方法调用
     * @access public
     * @param  string $method 调用方法
     * @param  mixed  $args   参数
     * @return void
     */
    public static function __callStatic($method, $args)
    {
        return call_user_func_array([self::getInstance(), '_'.$method], $args);
    }

}
