<?php

namespace Madphp\Db;

class MongoFactory extends Factory
{

    public static $modality = 'read';

    public static $instances = array();

    public function __construct()
    {

    }

    public function createDb($dbname, $config = array())
    {

        if (isset($config['modality']) && in_array($config['modality'], array('write', 'read'))) {
            $modality = $config['modality'];
        } else {
            $modality = self::$modality;
        }

        $instance = $modality . '_' . $dbname;
        if (!isset(self::$instances[$instance])) {
            $class = __NAMESPACE__ . "\\Mongo\\MongoCore";
            if (class_exists($class)) {
                try {
                    self::$instances[$instance] = new $class($dbname, $config);
                } catch (\Exception $exc) {
                    throw new \Exception("$class can not new!");
                }
            } else {
                throw new \Exception("$class not found!");
            }
        }

        return self::$instances[$instance];
    }
}
