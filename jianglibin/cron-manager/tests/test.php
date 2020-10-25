<?php

require __DIR__ . '/../vendor/autoload.php';

$manager = new SuperCronManager\CronManager();
$manager->workerNum = 10;

// 设置输出重定向,守护进程模式才生效
$manager->output = './test.log';

$manager->taskInterval('每个星期5凌晨运行一次', '0 * * * 5', function(){
	echo "每个星期5凌晨运行一次\n";
});

$manager->taskInterval('每天凌晨运行', '0 0 * * *', function(){
	echo "每天凌晨运行\n";
});

$manager->taskInterval('每秒运行一次', 's@1', function(){
	echo "每秒运行一次\n";
});
$manager->taskInterval('每秒运行一次', 's@1', function(){
	echo "每秒运行一次\n";
});

$manager->taskInterval('每分钟运行一次', 'i@1', function(){
	echo "每分钟运行一次\n";
});

$manager->taskInterval('每小时钟运行一次', 'h@1', function(){
	echo "每小时运行一次\n";
});

$manager->taskInterval('指定每天00:00点运行', 'at@00:00', function(){
	echo "指定每天00:00点运行\n";
});

$manager->taskInterval('测试多个进程运行同一个任务', '0 * * * 5', function($item){
	echo "$item 运行\n";
},[1,2,3,4,5]);

$manager->run();
