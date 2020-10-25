<?php

namespace API\DAO;

use Home\DAO\PSIBaseExDAO;
use Home\DAO\DataOrgDAO;
use Home\Common\FIdConst;
use Home\DAO\CustomerDAO;
use Home\DAO\WarehouseDAO;

/**
 * 客户API DAO
 *
 * @author 李静波
 */
class CustomerApiDAO extends PSIBaseExDAO {

	public function customerList($params) {
		$db = $this->db;
		
		$page = $params["page"];
		if (! $page) {
			$page = 1;
		}
		$limit = $params["limit"];
		if (! $limit) {
			$limit = 10;
		}
		
		$start = ($page - 1) * $limit;
		
		$loginUserId = $params["userId"];
		
		$categoryId = $params["categoryId"];
		$code = $params["code"];
		$name = $params["name"];
		$address = $params["address"];
		$contact = $params["contact"];
		$mobile = $params["mobile"];
		$tel = $params["tel"];
		$qq = $params["qq"];
		
		$result = [];
		$queryParam = [];
		
		$sql = "select c.id, c.code, c.name, g.name as category_name
				from t_customer c, t_customer_category g
				where (c.category_id = g.id)";
		$ds = new DataOrgDAO($db);
		$rs = $ds->buildSQL(FIdConst::CUSTOMER, "c", $loginUserId);
		if ($rs) {
			$sql .= " and " . $rs[0];
			$queryParam = array_merge($queryParam, $rs[1]);
		}
		if ($categoryId != "-1") {
			$sql .= " and (c.category_id = '%s') ";
			$queryParam[] = $categoryId;
		}
		if ($code) {
			$sql .= " and (c.code like '%s' ) ";
			$queryParam[] = "%{$code}%";
		}
		if ($name) {
			$sql .= " and (c.name like '%s' or c.py like '%s' ) ";
			$queryParam[] = "%{$name}%";
			$queryParam[] = "%{$name}%";
		}
		if ($address) {
			$sql .= " and (c.address like '%s' or c.address_receipt like '%s') ";
			$queryParam[] = "%$address%";
			$queryParam[] = "%{$address}%";
		}
		if ($contact) {
			$sql .= " and (c.contact01 like '%s' or c.contact02 like '%s' ) ";
			$queryParam[] = "%{$contact}%";
			$queryParam[] = "%{$contact}%";
		}
		if ($mobile) {
			$sql .= " and (c.mobile01 like '%s' or c.mobile02 like '%s' ) ";
			$queryParam[] = "%{$mobile}%";
			$queryParam[] = "%{$mobile}";
		}
		if ($tel) {
			$sql .= " and (c.tel01 like '%s' or c.tel02 like '%s' ) ";
			$queryParam[] = "%{$tel}%";
			$queryParam[] = "%{$tel}";
		}
		if ($qq) {
			$sql .= " and (c.qq01 like '%s' or c.qq02 like '%s' ) ";
			$queryParam[] = "%{$qq}%";
			$queryParam[] = "%{$qq}";
		}
		
		$sql .= "order by g.code, c.code
				limit %d, %d";
		$queryParam[] = $start;
		$queryParam[] = $limit;
		
		$data = $db->query($sql, $queryParam);
		foreach ( $data as $v ) {
			$result[] = [
					"id" => $v["id"],
					"code" => $v["code"],
					"name" => $v["name"],
					"categoryName" => $v["category_name"]
			];
		}
		
		$sql = "select count(*) as cnt
				from t_customer c
				where (1 = 1) ";
		$queryParam = [];
		$ds = new DataOrgDAO($db);
		$rs = $ds->buildSQL(FIdConst::CUSTOMER, "c", $loginUserId);
		if ($rs) {
			$sql .= " and " . $rs[0];
			$queryParam = array_merge($queryParam, $rs[1]);
		}
		if ($categoryId != "-1") {
			$sql .= " and (c.category_id = '%s') ";
			$queryParam[] = $categoryId;
		}
		if ($code) {
			$sql .= " and (c.code like '%s' ) ";
			$queryParam[] = "%{$code}%";
		}
		if ($name) {
			$sql .= " and (c.name like '%s' or c.py like '%s' ) ";
			$queryParam[] = "%{$name}%";
			$queryParam[] = "%{$name}%";
		}
		if ($address) {
			$sql .= " and (c.address like '%s' or c.address_receipt like '%s') ";
			$queryParam[] = "%$address%";
			$queryParam[] = "%{$address}%";
		}
		if ($contact) {
			$sql .= " and (c.contact01 like '%s' or c.contact02 like '%s' ) ";
			$queryParam[] = "%{$contact}%";
			$queryParam[] = "%{$contact}%";
		}
		if ($mobile) {
			$sql .= " and (c.mobile01 like '%s' or c.mobile02 like '%s' ) ";
			$queryParam[] = "%{$mobile}%";
			$queryParam[] = "%{$mobile}";
		}
		if ($tel) {
			$sql .= " and (c.tel01 like '%s' or c.tel02 like '%s' ) ";
			$queryParam[] = "%{$tel}%";
			$queryParam[] = "%{$tel}";
		}
		if ($qq) {
			$sql .= " and (c.qq01 like '%s' or c.qq02 like '%s' ) ";
			$queryParam[] = "%{$qq}%";
			$queryParam[] = "%{$qq}";
		}
		
		$data = $db->query($sql, $queryParam);
		$cnt = $data[0]["cnt"];
		
		$totalPage = ceil($cnt / $limit);
		
		return [
				"dataList" => $result,
				"totalPage" => $totalPage,
				"totalCount" => $cnt
		];
	}

