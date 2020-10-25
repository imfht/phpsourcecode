<?php
/**
 * HerosPHP 框架异常处理基类
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2013-05 v1.0.0
 */

namespace herosphp\exception;

class HeroException extends \Exception {

    public function __construct( $message, $code ){
        parent::__construct($message, $code);
    }

    /**
     * toString方法，用来记录错误日志
     * @return string
     */
    public function toString() {
        return parent::__toString();
    }
}
