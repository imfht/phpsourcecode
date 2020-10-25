<?php
namespace workerbase\classs\MQ\imps;

use workerbase\classs\Config;
use workerbase\classs\MQ\IMessageServer;
use workerbase\classs\ServiceFactory;
use workerbase\classs\worker\WorkerMessage;

/**
 * 消息队列服务
 * @author fukaiyao 2020-1-3
 */
class MessageServer implements IMessageServer
{
    /**
     * @var MessageServer
     */
    private static $_instance;

    /**
     * 消息队列驱动
     */
    private $_driver;

    /**
     * 重新连接标记
     */
    private $_reconnect = false;

    /**
     * MessageServer constructor.
     * @param $driverName -驱动名
     * @throws \Exception
     */
    public function __construct($driverName)
    {
        switch ($driverName) {
            case 'redis':
                $this->_driver = RedisMQ::getInstance(true);
                break;
            default:
                throw new \Exception('暂不支持该队列驱动！');
                break;
        }
    }

    /**
     * 获取消息服务
     * @param string $driverName 驱动名
     * @param bool $isFlush 强制重新连接
     * @return bool|MessageServer
     */
    public static function getInstance($driverName = 'redis', $isFlush = false)
    {
        if (is_null($driverName)) {
            //驱动名
            $driverName = Config::read('mq_driver');
            if (empty($driverName)) {
                return false;
            }
        }

        if (true == $isFlush) {
            return new MessageServer($driverName);
        }

        if (!isset(self::$_instance[$driverName])) {
            self::$_instance[$driverName] = new MessageServer($driverName);
        }
        return self::$_instance[$driverName];
    }

    /**
     * 清除连接实例
     * @access public
     * @return void
     */
    public static function clearInstance()
    {
        self::$_instance = [];
    }

    /**
     * 设置重连接标记
     * @access public
     * @return $this
     */
    public function setReconnect()
    {
        $this->_reconnect = true;
        return $this;
    }

    /**
     * 创建队列
     * @param string $queueName     - 队列名
     *
     * @return bool 成功返回true, 失败返回false
     */
    public function createQueue($queueName)
    {
        return $this->_driver->createQueue($queueName);
    }

    /**
     * 设置队列属性
     * @param string $queueName     - 队列名
     * @param array $option
     * @return bool
     */
    public function setQueueAttributes($queueName, $option)
    {
        return $this->_driver->setQueueAttributes($queueName, $option);
    }

    /**
     * 发送消息
     * @param string $queueName     - 队列名
     * @param string $msgBody       - 消息内容
     * @return bool 成功返回true, 失败返回false
     * @throws \Exception
     */
    public function send($queueName, $msgBody)
    {
        $res = $this->_driver->send($queueName, $msgBody);
        if ($res === 0 && !$this->_reconnect) {
            self::clearInstance();
            return self::getInstance()->setReconnect()->send($queueName, $msgBody);
        } else {
            $this->_reconnect = false;
            return $res?$res:false;
        }
    }

    /**
     * 发送不重复消息
     * @param string $queueName     - 队列名
     * @param string $msgBody       - 消息内容
     * @return bool 成功返回true, 失败返回false
     * @throws \Exception
     */
    public function uniqueSend($queueName, $msgBody)
    {
        $res = $this->_driver->uniqueSend($queueName, $msgBody);
        if ($res === 0 && !$this->_reconnect) {
            self::clearInstance();
            return self::getInstance()->setReconnect()->send($queueName, $msgBody);
        } else {
            $this->_reconnect = false;
            return $res?$res:false;
        }
    }

    /**
     * 获取消息
     * @param string $queueName     - 队列名
     * @param int $waitSeconds     - 无消息时阻塞等待时间
     * @return array|bool [ 'msgBody' => 消息体]
     * @throws \Exception
     */
    public function receive($queueName, $waitSeconds=null)
    {
        $res = $this->_driver->receive($queueName, $waitSeconds);
        if ($res === 0 && !$this->_reconnect) {
            self::clearInstance();
            return self::getInstance()->setReconnect()->receive($queueName, $waitSeconds);
        } else {
            $this->_reconnect = false;
            return $res?$res:false;
        }
    }

