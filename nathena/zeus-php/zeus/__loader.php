<?php
/**
 * Created by IntelliJ IDEA.
 * User: nathena
 * Date: 15/8/5
 * Time: 10:22
 */
//this is the  zeus framework implicit expression namespace
namespace _this_is_the_zeus_framework_implicit_expression_namespace_;

use zeus\Log;

function __forName($class)
{
    if( class_exists($class,false) || interface_exists($class,false) )
    {
        return;
    }

    require ( zRealpath($class.'.php') );
}

//set_exception_handler&set_error_handler
function __exception_handler($exception, $message = NULL, $file = NULL, $line = NULL)
{
    $PHP_ERROR = (func_num_args() === 5);

    if($PHP_ERROR AND (error_reporting() & $exception) === 0)
        return;

    if ($PHP_ERROR)
    {
        $code     = $exception;
        $type     = 'PHP Error';

        $message  = $type.'  '.$message.'  '.$file.'  '.$line;
    }
    else
    {
        $code     = $exception->getCode();
        $type     = get_class($exception);
        $message  = $exception->getMessage()."\n".$exception->getTraceAsString();
        $file     = $exception->getFile();
        $line     = $exception->getLine();
    }

    Log::error($type, $code, $message, $file, $line);

    if( !DEBUG )
    {
        $_file = tpl("error");
        if (file_exists($_file))
        {
            ob_end_clean();
            include $_file;
        }
        else
        {
            redirect('/',$message);
        }
    }
    else
    {
        $str = '<style>body {font-size:12px;}</style>';
        $str .= '<h1>操作失败！</h1><br />';
        $str .= '<strong>错误信息：<strong><font color="red">' . $message . '</font><br />';

        echo $str;
    }

    exit($code);
}

//启动自动加载
spl_autoload_register(__NAMESPACE__.'\__forName');
//异常处理
set_error_handler(__NAMESPACE__.'\__exception_handler');
set_exception_handler(__NAMESPACE__.'\__exception_handler');