	public function categoryListWithAllCategory($params) {
		$db = $this->db;
		
		$result = [];
		
		$result[] = [
				"id" => "-1",
				"name" => "[所有分类]"
		];
		
		$loginUserId = $params["userId"];
		$ds = new DataOrgDAO($db);
		$queryParam = [];
		$sql = "select c.id, c.code, c.name
				from t_customer_category c ";
		$rs = $ds->buildSQL(FIdConst::CUSTOMER_CATEGORY, "c", $loginUserId);
		if ($rs) {
			$sql .= " where " . $rs[0];
			$queryParam = array_merge($queryParam, $rs[1]);
		}
		
		$sql .= " order by c.code";
		
		$data = $db->query($sql, $queryParam);
		foreach ( $data as $v ) {
			$result[] = [
					"id" => $v["id"],
					"code" => $v["code"],
					"name" => $v["name"]
			];
		}
		
		return $result;
	}

	public function categoryList($params) {
		$db = $this->db;
		
		$result = [];
		
		$loginUserId = $params["userId"];
		
		$ds = new DataOrgDAO($db);
		$queryParam = [];
		$sql = "select c.id, c.code, c.name, c.ps_id
				from t_customer_category c ";
		$rs = $ds->buildSQL(FIdConst::CUSTOMER_CATEGORY, "c", $loginUserId);
		if ($rs) {
			$sql .= " where " . $rs[0];
			$queryParam = array_merge($queryParam, $rs[1]);
		}
		
		$sql .= " order by c.code";
		
		$data = $db->query($sql, $queryParam);
		foreach ( $data as $v ) {
			$psId = $v["ps_id"];
			$psName = '';
			if ($psId) {
				$sql = "select name from t_price_system where id = '%s' ";
				$d = $db->query($sql, $psId);
				$psName = $d[0]["name"];
			}
			
			$queryParam = [];
			$sql = "select count(*) as cnt from t_customer c
					where (category_id = '%s') ";
			$queryParam[] = $v["id"];
			$rs = $ds->buildSQL(FIdConst::CUSTOMER, "c", $loginUserId);
			if ($rs) {
				$sql .= " and " . $rs[0];
				$queryParam = array_merge($queryParam, $rs[1]);
			}
			$d = $db->query($sql, $queryParam);
			$cnt = $d[0]["cnt"];
			
			$result[] = [
					"id" => $v["id"],
					"code" => $v["code"],
					"name" => $v["name"],
					"psName" => $psName,
					"cnt" => $cnt
			];
		}
		
		return $result;
	}

	public function addCustomerCategory(& $params) {
		$dao = new CustomerDAO($this->db);
		return $dao->addCustomerCategory($params);
	}

	public function updateCustomerCategory(& $params) {
		$dao = new CustomerDAO($this->db);
		return $dao->updateCustomerCategory($params);
	}

	public function priceSystemList($params) {
		$db = $this->db;
		
		$sql = "select id, name
				from t_price_system
				order by name";
		$data = $db->query($sql);
		
		$result = [
				[
						"id" => "-1",
						"name" => "[无]"
				]
		];
		foreach ( $data as $v ) {
			$result[] = [
					"id" => $v["id"],
					"name" => $v["name"]
			];
		}
		
		return $result;
	}

	public function categoryInfo($params) {
		$db = $this->db;
		
		$id = $params["categoryId"];
		$loginUserId = $params["loginUserId"];
		
		$result = [];
		
		$sql = "select id, code, name, ps_id from t_customer_category where id = '%s' ";
		$data = $db->query($sql, $id);
		if ($data) {
			$v = $data[0];
			
			$result["id"] = $v["id"];
			$result["code"] = $v["code"];
			$result["name"] = $v["name"];
			$psId = $v["ps_id"];
			$result["psId"] = $psId;
			$result["psName"] = "[无]";
			if ($psId) {
				$sql = "select name from t_price_system where id = '%s' ";
				$d = $db->query($sql, $psId);
				$result["psName"] = $d[0]["name"];
			}
			
			// 统计该分类下的客户数，不用考虑数据域，因为是用来判断是否可以删除该分类用的，需要考虑所有的数据
			$sql = "select count(*) as cnt from t_customer where category_id = '%s' ";
			$d = $db->query($sql, $v["id"]);
			$cnt = $d[0]["cnt"];
			$result["canDelete"] = $cnt == 0;
		}
		
		return $result;
	}

