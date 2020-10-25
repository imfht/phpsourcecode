<?php
namespace YesfTest\Cache;

class TestUtils {
	public static function single($that, $handler) {
		$arr = [1, 2, 3];
		$key = uniqid();
		$handler->set($key, $arr);
		$that->assertSame($arr, $handler->get($key));
		$that->assertNull($handler->get(uniqid()));
		$default = rand(1, 999);
		$that->assertSame($default, $handler->get(uniqid(), $default));
		$handler->delete($key);
		$that->assertNull($handler->get($key));
	}
	public static function multi($that, $handler) {
		$arr = [
			'key1' => 123,
			'key2' => "string",
			'key3' => [1, 2, 3]
		];
		$handler->setMultiple($arr);
		$that->assertSame($arr['key1'], $handler->get('key1'));
		$that->assertSame($arr['key2'], $handler->get('key2'));
		$that->assertSame($arr['key3'], $handler->get('key3'));
		$that->assertEquals($arr, $handler->getMultiple(array_keys($arr)));
		$arr['key1'] = rand(1, 999);
		$handler->set('key1', $arr['key1']);
		$that->assertEquals($arr, $handler->getMultiple(array_keys($arr)));
		$that->assertEquals([
			'not_exists' => -1,
			'key1' => $arr['key1'],
			'not_exists_2' => -1
		], $handler->getMultiple(['not_exists', 'key1', 'not_exists_2'], -1));
		$handler->deleteMultiple(['key1', 'key2']);
		$that->assertEquals([
			'key1' => -1,
			'key2' => -1,
			'key3' => $arr['key3']
		], $handler->getMultiple(['key1', 'key2', 'key3'], -1));
	}
	public static function clear($that, $handler) {
		$arr = [
			'key1' => 123,
			'key2' => "string",
			'key3' => [1, 2, 3]
		];
		$handler->setMultiple($arr);
		$handler->clear();
		$that->assertNull($handler->get('key1'));
		$that->assertNull($handler->get('key2'));
		$that->assertNull($handler->get('key3'));
	}
}