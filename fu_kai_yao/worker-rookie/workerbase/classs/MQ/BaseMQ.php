<?php
namespace workerbase\classs\MQ;

use workerbase\classs\Log;

/**
 * 消息队列服务基础类
 * @author fukaiyao 2020-1-3 11:21:59
 */
abstract class BaseMQ
{
    //队列名前缀
    protected $_mqPrefix = "";

    /**
     * 根据worker type获取队列名
     * @param string $workerType        - worker type
     * @param string $env               - 环境
     * @return bool|string 成功返回队列名, 失败返回false
     */
    public function getQueueNameByWorkerType($workerType, $env = '')
    {
        if (empty($workerType)) {
            return false;
        }

        if (empty($env)) {
            $env = loadc('config')->get("env");
        }

        //获取worker配置
        $config = loadc('config')->get("workers.{$workerType}", 'worker');
        if (empty($config)) {
            Log::error("worker config not found. workerType={$workerType}");
            return false;
        }

        $prefix = loadc('config')->get('jobNamePrefix', 'worker');
        $name = $this->_mqPrefix . "{$env}-Worker-{$prefix}{$config['jobName']}";
        return $name;
    }

    /**
     * 根据jobName获取队列名
     * @param string $jobName
     * @param string $env   - 环境名
     * @return string
     */
    public function getQueueNameByJobName($jobName, $env = '')
    {
        if (empty($env)) {
            $env = loadc('config')->get("env");
        }
        $prefix = loadc('config')->get('jobNamePrefix', 'worker');
        $name = $this->_mqPrefix . "{$env}-Worker-{$prefix}{$jobName}";
        return $name;
    }

    /**
     * 发送消息
     * @param string $queueName     - 队列名
     * @param string $msgBody       - 消息内容
     * @return bool
     * 成功返回true, 失败返回false
     */
    abstract public function send($queueName, $msgBody);
}