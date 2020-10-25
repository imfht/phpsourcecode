<?php

namespace tests\Cmd;

use Ke\Cli\Cmd\NewApp;
use PHPUnit\Framework\TestCase;

class NewApp_Test extends TestCase
{

	public function provideFilterAppDirs()
	{
		return [
			['hello/world/test', 'hello//world//////test'],
			['hello/world/test', '/hello/world/test/'],
		];
	}

	/**
	 * @dataProvider provideFilterAppDirs
	 */
	public function testFilterAppDir($expected, $dir)
	{
		$cmd = new NewApp(['', 'any_dir']);

		$this->assertEquals($expected, $cmd->filterAppDir($dir));
	}


	public function provideMakeNamespaceFromDir()
	{
		return [
			['Test', 'hello//world//////test'],
			['HelloWorld', '/hello_world'],
			['HelloWorld', '/hello_____world'],
			['HelloWorld', '/hello.world'],
			['HelloWorld', '/hello.___world'],
		];
	}

	/**
	 * @dataProvider provideMakeNamespaceFromDir
	 */
	public function testMakeNamespaceFromDir($expected, $dir)
	{
		$cmd = new NewApp(['', 'any_dir']);

		$this->assertEquals($expected, $cmd->makeAppNamespaceFromDir($dir));
	}

	public function testFilterNamespace()
	{
		$cmd = new NewApp(['', 'any_dir']);

		$this->assertEquals('AnyApp', $cmd->filterAppNamespace('/AnyApp'));
		$this->assertEquals('Kephp\\NewApp', $cmd->filterAppNamespace('Kephp\\NewApp'));
		$this->assertEquals('hello_world\\app', $cmd->filterAppNamespace('hello_world/app'));
	}

	public function testVerifyAppNamespace()
	{
		$cmd = new NewApp(['', 'any_dir']);

		$this->assertEquals(true, $cmd->verifyAppNamespace('AnyApp'));
		$this->assertEquals(true, $cmd->verifyAppNamespace('AnyApp\\Hello'), '允许多层namespace');
		$this->assertEquals(false, $cmd->verifyAppNamespace('1K\\App'), '不能以数字开头');
		$this->assertEquals(false, $cmd->verifyAppNamespace('AnyApp\\\\Test'), '不能包括多个\\');
	}
}
