<?php
namespace apps\console;

use workerbase\classs\Config;
use workerbase\classs\MQ\imps\RedisMQ;

class MqCompensatorCommand
{

    //redisMQ队列检查
    public function queueCompensator()
    {
        $conf = Config::read("", "worker");

        $count = 50;//检查50条消息
        while ($count--) {
            foreach ($conf['workerConf'] as $jobName => $workerConfig) {
                try{
                    //获取根据环境拼接后的队列名称
                    $queueName = RedisMQ::getInstance()->getQueueNameByJobName($jobName);
                    if (empty($queueName)) {
                        continue;
                    }

                    //检查备份队列消息
                    $res = RedisMQ::getInstance()->bakQueueCheck($queueName);
                }catch (\Exception $e) {
                    continue;
                }
            }
        }

        return true;
    }

}