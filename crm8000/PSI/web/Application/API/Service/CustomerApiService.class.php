<?php

namespace API\Service;

use API\DAO\CustomerApiDAO;
use Home\Service\PinyinService;
use Home\DAO\WarehouseDAO;

/**
 * 客户 API Service
 *
 * @author 李静波
 */
class CustomerApiService extends PSIApiBaseService {
	private $LOG_CATEGORY = "客户关系-客户资料";

	public function customerList($params) {
		$tokenId = $params["tokenId"];
		if ($this->tokenIsInvalid($tokenId)) {
			return $this->emptyResult();
		}
		
		$params["userId"] = $this->getUserIdFromTokenId($tokenId);
		
		$dao = new CustomerApiDAO($this->db());
		return $dao->customerList($params);
	}

	public function categoryListWithAllCategory($params) {
		$tokenId = $params["tokenId"];
		if ($this->tokenIsInvalid($tokenId)) {
			return $this->emptyResult();
		}
		
		$params["userId"] = $this->getUserIdFromTokenId($tokenId);
		
		$dao = new CustomerApiDAO($this->db());
		return $dao->categoryListWithAllCategory($params);
	}

	public function categoryList($params) {
		$tokenId = $params["tokenId"];
		if ($this->tokenIsInvalid($tokenId)) {
			return $this->emptyResult();
		}
		
		$params["userId"] = $this->getUserIdFromTokenId($tokenId);
		
		$dao = new CustomerApiDAO($this->db());
		return $dao->categoryList($params);
	}

	public function editCategory($params) {
		$tokenId = $params["tokenId"];
		if ($this->tokenIsInvalid($tokenId)) {
			return $this->bad("当前用户没有登录");
		}
		
		$fromDevice = $params["fromDevice"];
		if (! $fromDevice) {
			$fromDevice = "移动端";
		}
		
		$params["loginUserId"] = $this->getUserIdFromTokenId($tokenId);
		
		$db = $this->db();
		$db->startTrans();
		$log = null;
		
		$id = $params["id"];
		$code = $params["code"];
		$name = $params["name"];
		
		$psId = $params["psId"];
		if ($psId == "-1") {
			$params["psId"] = "";
		}
		
		$dao = new CustomerApiDAO($db);
		if ($id) {
			// 编辑
			$rc = $dao->updateCustomerCategory($params);
			if ($rc) {
				$db->rollback();
				return $rc;
			}
			
			$log = "从{$fromDevice}编辑客户分类: 编码 = {$code}, 分类名 = {$name}";
		} else {
			// 新增
			$params["dataOrg"] = $this->getDataOrgFromTokenId($tokenId);
			$params["companyId"] = $this->getCompanyIdFromTokenId($tokenId);
			
			$rc = $dao->addCustomerCategory($params);
			if ($rc) {
				$db->rollback();
				return $rc;
			}
			
			$id = $params["id"];
			
			$log = "从{$fromDevice}新增客户分类：编码 = {$code}, 分类名 = {$name}";
		}
		
		// 记录业务日志
		$bs = new BizlogApiService($db);
		$bs->insertBizlog($tokenId, $log, $this->LOG_CATEGORY);
		
		$db->commit();
		
		return $this->ok();
	}

	public function priceSystemList($params) {
		$tokenId = $params["tokenId"];
		if ($this->tokenIsInvalid($tokenId)) {
			return $this->emptyResult();
		}
		
		$params["loginUserId"] = $this->getUserIdFromTokenId($tokenId);
		
		$dao = new CustomerApiDAO($this->db());
		
		return $dao->priceSystemList($params);
	}

	public function categoryInfo($params) {
		$tokenId = $params["tokenId"];
		if ($this->tokenIsInvalid($tokenId)) {
			return $this->emptyResult();
		}
		
		$params["loginUserId"] = $this->getUserIdFromTokenId($tokenId);
		$dao = new CustomerApiDAO($this->db());
		
		return $dao->categoryInfo($params);
	}

	public function deleteCategory($params) {
		$tokenId = $params["tokenId"];
		if ($this->tokenIsInvalid($tokenId)) {
			return $this->bad("当前用户没有登录");
		}
		
		$fromDevice = $params["fromDevice"];
		if (! $fromDevice) {
			$fromDevice = "移动端";
		}
		
		$db = $this->db();
		$db->startTrans();
		
		$dao = new CustomerApiDAO($db);
		
		$rc = $dao->deleteCategory($params);
		if ($rc) {
			$db->rollback();
			return $rc;
		}
		
		// 记录业务日志
		$code = $params["code"];
		$name = $params["name"];
		$log = "从{$fromDevice}删除客户分类: 编码={$code}, 名称={$name}";
		$bs = new BizlogApiService($db);
		$bs->insertBizlog($tokenId, $log, $this->LOG_CATEGORY);
		
		$db->commit();
		
		return $this->ok();
	}

