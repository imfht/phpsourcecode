<?php
namespace SyTest;

use PHPUnit\Framework\TestCase;
use Sy\Plugin;

class PluginTest extends TestCase {
	public static $isTrigger = 0;
	public static function callback1($data) {
		self::$isTrigger = 1;
		return null;
	}
	public static function callback2($data) {
		return '_t_' . $data;
	}
	public static function errorCallback($data = '') {
		throw new Exception('Test Exception');
	}
	public function testOnePlugin() {
		Plugin::clear('test');
		Plugin::register('test', [__CLASS__, 'callback2']);
		$this->assertEquals('_t__test_data_', Plugin::trigger('test', ['_test_data_']));
		$this->assertEquals(null, Plugin::trigger('none', ['_test_data_']));
	}
	public function testSeveralPlugin() {
		Plugin::clear('test');
		Plugin::register('test', [__CLASS__, 'callback2']);
		Plugin::register('test', [__CLASS__, 'callback1']);
		$this->assertEquals('_t__test_data_', Plugin::trigger('test', ['_test_data_']));
		$this->assertEquals(0, self::$isTrigger);
		Plugin::clear('test');
		Plugin::register('test', [__CLASS__, 'callback1']);
		Plugin::register('test', [__CLASS__, 'callback2']);
		$this->assertEquals('_t__test_data_', Plugin::trigger('test', ['_test_data_']));
		$this->assertEquals(1, self::$isTrigger);
	}
	public function testClear() {
		Plugin::register('test', [__CLASS__, 'callback2']);
		Plugin::register('test', [__CLASS__, 'callback1']);
		$this->assertEquals('_t__test_data_', Plugin::trigger('test', ['_test_data_']));
		Plugin::clear();
		$this->assertEquals(null, Plugin::trigger('test', ['_test_data_']));
		Plugin::register('test', [__CLASS__, 'callback2']);
		$this->assertEquals('_t__test_data_', Plugin::trigger('test', ['_test_data_']));
		Plugin::clear('test');
		$this->assertEquals(null, Plugin::trigger('test', ['_test_data_']));
	}
}