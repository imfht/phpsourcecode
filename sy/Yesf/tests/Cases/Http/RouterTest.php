<?php
namespace YesfTest\Http;

use PHPUnit\Framework\TestCase;
use Yesf\Yesf;
use Yesf\Http\Router;
use Yesf\Http\Request;
use YesfApp\Http\FakeRequest;

class RouterTest extends TestCase {
	private $req;
	private $req_content;
	private $router;
	public function setUp() {
		$this->router = new Router;
		$this->req_content = new FakeRequest();
		$this->req = new Request($this->req_content);
	}
	public function testMap() {
		$this->router->enableMap();
		$this->req_content->server['request_method'] = 'get';
		$this->req_content->server['request_uri'] = '/ap/foo';
		$this->router->parse($this->req);
		$this->assertEquals('ap', $this->req->controller);
		$this->assertEquals('foo', $this->req->action);
		$this->req_content->server['request_method'] = 'get';
		$this->req_content->server['request_uri'] = '/ap/foo/bar';
		$this->router->parse($this->req);
		$this->assertEquals('ap', $this->req->module);
		$this->assertEquals('foo', $this->req->controller);
		$this->assertEquals('bar', $this->req->action);
	}
	public function testNoParam() {
		$this->req_content->server['request_method'] = 'get';
		$this->req_content->server['request_uri'] = '/user/list';
		$this->router->get('/user/list', 'index.user.list');
		$this->router->disableMap();
		$this->router->parse($this->req);
		$this->assertEquals('index', $this->req->module);
		$this->assertEquals('user', $this->req->controller);
		$this->assertEquals('list', $this->req->action);
	}
	public function testParamCorrect() {
		$this->req_content->server['request_method'] = 'get';
		$this->req_content->server['request_uri'] = '/user/123';
		$this->router->get('/user/{id}', [
			'module' => 'index',
			'controller' => 'user',
			'action' => 'view'
		], [
			'id' => '(\d+)'
		]);
		$this->router->disableMap();
		$this->router->parse($this->req);
		$this->assertEquals('index', $this->req->module);
		$this->assertEquals('user', $this->req->controller);
		$this->assertEquals('view', $this->req->action);
		$this->assertEquals('123', $this->req->param['id']);
	}
	public function testParamIncorrect() {
		$this->req_content->server['request_method'] = 'get';
		$this->req_content->server['request_uri'] = '/user/someone';
		$this->router->get('/user/{id}', 'index.user.view', [
			'id' => '(\d+)'
		]);
		$this->router->disableMap();
		$this->router->parse($this->req);
		$this->assertNull($this->req->module);
		$this->assertNull($this->req->controller);
		$this->assertNull($this->req->action);
	}
	public function testMethod() {
		$this->req_content->server['request_method'] = 'put';
		$this->req_content->server['request_uri'] = '/user/123';
		$this->router->get('/user/{id}', [
			'module' => 'index',
			'controller' => 'user',
			'action' => 'view'
		]);
		$this->router->put('/user/{id}', [
			'module' => 'index',
			'controller' => 'user',
			'action' => 'update'
		]);
		$this->router->disableMap();
		$this->router->parse($this->req);
		$this->assertEquals('update', $this->req->action);
	}
	public function testAny() {
		$this->req_content->server['request_method'] = 'get';
		$this->req_content->server['request_uri'] = '/user/someone';
		$this->router->get('/user/{id}', [
			'module' => 'index',
			'controller' => 'user',
			'action' => 'update'
		], [
			'id' => '(\d+)'
		]);
		$this->router->any('/user/{id}', [
			'module' => 'index',
			'controller' => 'user',
			'action' => 'view'
		]);
		$this->router->disableMap();
		$this->router->parse($this->req);
		$this->assertEquals('view', $this->req->action);
	}
	public function testClosure() {
		$this->req_content->server['request_method'] = 'get';
		$this->req_content->server['request_uri'] = '/user/123/view';
		$this->router->get('/user/{id}/{action}', function($param) {
			return [
				'module' => 'index',
				'controller' => 'user',
				'action' => $param['action']
			];
		}, [
			'id' => '(\d+)'
		]);
		$this->router->disableMap();
		$this->router->parse($this->req);
		$this->assertEquals('123', $this->req->param['id']);
		$this->assertEquals('view', $this->req->action);
	}
	public function testEmpty() {
		$this->req_content->server['request_method'] = 'get';
		$this->req_content->server['request_uri'] = '/';
		$this->router->parse($this->req);
		$this->assertEquals('index', $this->req->module);
		$this->assertEquals('index', $this->req->controller);
		$this->assertEquals('index', $this->req->action);
	}
	public function testOther() {
		$this->router->setPrefix('/api');
		$this->req_content->server['request_method'] = 'get';
		$this->req_content->server['request_uri'] = '/api/user/view.html?id=1';
		$this->router->parse($this->req);
		$this->assertEquals('index', $this->req->module);
		$this->assertEquals('user', $this->req->controller);
		$this->assertEquals('view', $this->req->action);
		$this->assertEquals('html', $this->req->extension);
	}
}