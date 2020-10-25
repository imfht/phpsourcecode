<?php
use config\constants\WorkerTypes;
use system\services\SrvType;
/**
 * worker配置
 *
 * 任务名
 * defaultJob - 默认任务队列，主要处理一些小任务
 */
return [
    "pid" => WORKER_PROJECT_PATH .'/runtime/log/workerlog/workerServer.pid',
    "log" => WORKER_PROJECT_PATH .'/runtime/log/workerlog/workerServer.log',
    //队列驱动名
    "driver" => "redis",
    //进程运行角色
    "user"   => 'root',
    //任务名前缀
    "jobNamePrefix" => '',
    //每个worker最大空闲时间（超过则退出，出让队列进程数，值守进程不受此限制）
    "maxFreeTime" => 300,
    //消息积压点（队列消息积压超过此值，将尝试逐步增加worker数量，不超过threadNum的值）
    "msgBacklogPoint" => 180,
    //worker配置
    "workerConf" => [
//      'jobName' => [
//           //队列属性设置项
//    		"option" => [],
//           //true则预先消费消息，worker获取消息后立即删除消息, false则任务执行返回为真才会删除消息
//           "preConsume" => false,
//           //当前任务并发执行的worker数量
//    		"threadNum" => 10,
//            //每个worker生存时间, 超时则重启
//    		"lifeTime" => 3600,
//            //每个worker最大任务处理数，超过则重启
//    		"maxHandleNum" =>  10000,
//            //每个worker最大空闲时间（设置会覆盖服务设置的默认值，超过则退出，出让队列进程数，值守进程不受此限制）
//          "maxFreeTime" => 300,
//            //消息积压点（设置会覆盖服务设置的默认值，队列消息积压超过此值，将尝试逐步增加worker数量，不超过threadNum的值）
//          "msgBacklogPoint" => 180,
//       ],
        'defaultJob' => [
            'option' => [],
            "preConsume" => false,
            "threadNum" => 20,
            "lifeTime" => 3600,
            "maxHandleNum" => 10000,
            "msgBacklogPoint" => 20,
        ],

    ],
    //worker任务配置
    "workers" => [
//    	WorkerType  => [
//    	    //任务名, 任务名相同则共用同一个消息队列
//    		"jobName" => "defaultJob",
//            //任务处理器, 格式[SrvType, '方法名']
//    		"handler"    => [SrvType::COMMON_TEST, 'test'],
//            //任务描述信息
//          "desc"  => '描述信息',
//            //不重复消息（发送消息不允许重复）
//          "msgUnique" => true,
//    	],
    	WorkerTypes::COMMON_TEST  => [
    	    //任务名, 任务名相同则共用同一个消息队列
    		"jobName" => "defaultJob",
            //任务处理器, 格式[SrvType, '方法名']
    		"handler"    => [SrvType::COMMON_TEST, 'test'],
            //任务描述信息
            "desc"  => '描述信息',
            //不重复消息（发送消息不允许重复）
            "msgUnique" => true,
    	],
    ]
];