	public function deleteCategory(& $params) {
		$db = $this->db;
		
		$dao = new CustomerDAO($db);
		
		return $dao->deleteCustomerCategory($params);
	}

	public function customerInfo($params) {
		$db = $this->db;
		
		$id = $params["id"];
		
		$result = [];
		
		$sql = "select c.category_id, c.code, c.name, c.contact01, c.qq01, c.mobile01, c.tel01,
					c.contact02, c.qq02, c.mobile02, c.tel02, c.address, c.address_receipt,
					c.init_receivables, c.init_receivables_dt,
					c.bank_name, c.bank_account, c.tax_number, c.fax, c.note, c.sales_warehouse_id,
					g.name as category_name
				from t_customer c, t_customer_category g
				where (c.id = '%s') and (c.category_id = g.id) ";
		$data = $db->query($sql, $id);
		if ($data) {
			$result["id"] = $id;
			$result["categoryId"] = $data[0]["category_id"];
			$result["categoryName"] = $data[0]["category_name"];
			$result["code"] = $data[0]["code"];
			$result["name"] = $data[0]["name"];
			$result["contact01"] = $data[0]["contact01"];
			$result["qq01"] = $data[0]["qq01"];
			$result["mobile01"] = $data[0]["mobile01"];
			$result["tel01"] = $data[0]["tel01"];
			$result["contact02"] = $data[0]["contact02"];
			$result["qq02"] = $data[0]["qq02"];
			$result["mobile02"] = $data[0]["mobile02"];
			$result["tel02"] = $data[0]["tel02"];
			$result["address"] = $data[0]["address"];
			$result["addressReceipt"] = $data[0]["address_receipt"];
			$result["initReceivables"] = $data[0]["init_receivables"];
			$d = $data[0]["init_receivables_dt"];
			if ($d) {
				$result["initReceivablesDT"] = $this->toYMD($d);
			}
			$result["bankName"] = $data[0]["bank_name"];
			$result["bankAccount"] = $data[0]["bank_account"];
			$result["tax"] = $data[0]["tax_number"];
			$result["fax"] = $data[0]["fax"];
			$result["note"] = $data[0]["note"];
			
			$result["warehouseId"] = "";
			$result["warehouseName"] = "";
			$warehouseId = $data[0]["sales_warehouse_id"];
			if ($warehouseId) {
				$warehouseDAO = new WarehouseDAO($db);
				$warehouse = $warehouseDAO->getWarehouseById($warehouseId);
				if ($warehouse) {
					$result["warehouseId"] = $warehouseId;
					$result["warehouseName"] = $warehouse["name"];
				}
			}
		}
		
		return $result;
	}

	public function getCustomerCategoryById($id) {
		$dao = new CustomerDAO($this->db);
		return $dao->getCustomerCategoryById($id);
	}

	public function addCustomer(& $params) {
		$dao = new CustomerDAO($this->db);
		return $dao->addCustomer($params);
	}

	public function updateCustomer(& $params) {
		$dao = new CustomerDAO($this->db);
		return $dao->updateCustomer($params);
	}

	public function initReceivables(& $params) {
		$dao = new CustomerDAO($this->db);
		return $dao->initReceivables($params);
	}

	public function warehouseList($params) {
		$db = $this->db;
		
		$loginUserId = $params["loginUserId"];
		$ds = new DataOrgDAO($db);
		$sql = "select w.id, w.name
				from t_warehouse w ";
		
		$queryParam = [];
		$rs = $ds->buildSQL(FIdConst::WAREHOUSE, "w", $loginUserId);
		if ($rs) {
			$sql .= " and " . $rs[0];
			$queryParam = array_merge($queryParam, $rs[1]);
		}
		
		$sql .= " order by w.code ";
		$data = $db->query($sql, $queryParam);
		
		$result = [
				[
						"id" => "",
						"name" => "[无]"
				]
		];
		foreach ( $data as $v ) {
			$result[] = [
					"id" => $v["id"],
					"name" => $v["name"]
			];
		}
		
		return $result;
	}
	
	public function deleteCustomer(& $params){
		$dao = new CustomerDAO($this->db);
		return $dao->deleteCustomer($params);
	}
}