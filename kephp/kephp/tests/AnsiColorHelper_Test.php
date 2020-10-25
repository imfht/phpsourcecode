<?php

namespace tests;

use PHPUnit\Framework\TestCase;

class AnsiColorHelper_Test extends TestCase
{

	public function getAnsi()
	{
		return new \Ke\Helper\AnsiColorHelper();
	}

	public function testAnsiCodes()
	{
		$ansi = $this->getAnsi();
		echo $ansi->ansiStart('red'), 'ok', $ansi->ansiClose();
	}

	public function testWrap()
	{
		$ansi = $this->getAnsi();
		echo $ansi->wrap('Hello world', 'red|green:back|bold|underline');
		echo PHP_EOL, 'string';
	}
}
