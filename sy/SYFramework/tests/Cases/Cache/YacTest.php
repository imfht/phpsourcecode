<?php
namespace SyTest\Cache;

use PHPUnit\Framework\TestCase;
use Sy\Cache\Yac as SyYac;

class YacTest extends TestCase {
	/**
	 * @requires extension yac
	 */
	public function testYac() {
		$handler = new SyYac();
		TestUtils::single($this, $handler);
		TestUtils::multi($this, $handler);
		TestUtils::clear($this, $handler);
	}
}