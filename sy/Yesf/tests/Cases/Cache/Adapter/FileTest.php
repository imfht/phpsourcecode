<?php
namespace YesfTest\Cache\Adapter;

use PHPUnit\Framework\TestCase;
use Yesf\Yesf;
use Yesf\Cache\Adapter\File as YesfFile;
use YesfTest\Cache\TestUtils;

class FileTest extends TestCase {
	public function testFile() {
		$handler = new YesfFile();
		TestUtils::single($this, $handler);
		TestUtils::multi($this, $handler);
		TestUtils::clear($this, $handler);
	}
}