<?php

namespace Home\DAO;

use Home\Common\FIdConst;

/**
 * 采购退货出库单 DAO
 *
 * @author 李静波
 */
class PRBillDAO extends PSIBaseExDAO
{

  /**
   * 生成新的采购退货出库单单号
   *
   * @param string $companyId
   *
   * @return string
   */
  private function genNewBillRef($companyId)
  {
    $db = $this->db;

    // 单号前缀
    $bs = new BizConfigDAO($db);
    $pre = $bs->getPRBillRefPre($companyId);

    $mid = date("Ymd");

    $sql = "select ref from t_pr_bill where ref like '%s' order by ref desc limit 1";
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
   * 新建采购退货出库单
   *
   * @param array $bill
   * @return NULL|array
   */
  public function addPRBill(&$bill)
  {
    $db = $this->db;

    // 业务日期
    $bizDT = $bill["bizDT"];

    // 仓库id
    $warehouseId = $bill["warehouseId"];
    $warehouseDAO = new WarehouseDAO($db);
    $warehouse = $warehouseDAO->getWarehouseById($warehouseId);
    if (!$warehouse) {
      return $this->bad("选择的仓库不存在，无法保存");
    }

    $bizUserId = $bill["bizUserId"];
    $userDAO = new UserDAO($db);
    $user = $userDAO->getUserById($bizUserId);
    if (!$user) {
      return $this->bad("选择的业务人员不存在，无法保存");
    }

    // 采购入库单id
    $pwBillId = $bill["pwBillId"];
    $sql = "select supplier_id from t_pw_bill where id = '%s' ";
    $data = $db->query($sql, $pwBillId);
    if (!$data) {
      return $this->bad("选择采购入库单不存在，无法保存");
    }

    // 供应商id
    $supplierId = $data[0]["supplier_id"];

    // 收款方式
    $receivingType = $bill["receivingType"];

    $billMemo = $bill["billMemo"];

    // 退货明细记录
    $items = $bill["items"];

    // 检查业务日期
    if (!$this->dateIsValid($bizDT)) {
      return $this->bad("业务日期不正确");
    }

    $id = $this->newId();

    $dataOrg = $bill["dataOrg"];
    if ($this->dataOrgNotExists($dataOrg)) {
      return $this->badParam("dataOrg");
    }
    $companyId = $bill["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->badParam("companyId");
    }
    $loginUserId = $bill["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->badParam("loginUserId");
    }

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    // 新增采购退货出库单
    // 生成单号
    $ref = $this->genNewBillRef($companyId);

    // 主表
    $sql = "insert into t_pr_bill(id, bill_status, bizdt, biz_user_id, supplier_id, date_created,
              input_user_id, ref, warehouse_id, pw_bill_id, receiving_type, data_org, company_id, bill_memo)
            values ('%s', 0, '%s', '%s', '%s', now(), '%s', '%s', '%s', '%s', %d, '%s', '%s', '%s')";
    $rc = $db->execute(
      $sql,
      $id,
      $bizDT,
      $bizUserId,
      $supplierId,
      $loginUserId,
      $ref,
      $warehouseId,
      $pwBillId,
      $receivingType,
      $dataOrg,
      $companyId,
      $billMemo
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 明细表
    $sql = "insert into t_pr_bill_detail(id, date_created, goods_id, goods_count, goods_price,
              goods_money, rejection_goods_count, rejection_goods_price, rejection_money, show_order,
              prbill_id, pwbilldetail_id, data_org, company_id, inventory_price, inventory_money, memo,
              tax_rate, tax, rejection_goods_price_with_tax, rejection_money_with_tax,
              goods_price_with_tax, goods_money_with_tax)
            values ('%s', now(), '%s', convert(%f, $fmt), %f, %f, convert(%f, $fmt),
              %f, %f, %d, '%s', '%s', '%s', '%s', 0, 0, '%s',
              %f, %f, %f, %f,
              %f, %f)";
    foreach ($items as $i => $v) {
      $pwbillDetailId = $v["id"];
      $goodsId = $v["goodsId"];
      $goodsCount = $v["goodsCount"];
      $goodsPrice = $v["goodsPrice"];
      $goodsMoney = $goodsCount * $goodsPrice;
      $rejCount = $v["rejCount"];
      $rejPrice = $v["rejPrice"];
      $rejMoney = $v["rejMoney"];
      $memo = $v["memo"];

      $taxRate = $v["taxRate"];
      $rejPriceWithTax = $v["rejPriceWithTax"];
      $rejMoneyWithTax = $v["rejMoneyWithTax"];
      // 负数表示红字
      $tax = -$rejMoney * $taxRate / 100;
      $goodsPriceWithTax = $v["goodsPriceWithTax"];
      $goodsMoneyWithTax = $v["goodsMoneyWithTax"];

      $rc = $db->execute(
        $sql,
        $this->newId(),
        $goodsId,
        $goodsCount,
        $goodsPrice,
        $goodsMoney,
        $rejCount,
        $rejPrice,
        $rejMoney,
        $i,
        $id,
        $pwbillDetailId,
        $dataOrg,
        $companyId,
        $memo,
        $taxRate,
        $tax,
        $rejPriceWithTax,
        $rejMoneyWithTax,
        $goodsPriceWithTax,
        $goodsMoneyWithTax
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 同步主表退货金额
    $sql = "select sum(rejection_money) as rej_money,
              sum(tax) as tax,
              sum(rejection_money_with_tax) as rej_money_with_tax
            from t_pr_bill_detail
            where prbill_id = '%s' ";
    $data = $db->query($sql, $id);
    $rejMoney = $data[0]["rej_money"];
    $rejMoneyWithTax = $data[0]["rej_money_with_tax"];
    $tax = $data[0]["tax"];

    $sql = "update t_pr_bill
            set rejection_money = %f, tax = %f, rejection_money_with_tax = %f
            where id = '%s' ";
    $rc = $db->execute($sql, $rejMoney, $tax, $rejMoneyWithTax, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $bill["id"] = $id;
    $bill["ref"] = $ref;

    // 操作成功
    return null;
  }

  /**
   * 根据采购退货出库单id查询采购退货出库单
   *
   * @param string $id
   * @return array|NULL
   */
  public function getPRBillById($id)
  {
    $db = $this->db;

    $sql = "select ref, bill_status, data_org, company_id
            from t_pr_bill
            where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return null;
    } else {
      return [
        "ref" => $data[0]["ref"],
        "billStatus" => $data[0]["bill_status"],
        "dataOrg" => $data[0]["data_org"],
        "companyId" => $data[0]["company_id"]
      ];
    }
  }

  /**
   * 编辑采购退货出库单
   *
   * @param array $bill
   * @return NULL|array
   */
  public function updatePRBill(&$bill)
  {
    $db = $this->db;

    $id = $bill["id"];

    $loginUserId = $bill["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->badParam("loginUserId");
    }

    // 业务日期
    $bizDT = $bill["bizDT"];

    // 仓库id
    $warehouseId = $bill["warehouseId"];
    $warehouseDAO = new WarehouseDAO($db);
    $warehouse = $warehouseDAO->getWarehouseById($warehouseId);
    if (!$warehouse) {
      return $this->bad("选择的仓库不存在，无法保存");
    }

    // 业务员id
    $bizUserId = $bill["bizUserId"];
    $userDAO = new UserDAO($db);
    $user = $userDAO->getUserById($bizUserId);
    if (!$user) {
      return $this->bad("选择的业务人员不存在，无法保存");
    }

    // 要退货的采购入库单id
    $pwBillId = $bill["pwBillId"];
    $sql = "select supplier_id from t_pw_bill where id = '%s' ";
    $data = $db->query($sql, $pwBillId);
    if (!$data) {
      return $this->bad("选择采购入库单不存在，无法保存");
    }

    // 收款方式
    $receivingType = $bill["receivingType"];

    $billMemo = $bill["billMemo"];

    // 退货商品明细记录
    $items = $bill["items"];

    // 检查业务日期
    if (!$this->dateIsValid($bizDT)) {
      return $this->bad("业务日期不正确");
    }

    $oldBill = $this->getPRBillById($id);
    if (!$oldBill) {
      return $this->bad("要编辑的采购退货出库单不存在");
    }

    // 单号
    $ref = $oldBill["ref"];

    $companyId = $oldBill["companyId"];
    $billStatus = $oldBill["billStatus"];
    if ($billStatus != 0) {
      return $this->bad("采购退货出库单(单号：$ref)已经提交，不能再被编辑");
    }
    $dataOrg = $oldBill["data_org"];

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    // 明细表
    // 先删除旧数据，再插入新记录
    $sql = "delete from t_pr_bill_detail where prbill_id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $sql = "insert into t_pr_bill_detail(id, date_created, goods_id, goods_count, goods_price,
              goods_money, rejection_goods_count, rejection_goods_price, rejection_money, show_order,
              prbill_id, pwbilldetail_id, data_org, company_id, inventory_price, inventory_money, memo,
              tax_rate, tax, rejection_goods_price_with_tax, rejection_money_with_tax,
              goods_price_with_tax, goods_money_with_tax)
            values ('%s', now(), '%s', convert(%f, $fmt), %f, %f, 
              convert(%f, $fmt), %f, %f, %d, '%s', '%s', '%s', '%s', 0, 0, '%s',
              %f, %f, %f, %f,
              %f, %f)";
    foreach ($items as $i => $v) {
      $pwbillDetailId = $v["id"];
      $goodsId = $v["goodsId"];
      $goodsCount = $v["goodsCount"];
      $goodsPrice = $v["goodsPrice"];
      $goodsMoney = $goodsCount * $goodsPrice;
      $rejCount = $v["rejCount"];
      $rejPrice = $v["rejPrice"];
      $rejMoney = $v["rejMoney"];
      $memo = $v["memo"];

      $taxRate = $v["taxRate"];
      $rejPriceWithTax = $v["rejPriceWithTax"];
      $rejMoneyWithTax = $v["rejMoneyWithTax"];
      // 负数表示红字
      $tax = -$rejMoney * $taxRate / 100;
      $goodsPriceWithTax = $v["goodsPriceWithTax"];
      $goodsMoneyWithTax = $v["goodsMoneyWithTax"];

      $rc = $db->execute(
        $sql,
        $this->newId(),
        $goodsId,
        $goodsCount,
        $goodsPrice,
        $goodsMoney,
        $rejCount,
        $rejPrice,
        $rejMoney,
        $i,
        $id,
        $pwbillDetailId,
        $dataOrg,
        $companyId,
        $memo,
        $taxRate,
        $tax,
        $rejPriceWithTax,
        $rejMoneyWithTax,
        $goodsPriceWithTax,
        $goodsMoneyWithTax
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 同步主表退货金额
    $sql = "select sum(rejection_money) as rej_money,
              sum(tax) as tax,
              sum(rejection_money_with_tax) as rej_money_with_tax
            from t_pr_bill_detail
            where prbill_id = '%s' ";
    $data = $db->query($sql, $id);
    $rejMoney = $data[0]["rej_money"] ?? 0;
    $rejMoneyWithTax = $data[0]["rej_money_with_tax"] ?? 0;
    $tax = $data[0]["tax"] ?? 0;

    $sql = "update t_pr_bill
            set rejection_money = %f,
              bizdt = '%s', biz_user_id = '%s',
              date_created = now(), input_user_id = '%s',
              warehouse_id = '%s', receiving_type = %d,
              bill_memo = '%s',
              tax = %f, rejection_money_with_tax = %f
            where id = '%s' ";
    $rc = $db->execute(
      $sql,
      $rejMoney,
      $bizDT,
      $bizUserId,
      $loginUserId,
      $warehouseId,
      $receivingType,
      $billMemo,
      $tax,
      $rejMoneyWithTax,
      $id
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $bill["ref"] = $ref;

    // 操作成功
    return null;
  }

  /**
   * 选择可以退货的采购入库单
   *
   * @param array $params
   * @return array
   */
  public function selectPWBillList($params)
  {
    $db = $this->db;

    $start = $params["start"];
    $limit = $params["limit"];

    $ref = $params["ref"];
    $supplierId = $params["supplierId"];
    $warehouseId = $params["warehouseId"];
    $fromDT = $params["fromDT"];
    $toDT = $params["toDT"];

    $loginUserId = $params["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->emptyResult();
    }

    $result = [];

    $sql = "select p.id, p.ref, p.biz_dt, s.name as supplier_name, p.goods_money,
              w.name as warehouse_name, u1.name as biz_user_name, u2.name as input_user_name,
              p.tax, p.money_with_tax
            from t_pw_bill p, t_supplier s, t_warehouse w, t_user u1, t_user u2
            where (p.supplier_id = s.id)
              and (p.warehouse_id = w.id)
              and (p.biz_user_id = u1.id)
              and (p.input_user_id = u2.id)
              and (p.bill_status = 1000 or p.bill_status = 2000)";
    $queryParamas = [];
    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::PURCHASE_REJECTION, "p", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParamas = $rs[1];
    }

    if ($ref) {
      $sql .= " and (p.ref like '%s') ";
      $queryParamas[] = "%$ref%";
    }
    if ($supplierId) {
      $sql .= " and (p.supplier_id = '%s') ";
      $queryParamas[] = $supplierId;
    }
    if ($warehouseId) {
      $sql .= " and (p.warehouse_id = '%s') ";
      $queryParamas[] = $warehouseId;
    }
    if ($fromDT) {
      $sql .= " and (p.biz_dt >= '%s') ";
      $queryParamas[] = $fromDT;
    }
    if ($toDT) {
      $sql .= " and (p.biz_dt <= '%s') ";
      $queryParamas[] = $toDT;
    }

    $sql .= " order by p.ref desc limit %d, %d";
    $queryParamas[] = $start;
    $queryParamas[] = $limit;

    $data = $db->query($sql, $queryParamas);
    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "ref" => $v["ref"],
        "bizDate" => $this->toYMD($v["biz_dt"]),
        "supplierName" => $v["supplier_name"],
        "amount" => $v["goods_money"],
        "warehouseName" => $v["warehouse_name"],
        "bizUserName" => $v["biz_user_name"],
        "inputUserName" => $v["input_user_name"],
        "tax" => $v["tax"],
        "moneyWithTax" => $v["money_with_tax"]
      ];
    }

    $sql = "select count(*) as cnt
            from t_pw_bill p, t_supplier s, t_warehouse w, t_user u1, t_user u2
            where (p.supplier_id = s.id)
              and (p.warehouse_id = w.id)
              and (p.biz_user_id = u1.id)
              and (p.input_user_id = u2.id)
              and (p.bill_status = 1000)";
    $queryParamas = [];
    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::PURCHASE_REJECTION, "p", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParamas = $rs[1];
    }

    if ($ref) {
      $sql .= " and (p.ref like '%s') ";
      $queryParamas[] = "%$ref%";
    }
    if ($supplierId) {
      $sql .= " and (p.supplier_id = '%s') ";
      $queryParamas[] = $supplierId;
    }
    if ($warehouseId) {
      $sql .= " and (p.warehouse_id = '%s') ";
      $queryParamas[] = $warehouseId;
    }
    if ($fromDT) {
      $sql .= " and (p.biz_dt >= '%s') ";
      $queryParamas[] = $fromDT;
    }
    if ($toDT) {
      $sql .= " and (p.biz_dt <= '%s') ";
      $queryParamas[] = $toDT;
    }

    $data = $db->query($sql, $queryParamas);
    $cnt = $data[0]["cnt"];

    return [
      "dataList" => $result,
      "totalCount" => $cnt
    ];
  }

  /**
   * 根据采购入库单的id查询采购入库单的详细信息
   *
   * @param array $params
   * @return array
   */
  public function getPWBillInfoForPRBill($params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    // 采购入库单id
    $id = $params["id"];

    $result = [];

    $sql = "select p.ref,s.id as supplier_id, s.name as supplier_name,
              w.id as warehouse_id, w.name as warehouse_name
            from t_pw_bill p, t_supplier s, t_warehouse w
            where p.supplier_id = s.id
              and p.warehouse_id = w.id
              and p.id = '%s' ";

    $data = $db->query($sql, $id);
    if (!$data) {
      return $result;
    }

    $result["ref"] = $data[0]["ref"];
    $result["supplierId"] = $data[0]["supplier_id"];
    $result["supplierName"] = $data[0]["supplier_name"];
    $result["warehouseId"] = $data[0]["warehouse_id"];
    $result["warehouseName"] = $data[0]["warehouse_name"];

    $items = [];

    // 明细表
    $sql = "select p.id, g.id as goods_id, g.code as goods_code, g.name as goods_name,
              g.spec as goods_spec, u.name as unit_name,
              convert(p.goods_count, $fmt) as goods_count, p.goods_price, p.goods_money,
              p.tax_rate, p.money_with_tax, p.goods_price_with_tax
            from t_pw_bill_detail p, t_goods g, t_goods_unit u
            where p.goods_id = g.id
              and g.unit_id = u.id
              and p.pwbill_id = '%s'
              order by p.show_order ";
    $data = $db->query($sql, $id);
    foreach ($data as $v) {
      $items[] = [
        "id" => $v["id"],
        "goodsId" => $v["goods_id"],
        "goodsCode" => $v["goods_code"],
        "goodsName" => $v["goods_name"],
        "goodsSpec" => $v["goods_spec"],
        "unitName" => $v["unit_name"],
        "goodsCount" => $v["goods_count"],
        "goodsPrice" => $v["goods_price"],
        "goodsMoney" => $v["goods_money"],
        "rejPrice" => $v["goods_price"],
        "taxRate" => $v["tax_rate"],
        "rejPriceWithTax" => $v["goods_price_with_tax"],
        "goodsMoneyWithTax" => $v["money_with_tax"],
        "goodsPriceWithTax" => $v["goods_price_with_tax"]
      ];
    }

    $result["items"] = $items;

    return $result;
  }

  /**
   * 采购退货出库单列表
   *
   * @param array $params
   * @return array
   */
  public function prbillList($params)
  {
    $db = $this->db;

    $start = $params["start"];
    $limit = $params["limit"];

    $billStatus = $params["billStatus"];
    $ref = $params["ref"];
    $fromDT = $params["fromDT"];
    $toDT = $params["toDT"];
    $warehouseId = $params["warehouseId"];
    $supplierId = $params["supplierId"];
    $receivingType = $params["receivingType"];
    $goodsId = $params["goodsId"];

    $loginUserId = $params["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->emptyResult();
    }

    $result = [];
    $queryParams = [];
    $sql = "select p.id, p.ref, p.bill_status, w.name as warehouse_name, p.bizdt,
              p.rejection_money, u1.name as biz_user_name, u2.name as input_user_name,
              s.name as supplier_name, p.date_created, p.receiving_type, p.bill_memo,
              p.tax, p.rejection_money_with_tax
            from t_pr_bill p, t_warehouse w, t_user u1, t_user u2, t_supplier s
            where (p.warehouse_id = w.id)
              and (p.biz_user_id = u1.id)
              and (p.input_user_id = u2.id)
              and (p.supplier_id = s.id) ";

    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::PURCHASE_REJECTION, "p", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = $rs[1];
    }

    if ($billStatus != -1) {
      $sql .= " and (p.bill_status = %d) ";
      $queryParams[] = $billStatus;
    }
    if ($ref) {
      $sql .= " and (p.ref like '%s') ";
      $queryParams[] = "%{$ref}%";
    }
    if ($fromDT) {
      $sql .= " and (p.bizdt >= '%s') ";
      $queryParams[] = $fromDT;
    }
    if ($toDT) {
      $sql .= " and (p.bizdt <= '%s') ";
      $queryParams[] = $toDT;
    }
    if ($supplierId) {
      $sql .= " and (p.supplier_id = '%s') ";
      $queryParams[] = $supplierId;
    }
    if ($warehouseId) {
      $sql .= " and (p.warehouse_id = '%s') ";
      $queryParams[] = $warehouseId;
    }
    if ($receivingType != -1) {
      $sql .= " and (p.receiving_type = %d) ";
      $queryParams[] = $receivingType;
    }
    if ($goodsId) {
      $sql .= " and (p.id in (select distinct prbill_id from t_pr_bill_detail where goods_id = '%s' and rejection_goods_count > 0)) ";
      $queryParams[] = $goodsId;
    }

    $sql .= " order by p.bizdt desc, p.ref desc
              limit %d, %d";
    $queryParams[] = $start;
    $queryParams[] = $limit;
    $data = $db->query($sql, $queryParams);
    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "ref" => $v["ref"],
        "billStatus" => $v["bill_status"] == 0 ? "待出库" : "已出库",
        "warehouseName" => $v["warehouse_name"],
        "supplierName" => $v["supplier_name"],
        "rejMoney" => $v["rejection_money"],
        "bizUserName" => $v["biz_user_name"],
        "inputUserName" => $v["input_user_name"],
        "bizDT" => $this->toYMD($v["bizdt"]),
        "dateCreated" => $v["date_created"],
        "receivingType" => $v["receiving_type"],
        "billMemo" => $v["bill_memo"],
        "tax" => $v["tax"],
        "rejMoneyWithTax" => $v["rejection_money_with_tax"]
      ];
    }

    $sql = "select count(*) as cnt
            from t_pr_bill p, t_warehouse w, t_user u1, t_user u2, t_supplier s
            where (p.warehouse_id = w.id)
              and (p.biz_user_id = u1.id)
              and (p.input_user_id = u2.id)
              and (p.supplier_id = s.id) ";
    $queryParams = [];
    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::PURCHASE_REJECTION, "p", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = $rs[1];
    }

    if ($billStatus != -1) {
      $sql .= " and (p.bill_status = %d) ";
      $queryParams[] = $billStatus;
    }
    if ($ref) {
      $sql .= " and (p.ref like '%s') ";
      $queryParams[] = "%{$ref}%";
    }
    if ($fromDT) {
      $sql .= " and (p.bizdt >= '%s') ";
      $queryParams[] = $fromDT;
    }
    if ($toDT) {
      $sql .= " and (p.bizdt <= '%s') ";
      $queryParams[] = $toDT;
    }
    if ($supplierId) {
      $sql .= " and (p.supplier_id = '%s') ";
      $queryParams[] = $supplierId;
    }
    if ($warehouseId) {
      $sql .= " and (p.warehouse_id = '%s') ";
      $queryParams[] = $warehouseId;
    }
    if ($receivingType != -1) {
      $sql .= " and (p.receiving_type = %d) ";
      $queryParams[] = $receivingType;
    }
    if ($goodsId) {
      $sql .= " and (p.id in (select distinct prbill_id from t_pr_bill_detail where goods_id = '%s' and rejection_goods_count > 0)) ";
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
   * 采购退货出库单明细列表
   *
   * @param array $params
   * @return array
   */
  public function prBillDetailList($params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    // id：采购退货出库单id
    $id = $params["id"];

    $sql = "select g.code, g.name, g.spec, u.name as unit_name,
              convert(p.rejection_goods_count, $fmt) as rej_count, p.rejection_goods_price as rej_price,
              p.rejection_money as rej_money, p.memo, p.tax, p.tax_rate,
              p.rejection_money_with_tax, p.rejection_goods_price_with_tax
            from t_pr_bill_detail p, t_goods g, t_goods_unit u
            where p.goods_id = g.id and g.unit_id = u.id and p.prbill_id = '%s'
              and p.rejection_goods_count > 0
              order by p.show_order";
    $result = [];
    $data = $db->query($sql, $id);
    foreach ($data as $v) {
      $result[] = [
        "goodsCode" => $v["code"],
        "goodsName" => $v["name"],
        "goodsSpec" => $v["spec"],
        "unitName" => $v["unit_name"],
        "rejCount" => $v["rej_count"],
        "rejPrice" => $v["rej_price"],
        "rejMoney" => $v["rej_money"],
        "memo" => $v["memo"],
        "tax" => $v["tax"],
        "taxRate" => $v["tax_rate"],
        "rejPriceWithTax" => $v["rejection_goods_price_with_tax"],
        "rejMoneyWithTax" => $v["rejection_money_with_tax"]
      ];
    }

    return $result;
  }

  /**
   * 查询采购退货出库单详情
   *
   * @param array $params
   * @return array
   */
  public function prBillInfo($params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    // id:采购退货出库单id
    $id = $params["id"];

    $result = [];

    if ($id) {
      // 编辑
      $sql = "select p.ref, p.bill_status, p.warehouse_id, w.name as warehouse_name,
                p.biz_user_id, u.name as biz_user_name, pw.ref as pwbill_ref,
                s.name as supplier_name, s.id as supplier_id,
                p.pw_bill_id as pwbill_id, p.bizdt, p.receiving_type, p.bill_memo
              from t_pr_bill p, t_warehouse w, t_user u, t_pw_bill pw, t_supplier s
              where p.id = '%s'
                and p.warehouse_id = w.id
                and p.biz_user_id = u.id
                and p.pw_bill_id = pw.id
                and p.supplier_id = s.id ";
      $data = $db->query($sql, $id);
      if (!$data) {
        return $result;
      }

      $result["ref"] = $data[0]["ref"];
      $result["billStatus"] = $data[0]["bill_status"];
      $result["bizUserId"] = $data[0]["biz_user_id"];
      $result["bizUserName"] = $data[0]["biz_user_name"];
      $result["warehouseId"] = $data[0]["warehouse_id"];
      $result["warehouseName"] = $data[0]["warehouse_name"];
      $result["pwbillRef"] = $data[0]["pwbill_ref"];
      $result["supplierId"] = $data[0]["supplier_id"];
      $result["supplierName"] = $data[0]["supplier_name"];
      $result["pwbillId"] = $data[0]["pwbill_id"];
      $result["bizDT"] = $this->toYMD($data[0]["bizdt"]);
      $result["receivingType"] = $data[0]["receiving_type"];
      $result["billMemo"] = $data[0]["bill_memo"];

      $items = [];
      $sql = "select p.pwbilldetail_id as id, p.goods_id, g.code as goods_code, g.name as goods_name,
                g.spec as goods_spec, u.name as unit_name, convert(p.goods_count, $fmt) as goods_count,
                p.goods_price, p.goods_money, convert(p.rejection_goods_count, $fmt) as rej_count,
                p.rejection_goods_price as rej_price, p.rejection_money as rej_money, p.memo,
                p.tax_rate, p.rejection_goods_price_with_tax, p.rejection_money_with_tax,
                p.goods_price_with_tax, p.goods_money_with_tax
              from t_pr_bill_detail p, t_goods g, t_goods_unit u
              where p.prbill_id = '%s'
                and p.goods_id = g.id
                and g.unit_id = u.id
              order by p.show_order";
      $data = $db->query($sql, $id);
      foreach ($data as $v) {
        $items[] = [
          "id" => $v["id"],
          "goodsId" => $v["goods_id"],
          "goodsCode" => $v["goods_code"],
          "goodsName" => $v["goods_name"],
          "goodsSpec" => $v["goods_spec"],
          "unitName" => $v["unit_name"],
          "goodsCount" => $v["goods_count"],
          "goodsPrice" => $v["goods_price"],
          "goodsMoney" => $v["goods_money"],
          "rejCount" => $v["rej_count"],
          "rejPrice" => $v["rej_price"],
          "rejMoney" => $v["rej_money"],
          "memo" => $v["memo"],
          "taxRate" => $v["tax_rate"],
          "rejPriceWithTax" => $v["rejection_goods_price_with_tax"],
          "rejMoneyWithTax" => $v["rejection_money_with_tax"],
          "goodsPriceWithTax" => $v["goods_price_with_tax"],
          "goodsMoneyWithTax" => $v["goods_money_with_tax"]
        ];
      }

      $result["items"] = $items;
    } else {
      // 新建
      $result["bizUserId"] = $params["loginUserId"];
      $result["bizUserName"] = $params["loginUserName"];
    }

    return $result;
  }

  /**
   * 删除采购退货出库单
   *
   * @param array $params
   * @return NULL|array
   */
  public function deletePRBill(&$params)
  {
    $db = $this->db;

    $id = $params["id"];

    $bill = $this->getPRBillById($id);

    if (!$bill) {
      return $this->bad("要删除的采购退货出库单不存在");
    }
    $ref = $bill["ref"];
    $billStatus = $bill["billStatus"];
    if ($billStatus != 0) {
      return $this->bad("采购退货出库单(单号：$ref)已经提交，不能被删除");
    }

    $sql = "delete from t_pr_bill_detail where prbill_id = '%s'";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $sql = "delete from t_pr_bill where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $params["ref"] = $ref;

    // 操作成功
    return null;
  }

  /**
   * 提交采购退货出库单
   *
   * @param array $params
   *
   * @return null|array
   */
  public function commitPRBill(&$params)
  {
    $db = $this->db;

    $id = $params["id"];

    $sql = "select ref, bill_status, warehouse_id, bizdt, biz_user_id, rejection_money,
              supplier_id, receiving_type, company_id, pw_bill_id, rejection_money_with_tax
            from t_pr_bill
            where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return $this->bad("要提交的采购退货出库单不存在");
    }
    $ref = $data[0]["ref"];
    $billStatus = $data[0]["bill_status"];
    $warehouseId = $data[0]["warehouse_id"];
    $bizDT = $this->toYMD($data[0]["bizdt"]);
    $bizUserId = $data[0]["biz_user_id"];
    $allRejMoney = $data[0]["rejection_money_with_tax"] ?? $data[0]["rejection_money"];
    $supplierId = $data[0]["supplier_id"];
    $receivingType = $data[0]["receiving_type"];
    $companyId = $data[0]["company_id"];
    $pwBillId = $data[0]["pw_bill_id"];

    if ($billStatus != 0) {
      return $this->bad("采购退货出库单(单号：$ref)已经提交，不能再次提交");
    }

    $bs = new BizConfigDAO($db);
    $fifo = $bs->getInventoryMethod($companyId) == 1;

    $dataScale = $bs->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $warehouseDAO = new WarehouseDAO($db);
    $warehouse = $warehouseDAO->getWarehouseById($warehouseId);
    if (!$warehouse) {
      return $this->bad("要出库的仓库不存在");
    }
    $warehouseName = $warehouse["name"];
    $inited = $warehouse["inited"];
    if ($inited != 1) {
      return $this->bad("仓库[$warehouseName]还没有完成库存建账，不能进行出库操作");
    }

    $userDAO = new UserDAO($db);
    $user = $userDAO->getUserById($bizUserId);
    if (!$user) {
      return $this->bad("业务人员不存在，无法完成提交操作");
    }

    $supplierDAO = new SupplierDAO($db);
    $supplier = $supplierDAO->getSupplierById($supplierId);
    if (!$supplier) {
      return $this->bad("供应商不存在，无法完成提交操作");
    }

    $allReceivingType = array(
      0,
      1
    );
    if (!in_array($receivingType, $allReceivingType)) {
      return $this->bad("收款方式不正确，无法完成提交操作");
    }

    $sql = "select goods_id, convert(rejection_goods_count, $fmt) as rej_count,
              rejection_money as rej_money,
              convert(goods_count, $fmt) as goods_count, goods_price, pwbilldetail_id
            from t_pr_bill_detail
            where prbill_id = '%s'
            order by show_order";
    $items = $db->query($sql, $id);
    foreach ($items as $i => $v) {
      $goodsId = $v["goods_id"];
      $rejCount = $v["rej_count"];
      $goodsCount = $v["goods_count"];
      $goodsPricePurchase = $v["goods_price"];

      $pwbillDetailId = $v["pwbilldetail_id"];
      // 检查$pwbillDetailId对应的记录在t_pw_bill_detail中是否存在
      $sql = "select convert(goods_count, $fmt) as goods_count,
                convert(rej_goods_count, $fmt) as rej_goods_count,
                convert(real_goods_count, $fmt) as real_goods_count
              from t_pw_bill_detail 
              where id = '%s' ";
      $d = $db->query($sql, $pwbillDetailId);
      if (!$d) {
        $index = $i + 1;
        return $this->bad("第{$index}条记录在t_pw_bill_detail中没有对应记录，本单据数据有错误，不能提交");
      }
      $pwGoodsCount = $d[0]["goods_count"];
      $hasRejGoodsCount = $d[0]["rej_goods_count"];
      $realGoodsCount = $d[0]["real_goods_count"];
      $sum = $hasRejGoodsCount + $realGoodsCount;
      if ($sum != $pwGoodsCount) {
        // 因为rej_goods_count和real_goods_count是后加的字段，原来的旧数据是为空
        // 就会有可能执行到这里
        $index = $i + 1;
        return $this->bad("第{$index}条记录在t_pw_bill_detail中的对应记录中的rej_goods_count和real_goods_count有误，不能提交");
      }

      if ($rejCount == 0) {
        continue;
      }

      if ($rejCount < 0) {
        $index = $i + 1;
        return $this->bad("第{$index}条记录的退货数量不能为负数");
      }
      if ($rejCount > $realGoodsCount) {
        $index = $i + 1;
        return $this->bad("第{$index}条记录的退货数量不能大于采购数量");
      }

      // 调整采购入库单明细记录中的退货数量
      $hasRejGoodsCount += $rejCount;
      $realGoodsCount -= $rejCount;
      $sql = "update t_pw_bill_detail
              set rej_goods_count = convert(%f, $fmt), 
                  real_goods_count = convert(%f, $fmt)
              where id = '%s' ";
      $rc = $db->execute($sql, $hasRejGoodsCount, $realGoodsCount, $pwbillDetailId);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }

      // 调整采购订单明细记录中的退货数量
      // 先检查退货对应的入库单是不是由采购订单生成的
      $sql = "select pod.id, convert(pod.rej_count, $fmt) as rej_count, 
                convert(pod.pw_count, $fmt) as pw_count,
                convert(pod.goods_count, $fmt) as goods_count
              from t_po_bill_detail pod, t_pw_bill_detail pwd
              where pod.id = pwd.pobilldetail_id and pwd.id = '%s' ";
      $poDetail = $db->query($sql, $pwbillDetailId);
      if ($poDetail) {
        // 采购入库单是由采购订单生成的
        // 同步退货数量
        $poDetailId = $poDetail[0]["id"];
        $poGoodsCount = $poDetail[0]["goods_count"];
        $poPWCount = $poDetail[0]["pw_count"];
        $poRejCount = $poDetail[0]["rej_count"];

        $poRejCount += $rejCount;
        $poLeftCount = $poGoodsCount - $poPWCount + $poRejCount;
        $poRealCount = $poPWCount - $poRejCount;
        $sql = "update t_po_bill_detail
                set rej_count = convert(%f, $fmt),
                    left_count = convert(%f, $fmt),
                    real_count = convert(%f, $fmt)
                where id = '%s' ";
        $rc = $db->execute($sql, $poRejCount, $poLeftCount, $poRealCount,  $poDetailId);
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      }

      if ($fifo) {
        // 先进先出

        $sql = "select balance_count, balance_price, balance_money,
                  out_count, out_money, date_created
                from t_inventory_fifo
                where pwbilldetail_id = '%s' ";
        $data = $db->query($sql, $pwbillDetailId);
        if (!$data) {
          $index = $i + 1;
          return $this->bad("第{$index}条商品库存不足，无法退货");
        }
        $fifoDateCreated = $data[0]["date_created"];
        $fifoOutCount = $data[0]["out_count"];
        if (!$fifoOutCount) {
          $fifoOutCount = 0;
        }
        $fifoOutMoney = $data[0]["out_money"];
        if (!$fifoOutMoney) {
          $fifoOutMoney = 0;
        }
        $fifoBalanceCount = $data[0]["balance_count"];
        if ($fifoBalanceCount < $rejCount) {
          $index = $i + 1;
          return $this->bad("第{$index}条商品库存不足，无法退货");
        }
        $fifoBalancePrice = $data[0]["balance_price"];
        $fifoBalanceMoney = $data[0]["balance_money"];
        $outMoney = 0;
        if ($rejCount == $fifoBalanceCount) {
          $outMoney = $fifoBalanceMoney;
        } else {
          $outMoney = $fifoBalancePrice * $rejCount;
        }

        // 库存总账
        $sql = "select balance_count, balance_price, balance_money,
                  out_count, out_money
                from t_inventory
                where warehouse_id = '%s' and goods_id = '%s' ";
        $data = $db->query($sql, $warehouseId, $goodsId);
        if (!$data) {
          $index = $i + 1;
          return $this->bad("第{$index}条商品库存不足，无法退货");
        }
        $balanceCount = $data[0]["balance_count"];
        $balancePrice = $data[0]["balance_price"];
        $balanceMoney = $data[0]["balance_money"];

        $totalOutCount = $data[0]["out_count"];
        $totalOutMoney = $data[0]["out_money"];

        $outCount = $rejCount;
        $outPrice = $outMoney / $rejCount;

        $totalOutCount += $outCount;
        $totalOutMoney += $outMoney;
        $totalOutPrice = $totalOutMoney / $totalOutCount;
        $balanceCount -= $outCount;
        if ($balanceCount == 0) {
          $balanceMoney -= $outMoney;
          $balancePrice = 0;
        } else {
          $balanceMoney -= $outMoney;
          $balancePrice = $balanceMoney / $balanceCount;
        }

        $sql = "update t_inventory
                set out_count = %d, out_price = %f, out_money = %f,
                  balance_count = %d, balance_price = %f, balance_money = %f
                where warehouse_id = '%s' and goods_id = '%s' ";
        $rc = $db->execute(
          $sql,
          $totalOutCount,
          $totalOutPrice,
          $totalOutMoney,
          $balanceCount,
          $balancePrice,
          $balanceMoney,
          $warehouseId,
          $goodsId
        );
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }

        // 库存明细账
        $sql = "insert into t_inventory_detail(out_count, out_price, out_money, balance_count,
                  balance_price, balance_money, warehouse_id, goods_id, biz_date, biz_user_id,
                  date_created, ref_number, ref_type)
                values (%d, %f, %f, %d, %f, %f, '%s', '%s', '%s', '%s', now(), '%s', '采购退货出库')";
        $rc = $db->execute(
          $sql,
          $outCount,
          $outPrice,
          $outMoney,
          $balanceCount,
          $balancePrice,
          $balanceMoney,
          $warehouseId,
          $goodsId,
          $bizDT,
          $bizUserId,
          $ref
        );
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }

        // fifo
        $fvOutCount = $outCount + $fifoOutCount;
        $fvOutMoney = $outMoney + $fifoOutMoney;
        $fvBalanceCount = $fifoBalanceCount - $outCount;
        $fvBalanceMoney = 0;
        if ($fvBalanceCount > 0) {
          $fvBalanceMoney = $fifoBalanceMoney - $outMoney;
        }
        $sql = "update t_inventory_fifo
                set out_count = %d, out_price = %f, out_money = %f, balance_count = %d,
                  balance_money = %f
                where pwbilldetail_id = '%s' ";
        $rc = $db->execute(
          $sql,
          $fvOutCount,
          $fvOutMoney / $fvOutCount,
          $fvOutMoney,
          $fvBalanceCount,
          $fvBalanceMoney,
          $pwbillDetailId
        );
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }

        // fifo的明细记录
        $sql = "insert into t_inventory_fifo_detail(date_created,
                  out_count, out_price, out_money, balance_count, balance_price, balance_money,
                  warehouse_id, goods_id)
                values ('%s', %d, %f, %f, %d, %f, %f, '%s', '%s')";
        $rc = $db->execute(
          $sql,
          $fifoDateCreated,
          $outCount,
          $outPrice,
          $outMoney,
          $fvBalanceCount,
          $outPrice,
          $fvBalanceMoney,
          $warehouseId,
          $goodsId
        );
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      } else {
        // 移动平均法

        // 库存总账
        $sql = "select convert(balance_count, $fmt) as balance_count, balance_price, balance_money,
                  convert(out_count, $fmt) as out_count, out_money
                from t_inventory
                where warehouse_id = '%s' and goods_id = '%s' ";
        $data = $db->query($sql, $warehouseId, $goodsId);
        if (!$data) {
          $index = $i + 1;
          return $this->bad("第{$index}条商品库存不足，无法退货");
        }
        $balanceCount = $data[0]["balance_count"];
        $balancePrice = $data[0]["balance_price"];
        $balanceMoney = $data[0]["balance_money"];
        if ($rejCount > $balanceCount) {
          $index = $i + 1;
          return $this->bad("第{$index}条商品库存不足，无法退货");
        }
        $totalOutCount = $data[0]["out_count"];
        $totalOutMoney = $data[0]["out_money"];

        $outCount = $rejCount;
        $outMoney = $goodsPricePurchase * $outCount;
        $outPrice = $goodsPricePurchase;

        if ($outMoney > $balanceMoney) {
          // 这种情况的出现，是因为采购入库导致存货成本增加之后又发生了出库业务
          // 再退货的时候，出现库存数量够，但是库存金额不够
          // 这个时候退货成本就不能取原来的采购入库成本了，只能用当前的存货成本
          $outPrice = $balancePrice;
          $outMoney = $outPrice * $outCount;
          if ($outMoney > $balanceMoney) {
            // 超过余额，是因为单价两位小数有计算误差
            $outMoney = $balanceMoney;
            $outPrice = $outMoney / $outCount;
          }
        }

        $totalOutCount += $outCount;
        $totalOutMoney += $outMoney;
        $totalOutPrice = $totalOutMoney / $totalOutCount;
        $balanceCount -= $outCount;
        if ($balanceCount == 0) {
          // 基本原则：数量为0的时候，保持存货金额也为0
          $outMoney = $balanceMoney;
          $outPrice = $outMoney / $outCount;

          $balanceMoney = 0;
          $balancePrice = $outPrice;
        } else {
          $balanceMoney -= $outMoney;
          $balancePrice = $balanceMoney / $balanceCount;
        }

        $sql = "update t_inventory
                set out_count = convert(%f, $fmt), out_price = %f, out_money = %f,
                  balance_count = convert(%f, $fmt), balance_price = %f, balance_money = %f
                where warehouse_id = '%s' and goods_id = '%s' ";
        $rc = $db->execute(
          $sql,
          $totalOutCount,
          $totalOutPrice,
          $totalOutMoney,
          $balanceCount,
          $balancePrice,
          $balanceMoney,
          $warehouseId,
          $goodsId
        );
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }

        // 库存明细账
        $sql = "insert into t_inventory_detail(out_count, out_price, out_money, balance_count,
                  balance_price, balance_money, warehouse_id, goods_id, biz_date, biz_user_id,
                  date_created, ref_number, ref_type)
                values (convert(%f, $fmt), %f, %f, convert(%f, $fmt), %f, %f, '%s', '%s', '%s', '%s', now(), '%s', '采购退货出库')";
        $rc = $db->execute(
          $sql,
          $outCount,
          $outPrice,
          $outMoney,
          $balanceCount,
          $balancePrice,
          $balanceMoney,
          $warehouseId,
          $goodsId,
          $bizDT,
          $bizUserId,
          $ref
        );
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      }
    }

    if ($receivingType == 0) {
      // 记应收账款
      // 应收总账
      $sql = "select rv_money, balance_money
              from t_receivables
              where ca_id = '%s' and ca_type = 'supplier'
                and company_id = '%s' ";
      $data = $db->query($sql, $supplierId, $companyId);
      if (!$data) {
        $sql = "insert into t_receivables(id, rv_money, act_money, balance_money, ca_id, ca_type,
                  company_id)
                values ('%s', %f, 0, %f, '%s', 'supplier', '%s')";
        $rc = $db->execute(
          $sql,
          $this->newId(),
          $allRejMoney,
          $allRejMoney,
          $supplierId,
          $companyId
        );
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      } else {
        $rvMoney = $data[0]["rv_money"];
        $balanceMoney = $data[0]["balance_money"];
        $rvMoney += $allRejMoney;
        $balanceMoney += $allRejMoney;
        $sql = "update t_receivables
                set rv_money = %f, balance_money = %f
                where ca_id = '%s' and ca_type = 'supplier'
                  and company_id = '%s' ";
        $rc = $db->execute($sql, $rvMoney, $balanceMoney, $supplierId, $companyId);
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      }

      // 应收明细账
      $sql = "insert into t_receivables_detail(id, rv_money, act_money, balance_money, ca_id, ca_type,
                biz_date, date_created, ref_number, ref_type, company_id)
              values ('%s', %f, 0, %f, '%s', 'supplier', '%s', now(), '%s', '采购退货出库', '%s')";
      $rc = $db->execute(
        $sql,
        $this->newId(),
        $allRejMoney,
        $allRejMoney,
        $supplierId,
        $bizDT,
        $ref,
        $companyId
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    } else if ($receivingType == 1) {
      // 现金收款
      $inCash = $allRejMoney;

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

        $balanceCash = $sumInMoney - $sumOutMoney + $inCash;
        $sql = "insert into t_cash(in_money, balance_money, biz_date, company_id)
                values (%f, %f, '%s', '%s')";
        $rc = $db->execute($sql, $inCash, $balanceCash, $bizDT, $companyId);
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }

        // 记现金明细账
        $sql = "insert into t_cash_detail(in_money, balance_money, biz_date, ref_type,
                  ref_number, date_created, company_id)
                values (%f, %f, '%s', '采购退货出库', '%s', now(), '%s')";
        $rc = $db->execute($sql, $inCash, $balanceCash, $bizDT, $ref, $companyId);
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      } else {
        $balanceCash = $data[0]["balance_money"] + $inCash;
        $sumInMoney = $data[0]["in_money"] + $inCash;
        $sql = "update t_cash
                set in_money = %f, balance_money = %f
                where biz_date = '%s' and company_id = '%s' ";
        $db->execute($sql, $sumInMoney, $balanceCash, $bizDT, $companyId);

        // 记现金明细账
        $sql = "insert into t_cash_detail(in_money, balance_money, biz_date, ref_type,
                  ref_number, date_created, company_id)
                values (%f, %f, '%s', '采购退货出库', '%s', now(), '%s')";
        $rc = $db->execute($sql, $inCash, $balanceCash, $bizDT, $ref, $companyId);
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      }

      // 调整业务日期之后的现金总账和明细账的余额
      $sql = "update t_cash
              set balance_money = balance_money + %f
              where biz_date > '%s' and company_id = '%s' ";
      $rc = $db->execute($sql, $inCash, $bizDT, $companyId);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }

      $sql = "update t_cash_detail
              set balance_money = balance_money + %f
              where biz_date > '%s' and company_id = '%s' ";
      $rc = $db->execute($sql, $inCash, $bizDT, $companyId);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 修改单据本身的状态
    $sql = "update t_pr_bill
            set bill_status = 1000
            where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 修改对应的采购入库单的状态位：2000 - 部分退货，3000 - 全部退货
    $sql = "select count(*) as cnt 
            from t_pw_bill_detail 
            where pwbill_id = '%s' and convert(real_goods_count, $fmt) > 0";
    $data = $db->query($sql, $pwBillId);
    $cnt = $data[0]["cnt"];
    $billStatrus = 2000;
    if ($cnt == 0) {
      $billStatrus = 3000;
    } else {
      $billStatrus = 2000;
    }

    $sql = "update t_pw_bill
            set bill_status = %d
            where id = '%s' ";
    $rc = $db->execute($sql, $billStatrus, $pwBillId);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 同步采购订单的状态
    $sql = "select po_id
            from t_po_pw
            where pw_id = '%s' ";
    $data = $db->query($sql, $pwBillId);
    if ($data) {
      // 采购入库单是由采购订单生成的
      $poBillId = $data[0]["po_id"];

      $sql = "select count(*) as cnt
              from t_po_bill_detail
              where pobill_id = '%s' 
                and convert(real_count, $fmt) > 0 ";
      $data = $db->query($sql, $poBillId);
      $cnt = $data[0]["cnt"];
      $billStatus = 1000;
      if ($cnt == 0) {
        // 全部退货的时候，单据状态改为已审核
        $billStatus = 1000;
      } else {
        // 部分退货的时候，单据状态改为部分入库
        $billStatus = 2000;
      }

      $sql = "update t_po_bill set bill_status = %d where id = '%s' ";
      $rc = $db->execute($sql, $billStatus, $poBillId);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    $params["ref"] = $ref;

    return null;
  }

  /**
   * 查询采购退货出库单的数据，用于生成PDF文件
   *
   * @param array $params
   *
   * @return NULL|array
   */
  public function getDataForPDF($params)
  {
    $db = $this->db;

    $ref = $params["ref"];

    $sql = "select p.id, p.bill_status, w.name as warehouse_name, p.bizdt,
              p.rejection_money, u1.name as biz_user_name, u2.name as input_user_name,
              s.name as supplier_name, p.date_created, p.receiving_type, p.company_id,
              p.rejection_money_with_tax as money_with_tax
            from t_pr_bill p, t_warehouse w, t_user u1, t_user u2, t_supplier s
            where (p.warehouse_id = w.id)
              and (p.biz_user_id = u1.id)
              and (p.input_user_id = u2.id)
              and (p.supplier_id = s.id) 
              and (p.ref = '%s')";

    $data = $db->query($sql, $ref);
    if (!$data) {
      return null;
    }

    $v = $data[0];
    $id = $v["id"];
    $companyId = $v["company_id"];

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $result = [
      "billStatus" => $v["bill_status"],
      "supplierName" => $v["supplier_name"],
      "goodsMoney" => $v["rejection_money"],
      "bizDT" => $this->toYMD($v["bizdt"]),
      "warehouseName" => $v["warehouse_name"],
      "bizUserName" => $v["biz_user_name"],
      "moneyWithTax" => $v["money_with_tax"]
    ];

    $sql = "select g.code, g.name, g.spec, u.name as unit_name,
              convert(p.rejection_goods_count, $fmt) as rej_count, p.rejection_goods_price as rej_price,
              p.rejection_money as rej_money, p.tax_rate, p.rejection_money_with_tax as money_with_tax
            from t_pr_bill_detail p, t_goods g, t_goods_unit u
            where p.goods_id = g.id and g.unit_id = u.id and p.prbill_id = '%s'
              and p.rejection_goods_count > 0
            order by p.show_order";
    $items = [];
    $data = $db->query($sql, $id);

    foreach ($data as $v) {
      $items[] = [
        "goodsCode" => $v["code"],
        "goodsName" => $v["name"],
        "goodsSpec" => $v["spec"],
        "goodsCount" => $v["rej_count"],
        "unitName" => $v["unit_name"],
        "goodsPrice" => $v["rej_price"],
        "goodsMoney" => $v["rej_money"],
        "taxRate" => intval($v["tax_rate"]),
        "moneyWithTax" => $v["money_with_tax"]
      ];
    }

    $result["items"] = $items;

    return $result;
  }

  /**
   * 通过单号查询采购退货出库单完整信息，包括明细记录
   *
   * @param string $ref
   *        	采购退货出库单单号
   * @return array|NULL
   */
  public function getFullBillDataByRef($ref)
  {
    $db = $this->db;
    $sql = "select p.id, w.name as warehouse_name,
              u.name as biz_user_name, pw.ref as pwbill_ref,
              s.name as supplier_name,
              p.bizdt, p.company_id
            from t_pr_bill p, t_warehouse w, t_user u, t_pw_bill pw, t_supplier s
            where p.ref = '%s'
              and p.warehouse_id = w.id
              and p.biz_user_id = u.id
              and p.pw_bill_id = pw.id
              and p.supplier_id = s.id ";
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
      "bizUserName" => $data[0]["biz_user_name"],
      "warehouseName" => $data[0]["warehouse_name"],
      "pwbillRef" => $data[0]["pwbill_ref"],
      "supplierName" => $data[0]["supplier_name"],
      "bizDT" => $this->toYMD($data[0]["bizdt"])
    ];

    $items = [];
    $sql = "select p.pwbilldetail_id as id, p.goods_id, g.code as goods_code, g.name as goods_name,
              g.spec as goods_spec, u.name as unit_name, p.goods_count,
              p.goods_price, p.goods_money, convert(p.rejection_goods_count, $fmt) as rej_count,
              p.rejection_goods_price as rej_price, p.rejection_money as rej_money
            from t_pr_bill_detail p, t_goods g, t_goods_unit u
            where p.prbill_id = '%s'
              and p.goods_id = g.id
              and g.unit_id = u.id
            order by p.show_order";
    $data = $db->query($sql, $id);
    foreach ($data as $v) {
      $items[] = [
        "id" => $v["id"],
        "goodsId" => $v["goods_id"],
        "goodsCode" => $v["goods_code"],
        "goodsName" => $v["goods_name"],
        "goodsSpec" => $v["goods_spec"],
        "unitName" => $v["unit_name"],
        "goodsCount" => $v["goods_count"],
        "goodsPrice" => $v["goods_price"],
        "goodsMoney" => $v["goods_money"],
        "rejCount" => $v["rej_count"],
        "rejPrice" => $v["rej_price"],
        "rejMoney" => $v["rej_money"]
      ];
    }

    $result["items"] = $items;

    return $result;
  }

  /**
   * 生成打印采购退货出库单的页面
   *
   * @param array $params
   */
  public function getPRBillDataForLodopPrint($params)
  {
    $db = $this->db;

    $id = $params["id"];

    $sql = "select p.ref, p.bill_status, w.name as warehouse_name, p.bizdt,
              p.rejection_money, u1.name as biz_user_name, u2.name as input_user_name,
              s.name as supplier_name, p.date_created, p.receiving_type, p.company_id,
              p.rejection_money_with_tax as money_with_tax
            from t_pr_bill p, t_warehouse w, t_user u1, t_user u2, t_supplier s
            where (p.warehouse_id = w.id)
              and (p.biz_user_id = u1.id)
              and (p.input_user_id = u2.id)
              and (p.supplier_id = s.id)
              and (p.id = '%s')";

    $data = $db->query($sql, $id);
    if (!$data) {
      return null;
    }

    $v = $data[0];
    $companyId = $v["company_id"];

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $result = [
      "ref" => $v["ref"],
      "billStatus" => $v["bill_status"],
      "supplierName" => $v["supplier_name"],
      "goodsMoney" => $v["rejection_money"],
      "bizDT" => $this->toYMD($v["bizdt"]),
      "warehouseName" => $v["warehouse_name"],
      "bizUserName" => $v["biz_user_name"],
      "printDT" => date("Y-m-d H:i:s"),
      "moneyWithTax" => $v["money_with_tax"]
    ];

    $sql = "select g.code, g.name, g.spec, u.name as unit_name,
              convert(p.rejection_goods_count, $fmt) as rej_count, p.rejection_goods_price as rej_price,
              p.rejection_money as rej_money, p.rejection_money_with_tax as money_with_tax, p.tax_rate
            from t_pr_bill_detail p, t_goods g, t_goods_unit u
            where p.goods_id = g.id and g.unit_id = u.id and p.prbill_id = '%s'
              and p.rejection_goods_count > 0
            order by p.show_order";
    $items = [];
    $data = $db->query($sql, $id);

    foreach ($data as $v) {
      $items[] = [
        "goodsCode" => $v["code"],
        "goodsName" => $v["name"],
        "goodsSpec" => $v["spec"],
        "goodsCount" => $v["rej_count"],
        "unitName" => $v["unit_name"],
        "goodsPrice" => $v["rej_price"],
        "goodsMoney" => $v["rej_money"],
        "moneyWithTax" => $v["money_with_tax"],
        "taxRate" => intval($v["tax_rate"])
      ];
    }

    $result["items"] = $items;

    return $result;
  }
}
