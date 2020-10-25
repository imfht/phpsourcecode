<?php

namespace Home\DAO;

use Home\Common\FIdConst;

/**
 * 销售订单 DAO
 *
 * @author 李静波
 */
class SOBillDAO extends PSIBaseExDAO
{

  /**
   * 生成新的销售订单号
   *
   * @param string $companyId
   * @return string
   */
  private function genNewBillRef($companyId)
  {
    $db = $this->db;

    $bs = new BizConfigDAO($db);
    $pre = $bs->getSOBillRefPre($companyId);

    $mid = date("Ymd");

    $sql = "select ref from t_so_bill where ref like '%s' order by ref desc limit 1";
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
   * 获得销售订单主表信息列表
   *
   * @param array $params
   * @return array
   */
  public function sobillList($params)
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
    $receivingType = $params["receivingType"];
    $goodsId = $params["goodsId"];
    $userId = $params["userId"];

    $queryParams = array();

    $result = array();
    $sql = "select s.id, s.ref, s.bill_status, s.goods_money, s.tax, s.money_with_tax,
              c.name as customer_name, s.contact, s.tel, s.fax, s.deal_address,
              s.deal_date, s.receiving_type, s.bill_memo, s.date_created,
              o.full_name as org_name, u1.name as biz_user_name, u2.name as input_user_name,
              s.confirm_user_id, s.confirm_date
            from t_so_bill s, t_customer c, t_org o, t_user u1, t_user u2
            where (s.customer_id = c.id) and (s.org_id = o.id)
              and (s.biz_user_id = u1.id) and (s.input_user_id = u2.id) ";

    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::SALE_ORDER, "s", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = $rs[1];
    }

    if ($billStatus != -1) {
      if ($billStatus < 4000) {
        $sql .= " and (s.bill_status = %d) ";
      } else {
        // 订单关闭 - 有多种状态
        $sql .= " and (s.bill_status >= %d) ";
      }
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
    if ($receivingType != -1) {
      $sql .= " and (s.receiving_type = %d) ";
      $queryParams[] = $receivingType;
    }
    if ($goodsId) {
      $sql .= " and (s.id in (select distinct sobill_id from t_so_bill_detail where goods_id = '%s'))";
      $queryParams[] = $goodsId;
    }
    if ($userId) {
      $sql .= " and (s.input_user_id = '%s') ";
      $queryParams[] = $userId;
    }
    $sql .= " order by s.deal_date desc, s.ref desc
              limit %d , %d";
    $queryParams[] = $start;
    $queryParams[] = $limit;
    $data = $db->query($sql, $queryParams);
    foreach ($data as $i => $v) {
      $result[$i]["id"] = $v["id"];
      $result[$i]["ref"] = $v["ref"];
      $result[$i]["billStatus"] = $v["bill_status"];
      $result[$i]["dealDate"] = $this->toYMD($v["deal_date"]);
      $result[$i]["dealAddress"] = $v["deal_address"];
      $result[$i]["customerName"] = $v["customer_name"];
      $result[$i]["contact"] = $v["contact"];
      $result[$i]["tel"] = $v["tel"];
      $result[$i]["fax"] = $v["fax"];
      $result[$i]["goodsMoney"] = $v["goods_money"];
      $result[$i]["tax"] = $v["tax"];
      $result[$i]["moneyWithTax"] = $v["money_with_tax"];
      $result[$i]["receivingType"] = $v["receiving_type"];
      $result[$i]["billMemo"] = $v["bill_memo"];
      $result[$i]["bizUserName"] = $v["biz_user_name"];
      $result[$i]["orgName"] = $v["org_name"];
      $result[$i]["inputUserName"] = $v["input_user_name"];
      $result[$i]["dateCreated"] = $v["date_created"];

      $confirmUserId = $v["confirm_user_id"];
      if ($confirmUserId) {
        $sql = "select name from t_user where id = '%s' ";
        $d = $db->query($sql, $confirmUserId);
        if ($d) {
          $result[$i]["confirmUserName"] = $d[0]["name"];
          $result[$i]["confirmDate"] = $v["confirm_date"];
        }
      }

      // 查询是否生成了销售出库单
      $sql = "select count(*) as cnt from t_so_ws
              where so_id = '%s' ";
      $d = $db->query($sql, $v["id"]);
      $cnt = $d[0]["cnt"];
      $genPWBill = $cnt > 0 ? "▲" : "";
      $result[$i]["genPWBill"] = $genPWBill;
    }

    $sql = "select count(*) as cnt
            from t_so_bill s, t_customer c, t_org o, t_user u1, t_user u2
            where (s.customer_id = c.id) and (s.org_id = o.id)
              and (s.biz_user_id = u1.id) and (s.input_user_id = u2.id)
            ";
    $queryParams = array();
    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::SALE_ORDER, "s", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = $rs[1];
    }
    if ($billStatus != -1) {
      if ($billStatus < 4000) {
        $sql .= " and (s.bill_status = %d) ";
      } else {
        // 订单关闭 - 有多种状态
        $sql .= " and (s.bill_status >= %d) ";
      }
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
    if ($receivingType != -1) {
      $sql .= " and (s.receiving_type = %d) ";
      $queryParams[] = $receivingType;
    }
    if ($goodsId) {
      $sql .= " and (s.id in (select distinct sobill_id from t_so_bill_detail where goods_id = '%s'))";
      $queryParams[] = $goodsId;
    }
    if ($userId) {
      $sql .= " and (s.input_user_id = '%s') ";
      $queryParams[] = $userId;
    }
    $data = $db->query($sql, $queryParams);
    $cnt = $data[0]["cnt"];

    return array(
      "dataList" => $result,
      "totalCount" => $cnt
    );
  }

