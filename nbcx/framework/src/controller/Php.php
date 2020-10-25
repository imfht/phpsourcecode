<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\controller;

use nb\Collection;
use nb\Pool;
use nb\Request;
use nb\Validate;
use nb\View;

/**
 * Php
 *
 * @package nb\controller
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/10/15
 *
 * @property View view
 */
class Php extends Driver {

    /**
     * 获取表单参数的类型，request|post|get等等
     * @var string
     */
    //public $_method='request';

    public function assign($name, $value = ''){
        $this->view->assign($name,$value);
        return $this;
    }

    public function display($template='', $vars = [], $config = []) {
        $this->view->display($template, $vars, $config);
    }

    /**
     * 获取表单参数,并包装城Collection返回
     * 如果获取多个，则以值数组的形式返回
     *
     * @param mixed ...$params
     * @return Collection
     */
    public function formx(...$params){
        $input = call_user_func_array(
            [$this,'form'],
            $params
        );
        return new Collection($input);
    }

    /**
     * 获取表单参数
     * @param $params
     * @return array|bool
     */
    public function form($method='request', ...$args){
        if(is_array($method)) {
            $args = $method;
            $method = $this->method;
            //$this->form(['name','pass']);
        }
        else if($args && is_array($args[0])) {
            $args = $args[0];
            //$this->form('get',['name','pass']);
        }

        //$this->form('get','name','pass');
        $method === null and $method = $this->method;

        $form = Request::form($method,$args);

        $va = Pool::get(Validate::class);

        if(!$va || $va->scene('_form_',$args)->check($form)) {
            return $form;
        }

        return  Pool::get('controller')->__error($va->error, $va->field);
    }


    /**
     * 获取表单参数对应的值
     * 如果获取多个，则以值数组的形式返回
     * @param $params
     * @return array|bool
     */
    public function input($arg,...$args){
        /** $args != null */
        if($args) {
            if(is_array($args[0])) {
                //$this->input('get',['name','pass']);
                $args = [$arg,$args[0]];
            }
            else {
                //$this->input('name','pass');
                array_unshift($args,$arg);
                $args = [$this->method,$args];
            }
        }
        else {
            /** $args == null */
            //$this->input('name');
            //$this->input(['name','pass']);
            $args = [$this->method,$arg];
        }

        $input = call_user_func_array([$this,'form'],$args);

        if(is_array($input) === false) {
            return null;
        }

        if(count($input) == 1) {
            return current($input);
        }

        return array_values($input);
    }

    protected function _isAjax() {
        return Request::driver()->isAjax;
    }

    protected function _isGet() {
        return Request::driver()->isGet;
    }

    protected function _isPost() {
        return Request::driver()->isPost;
    }

    protected function _view() {
        return View::driver();
    }

    protected function _method() {
        $exists = property_exists($this->controller,'_method');
        return $exists?$this->controller->_method:'request';
    }


}