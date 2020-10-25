<?php
/**
 * 任务类，负责消息封装，pop、push消息队列
 * Created by PhpStorm.
 * User: zsw
 * Date: 2018/4/2
 * Time: 12:49
 */

namespace Bjask;

use Bjask\Queue\QueueHandlerInterface;

class Task implements \ArrayAccess, \Serializable
{
    public $fields = [
        'controller' => ['typeof' => 'string', 'value' => null],//带命名空间的类
        'action' => ['typeof' => 'string', 'value' => null],
        'extras' => ['typeof' => 'array', 'value' => []]
    ];//限定字段
    public $topicName = '';
    private $queue = null;
    private $logger = null;
    private $connect = null;

    public function __construct(QueueHandlerInterface $queue, Logger $logger)
    {
        $this->queue = $queue;
        $this->logger = $logger;
    }

    /**
     * 从队列中弹出一个任务执行
     */
    public function run()
    {
        try {
            if (is_null($this->connect)) {
                throw new \Exception('队列连接丢失');
            }
            $msg = $this->connect->pop();
            $task = unserialize($msg);
            $controller = $task['controller'];
            $action = $task['action'];
            $extras = $task['extras'];
            if (!class_exists($controller)) {
                throw new \Exception(sprintf('Class "%s" does not exist.', $controller));
            }
            $class = new $controller;
            $user_func = [$class, $action . 'Action'];
            if (!is_callable($user_func)) {
                throw new \Exception(sprintf('The method  "%s" is not callable.', $action));
            }
            $reflectionMethod = new \ReflectionMethod($class, 'setExtras');
            $reflectionMethod->invokeArgs($class, [$extras]);
            call_user_func($user_func);
        } catch (\Exception $e) {
            $this->logger->log($e->getMessage(), $this->logger::LEVEL_ERROR);
        } catch (\Throwable $e) {
            $this->logger->log($e->getMessage(), $this->logger::LEVEL_ERROR);
        }
    }

    /**
     * 增加一个任务到队列
     * @param string $msg
     * @return bool
     */
    public function add(string $msg)
    {
        try {
            if (is_null($this->connect)) {
                throw new \Exception('队列连接丢失');
            }
            return $this->connect->push($msg);
        } catch (\Exception $e) {
            $this->logger->log($e->getMessage(), $this->logger::LEVEL_ERROR);
        } catch (\Throwable $e) {
            $this->logger->log($e->getMessage(), $this->logger::LEVEL_ERROR);
        }
        return false;
    }

    /**
     * 返回队列长度
     * @return int
     */
    public function len()
    {
        $len = 0;
        try {
            if (is_null($this->connect)) {
                throw new \Exception('队列连接丢失');
            }
            $len = $this->connect->len();
        } catch (\Exception $e) {
            $this->logger->log($e->getMessage(), $this->logger::LEVEL_ERROR);
        } catch (\Throwable $e) {
            $this->logger->log($e->getMessage(), $this->logger::LEVEL_ERROR);
        }
        return $len;
    }


    /**
     * 获取队列连接
     * @param string $topic_name
     */
    public function openConnect(string $topic_name)
    {
        try {
            $this->topicName = $topic_name;
            $this->connect = $this->queue->createConnection($this->topicName);
        } catch (\Exception $e) {
            $this->logger->log($e->getMessage(), $this->logger::LEVEL_ERROR);
        } catch (\Throwable $e) {
            $this->logger->log($e->getMessage(), $this->logger::LEVEL_ERROR);
        }

    }

    /**
     * 关闭任务队列连接
     * @return bool
     */
    public function closeConnect()
    {
        $this->connect->isConnected() && $this->queue->close();
        return true;
    }

    public function serialize()
    {
        return serialize($this->fields);
    }

    public function unserialize($serialized)
    {
        $this->fields = unserialize($serialized);
    }

    final public function offsetExists($offset)
    {
        return isset($this->fields[$offset]);
    }

    final public function offsetGet($offset)
    {
        return isset($this->fields[$offset]) ? $this->fields[$offset]['value'] : null;
    }

    final public function offsetSet($offset, $value)
    {
        if (!array_key_exists($offset, $this->fields)) {
            throw new \Exception('非法字段');
        }
        settype($value, $this->fields[$offset]['typeof']);
        $this->fields[$offset]['value'] = $value;
    }

    final public function offsetUnset($offset)
    {
        isset($this->fields[$offset]) && ($this->fields[$offset]['value'] = null);
    }
}