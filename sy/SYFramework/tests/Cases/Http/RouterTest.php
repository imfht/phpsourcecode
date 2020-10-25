<?php
namespace SyTest\Http;

use PHPUnit\Framework\TestCase;
use Sy\App;
use Sy\Http\Router;
use Sy\Http\Request;

class RouterTest extends TestCase {
	private $request;
	public static function setUpBeforeClass() {
		Router::init();
	}
	public function setUp() {
		Router::from(serialize([]));
		Router::enableMap();Router::setPrefix('');
		$this->request = new Request();
	}
	public function testMap() {
		$this->request->server['REQUEST_METHOD'] = 'get';
		$this->request->server['REQUEST_URI'] = '/ap/foo';
		Router::parse($this->request);
		$this->assertEquals('ap', $this->request->controller);
		$this->assertEquals('foo', $this->request->action);
		$this->request->server['REQUEST_METHOD'] = 'get';
		$this->request->server['REQUEST_URI'] = '/ap/foo/bar';
		Router::parse($this->request);
		$this->assertEquals('ap', $this->request->module);
		$this->assertEquals('foo', $this->request->controller);
		$this->assertEquals('bar', $this->request->action);
	}
	public function testNoParam() {
		$this->request->server['REQUEST_METHOD'] = 'GET';
		$this->request->server['REQUEST_URI'] = '/user/list';
		Router::get('/user/list', 'index.user.list');
		Router::disableMap();
		Router::parse($this->request);
		$this->assertEquals('index', $this->request->module);
		$this->assertEquals('user', $this->request->controller);
		$this->assertEquals('list', $this->request->action);
	}
	public function testParamCorrect() {
		$this->request->server['REQUEST_METHOD'] = 'GET';
		$this->request->server['REQUEST_URI'] = '/user/123';
		Router::get('/user/{id}', [
			'module' => 'index',
			'controller' => 'user',
			'action' => 'view'
		], [
			'id' => '(\d+)'
		]);
		Router::disableMap();
		Router::parse($this->request);
		$this->assertEquals('index', $this->request->module);
		$this->assertEquals('user', $this->request->controller);
		$this->assertEquals('view', $this->request->action);
		$this->assertEquals('123', $this->request->param['id']);
	}
	public function testParamIncorrect() {
		$this->request->server['REQUEST_METHOD'] = 'GET';
		$this->request->server['REQUEST_URI'] = '/user/someone';
		Router::get('/user/{id}', 'index.user.view', [
			'id' => '(\d+)'
		]);
		Router::disableMap();
		Router::parse($this->request);
		$this->assertNull($this->request->module);
		$this->assertNull($this->request->controller);
		$this->assertNull($this->request->action);
	}
	public function testMethod() {
		$this->request->server['REQUEST_METHOD'] = 'PUT';
		$this->request->server['REQUEST_URI'] = '/user/123';
		Router::get('/user/{id}', 'user.view');
		Router::put('/user/{id}', 'user.update');
		Router::disableMap();
		Router::parse($this->request);
		$this->assertEquals('update', $this->request->action);
	}
	public function testAny() {
		$this->request->server['REQUEST_METHOD'] = 'GET';
		$this->request->server['REQUEST_URI'] = '/user/someone';
		Router::get('/user/{id}', [
			'module' => 'index',
			'controller' => 'user',
			'action' => 'update'
		], [
			'id' => '(\d+)'
		]);
		Router::any('/user/{id}', [
			'module' => 'index',
			'controller' => 'user',
			'action' => 'view'
		]);
		Router::disableMap();
		Router::parse($this->request);
		$this->assertEquals('view', $this->request->action);
	}
	public function testClosure() {
		$this->request->server['REQUEST_METHOD'] = 'GET';
		$this->request->server['REQUEST_URI'] = '/user/123/view';
		Router::get('/user/{id}/{action}', function($param) {
			return [
				'module' => 'index',
				'controller' => 'user',
				'action' => $param['action']
			];
		}, [
			'id' => '(\d+)'
		]);
		Router::disableMap();
		Router::parse($this->request);
		$this->assertEquals('123', $this->request->param['id']);
		$this->assertEquals('view', $this->request->action);
	}
	public function testEmpty() {
		$this->request->server['REQUEST_METHOD'] = 'GET';
		$this->request->server['REQUEST_URI'] = '/';
		Router::parse($this->request);
		$this->assertEquals('index', $this->request->module);
		$this->assertEquals('index', $this->request->controller);
		$this->assertEquals('index', $this->request->action);
	}
	public function testOther() {
		Router::setPrefix('/api');
		$this->request->server['REQUEST_METHOD'] = 'GET';
		$this->request->server['REQUEST_URI'] = '/api/user/view.html?id=1';
		Router::parse($this->request);
		$this->assertEquals('index', $this->request->module);
		$this->assertEquals('user', $this->request->controller);
		$this->assertEquals('view', $this->request->action);
		$this->assertEquals('html', $this->request->extension);
	}
}