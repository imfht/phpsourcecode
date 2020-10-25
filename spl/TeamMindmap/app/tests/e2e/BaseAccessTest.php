<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 14-10-27
 * Time: 下午7:48
 */

/**
 * Class BaseAccessTest
 *
 * 对一些外部可访问的页面的可访问性进行测试
 */
class BaseAccessTest extends TestCase{

	public function testIndex(){
		$this->accessPageRight('/');
	}

	public function testGuide(){
		$this->accessPageRight('/guide');
	}

	public function testLogin(){
		$this->accessPageRight('/authority/login');
	}

	public function testSign(){
		$this->accessPageRight('/authority/signin');
	}

	private function accessPageRight($uri){
		$crawler = $this->client->request('get', $uri);
		$this->assertResponseOk();

		return $crawler;
	}
}