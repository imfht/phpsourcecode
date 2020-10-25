<?php
/**
 * Yaf_Dispatcher用于初始化处理请求的运行环境,
 * 它协调路由来的请求, 并分发和执行发现的动作,
 * 然后收集动作产生的响应, 输出响应给请求者, 并在整个过程完成以后返回响应.
 * Yaf_Dispatcher是单例模式运行的,
 * 也就是说自始至终只生成一个Yaf_Dispatcher实例,
 * 因此, 可以把它看成是在分发过程中生成的对象的注册表, 可以从中获取到分发过程中产生的对象.
 * 
 * @package Yaf
 * @author 李枨煊<lcx165@gmail.com> (DOC Only)
 */
final class Yaf_Dispatcher {

    protected $_router;

    protected $_view;

    protected $_request;

    protected $_plugins;

    protected $_instance;

    protected $_auto_render;

    protected $_return_response;

    protected $_instantly_flush;

    protected $_default_module;

    protected $_default_controller;

    protected $_default_action;

    /**
     * 初始化视图引擎并返回它
     *
     * @param string $templates_dir 
     * @param array $options 
     *
     * @return Yaf_View_Interface
     */
    public function initView($templates_dir, $options = null) {}

    /**
     * 获取当前的Yaf_Application实例
     *
     * @return Yaf_Application
     */
    public function getApplication() {}

    /**
     * 设置视图引擎
     *
     * @param Yaf_View_Interface $view A Yaf_View_Interface instance
     *
     * @return Yaf_Dispatcher
     */
    public function setView($view) {}

    /**
     * 获取路由器
     *
     * @return Yaf_Router
     */
    public function getRouter() {}

    /**
     * Yaf_Dispatcher 不能被序列化
     *
     * @return void
     */
    private function __sleep() {}

    /**
     * 开启自动渲染
     *
     * @return Yaf_Dispatcher
     */
    public function enableView() {}

    /**
     * 设置路由的默认模块
     *
     * @param string $module 
     *
     * @return Yaf_Dispatcher
     */
    public function setDefaultModule($module) {}

    /**
     * 打开关闭自动响应
     *
     * @param bool $flag 
     *
     * @return Yaf_Dispatcher
     */
    public function flushInstantly($flag) {}

    /**
     * 开启/关闭自动异常捕获功能
     *
     * @param bool $flag bool
     *
     * @return Yaf_Dispatcher
     */
    public function catchException($flag = null) {}

    /**
     * 开启/关闭自动渲染功能
     *
     * @param bool $flag bool
     *
     * @return Yaf_Dispatcher
     */
    public function autoRender($flag) {}

    /**
     * 设置路由的默认动作
     *
     * @param string $action 
     *
     * @return Yaf_Dispatcher
     */
    public function setDefaultAction($action) {}

    /**
     * 获取当前的Yaf_Dispatcher实例
     *
     * @return Yaf_Dispatcher
     */
    public static function getInstance() {}

    /**
     * 开启/关闭异常抛出
     *
     * @param bool $flag bool
     *
     * @return Yaf_Dispatcher
     */
    public function throwException($flag = null) {}

    /**
     * Yaf_Dispatcher 不能呗反序列化
     *
     * @return void
     */
    private function __wakeup() {}

    /**
     * 关闭自动渲染
     *
     * @return bool
     */
    public function disableView() {}

    /**
     * 设置错误处理函数
     *
     * @param call $callback 错误处理的回调函数
     * @param int $error_types 
     *
     * @return Yaf_Dispatcher
     */
    public function setErrorHandler($callback, $error_types) {}

    /**
     * The returnResponse purpose
     *
     * @param bool $flag 
     *
     * @return Yaf_Dispatcher
     */
    public function returnResponse($flag) {}

    /**
     * Yaf_Dispatcher 不能被克隆
     *
     * @return void
     */
    private function __clone() {}

    /**
     * 设置路由的默认控制器
     *
     * @param string $controller 
     *
     * @return Yaf_Dispatcher
     */
    public function setDefaultController($controller) {}

    /**
     * 获取当前的请求实例
     *
     * @return Yaf_Request_Abstract
     */
    public function getRequest() {}

    /**
     * Yaf_Dispatcher 构造函数
     */
    public function __construct() {}

    /**
     * 分发请求
     *
     * @param Yaf_Request_Abstract $request 
     *
     * @return Yaf_Response_Abstract
     */
    public function dispatch($request) {}

    /**
     * 注册一个插件
     *
     * @param Yaf_Plugin_Abstract $plugin 
     *
     * @return Yaf_Dispatcher
     */
    public function registerPlugin($plugin) {}

    /**
     * The setRequest purpose
     *
     * @param Yaf_Request_Abstract $request 
     *
     * @return Yaf_Dispatcher
     */
    public function setRequest($request) {}


}