<?php
/**
 * 单独实例化SphinxClient
 */
require dirname(__DIR__) . '/vendor/autoload.php';

use Yurun\Util\Swoole\SphinxClient;

go(function(){
	$client = new SphinxClient;
	// 改为你自己的连接配置
	$client->SetServer('192.168.0.110', 9312);
	// 改成你自己的搜索名和索引名
	var_dump($client->Query('query string', 'indexName'));
});