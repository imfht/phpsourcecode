<?php

namespace Home\DAO;

use Home\Common\FIdConst;

/**
 * 销售合同 DAO
 *
 * @author 李静波
 */
class SCBillDAO extends PSIBaseExDAO
{

  /**
   * 生成新的销售合同号
   *
   * @param string $companyId
   *
   * @return string
   */
  private function genNewBillRef($companyId)
  {
    $db = $this->db;

    $bs = new BizConfigDAO($db);
    $pre = $bs->getSCBillRefPre($companyId);

    $mid = date("Ymd");

    $sql = "select ref from t_sc_bill where ref like '%s' order by ref desc limit 1";
    $data = $db->query($sql, $pre . $mid . "%");
    $sufLength = 3;
    $suf = str_pad("1", $sufLength, "0", STR_PAD_LEFT);
    if ($data) {
      $ref = $data[0]["ref"];
      $nextNumber = intval(substr($ref, strlen($pre . $mid))) + 1;
      $suf = str_pad($nextNumber, $sufLength, "0", STR_PAD_LEFT);
    }

    return $pre . $mid . $suf;
  }

  /**
   * 获得销售合同主表信息列表
   *
   * @param array $params
   * @return array
   */
  public function scbillList($params)
  {
    $db = $this->db;

    $loginUserId = $params["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->emptyResult();
    }

    $start = $params["start"];
    $limit = $params["limit"];

    $billStatus = $params["billStatus"];
    $ref = $params["ref"];
    $fromDT = $params["fromDT"];
    $toDT = $params["toDT"];
    $customerId = $params["customerId"];
    $goodsId = $params["goodsId"];

    $sql = "select s.id, s.ref, s.bill_status, c.name as customer_name,
					u.name as input_user_name, g.full_name as org_name,
					s.begin_dt, s.end_dt, s.goods_money, s.tax, s.money_with_tax,
					s.deal_date, s.deal_address, s.bill_memo, s.discount,
					u2.name as biz_user_name, s.biz_dt, s.confirm_user_id, s.confirm_date,
					s.date_created
				from t_sc_bill s, t_customer c, t_user u, t_org g, t_user u2
				where (s.customer_id = c.id) and (s.input_user_id = u.id) 
					and (s.org_id = g.id) and (s.biz_user_id = u2.id) ";

    $queryParams = [];

    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::SALE_CONTRACT, "s", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = array_merge($queryParams, $rs[1]);
    }
    if ($ref) {
      $sql .= " and (s.ref like '%s') ";
      $queryParams[] = "%{$ref}%";
    }
    if ($billStatus != -1) {
      $sql .= " and (s.bill_status = %d) ";
      $queryParams[] = $billStatus;
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
    if ($goodsId) {
      $sql .= " and (s.id in (select distinct scbill_id from t_sc_bill_detail where goods_id = '%s')) ";
      $queryParams[] = $goodsId;
    }
    $sql .= " order by s.ref desc
				  limit %d , %d";
    $queryParams[] = $start;
    $queryParams[] = $limit;
    $data = $db->query($sql, $queryParams);

    $result = [];
    foreach ($data as $v) {
      $item = [
        "id" => $v["id"],
        "billStatus" => $v["bill_status"],
        "ref" => $v["ref"],
        "customerName" => $v["customer_name"],
        "orgName" => $v["org_name"],
        "beginDT" => $this->toYMD($v["begin_dt"]),
        "endDT" => $this->toYMD($v["end_dt"]),
        "goodsMoney" => $v["goods_money"],
        "tax" => $v["tax"],
        "moneyWithTax" => $v["money_with_tax"],
        "dealDate" => $this->toYMD($v["deal_date"]),
        "dealAddress" => $v["deal_address"],
        "discount" => $v["discount"],
        "bizUserName" => $v["biz_user_name"],
        "bizDT" => $this->toYMD($v["biz_dt"]),
        "billMemo" => $v["bill_memo"],
        "inputUserName" => $v["input_user_name"],
        "dateCreated" => $v["date_created"]
      ];

      $confirmUserId = $v["confirm_user_id"];
      if ($confirmUserId) {
        $sql = "select name from t_user where id = '%s' ";
        $d = $db->query($sql, $confirmUserId);
        if ($d) {
          $item["confirmUserName"] = $d[0]["name"];
          $item["confirmDate"] = $v["confirm_date"];
        }
      }

      $result[] = $item;
    }

    $sql = "select count(*) as cnt
				from t_sc_bill s, t_customer c, t_user u, t_org g, t_user u2
				where (s.customer_id = c.id) and (s.input_user_id = u.id)
					and (s.org_id = g.id) and (s.biz_user_id = u2.id) ";
    $queryParams = [];

    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::SALE_CONTRACT, "s", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = array_merge($queryParams, $rs[1]);
    }
    if ($ref) {
      $sql .= " and (s.ref like '%s') ";
      $queryParams[] = "%{$ref}%";
    }
    if ($billStatus != -1) {
      $sql .= " and (s.bill_status = %d) ";
      $queryParams[] = $billStatus;
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
    if ($goodsId) {
      $sql .= " and (s.id in (select distinct scbill_id from t_sc_bill_detail where goods_id = '%s')) ";
      $queryParams[] = $goodsId;
    }

    $data = $db->query($sql, $queryParams);
    $cnt = $data[0]["cnt"];

    return [
      "dataList" => $result,
      "totalCount" => $cnt
    ];
  }

