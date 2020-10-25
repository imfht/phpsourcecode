<?php
namespace workerbase\classs\MQ\imps;

use workerbase\classs\Config;
use workerbase\classs\datalevels\Redis;
use workerbase\classs\Log;
use workerbase\classs\MQ\BaseMQ;
use workerbase\classs\MQ\IMQ;
use workerbase\classs\worker\WorkerMessage;

/**
 * 基于redis的消息队列
 * @author fukaiyao 2020-1-3 11:21:59
 */
class RedisMQ extends BaseMQ implements IMQ
{
    /**
     * @var RedisMQ
     */
    private static $_instance;

    private $_client;

    //队列名前缀
    protected $_mqPrefix = "wkRedisMQ:";

    public function __construct()
    {
        $this->_client = Redis::getInstance([], true);
    }

    /**
     * 获取消息服务
     * @param bool $isFlush 强制重新连接
     * @return RedisMQ
     */
    public static function getInstance($isFlush = false)
    {
        if (true == $isFlush || null == self::$_instance) {
            if ($isFlush) {
                return new RedisMQ();
            }
            self::$_instance = new RedisMQ();
        }
        return self::$_instance;
    }

    /**
     * 清除连接实例
     * @access public
     * @return void
     */
    public static function clearInstance()
    {
        self::$_instance = null;
    }

    /**
     * 创建队列
     * @param string $queueName     - 队列名
     * @return bool 成功返回true, 失败返回false
     */
    public function createQueue($queueName)
    {
       return true;
    }

    /**
     * 设置队列属性
     * @param string $queueName     - 队列名
     * @param array $option
     * @return bool
     */
    public function setQueueAttributes($queueName, $option)
    {
       return true;
    }

    /**
     * 发送消息
     * @param string $queueName     - 队列名
     * @param string $msgBody       - 消息内容
     * @return bool
     * 成功返回true, 失败返回false
     */
    public function send($queueName, $msgBody)
    {
        if (empty($queueName)) {
            return false;
        }

        try{
            $res = $this->_client->lPush($queueName, $msgBody);
            if (!$res) {
                Log::error("send message failure. queue={$queueName}");
                return false;
            }
        }
        catch (\RedisException $e) {
            Log::error("redis send failure, queue={$queueName}, error=". $e->getMessage() . "[" . $e->getFile() . ':' . $e->getLine() . "]");
            //redis连接错误，不抛异常
            return 0;
        }
        catch (\Exception $e) {
            Log::error("send message failure. queue={$queueName}, error=". $e->getMessage() . "[" . $e->getFile() . ':' . $e->getLine() . "]");
            return false;
        }
        return true;
    }


    /**
     * 发送消息(消息不允许重复)
     * @param string $queueName     - 队列名
     * @param string $msgBody       - 消息内容
     * @return bool
     * 成功返回true, 失败返回false
     */
    public function uniqueSend($queueName, $msgBody)
    {
        if (empty($queueName)) {
            return false;
        }

        try{
            $script = '
                if redis.call("LREM", KEYS[1], ARGV[2], ARGV[1]) then
                    return redis.call("LPUSH", KEYS[1],  ARGV[1])
                else
                    return 0
                end
            ';
            if ($this->_client->getOriginInstance()->getOption(\Redis::OPT_SERIALIZER)) {
                $argv = serialize($msgBody);
            } else {
                $argv = $msgBody;
            }
            $res = $this->_client->getOriginInstance()->eval($script, [$queueName, $argv, 0], 1);
            if (!$res) {
                Log::error("send unique message failure. queue={$queueName}");
                return false;
            }
        }
        catch (\RedisException $e) {
            Log::error("redis unique send failure, queue={$queueName}, error=". $e->getMessage() . "[" . $e->getFile() . ':' . $e->getLine() . "]");
            //redis连接错误，不抛异常
            return 0;//0表示连接错误
        }
        catch (\Exception $e) {
            Log::error("send unique message failure. queue={$queueName}, error=". $e->getMessage() . "[" . $e->getFile() . ':' . $e->getLine() . "]");
            return false;
        }
        return true;
    }

