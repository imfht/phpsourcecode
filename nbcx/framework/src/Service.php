<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb;

/**
 * Service
 *
 * @package util
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/8/30
 */
class Service {

    //true成功，false失败
    public $status = null;

    //业务执行状态码
    public $code = 0;
    public $msg;
    public $data;

    protected $on = [];

    protected $controller;

    //默认回调成功还是失败
    protected $defaultCall = 'fail';

    public function __construct($controller) {
        $this->controller = $controller;
    }

    protected function form(...$params){
        return call_user_func_array (
            [$this->controller,'form'],
            $params
        );
    }

    protected function input(...$params){
        return call_user_func_array (
            [$this->controller,'input'],
            $params
        );
    }

    /**
     * @return $this
     */
    private static function instance() {
        //获取控制器对象
        $controller = Pool::get('controller');

        $self = get_called_class();
        return new $self($controller);
    }

    /**
     * 获取对象插件句柄
     *
     * @access public
     * @param string $handle 句柄
     * @return \nb\Hook
     */
    protected function hook($handle = NULL) {
        return \nb\Hook::pos(empty($handle) ? get_class($this) : $handle);
    }

    /**
     * 中间件条件触发
     *
     * @param bool $condition 是否触发中间件
     * @param null $function  触发中间件的方法
     * @param mixed ...$params 传给中间件函数的参数，最后一个参数如果是function，则为成功后的回调
     * @return Middle|Collection
     * @throws \ReflectionException
     */
    public static function trigger($condition=true,...$params) {
        //$this->middle(false);
        if ($condition == false) {
            return false;
        }

        return call_user_func_array([get_called_class(),'run'],$params);
    }

    public static function withTrigger($condition=true,...$params) {
        //$this->middle(false);
        if ($condition == false) {
            return false;
        }
        return call_user_func_array([get_called_class(),'withRun'],$params);
    }

    protected function with($function,$params=[]) {
        $this->status = call_user_func_array([$this,$function],$params);
        if($this->status) {
            isset($this->on['success']) and call_user_func($this->on['success'],$this->msg,$this->data,$this->code);
        }
        else {
            isset($this->on['fail']) and call_user_func($this->on['fail'],$this->msg,$this->code,$this->data);
        }
        return $this;
    }

    public static function withRun($function=null,$params=[],$callback=null) {
        $that = self::instance();

        if(is_array($function)) {
            $callback = $params;
            $params = $function;
            $function = Router::driver()->controller;
        }

        is_array($params) or $params = [$params];

        $that->on($that->defaultCall,$callback);

        return $that->with($function,$params);
    }

    /**
     * 中间件条件触发
     *
     * @param $that
     * @param null $function
     * @param mixed ...$params
     * @return $this|Collection
     */
    public static function run($function=null,$callback=null) {
        $that = self::instance();

        if($function instanceof \Closure) {
            $callback = $function;
            $function = Router::driver()->function;
        }

        $that->on($that->defaultCall,$callback);

        return $that->with($function);
    }

    /**
     * 设置成功或失败后触发的事件
     *
     * @param $type success|fail
     * @param $callback
     */
    public function on($type,$callback) {
        $callback and $this->on[$type] = $callback;
    }

    /**
     * 成功后触发回调
     * @param $callback
     */
    public function success($callback) {
        if($this->status) {
            return $callback($this);
        }
        return $this;
    }

    /**
     * 失败后触发回调
     * @param $callback
     */
    public function fail($callback) {
        if($this->status == false) {
            return $callback($this);//call_user_func_array($callback,);//
        }
        return $this;
    }

}