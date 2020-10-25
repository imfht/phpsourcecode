<?php

namespace UnitTest\Service;

use UnitTest\Service\Warehouse\WarehouseTestSuite;

/**
 * PSI单元测试 Service
 *
 * @author 李静波
 */
class PSIUnitTestService {

	/**
	 * 返回所有的单元测试测试结果
	 *
	 * @return array
	 */
	public function getAllUnitTestsResult() {
		$result = [];
		
		$ts = new WarehouseTestSuite();
		$ts->run();
		
		$tr = $ts->getResults();
		
		foreach ( $tr as $r ) {
			$result[] = $r;
		}
		
		return $result;
	}
}