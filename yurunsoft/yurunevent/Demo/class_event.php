<?php
/**
 * 在一个类里使用事件
 */
require dirname(__DIR__) . '/vendor/autoload.php';

use Yurun\Util\ClassEvent;

// 先定义个类
class Test
{
	// 必须use一下
	use ClassEvent;

	private $value;

	public function setValue($value)
	{
		$this->value = $value;
		// 触发事件changeValue
		$this->trigger('changeValue', $value, [
			'message'	=>	&$message
		]);
		// 被回调里修改了值
		echo 'message:', $message, PHP_EOL;
	}
}

// 实例化测试
$test = new Test;
// 监听changeValue事件
$test->on('changeValue', function($value, $data){
	echo 'changeValue1:', PHP_EOL;
	echo 'value:', $value, PHP_EOL;
	echo 'data:', PHP_EOL;
	var_dump($data);
	// 数组和对象引用传值修改
	$data['message'] = 'six six six';
});
// 赋值
$test->setValue(123);