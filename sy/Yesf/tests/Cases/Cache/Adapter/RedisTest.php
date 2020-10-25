<?php
namespace YesfTest\Cache\Adapter;

use PHPUnit\Framework\TestCase;
use Yesf\Yesf;
use Yesf\Connection\Pool;
use YesfTest\Cache\TestUtils;

class RedisTest extends TestCase {
	public function testRedis() {
		$handler = Pool::getAdapter('redis');
		TestUtils::single($this, $handler);
		TestUtils::multi($this, $handler);
		TestUtils::clear($this, $handler);
	}
}