<?php
/**
 * 使用连接池的性能测试脚本
 */
require dirname(__DIR__) . '/vendor/autoload.php';

use Yurun\Util\Swoole\SphinxClient;
use Yurun\Util\Swoole\SphinxPool;

/**
 * 查询方法
 *
 * @return void
 */
function query()
{
	SphinxPool::use(function($sphinxClient){
		// 改成你自己的搜索名和索引名
		$result = $sphinxClient->Query('query string', 'indexName');
			
		if($result)
		{
			var_dump($result['total']);
		}
		else
		{
			var_dump($sphinxClient->GetLastError());
		}
	});

}

$time1 = $time2 = 0;

go(function() use(&$time1, &$time2){
	// 初始化连接池，改为你自己的连接配置
	SphinxPool::init(5, '192.168.0.110', 9312);

	// 模拟同步测试
	$time1 = microtime(true);
	for($i = 0; $i < 10; ++$i)
	{
		query();
	}
	$time1 = microtime(true) - $time1;

	echo PHP_EOL;

	// 协程测试
	$time2 = microtime(true);
	for($i = 0; $i < 10; ++$i)
	{
		go('query');
	}
	
});

// 等待协程代码执行完毕
swoole_event_wait();

$time2 = microtime(true) - $time2;

echo PHP_EOL;
echo 'sync time:', $time1, 's', PHP_EOL;
echo 'co time:', $time2, 's', PHP_EOL;
