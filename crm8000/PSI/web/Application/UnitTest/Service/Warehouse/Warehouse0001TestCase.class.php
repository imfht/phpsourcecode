<?php

namespace UnitTest\Service\Warehouse;

use Home\DAO\WarehouseDAO;
use UnitTest\Service\BaseTestCase;

/**
 * 仓库用例基类
 *
 * @author 李静波
 */
class Warehouse0001TestCase extends BaseTestCase {

	/**
	 * 运行测试用例
	 */
	function run($db) {
		$id = "Warehouse0001";
		$name = "UnitTest\\Service\\Warehouse\\Warehouse0001TestCase";
		
		$dao = new WarehouseDAO($db);
		$parasm = [];
		$rc = $dao->addWarehouse($parasm);
		if ($rc) {
			return $this->toResult($id, $name, 0, $rc["msg"]);
		} else {
			return $this->toResult($id, $name, 1, "");
		}
	}
}