  /**
   * 销售合同详情
   */
  public function scBillInfo($params)
  {
    $db = $this->db;

    // 销售合同id
    $id = $params["id"];
    $result = [];

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    $cs = new BizConfigDAO($db);
    $result["taxRate"] = $cs->getTaxRate($companyId);

    $dataScale = $cs->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    if ($id) {
      // 编辑或查看

      // 主表
      $sql = "select s.ref, s.customer_id, c.name as customer_name,
						s.begin_dt, s.end_dt, s.org_id, g.full_name as org_name,
						s.biz_dt, s.biz_user_id, u.name as biz_user_name,
						s.deal_date, s.deal_address, s.discount, s.bill_memo,
						s.quality_clause, s.insurance_clause, s.transport_clause,
						s.other_clause
					from t_sc_bill s, t_customer c, t_org g, t_user u
					where s.id = '%s' and s.customer_id = c.id 
						and s.org_id = g.id and s.biz_user_id = u.id";
      $data = $db->query($sql, $id);
      if (!$data) {
        return $this->emptyResult();
      }

      $v = $data[0];
      $result["ref"] = $v["ref"];
      $result["customerId"] = $v["customer_id"];
      $result["customerName"] = $v["customer_name"];
      $result["beginDT"] = $this->toYMD($v["begin_dt"]);
      $result["endDT"] = $this->toYMD($v["end_dt"]);
      $result["orgId"] = $v["org_id"];
      $result["orgFullName"] = $v["org_name"];
      $result["bizDT"] = $this->toYMD($v["biz_dt"]);
      $result["bizUserId"] = $v["biz_user_id"];
      $result["bizUserName"] = $v["biz_user_name"];
      $result["dealDate"] = $this->toYMD($v["deal_date"]);
      $result["dealAddress"] = $v["deal_address"];
      $result["discount"] = $v["discount"];
      $result["billMemo"] = $v["bill_memo"];
      $result["qualityClause"] = $v["quality_clause"];
      $result["insuranceClause"] = $v["insurance_clause"];
      $result["transportClause"] = $v["transport_clause"];
      $result["otherClause"] = $v["other_clause"];

      // 明细
      $sql = "select s.id, s.goods_id, g.code, g.name, g.spec,
							convert(s.goods_count, " . $fmt . ") as goods_count, s.goods_price, s.goods_money,
					s.tax_rate, s.tax, s.money_with_tax, u.name as unit_name, s.memo, s.goods_price_with_tax
				from t_sc_bill_detail s, t_goods g, t_goods_unit u
				where s.scbill_id = '%s' and s.goods_id = g.id and g.unit_id = u.id
				order by s.show_order";
      $items = [];
      $data = $db->query($sql, $id);

      foreach ($data as $v) {
        $goodsPriceWithTax = $v["goods_price_with_tax"];
        if ($goodsPriceWithTax == null) {
          // 兼容旧数据
          if ($v["goods_count"] != 0) {
            $goodsPriceWithTax = $v["money_with_tax"] / $v["goods_count"];
          }
        }
        $items[] = [
          "id" => $v["id"],
          "goodsId" => $v["goods_id"],
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
          "memo" => $v["memo"],
          "goodsPriceWithTax" => $goodsPriceWithTax
        ];
      }

      $result["items"] = $items;
    } else {
      // 新建
      $loginUserId = $params["loginUserId"];
      $result["bizUserId"] = $loginUserId;
      $result["bizUserName"] = $params["loginUserName"];

      $sql = "select o.id, o.full_name
					from t_org o, t_user u
					where o.id = u.org_id and u.id = '%s' ";
      $data = $db->query($sql, $loginUserId);
      if ($data) {
        $result["orgId"] = $data[0]["id"];
        $result["orgFullName"] = $data[0]["full_name"];
      }
    }

    return $result;
  }

