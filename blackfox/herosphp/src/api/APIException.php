<?php
/**
 * api 异常类
 * -------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2017-03-27 v2.0.0
 */
namespace herosphp\api;

use herosphp\exception\HeroException;

class APIException extends HeroException {

    public function __construct($code, $message ) {
        parent::__construct($message, $code);
    }

}