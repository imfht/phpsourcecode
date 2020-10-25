<?php
namespace YesfTest\Http;

use PHPUnit\Framework\TestCase;
use Yesf\Yesf;
use Yesf\Http\Response;
use YesfApp\Http\CustomEngine;
use YesfApp\Http\FakeResponse;

class ResponseTest extends TestCase {
	private static $fake_resp;
	public static function setUpBeforeClass() {
		self::$fake_resp = new FakeResponse;
		Response::init();
		// Response::initInWorker();
	}
	public function testDefaultTemplate() {
		$resp = clone self::$fake_resp;
		$response = new Response($resp);
		$response->setTemplate('Index');
		$id1 = uniqid();
		$response->assign('id', $id1);
		$response->clearAssign();
		$id2 = uniqid();
		$response->assign('id', $id2);
		unset($response);
		$expected_result = "<p>$id2</p>";
		$this->assertEquals($expected_result, $resp->content);
	}
	public function testAbsTemplate() {
		$resp = clone self::$fake_resp;
		$response = new Response($resp);
		$response->setTemplate('Other');
		$response->disableView();
		$id = uniqid();
		$response->assign('id', $id);
		$response->display(APP_PATH . 'View/Index.phtml', true);
		unset($response);
		$expected_result = "<p>$id</p>";
		$this->assertEquals($expected_result, $resp->content);
	}
	public function testCustomEngine() {
		$resp = clone self::$fake_resp;
		$response = new Response($resp);
		$response->setTemplate('Custom');
		$response->setCurrentTemplateEngine(CustomEngine::class);
		$prefix = uniqid();
		$response->getTemplate()->setPrefix($prefix);
		$id = uniqid();
		$response->assign('id', $id);
		unset($response);
		$expected_result = "<p>$prefix$id</p>";
		$this->assertEquals($expected_result, $resp->content);
	}
	public function testSendFile() {
		$path = APP_PATH . 'test_sendfile.zip';
		$send_size = 1024; //1KB
		$max_offset = filesize($path) - $send_size;
		$start_offset = rand(0, $max_offset);
		$resp = clone self::$fake_resp;
		$response = new Response($resp);
		$response->sendfile($path, $start_offset, $send_size);
		unset($response);
		$expected_result = file_get_contents($path, false, null, $start_offset, $send_size);
		$this->assertEquals($expected_result, $resp->content);
	}
	public function testHeader() {
		$resp = clone self::$fake_resp;
		$response = new Response($resp);
		$response->disableView();
		$location_to = '/' . uniqid() . '.html';
		$response->header('Location', $location_to);
		$response->status(302);
		unset($response);
		$this->assertEquals(302, $resp->status);
		$this->assertEquals($location_to, $resp->headers['Location']);
	}
	/*
	public function testCookie() {
		$resp = clone $this->fake_resp;
		$response = new Response($resp);
		$response->disableView();
		$cookie1 = uniqid();
		$response->cookie([
			'name' => 'cookie1',
			'value' => $cookie1
		]);
	}
	*/
}