  /**
   * 获得某个销售订单的明细信息
   *
   * @param array $params
   * @return array
   */
  public function soBillDetailList($params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    // id:销售订单id
    $id = $params["id"];

    $sql = "select s.id, g.code, g.name, g.spec, convert(s.goods_count, " . $fmt . ") as goods_count,
              s.goods_price, s.goods_money,
              s.tax_rate, s.tax, s.money_with_tax, u.name as unit_name,
              convert(s.ws_count, " . $fmt . ") as ws_count,
              convert(s.left_count, " . $fmt . ") as left_count, s.memo, s.goods_price_with_tax
            from t_so_bill_detail s, t_goods g, t_goods_unit u
            where s.sobill_id = '%s' and s.goods_id = g.id and g.unit_id = u.id
            order by s.show_order";
    $result = array();
    $data = $db->query($sql, $id);

    foreach ($data as $v) {
      $goodsPriceWithTax = $v["goods_price_with_tax"];
      if ($goodsPriceWithTax == null) {
        // 兼容旧数据
        if ($v["goods_count"] != 0) {
          $goodsPriceWithTax = $v["money_with_tax"] / $v["goods_count"];
        }
      }
      $result[] = [
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
        "wsCount" => $v["ws_count"],
        "leftCount" => $v["left_count"],
        "memo" => $v["memo"],
        "goodsPriceWithTax" => $goodsPriceWithTax
      ];
    }

    return $result;
  }

  /**
   * 新建销售订单
   *
   * @param array $bill
   * @return null|array
   */
  public function addSOBill(&$bill)
  {
    $db = $this->db;

    $dealDate = $bill["dealDate"];
    if (!$this->dateIsValid($dealDate)) {
      return $this->bad("交货日期不正确");
    }

    $customerId = $bill["customerId"];
    $customerDAO = new CustomerDAO($db);
    $customer = $customerDAO->getCustomerById($customerId);
    if (!$customer) {
      return $this->bad("客户不存在");
    }

    $orgId = $bill["orgId"];
    $orgDAO = new OrgDAO($db);
    $org = $orgDAO->getOrgById($orgId);
    if (!$org) {
      return $this->bad("组织机构不存在");
    }

    $bizUserId = $bill["bizUserId"];
    $userDAO = new UserDAO($db);
    $user = $userDAO->getUserById($bizUserId);
    if (!$user) {
      return $this->bad("业务员不存在");
    }

    $receivingType = $bill["receivingType"];
    $contact = $bill["contact"];
    $tel = $bill["tel"];
    $fax = $bill["fax"];
    $dealAddress = $bill["dealAddress"];
    $billMemo = $bill["billMemo"];

    $items = $bill["items"];

    $companyId = $bill["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->bad("所属公司不存在");
    }

    $dataOrg = $bill["dataOrg"];
    if ($this->dataOrgNotExists($dataOrg)) {
      return $this->badParam("dataOrg");
    }

    $loginUserId = $bill["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->badParam("loginUserId");
    }

    // 销售合同号
    // 当销售订单是由销售合同创建的时候，销售合同号就不为空
    $scbillRef = $bill["scbillRef"];

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $id = $this->newId();
    $ref = $this->genNewBillRef($companyId);

    // 主表
    $sql = "insert into t_so_bill(id, ref, bill_status, deal_date, biz_dt, org_id, biz_user_id,
              goods_money, tax, money_with_tax, input_user_id, customer_id, contact, tel, fax,
              deal_address, bill_memo, receiving_type, date_created, data_org, company_id)
            values ('%s', '%s', 0, '%s', '%s', '%s', '%s',
              0, 0, 0, '%s', '%s', '%s', '%s', '%s',
              '%s', '%s', %d, now(), '%s', '%s')";
    $rc = $db->execute(
      $sql,
      $id,
      $ref,
      $dealDate,
      $dealDate,
      $orgId,
      $bizUserId,
      $loginUserId,
      $customerId,
      $contact,
      $tel,
      $fax,
      $dealAddress,
      $billMemo,
      $receivingType,
      $dataOrg,
      $companyId
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 明细记录
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
      $scbillDetailId = $v["scbillDetailId"];

      $sql = "insert into t_so_bill_detail(id, date_created, goods_id, goods_count, goods_money,
                goods_price, sobill_id, tax_rate, tax, money_with_tax, ws_count, left_count,
                show_order, data_org, company_id, memo, scbilldetail_id, goods_price_with_tax)
              values ('%s', now(), '%s', convert(%f, $fmt), %f,
                %f, '%s', %d, %f, %f, 0, convert(%f, $fmt), %d, '%s', '%s', '%s', '%s', %f)";
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
        $scbillDetailId,
        $goodsPriceWithTax
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 同步主表的金额合计字段
    $sql = "select sum(goods_money) as sum_goods_money, sum(tax) as sum_tax,
              sum(money_with_tax) as sum_money_with_tax
            from t_so_bill_detail
            where sobill_id = '%s' ";
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

