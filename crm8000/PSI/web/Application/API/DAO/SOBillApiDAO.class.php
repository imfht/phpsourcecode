<?php

namespace API\DAO;

use Home\DAO\PSIBaseExDAO;
use Home\DAO\DataOrgDAO;
use Home\Common\FIdConst;
use Home\DAO\BizConfigDAO;

/**
 * 销售订单API DAO
 *
 * @author 李静波
 */
class SOBillApiDAO extends PSIBaseExDAO {

	private function receivingTypeCodeToName($code) {
		switch ($code) {
			case 0 :
				return "记应收账款";
			case 1 :
				return "现金收款";
			default :
				return $code;
		}
	}

	private function billStatusCodeToName($code) {
		switch ($code) {
			case 0 :
				return "待审核";
			case 1000 :
				return "已审核";
			case 2000 :
				return "部分出库";
			case 3000 :
				return "全部出库";
			default :
				return $code;
		}
	}

	public function sobillList($params) {
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
		
		$loginUserId = $params["loginUserId"];
		if ($this->loginUserIdNotExists($loginUserId)) {
			return $this->emptyResult();
		}
		
		$billStatus = $params["billStatus"];
		$ref = $params["ref"];
		$fromDT = $params["fromDT"];
		$toDT = $params["toDT"];
		$customerId = $params["customerId"];
		$receivingType = $params["receivingType"];
		
		$queryParams = [];
		
		$result = [];
		$sql = "select s.id, s.ref, s.bill_status, s.goods_money,
					c.name as customer_name, s.deal_date
				from t_so_bill s, t_customer c
				where (s.customer_id = c.id) ";
		
		$ds = new DataOrgDAO($db);
		$rs = $ds->buildSQL(FIdConst::SALE_ORDER, "s", $loginUserId);
		if ($rs) {
			$sql .= " and " . $rs[0];
			$queryParams = $rs[1];
		}
		
		if ($billStatus != - 1) {
			$sql .= " and (s.bill_status = %d) ";
			$queryParams[] = $billStatus;
		}
		if ($ref) {
			$sql .= " and (s.ref like '%s') ";
			$queryParams[] = "%$ref%";
		}
		if ($fromDT) {
			$sql .= " and (s.deal_date >= '%s')";
			$queryParams[] = $fromDT;
		}
		if ($toDT) {
			$sql .= " and (s.deal_date <= '%s')";
			$queryParams[] = $toDT;
		}
		if ($customerId) {
			$sql .= " and (s.customer_id = '%s')";
			$queryParams[] = $customerId;
		}
		if ($receivingType != - 1) {
			$sql .= " and (s.receiving_type = %d) ";
			$queryParams[] = $receivingType;
		}
		$sql .= " order by s.deal_date desc, s.ref desc
				  limit %d , %d";
		$queryParams[] = $start;
		$queryParams[] = $limit;
		$data = $db->query($sql, $queryParams);
		foreach ( $data as $i => $v ) {
			$result[] = [
					"id" => $v["id"],
					"ref" => $v["ref"],
					"billStatus" => $this->billStatusCodeToName($v["bill_status"]),
					"dealDate" => $this->toYMD($v["deal_date"]),
					"goodsMoney" => $v["goods_money"],
					"customerName" => $v["customer_name"]
			];
		}
		
		$sql = "select count(*) as cnt
				from t_so_bill s, t_customer c, t_org o, t_user u1, t_user u2
				where (s.customer_id = c.id) and (s.org_id = o.id)
					and (s.biz_user_id = u1.id) and (s.input_user_id = u2.id)
				";
		$queryParams = [];
		$ds = new DataOrgDAO($db);
		$rs = $ds->buildSQL(FIdConst::SALE_ORDER, "s", $loginUserId);
		if ($rs) {
			$sql .= " and " . $rs[0];
			$queryParams = $rs[1];
		}
		if ($billStatus != - 1) {
			$sql .= " and (s.bill_status = %d) ";
			$queryParams[] = $billStatus;
		}
		if ($ref) {
			$sql .= " and (s.ref like '%s') ";
			$queryParams[] = "%$ref%";
		}
		if ($fromDT) {
			$sql .= " and (s.deal_date >= '%s')";
			$queryParams[] = $fromDT;
		}
		if ($toDT) {
			$sql .= " and (s.deal_date <= '%s')";
			$queryParams[] = $toDT;
		}
		if ($customerId) {
			$sql .= " and (s.customer_id = '%s')";
			$queryParams[] = $customerId;
		}
		if ($receivingType != - 1) {
			$sql .= " and (s.receiving_type = %d) ";
			$queryParams[] = $receivingType;
		}
		$data = $db->query($sql, $queryParams);
		$cnt = $data[0]["cnt"];
		
		$totalPage = ceil($cnt / $limit);
		
		return array(
				"dataList" => $result,
				"totalPage" => $totalPage
		);
	}

