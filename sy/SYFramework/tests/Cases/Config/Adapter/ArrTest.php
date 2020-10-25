<?php
namespace SyTest\Config\Adapter;

use PHPUnit\Framework\TestCase;
use Sy\App;
use Sy\Config\Adapter\Arr;

class ArrTest extends TestCase {
	public function testAll() {
		$env = App::getEnv();
		App::setEnv('gce');
		// Load
		$config = Arr::fromIniFile(APP_PATH . 'Config/config.ini');
		$this->assertEquals('gce', $config->get('connection.my.user'));
		$this->assertEquals('/product', $config->get('path2'));
		$this->assertEquals('/base', $config->get('path'));
		$this->assertEquals([
			'host' => 'localhost',
			'port' => '3306',
			'user' => 'gce',
			'password' => 'gce'
		], $config->get('connection.my'));
		$this->assertEquals('localhost', $config->get('connection.my.host'));
		// Finish
		App::setEnv($env);
	}
}