<?php
return [
	'ip' => "0.0.0.0",
	'port' => 9501,
	'pid' => "/tmp",
	'advanced' => [
		'daemonize' => 0,
		'dispatch_mode' => 2, 
		'package_max_length' => 2097152, //1024 * 1024 * 2
		'buffer_output_size' => 3145728, //1024 * 1024 * 3
		'pipe_buffer_size' => 33554432, //1024 * 1024 * 32
		'open_tcp_nodelay' => 1,
		'heartbeat_check_interval' => 5, //心跳检测
		'heartbeat_idle_time' => 11, //心跳检测
		'open_cpu_affinity' => 1, //CPU亲和
		'reactor_num' => 2, //reactor线程数，建议设置为CPU核数 x 2
		'worker_num' => 4, //守护进程数，详情见http://wiki.swoole.com/wiki/page/275.html
		'task_worker_num' => 2, //Task进程数，详情见http://wiki.swoole.com/wiki/page/276.html
		'max_request' => 0, 
		'task_max_request' => 4000, 
		'backlog' => 3000, 
		'log_file' => "/tmp/sw_server.log", //swoole系统日志，任何代码内echo都会在这里输出
		'task_tmpdir' => "/tmp/swtasktmp/" //task投递内容过长时，会临时保存在这里
	]
];