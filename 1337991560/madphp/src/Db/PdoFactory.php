<?php

namespace Madphp\Db;

class PdoFactory extends Factory
{

    public static $instances = array();

    public function __construct()
    {

    }

    public function createDb($dbname, $config = array(), $type = 'mysql')
    {
        $instance = 'pdo_' . $type . '_' . $dbname . '_';
        $classPrefix = '\Pdo\\Engine\\';
        if (isset(self::$instances[$instance])) {
            return self::$instances[$instance];
        }

        $class = __NAMESPACE__ . $classPrefix . ucfirst(strtolower($type));
        if (!class_exists($class)) {
            throw new \Exception("$class not found!");
        }

        try {
            self::$instances[$instance] = new $class($dbname, $config);
        } catch (\Exception $exc) {
            throw new \Exception("$class can not new!");
        }
        return self::$instances[$instance];
    }
}
