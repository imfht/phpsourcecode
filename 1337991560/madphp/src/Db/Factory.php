<?php
/**
 * 工厂方法模式
 */

namespace Madphp\Db;

abstract class Factory
{
    abstract protected function createDb($type);

    public function create($dbname, $config = array(), $type = 'mysql')
    {
        $obj = $this->createDb($dbname, $config, $type);
        return $obj;
    }
}