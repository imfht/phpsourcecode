<?php declare (strict_types = 1);
namespace msqphp\core\traits;

trait Get
{
    public function __get(string $property)
    {
        static $gets = [];

        if (!isset($gets[$property])) {
            $file_path = \msqphp\Environment::getVenderFilePath(__CLASS__, $property, 'gets');
            if ($file_path === null) {
                throw new TraitsException(__CLASS__ . '类的' . $property . '属性不存在');
            }
            $gets[$property] = require $file_path;
        }

        return $gets[$property];
    }
}
