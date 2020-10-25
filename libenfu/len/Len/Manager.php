<?php

class Manager
{
    /**
     * @var array
     */
    static $pool;

    /**
     * @return bool|mixed
     * @throws Exception
     */
    public static function pool()
    {
        $args = func_get_args();
        if (empty($args[0])) {
            throw new \Exception('pool $class_name empty');
        }
        $class_name = $args[0];
        $handler = self::getPool($class_name);
        if (false !== $handler) {
            return $handler;
        }

        $handler = call_user_func_array(['\Manager', 'getDynamic'], $args);

        self::addPool($class_name, $handler);

        return $handler;
    }

    /**
     * @return object
     */
    public static function getDynamic()
    {
        $args = func_get_args();
        $class_name = array_shift($args);
        $gen = new \ReflectionClass($class_name);

        return $gen->newInstanceArgs($args);
    }

    private static function addPool($handler_name ,$handler)
    {
        self::$pool[$handler_name] = $handler;
    }

    private static function getPool($handler_name)
    {
        if (empty(self::$pool[$handler_name])) {
            return false;
        }

        return self::$pool[$handler_name];
    }

}