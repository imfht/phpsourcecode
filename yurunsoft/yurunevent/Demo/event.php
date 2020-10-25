<?php
/**
 * 全局事件
 */
require dirname(__DIR__) . '/vendor/autoload.php';

use Yurun\Util\Event;

// 监听事件，事件名称叫test
Event::on('test', function($message, $arr){
	// 输出一下
	echo 'trigger test', PHP_EOL;
	echo 'message:', $message, PHP_EOL;
	// 数组和对象依然支持引用传值
	$arr['a'] = 'yurun';
});

$value = '123';
$a = '';

// 触发事件，事件名称叫test
Event::trigger('test', '666', ['a' => &$a]);
// 这里可以看到，a已经在事件回调里被改变了
echo 'a:', $a, PHP_EOL;

