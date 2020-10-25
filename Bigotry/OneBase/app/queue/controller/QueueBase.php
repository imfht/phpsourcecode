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

use app\common\controller\ControllerBase;
use think\queue\Job;

/**
 * 队列模块基类控制器
 */
class QueueBase extends ControllerBase
{
    
    /**
     * 构造方法
     */
    public function __construct()
    {
        
        // 执行父类构造方法
        parent::__construct();
    }
    
    /**
     * fire是消息队列默认调用的方法
     * @param Job $job 当前的任务对象
     * @param array|mixed $data 发布任务时自定义的数据
    */
    public function fire(Job $job, $data)
    {
        // 有效消息到达消费者时可能已经不再需要执行了
        if(!$this->checkJob($data)){
            $job->delete();
            exit;
        }
        
        // 执行业务处理
        if($this->doJob($data)){
            
            $job->delete();// 任务执行成功后删除
            
        }else{
            // 检查任务重试次数
            if($job->attempts() > 3){
                
                $job->delete();
            }
        }
    }
}