    /**
     * 获取消息
     * @param string $queueName     - 队列名
     * @param int $waitSeconds     - 无消息时阻塞等待时间
     * @return array|bool [ 'msgBody' => 消息体,'token' => 消息识别token]
     */
    public function receive($queueName, $waitSeconds=null)
    {
        try {
            if (is_null($waitSeconds)) {
                $msgBody = $this->_client->rpoplpush($queueName, $queueName.'-bak');
            } else {
                $msgBody = $this->_client->brpoplpush($queueName, $queueName.'-bak', $waitSeconds);
            }

            //回复协议
            if ($msgBody && in_array(substr($msgBody, 0, 1), ['+', ':', '-', '$', '*'])) {
                return false;
            }

            if (!$msgBody) {
                if (in_array(Config::read('env_flag'), ['dev', 'local_debug'])) { //没有消息
                    Log::info("receive message failure. queue={$queueName}");
                }
                return false;
            }

            //替换备份队列的时间戳为消息获取时间
            $workerMsg = new WorkerMessage($msgBody);
            $useNum = $workerMsg->getUseNum();
            $workerMsg->setTimestamp(time());
            $workerMsg->setUseNum($useNum-1);
            $newMsgBody = $workerMsg->serialize();

            //插入一条新备份消息
            $script = '
                if redis.call("LREM", KEYS[1], 0, ARGV[1]) then
                    return redis.call("LPUSH", KEYS[1], ARGV[2])
                else
                    return 0
                end
            ';
            if ($this->_client->getOriginInstance()->getOption(\Redis::OPT_SERIALIZER)) {
                $argv1 = serialize($msgBody);
                $argv2 = serialize($newMsgBody);
            } else {
                $argv1 = $msgBody;
                $argv2 = $newMsgBody;
            }
            $res = $this->_client->getOriginInstance()->eval($script, [$queueName.'-bak', $argv1, $argv2], 1);
            if (!$res) {
                //备份队列替换失败，休息100毫秒再尝试替换
                usleep(100*1000);
                $res = $this->_client->getOriginInstance()->eval($script, [$queueName.'-bak', $argv1, $argv2], 1);
            }
            if ($res) {
                $msgBody = $newMsgBody;
            }

            unset($workerMsg, $newMsgBody);
        }
        catch (\RedisException $e) {
            Log::error("redis receive failure, queue={$queueName}, error=". $e->getMessage() . "[" . $e->getFile() . ':' . $e->getLine() . "]");
            //redis连接错误，不抛异常
            return 0;//0表示连接错误
        }
        catch (\Exception $e) {
            Log::error("error receive message failure. queue={$queueName}, error=". $e->getMessage() . "[" . $e->getFile() . ':' . $e->getLine() . "]");
            return false;
        }

        return array(
            'msgBody' => $msgBody,
        );
    }

    /**
     * 消息重试
     * @param $queueName      - 队列名
     * @param $token          - 消息获取的token
     * @return bool|false|mixed
     */
    public function retry($queueName, $token)
    {
        return true;
    }

    /**
     * 获取队列消息总数
     * @param string $jobName
     * @return bool|false|int
     */
    public function getQueueSize($jobName)
    {
        $queueName = $this->getQueueNameByJobName($jobName);
        try{
            return $this->_client->lSize($queueName);
        }
        catch (\RedisException $e) {
            Log::error("redis get queue size failure, queue={$queueName}, error=". $e->getMessage() . "[" . $e->getFile() . ':' . $e->getLine() . "]");
            //redis连接错误，不抛异常
            return -1;//-1表示连接错误
        }
        catch (\Exception $e) {
            Log::error("get queue size failure. queue={$queueName}, error=". $e->getMessage() . "[" . $e->getFile() . ':' . $e->getLine() . "]");
            return false;
        }
    }

