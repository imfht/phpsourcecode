<?php
namespace CigoAdminLib\Lib\JPush\jpush\Exceptions;

class JPushException extends \Exception {

    function __construct($message) {
        parent::__construct($message);
    }
}
