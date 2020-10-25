<?php

namespace Home\DAO;

use Home\Common\FIdConst;

/**
 * 销售退货入库单 DAO
 *
 * @author 李静波
 */
class SRBillDAO extends PSIBaseExDAO
{

  /**
   * 生成新的销售退货入库单单号
   *
   * @param string $companyId
   *
   * @return string
   */
  private function genNewBillRef($companyId)
  {
    $db = $this->db;

    $bs = new BizConfigDAO($db);
    $pre = $bs->getSRBillRefPre($companyId);

    $mid = date("Ymd");

    $sql = "select ref from t_sr_bill where ref like '%s' order by ref desc limit 1";
    $data = M()->query($sql, $pre . $mid . "%");
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
   * 销售退货入库单主表信息列表
   *
   * @param array $params
   * @return array
   */
  public function srbillList($params)
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
    $warehouseId = $params["warehouseId"];
    $customerId = $params["customerId"];
    $sn = $params["sn"];
    $paymentType = $params["paymentType"];
    $goodsId = $params["goodsId"];

    $sql = "select w.id, w.ref, w.bizdt, c.name as customer_name, u.name as biz_user_name,
              user.name as input_user_name, h.name as warehouse_name, w.rejection_sale_money,
              w.bill_status, w.date_created, w.payment_type, w.bill_memo,
              w.tax, w.rejection_sale_money_with_tax
            from t_sr_bill w, t_customer c, t_user u, t_user user, t_warehouse h
              where (w.customer_id = c.id) and (w.biz_user_id = u.id)
              and (w.input_user_id = user.id) and (w.warehouse_id = h.id) ";
    $queryParams = [];

    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::SALE_REJECTION, "w", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = $rs[1];
    }

