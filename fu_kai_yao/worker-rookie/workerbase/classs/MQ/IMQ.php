<?php
namespace workerbase\classs\MQ;

/**
 *  消息队列驱动接口
 * @author fukaiyao
 */
interface IMQ
{
    /**
     * 创建队列
     * @param string $queueName     - 队列名
     *
     * @return bool 成功返回true, 失败返回false
     */
    public function createQueue($queueName);

    /**
     * 设置队列属性
     * @param string $queueName     - 队列名
     * @param array $option
     * @return bool
     */
    public function setQueueAttributes($queueName, $option);

    /**
     * 发送消息
     * @param string $queueName     - 队列名
     * @param string $msgBody       - 消息内容
     * @return bool
     * 成功返回true, 失败返回false
     */
    public function send($queueName, $msgBody);


    /**
     * 发送不重复消息
     * @param string $queueName     - 队列名
     * @param string $msgBody       - 消息内容
     * @return bool 成功返回true, 失败返回false
     * @throws \Exception
     */
    public function uniqueSend($queueName, $msgBody);

    /**
     * 获取消息
     * @param string $queueName     - 队列名
     * @param int $waitSeconds     - 无消息时阻塞等待时间
     * @return array|bool [ 'msgBody' => 消息体,'token' => 消息识别token，根据相关队列不同自定义]
     */
    public function receive($queueName, $waitSeconds=null);

    /**
     * 消息重试
     * @param $queueName      - 队列名
     * @param $value          - 消息获取的value(用于识别消息，根据相关队列不同自定义)
     * @return bool|false|mixed
     */
    public function retry($queueName, $value);

    /**
     * 删除消息
     * @param string $queueName     - 队列名
     * @param mixed $value  - 消息获取的value(用于识别消息，根据相关队列不同自定义)
     * @return bool 删除成功返回true, 失败返回false
     */
    public function delete($queueName, $value);

    /**
     * 获取队列消息总数
     * @param string $jobName
     * @return bool|false|int
     */
    public function getQueueSize($jobName);

    /**
     * 根据worker type获取队列名
     * @param string $workerType        - worker type
     * @param string $env               - 环境
     * @return bool|string 成功返回队列名, 失败返回false
     */
    public function getQueueNameByWorkerType($workerType, $env = '');

    /**
     * 根据jobName获取队列名
     * @param string $jobName
     * @param string $env   - 环境名
     * @return string
     */
    public function getQueueNameByJobName($jobName, $env = '');
}