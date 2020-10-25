<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\exception;

use nb\Debug;
use nb\Pool;

/**
 * Driver
 *
 * @package nb\exception
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/11/28
 */
abstract class Driver {

    public function shutdown() {
        //有错记错
        $e = error_get_last();
        if($e) {
            $e = new \ErrorException($e['message'], $e['type'], $e['type'], $e['file'], $e['line']);
            $this->dowith($e,true);
        }
        Debug::end();
    }

    //set_exception_handler
    public function exception($e) {
        $this->dowith($e,true);
    }

    //set_error_handler
    public function error($error,$errstr, $errfile=null, $errline=null, $errcontext=null){
        //错误捕获
        $e = new \ErrorException($errstr, $error, $error, $errfile, $errline);
        $this->dowith($e);

    }

    protected function dowith($e,$deadly = false) {
        Debug::record(2,$e);
        $do = Pool::object('nb\\event\\Framework')->error($e,$deadly);
        $do and $this->show($e,$deadly);
    }


    /**
     * 当程序运行遇到错误，都会回调此方法
     *
     * PS:error里不能使用quit函数，否则会造成错误冲突
     *
     * @param array $e 错误信息
     * @param bool $deadly 是否终止程序运行，一般true代表遇到致命错误
     */
    abstract protected function show($e,$deadly = false);

    abstract public function notfound();

}