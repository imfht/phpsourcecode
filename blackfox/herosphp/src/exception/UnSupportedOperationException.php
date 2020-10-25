<?php
/**
 * 不支持的操作异常
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2013-05 v1.0.0
 */

namespace herosphp\exception;

class UnSupportedOperationException extends HeroException {

    public function __contruct($message, $code) {
        parent::__contruct($message, $code);
    }

}
