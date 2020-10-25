<?php

namespace tests\Web;

use PHPUnit\Framework\TestCase;

require_once './TestService.php';

class WebService_Test extends TestCase
{

	/**
	 * @var TestService;
	 */
	private $service = null;

	public function setUp(): void
	{
		$this->service = new TestService();
	}

	public function testNoReturnServe()
	{
		$st = $this->service->serve('no_return');
		$this->assertTrue($st->isFailure());
		$this->assertEquals($st->getMessage(), 'Invalid serve return!');
	}

	public function testThrowErrorServe()
	{
		$st = $this->service->serve('throw_error');
		$this->assertTrue($st->isFailure());
		$this->assertEquals($st->getMessage(), '这是一个异常');
		$this->assertArrayHasKey('error', $st->data);
	}

	public function testAnotherServe()
	{
		$st = $this->service->serve('another');
		$this->assertTrue($st->isSuccess());
		$this->assertEquals($st->getMessage(), 'success');
		$this->assertArrayHasKey('key', $st->data);
		$this->assertEquals($st->data['key'], 'value');
	}
}