    /**
     * 删除消息
     * @param string $queueName     - 队列名
     * @param mixed $token  - 消息获取的token或者value(用于识别消息，根据相关队列不同自定义)
     * @return bool 删除成功返回true, 失败返回false
     */
    public function delete($queueName, $token)
    {
        if (empty($token)) {
            return false;
        }

        try{
            return $this->_client->lRem($queueName.'-bak', $token, 0);
        }
        catch (\RedisException $e) {
            Log::error("redis delete failure, queue={$queueName}:".json_encode($token).", error=". $e->getMessage() . "[" . $e->getFile() . ':' . $e->getLine() . "]");
            //redis连接错误，不抛异常
            return 0;//0表示连接错误
        }
        catch (\Exception $e) {
            Log::error("delete message failure. queue={$queueName}:".json_encode($token).", error=". $e->getMessage() . "[" . $e->getFile() . ':' . $e->getLine() . "]");
            return false;
        }
    }

    /**
     * 检查一条备份队列消息
     * @param $queueName      - 队列名
     * @return bool|false|mixed
     */
    public function bakQueueCheck($queueName)
    {
        if (empty($queueName)) {
            return false;
        }

        try{
            $msgBody = $this->_client->rpoplpush($queueName . '-bak', $queueName . '-bak');
            if (!$msgBody) {
                return false;
            }

            //回复协议
            if ($msgBody && in_array(substr($msgBody, 0, 1), ['+', ':', '-', '$', '*'])) {
                return false;
            }

            $workerMsg = new WorkerMessage($msgBody);
            $timestamp = $workerMsg->getTimestamp();
            $useNum = $workerMsg->getUseNum();
            $date = $workerMsg->getDate();

            //重复消费20次，删除
            if ($useNum > 20) {
                $res = $this->delete($queueName, $msgBody);
                if (!$res && $res === false) {
                    Log::error("overuse delete message failure. queue={$queueName}:".json_encode($msgBody));
                    //备份队列删除失败，休息100毫秒再尝试删除
                    usleep(100*1000);
                    $res = $this->delete($queueName, $msgBody);
                }

                if (!$res && $res === 0) {
                    throw new \RedisException('redis connnect false');
                }
                Log::error("overuse delete message success. queue={$queueName}:".json_encode($msgBody));
                return true;
            }

            //十分钟没消费重回队列（1.消息10分钟没处理完或失败，2.积压的消息并发获取和检查）
            if ($timestamp > 1 && (time() - $timestamp) > 600) {
                $workerMsg->setTimestamp(1);
                $workerMsg->setDate(date('Y-m-d'));
                $res = $this->send($queueName, $workerMsg->serialize());
                if ($res) {
                    $res = $this->delete($queueName, $msgBody);
                    if ($res === 0) {
                        self::clearInstance();
                        $res = self::getInstance()->delete($queueName, $msgBody);
                    }

                    if (false === $res) {
                        Log::error("retry delete message failure. queue={$queueName}:".json_encode($msgBody));
                        //备份队列删除失败，休息100毫秒再尝试删除
                        usleep(100*1000);
                        $res = $this->delete($queueName, $msgBody);
                    }
                } elseif(!$res && $res === 0) {
                    throw new \RedisException('redis connnect false');
                }
            }
            elseif (1 == $timestamp && !empty($date) && (strtotime(date('Y-m-d').' 00:00:00') - strtotime($date.' 00:00:00')) > 864000) {
                //唯一消息类型
                //1、获取时，替换消息失败，并且删除失败，残留
                //2、当前执行10天前的消息
                $res = $this->delete($queueName, $msgBody);
            }

            unset($workerMsg);
        }
        catch (\RedisException $e) {
            Log::error("check redis failure, queue={$queueName}, error=". $e->getMessage() . "[" . $e->getFile() . ':' . $e->getLine() . "]");
            self::clearInstance();
            return false;
        }
        catch (\Exception $e) {
            Log::error("check message failure, queue={$queueName}, error=". $e->getMessage() . "[" . $e->getFile() . ':' . $e->getLine() . "]");
            return false;
        }

        return true;
    }

}