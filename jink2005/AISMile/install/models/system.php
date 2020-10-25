<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class InstallModelSystem extends InstallAbstractModel
{
	public function checkRequiredTests()
	{
		return self::checkTests(ConfigurationTest::getDefaultTests(), 'required');
	}

	public function checkOptionalTests()
	{
		return self::checkTests(ConfigurationTest::getDefaultTestsOp(), 'optional');
	}

	public function checkTests($list, $type)
	{
		$tests = ConfigurationTest::check($list);
		$success = true;
		foreach ($tests as $result)
			$success &= ($result == 'ok') ? true : false;

		return array(
			'checks' =>		$tests,
			'success' =>	$success,
		);
	}
}
