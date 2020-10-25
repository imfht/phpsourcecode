<?php

namespace tests;

use Ke\DirectoryRegistry;
use PHPUnit\Framework\TestCase;

class Base_Test extends TestCase
{

	public function testRealPath()
	{
		$path = __DIR__ . '/tmp/test.txt';
		$file = real_path($path);
		$this->assertEquals($file, false);
		file_put_contents($path, 'a');
		$file = real_path($path, true); // 刷新 real_path 缓存
		$this->assertTrue(is_file($file));
		unlink($path);
	}

	public function testRealFile()
	{
		$path = __DIR__ . '/tmp/test.txt';
		$file = real_file($path);
		$this->assertEquals($file, false);
		file_put_contents($path, 'a');
		$file = real_file($path, true); // 刷新 real_path 缓存
		$this->assertTrue(is_file($file));
		unlink($path);
	}

	public function testRealDir()
	{
		$path = __DIR__ . '/tmp/a';
		$file = real_dir($path);
		$this->assertEquals($file, false);
		mkdir($path);
		$file = real_dir($path, true); // 刷新 real_path 缓存
		$this->assertTrue(is_dir($file));
		rmdir($path);
	}

	public function testDirectoryRegistrySeekRefresh()
	{
		$dir = new DirectoryRegistry();
		$dir->setExtension('txt');
		$dir->setDirs([
			'default' => [real_dir(__DIR__ . '/tmp'), 0],
		]);

		$path = __DIR__ . '/tmp/a.txt';
		$file = $dir->seek(null, 'a', false);
		$this->assertEquals($file, false);
		file_put_contents($path, 'a');
		$file = $dir->seek(null, 'a', false, true);
		$this->assertTrue(is_file($file));

		unlink($path);
	}
}
