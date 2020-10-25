<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Alpaca\Alpaca;

/**
 * 所有在Bootstrap类中, 以_init开头的方法, 都会被Alpaca调用,
 */
class Bootstrap
{
    //初始化数据库
    public function _initDatabase()
    {
        $config = Alpaca::app()->config;
        $capsule = new Capsule;
        $capsule->addConnection($config['db']);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }

    //定义异常处理，错误处理
    public function _initException()
    {
        function myException(\Exception $exception)
        {
            $result['code'] = '9900';
            $result['msg'] = "Exception:" . $exception->getMessage();
            Alpaca::app()->toJson($result);
            die();
        }

        function customError($no,$str)
        {
            $result['code'] = '9900';
            $result['msg'] =  "Error:[$no] $str";
            Alpaca::app()->toJson($result);
            die();
        }

        set_exception_handler('myException');
        set_error_handler("customError");
    }
}