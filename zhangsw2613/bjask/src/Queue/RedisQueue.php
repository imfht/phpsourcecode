<?php
/**
 * redis队列类
 * Created by PhpStorm.
 * User: zsw
 * Date: 2018/3/28
 * Time: 13:12
 */

namespace Bjask\Queue;


class RedisQueue implements QueueHandlerInterface
{

    private $redis = null;
    private $listKey = '';
    private $config = [];

    public function __construct(array $config)
    {
        $this->config['host'] = $config['host'];
        $this->config['port'] = $config['port'];
        $this->config['password'] = $config['password'];
    }

    /**
     * 创建连接
     * @param string $topic_name
     * @return $this
     */
    public function createConnection(string $topic_name)
    {
        $this->redis = new \Redis();
        $this->redis->connect($this->config['host'],$this->config['port']);
        if(!empty($this->config['password'])){
            $this->redis->auth($this->config['password']);
        }
        $this->listKey = $topic_name;

        return $this;
    }

    /**
     * 推入一个消息到队列
     * @param string $messgae
     * @return mixed
     */
    public function push(string $messgae)
    {
        return $this->redis->rPush($this->listKey,$messgae);
    }

    /**
     * 从队列中取出一个消息
     * @return mixed
     */
    public function pop()
    {
        return $this->redis->lPop($this->listKey);
    }

    /**
     * 返回当前队列长度
     * @return mixed
     */
    public function len()
    {
        return $this->redis->lLen($this->listKey);
    }

    /**
     * 返回当前连接状态
     * @return bool
     */
    public function isConnected()
    {
        try{
            return $this->redis->ping() == '+PONG' || false;
        } catch (\Exception $e) {
            return false;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * 关闭连接
     */
    public function close()
    {
        $this->redis->close();
    }
}
