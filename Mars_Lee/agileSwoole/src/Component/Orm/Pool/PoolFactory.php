<?php
/**
 * Created by Wenlong Li
 * User: wenlong
 * Date: 2018/9/26
 * Time: 下午12:33
 */

namespace Component\Orm\Pool;


use Component\Orm\Connection\Mongodb;
use Kernel\AgileCore;
use Kernel\Core\IComponent\IConnection;

class PoolFactory
{
    public $pool = [];

    public function __construct()
    {
        $drivers = AgileCore::getInstance()->getConfig('pool');
        foreach ($drivers as $name=>$v) {
            $pool = new ConnectionPool();
            $pool->setDriver($name);
            $pool->init();
            $this->pool[$name] = $pool;
        }
    }

    public function getPool(string $name) {
        return $this->pool[$name];
    }

    public function getConnection(string $name) {
        return $this->pool[$name]->getConnection();
    }

    public function free(IConnection $connection)
    {
        if($connection instanceof \Swoole\Coroutine\Mysql) {
            $name = 'async';
            $this->pool[$name]->free($connection);
        }
        if($connection instanceof \PDO) {
            $name = 'pdo';
            $this->pool[$name]->free($connection);
        }
        if($connection instanceof Mongodb) {
            $name = 'mongo';
            $this->pool[$name]->free($connection);
        }
    }


}