    if ($billStatus != -1) {
      $sql .= " and (w.bill_status = %d) ";
      $queryParams[] = $billStatus;
    }
    if ($ref) {
      $sql .= " and (w.ref like '%s') ";
      $queryParams[] = "%{$ref}%";
    }
    if ($fromDT) {
      $sql .= " and (w.bizdt >= '%s') ";
      $queryParams[] = $fromDT;
    }
    if ($toDT) {
      $sql .= " and (w.bizdt <= '%s') ";
      $queryParams[] = $toDT;
    }
    if ($customerId) {
      $sql .= " and (w.customer_id = '%s') ";
      $queryParams[] = $customerId;
    }
    if ($warehouseId) {
      $sql .= " and (w.warehouse_id = '%s') ";
      $queryParams[] = $warehouseId;
    }
    if ($sn) {
      $sql .= " and (w.id in (
                      select d.srbill_id
                      from t_sr_bill_detail d
                      where d.sn_note like '%s')) ";
      $queryParams[] = "%$sn%";
    }
    if ($paymentType != -1) {
      $sql .= " and (w.payment_type = %d) ";
      $queryParams[] = $paymentType;
    }
    if ($goodsId) {
      $sql .= " and (w.id in (
                      select d.srbill_id
                      from t_sr_bill_detail d
                      where d.goods_id = '%s')) ";
      $queryParams[] = $goodsId;
    }

    $sql .= " order by w.bizdt desc, w.ref desc
              limit %d, %d";
    $queryParams[] = $start;
    $queryParams[] = $limit;
    $data = $db->query($sql, $queryParams);
    $result = [];

    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "ref" => $v["ref"],
        "bizDate" => $this->toYMD($v["bizdt"]),
        "customerName" => $v["customer_name"],
        "warehouseName" => $v["warehouse_name"],
        "inputUserName" => $v["input_user_name"],
        "bizUserName" => $v["biz_user_name"],
        "billStatus" => $v["bill_status"] == 0 ? "待入库" : "已入库",
        "amount" => $v["rejection_sale_money"],
        "dateCreated" => $v["date_created"],
        "paymentType" => $v["payment_type"],
        "billMemo" => $v["bill_memo"],
        "tax" => $v["tax"],
        "moneyWithTax" => $v["rejection_sale_money_with_tax"]
      ];
    }

    $sql = "select count(*) as cnt
            from t_sr_bill w, t_customer c, t_user u, t_user user, t_warehouse h
            where (w.customer_id = c.id) and (w.biz_user_id = u.id)
              and (w.input_user_id = user.id) and (w.warehouse_id = h.id) ";
    $queryParams = [];

    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::SALE_REJECTION, "w", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = $rs[1];
    }

    if ($billStatus != -1) {
      $sql .= " and (w.bill_status = %d) ";
      $queryParams[] = $billStatus;
    }
    if ($ref) {
      $sql .= " and (w.ref like '%s') ";
      $queryParams[] = "%{$ref}%";
    }
    if ($fromDT) {
      $sql .= " and (w.bizdt >= '%s') ";
      $queryParams[] = $fromDT;
    }
    if ($toDT) {
      $sql .= " and (w.bizdt <= '%s') ";
      $queryParams[] = $toDT;
    }
    if ($customerId) {
      $sql .= " and (w.customer_id = '%s') ";
      $queryParams[] = $customerId;
    }
    if ($warehouseId) {
      $sql .= " and (w.warehouse_id = '%s') ";
      $queryParams[] = $warehouseId;
    }
    if ($sn) {
      $sql .= " and (w.id in (
                      select d.srbill_id
                      from t_sr_bill_detail d
                      where d.sn_note like '%s')) ";
      $queryParams[] = "%$sn%";
    }
    if ($paymentType != -1) {
      $sql .= " and (w.payment_type = %d) ";
      $queryParams[] = $paymentType;
    }
    if ($goodsId) {
      $sql .= " and (w.id in (
                      select d.srbill_id
                      from t_sr_bill_detail d
                      where d.goods_id = '%s')) ";
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
   * 销售退货入库单明细信息列表
   *
   * @param array $params
   * @return array
   */
  public function srBillDetailList($params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    // id: 销售退货入库单id
    $id = $params["id"];

    $sql = "select s.id, g.code, g.name, g.spec, u.name as unit_name,
              convert(s.rejection_goods_count, $fmt) as rejection_goods_count, 
              s.rejection_goods_price, s.rejection_sale_money,
              s.sn_note, s.memo, s.tax_rate, s.tax, s.rejection_sale_money_with_tax,
              s.rejection_goods_price_with_tax
            from t_sr_bill_detail s, t_goods g, t_goods_unit u
            where s.srbill_id = '%s' and s.goods_id = g.id and g.unit_id = u.id
              and s.rejection_goods_count > 0
            order by s.show_order";
    $data = $db->query($sql, $id);

    $result = [];

    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "goodsCode" => $v["code"],
        "goodsName" => $v["name"],
        "goodsSpec" => $v["spec"],
        "unitName" => $v["unit_name"],
        "rejCount" => $v["rejection_goods_count"],
        "rejPrice" => $v["rejection_goods_price"],
        "rejSaleMoney" => $v["rejection_sale_money"],
        "sn" => $v["sn_note"],
        "memo" => $v["memo"],
        "taxRate" => $v["tax_rate"],
        "tax" => $v["tax"],
        "rejSaleMoneyWithTax" => $v["rejection_sale_money_with_tax"],
        "rejPriceWithTax" => $v["rejection_goods_price_with_tax"]
      ];
    }
    return $result;
  }

  /**
   * 列出要选择的可以做退货入库的销售出库单
   *
   * @param array $params
   * @return array
   */
  public function selectWSBillList($params)
  {
    $db = $this->db;

    $loginUserId = $params["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->emptyResult();
    }

    $start = $params["start"];
    $limit = $params["limit"];

    $ref = $params["ref"];
    $customerId = $params["customerId"];
    $warehouseId = $params["warehouseId"];
    $fromDT = $params["fromDT"];
    $toDT = $params["toDT"];
    $sn = $params["sn"];

    $sql = "select w.id, w.ref, w.bizdt, c.name as customer_name, u.name as biz_user_name,
              user.name as input_user_name, h.name as warehouse_name, w.sale_money,
              w.tax, w.money_with_tax
            from t_ws_bill w, t_customer c, t_user u, t_user user, t_warehouse h
            where (w.customer_id = c.id) and (w.biz_user_id = u.id)
              and (w.input_user_id = user.id) and (w.warehouse_id = h.id)
              and (w.bill_status = 1000) ";
    $queryParamas = [];

    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::SALE_REJECTION, "w", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParamas = $rs[1];
    }

    if ($ref) {
      $sql .= " and (w.ref like '%s') ";
      $queryParamas[] = "%$ref%";
    }
    if ($customerId) {
      $sql .= " and (w.customer_id = '%s') ";
      $queryParamas[] = $customerId;
    }
    if ($warehouseId) {
      $sql .= " and (w.warehouse_id = '%s') ";
      $queryParamas[] = $warehouseId;
    }
    if ($fromDT) {
      $sql .= " and (w.bizdt >= '%s') ";
      $queryParamas[] = $fromDT;
    }
    if ($toDT) {
      $sql .= " and (w.bizdt <= '%s') ";
      $queryParamas[] = $toDT;
    }
    if ($sn) {
      $sql .= " and (w.id in (
                      select wsbill_id
                      from t_ws_bill_detail d
                      where d.sn_note like '%s'
                    ))";
      $queryParamas[] = "%$sn%";
    }
    $sql .= " order by w.ref desc
              limit %d, %d";
    $queryParamas[] = $start;
    $queryParamas[] = $limit;
    $data = $db->query($sql, $queryParamas);
    $result = [];

    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "ref" => $v["ref"],
        "bizDate" => $this->toYMD($v["bizdt"]),
        "customerName" => $v["customer_name"],
        "warehouseName" => $v["warehouse_name"],
        "inputUserName" => $v["input_user_name"],
        "bizUserName" => $v["biz_user_name"],
        "amount" => $v["sale_money"],
        "tax" => $v["tax"],
        "moneyWithTax" => $v["money_with_tax"]
      ];
    }

    $sql = "select count(*) as cnt
            from t_ws_bill w, t_customer c, t_user u, t_user user, t_warehouse h
            where (w.customer_id = c.id) and (w.biz_user_id = u.id)
              and (w.input_user_id = user.id) and (w.warehouse_id = h.id)
              and (w.bill_status = 1000) ";
    $queryParamas = [];
    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::SALE_REJECTION, "w", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParamas = $rs[1];
    }

    if ($ref) {
      $sql .= " and (w.ref like '%s') ";
      $queryParamas[] = "%$ref%";
    }
    if ($customerId) {
      $sql .= " and (w.customer_id = '%s') ";
      $queryParamas[] = $customerId;
    }
    if ($warehouseId) {
      $sql .= " and (w.warehouse_id = '%s') ";
      $queryParamas[] = $warehouseId;
    }
    if ($fromDT) {
      $sql .= " and (w.bizdt >= '%s') ";
      $queryParamas[] = $fromDT;
    }
    if ($toDT) {
      $sql .= " and (w.bizdt <= '%s') ";
      $queryParamas[] = $toDT;
    }
    if ($sn) {
      $sql .= " and (w.id in (
						select wsbill_id
						from t_ws_bill_detail d
						where d.sn_note like '%s'
					))";
      $queryParamas[] = "%$sn%";
    }

    $data = $db->query($sql, $queryParamas);
    $cnt = $data[0]["cnt"];

    return [
      "dataList" => $result,
      "totalCount" => $cnt
    ];
  }

  /**
   * 获得销售出库单的信息
   *
   * @param array $params
   * @return array
   */
  public function getWSBillInfoForSRBill($params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $result = [];

    $id = $params["id"];

    $sql = "select c.name as customer_name, w.ref, h.id as warehouse_id,
              h.name as warehouse_name, c.id as customer_id
            from t_ws_bill w, t_customer c, t_warehouse h
            where w.id = '%s' and w.customer_id = c.id and w.warehouse_id = h.id ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return $result;
    }

    $result["ref"] = $data[0]["ref"];
    $result["customerName"] = $data[0]["customer_name"];
    $result["warehouseId"] = $data[0]["warehouse_id"];
    $result["warehouseName"] = $data[0]["warehouse_name"];
    $result["customerId"] = $data[0]["customer_id"];

    $sql = "select d.id, g.id as goods_id, g.code, g.name, g.spec, u.name as unit_name, 
              convert(d.goods_count, $fmt) as goods_count,
              d.goods_price, d.goods_money, d.sn_note, d.tax_rate, d.goods_price_with_tax,
              d.money_with_tax
            from t_ws_bill_detail d, t_goods g, t_goods_unit u
            where d.wsbill_id = '%s' and d.goods_id = g.id and g.unit_id = u.id
            order by d.show_order";
    $data = $db->query($sql, $id);
    $items = [];

    foreach ($data as $v) {
      $items[] = [
        "id" => $v["id"],
        "goodsId" => $v["goods_id"],
        "goodsCode" => $v["code"],
        "goodsName" => $v["name"],
        "goodsSpec" => $v["spec"],
        "unitName" => $v["unit_name"],
        "goodsCount" => $v["goods_count"],
        "goodsPrice" => $v["goods_price"],
        "goodsMoney" => $v["goods_money"],
        "rejPrice" => $v["goods_price"],
        "sn" => $v["sn_note"],
        "taxRate" => $v["tax_rate"],
        "goodsPriceWithTax" => $v["goods_price_with_tax"],
        "goodsMoneyWithTax" => $v["money_with_tax"],
        "rejPriceWithTax" => $v["goods_price_with_tax"]
      ];
    }

    $result["items"] = $items;

    return $result;
  }

  /**
   * 新建销售退货入库单
   *
   * @param array $bill
   * @return NULL|array
   */
  public function addSRBill(&$bill)
  {
    $db = $this->db;

    $bizDT = $bill["bizDT"];
    $customerId = $bill["customerId"];
    $warehouseId = $bill["warehouseId"];
    $bizUserId = $bill["bizUserId"];
    $items = $bill["items"];
    $wsBillId = $bill["wsBillId"];
    $paymentType = $bill["paymentType"];
    $billMemo = $bill["billMemo"];

    $wsBillDAO = new WSBillDAO($db);
    $wsBill = $wsBillDAO->getWSBillById($wsBillId);
    if (!$wsBill) {
      return $this->bad("选择的销售出库单不存在");
    }

    $customerDAO = new CustomerDAO($db);
    $customer = $customerDAO->getCustomerById($customerId);
    if (!$customer) {
      return $this->bad("选择的客户不存在");
    }

    $warehouseDAO = new WarehouseDAO($db);
    $warehouse = $warehouseDAO->getWarehouseById($warehouseId);
    if (!$warehouse) {
      return $this->bad("选择的仓库不存在");
    }

    $userDAO = new UserDAO($db);
    $user = $userDAO->getUserById($bizUserId);
    if (!$user) {
      return $this->bad("选择的业务员不存在");
    }

    // 检查业务日期
    if (!$this->dateIsValid($bizDT)) {
      return $this->bad("业务日期不正确");
    }

    $dataOrg = $bill["dataOrg"];
    $companyId = $bill["companyId"];
    $loginUserId = $bill["loginUserId"];
    if ($this->dataOrgNotExists($dataOrg)) {
      return $this->badParam("dataOrg");
    }
    if ($this->companyIdNotExists($companyId)) {
      return $this->badParam("companyId");
    }
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->badParam("loginUserId");
    }

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $id = $this->newId();
    $ref = $this->genNewBillRef($companyId);
    $sql = "insert into t_sr_bill(id, bill_status, bizdt, biz_user_id, customer_id,
              date_created, input_user_id, ref, warehouse_id, ws_bill_id, payment_type,
              data_org, company_id, bill_memo)
            values ('%s', 0, '%s', '%s', '%s',
              now(), '%s', '%s', '%s', '%s', %d, '%s', '%s', '%s')";

    $rc = $db->execute(
      $sql,
      $id,
      $bizDT,
      $bizUserId,
      $customerId,
      $loginUserId,
      $ref,
      $warehouseId,
      $wsBillId,
      $paymentType,
      $dataOrg,
      $companyId,
      $billMemo
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    foreach ($items as $i => $v) {
      $wsBillDetailId = $v["id"];
      $sql = "select inventory_price, convert(goods_count, $fmt) as goods_count, goods_price, goods_money,
                goods_price_with_tax, money_with_tax
              from t_ws_bill_detail
              where id = '%s' ";
      $data = $db->query($sql, $wsBillDetailId);
      if (!$data) {
        continue;
      }
      $goodsCount = $data[0]["goods_count"];
      $goodsPrice = $data[0]["goods_price"];
      $goodsMoney = $data[0]["goods_money"];
      $goodsPriceWithTax = $data[0]["goods_price_with_tax"];
      $goodsMoneyWithTax = $data[0]["money_with_tax"];
      $inventoryPrice = $data[0]["inventory_price"];
      $rejCount = $v["rejCount"];
      $rejPrice = $v["rejPrice"];
      if ($rejCount == null) {
        $rejCount = 0;
      }
      $rejSaleMoney = $rejCount * $rejPrice;
      $inventoryMoney = $rejCount * $inventoryPrice;
      $goodsId = $v["goodsId"];
      $sn = $v["sn"];
      $memo = $v["memo"];
      $taxRate = $v["taxRate"];
      // 税金为负数，也就是“红字”
      $tax = -$rejSaleMoney * $taxRate / 100;
      $rejPriceWithTax = $v["rejPriceWithTax"];
      $rejMoneyWithTax = $v["rejMoneyWithTax"];

      $sql = "insert into t_sr_bill_detail(id, date_created, goods_id, goods_count, goods_money,
                goods_price, inventory_money, inventory_price, rejection_goods_count,
                rejection_goods_price, rejection_sale_money, show_order, srbill_id, wsbilldetail_id,
                sn_note, data_org, company_id, memo,
                tax_rate, tax, rejection_sale_money_with_tax, rejection_goods_price_with_tax,
                goods_money_with_tax, goods_price_with_tax)
              values('%s', now(), '%s', convert(%f, $fmt), %f, %f, %f, %f, convert(%f, $fmt),
                %f, %f, %d, '%s', '%s', '%s', '%s', '%s', '%s',
                %f, %f, %f, %f,
                %f, %f) ";
      $rc = $db->execute(
        $sql,
        $this->newId(),
        $goodsId,
        $goodsCount,
        $goodsMoney,
        $goodsPrice,
        $inventoryMoney,
        $inventoryPrice,
        $rejCount,
        $rejPrice,
        $rejSaleMoney,
        $i,
        $id,
        $wsBillDetailId,
        $sn,
        $dataOrg,
        $companyId,
        $memo,
        $taxRate,
        $tax,
        $rejMoneyWithTax,
        $rejPriceWithTax,
        $goodsMoneyWithTax,
        $goodsPriceWithTax
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 更新主表的汇总信息
    $sql = "select sum(rejection_sale_money) as rej_money,
              sum(inventory_money) as inv_money,
              sum(tax) as sum_tax,
              sum(rejection_sale_money_with_tax) as sum_rej_money_with_tax
            from t_sr_bill_detail
            where srbill_id = '%s' ";
    $data = $db->query($sql, $id);
    $rejMoney = $data[0]["rej_money"];
    $invMoney = $data[0]["inv_money"];
    $profit = $invMoney - $rejMoney;
    $tax = $data[0]["sum_tax"];
    $rejMoneyWithTax = $data[0]["sum_rej_money_with_tax"];
    $sql = "update t_sr_bill
            set rejection_sale_money = %f, inventory_money = %f, profit = %f,
              tax = %f, rejection_sale_money_with_tax = %f
            where id = '%s' ";
    $rc = $db->execute($sql, $rejMoney, $invMoney, $profit, $tax, $rejMoneyWithTax, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $bill["id"] = $id;
    $bill["ref"] = $ref;

    // 操作成功
    return null;
  }

  /**
   * 根据销售退货入库单id查询销售退货入库单
   *
   * @param string $id
   * @return array|NULL
   */
  public function getSRBillById($id)
  {
    $db = $this->db;
    $sql = "select bill_status, ref, data_org, company_id from t_sr_bill where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return null;
    } else {
      return array(
        "ref" => $data[0]["ref"],
        "billStatus" => $data[0]["bill_status"],
        "dataOrg" => $data[0]["data_org"],
        "companyId" => $data[0]["company_id"]
      );
    }
  }

  /**
   * 编辑销售退货入库单
   *
   * @param array $bill
   * @return NULL|array
   */
  public function updateSRBill(&$bill)
  {
    $db = $this->db;

    $id = $bill["id"];

    $oldBill = $this->getSRBillById($id);
    if (!$oldBill) {
      return $this->bad("要编辑的销售退货入库单不存在");
    }
    $billStatus = $oldBill["billStatus"];
    if ($billStatus != 0) {
      return $this->bad("销售退货入库单已经提交，不能再编辑");
    }
    $ref = $oldBill["ref"];
    $dataOrg = $oldBill["dataOrg"];
    $companyId = $oldBill["companyId"];

    $bizDT = $bill["bizDT"];
    $customerId = $bill["customerId"];
    $warehouseId = $bill["warehouseId"];
    $bizUserId = $bill["bizUserId"];
    $items = $bill["items"];
    $paymentType = $bill["paymentType"];
    $billMemo = $bill["billMemo"];

    $customerDAO = new CustomerDAO($db);
    $customer = $customerDAO->getCustomerById($customerId);
    if (!$customer) {
      return $this->bad("选择的客户不存在");
    }

    $warehouseDAO = new WarehouseDAO($db);
    $warehouse = $warehouseDAO->getWarehouseById($warehouseId);
    if (!$warehouse) {
      return $this->bad("选择的仓库不存在");
    }

    $userDAO = new UserDAO($db);
    $user = $userDAO->getUserById($bizUserId);
    if (!$user) {
      return $this->bad("选择的业务员不存在");
    }

    // 检查业务日期
    if (!$this->dateIsValid($bizDT)) {
      return $this->bad("业务日期不正确");
    }

    $loginUserId = $bill["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->badParam("loginUserId");
    }

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    // 主表
    $sql = "update t_sr_bill
            set bizdt = '%s', biz_user_id = '%s', date_created = now(),
              input_user_id = '%s', warehouse_id = '%s',
              payment_type = %d, bill_memo = '%s'
            where id = '%s' ";
    $db->execute(
      $sql,
      $bizDT,
      $bizUserId,
      $loginUserId,
      $warehouseId,
      $paymentType,
      $billMemo,
      $id
    );

    // 退货明细
    $sql = "delete from t_sr_bill_detail where srbill_id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    foreach ($items as $i => $v) {
      $wsBillDetailId = $v["id"];
      $sql = "select inventory_price, convert(goods_count, $fmt) as goods_count, goods_price, goods_money,
                goods_price_with_tax, money_with_tax
              from t_ws_bill_detail
              where id = '%s' ";
      $data = $db->query($sql, $wsBillDetailId);
      if (!$data) {
        continue;
      }
      $goodsCount = $data[0]["goods_count"];
      $goodsPrice = $data[0]["goods_price"];
      $goodsMoney = $data[0]["goods_money"];
      $goodsPriceWithTax = $data[0]["goods_price_with_tax"];
      $goodsMoneyWithTax = $data[0]["money_with_tax"];
      $inventoryPrice = $data[0]["inventory_price"];
      $rejCount = $v["rejCount"];
      $rejPrice = $v["rejPrice"];
      if ($rejCount == null) {
        $rejCount = 0;
      }
      $rejSaleMoney = $v["rejMoney"];
      $inventoryMoney = $rejCount * $inventoryPrice;
      $goodsId = $v["goodsId"];
      $sn = $v["sn"];
      $memo = $v["memo"];
      $taxRate = $v["taxRate"];
      // 税金为负数，也就是“红字”
      $tax = -$rejSaleMoney * $taxRate / 100;
      $rejPriceWithTax = $v["rejPriceWithTax"];
      $rejMoneyWithTax = $v["rejMoneyWithTax"];

      $sql = "insert into t_sr_bill_detail(id, date_created, goods_id, goods_count, goods_money,
                goods_price, inventory_money, inventory_price, rejection_goods_count,
                rejection_goods_price, rejection_sale_money, show_order, srbill_id, wsbilldetail_id,
                sn_note, data_org, company_id, memo,
                tax_rate, tax, rejection_sale_money_with_tax, rejection_goods_price_with_tax,
                goods_money_with_tax, goods_price_with_tax)
              values('%s', now(), '%s', convert(%f, $fmt), %f, %f, %f, %f, convert(%f, $fmt),
                %f, %f, %d, '%s', '%s', '%s', '%s', '%s', '%s',
                %f, %f, %f, %f,
                %f, %f) ";
      $rc = $db->execute(
        $sql,
        $this->newId(),
        $goodsId,
        $goodsCount,
        $goodsMoney,
        $goodsPrice,
        $inventoryMoney,
        $inventoryPrice,
        $rejCount,
        $rejPrice,
        $rejSaleMoney,
        $i,
        $id,
        $wsBillDetailId,
        $sn,
        $dataOrg,
        $companyId,
        $memo,
        $taxRate,
        $tax,
        $rejMoneyWithTax,
        $rejPriceWithTax,
        $goodsMoneyWithTax,
        $goodsPriceWithTax
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 更新主表的汇总信息
    $sql = "select sum(rejection_sale_money) as rej_money,
              sum(inventory_money) as inv_money,
              sum(tax) as sum_tax,
              sum(rejection_sale_money_with_tax) as sum_rej_money_with_tax
            from t_sr_bill_detail
            where srbill_id = '%s' ";
    $data = $db->query($sql, $id);
    $rejMoney = $data[0]["rej_money"];
    if (!$rejMoney) {
      $rejMoney = 0;
    }
    $invMoney = $data[0]["inv_money"];
    if (!$invMoney) {
      $invMoney = 0;
    }
    $profit = $invMoney - $rejMoney;
    $tax = $data[0]["sum_tax"];
    $rejMoneyWithTax = $data[0]["sum_rej_money_with_tax"];
    $sql = "update t_sr_bill
            set rejection_sale_money = %f, inventory_money = %f, profit = %f,
            tax = %f, rejection_sale_money_with_tax = %f
            where id = '%s' ";
    $rc = $db->execute($sql, $rejMoney, $invMoney, $profit, $tax, $rejMoneyWithTax, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $bill["ref"] = $ref;

    // 操作成功
    return null;
  }

  /**
   * 获得退货入库单单据数据
   *
   * @param array $params
   * @return array
   */
  public function srBillInfo($params)
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

    if (!$id) {
      // 新增单据
      $result = [];
      $result["bizUserId"] = $params["loginUserId"];
      $result["bizUserName"] = $params["loginUserName"];
      return $result;
    } else {
      // 编辑单据
      $result = [];
      $sql = "select w.id, w.ref, w.bill_status, w.bizdt, c.id as customer_id, c.name as customer_name,
                u.id as biz_user_id, u.name as biz_user_name,
                h.id as warehouse_id, h.name as warehouse_name, wsBill.ref as ws_bill_ref,
                w.payment_type, w.bill_memo
              from t_sr_bill w, t_customer c, t_user u, t_warehouse h, t_ws_bill wsBill
              where w.customer_id = c.id and w.biz_user_id = u.id
                and w.warehouse_id = h.id
                and w.id = '%s' and wsBill.id = w.ws_bill_id";
      $data = $db->query($sql, $id);
      if ($data) {
        $result["ref"] = $data[0]["ref"];
        $result["billStatus"] = $data[0]["bill_status"];
        $result["bizDT"] = date("Y-m-d", strtotime($data[0]["bizdt"]));
        $result["customerId"] = $data[0]["customer_id"];
        $result["customerName"] = $data[0]["customer_name"];
        $result["warehouseId"] = $data[0]["warehouse_id"];
        $result["warehouseName"] = $data[0]["warehouse_name"];
        $result["bizUserId"] = $data[0]["biz_user_id"];
        $result["bizUserName"] = $data[0]["biz_user_name"];
        $result["wsBillRef"] = $data[0]["ws_bill_ref"];
        $result["paymentType"] = $data[0]["payment_type"];
        $result["billMemo"] = $data[0]["bill_memo"];
      }

      $sql = "select d.id, g.id as goods_id, g.code, g.name, g.spec, u.name as unit_name, 
                convert(d.goods_count, $fmt) as goods_count,
                d.goods_price, d.goods_money,
                convert(d.rejection_goods_count, $fmt) as rejection_goods_count, 
                d.rejection_goods_price, d.rejection_sale_money,
                d.wsbilldetail_id, d.sn_note, d.memo,
                d.tax_rate, d.rejection_sale_money_with_tax, d.rejection_goods_price_with_tax,
                d.goods_money_with_tax, d.goods_price_with_tax
              from t_sr_bill_detail d, t_goods g, t_goods_unit u
              where d.srbill_id = '%s' and d.goods_id = g.id and g.unit_id = u.id
              order by d.show_order";
      $data = $db->query($sql, $id);
      $items = [];
      foreach ($data as $v) {
        $items[] = [
          "id" => $v["wsbilldetail_id"],
          "goodsId" => $v["goods_id"],
          "goodsCode" => $v["code"],
          "goodsName" => $v["name"],
          "goodsSpec" => $v["spec"],
          "unitName" => $v["unit_name"],
          "goodsCount" => $v["goods_count"],
          "goodsPrice" => $v["goods_price"],
          "goodsMoney" => $v["goods_money"],
          "rejCount" => $v["rejection_goods_count"],
          "rejPrice" => $v["rejection_goods_price"],
          "rejMoney" => $v["rejection_sale_money"],
          "sn" => $v["sn_note"],
          "memo" => $v["memo"],
          "taxRate" => $v["tax_rate"],
          "rejPriceWithTax" => $v["rejection_goods_price_with_tax"],
          "rejMoneyWithTax" => $v["rejection_sale_money_with_tax"],
          "goodsPriceWithTax" => $v["goods_price_with_tax"],
          "goodsMoneyWithTax" => $v["goods_money_with_tax"]
        ];
      }

      $result["items"] = $items;

      return $result;
    }
  }

  /**
   * 删除销售退货入库单
   *
   * @param array $params
   * @return NULL|array
   */
  public function deleteSRBill(&$params)
  {
    $db = $this->db;

    $id = $params["id"];

    $bill = $this->getSRBillById($id);
    if (!$bill) {
      return $this->bad("要删除的销售退货入库单不存在");
    }

    $billStatus = $bill["billStatus"];
    $ref = $bill["ref"];
    if ($billStatus != 0) {
      return $this->bad("销售退货入库单[单号: {$ref}]已经提交，不能删除");
    }

    $sql = "delete from t_sr_bill_detail where srbill_id = '%s'";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $sql = "delete from t_sr_bill where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $params["ref"] = $ref;

    // 操作成功
    return null;
  }

  /**
   * 提交销售退货入库单
   *
   * @param array $params
   * @return NULL|array
   */
  public function commitSRBill(&$params)
  {
    $db = $this->db;

    $id = $params["id"];

    $loginUserId = $params["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->badParam("loginUserId");
    }

    $sql = "select ref, bill_status, warehouse_id, customer_id, bizdt,
              biz_user_id, rejection_sale_money, payment_type,
              company_id, ws_bill_id, rejection_sale_money_with_tax
            from t_sr_bill where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return $this->bad("要提交的销售退货入库单不存在");
    }
    $billStatus = $data[0]["bill_status"];
    $ref = $data[0]["ref"];
    if ($billStatus != 0) {
      return $this->bad("销售退货入库单(单号:{$ref})已经提交，不能再次提交");
    }

    $paymentType = $data[0]["payment_type"];
    $warehouseId = $data[0]["warehouse_id"];
    $customerId = $data[0]["customer_id"];
    $bizDT = $data[0]["bizdt"];
    $bizUserId = $data[0]["biz_user_id"];
    // 取含税金额
    // 如果是旧数据，则取不含税金额，不过这种情形多半是bug
    $rejectionSaleMoney = $data[0]["rejection_sale_money_with_tax"] ?? $data[0]["rejection_sale_money"];
    $companyId = $data[0]["company_id"];
    $wsBillId = $data[0]["ws_bill_id"];

    $bs = new BizConfigDAO($db);
    $fifo = $bs->getInventoryMethod($companyId) == 1; // true: 先进先出
    $dataScale = $bs->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $warehouseDAO = new WarehouseDAO($db);
    $warehouse = $warehouseDAO->getWarehouseById($warehouseId);
    if (!$warehouse) {
      return $this->bad("仓库不存在，无法提交");
    }
    $warehouseName = $warehouse["name"];
    $inited = $warehouse["inited"];
    if ($inited != 1) {
      return $this->bad("仓库[{$warehouseName}]还没有建账");
    }

    $customerDAO = new CustomerDAO($db);
    $customer = $customerDAO->getCustomerById($customerId);
    if (!$customer) {
      return $this->bad("客户不存在，无法提交");
    }

    $userDAO = new UserDAO($db);
    $user = $userDAO->getUserById($bizUserId);
    if (!$user) {
      return $this->bad("业务人员不存在，无法提交");
    }

    $allPaymentType = array(
      0,
      1,
      2
    );
    if (!in_array($paymentType, $allPaymentType)) {
      return $this->bad("付款方式不正确，无法提交");
    }

    // 检查退货数量
    // 1、不能为负数
    // 2、累计退货数量不能超过销售的数量
    $sql = "select wsbilldetail_id, convert(rejection_goods_count, $fmt) as rejection_goods_count, goods_id
            from t_sr_bill_detail
            where srbill_id = '%s'
            order by show_order";
    $data = $db->query($sql, $id);
    if (!$data) {
      return $this->bad("销售退货入库单(单号:{$ref})没有退货明细，无法提交");
    }

    foreach ($data as $v) {
      $wsbillDetailId = $v["wsbilldetail_id"];
      $rejCount = $v["rejection_goods_count"];
      $goodsId = $v["goods_id"];

      // 退货数量为负数
      if ($rejCount < 0) {
        $sql = "select code, name, spec
                from t_goods
                where id = '%s' ";
        $data = $db->query($sql, $goodsId);
        if ($data) {
          $goodsInfo = "编码：" . $data[0]["code"] . " 名称：" . $data[0]["name"] . " 规格：" . $data[0]["spec"];
          return $this->bad("商品({$goodsInfo})退货数量不能为负数");
        } else {
          return $this->bad("商品退货数量不能为负数");
        }
      }

      // 累计退货数量不能超过销售数量
      $sql = "select convert(goods_count, $fmt) as goods_count from t_ws_bill_detail where id = '%s' ";
      $data = $db->query($sql, $wsbillDetailId);
      $saleGoodsCount = 0;
      if ($data) {
        $saleGoodsCount = $data[0]["goods_count"];
      }

      $sql = "select sum(convert(d.rejection_goods_count, $fmt)) as rej_count
              from t_sr_bill s, t_sr_bill_detail d
              where s.id = d.srbill_id and s.bill_status <> 0
                and d.wsbilldetail_id = '%s' ";
      $data = $db->query($sql, $wsbillDetailId);
      $totalRejCount = $data[0]["rej_count"] + $rejCount;
      if ($totalRejCount > $saleGoodsCount) {
        $sql = "select code, name, spec
                from t_goods
                where id = '%s' ";
        $data = $db->query($sql, $goodsId);
        if ($data) {
          $goodsInfo = "编码：" . $data[0]["code"] . " 名称：" . $data[0]["name"] . " 规格：" . $data[0]["spec"];
          return $this->bad("商品({$goodsInfo})累计退货数量不超过销售量");
        } else {
          return $this->bad("商品累计退货数量不超过销售量");
        }
      }
    }

    $sql = "select goods_id, convert(rejection_goods_count, $fmt) as rejection_goods_count, inventory_money
            from t_sr_bill_detail
            where srbill_id = '%s'
            order by show_order";
    $items = $db->query($sql, $id);
    foreach ($items as $v) {
      $goodsId = $v["goods_id"];
      $rejCount = $v["rejection_goods_count"];
      $rejMoney = $v["inventory_money"];
      if ($rejCount == 0) {
        continue;
      }
      $rejPrice = $rejMoney / $rejCount;

      if ($fifo) {
        // TODO 先进先出
      } else {
        // 移动平均
        $sql = "select convert(in_count, $fmt) as in_count, in_money, 
                  convert(balance_count, $fmt) as balance_count, balance_money
                from t_inventory
                where warehouse_id = '%s' and goods_id = '%s' ";
        $data = $db->query($sql, $warehouseId, $goodsId);
        if (!$data) {
          $totalInCount = 0;
          $totalInMoney = 0;
          $totalBalanceCount = 0;
          $totalBalanceMoney = 0;

          $totalInCount += $rejCount;
          $totalInMoney += $rejMoney;
          $totalInPrice = $totalInMoney / $totalInCount;
          $totalBalanceCount += $rejCount;
          $totalBalanceMoney += $rejMoney;
          $totalBalancePrice = $totalBalanceMoney / $totalBalanceCount;

          // 库存明细账
          $sql = "insert into t_inventory_detail(in_count, in_price, in_money,
                    balance_count, balance_price, balance_money, ref_number, ref_type,
                    biz_date, biz_user_id, date_created, goods_id, warehouse_id)
                  values (convert(%f, $fmt), %f, %f,
                    convert(%f, $fmt), %f, %f, '%s', '销售退货入库',
                    '%s', '%s', now(), '%s', '%s')";
          $rc = $db->execute(
            $sql,
            $rejCount,
            $rejPrice,
            $rejMoney,
            $totalBalanceCount,
            $totalBalancePrice,
            $totalBalanceMoney,
            $ref,
            $bizDT,
            $bizUserId,
            $goodsId,
            $warehouseId
          );
          if ($rc === false) {
            return $this->sqlError(__METHOD__, __LINE__);
          }

          // 库存总账
          $sql = "insert into t_inventory(in_count, in_price, in_money,
                    balance_count, balance_price, balance_money,
                    goods_id, warehouse_id)
                  values (convert(%f, $fmt), %f, %f,
                    convert(%f, $fmt), %f, %f, '%s', '%s')";
          $rc = $db->execute(
            $sql,
            $totalInCount,
            $totalInPrice,
            $totalInMoney,
            $totalBalanceCount,
            $totalBalancePrice,
            $totalBalanceMoney,
            $goodsId,
            $warehouseId
          );
          if ($rc === false) {
            return $this->sqlError(__METHOD__, __LINE__);
          }
        } else {
          $totalInCount = $data[0]["in_count"];
          $totalInMoney = $data[0]["in_money"];
          $totalBalanceCount = $data[0]["balance_count"];
          $totalBalanceMoney = $data[0]["balance_money"];

          $totalInCount += $rejCount;
          $totalInMoney += $rejMoney;
          $totalInPrice = $totalInMoney / $totalInCount;
          $totalBalanceCount += $rejCount;
          $totalBalanceMoney += $rejMoney;
          $totalBalancePrice = $totalBalanceMoney / $totalBalanceCount;

          // 库存明细账
          $sql = "insert into t_inventory_detail(in_count, in_price, in_money,
                    balance_count, balance_price, balance_money, ref_number, ref_type,
                    biz_date, biz_user_id, date_created, goods_id, warehouse_id)
                  values (convert(%f, $fmt), %f, %f,
                    convert(%f, $fmt), %f, %f, '%s', '销售退货入库',
                    '%s', '%s', now(), '%s', '%s')";
          $rc = $db->execute(
            $sql,
            $rejCount,
            $rejPrice,
            $rejMoney,
            $totalBalanceCount,
            $totalBalancePrice,
            $totalBalanceMoney,
            $ref,
            $bizDT,
            $bizUserId,
            $goodsId,
            $warehouseId
          );
          if ($rc === false) {
            return $this->sqlError(__METHOD__, __LINE__);
          }

          // 库存总账
          $sql = "update t_inventory
                  set in_count = convert(%f, $fmt), in_price = %f, in_money = %f,
                    balance_count = convert(%f, $fmt), balance_price = %f, balance_money = %f
                  where goods_id = '%s' and warehouse_id = '%s' ";
          $rc = $db->execute(
            $sql,
            $totalInCount,
            $totalInPrice,
            $totalInMoney,
            $totalBalanceCount,
            $totalBalancePrice,
            $totalBalanceMoney,
            $goodsId,
            $warehouseId
          );
          if ($rc === false) {
            return $this->sqlError(__METHOD__, __LINE__);
          }
        }
      }
    }

    if ($paymentType == 0) {
      // 记应付账款
      // 应付账款总账
      $sql = "select pay_money, balance_money
              from t_payables
              where ca_id = '%s' and ca_type = 'customer'
                and company_id = '%s' ";
      $data = $db->query($sql, $customerId, $companyId);
      if ($data) {
        $totalPayMoney = $data[0]["pay_money"];
        $totalBalanceMoney = $data[0]["balance_money"];

        $totalPayMoney += $rejectionSaleMoney;
        $totalBalanceMoney += $rejectionSaleMoney;
        $sql = "update t_payables
                set pay_money = %f, balance_money = %f
                where ca_id = '%s' and ca_type = 'customer'
                  and company_id = '%s' ";
        $rc = $db->execute($sql, $totalPayMoney, $totalBalanceMoney, $customerId, $companyId);
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      } else {

        $sql = "insert into t_payables (id, ca_id, ca_type, pay_money, balance_money,
                  act_money, company_id)
                values ('%s', '%s', 'customer', %f, %f, %f, '%s')";
        $rc = $db->execute(
          $sql,
          $this->newId(),
          $customerId,
          $rejectionSaleMoney,
          $rejectionSaleMoney,
          0,
          $companyId
        );
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      }

      // 应付账款明细账
      $sql = "insert into t_payables_detail(id, ca_id, ca_type, pay_money, balance_money,
                biz_date, date_created, ref_number, ref_type, act_money, company_id)
              values ('%s', '%s', 'customer', %f, %f,
                '%s', now(), '%s', '销售退货入库', 0, '%s')";
      $rc = $db->execute(
        $sql,
        $this->newId(),
        $customerId,
        $rejectionSaleMoney,
        $rejectionSaleMoney,
        $bizDT,
        $ref,
        $companyId
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    } else if ($paymentType == 1) {
      // 现金付款
      $outCash = $rejectionSaleMoney;

      $sql = "select in_money, out_money, balance_money
              from t_cash
              where biz_date = '%s' and company_id = '%s' ";
      $data = $db->query($sql, $bizDT, $companyId);
      if (!$data) {
        // 当天首次发生现金业务

        $sql = "select sum(in_money) as sum_in_money, sum(out_money) as sum_out_money
                from t_cash
                where biz_date <= '%s' and company_id = '%s' ";
        $data = $db->query($sql, $bizDT, $companyId);
        $sumInMoney = $data[0]["sum_in_money"];
        $sumOutMoney = $data[0]["sum_out_money"];
        if (!$sumInMoney) {
          $sumInMoney = 0;
        }
        if (!$sumOutMoney) {
          $sumOutMoney = 0;
        }

        $balanceCash = $sumInMoney - $sumOutMoney - $outCash;
        $sql = "insert into t_cash(out_money, balance_money, biz_date, company_id)
                values (%f, %f, '%s', '%s')";
        $rc = $db->execute($sql, $outCash, $balanceCash, $bizDT, $companyId);
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }

        // 记现金明细账
        $sql = "insert into t_cash_detail(out_money, balance_money, biz_date, ref_type,
                  ref_number, date_created, company_id)
                values (%f, %f, '%s', '销售退货入库', '%s', now(), '%s')";
        $rc = $db->execute($sql, $outCash, $balanceCash, $bizDT, $ref, $companyId);
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      } else {
        $balanceCash = $data[0]["balance_money"] - $outCash;
        $sumOutMoney = $data[0]["out_money"] + $outCash;
        $sql = "update t_cash
                  set out_money = %f, balance_money = %f
                where biz_date = '%s' and company_id = '%s' ";
        $rc = $db->execute($sql, $sumOutMoney, $balanceCash, $bizDT, $companyId);
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }

        // 记现金明细账
        $sql = "insert into t_cash_detail(out_money, balance_money, biz_date, ref_type,
                  ref_number, date_created, company_id)
                values (%f, %f, '%s', '销售退货入库', '%s', now(), '%s')";
        $rc = $db->execute($sql, $outCash, $balanceCash, $bizDT, $ref, $companyId);
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      }

      // 调整业务日期之后的现金总账和明细账的余额
      $sql = "update t_cash
              set balance_money = balance_money - %f
              where biz_date > '%s' and company_id = '%s' ";
      $rc = $db->execute($sql, $outCash, $bizDT, $companyId);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }

      $sql = "update t_cash_detail
              set balance_money = balance_money - %f
              where biz_date > '%s' and company_id = '%s' ";
      $rc = $db->execute($sql, $outCash, $bizDT, $companyId);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    } else if ($paymentType == 2) {
      // 2： 退款转入预收款
      $inMoney = $rejectionSaleMoney;

      // 预收款总账
      $totalInMoney = 0;
      $totalBalanceMoney = 0;
      $sql = "select in_money, balance_money
              from t_pre_receiving
              where customer_id = '%s' and company_id = '%s' ";
      $data = $db->query($sql, $customerId, $companyId);
      if (!$data) {
        $totalInMoney = $inMoney;
        $totalBalanceMoney = $inMoney;
        $sql = "insert into t_pre_receiving(id, customer_id, in_money, balance_money, company_id)
                values ('%s', '%s', %f, %f, '%s')";
        $rc = $db->execute(
          $sql,
          $this->newId(),
          $customerId,
          $totalInMoney,
          $totalBalanceMoney,
          $companyId
        );
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      } else {
        $totalInMoney = $data[0]["in_money"];
        if (!$totalInMoney) {
          $totalInMoney = 0;
        }
        $totalBalanceMoney = $data[0]["balance_money"];
        if (!$totalBalanceMoney) {
          $totalBalanceMoney = 0;
        }
        $totalInMoney += $inMoney;
        $totalBalanceMoney += $inMoney;
        $sql = "update t_pre_receiving
                set in_money = %f, balance_money = %f
                where customer_id = '%s' and company_id = '%s' ";
        $rc = $db->execute($sql, $totalInMoney, $totalBalanceMoney, $customerId, $companyId);
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      }

      // 预收款明细账
      $sql = "insert into t_pre_receiving_detail(id, customer_id, in_money, balance_money,
                biz_date, date_created, ref_number, ref_type, biz_user_id, input_user_id,
                company_id)
              values('%s', '%s', %f, %f, '%s', now(), '%s', '销售退货入库', '%s', '%s', '%s')";
      $rc = $db->execute(
        $sql,
        $this->newId(),
        $customerId,
        $inMoney,
        $totalBalanceMoney,
        $bizDT,
        $ref,
        $bizUserId,
        $loginUserId,
        $companyId
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 把单据本身的状态修改为已经提交
    $sql = "update t_sr_bill
            set bill_status = 1000
            where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 把对应的销售出库单的状态改为：已退货
    $sql = "update t_ws_bill
            set bill_status = 2000
            where id = '%s' ";
    $rc = $db->execute($sql, $wsBillId);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $params["ref"] = $ref;

    // 操作成功
    return null;
  }

  /**
   * 查询数据，用于销售退货入库单生成pdf文件
   *
   * @param array $params
   * @return array|NULL
   */
  public function getDataForPDF($params)
  {
    $ref = $params["ref"];

    $db = $this->db;
    $sql = "select w.id, w.bizdt, c.name as customer_name, u.name as biz_user_name,
              user.name as input_user_name, h.name as warehouse_name, w.rejection_sale_money,
              w.bill_status, w.date_created, w.payment_type, w.company_id,
              w.rejection_sale_money_with_tax as money_with_tax
            from t_sr_bill w, t_customer c, t_user u, t_user user, t_warehouse h
            where (w.customer_id = c.id) and (w.biz_user_id = u.id)
              and (w.input_user_id = user.id) and (w.warehouse_id = h.id) 
              and (w.ref = '%s')";
    $data = $db->query($sql, $ref);
    if (!$data) {
      return null;
    }

    $id = $data[0]["id"];
    $companyId = $data[0]["company_id"];

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $bill = [
      "bizDT" => $this->toYMD($data[0]["bizdt"]),
      "customerName" => $data[0]["customer_name"],
      "warehouseName" => $data[0]["warehouse_name"],
      "bizUserName" => $data[0]["biz_user_name"],
      "rejMoney" => $data[0]["rejection_sale_money"],
      "moneyWithTax" => $data[0]["money_with_tax"]
    ];

    // 明细表
    $sql = "select s.id, g.code, g.name, g.spec, u.name as unit_name,
              convert(s.rejection_goods_count, $fmt) as rejection_goods_count, 
              s.rejection_goods_price, s.rejection_sale_money,
              s.sn_note, s.tax_rate, s.rejection_sale_money_with_tax as money_with_tax
            from t_sr_bill_detail s, t_goods g, t_goods_unit u
            where s.srbill_id = '%s' and s.goods_id = g.id and g.unit_id = u.id
              and s.rejection_goods_count > 0
              order by s.show_order";
    $data = $db->query($sql, $id);
    $items = [];
    foreach ($data as $v) {
      $items[] = [
        "goodsCode" => $v["code"],
        "goodsName" => $v["name"],
        "goodsSpec" => $v["spec"],
        "unitName" => $v["unit_name"],
        "goodsCount" => $v["rejection_goods_count"],
        "goodsPrice" => $v["rejection_goods_price"],
        "goodsMoney" => $v["rejection_sale_money"],
        "sn" => $v["sn_note"],
        "taxRate" => intval($v["tax_rate"]),
        "moneyWithTax" => $v["money_with_tax"]
      ];
    }
    $bill["items"] = $items;

    return $bill;
  }

  /**
   * 通过销售退货入库单单号查询销售退货入库单完整信息，包括明细记录
   *
   * @param string $ref
   *        	销售退货入库单单号
   * @return array|NULL
   */
  public function getFullBillDataByRef($ref)
  {
    $db = $this->db;

    $sql = "select w.id, w.bizdt, c.name as customer_name,
            u.name as biz_user_name,
            h.name as warehouse_name, wsBill.ref as ws_bill_ref, w.company_id
          from t_sr_bill w, t_customer c, t_user u, t_warehouse h, t_ws_bill wsBill
          where w.customer_id = c.id and w.biz_user_id = u.id
            and w.warehouse_id = h.id
            and w.ref = '%s' and wsBill.id = w.ws_bill_id";
    $data = $db->query($sql, $ref);
    if (!$data) {
      return NULL;
    }

    $id = $data[0]["id"];
    $companyId = $data[0]["company_id"];

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $result = [
      "bizDT" => $this->toYMD($data[0]["bizdt"]),
      "customerName" => $data[0]["customer_name"],
      "warehouseName" => $data[0]["warehouse_name"],
      "bizUserName" => $data[0]["biz_user_name"],
      "wsBillRef" => $data[0]["ws_bill_ref"]
    ];

    $sql = "select d.id, g.id as goods_id, g.code, g.name, g.spec, u.name as unit_name, 
              convert(d.goods_count, $fmt) as goods_count,
              d.goods_price, d.goods_money,
              convert(d.rejection_goods_count, $fmt) as rejection_goods_count, 
              d.rejection_goods_price, d.rejection_sale_money,
              d.wsbilldetail_id, d.sn_note
            from t_sr_bill_detail d, t_goods g, t_goods_unit u
            where d.srbill_id = '%s' and d.goods_id = g.id and g.unit_id = u.id
            order by d.show_order";
    $data = $db->query($sql, $id);
    $items = [];
    foreach ($data as $v) {
      $items[] = [
        "id" => $v["wsbilldetail_id"],
        "goodsId" => $v["goods_id"],
        "goodsCode" => $v["code"],
        "goodsName" => $v["name"],
        "goodsSpec" => $v["spec"],
        "unitName" => $v["unit_name"],
        "goodsCount" => $v["goods_count"],
        "goodsPrice" => $v["goods_price"],
        "goodsMoney" => $v["goods_money"],
        "rejCount" => $v["rejection_goods_count"],
        "rejPrice" => $v["rejection_goods_price"],
        "rejMoney" => $v["rejection_sale_money"],
        "sn" => $v["sn_note"]
      ];
    }

    $result["items"] = $items;

    return $result;
  }

  /**
   * 获得打印销售退货入库单的数据
   *
   * @param array $params
   */
  public function getSRBillDataForLodopPrint($params)
  {
    $db = $this->db;

    $id = $params["id"];

    $sql = "select w.ref, w.bizdt, c.name as customer_name, u.name as biz_user_name,
              user.name as input_user_name, h.name as warehouse_name, w.rejection_sale_money,
              w.bill_status, w.date_created, w.payment_type, w.company_id,
              w.rejection_sale_money_with_tax as money_with_tax
            from t_sr_bill w, t_customer c, t_user u, t_user user, t_warehouse h
            where (w.customer_id = c.id) and (w.biz_user_id = u.id)
              and (w.input_user_id = user.id) and (w.warehouse_id = h.id)
              and (w.id = '%s')";
    $data = $db->query($sql, $id);
    if (!$data) {
      return null;
    }

    $companyId = $data[0]["company_id"];

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $bill = [
      "ref" => $data[0]["ref"],
      "bizDT" => $this->toYMD($data[0]["bizdt"]),
      "customerName" => $data[0]["customer_name"],
      "warehouseName" => $data[0]["warehouse_name"],
      "bizUserName" => $data[0]["biz_user_name"],
      "rejMoney" => $data[0]["rejection_sale_money"],
      "printDT" => date("Y-m-d H:i:s"),
      "moneyWithTax" => $data[0]["money_with_tax"]
    ];

    // 明细表
    $sql = "select s.id, g.code, g.name, g.spec, u.name as unit_name,
              convert(s.rejection_goods_count, $fmt) as rejection_goods_count,
              s.rejection_goods_price, s.rejection_sale_money,
              s.sn_note, s.rejection_sale_money_with_tax as money_with_tax,
              s.tax_rate
            from t_sr_bill_detail s, t_goods g, t_goods_unit u
            where s.srbill_id = '%s' and s.goods_id = g.id and g.unit_id = u.id
              and s.rejection_goods_count > 0
              order by s.show_order";
    $data = $db->query($sql, $id);
    $items = [];
    foreach ($data as $v) {
      $items[] = [
        "goodsCode" => $v["code"],
        "goodsName" => $v["name"],
        "goodsSpec" => $v["spec"],
        "unitName" => $v["unit_name"],
        "goodsCount" => $v["rejection_goods_count"],
        "goodsPrice" => $v["rejection_goods_price"],
        "goodsMoney" => $v["rejection_sale_money"],
        "sn" => $v["sn_note"],
        "moneyWithTax" => $v["money_with_tax"],
        "taxRate" => intval($v["tax_rate"])
      ];
    }
    $bill["items"] = $items;

    return $bill;
  }
}
