<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 2/18/14
 * Time: 5:21 PM
 */

namespace Api\Exception;
use Think\Exception;

class ReturnException extends Exception {
    private $result;

    public function __construct($return) {
        $this->result = $return;
    }

    public function getResult() {
        return $this->result;
    }
}