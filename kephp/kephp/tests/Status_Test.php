<?php

namespace tests;

use Ke\Utils\Status;
use PHPUnit\Framework\TestCase;

class Status_Test extends TestCase
{

	public function testSuccessStatus()
	{
		$status = new Status(true, '测试状态', [
			'key' => 'value',
		]);

		$this->assertTrue($status->isSuccess());
		$this->assertEquals($status->getMessage(), '测试状态');
		$this->assertArrayHasKey('key', $status->data);
		$this->assertEquals($status->data['key'], 'value');
	}

	public function testFailStatus()
	{
		$status = new Status(0, '失败状态', [
			'key' => 'value',
		]);

		$this->assertTrue($status->isFailure());
		$this->assertEquals($status->getMessage(), '失败状态');
		$this->assertArrayHasKey('key', $status->data);
		$this->assertEquals($status->data['key'], 'value');
	}
}
