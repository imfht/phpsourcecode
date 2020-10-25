<?php
namespace SyTest\Cache;

use PHPUnit\Framework\TestCase;
use Sy\Cache\Redis as SyRedis;

class RedisTest extends TestCase {
	public function testRedis() {
		$handler = new SyRedis();
		TestUtils::single($this, $handler);
		TestUtils::multi($this, $handler);
		TestUtils::clear($this, $handler);
	}
}