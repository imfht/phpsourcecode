<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | Repository | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

namespace app\queue\controller;

use app\queue\logic\TestLogic;

/**
 * 消费者类
 * 用于处理队列中的任务
*/
class Test extends QueueBase
{
    
    /**
     * 消息在到达消费者时可能已经不需要执行了
     * @param array|mixed $data 发布任务时自定义的数据
     * @return boolean 任务执行的结果
     */
    public function checkJob($data)
    {
        
        return true;
    }
    
    /**
     * 根据消息中的数据进行实际的业务处理
    */
    public function doJob($data)
    {
        
        // 实际业务流程处理
        return (new TestLogic())->testHandle($data);
    }
}