	public function sobillInfo($params) {
		$db = $this->db;
		
		// 采购订单主表id
		$id = $params["id"];
		
		$companyId = $params["companyId"];
		if ($this->companyIdNotExists($companyId)) {
			return $this->emptyResult();
		}
		
		$result = [];
		
		$cs = new BizConfigDAO($db);
		$dataScale = $cs->getGoodsCountDecNumber($companyId);
		$fmt = "decimal(19, " . $dataScale . ")";
		
		$sql = "select s.ref, s.deal_date, s.deal_address, s.customer_id,
					c.name as customer_name, s.contact, s.tel, s.fax,
					s.org_id, o.full_name, s.biz_user_id, u.name as biz_user_name,
					s.receiving_type, s.bill_memo, s.bill_status,
					s.goods_money, s.tax, s.money_with_tax
				from t_so_bill s, t_customer c, t_user u, t_org o
				where s.id = '%s' and s.customer_Id = c.id
					and s.biz_user_id = u.id
					and s.org_id = o.id";
		$data = $db->query($sql, $id);
		if ($data) {
			$v = $data[0];
			$result["ref"] = $v["ref"];
			$result["dealDate"] = $this->toYMD($v["deal_date"]);
			$result["dealAddress"] = $v["deal_address"];
			$result["customerName"] = $v["customer_name"];
			$result["contact"] = $v["contact"];
			$result["tel"] = $v["tel"];
			$result["fax"] = $v["fax"];
			$result["orgFullName"] = $v["full_name"];
			$result["bizUserName"] = $v["biz_user_name"];
			$result["receivingType"] = $this->receivingTypeCodeToName($v["receiving_type"]);
			$result["billMemo"] = $v["bill_memo"];
			$result["billStatus"] = $this->billStatusCodeToName($v["bill_status"]);
			$result["goodsMoney"] = $v["goods_money"];
			$result["tax"] = $v["tax"];
			$result["moneyWithTax"] = $v["money_with_tax"];
			
			// 明细表
			$sql = "select s.id, s.goods_id, g.code, g.name, g.spec,
						convert(s.goods_count, " . $fmt . ") as goods_count, s.goods_price, s.goods_money,
						s.tax_rate, s.tax, s.money_with_tax, u.name as unit_name, s.memo
					from t_so_bill_detail s, t_goods g, t_goods_unit u
					where s.sobill_id = '%s' and s.goods_id = g.id and g.unit_id = u.id
					order by s.show_order";
			$items = [];
			$data = $db->query($sql, $id);
			
			foreach ( $data as $v ) {
				$items[] = [
						"goodsCode" => $v["code"],
						"goodsName" => $v["name"],
						"goodsSpec" => $v["spec"],
						"goodsCount" => $v["goods_count"],
						"goodsPrice" => $v["goods_price"],
						"goodsMoney" => $v["goods_money"],
						"taxRate" => $v["tax_rate"],
						"tax" => $v["tax"],
						"moneyWithTax" => $v["money_with_tax"],
						"unitName" => $v["unit_name"],
						"memo" => $v["memo"]
				];
			}
			
			$result["items"] = $items;
		}
		
		return $result;
	}
}