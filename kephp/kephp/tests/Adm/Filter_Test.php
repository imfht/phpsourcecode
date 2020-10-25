<?php

namespace tests\Adm;

use Ke\Adm\Filter;
use PHPUnit\Framework\TestCase;

class Filter_Test extends TestCase
{

	/** @var Filter */
	private $filter;

	public function setUp(): void
	{
		$this->filter = new Filter();
	}

	public function testFilterDateTimeByDateTime()
	{
		$values = [
			['2012-12-12', '2012-12-12 00:00:00'],
			['12-12-12', '2012-12-12 00:00:00'],
			['12-12-12 12', '2012-12-12 12:00:00'],
			['12-12-12   12', '2012-12-12 12:00:00'],
			['12-12-12  12:41:12', '2012-12-12 12:41:12'],
			['12-12  12:2:1', '0000-00-00 00:00:00'],
		];

		foreach ($values as $item) {
			list ($value, $actual) = $item;
			$r = $this->filter->filterDatetime($value, null, 'datetime');
			$this->assertEquals($r, $actual);
		}
	}

	public function testFilterDateTimeByDate()
	{
		$values = [
			['2012-12-12', '2012-12-12'],
			['12-12-12', '2012-12-12'],
			['12-12-12 12', '2012-12-12'],
			['12-12-12   12', '2012-12-12'],
			['12-12-12  12:41:12', '2012-12-12'],
			['12-12  12:2:1', '0000-00-00'],
		];

		foreach ($values as $item) {
			list ($value, $actual) = $item;
			$r = $this->filter->filterDatetime($value, null, 'date');
			$this->assertEquals($r, $actual, $value);
		}
	}

	public function testFilterDateTimeByTime()
	{
		$values = [
			['2012-12-12', '00:00:00'],
			['12-12-12', '00:00:00'],
			['12-12-12 12', '12:00:00'],
			['12-12-12   12', '12:00:00'],
			['12-12-12  12:41:12', '12:41:12'],
			['12-12  12:2:1', '00:00:00'],
			['1:2:1', '01:02:01'],
		];

		foreach ($values as $item) {
			list ($value, $actual) = $item;
			$r = $this->filter->filterDatetime($value, null, 'time');
			$this->assertEquals($r, $actual);
		}
	}
}
