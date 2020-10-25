<?php
/**
 * Created by Wenlong Li
 * User: wenlong
 * Date: 2018/9/26
 * Time: 上午11:54
 */

namespace Component\Orm\Pool;


use Component\Orm\Connection\AsyncMysql;
use Component\Orm\Connection\Mongodb;
use Component\Orm\Connection\Mysql;
use Kernel\AgileCore;
use Kernel\Core\IComponent\IConnection;

trait PoolTrait
{
    protected $driver = 'pdo';
    protected $pool;
    protected $config = [];
    protected $num = 0;
    public function poolInit()
    {
        $core = AgileCore::getInstance();
        $this->config = $core->getConfig('pool')[$this->driver];
        for ($i = 0; $i < $this->config['start']; $i++) {
            $this->addConnection($this->initConnection());
        }
        $this->num = $this->config['start'];
    }

    public function setDriver(string $driver)
    {
        $this->driver = $driver;
    }

    public function addConnection(IConnection $connection)
    {
        /** @var $pool \SplQueue */
        $pool = $this->getPool();
        $pool->push($connection);
    }

    public function free(IConnection $connection)
    {
        $this->addConnection($connection);
    }

    public function get() :IConnection
    {
        $pool = $this->getPool();
        if($pool->count() > 0) {
            return $pool->pop();
        }
        if($this->num <= $this->config['max']) {
            $this->addConnection($this->initConnection());
            $this->num ++;
            return $this->get();
        }else{
            throw new \Exception('Request too busy!');
        }
    }

    public function getPool() : \SplQueue
    {
        if(!($this->pool instanceof \SplQueue)) {
            $this->pool = new \SplQueue();
        }
        return $this->pool;
    }

    public function initConnection() :IConnection
    {
        $config = AgileCore::getInstance()->get('config');
        switch ($this->driver) {
            case 'pdo':
                $obj = new Mysql($config);
                break;
            case 'async':
                $obj = new AsyncMysql($config);
                break;
            case 'mongo':
                $obj = new Mongodb($config);
                break;
            default:
                throw new \Exception('db driver not found!');
        }
        return $obj;
    }
}