  /**
   * 新增销售合同
   *
   * @param array $params
   * @return array|null
   */
  public function addSCBill(&$bill)
  {
    $db = $this->db;

    $companyId = $bill["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->badParam("companyId");
    }

    $dataOrg = $bill["dataOrg"];
    if ($this->dataOrgNotExists($dataOrg)) {
      return $this->badParam("dataOrg");
    }

    $loginUserId = $bill["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->badParam("loginUserId");
    }

    $customerId = $bill["customerId"];
    $customerDAO = new CustomerDAO($db);
    $customer = $customerDAO->getCustomerById($customerId);
    if (!$customer) {
      return $this->bad("甲方客户不存在");
    }

    $beginDT = $bill["beginDT"];
    if (!$this->dateIsValid($beginDT)) {
      return $this->bad("合同开始日期不正确");
    }
    $endDT = $bill["endDT"];
    if (!$this->dateIsValid($endDT)) {
      return $this->bad("合同结束日期不正确");
    }

    $orgId = $bill["orgId"];
    $orgDAO = new OrgDAO($db);
    $org = $orgDAO->getOrgById($orgId);
    if (!$org) {
      return $this->bad("乙方组织机构不存在");
    }

    $dealDate = $bill["dealDate"];
    if (!$this->dateIsValid($dealDate)) {
      return $this->bad("交货日期不正确");
    }

    $bizDT = $bill["bizDT"];
    if (!$this->dateIsValid($bizDT)) {
      return $this->bad("合同签订日期不正确");
    }
    $bizUserId = $bill["bizUserId"];
    $userDAO = new UserDAO($db);
    $user = $userDAO->getUserById($bizUserId);
    if (!$user) {
      return $this->bad("业务员不存在");
    }

    $dealAddress = $bill["dealAddress"];
    $discount = intval($bill["discount"]);
    if ($discount < 0 || $discount > 100) {
      $discount = 100;
    }

    $billMemo = $bill["billMemo"];
    $qualityClause = $bill["qualityClause"];
    $insuranceClause = $bill["insuranceClause"];
    $transportClause = $bill["transportClause"];
    $otherClause = $bill["otherClause"];

    $items = $bill["items"];

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $id = $this->newId();
    $ref = $this->genNewBillRef($companyId);

    // 主表
    $sql = "insert into t_sc_bill (id, ref, customer_id, org_id, biz_user_id,
					biz_dt, input_user_id, date_created, bill_status, goods_money,
					tax, money_with_tax, deal_date, deal_address, bill_memo, 
					data_org, company_id, begin_dt, end_dt, discount,
					quality_clause, insurance_clause, transport_clause, other_clause)
				values ('%s', '%s', '%s', '%s', '%s',
					'%s', '%s', now(), 0, 0,
					0, 0, '%s', '%s', '%s',
					'%s', '%s', '%s', '%s', %d,
					'%s', '%s', '%s', '%s')";
    $rc = $db->execute(
      $sql,
      $id,
      $ref,
      $customerId,
      $orgId,
      $bizUserId,
      $bizDT,
      $loginUserId,
      $dealDate,
      $dealAddress,
      $billMemo,
      $dataOrg,
      $companyId,
      $beginDT,
      $endDT,
      $discount,
      $qualityClause,
      $insuranceClause,
      $transportClause,
      $otherClause
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 明细表
    foreach ($items as $i => $v) {
      $goodsId = $v["goodsId"];
      if (!$goodsId) {
        continue;
      }
      $goodsCount = $v["goodsCount"];
      $goodsPrice = $v["goodsPrice"];
      $goodsPriceWithTax = $v["goodsPriceWithTax"];
      $goodsMoney = $v["goodsMoney"];
      $taxRate = $v["taxRate"];
      $tax = $v["tax"];
      $moneyWithTax = $v["moneyWithTax"];
      $memo = $v["memo"];

      $sql = "insert into t_sc_bill_detail(id, date_created, goods_id, goods_count, goods_money,
						goods_price, scbill_id, tax_rate, tax, money_with_tax, so_count, left_count,
						show_order, data_org, company_id, memo, discount, goods_price_with_tax)
					values ('%s', now(), '%s', convert(%f, $fmt), %f,
						%f, '%s', %d, %f, %f, 0, convert(%f, $fmt), %d, '%s', '%s', '%s', %d, %f)";
      $rc = $db->execute(
        $sql,
        $this->newId(),
        $goodsId,
        $goodsCount,
        $goodsMoney,
        $goodsPrice,
        $id,
        $taxRate,
        $tax,
        $moneyWithTax,
        $goodsCount,
        $i,
        $dataOrg,
        $companyId,
        $memo,
        $discount,
        $goodsPriceWithTax
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 同步主表的金额合计字段
    $sql = "select sum(goods_money) as sum_goods_money, sum(tax) as sum_tax,
					sum(money_with_tax) as sum_money_with_tax
				from t_sc_bill_detail
				where scbill_id = '%s' ";
    $data = $db->query($sql, $id);
    $sumGoodsMoney = $data[0]["sum_goods_money"];
    if (!$sumGoodsMoney) {
      $sumGoodsMoney = 0;
    }
    $sumTax = $data[0]["sum_tax"];
    if (!$sumTax) {
      $sumTax = 0;
    }
    $sumMoneyWithTax = $data[0]["sum_money_with_tax"];
    if (!$sumMoneyWithTax) {
      $sumMoneyWithTax = 0;
    }

    $sql = "update t_sc_bill
				set goods_money = %f, tax = %f, money_with_tax = %f
				where id = '%s' ";
    $rc = $db->execute($sql, $sumGoodsMoney, $sumTax, $sumMoneyWithTax, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $bill["id"] = $id;
    $bill["ref"] = $ref;
    return null;
  }

  public function getSCBillById($id)
  {
    $db = $this->db;

    $sql = "select ref, bill_status, data_org
				from t_sc_bill
				where id = '%s' ";
    $data = $db->query($sql, $id);
    if ($data) {
      return [
        "id" => $id,
        "ref" => $data[0]["ref"],
        "billStatus" => $data[0]["bill_status"],
        "dataOrg" => $data[0]["data_org"]
      ];
    } else {
      return null;
    }
  }

  /**
   * 编辑销售合同
   *
   * @param array $params
   * @return array|null
   */
  public function updateSCBill(&$bill)
  {
    $db = $this->db;

    // 销售合同主表id
    $id = $bill["id"];
    $b = $this->getSCBillById($id);
    if (!$b) {
      return $this->bad("要编辑的销售合同不存在");
    }
    $ref = $b["ref"];
    $billStatus = $b["billStatus"];
    if ($billStatus > 0) {
      return $this->bad("销售合同[合同号：{$ref}]已经提交审核，不能再次编辑");
    }
    $dataOrg = $b["dataOrg"];
    if ($this->dataOrgNotExists($dataOrg)) {
      return $this->badParam("dataOrg");
    }

    $companyId = $bill["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->badParam("companyId");
    }

    $customerId = $bill["customerId"];
    $customerDAO = new CustomerDAO($db);
    $customer = $customerDAO->getCustomerById($customerId);
    if (!$customer) {
      return $this->bad("甲方客户不存在");
    }

    $beginDT = $bill["beginDT"];
    if (!$this->dateIsValid($beginDT)) {
      return $this->bad("合同开始日期不正确");
    }
    $endDT = $bill["endDT"];
    if (!$this->dateIsValid($endDT)) {
      return $this->bad("合同结束日期不正确");
    }

    $orgId = $bill["orgId"];
    $orgDAO = new OrgDAO($db);
    $org = $orgDAO->getOrgById($orgId);
    if (!$org) {
      return $this->bad("乙方组织机构不存在");
    }

    $dealDate = $bill["dealDate"];
    if (!$this->dateIsValid($dealDate)) {
      return $this->bad("交货日期不正确");
    }

    $bizDT = $bill["bizDT"];
    if (!$this->dateIsValid($bizDT)) {
      return $this->bad("合同签订日期不正确");
    }
    $bizUserId = $bill["bizUserId"];
    $userDAO = new UserDAO($db);
    $user = $userDAO->getUserById($bizUserId);
    if (!$user) {
      return $this->bad("业务员不存在");
    }

    $dealAddress = $bill["dealAddress"];
    $discount = intval($bill["discount"]);
    if ($discount < 0 || $discount > 100) {
      $discount = 100;
    }

    $billMemo = $bill["billMemo"];
    $qualityClause = $bill["qualityClause"];
    $insuranceClause = $bill["insuranceClause"];
    $transportClause = $bill["transportClause"];
    $otherClause = $bill["otherClause"];

    $items = $bill["items"];

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    // 主表
    $sql = "update t_sc_bill
				set customer_id = '%s', begin_dt = '%s', end_dt = '%s',
					org_id = '%s', biz_dt = '%s', deal_date = '%s',
					deal_address = '%s', biz_user_id = '%s', discount = %d,
					bill_memo = '%s', quality_clause = '%s',
					insurance_clause = '%s', transport_clause = '%s',
					other_clause = '%s'
				where id = '%s' ";
    $rc = $db->execute(
      $sql,
      $customerId,
      $beginDT,
      $endDT,
      $orgId,
      $bizDT,
      $dealDate,
      $dealAddress,
      $bizUserId,
      $discount,
      $billMemo,
      $qualityClause,
      $insuranceClause,
      $transportClause,
      $otherClause,
      $id
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 明细表
    $sql = "delete from t_sc_bill_detail where scbill_id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    foreach ($items as $i => $v) {
      $goodsId = $v["goodsId"];
      if (!$goodsId) {
        continue;
      }
      $goodsCount = $v["goodsCount"];
      $goodsPrice = $v["goodsPrice"];
      $goodsPriceWithTax = $v["goodsPriceWithTax"];
      $goodsMoney = $v["goodsMoney"];
      $taxRate = $v["taxRate"];
      $tax = $v["tax"];
      $moneyWithTax = $v["moneyWithTax"];
      $memo = $v["memo"];

      $sql = "insert into t_sc_bill_detail(id, date_created, goods_id, goods_count, goods_money,
						goods_price, scbill_id, tax_rate, tax, money_with_tax, so_count, left_count,
						show_order, data_org, company_id, memo, discount, goods_price_with_tax)
					values ('%s', now(), '%s', convert(%f, $fmt), %f,
						%f, '%s', %d, %f, %f, 0, convert(%f, $fmt), %d, '%s', '%s', '%s', %d, %f)";
      $rc = $db->execute(
        $sql,
        $this->newId(),
        $goodsId,
        $goodsCount,
        $goodsMoney,
        $goodsPrice,
        $id,
        $taxRate,
        $tax,
        $moneyWithTax,
        $goodsCount,
        $i,
        $dataOrg,
        $companyId,
        $memo,
        $discount,
        $goodsPriceWithTax
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 同步主表销售金额
    $sql = "select sum(goods_money) as sum_goods_money, sum(tax) as sum_tax,
					sum(money_with_tax) as sum_money_with_tax
				from t_sc_bill_detail
				where scbill_id = '%s' ";
    $data = $db->query($sql, $id);
    $sumGoodsMoney = $data[0]["sum_goods_money"];
    if (!$sumGoodsMoney) {
      $sumGoodsMoney = 0;
    }
    $sumTax = $data[0]["sum_tax"];
    if (!$sumTax) {
      $sumTax = 0;
    }
    $sumMoneyWithTax = $data[0]["sum_money_with_tax"];
    if (!$sumMoneyWithTax) {
      $sumMoneyWithTax = 0;
    }

    $sql = "update t_sc_bill
				set goods_money = %f, tax = %f, money_with_tax = %f
				where id = '%s' ";
    $rc = $db->execute($sql, $sumGoodsMoney, $sumTax, $sumMoneyWithTax, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $bill["ref"] = $ref;
    return null;
  }

  /**
   * 销售合同商品明细
   */
  public function scBillDetailList($params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    // 销售合同主表id
    $id = $params["id"];

    $sql = "select quality_clause, insurance_clause, transport_clause, other_clause
				from t_sc_bill where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return $this->emptyResult();
    }

    $v = $data[0];
    $result = [
      "qualityClause" => $v["quality_clause"],
      "insuranceClause" => $v["insurance_clause"],
      "transportClause" => $v["transport_clause"],
      "otherClause" => $v["other_clause"]
    ];

    $sql = "select s.id, g.code, g.name, g.spec, convert(s.goods_count, " . $fmt . ") as goods_count,
					s.goods_price, s.goods_money,
					s.tax_rate, s.tax, s.money_with_tax, u.name as unit_name,
					convert(s.so_count, " . $fmt . ") as so_count,
					convert(s.left_count, " . $fmt . ") as left_count, s.memo,
					s.goods_price_with_tax
				from t_sc_bill_detail s, t_goods g, t_goods_unit u
				where s.scbill_id = '%s' and s.goods_id = g.id and g.unit_id = u.id
				order by s.show_order";
    $items = [];
    $data = $db->query($sql, $id);

    foreach ($data as $v) {
      $goodsPriceWithTax = $v["goods_price_with_tax"];
      if ($goodsPriceWithTax == null) {
        // 兼容旧数据
        if ($v["goods_count"] != 0) {
          $goodsPriceWithTax = $v["money_with_tax"] / $v["goods_count"];
        }
      }
      $items[] = [
        "id" => $v["id"],
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
        "soCount" => $v["so_count"],
        "leftCount" => $v["left_count"],
        "memo" => $v["memo"],
        "goodsPriceWithTax" => $goodsPriceWithTax
      ];
    }

    $result["items"] = $items;

    return $result;
  }

  /**
   * 删除销售合同
   */
  public function deleteSCBill(&$params)
  {
    $db = $this->db;

    // 销售合同主表
    $id = $params["id"];

    $bill = $this->getSCBillById($id);
    if (!$bill) {
      return $this->bad("要删除的销售合同不存在");
    }
    $ref = $bill["ref"];
    $billStatus = $bill["billStatus"];
    if ($billStatus > 0) {
      return $this->bad("销售合同[合同号：{$ref}]已经审核，不能被删除");
    }

    // 删除明细表
    $sql = "delete from t_sc_bill_detail where scbill_id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 删除主表
    $sql = "delete from t_sc_bill where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $params["ref"] = $ref;
    return null;
  }

  /**
   * 审核销售合同
   */
  public function commitSCBill(&$params)
  {
    $db = $this->db;

    // 销售合同主表
    $id = $params["id"];

    $bill = $this->getSCBillById($id);
    if (!$bill) {
      return $this->bad("要审核的销售合同不存在");
    }
    $ref = $bill["ref"];
    $billStatus = $bill["billStatus"];
    if ($billStatus > 0) {
      return $this->bad("销售合同[合同号：{$ref}]已经审核");
    }

    $sql = "update t_sc_bill
				set bill_status = 1000
				where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $params["ref"] = $ref;
    return null;
  }

  /**
   * 取消审核销售合同
   */
  public function cancelConfirmSCBill(&$params)
  {
    $db = $this->db;

    // 销售合同主表
    $id = $params["id"];

    $bill = $this->getSCBillById($id);
    if (!$bill) {
      return $this->bad("要取消审核的销售合同不存在");
    }
    $ref = $bill["ref"];
    $billStatus = $bill["billStatus"];
    if ($billStatus == 0) {
      return $this->bad("销售合同[合同号：{$ref}]还没有审核，无需取消");
    }

    // 检查是否生成了销售订单
    $sql = "select count(*) as cnt
				from t_sc_so
				where sc_id = '%s' ";
    $data = $db->query($sql, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("销售合同[合同号：{$ref}]已经生成了销售订单，不能再取消");
    }

    $sql = "update t_sc_bill
				set bill_status = 0
				where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $params["ref"] = $ref;
    return null;
  }

  /**
   * 为销售合同生成Word文件查询数据
   */
  public function getDataForWord($params)
  {
    return $this->getDataForPDF($params);
  }

  /**
   * 为销售合同生成PDF文件查询数据
   *
   * @param array $params
   */
  public function getDataForPDF($params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $ref = $params["ref"];

    $sql = "select s.id, s.bill_status, c.name as customer_name,
					u.name as input_user_name, g.full_name as org_name,
					s.begin_dt, s.end_dt, s.goods_money, s.tax, s.money_with_tax,
					s.deal_date, s.deal_address, s.bill_memo, s.discount,
					u2.name as biz_user_name, s.biz_dt, s.date_created,
					s.quality_clause, s.insurance_clause, s.transport_clause, s.other_clause
				from t_sc_bill s, t_customer c, t_user u, t_org g, t_user u2
				where (s.customer_id = c.id) and (s.input_user_id = u.id)
					and (s.org_id = g.id) and (s.biz_user_id = u2.id) 
					and (s.ref = '%s')";
    $data = $db->query($sql, $ref);
    if (!$data) {
      return $this->emptyResult();
    }

    $v = $data[0];

    $id = $v["id"];

    $result = [
      "ref" => $ref,
      "billStatus" => $v["bill_status"],
      "customerName" => $v["customer_name"],
      "orgName" => $v["org_name"],
      "beginDT" => $this->toYMD($v["begin_dt"]),
      "endDT" => $this->toYMD($v["end_dt"]),
      "goodsMoney" => $v["goods_money"],
      "tax" => $v["tax"],
      "moneyWithTax" => $v["money_with_tax"],
      "dealDate" => $this->toYMD($v["deal_date"]),
      "dealAddress" => $v["deal_address"],
      "discount" => $v["discount"],
      "bizUserName" => $v["biz_user_name"],
      "bizDT" => $this->toYMD($v["biz_dt"]),
      "billMemo" => $v["bill_memo"],
      "inputUserName" => $v["input_user_name"],
      "dateCreated" => $v["date_created"],
      "qualityClause" => $v["quality_clause"],
      "insuranceClause" => $v["insurance_clause"],
      "transportClause" => $v["transport_clause"],
      "otherClause" => $v["other_clause"]
    ];

    $sql = "select g.code, g.name, g.spec, convert(s.goods_count, " . $fmt . ") as goods_count,
					s.goods_price, s.goods_money,
					s.tax_rate, s.tax, s.money_with_tax, u.name as unit_name,
					convert(s.so_count, " . $fmt . ") as so_count,
					convert(s.left_count, " . $fmt . ") as left_count, s.memo
				from t_sc_bill_detail s, t_goods g, t_goods_unit u
				where s.scbill_id = '%s' and s.goods_id = g.id and g.unit_id = u.id
				order by s.show_order";
    $items = [];
    $data = $db->query($sql, $id);

    foreach ($data as $v) {
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
        "soCount" => $v["so_count"],
        "leftCount" => $v["left_count"],
        "memo" => $v["memo"]
      ];
    }

    $result["items"] = $items;

    return $result;
  }

  /**
   * 查询销售合同的数据，用于Lodop打印
   *
   * @param array $params
   */
  public function getSCBillDataForLodopPrint($params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $id = $params["id"];

    $sql = "select s.ref, s.bill_status, c.name as customer_name,
					u.name as input_user_name, g.full_name as org_name,
					s.begin_dt, s.end_dt, s.goods_money, s.tax, s.money_with_tax,
					s.deal_date, s.deal_address, s.bill_memo, s.discount,
					u2.name as biz_user_name, s.biz_dt, s.date_created,
					s.quality_clause, s.insurance_clause, s.transport_clause, s.other_clause
				from t_sc_bill s, t_customer c, t_user u, t_org g, t_user u2
				where (s.customer_id = c.id) and (s.input_user_id = u.id)
					and (s.org_id = g.id) and (s.biz_user_id = u2.id)
					and (s.id = '%s')";
    $data = $db->query($sql, $id);
    if (!$data) {
      return $this->emptyResult();
    }

    $v = $data[0];

    $result = [
      "ref" => $v["ref"],
      "billStatus" => $v["bill_status"],
      "customerName" => $v["customer_name"],
      "orgName" => $v["org_name"],
      "beginDT" => $this->toYMD($v["begin_dt"]),
      "endDT" => $this->toYMD($v["end_dt"]),
      "goodsMoney" => $v["goods_money"],
      "tax" => $v["tax"],
      "moneyWithTax" => $v["money_with_tax"],
      "dealDate" => $this->toYMD($v["deal_date"]),
      "dealAddress" => $v["deal_address"],
      "discount" => $v["discount"],
      "bizUserName" => $v["biz_user_name"],
      "bizDT" => $this->toYMD($v["biz_dt"]),
      "billMemo" => $v["bill_memo"],
      "inputUserName" => $v["input_user_name"],
      "dateCreated" => $v["date_created"],
      "qualityClause" => $v["quality_clause"],
      "insuranceClause" => $v["insurance_clause"],
      "transportClause" => $v["transport_clause"],
      "otherClause" => $v["other_clause"],
      "printDT" => date("Y-m-d H:i:s")
    ];

    $sql = "select g.code, g.name, g.spec, convert(s.goods_count, " . $fmt . ") as goods_count,
					s.goods_price, s.goods_money,
					s.tax_rate, s.tax, s.money_with_tax, u.name as unit_name,
					convert(s.so_count, " . $fmt . ") as so_count,
					convert(s.left_count, " . $fmt . ") as left_count, s.memo
				from t_sc_bill_detail s, t_goods g, t_goods_unit u
				where s.scbill_id = '%s' and s.goods_id = g.id and g.unit_id = u.id
				order by s.show_order";
    $items = [];
    $data = $db->query($sql, $id);

    foreach ($data as $v) {
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
        "soCount" => $v["so_count"],
        "leftCount" => $v["left_count"],
        "memo" => $v["memo"]
      ];
    }

    $result["items"] = $items;

    return $result;
  }
}
