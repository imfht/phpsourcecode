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

use nb\Pool;
use nb\Request;
use nb\Validate;

/**
 * Base
 *
 * @package nb\controller
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/10/15
 */
class Base extends Driver {

    /**
     * 获取表单参数,并包装城Collection返回
     * 如果获取多个，则以值数组的形式返回
     *
     * @param mixed ...$params
     * @return Collection
     */
    public function formx(...$args){
        $input = call_user_func_array(
            [$this,'form'],
            $args
        );
        return new Collection($input);
    }

    /**
     * 获取表单参数
     * @param $params
     * @return array|bool
     */
    public function form(...$args){
        $form = Request::form($args);

        $va = Pool::get(Validate::class);
        if(!$va) {
            return $form;
        }

        if($va->scene('_form_',$args)->check($form)) {
            return $form;
        }
        return $this->controller->__error($va->error, $va->field);
    }

    /**
     * 获取表单参数对应的值
     * 如果获取多个，则以值数组的形式返回
     * @param $params
     * @return array|bool
     */
    public function input(...$args){
        $input = call_user_func_array([$this,'form'],$args);

        if(is_array($input) === false) {
            return null;
        }

        if(count($input) == 1) {
            return current($input);
        }

        return array_values($input);
    }

}