	public function customerInfo($params) {
		$tokenId = $params["tokenId"];
		if ($this->tokenIsInvalid($tokenId)) {
			return $this->emptyResult();
		}
		
		$params["loginUserId"] = $this->getUserIdFromTokenId($tokenId);
		
		$dao = new CustomerApiDAO($this->db());
		
		return $dao->customerInfo($params);
	}

	public function editCustomer($params) {
		$tokenId = $params["tokenId"];
		if ($this->tokenIsInvalid($tokenId)) {
			return $this->bad("当前用户没有登录");
		}
		
		$id = $params["id"];
		$code = $params["code"];
		$name = $params["name"];
		
		$params["loginUserId"] = $this->getUserIdFromTokenId($tokenId);
		$params["dataOrg"] = $this->getDataOrgFromTokenId($tokenId);
		$params["companyId"] = $this->getCompanyIdFromTokenId($tokenId);
		
		$ps = new PinyinService();
		$params["py"] = $ps->toPY($name);
		
		$db = $this->db();
		$db->startTrans();
		
		$dao = new CustomerApiDAO($db);
		
		$category = $dao->getCustomerCategoryById($params["categoryId"]);
		if (! $category) {
			$db->rollback();
			return $this->bad("客户分类不存在");
		}
		
		$initReceivablesDT = $params["initReceivablesDT"];
		if ($initReceivablesDT) {
			if (! $this->dateIsValid($initReceivablesDT)) {
				$db->rollback();
				return $this->bad("应收账款期初日期不正确");
			}
		}
		$warehouseId = $params["warehouseId"];
		if ($warehouseId) {
			$warehouseDAO = new WarehouseDAO($db);
			$warehouse = $warehouseDAO->getWarehouseById($warehouseId);
			if (! $warehouse) {
				$db->rollback();
				return $this->bad("选择的销售出库仓库不存在");
			}
		}
		
		$fromDevice = $params["fromDevice"];
		if (! $fromDevice) {
			$fromDevice = "移动端";
		}
		$log = null;
		
		if ($id) {
			// 编辑
			$rc = $dao->updateCustomer($params);
			if ($rc) {
				$db->rollback();
				return $rc;
			}
			
			$log = "从{$fromDevice}编辑客户：编码 = {$code}, 名称 = {$name}";
		} else {
			// 新增
			$rc = $dao->addCustomer($params);
			if ($rc) {
				$db->rollback();
				return $rc;
			}
			
			$id = $params["id"];
			
			$log = "从{$fromDevice}新增客户：编码 = {$code}, 名称 = {$name}";
		}
		
		// 处理应收账款期初
		$rc = $dao->initReceivables($params);
		if ($rc) {
			$db->rollback();
			return $rc;
		}
		
		// 记录业务日志
		$bs = new BizlogApiService($db);
		$bs->insertBizlog($tokenId, $log, $this->LOG_CATEGORY);
		
		$db->commit();
		
		return $this->ok($id);
	}

	public function warehouseList($params) {
		$tokenId = $params["tokenId"];
		if ($this->tokenIsInvalid($tokenId)) {
			return $this->emptyResult();
		}
		
		$params["loginUserId"] = $this->getUserIdFromTokenId($tokenId);
		
		$dao = new CustomerApiDAO($this->db());
		
		return $dao->warehouseList($params);
	}

	public function deleteCustomer($params) {
		$tokenId = $params["tokenId"];
		if ($this->tokenIsInvalid($tokenId)) {
			return $this->bad("当前用户没有登录");
		}
		
		$params["loginUserId"] = $this->getUserIdFromTokenId($tokenId);
		
		$db = $this->db();
		$db->startTrans();
		
		$dao = new CustomerApiDAO($this->db());
		
		$rc = $dao->deleteCustomer($params);
		if ($rc) {
			$db->rollback();
			return $rc;
		}
		
		// 记录业务日志
		$fromDevice = $params["fromDevice"];
		if (! $fromDevice) {
			$fromDevice = "移动端";
		}
		$code = $params["code"];
		$name = $params["name"];
		$log = "从{$fromDevice}删除客户：编码 = {$code},  名称 = {$name}";
		$bs = new BizlogApiService($db);
		$bs->insertBizlog($tokenId, $log, $this->LOG_CATEGORY);
		
		$db->commit();
		
		return $this->ok();
	}
}