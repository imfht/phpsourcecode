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
 * Exception
 *
 * @package nb
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/12/7
 */
class Exception extends Component {

    /**
     * @var \nb\exception\Driver
     */
    public $driver;

    public static function config(){
        return null;
    }

    public static function register() {
        register_shutdown_function('nb\Exception::shutdown');
        set_exception_handler('nb\Exception::exception');
        set_error_handler('nb\Exception::error');
    }

    public static function shutdown() {
        ///e('run shutdown');
        self::driver()->shutdown();
    }

    public static function exception($e) {
        //e('run exception');
        self::driver()->exception($e);
    }

    public static function error($error,$errstr, $errfile=null, $errline=null, $errcontext=null) {
        //e($error,$errstr, $errfile, $errline, $errcontext);
        //e('run error');
        self::driver()->error($error,$errstr, $errfile, $errline, $errcontext);
    }

    /**
     * Returns if error is one of fatal type.
     *
     * @param array $error error got from error_get_last()
     * @return bool if error is one of fatal type
     */
    public static function isFatalError($error) {
        return isset($error['type']) && in_array($error['type'], [
                E_ERROR,
                E_PARSE,
                E_CORE_ERROR,
                E_CORE_WARNING,
                E_COMPILE_ERROR,
                E_COMPILE_WARNING,
                self::E_HHVM_FATAL_ERROR
            ]);
    }


}