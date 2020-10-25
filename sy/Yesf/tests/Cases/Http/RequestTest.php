<?php
namespace YesfTest\Http;

use PHPUnit\Framework\TestCase;
use Yesf\Yesf;
use Yesf\Http\Request;
use YesfApp\Http\FakeRequest;

class RequestTest extends TestCase {
	private static $fake_req;
	public static function setUpBeforeClass() {
		self::$fake_req = new FakeRequest;
	}
	public function testRequest() {
		$req = clone self::$fake_req;
		$req->raw_content = uniqid();
		$request = new Request($req);
		$this->assertSame($req->raw_content, $request->rawContent());
		$id = uniqid();
		$this->assertFalse(isset($request->test));
		$request->test = $id;
		$this->assertTrue(isset($request->test));
		$this->assertSame($id, $request->test);
		unset($request->test);
		$this->assertFalse(isset($request->test));
		$this->assertEquals('test', $request->get['action']);
		$this->assertNull($request->hahaha);
	}
	public function testHook() {
		Request::hook('user', function($req) {
			return $req->get['user'];
		});
		$req = clone self::$fake_req;
		$id = uniqid();
		$req->get['user'] = $id;
		$request = new Request($req);
		$this->assertSame($id, $request->user);
	}
	public function testSession() {
		$id = uniqid();
		$req = clone self::$fake_req;
		$req->cookie['testsessid'] = $id;
		$request = new Request($req);
		$request->setCookieHandler([$this, 'handleCookie']);
		$session = $request->session();
		$this->assertNull($session->get('key'));
		// Save session
		$session->set('key', $id);
		$request->end();
		unset($req, $request, $session);
		// Get session
		$req = clone self::$fake_req;
		$req->cookie['testsessid'] = $id;
		$request = new Request($req);
		$request->setCookieHandler([$this, 'handleCookie']);
		$session = $request->session();
		$this->assertSame($id, $session->get('key'));
		// Clear
		$session->clear();
		$this->assertNull($session->get('key'));
	}
	public function handleCookie($name, $value, $expire, $path) {
		//
	}
}