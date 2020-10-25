<?php
namespace SyTest\Cache;

use PHPUnit\Framework\TestCase;
use Sy\Cache\Memcached as SyMemcached;

class MemcachedTest extends TestCase {
	public function testMemcached() {
		$handler = new SyMemcached();
		TestUtils::single($this, $handler);
		TestUtils::multi($this, $handler);
		TestUtils::clear($this, $handler);
	}
}