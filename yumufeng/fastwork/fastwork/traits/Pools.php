<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/4
 * Time: 23:46
 */

namespace traits;


use fastwork\exception\MethodNotFoundException;
use fastwork\exception\PoolsNotAvailableException;
use Swoole\Coroutine\Channel;

trait Pools
{
    /**
     * @var Channel
     */
    protected $pool;
    /**
     * 入池时间
     * @var
     */
    protected $pushTime = '';
    //新建时间
    protected $addPoolTime = '';
    //池状态
    protected $available = true;

    public function init($config)
    {
        if ($config['clearAll'] < $config['clearTime']) {
            $config['clearAll'] = $config['clearTime'];
        }
        $this->config = array_merge($this->config, $config);
        $this->pool = new Channel($this->config['poolMax']);
    }

    /**
     * @入池
     *
     * @param $pools
     */
    public function push($pools)
    {
        //未超出池最大值时
        if ($this->pool->length() < $this->config['poolMax']) {
            $this->pool->push($pools);
        }
        if (!method_exists($this, 'createPool')) {
            throw  new MethodNotFoundException('createPool is not found');
        }
        $this->pushTime = time();
    }

    /**
     * @出池
     * @param null $create
     * @return bool|mixed
     */
    public function pop($create = null)
    {
        $re_i = -1;
        back:
        $re_i++;
        if (!$this->available) {
            throw new PoolsNotAvailableException('Redis连接池正在销毁');
            return false;
        }
        //有空闲连接且连接池处于可用状态
        if ($this->pool->length() > 0 && $create == null) {
            $pools = $this->pool->pop();
        } else {
            $pools = $this->createPool();
            $this->addPoolTime = time();
        }
        if ($pools->connected === true) {
            return $pools;
        } else {
            if ($re_i <= $this->config['reconnect']) {
                $pools->close();
                unset($pools);
                goto back;
            }
        }
    }

    /**
     * @定时器
     *
     * @param $server
     */
    public function clearTimer(\swoole_server $server)
    {

        $server->tick($this->config['clearTime'] * 1000, function () use ($server) {
            if ($this->pool->length() > $this->config['poolMin'] && time() - 10 > $this->addPoolTime) {
                $this->pool->pop();
            }
            if ($this->pool->length() > 0 && time() - $this->config['clearAll'] > $this->pushTime) {
                while (!$this->pool->isEmpty()) {
                    if ($this->pool->length() <= $this->config['poolMin']) {
                        break;
                    }
                    $this->pool->pop();
                }
            }
        });
    }

    /**
     * @return Channel
     */
    public function getPool(): Channel
    {
        return $this->pool;
    }

}