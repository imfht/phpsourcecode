<?php declare (strict_types = 1);
namespace msqphp\core\traits;

trait Call
{
    public function __call(string $method, array $args)
    {
        static $methods = [];

        if (!isset($methods[$method])) {
            $file_path = \msqphp\Environment::getVenderFilePath(__CLASS__, $method, 'methods');
            if ($file_path === null) {
                throw new TraitsException(__CLASS__ . '类的扩展方法' . $method . '不存在');
            }
            $methods[$method] = require $file_path;
        }

        return call_user_func_array($methods[$method], $args);
    }
}
