<?php
namespace SyTest;

use PHPUnit\Framework\TestCase;

class ConsoleTest extends TestCase {
	public function testConsole() {
		$this->assertEquals(1, $GLOBALS['is_console_run']);
	}
}