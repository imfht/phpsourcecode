<?php
use Workerman\Worker;
use Workerman\WebServer;
use Workerman\Lib\Timer;

// composer 的 autoload 文件
include __DIR__ . '/vendor/autoload.php';

function http_get($url) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_TIMEOUT, 500);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_URL, $url);

	$res = curl_exec($curl);
	if ($res === false) {
		echo curl_error($curl);
	}
	curl_close($curl);
	return $res;
}

$task = new Worker();
// 开启多少个进程运行定时任务，注意多进程并发问题
$task -> onWorkerStart = function($task) {

	$time_interval = 10;
	\Workerman\Lib\Timer::add($time_interval, function() {
		$url="http://xiaowei.test.com/index.php?m=&c=public&a=recevie_mail";
		echo http_get($url);
		echo 'running';
	});
};

// 运行所有的服务
Worker::runAll();
