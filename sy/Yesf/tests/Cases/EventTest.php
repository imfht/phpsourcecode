<?php
namespace YesfTest;

use PHPUnit\Framework\TestCase;
use Yesf\Event;

class EventTest extends TestCase {
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
		Event::clear('test');
		Event::listen('test', [__CLASS__, 'callback2']);
		$this->assertEquals('_t__test_data_', Event::trigger('test', ['_test_data_']));
		$this->assertEquals(null, Event::trigger('none', ['_test_data_']));
	}
	public function testSeveralPlugin() {
		Event::clear('test');
		Event::listen('test', [__CLASS__, 'callback2']);
		Event::listen('test', [__CLASS__, 'callback1']);
		$this->assertEquals('_t__test_data_', Event::trigger('test', ['_test_data_']));
		$this->assertEquals(0, self::$isTrigger);
		Event::clear('test');
		Event::listen('test', [__CLASS__, 'callback1']);
		Event::listen('test', [__CLASS__, 'callback2']);
		$this->assertEquals('_t__test_data_', Event::trigger('test', ['_test_data_']));
		$this->assertEquals(1, self::$isTrigger);
	}
	public function testClear() {
		Event::listen('test', [__CLASS__, 'callback2']);
		Event::listen('test', [__CLASS__, 'callback1']);
		$this->assertEquals('_t__test_data_', Event::trigger('test', ['_test_data_']));
		Event::clear();
		$this->assertEquals(null, Event::trigger('test', ['_test_data_']));
		Event::listen('test', [__CLASS__, 'callback2']);
		$this->assertEquals('_t__test_data_', Event::trigger('test', ['_test_data_']));
		Event::clear('test');
		$this->assertEquals(null, Event::trigger('test', ['_test_data_']));
	}
}