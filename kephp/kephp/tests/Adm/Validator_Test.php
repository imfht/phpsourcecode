<?php

namespace tests\Adm;

use Ke\Adm\Model;
use Ke\Adm\Validator;
use Ke\Helper\DateHelper;
use PHPUnit\Framework\TestCase;

class Validator_Test extends TestCase
{

	/** @var Validator */
	private $validator;

	public function setUp(): void
	{
		$this->validator = new Validator();
	}

	public function testIsValidDateTimeByDateTime()
	{
		$values = [
			['201', 0],
			['201-12', 0],
			['2012-12-12', 1],
			['12-12-12', 1],
			['12-12-12 12', 1],
			['12-12-12   12', 1],
			['12-12-12  12:41:12', 1],
			['12-12  12:2:1', 0],
			['00-00-00', 2],
			['00-00-00 0:0:0', 2],
		];

		foreach ($values as $item) {
			list ($value, $actual) = $item;
			$result = $this->validator->isValidDateTime($value, '', 'datetime');
			$this->assertEquals($result, $actual);
		}
	}

	public function testIsValidDateTimeByTime()
	{
		$values = [
			['201', 0],
			['201-12', 0],
			['2012-12-12', 0],
			['12-12-12', 0],
			['12-12-12 12', 1],
			['12-12-12   12', 1],
			['12-12-12  12:41:12', 1],
			['12-12  12:2:1', 1],
			['12', 1],
			['12:1', 1],
			['0', 2],
			['0:0:0', 2],
		];

		foreach ($values as $item) {
			list ($value, $actual) = $item;
			$result = $this->validator->isValidDateTime($value, '', 'time');
			$this->assertEquals($result, $actual);
		}
	}

	public function testValidateDateTimeByRequire()
	{
		$values = [
			['00-00-00 00:00:00', true, Model::ERR_NOT_ALLOW_EMPTY],
			['1', true, Model::ERR_NOT_DATETIME],
			['now', false, null],
			['', true, Model::ERR_NOT_ALLOW_EMPTY],
			[0, false, null], // 0 表示的是 时间戳 1970-1-1 xxx
			[new \DateTime(), false, null],
		];
		$column = [
			'datetime' => 'datetime',
			'require'  => true,
		];
		foreach ($values as $item) {
			list ($value, $shouldError, $err) = $item;
			$error = $this->validator->validateColumn('test', $value, $column);
			if ($shouldError) {
				$this->assertEquals(gettype($error), 'array');
				$this->assertEquals($error[0], $err);
			} else {
				$this->assertEquals($error, false);
			}
		}
	}
}
