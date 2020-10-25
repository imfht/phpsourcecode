<?php
namespace workerbase\classs\MQ;
/**
 *  消息队列服务接口
 * @author fukaiyao 2020-1-3 11:21:35
 */
interface IMessageServer
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
     * 获取消息
     * @param string $queueName     - 队列名
     * @param int $waitSeconds     - 无消息时阻塞等待时间
     * @return array|bool [ 'msgBody' => 消息体,'token' => 消息识别token，根据相关队列不同自定义]
     */
    public function receive($queueName, $waitSeconds=null);

    /**
     * 消息重试
     * @param $queueName      - 队列名
     * @param $token          - 消息获取的token(用于识别消息，根据相关队列不同自定义)
     * @return bool|false|mixed
     */
    public function retry($queueName, $token);

    /**
     * 删除消息
     * @param string $queueName     - 队列名
     * @param mixed $token  - 消息获取的token(用于识别消息，根据相关队列不同自定义)
     * @return bool 删除成功返回true, 失败返回false
     */
    public function delete($queueName, $token);

    /**
     * 获取队列消息总数
     * @param string $jobName
     * @return bool|false|int
     */
    public function getQueueSize($jobName);

    /**
     * 把消息发送给指定的worker执行
     * @param string $workerType - worker type
     * @param array $params - 任务参数
     * @return bool 成功返回true, 失败返回false
     * @throws \Exception
     * @throws \ReflectionException
     */
    public function dispatch($workerType, array $params = []);

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