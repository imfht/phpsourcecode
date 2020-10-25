<?php
/**
 * Class Not Found Exception
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2013-05 v1.0.0
 */

namespace herosphp\exception;

class ClassNotFoundException extends HeroException {

    public function __construct($message, $code)
    {
        $this->message = $message;
        $this->code = $code;
       // parent::__construct($message, $code);
    }
}
