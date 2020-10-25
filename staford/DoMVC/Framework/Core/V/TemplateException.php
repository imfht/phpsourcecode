<?php

/**
 * 异常处理
 * 
 * 约定：根据异常代码生成对应错误等级
 * 代码  =>  错误等级  =>颜色
 *  0    =>  notice(提示)    => black
 *  1    =>  warning(警告)   => yellow
 *  2    =>  fatal(致命)     => red
 * @author 暮雨秋晨
 * @copyright 2014
 */

class TemplateException extends Exception
{
    public function __construct($msg, $code, $line = 0, $path = '')
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
        $msg = '[<font color="' . $color . '">Template' . $tag . '</font>] ' . $msg;
        $this->message = $msg;
        if ($line != 0) {
            $this->line = $line + 1;
        }
        if (!empty($path)) {
            $this->file = $path;
        }
    }

    public function __toString()
    {
        echo ('Framework\'s template exception handler class.');
    }
}

?>