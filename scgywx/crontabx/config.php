<?php
return array(
	'pidfile' => '/tmp/crontab.pid',
	'logfile' => '/tmp/crontab.log',
	'loglevel' => 2,
	'daemonize' => false,
	'crontab' => array(
		array(//test(守护进程)
			'name' => 'test',
			'script' => 'daemon/test.php',
			'childs' => 1,
			'daemon' => 1,
		),
		array(//test2(每分钟运行一次)
			'name' => 'test2',
			'script' => 'crontab/test2.php',
			'wakeup' => '* * * * *',
		),
		array(//test3(每五秒运行一次)
			'name' => 'test3',
			'script' => 'crontab/test3.php',
			'wakeup' => '*/5 * * * * *',
		),
		array(//test4(每天凌晨0点0分运行)
			'name' => 'test4',
			'script' => 'crontab/test4.php',
			'wakeup' => '0 0 * * *',
		),
		array(//test5(1-30内，每分钟运行一次)
			'name' => 'test5',
			'script' => 'crontab/test5.php',
			'wakeup' => '1-30 * * * *',
		),
	)
);