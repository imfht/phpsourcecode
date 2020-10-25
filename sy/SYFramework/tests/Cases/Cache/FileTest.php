<?php
namespace SyTest\Cache;

use PHPUnit\Framework\TestCase;
use Sy\Cache\File;

class FileTest extends TestCase {
	public function testFile() {
		$handler = new File();
		TestUtils::single($this, $handler);
		TestUtils::multi($this, $handler);
		TestUtils::clear($this, $handler);
	}
}