    $sql = "update t_so_bill
            set goods_money = %f, tax = %f, money_with_tax = %f
            where id = '%s' ";
    $rc = $db->execute($sql, $sumGoodsMoney, $sumTax, $sumMoneyWithTax, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 关联销售合同和销售订单
    if ($scbillRef) {
      $sql = "select id from t_sc_bill where ref = '%s' ";
      $data = $db->query($sql, $scbillRef);
      if ($data) {
        $scbillId = $data[0]["id"];

        $sql = "insert into t_sc_so(sc_id, so_id) values ('%s', '%s')";
        $rc = $db->execute($sql, $scbillId, $id);
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      }
    }

    // 操作成功
    $bill["id"] = $id;
    $bill["ref"] = $ref;

    return null;
  }

  /**
   * 通过销售订单id查询销售订单
   *
   * @param string $id
   * @return array|NULL
   */
  public function getSOBillById($id)
  {
    $db = $this->db;

    $sql = "select ref, data_org, bill_status, company_id from t_so_bill where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return null;
    } else {
      return array(
        "ref" => $data[0]["ref"],
        "dataOrg" => $data[0]["data_org"],
        "billStatus" => $data[0]["bill_status"],
        "companyId" => $data[0]["company_id"]
      );
    }
  }

  /**
   * 编辑销售订单
   *
   * @param array $bill
   * @return null|array
   */
  public function updateSOBill(&$bill)
  {
    $db = $this->db;

    $id = $bill["id"];

    $dealDate = $bill["dealDate"];
    if (!$this->dateIsValid($dealDate)) {
      return $this->bad("交货日期不正确");
    }

    $customerId = $bill["customerId"];
    $customerDAO = new CustomerDAO($db);
    $customer = $customerDAO->getCustomerById($customerId);
    if (!$customer) {
      return $this->bad("客户不存在");
    }

    $orgId = $bill["orgId"];
    $orgDAO = new OrgDAO($db);
    $org = $orgDAO->getOrgById($orgId);
    if (!$org) {
      return $this->bad("组织机构不存在");
    }

    $bizUserId = $bill["bizUserId"];
    $userDAO = new UserDAO($db);
    $user = $userDAO->getUserById($bizUserId);
    if (!$user) {
      return $this->bad("业务员不存在");
    }

    $receivingType = $bill["receivingType"];
    $contact = $bill["contact"];
    $tel = $bill["tel"];
    $fax = $bill["fax"];
    $dealAddress = $bill["dealAddress"];
    $billMemo = $bill["billMemo"];

    $items = $bill["items"];

    $loginUserId = $bill["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->badParam("loginUserId");
    }

    $oldBill = $this->getSOBillById($id);

    if (!$oldBill) {
      return $this->bad("要编辑的销售订单不存在");
    }
    $ref = $oldBill["ref"];
    $dataOrg = $oldBill["dataOrg"];
    $companyId = $oldBill["companyId"];
    $billStatus = $oldBill["billStatus"];
    if ($billStatus != 0) {
      return $this->bad("当前销售订单已经审核，不能再编辑");
    }

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $sql = "delete from t_so_bill_detail where sobill_id = '%s' ";
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
      $scbillDetailId = $v["scbillDetailId"];

      $sql = "insert into t_so_bill_detail(id, date_created, goods_id, goods_count, goods_money,
                goods_price, sobill_id, tax_rate, tax, money_with_tax, ws_count, left_count,
                show_order, data_org, company_id, memo, scbilldetail_id, goods_price_with_tax)
              values ('%s', now(), '%s', convert(%f, $fmt), %f,
                %f, '%s', %d, %f, %f, 0, convert(%f, $fmt), %d, '%s', '%s', '%s', '%s', %f)";
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
        $scbillDetailId,
        $goodsPriceWithTax
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 同步主表的金额合计字段
    $sql = "select sum(goods_money) as sum_goods_money, sum(tax) as sum_tax,
              sum(money_with_tax) as sum_money_with_tax
            from t_so_bill_detail
            where sobill_id = '%s' ";
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

    $sql = "update t_so_bill
            set goods_money = %f, tax = %f, money_with_tax = %f,
              deal_date = '%s', customer_id = '%s',
              deal_address = '%s', contact = '%s', tel = '%s', fax = '%s',
              org_id = '%s', biz_user_id = '%s', receiving_type = %d,
              bill_memo = '%s', input_user_id = '%s', date_created = now()
            where id = '%s' ";
    $rc = $db->execute(
      $sql,
      $sumGoodsMoney,
      $sumTax,
      $sumMoneyWithTax,
      $dealDate,
      $customerId,
      $dealAddress,
      $contact,
      $tel,
      $fax,
      $orgId,
      $bizUserId,
      $receivingType,
      $billMemo,
      $loginUserId,
      $id
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $bill["ref"] = $ref;

    return null;
  }

  /**
   * 删除销售订单
   *
   * @param array $params
   * @return null|array
   */
  public function deleteSOBill(&$params)
  {
    $db = $this->db;

    $id = $params["id"];

    $bill = $this->getSOBillById($id);

    if (!$bill) {
      return $this->bad("要删除的销售订单不存在");
    }
    $ref = $bill["ref"];
    $billStatus = $bill["billStatus"];
    if ($billStatus > 0) {
      return $this->bad("销售订单(单号：{$ref})已经审核，不能被删除");
    }

    $sql = "delete from t_so_bill_detail where sobill_id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $sql = "delete from t_so_bill where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 删除销售订单和销售合同的关联
    $sql = "delete from t_sc_so where so_id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $params["ref"] = $ref;

    return null;
  }

  /**
   * 获得销售订单的信息
   *
   * @param array $params
   * @return array
   */
  public function soBillInfo($params)
  {
    $db = $this->db;

    $id = $params["id"];

    // 销售合同号
    // 当从销售合同创建销售订单的时候，这个值就不为空
    $scbillRef = $params["scbillRef"];

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    $result = [];

    $cs = new BizConfigDAO($db);
    $result["taxRate"] = $cs->getTaxRate($companyId);

    $dataScale = $cs->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    if ($id) {
      // 编辑销售订单
      $sql = "select s.ref, s.deal_date, s.deal_address, s.customer_id,
                c.name as customer_name, s.contact, s.tel, s.fax,
                s.org_id, o.full_name, s.biz_user_id, u.name as biz_user_name,
                s.receiving_type, s.bill_memo, s.bill_status
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
        $result["customerId"] = $v["customer_id"];
        $result["customerName"] = $v["customer_name"];
        $result["contact"] = $v["contact"];
        $result["tel"] = $v["tel"];
        $result["fax"] = $v["fax"];
        $result["orgId"] = $v["org_id"];
        $result["orgFullName"] = $v["full_name"];
        $result["bizUserId"] = $v["biz_user_id"];
        $result["bizUserName"] = $v["biz_user_name"];
        $result["receivingType"] = $v["receiving_type"];
        $result["billMemo"] = $v["bill_memo"];
        $result["billStatus"] = $v["bill_status"];

        // 明细表
        $sql = "select s.id, s.goods_id, g.code, g.name, g.spec, 
                  convert(s.goods_count, " . $fmt . ") as goods_count, s.goods_price, s.goods_money,
                  s.tax_rate, s.tax, s.money_with_tax, u.name as unit_name, s.memo, s.scbilldetail_id,
                  s.goods_price_with_tax
                from t_so_bill_detail s, t_goods g, t_goods_unit u
                where s.sobill_id = '%s' and s.goods_id = g.id and g.unit_id = u.id
                order by s.show_order";
        $items = array();
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
            "scbillDetailId" => $v["scbilldetail_id"],
            "goodsPriceWithTax" => $goodsPriceWithTax
          ];
        }

        $result["items"] = $items;

        // 查询当前销售订单是不是由销售合同创建
        $sql = "select count(*) as cnt from t_sc_so where so_id = '%s' ";
        $data = $db->query($sql, $id);
        $cnt = $data[0]["cnt"];
        $result["genBill"] = $cnt > 0 ? "1" : "0";
      }
    } else {
      // 新建销售订单

      if ($scbillRef) {
        // 从销售合同创建销售订单
        $sql = "select s.id, s.deal_date, s.deal_address,
                  s.customer_id, c.name as customer_name,
                  s.org_id, g.full_name as org_full_name
                from t_sc_bill s, t_customer c, t_org g
                where s.ref = '%s' and s.customer_id = c.id
                  and s.org_id = g.id";
        $data = $db->query($sql, $scbillRef);
        if (!$data) {
          // 这个时候多半是参数传递错误了
          return $this->emptyResult();
        }
        $v = $data[0];
        $result["genBill"] = 1;
        $result["customerId"] = $v["customer_id"];
        $result["customerName"] = $v["customer_name"];
        $result["dealDate"] = $this->toYMD($v["deal_date"]);
        $result["dealAddress"] = $v["deal_address"];
        $result["orgId"] = $v["org_id"];
        $result["orgFullName"] = $v["org_full_name"];

        $scBillId = $v["id"];
        // 从销售合同查询商品明细
        $sql = "select s.id, s.goods_id, g.code, g.name, g.spec,
                  convert(s.left_count, " . $fmt . ") as goods_count, s.goods_price,
                  s.tax_rate, u.name as unit_name, s.goods_price_with_tax
                from t_sc_bill_detail s, t_goods g, t_goods_unit u
                where s.scbill_id = '%s' and s.goods_id = g.id and g.unit_id = u.id
                order by s.show_order";
        $items = [];
        $data = $db->query($sql, $scBillId);

        foreach ($data as $v) {
          $goodsMoney = $v["goods_count"] * $v["goods_price"];
          $tax = $goodsMoney * $v["tax_rate"] / 100;
          $goodsPriceWithTax = $v["goods_price_with_tax"];
          if ($goodsPriceWithTax == null) {
            // 兼容旧数据
            if ($v["goods_count"] != 0) {
              $goodsPriceWithTax = ($goodsMoney + $tax) / $v["goods_count"];
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
            "goodsMoney" => $goodsMoney,
            "taxRate" => $v["tax_rate"],
            "tax" => $tax,
            "moneyWithTax" => $goodsMoney + $tax,
            "unitName" => $v["unit_name"],
            "scbillDetailId" => $v["id"],
            "goodsPriceWithTax" => $goodsPriceWithTax
          ];
        }

        $result["items"] = $items;

        $loginUserId = $params["loginUserId"];
        $result["bizUserId"] = $loginUserId;
        $result["bizUserName"] = $params["loginUserName"];
      } else {
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

      // 默认收款方式
      $bc = new BizConfigDAO($db);
      $result["receivingType"] = $bc->getSOBillDefaultReceving($companyId);
    }

    return $result;
  }

  /**
   * 审核销售订单
   *
   * @param array $params
   * @return null|array
   */
  public function commitSOBill(&$params)
  {
    $db = $this->db;

    $loginUserId = $params["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->badParam("loginUserId");
    }

    $id = $params["id"];

    $bill = $this->getSOBillById($id);

    if (!$bill) {
      return $this->bad("要审核的销售订单不存在");
    }
    $ref = $bill["ref"];
    $billStatus = $bill["billStatus"];
    if ($billStatus > 0) {
      return $this->bad("销售订单(单号：$ref)已经被审核，不能再次审核");
    }

    $sql = "update t_so_bill
            set bill_status = 1000,
              confirm_user_id = '%s',
              confirm_date = now()
            where id = '%s' ";
    $rc = $db->execute($sql, $loginUserId, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $params["ref"] = $ref;

    return null;
  }

  /**
   * 取消销售订单审核
   */
  public function cancelConfirmSOBill(&$params)
  {
    $db = $this->db;

    $id = $params["id"];

    $bill = $this->getSOBillById($id);

    if (!$bill) {
      return $this->bad("要取消审核的销售订单不存在");
    }
    $ref = $bill["ref"];
    $billStatus = $bill["billStatus"];
    if ($billStatus == 0) {
      return $this->bad("销售订单(单号:{$ref})还没有审核，无需取消审核操作");
    }
    if ($billStatus > 1000) {
      return $this->bad("销售订单(单号:{$ref})不能取消审核");
    }

    $sql = "select count(*) as cnt from t_so_ws where so_id = '%s' ";
    $data = $db->query($sql, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("销售订单(单号:{$ref})已经生成了销售出库单，不能取消审核");
    }

    $sql = "update t_so_bill
            set bill_status = 0, confirm_user_id = null, confirm_date = null
            where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $params["ref"] = $ref;

    // 操作成功
    return null;
  }

  /**
   * 销售订单生成pdf文件
   */
  public function getDataForPDF($params)
  {
    $ref = $params["ref"];

    $db = $this->db;
    $sql = "select s.id, s.bill_status, s.goods_money, s.tax, s.money_with_tax,
              c.name as customer_name, s.contact, s.tel, s.fax, s.deal_address,
              s.deal_date, s.receiving_type, s.bill_memo, s.date_created,
              o.full_name as org_name, u1.name as biz_user_name, u2.name as input_user_name,
              s.confirm_user_id, s.confirm_date, s.company_id
            from t_so_bill s, t_customer c, t_org o, t_user u1, t_user u2
            where (s.customer_id = c.id) and (s.org_id = o.id)
              and (s.biz_user_id = u1.id) and (s.input_user_id = u2.id) 
              and (s.ref = '%s')";
    $data = $db->query($sql, $ref);
    if (!$data) {
      return null;
    }

    $id = $data[0]["id"];

    $companyId = $data[0]["company_id"];
    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $bill = [];
    $bill["dealDate"] = $this->toYMD($data[0]["deal_date"]);
    $bill["customerName"] = $data[0]["customer_name"];
    $bill["warehouseName"] = $data[0]["warehouse_name"];
    $bill["bizUserName"] = $data[0]["biz_user_name"];
    $bill["saleMoney"] = $data[0]["goods_money"];
    $bill["tax"] = $data[0]["tax"];
    $bill["moneyWithTax"] = $data[0]["money_with_tax"];
    $bill["dealAddress"] = $data[0]["deal_address"];

    // 明细表
    $sql = "select s.id, g.code, g.name, g.spec, convert(s.goods_count, $fmt) as goods_count, 
              s.goods_price, s.goods_money,
              s.tax_rate, s.tax, s.money_with_tax, u.name as unit_name
            from t_so_bill_detail s, t_goods g, t_goods_unit u
            where s.sobill_id = '%s' and s.goods_id = g.id and g.unit_id = u.id
            order by s.show_order";
    $data = $db->query($sql, $id);
    $items = array();
    foreach ($data as $v) {
      $items[] = [
        "goodsCode" => $v["code"],
        "goodsName" => $v["name"],
        "goodsSpec" => $v["spec"],
        "unitName" => $v["unit_name"],
        "goodsCount" => $v["goods_count"],
        "goodsPrice" => $v["goods_price"],
        "goodsMoney" => $v["goods_money"],
        "taxRate" => $v["tax_rate"],
        "tax" => $v["tax"],
        "moneyWithTax" => $v["money_with_tax"]
      ];
    }
    $bill["items"] = $items;

    return $bill;
  }

  /**
   * 获得打印销售订单的数据
   *
   * @param array $params
   */
  public function getSOBillDataForLodopPrint($params)
  {
    $db = $this->db;

    $id = $params["id"];

    $sql = "select s.ref, s.bill_status, s.goods_money, s.tax, s.money_with_tax,
              c.name as customer_name, s.contact, s.tel, s.fax, s.deal_address,
              s.deal_date, s.receiving_type, s.bill_memo, s.date_created,
              o.full_name as org_name, u1.name as biz_user_name, u2.name as input_user_name,
              s.confirm_user_id, s.confirm_date, s.company_id, s.deal_date
            from t_so_bill s, t_customer c, t_org o, t_user u1, t_user u2
            where (s.customer_id = c.id) and (s.org_id = o.id)
              and (s.biz_user_id = u1.id) and (s.input_user_id = u2.id)
              and (s.id = '%s')";
    $data = $db->query($sql, $id);
    if (!$data) {
      return null;
    }

    $companyId = $data[0]["company_id"];
    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $bill = [];
    $bill["ref"] = $data[0]["ref"];
    $bill["bizDT"] = $this->toYMD($data[0]["bizdt"]);
    $bill["customerName"] = $data[0]["customer_name"];
    $bill["bizUserName"] = $data[0]["biz_user_name"];
    $bill["saleMoney"] = $data[0]["goods_money"];
    $bill["dealAddress"] = $data[0]["deal_address"];
    $bill["dealDate"] = $this->toYMD($data[0]["deal_date"]);
    $bill["tel"] = $data[0]["tel"];
    $bill["billMemo"] = $data[0]["bill_memo"];
    $bill["goodsMoney"] = $data[0]["goods_money"];
    $bill["moneyWithTax"] = $data[0]["money_with_tax"];

    $bill["printDT"] = date("Y-m-d H:i:s");

    // 明细表
    $sql = "select s.id, g.code, g.name, g.spec, convert(s.goods_count, $fmt) as goods_count,
              s.goods_price, s.goods_money, s.memo,
              s.tax_rate, s.tax, s.money_with_tax, u.name as unit_name
            from t_so_bill_detail s, t_goods g, t_goods_unit u
            where s.sobill_id = '%s' and s.goods_id = g.id and g.unit_id = u.id
            order by s.show_order";
    $data = $db->query($sql, $id);
    $items = array();
    foreach ($data as $i => $v) {
      $items[$i]["goodsCode"] = $v["code"];
      $items[$i]["goodsName"] = $v["name"];
      $items[$i]["goodsSpec"] = $v["spec"];
      $items[$i]["unitName"] = $v["unit_name"];
      $items[$i]["goodsCount"] = $v["goods_count"];
      $items[$i]["goodsPrice"] = $v["goods_price"];
      $items[$i]["goodsMoney"] = $v["goods_money"];
      $items[$i]["taxRate"] = intval($v["tax_rate"]);
      $items[$i]["goodsMoneyWithTax"] = $v["money_with_tax"];
      $items[$i]["memo"] = $v["memo"];
    }
    $bill["items"] = $items;

    return $bill;
  }

  /**
   * 销售订单 - 订单变更
   */
  public function changeSaleOrder(&$params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->badParam("companyId");
    }
    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    // 销售订单明细记录id
    $detailId = $params["id"];

    $goodsCount = $params["goodsCount"];
    $goodsPrice = $params["goodsPrice"];

    $sql = "select sobill_id, convert(ws_count, $fmt) as ws_count,
              tax_rate
            from t_so_bill_detail
            where id = '%s' ";
    $data = $db->query($sql, $detailId);
    if (!$data) {
      return $this->bad("要变更的明细记录不存在");
    }

    // 采购订单主表id
    $id = $data[0]["sobill_id"];

    $wsCount = $data[0]["ws_count"];
    $taxRate = $data[0]["tax_rate"];

    $sql = "select ref, bill_status
            from t_so_bill
            where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return $this->bad("要变更的销售订单不存在");
    }
    $ref = $data[0]["ref"];
    $billStatus = $data[0]["bill_status"];
    if ($billStatus >= 4000) {
      return $this->bad("销售订单[单号={$ref}]已经关闭，不能再做订单变更");
    }

    $goodsMoney = $goodsCount * $goodsPrice;
    $leftCount = $goodsCount - $wsCount;
    $tax = $goodsMoney * $taxRate / 100;
    $moneyWithTax = $goodsMoney + $tax;

    $sql = "update t_so_bill_detail
            set goods_count = convert(%f, $fmt), goods_price = %f,
              goods_money = %f, left_count = convert(%f, $fmt),
              tax = %f, money_with_tax = %f
            where id = '%s' ";
    $rc = $db->execute(
      $sql,
      $goodsCount,
      $goodsPrice,
      $goodsMoney,
      $leftCount,
      $tax,
      $moneyWithTax,
      $detailId
    );

    // 同步主表的金额合计字段
    $sql = "select sum(goods_money) as sum_goods_money, sum(tax) as sum_tax,
              sum(money_with_tax) as sum_money_with_tax
            from t_so_bill_detail
            where sobill_id = '%s' ";
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

    $sql = "update t_so_bill
            set goods_money = %f, tax = %f, money_with_tax = %f
            where id = '%s' ";
    $rc = $db->execute($sql, $sumGoodsMoney, $sumTax, $sumMoneyWithTax, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $params["ref"] = $ref;
    return null;
  }

  /**
   * 查询主表金额相关数据 - 订单变更后刷新界面用
   */
  public function getSOBillDataAterChangeOrder($params)
  {
    $db = $this->db;

    $id = $params["id"];

    $sql = "select goods_money, tax, money_with_tax
            from t_so_bill
            where id = '%s' ";
    $data = $db->query($sql, $id);
    if ($data) {
      $v = $data[0];
      return [
        "goodsMoney" => $v["goods_money"],
        "tax" => $v["tax"],
        "moneyWithTax" => $v["money_with_tax"]
      ];
    } else {
      return $this->emptyResult();
    }
  }

  /**
   * 关闭销售订单
   */
  public function closeSOBill(&$params)
  {
    $db = $this->db;

    $id = $params["id"];

    $sql = "select ref, bill_status
            from t_so_bill
            where id = '%s' ";
    $data = $db->query($sql, $id);

    if (!$data) {
      return $this->bad("要关闭的销售订单不存在");
    }

    $ref = $data[0]["ref"];
    $billStatus = $data[0]["bill_status"];

    if ($billStatus >= 4000) {
      return $this->bad("销售订单已经被关闭");
    }

    // 检查该销售订单是否有生成的销售出库单，并且这些销售出库单是没有提交出库的
    // 如果存在这类销售出库单，那么该销售订单不能关闭。
    $sql = "select count(*) as cnt
            from t_ws_bill w, t_so_ws s
            where w.id = s.ws_id and s.so_id = '%s'
              and w.bill_status = 0 ";
    $data = $db->query($sql, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      $info = "当前销售订单生成的出库单中还有没提交的<br/><br/>把这些出库单删除后，才能关闭采购订单";
      return $this->bad($info);
    }

    if ($billStatus < 1000) {
      return $this->bad("当前销售订单还没有审核，没有审核的销售订单不能关闭");
    }

    $newBillStatus = -1;
    if ($billStatus == 1000) {
      // 当前订单只是审核了
      $newBillStatus = 4000;
    } else if ($billStatus == 2000) {
      // 部分出库
      $newBillStatus = 4001;
    } else if ($billStatus == 3000) {
      // 全部出库
      $newBillStatus = 4002;
    }

    if ($newBillStatus == -1) {
      return $this->bad("当前销售订单的订单状态是不能识别的状态码：{$billStatus}");
    }

    $sql = "update t_so_bill
            set bill_status = %d
            where id = '%s' ";
    $rc = $db->execute($sql, $newBillStatus, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $params["ref"] = $ref;
    return null;
  }

  /**
   * 取消订单关闭状态
   */
  public function cancelClosedSOBill(&$params)
  {
    $db = $this->db;

    $id = $params["id"];

    $sql = "select ref, bill_status
            from t_so_bill
            where id = '%s' ";
    $data = $db->query($sql, $id);

    if (!$data) {
      return $this->bad("要关闭的销售订单不存在");
    }

    $ref = $data[0]["ref"];
    $billStatus = $data[0]["bill_status"];

    if ($billStatus < 4000) {
      return $this->bad("销售订单没有被关闭，无需取消");
    }

    $newBillStatus = -1;
    if ($billStatus == 4000) {
      $newBillStatus = 1000;
    } else if ($billStatus == 4001) {
      $newBillStatus = 2000;
    } else if ($billStatus == 4002) {
      $newBillStatus = 3000;
    }

    if ($newBillStatus == -1) {
      return $this->bad("当前销售订单的订单状态是不能识别的状态码：{$billStatus}");
    }

    $sql = "update t_so_bill
            set bill_status = %d
            where id = '%s' ";
    $rc = $db->execute($sql, $newBillStatus, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $params["ref"] = $ref;
    return null;
  }

  /**
   * 根据单号查询销售订单的完整数据，包括明细表的数据
   */
  public function getFullBillDataByRef($ref)
  {
    $db = $this->db;

    $result = [];

    $sql = "select s.id, s.deal_date, s.deal_address,
              c.name as customer_name, s.contact, s.tel, s.fax,
              o.full_name, u.name as biz_user_name,
              s.receiving_type, s.bill_memo, s.company_id
            from t_so_bill s, t_customer c, t_user u, t_org o
            where s.ref = '%s' and s.customer_Id = c.id
              and s.biz_user_id = u.id
              and s.org_id = o.id";
    $data = $db->query($sql, $ref);
    if (!$data) {
      return $this->emptyResult();
    }

    $v = $data[0];
    $result["dealDate"] = $this->toYMD($v["deal_date"]);
    $result["dealAddress"] = $v["deal_address"];
    $result["customerName"] = $v["customer_name"];
    $result["contact"] = $v["contact"];
    $result["tel"] = $v["tel"];
    $result["fax"] = $v["fax"];
    $result["orgFullName"] = $v["full_name"];
    $result["bizUserName"] = $v["biz_user_name"];
    $result["receivingType"] = $v["receiving_type"];
    $result["billMemo"] = $v["bill_memo"];
    $result["billStatus"] = $v["bill_status"];

    $companyId = $v["company_id"];
    $cs = new BizConfigDAO($db);
    $result["taxRate"] = $cs->getTaxRate($companyId);

    $dataScale = $cs->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";
    // 明细表
    $id = $v["id"];
    $sql = "select s.id, s.goods_id, g.code, g.name, g.spec, 
              convert(s.goods_count, " . $fmt . ") as goods_count, s.goods_price, s.goods_money,
              s.tax_rate, s.tax, s.money_with_tax, u.name as unit_name, s.memo,
              s.goods_price_with_tax
            from t_so_bill_detail s, t_goods g, t_goods_unit u
            where s.sobill_id = '%s' and s.goods_id = g.id and g.unit_id = u.id
            order by s.show_order";
    $items = array();
    $data = $db->query($sql, $id);

    foreach ($data as $v) {
      $items[] = [
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
        "goodsPriceWithTax" => $$v["goods_price_with_tax"]
      ];
    }

    $result["items"] = $items;


    return $result;
  }

  /**
   * 根据销售订单单号查询其生成的采购订单
   * 用于：在销售订单生成采购订单之前，提醒用户
   */
  public function getPOBillRefListBySOBillRef($params)
  {
    $db = $this->db;

    $soRef = $params["soRef"];

    $sql = "select p.ref
            from t_so_bill s, t_so_po sp, t_po_bill p
            where s.ref  = '%s' and s.id = sp.so_id and sp.po_id = p.id
            order by p.ref";
    $data = $db->query($sql, $soRef);

    return $data;
  }

  /**
   * 根据销售订单单号查询其生成的销售出库单
   * 用于：在销售订单生成销售出库单之前，提醒用户
   */
  public function getWSBillRefListBySOBillRef($params)
  {
    $db = $this->db;

    $soRef = $params["soRef"];

    $sql = "select w.ref
            from t_so_bill s, t_so_ws sw, t_ws_bill w
            where s.ref  = '%s' and s.id = sw.so_id and sw.ws_id = w.id
            order by w.ref";
    $data = $db->query($sql, $soRef);

    return $data;
  }
}
