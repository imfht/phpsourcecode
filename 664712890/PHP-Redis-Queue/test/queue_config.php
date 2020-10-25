<?php

return array(
	/**
	 * 队列为空时等待时间, 单位秒
	 */
	'INTERVAL' => 10,
	
	/**
	 * 最大执行任务数, 执行MAX_TASK次后退出 
	 */
	'MAX_TASK' => 2000,
	
	/**
	 * 队列数量
	 */
	'QUEUE_COUNT' => 64,
	
	/**
	 * 运行master队列的命令
	 */
	'START_COMMAND'=> '/usr/bin/php '.Yii::app()->basePath.'/yiic.php queueV2 master',
	
	/**
	 * 日志目录
	 */
	'LOG_DIR' => '/data/logs/queue',
	
	/**
	 * worker处理类
	 * 放置于 queue/worker 下， 从CWorker继承
	 */
	'WORKER'=> 'CWorkerForYii',
	
	/**
	 * 使用的存储
	 * 放置于 queue/store 下， 从CStore继承
	 */
	'STORE'=> 'CFileStore',
	
	/**
	 * 例如给文件缓存等使用
	 */
	'STORE_DIR'=> '/data/logs/queue',
	
	/**
	 * worker 结束状态标识值
	 */
	'FINISH_STATE'=> -1,
	
	/**
	 * 队列信息存储的前缀，以便项目之间作区分等
	 */
	'STORE_PREEFIX'=> 'a',
	
	/**
	 * master进程ID存放的键 
	 */
	'STORE_MASTER_PID_KEY'=> 'master_pid',
);