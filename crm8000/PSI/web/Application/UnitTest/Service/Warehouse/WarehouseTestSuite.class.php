<?php

namespace UnitTest\Service\Warehouse;

use UnitTest\Service\BaseTestSuite;

/**
 * 仓库测试套件
 *
 * @author 李静波
 */
class WarehouseTestSuite extends BaseTestSuite {

	function __construct() {
		parent::__construct();
		
		$this->addTest(new Warehouse0001TestCase());
	}
}