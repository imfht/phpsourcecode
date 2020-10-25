<?php

/**
 * @author 暮雨秋晨
 * @copyright 2014
 */

class ModelException extends Exception
{
    public function __construct($msg, $code)
    {
        parent::__construct($msg, $code);
        switch ($code) {
            case 0:
                $tag = 'Notice';
                $color = 'black';
                break;
            case 1:
                $tag = 'Warning';
                $color = 'yellow';
                break;
            case 2:
                $tag = 'Fatal';
                $color = 'red';
                break;
            default:
                $tag = 'Notice';
                $color = 'black';
        }
        $this->message = '[<font color="' . $color . '">Model' . $tag . '</font>] ' . $msg;
        return true;
    }
}

?>