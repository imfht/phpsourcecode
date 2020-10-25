<?php
namespace CigoAdminLib\Lib\JPush\jpush\Exceptions;

class APIConnectionException extends JPushException {

    function __toString() {
        return "\n" . __CLASS__ . " -- {$this->message} \n";
    }
}