    /**
     * 消息重试
     * @param $queueName      - 队列名
     * @param $token          - 消息获取的token(用于识别消息，根据相关队列不同自定义)
     * @return bool|false|mixed
     */
    public function retry($queueName, $token)
    {
        return $this->_driver->retry($queueName, $token);
    }

    /**
     * 删除消息
     * @param string $queueName     - 队列名
     * @param mixed $value  - 消息获取的value(用于识别消息，根据相关队列不同自定义)
     * @return bool 删除成功返回true, 失败返回false
     * @throws \Exception
     */
    public function delete($queueName, $value)
    {
        $res = $this->_driver->delete($queueName, $value);
        if ($res === 0 && !$this->_reconnect) {
            self::clearInstance();
            return self::getInstance()->setReconnect()->delete($queueName, $value);
        } else {
            $this->_reconnect = false;
            return $res?$res:false;
        }
    }

    /**
     * 获取队列消息总数
     * @param string $jobName
     * @return bool|false|int
     */
    public function getQueueSize($jobName)
    {
        $res = $this->_driver->getQueueSize($jobName);
        if (-1 === $res && !$this->_reconnect) {
            self::clearInstance();
            $res = self::getInstance()->setReconnect()->getQueueSize($jobName);
            if (-1 === $res) {
                return false;
            }
            return $res;
        } else {
            $this->_reconnect = false;
            return $res;
        }
    }

    /**
     * 把消息发送给指定的worker执行
     * @param string $workerType - worker type
     * @param array $params - 任务参数
     * @return bool 成功返回true, 失败返回false
     * @throws \Exception
     * @throws \ReflectionException
     */
    public function dispatch($workerType, array $params = [])
    {
        try {
            $workerConfig = Config::read("workers.{$workerType}", 'worker');
        } catch (\Exception $e) {
            throw new \Exception("worker config not found, workerType={$workerType}");
        }

        //dev环境不走消息队列, 直接调用消息处理器
        if (Config::read("env") == 'dev') {
            if (!isset($workerConfig['handler']) || empty($workerConfig['handler'])) {
                throw new \Exception("worker config invalid, workerType={$workerType}");
            }

            $handler = $workerConfig['handler'];

            $srvObj = ServiceFactory::getService($handler[0]);//单例实例化类
            $ret = call_user_func_array([$srvObj, $handler[1]], $params);
            if (!empty($ret)) {
                return true;
            }
            return false;
        }

        $env = Config::read("env");
        $queueName = $this->getQueueNameByWorkerType($workerType, $env);
        if (empty($queueName)) {
            return false;
        }

        $msg = new WorkerMessage();
        $msg->setWorkerType($workerType);
        $msg->setParams($params);

        if (isset($workerConfig['msgUnique']) && $workerConfig['msgUnique']) {
            $msg->setTimestamp(1);
            $msg->setId(1);
            $ret = $this->uniqueSend($queueName, $msg->serialize());
        } else {
            $ret = $this->send($queueName, $msg->serialize());
        }
        unset($msg);
        if ($ret !== false) {
            return true;
        }
        return false;
    }

    /**
     * 根据worker type获取队列名
     * @param string $workerType        - worker type
     * @param string $env               - 环境
     * @return bool|string 成功返回队列名, 失败返回false
     */
    public function getQueueNameByWorkerType($workerType, $env = '')
    {
        return $this->_driver->getQueueNameByWorkerType($workerType, $env);
    }

    /**
     * 根据jobName获取队列名
     * @param string $jobName
     * @param string $env   - 环境名
     * @return string
     */
    public function getQueueNameByJobName($jobName, $env = '')
    {
        return $this->_driver->getQueueNameByJobName($jobName, $env);
    }

}