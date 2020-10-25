<?php

namespace Home\DAO;

use Home\Common\FIdConst;

/**
 * 采购入库单 DAO
 *
 * @author 李静波
 */
class PWBillDAO extends PSIBaseExDAO
{

  /**
   * 生成新的采购入库单单号
   *
   * @param string $companyId
   * @return string
   */
  private function genNewBillRef($companyId)
  {
    $db = $this->db;

    $bs = new BizConfigDAO($db);

    // 取单号前缀
    $pre = $bs->getPWBillRefPre($companyId);

    $mid = date("Ymd");

    $sql = "select ref from t_pw_bill where ref like '%s' order by ref desc limit 1";
    $data = $db->query($sql, $pre . $mid . "%");
    $suf = "001";
    if ($data) {
      $ref = $data[0]["ref"];
      $nextNumber = intval(substr($ref, strlen($pre . $mid))) + 1;
      $suf = str_pad($nextNumber, 3, "0", STR_PAD_LEFT);
    }

    return $pre . $mid . $suf;
  }

  /**
   * 单据状态标志转化为文字
   *
   * @param int $code
   * @return string
   */
  private function billStatusCodeToName($code)
  {
    switch ($code) {
      case 0:
        return "待入库";
      case 1000:
        return "已入库";
      case 2000:
        return "部分退货";
      case 3000:
        return "全部退货";
      default:
        return "";
    }
  }

  /**
   * 获得采购入库单主表列表
   *
   * @param array $params
   * @return array
   */
  public function pwbillList($params)
  {
    $db = $this->db;

    $start = $params["start"];
    $limit = $params["limit"];

    // 订单状态
    $billStatus = $params["billStatus"];

    // 单号
    $ref = $params["ref"];

    // 业务日期 -起
    $fromDT = $params["fromDT"];
    // 业务日期-止
    $toDT = $params["toDT"];

    // 仓库id
    $warehouseId = $params["warehouseId"];

    // 供应商id
    $supplierId = $params["supplierId"];

    // 付款方式
    $paymentType = $params["paymentType"];

    $goodsId = $params["goodsId"];

    $loginUserId = $params["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->emptyResult();
    }

    // 是否有权限查看单价和金额
    $canViewPrice = $params["canViewPrice"];

    $queryParams = [];
    $sql = "select p.id, p.bill_status, p.ref, p.biz_dt, u1.name as biz_user_name, u2.name as input_user_name,
              p.goods_money, w.name as warehouse_name, s.name as supplier_name,
              p.date_created, p.payment_type, p.bill_memo, p.expand_by_bom, p.wspbill_id,
              p.tax, p.money_with_tax
            from t_pw_bill p, t_warehouse w, t_supplier s, t_user u1, t_user u2
            where (p.warehouse_id = w.id) and (p.supplier_id = s.id)
            and (p.biz_user_id = u1.id) and (p.input_user_id = u2.id) ";

    $ds = new DataOrgDAO($db);
    // 构建数据域SQL
    $rs = $ds->buildSQL(FIdConst::PURCHASE_WAREHOUSE, "p", $loginUserId);
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
      $sql .= " and (p.biz_dt >= '%s') ";
      $queryParams[] = $fromDT;
    }
    if ($toDT) {
      $sql .= " and (p.biz_dt <= '%s') ";
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
    if ($paymentType != -1) {
      $sql .= " and (p.payment_type = %d) ";
      $queryParams[] = $paymentType;
    }
    if ($goodsId) {
      $sql .= " and (p.id in (select distinct pwbill_id from t_pw_bill_detail where goods_id = '%s'))";
      $queryParams[] = $goodsId;
    }

    $sql .= " order by p.biz_dt desc, p.ref desc
              limit %d, %d";
    $queryParams[] = $start;
    $queryParams[] = $limit;
    $data = $db->query($sql, $queryParams);
    $result = [];

    foreach ($data as $v) {
      $wspBillId = $v["wspbill_id"];
      $wspBillRef = null;
      if ($wspBillId) {
        $sql = "select ref from t_wsp_bill where id = '%s' ";
        $d = $db->query($sql, $wspBillId);
        if ($d) {
          $wspBillRef = $d[0]["ref"];
        }
      }

      $result[] = [
        "id" => $v["id"],
        "ref" => $v["ref"],
        "bizDate" => $this->toYMD($v["biz_dt"]),
        "supplierName" => $v["supplier_name"],
        "warehouseName" => $v["warehouse_name"],
        "inputUserName" => $v["input_user_name"],
        "bizUserName" => $v["biz_user_name"],
        "billStatus" => $this->billStatusCodeToName($v["bill_status"]),
        "amount" => $canViewPrice ? $v["goods_money"] : null,
        "dateCreated" => $v["date_created"],
        "paymentType" => $v["payment_type"],
        "billMemo" => $v["bill_memo"],
        "expandByBOM" => $v["expand_by_bom"],
        "wspBillRef" => $wspBillRef,
        "tax" => $canViewPrice ? $v["tax"] : null,
        "moneyWithTax" => $canViewPrice ? $v["money_with_tax"] : null
      ];
    }

    $sql = "select count(*) as cnt
            from t_pw_bill p, t_warehouse w, t_supplier s, t_user u1, t_user u2
            where (p.warehouse_id = w.id) and (p.supplier_id = s.id)
            and (p.biz_user_id = u1.id) and (p.input_user_id = u2.id)";
    $queryParams = [];
    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::PURCHASE_WAREHOUSE, "p", $loginUserId);
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
      $sql .= " and (p.biz_dt >= '%s') ";
      $queryParams[] = $fromDT;
    }
    if ($toDT) {
      $sql .= " and (p.biz_dt <= '%s') ";
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
    if ($paymentType != -1) {
      $sql .= " and (p.payment_type = %d) ";
      $queryParams[] = $paymentType;
    }
    if ($goodsId) {
      $sql .= " and (p.id in (select distinct pwbill_id from t_pw_bill_detail where goods_id = '%s'))";
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
   * 获得采购入库单商品明细记录列表
   *
   * @param array $params
   * @return array
   */
  public function pwBillDetailList($params)
  {
    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    // 是否有权限查看单价和金额
    $canViewPrice = $params["canViewPrice"];

    $db = $this->db;

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    // 采购入库单id
    $pwbillId = $params["id"];

    $sql = "select p.id, g.code, g.name, g.spec, u.name as unit_name, 
              convert(p.goods_count, $fmt) as goods_count, p.goods_price,
              p.goods_money, p.memo, p.tax_rate, p.tax, p.money_with_tax,
              p.goods_price_with_tax, convert(p.rej_goods_count, $fmt) as rej_goods_count,
              convert(p.real_goods_count, $fmt) as real_goods_count
            from t_pw_bill_detail p, t_goods g, t_goods_unit u
            where p.pwbill_id = '%s' and p.goods_id = g.id and g.unit_id = u.id
            order by p.show_order ";
    $data = $db->query($sql, $pwbillId);
    $result = [];

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
        "unitName" => $v["unit_name"],
        "goodsCount" => $v["goods_count"],
        "rejGoodsCount" => $v["rej_goods_count"],
        "realGoodsCount" => $v["real_goods_count"],
        "goodsMoney" => $canViewPrice ? $v["goods_money"] : null,
        "goodsPrice" => $canViewPrice ? $v["goods_price"] : null,
        "memo" => $v["memo"],
        "taxRate" => $canViewPrice ? $v["tax_rate"] : null,
        "tax" => $canViewPrice ? $v["tax"] : null,
        "moneyWithTax" => $canViewPrice ? $v["money_with_tax"] : null,
        "goodsPriceWithTax" => $canViewPrice ? $goodsPriceWithTax : null
      ];
    }

    return $result;
  }

  /**
   * 新建采购入库单
   *
   * @param array $bill
   * @return NULL|array
   */
  public function addPWBill(&$bill)
  {
    $db = $this->db;

    $viewPrice = $bill["viewPrice"];
    if ($viewPrice == "0") {
      return $this->bad("没有赋权[采购入库-采购单价和金额可见]，不能新建采购入库单");
    }

    // 业务日期
    $bizDT = $bill["bizDT"];

    // 仓库id
    $warehouseId = $bill["warehouseId"];

    // 供应商id
    $supplierId = $bill["supplierId"];

    // 业务员id
    $bizUserId = $bill["bizUserId"];

    // 付款方式
    $paymentType = $bill["paymentType"];

    // 自动拆分
    $expandByBOM = $bill["expandByBOM"];

    // 单据备注
    $billMemo = $bill["billMemo"];

    // 采购订单单号
    $pobillRef = $bill["pobillRef"];

    $warehouseDAO = new WarehouseDAO($db);
    $warehouse = $warehouseDAO->getWarehouseById($warehouseId);
    if (!$warehouse) {
      return $this->bad("入库仓库不存在");
    }

    $supplierDAO = new SupplierDAO($db);
    $supplier = $supplierDAO->getSupplierById($supplierId);
    if (!$supplier) {
      return $this->bad("供应商不存在");
    }

    $userDAO = new UserDAO($db);
    $user = $userDAO->getUserById($bizUserId);
    if (!$user) {
      return $this->bad("业务人员不存在");
    }

    // 检查业务日期
    if (!$this->dateIsValid($bizDT)) {
      return $this->bad("业务日期不正确");
    }

    $loginUserId = $bill["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->badParam("loginUserId");
    }

    $dataOrg = $bill["dataOrg"];
    if ($this->dataOrgNotExists($dataOrg)) {
      return $this->badParam("dataOrg");
    }

    $companyId = $bill["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->badParam("companyId");
    }

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $ref = $this->genNewBillRef($companyId);

    $id = $this->newId();

    // 主表
    $sql = "insert into t_pw_bill (id, ref, supplier_id, warehouse_id, biz_dt,
              biz_user_id, bill_status, date_created, goods_money, input_user_id, payment_type,
              data_org, company_id, bill_memo, expand_by_bom)
            values ('%s', '%s', '%s', '%s', '%s', '%s', 0, now(), 0, '%s', %d, '%s', '%s', '%s', %d)";

    $rc = $db->execute(
      $sql,
      $id,
      $ref,
      $supplierId,
      $warehouseId,
      $bizDT,
      $bizUserId,
      $loginUserId,
      $paymentType,
      $dataOrg,
      $companyId,
      $billMemo,
      $expandByBOM
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $goodsDAO = new GoodsDAO($db);

    // 明细记录
    $items = $bill["items"];
    foreach ($items as $i => $item) {
      // 商品id
      $goodsId = $item["goodsId"];
      if ($goodsId == null) {
        continue;
      }

      // 检查商品是否存在
      $goods = $goodsDAO->getGoodsById($goodsId);
      if (!$goods) {
        return $this->bad("选择的商品不存在");
      }

      // 检查供应商关联商品
      if (!$supplierDAO->goodsIdIsInGoodsRange($supplierId, $goodsId)) {
        $recordInde = $i + 1;
        return $this->bad("第{$recordInde}条记录中的商品不在当前供应商的关联商品内，不能保存");
      }

      // 关于入库数量为什么允许填写0：
      // 当由采购订单生成采购入库单的时候，采购订单中有多种商品，但是是部分到货
      // 那么就存在有些商品的数量是0的情形。
      $goodsCount = $item["goodsCount"];
      if ($goodsCount < 0) {
        return $this->bad("入库数量不能是负数");
      }

      // 入库单明细记录的备注
      $memo = $item["memo"];

      // 采购单价
      $goodsPrice = $item["goodsPrice"];

      // 含税价
      $goodsPriceWithTax = $item["goodsPriceWithTax"];

      // 采购金额
      $goodsMoney = $item["goodsMoney"];

      // 采购订单明细记录id
      $poBillDetailId = $item["poBillDetailId"];

      $taxRate = $item["taxRate"];
      $tax = $item["tax"];
      $moneyWithTax = $item["moneyWithTax"];

      $sql = "insert into t_pw_bill_detail
                (id, date_created, goods_id, goods_count, goods_price,
                goods_money,  pwbill_id, show_order, data_org, memo, company_id,
                pobilldetail_id, tax_rate, tax, money_with_tax, goods_price_with_tax,
                rej_goods_count, real_goods_count)
              values ('%s', now(), '%s', convert(%f, $fmt), %f, %f, '%s', %d, '%s', '%s', '%s', '%s',
                %d, %f, %f, %f, 0, convert(%f, $fmt))";
      $rc = $db->execute(
        $sql,
        $this->newId(),
        $goodsId,
        $goodsCount,
        $goodsPrice,
        $goodsMoney,
        $id,
        $i,
        $dataOrg,
        $memo,
        $companyId,
        $poBillDetailId,
        $taxRate,
        $tax,
        $moneyWithTax,
        $goodsPriceWithTax,
        $goodsCount
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 同步入库单主表中的采购金额合计值
    $sql = "select sum(goods_money) as goods_money,
              sum(tax) as tax, sum(money_with_tax) as money_with_tax
            from t_pw_bill_detail
            where pwbill_id = '%s' ";
    $data = $db->query($sql, $id);
    $totalMoney = $data[0]["goods_money"];
    if (!$totalMoney) {
      $totalMoney = 0;
    }
    $totalTax = $data[0]["tax"];
    $totalMoneyWithTax = $data[0]["money_with_tax"];

    $sql = "update t_pw_bill
            set goods_money = %f, tax = %f, money_with_tax = %f
            where id = '%s' ";
    $rc = $db->execute($sql, $totalMoney, $totalTax, $totalMoneyWithTax, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    if ($pobillRef) {
      // 从采购订单生成采购入库单
      $sql = "select id, company_id from t_po_bill where ref = '%s' ";
      $data = $db->query($sql, $pobillRef);
      if (!$data) {
        // 传入了不存在的采购订单单号
        return $this->sqlError(__METHOD__, __LINE__);
      }
      $pobillId = $data[0]["id"];
      $companyId = $data[0]["company_id"];

      $sql = "update t_pw_bill
              set company_id = '%s'
              where id = '%s' ";
      $rc = $db->execute($sql, $companyId, $id);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }

      // 关联采购订单和采购入库单
      $sql = "insert into t_po_pw(po_id, pw_id) values('%s', '%s')";
      $rc = $db->execute($sql, $pobillId, $id);
      if (!$rc) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    $bill["id"] = $id;
    $bill["ref"] = $ref;

    // 操作成功
    return null;
  }

  /**
   * 当不能查看采购金额和单价的时候，编辑采购入库单就只能修改数量
   */
  private function updatePWBillForNotViewPrice(&$bill, $fmt)
  {
    $db = $this->db;

    // 采购入库单id
    $id = $bill["id"];

    // 业务日期
    $bizDT = $bill["bizDT"];

    // 仓库id
    $warehouseId = $bill["warehouseId"];

    // 供应商id
    $supplierId = $bill["supplierId"];

    // 业务员id
    $bizUserId = $bill["bizUserId"];

    // 付款方式
    $paymentType = $bill["paymentType"];

    // 单据备注
    $billMemo = $bill["billMemo"];

    $goodsDAO = new GoodsDAO($db);

    // 明细记录
    $items = $bill["items"];

    $detailIdArray = [];
    foreach ($items as $i => $item) {
      // 明细记录id
      $detailId = $item["id"];

      // 商品id
      $goodsId = $item["goodsId"];

      if ($goodsId == null) {
        continue;
      }

      $goods = $goodsDAO->getGoodsById($goodsId);
      if (!$goods) {
        return $this->bad("选择的商品不存在");
      }

      // 关于入库数量为什么允许填写0：
      // 当由采购订单生成采购入库单的时候，采购订单中有多种商品，但是是部分到货
      // 那么就存在有些商品的数量是0的情形。
      $goodsCount = $item["goodsCount"];
      if ($goodsCount < 0) {
        return $this->bad("入库数量不能是负数");
      }

      // 入库明细记录的备注
      $memo = $item["memo"];

      // 当不能查看采购金额和单价的时候，编辑采购入库单就只能修改数量
      $sql = "update t_pw_bill_detail
              set goods_count = convert(%f, $fmt), memo = '%s' 
              where id = '%s' ";
      $rc = $db->execute($sql, $goodsCount, $memo, $detailId);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }

      // 修改了数量，同步一下金额
      $sql = "update t_pw_bill_detail set goods_money = goods_count * goods_price
              where id = '%s' ";
      $rc = $db->execute($sql, $detailId);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }

      $detailIdArray[] = $detailId;
    }

    // 编辑的时候，被用户删除掉的记录，在后台表中同样需要删除
    // TODO: 这样拼接SQL有没有安全漏洞？
    $sql = "delete from t_pw_bill_detail
            where (pwbill_id = '%s') and id not in (";
    foreach ($detailIdArray as $i => $detailId) {
      if ($i > 0) {
        $sql .= ",";
      }
      $sql .= " '" . $detailId . "'";
    }
    $sql .= ")";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 同步主表数据
    $sql = "select sum(goods_money) as goods_money from t_pw_bill_detail
            where pwbill_id = '%s' ";
    $data = $db->query($sql, $id);
    $totalMoney = $data[0]["goods_money"];
    if (!$totalMoney) {
      $totalMoney = 0;
    }
    $sql = "update t_pw_bill
            set goods_money = %f, warehouse_id = '%s',
              supplier_id = '%s', biz_dt = '%s',
              biz_user_id = '%s', payment_type = %d,
              bill_memo = '%s'
            where id = '%s' ";
    $rc = $db->execute(
      $sql,
      $totalMoney,
      $warehouseId,
      $supplierId,
      $bizDT,
      $bizUserId,
      $paymentType,
      $billMemo,
      $id
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    return null;
  }

  /**
   * 编辑采购入库单
   *
   * @param array $bill
   * @return NULL|array
   */
  public function updatePWBill(&$bill)
  {
    $db = $this->db;

    // 采购入库单id
    $id = $bill["id"];

    // 业务日期
    $bizDT = $bill["bizDT"];

    // 仓库id
    $warehouseId = $bill["warehouseId"];

    // 供应商id
    $supplierId = $bill["supplierId"];

    // 业务员id
    $bizUserId = $bill["bizUserId"];

    // 付款方式
    $paymentType = $bill["paymentType"];

    // 自动拆分
    $expandByBOM = $bill["expandByBOM"];

    // 采购入库单备注
    $billMemo = $bill["billMemo"];

    $warehouseDAO = new WarehouseDAO($db);
    $warehouse = $warehouseDAO->getWarehouseById($warehouseId);
    if (!$warehouse) {
      return $this->bad("入库仓库不存在");
    }

    $supplierDAO = new SupplierDAO($db);
    $supplier = $supplierDAO->getSupplierById($supplierId);
    if (!$supplier) {
      return $this->bad("供应商不存在");
    }

    $userDAO = new UserDAO($db);
    $user = $userDAO->getUserById($bizUserId);
    if (!$user) {
      return $this->bad("业务人员不存在");
    }

    // 检查业务日期
    if (!$this->dateIsValid($bizDT)) {
      return $this->bad("业务日期不正确");
    }

    $oldBill = $this->getPWBillById($id);
    if (!$oldBill) {
      return $this->bad("要编辑的采购入库单不存在");
    }
    $dataOrg = $oldBill["dataOrg"];
    $billStatus = $oldBill["billStatus"];
    $companyId = $oldBill["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->badParam("companyId");
    }

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $ref = $oldBill["ref"];
    if ($billStatus != 0) {
      return $this->bad("当前采购入库单已经提交入库，不能再编辑");
    }
    $bill["ref"] = $ref;

    // 是否有权限查看采购单价和金额
    $viewPrice = $bill["viewPrice"] == "1";
    if (!$viewPrice) {
      // 没有该权限的时候，调用另外一个单独的function来修改入库单
      return $this->updatePWBillForNotViewPrice($bill, $fmt);
    }

    // 编辑单据的时候，先删除原来的明细记录，再新增明细记录
    $sql = "delete from t_pw_bill_detail where pwbill_id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $goodsDAO = new GoodsDAO($db);

    // 明细记录
    $items = $bill["items"];
    foreach ($items as $i => $item) {
      // 商品id
      $goodsId = $item["goodsId"];

      if ($goodsId == null) {
        continue;
      }

      $goods = $goodsDAO->getGoodsById($goodsId);
      if (!$goods) {
        return $this->bad("选择的商品不存在");
      }

      // 检查供应商关联商品
      if (!$supplierDAO->goodsIdIsInGoodsRange($supplierId, $goodsId)) {
        $recordInde = $i + 1;
        return $this->bad("第{$recordInde}条记录中的商品不在当前供应商的关联商品内，不能保存");
      }

      // 关于入库数量为什么允许填写0：
      // 当由采购订单生成采购入库单的时候，采购订单中有多种商品，但是是部分到货
      // 那么就存在有些商品的数量是0的情形。
      $goodsCount = $item["goodsCount"];
      if ($goodsCount < 0) {
        return $this->bad("入库数量不能是负数");
      }

      // 入库明细记录的备注
      $memo = $item["memo"];

      // 采购单价
      $goodsPrice = $item["goodsPrice"];

      // 含税价
      $goodsPriceWithTax = $item["goodsPriceWithTax"];

      // 采购金额
      $goodsMoney = $item["goodsMoney"];

      // 采购订单明细记录id
      $poBillDetailId = $item["poBillDetailId"];

      $taxRate = $item["taxRate"];
      $tax = $item["tax"];
      $moneyWithTax = $item["moneyWithTax"];

      $sql = "insert into t_pw_bill_detail (id, date_created, goods_id, goods_count, goods_price,
                goods_money,  pwbill_id, show_order, data_org, memo, company_id, pobilldetail_id,
                tax_rate, tax, money_with_tax, goods_price_with_tax,
                rej_goods_count, real_goods_count)
              values ('%s', now(), '%s', convert(%f, $fmt), %f, %f, '%s', %d, '%s', '%s', '%s', '%s',
                %d, %f, %f, %f, 0, convert(%f, $fmt))";
      $rc = $db->execute(
        $sql,
        $this->newId(),
        $goodsId,
        $goodsCount,
        $goodsPrice,
        $goodsMoney,
        $id,
        $i,
        $dataOrg,
        $memo,
        $companyId,
        $poBillDetailId,
        $taxRate,
        $tax,
        $moneyWithTax,
        $goodsPriceWithTax,
        $goodsCount
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 同步主表数据
    $sql = "select sum(goods_money) as goods_money,
              sum(tax) as tax, sum(money_with_tax) as money_with_tax
            from t_pw_bill_detail
            where pwbill_id = '%s' ";
    $data = $db->query($sql, $id);
    $totalMoney = $data[0]["goods_money"];
    if (!$totalMoney) {
      $totalMoney = 0;
    }
    $totalTax = $data[0]["tax"];
    $totalMoneyWithTax = $data[0]["money_with_tax"];

    $sql = "update t_pw_bill
            set goods_money = %f, warehouse_id = '%s',
              supplier_id = '%s', biz_dt = '%s',
              biz_user_id = '%s', payment_type = %d,
              bill_memo = '%s', expand_by_bom = %d,
              tax = %f, money_with_tax = %f
            where id = '%s' ";
    $rc = $db->execute(
      $sql,
      $totalMoney,
      $warehouseId,
      $supplierId,
      $bizDT,
      $bizUserId,
      $paymentType,
      $billMemo,
      $expandByBOM,
      $totalTax,
      $totalMoneyWithTax,
      $id
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    return null;
  }

  /**
   * 通过id查询采购入库单
   *
   * @param string $id
   *        	采购入库单id
   * @return NULL|array
   */
  public function getPWBillById($id)
  {
    $db = $this->db;

    $sql = "select ref, bill_status, data_org, company_id, warehouse_id 
            from t_pw_bill where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return null;
    } else {
      return [
        "ref" => $data[0]["ref"],
        "billStatus" => $data[0]["bill_status"],
        "dataOrg" => $data[0]["data_org"],
        "companyId" => $data[0]["company_id"],
        "warehouseId" => $data[0]["warehouse_id"]
      ];
    }
  }

  /**
   * 同步在途库存
   *
   * @param array $bill
   * @return NULL|array
   */
  public function updateAfloatInventoryByPWBill(&$bill)
  {
    $db = $this->db;

    // 采购入库单id
    $id = $bill["id"];

    // 仓库id
    $warehouseId = $bill["warehouseId"];

    // 公司id
    $companyId = $bill["companyId"];

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $sql = "select goods_id
            from t_pw_bill_detail
            where pwbill_id = '%s'
            order by show_order";
    $data = $db->query($sql, $id);
    foreach ($data as $v) {
      $goodsId = $v["goods_id"];

      $rc = $this->updateAfloatInventory($db, $warehouseId, $goodsId, $fmt);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    return null;
  }

  private function updateAfloatInventory($db, $warehouseId, $goodsId, $fmt)
  {
    $sql = "select sum(convert(pd.goods_count, $fmt)) as goods_count, 
              sum(convert(pd.goods_money, $fmt)) as goods_money
            from t_pw_bill p, t_pw_bill_detail pd
            where p.id = pd.pwbill_id
              and p.warehouse_id = '%s'
              and pd.goods_id = '%s'
              and p.bill_status = 0 ";

    $data = $db->query($sql, $warehouseId, $goodsId);
    $count = 0;
    $price = 0;
    $money = 0;
    if ($data) {
      $count = $data[0]["goods_count"];
      if (!$count) {
        $count = 0;
      }
      $money = $data[0]["goods_money"];
      if (!$money) {
        $money = 0;
      }

      if ($count !== 0) {
        $price = $money / $count;
      }
    }

    $sql = "select id from t_inventory where warehouse_id = '%s' and goods_id = '%s' ";
    $data = $db->query($sql, $warehouseId, $goodsId);
    if (!$data) {
      // 首次有库存记录
      $sql = "insert into t_inventory (warehouse_id, goods_id, afloat_count, afloat_price,
                afloat_money, balance_count, balance_price, balance_money)
              values ('%s', '%s', convert(%f, $fmt), %f, %f, 0, 0, 0)";
      return $db->execute($sql, $warehouseId, $goodsId, $count, $price, $money);
    } else {
      $sql = "update t_inventory
              set afloat_count = convert(%f, $fmt), afloat_price = %f, afloat_money = %f
              where warehouse_id = '%s' and goods_id = '%s' ";
      return $db->execute($sql, $count, $price, $money, $warehouseId, $goodsId);
    }

    return true;
  }

  /**
   * 获得某个采购入库单的信息
   *
   * @param array $params
   *
   * @return array
   */
  public function pwBillInfo($params)
  {
    $db = $this->db;

    // 是否能查看采购单价和金额
    $canViewPrice = $params["canViewPrice"];

    // 公司id
    $companyId = $params["companyId"];

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    // id: 采购入库单id
    $id = $params["id"];
    // pobillRef: 采购订单单号，可以为空，为空表示直接录入采购入库单；不为空表示是从采购订单生成入库单
    $pobillRef = $params["pobillRef"];

    $result = [
      "id" => $id
    ];

    $sql = "select p.ref, p.bill_status, p.supplier_id, s.name as supplier_name,
              p.warehouse_id, w.name as  warehouse_name,
              p.biz_user_id, u.name as biz_user_name, p.biz_dt, p.payment_type,
              p.bill_memo, p.expand_by_bom
            from t_pw_bill p, t_supplier s, t_warehouse w, t_user u
            where p.id = '%s' and p.supplier_id = s.id and p.warehouse_id = w.id
              and p.biz_user_id = u.id";
    $data = $db->query($sql, $id);
    if ($data) {
      $v = $data[0];
      $result["ref"] = $v["ref"];
      $result["billStatus"] = $v["bill_status"];
      $result["supplierId"] = $v["supplier_id"];
      $result["supplierName"] = $v["supplier_name"];
      $result["warehouseId"] = $v["warehouse_id"];
      $result["warehouseName"] = $v["warehouse_name"];
      $result["bizUserId"] = $v["biz_user_id"];
      $result["bizUserName"] = $v["biz_user_name"];
      $result["bizDT"] = $this->toYMD($v["biz_dt"]);
      $result["paymentType"] = $v["payment_type"];
      $result["billMemo"] = $v["bill_memo"];
      $result["expandByBOM"] = $v["expand_by_bom"];

      // 采购的商品明细
      $items = [];
      $sql = "select p.id, p.goods_id, g.code, g.name, g.spec, u.name as unit_name,
                convert(p.goods_count, $fmt) as goods_count, p.goods_price, p.goods_money, p.memo,
                p.pobilldetail_id, p.tax_rate, p.tax, p.money_with_tax, p.goods_price_with_tax
              from t_pw_bill_detail p, t_goods g, t_goods_unit u
              where p.goods_Id = g.id and g.unit_id = u.id and p.pwbill_id = '%s'
              order by p.show_order";
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
          "unitName" => $v["unit_name"],
          "goodsCount" => $v["goods_count"],
          "goodsPrice" => $canViewPrice ? $v["goods_price"] : null,
          "goodsMoney" => $canViewPrice ? $v["goods_money"] : null,
          "memo" => $v["memo"],
          "poBillDetailId" => $v["pobilldetail_id"],
          "taxRate" => intval($v["tax_rate"]),
          "tax" => $canViewPrice ? $v["tax"] : null,
          "moneyWithTax" => $canViewPrice ? $v["money_with_tax"] : null,
          "goodsPriceWithTax" => $canViewPrice ? $goodsPriceWithTax : null
        ];
      }

      $result["items"] = $items;

      // 查询该单据是否是由采购订单生成的
      $sql = "select po_id from t_po_pw where pw_id = '%s' ";
      $data = $db->query($sql, $id);
      if ($data) {
        $result["genBill"] = true;
      } else {
        $result["genBill"] = false;
      }
    } else {
      // 新建采购入库单
      $result["bizUserId"] = $params["loginUserId"];
      $result["bizUserName"] = $params["loginUserName"];

      $tc = new BizConfigDAO($db);
      $companyId = $params["companyId"];

      $warehouse = $tc->getPWBillDefaultWarehouse($companyId);
      if ($warehouse) {
        $result["warehouseId"] = $warehouse["id"];
        $result["warehouseName"] = $warehouse["name"];
      }

      if ($pobillRef) {
        // 由采购订单生成采购入库单
        $sql = "select p.id, p.supplier_id, s.name as supplier_name, p.deal_date,
                  p.payment_type, p.bill_memo
                from t_po_bill p, t_supplier s
                where p.ref = '%s' and p.supplier_id = s.id ";
        $data = $db->query($sql, $pobillRef);
        if ($data) {
          $v = $data[0];
          $result["supplierId"] = $v["supplier_id"];
          $result["supplierName"] = $v["supplier_name"];
          $result["dealDate"] = $this->toYMD($v["deal_date"]);
          $result["paymentType"] = $v["payment_type"];
          $result["billMemo"] = $v["bill_memo"];

          $pobillId = $v["id"];
          // 采购的明细
          $items = [];
          $sql = "select p.id, p.goods_id, g.code, g.name, g.spec, u.name as unit_name,
                    convert(p.goods_count, $fmt) as goods_count, 
                    p.goods_price, p.goods_money, 
                    convert(p.left_count, $fmt) as left_count, p.memo,
                  p.tax_rate, p.tax, p.money_with_tax, p.goods_price_with_tax
                  from t_po_bill_detail p, t_goods g, t_goods_unit u
                  where p.pobill_id = '%s' and p.goods_id = g.id and g.unit_id = u.id
                  order by p.show_order ";
          $data = $db->query($sql, $pobillId);
          foreach ($data as $v) {
            $items[] = [
              "id" => $v["id"],
              "poBillDetailId" => $v["id"],
              "goodsId" => $v["goods_id"],
              "goodsCode" => $v["code"],
              "goodsName" => $v["name"],
              "goodsSpec" => $v["spec"],
              "unitName" => $v["unit_name"],
              "goodsCount" => $v["left_count"],
              "goodsPrice" => $v["goods_price"],
              "goodsMoney" => $v["left_count"] * $v["goods_price"],
              "memo" => $v["memo"],
              "taxRate" => $v["tax_rate"],
              "tax" => $v["tax"],
              "moneyWithTax" => $v["money_with_tax"],
              "goodsPriceWithTax" => $v["goods_price_with_tax"]
            ];
          }

          $result["items"] = $items;
        }
      } else {
        // 不是由采购订单生成采购入库单，是普通的新建采购入库单

        // 采购入库单默认付款方式
        $result["paymentType"] = $tc->getPWBillDefaultPayment($companyId);
      }
    }

    return $result;
  }

  /**
   * 删除采购入库单
   *
   * @param array $params
   * @return NULL|array
   */
  public function deletePWBill(&$params)
  {
    $db = $this->db;

    // 采购入库单id
    $id = $params["id"];

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->badParam("companyId");
    }

    $bill = $this->getPWBillById($id);
    if (!$bill) {
      return $this->bad("要删除的采购入库单不存在");
    }

    // 单号
    $ref = $bill["ref"];

    // 单据状态
    $billStatus = $bill["billStatus"];
    if ($billStatus != 0) {
      return $this->bad("当前采购入库单已经提交入库，不能删除");
    }

    // 仓库id
    $warehouseId = $bill["warehouseId"];

    $sql = "select goods_id
            from t_pw_bill_detail
            where pwbill_id = '%s'
            order by show_order";
    $data = $db->query($sql, $id);
    $goodsIdList = array();
    foreach ($data as $v) {
      $goodsIdList[] = $v["goods_id"];
    }

    // 先删除明细记录
    $sql = "delete from t_pw_bill_detail where pwbill_id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 再删除主表
    $sql = "delete from t_pw_bill where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 删除从采购订单生成的记录
    $sql = "delete from t_po_pw where pw_id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    // 同步库存账中的在途库存
    foreach ($goodsIdList as $v) {
      $goodsId = $v;

      $rc = $this->updateAfloatInventory($db, $warehouseId, $goodsId, $fmt);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    $params["ref"] = $ref;

    // 操作成功
    return null;
  }

  /**
   * 提交采购入库单
   *
   * @param array $params
   * @return NULL|array
   */
  public function commitPWBill(&$params)
  {
    $db = $this->db;

    // id: 采购入库单id
    $id = $params["id"];

    $loginUserId = $params["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->badParam("loginUserId");
    }
    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->badParam("companyId");
    }

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $sql = "select ref, warehouse_id, bill_status, biz_dt, biz_user_id,  goods_money, supplier_id,
              payment_type, company_id, money_with_tax
            from t_pw_bill
            where id = '%s' ";
    $data = $db->query($sql, $id);

    if (!$data) {
      return $this->bad("要提交的采购入库单不存在");
    }
    $billStatus = $data[0]["bill_status"];
    if ($billStatus != 0) {
      return $this->bad("采购入库单已经提交入库，不能再次提交");
    }

    $ref = $data[0]["ref"];
    $bizDT = $data[0]["biz_dt"];
    $bizUserId = $data[0]["biz_user_id"];
    // money_with_tax字段是之后增加的，下面的写法是考虑到该字段为null的情况，事实上似乎不用这么小心谨慎
    // money_with_tax为null的时候，取goods_money
    $billPayables = $data[0]["money_with_tax"] ?? floatval($data[0]["goods_money"]);
    $supplierId = $data[0]["supplier_id"];
    $warehouseId = $data[0]["warehouse_id"];
    $paymentType = $data[0]["payment_type"];
    $companyId = $data[0]["company_id"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->badParam("companyId");
    }

    $bc = new BizConfigDAO($db);
    // true: 先进先出法
    $fifo = $bc->getInventoryMethod($companyId) == 1;

    $warehouseDAO = new WarehouseDAO($db);
    $warehouse = $warehouseDAO->getWarehouseById($warehouseId);
    if (!$warehouse) {
      return $this->bad("要入库的仓库不存在");
    }
    $inited = $warehouse["inited"];
    if ($inited == 0) {
      return $this->bad("仓库 [{$warehouse['name']}] 还没有完成建账，不能做采购入库的操作");
    }

    // 检查供应商是否存在
    $supplierDAO = new SupplierDAO($db);
    $supplier = $supplierDAO->getSupplierById($supplierId);
    if (!$supplier) {
      return $this->bad("供应商不存在");
    }

    // 检查业务员是否存在
    $userDAO = new UserDAO($db);
    $user = $userDAO->getUserById($bizUserId);
    if (!$user) {
      return $this->bad("业务员不存在");
    }

    $sql = "select goods_id, convert(goods_count, $fmt) as goods_count, goods_price, goods_money, id,
              pobilldetail_id
            from t_pw_bill_detail
            where pwbill_id = '%s' order by show_order";
    $items = $db->query($sql, $id);
    if (!$items) {
      return $this->bad("采购入库单没有采购明细记录，不能入库");
    }

    $bizConfigDAO = new BizConfigDAO($db);
    // $countLimit: true - 入库数量不能超过采购订单上未入库数量
    $countLimit = $bizConfigDAO->getPWCountLimit($companyId) == "1";
    $poId = null;
    $sql = "select po_id
            from t_po_pw
            where pw_id = '%s' ";
    $data = $db->query($sql, $id);
    if ($data) {
      $poId = $data[0]["po_id"];
    }

    // 检查入库数量、单价、金额不能为负数
    foreach ($items as $i => $v) {
      $goodsCount = $v["goods_count"];
      if ($goodsCount < 0) {
        return $this->bad("采购数量不能小于0");
      }
      $goodsPrice = floatval($v["goods_price"]);
      if ($goodsPrice < 0) {
        return $this->bad("采购单价不能为负数");
      }
      $goodsMoney = floatval($v["goods_money"]);
      if ($goodsMoney < 0) {
        return $this->bad("采购金额不能为负数");
      }

      if (!$countLimit) {
        continue;
      }

      if (!$poId) {
        // 没有采购订单
        continue;
      }

      // 检查采购入库数量是否超过采购订单上未入库数量
      $pobillDetailId = $v["pobilldetail_id"];
      $sql = "select convert(left_count, $fmt) as left_count
              from t_po_bill_detail
              where id = '%s' ";
      $data = $db->query($sql, $pobillDetailId);
      if (!$data) {
        continue;
      }

      $leftCount = $data[0]["left_count"];
      if ($goodsCount > $leftCount) {
        $index = $i + 1;
        $info = "第{$index}条入库记录中采购入库数量超过采购订单上未入库数量<br/><br/>";
        $info .= "入库数量是: {$goodsCount}<br/>采购订单中未入库数量是: {$leftCount}";
        return $this->bad($info);
      }
    }

    $allPaymentType = array(
      0,
      1,
      2
    );
    if (!in_array($paymentType, $allPaymentType)) {
      return $this->bad("付款方式填写不正确，无法提交");
    }

    foreach ($items as $v) {
      $pwbilldetailId = $v["id"];

      $pobillDetailId = $v["pobilldetail_id"];

      $goodsCount = $v["goods_count"];
      if ($goodsCount <= 0) {
        // 忽略非正入库数量
        continue;
      }
      $goodsPrice = floatval($v["goods_price"]);
      $goodsMoney = floatval($v["goods_money"]);
      if ($goodsCount != 0) {
        $goodsPrice = $goodsMoney / $goodsCount;
      }

      $goodsId = $v["goods_id"];

      $balanceCount = 0;
      $balanceMoney = 0;
      $balancePrice = (float) 0;
      // 库存总账
      $sql = "select convert(in_count, $fmt) as in_count, in_money, balance_count, balance_money
              from t_inventory
              where warehouse_id = '%s' and goods_id = '%s' ";
      $data = $db->query($sql, $warehouseId, $goodsId);
      if ($data) {
        $inCount = $data[0]["in_count"];
        $inMoney = floatval($data[0]["in_money"]);
        $balanceCount = $data[0]["balance_count"];
        $balanceMoney = floatval($data[0]["balance_money"]);

        $inCount += $goodsCount;
        $inMoney += $goodsMoney;
        $inPrice = $inMoney / $inCount;

        $balanceCount += $goodsCount;
        $balanceMoney += $goodsMoney;
        $balancePrice = $balanceMoney / $balanceCount;

        $sql = "update t_inventory
                set in_count = convert(%f, $fmt), in_price = %f, in_money = %f,
                  balance_count = convert(%f, $fmt), balance_price = %f, balance_money = %f
                where warehouse_id = '%s' and goods_id = '%s' ";
        $rc = $db->execute(
          $sql,
          $inCount,
          $inPrice,
          $inMoney,
          $balanceCount,
          $balancePrice,
          $balanceMoney,
          $warehouseId,
          $goodsId
        );
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      } else {
        $inCount = $goodsCount;
        $inMoney = $goodsMoney;
        $inPrice = $inMoney / $inCount;
        $balanceCount += $goodsCount;
        $balanceMoney += $goodsMoney;
        $balancePrice = $balanceMoney / $balanceCount;

        $sql = "insert into t_inventory (in_count, in_price, in_money, balance_count,
                  balance_price, balance_money, warehouse_id, goods_id)
                values (convert(%f, $fmt), %f, %f, convert(%f, $fmt), %f, %f, '%s', '%s')";
        $rc = $db->execute(
          $sql,
          $inCount,
          $inPrice,
          $inMoney,
          $balanceCount,
          $balancePrice,
          $balanceMoney,
          $warehouseId,
          $goodsId
        );
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      }

      // 库存明细账
      $sql = "insert into t_inventory_detail (in_count, in_price, in_money, balance_count,
                balance_price, balance_money, warehouse_id, goods_id, biz_date,
                biz_user_id, date_created, ref_number, ref_type)
              values (convert(%f, $fmt), %f, %f, convert(%f, $fmt), %f, %f, '%s', '%s', '%s', '%s', 
                now(), '%s', '采购入库')";
      $rc = $db->execute(
        $sql,
        $goodsCount,
        $goodsPrice,
        $goodsMoney,
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

      // 先进先出
      if ($fifo) {
        $dt = date("Y-m-d H:i:s");
        $sql = "insert into t_inventory_fifo (in_count, in_price, in_money, balance_count,
                  balance_price, balance_money, warehouse_id, goods_id, date_created, in_ref,
                  in_ref_type, pwbilldetail_id)
                values (convert(%f, $fmt), %f, %f, convert(%f, $fmt), %f, %f, '%s', '%s', '%s', '%s', '采购入库', '%s')";
        $rc = $db->execute(
          $sql,
          $goodsCount,
          $goodsPrice,
          $goodsMoney,
          $goodsCount,
          $goodsPrice,
          $goodsMoney,
          $warehouseId,
          $goodsId,
          $dt,
          $ref,
          $pwbilldetailId
        );
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }

        // fifo 明细记录
        $sql = "insert into t_inventory_fifo_detail(in_count, in_price, in_money, balance_count,
                  balance_price, balance_money, warehouse_id, goods_id, date_created, pwbilldetail_id)
                values (convert(%f, $fmt), %f, %f, convert(%f, $fmt), %f, %f, '%s', '%s', '%s', '%s')";
        $rc = $db->execute(
          $sql,
          $goodsCount,
          $goodsPrice,
          $goodsMoney,
          $goodsCount,
          $goodsPrice,
          $goodsMoney,
          $warehouseId,
          $goodsId,
          $dt,
          $pwbilldetailId
        );
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      }

      // 同步采购订单中的到货情况
      $sql = "select convert(goods_count, $fmt) as goods_count, 
                convert(pw_count, $fmt) as pw_count,
                convert(rej_count, $fmt) as rej_count
              from t_po_bill_detail
              where id = '%s' ";
      $poDetail = $db->query($sql, $pobillDetailId);
      if (!$poDetail) {
        // 当前采购入库单不是由采购订单创建的
        continue;
      }

      $totalGoodsCount = $poDetail[0]["goods_count"];
      $totalPWCount = $poDetail[0]["pw_count"];
      $totalRejCount = $poDetail[0]["rej_count"];
      $totalPWCount += $goodsCount;
      $totalLeftCount = $totalGoodsCount - $totalPWCount + $totalRejCount;
      $totalRealCount = $totalPWCount - $totalRejCount;

      $sql = "update t_po_bill_detail
              set pw_count = convert(%f, $fmt), left_count = convert(%f, $fmt),
                real_count = convert(%f, $fmt)
              where id = '%s' ";
      $rc = $db->execute($sql, $totalPWCount, $totalLeftCount, $totalRealCount, $pobillDetailId);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 修改本单据状态为已入库
    $sql = "update t_pw_bill set bill_status = 1000 where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 同步采购订单的状态
    $sql = "select po_id
            from t_po_pw
            where pw_id = '%s' ";
    $data = $db->query($sql, $id);
    if ($data) {
      $poBillId = $data[0]["po_id"];

      $sql = "select count(*) as cnt from t_po_bill_detail
              where pobill_id = '%s' and convert(left_count, $fmt) > 0 ";
      $data = $db->query($sql, $poBillId);
      $cnt = $data[0]["cnt"];
      $billStatus = 1000;
      if ($cnt > 0) {
        // 部分入库
        $billStatus = 2000;
      } else {
        // 全部入库
        $billStatus = 3000;
      }
      $sql = "update t_po_bill
              set bill_status = %d
              where id = '%s' ";
      $rc = $db->execute($sql, $billStatus, $poBillId);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    if ($paymentType == 0) {
      // 记应付账款
      // 应付明细账
      $sql = "insert into t_payables_detail (id, pay_money, act_money, balance_money,
                ca_id, ca_type, date_created, ref_number, ref_type, biz_date, company_id)
              values ('%s', %f, 0, %f, '%s', 'supplier', now(), '%s', '采购入库', '%s', '%s')";
      $rc = $db->execute(
        $sql,
        $this->newId(),
        $billPayables,
        $billPayables,
        $supplierId,
        $ref,
        $bizDT,
        $companyId
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }

      // 应付总账
      $sql = "select id, pay_money, act_money
              from t_payables
              where ca_id = '%s' and ca_type = 'supplier' and company_id = '%s' ";
      $data = $db->query($sql, $supplierId, $companyId);
      if ($data) {
        $pId = $data[0]["id"];
        $payMoney = floatval($data[0]["pay_money"]);
        $payMoney += $billPayables;

        $actMoney = floatval($data[0]["act_money"]);
        $balanMoney = $payMoney - $actMoney;

        $sql = "update t_payables
                set pay_money = %f, balance_money = %f
                where id = '%s' ";
        $rc = $db->execute($sql, $payMoney, $balanMoney, $pId);
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      } else {
        $payMoney = $billPayables;

        $sql = "insert into t_payables (id, pay_money, act_money, balance_money,
                  ca_id, ca_type, company_id)
                values ('%s', %f, 0, %f, '%s', 'supplier', '%s')";
        $rc = $db->execute(
          $sql,
          $this->newId(),
          $payMoney,
          $payMoney,
          $supplierId,
          $companyId
        );
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      }
    } else if ($paymentType == 1) {
      // 现金付款

      $outCash = $billPayables;

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
                values (%f, %f, '%s', '采购入库', '%s', now(), '%s')";
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
                values (%f, %f, '%s', '采购入库', '%s', now(), '%s')";
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
      // 2: 预付款

      $outMoney = $billPayables;

      $sql = "select out_money, balance_money from t_pre_payment
              where supplier_id = '%s' and company_id = '%s' ";
      $data = $db->query($sql, $supplierId, $companyId);
      $totalOutMoney = $data[0]["out_money"];
      $totalBalanceMoney = $data[0]["balance_money"];
      if (!$totalOutMoney) {
        $totalOutMoney = 0;
      }
      if (!$totalBalanceMoney) {
        $totalBalanceMoney = 0;
      }
      if ($outMoney > $totalBalanceMoney) {
        $supplierName = $supplier["name"];
        $info = "供应商[{$supplierName}]预付款余额不足，无法完成支付<br/><br/>余额:{$totalBalanceMoney}元，付款金额:{$outMoney}元";
        return $this->bad($info);
      }

      // 预付款总账
      $sql = "update t_pre_payment
              set out_money = %f, balance_money = %f
              where supplier_id = '%s' and company_id = '%s' ";
      $totalOutMoney += $outMoney;
      $totalBalanceMoney -= $outMoney;
      $rc = $db->execute($sql, $totalOutMoney, $totalBalanceMoney, $supplierId, $companyId);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }

      // 预付款明细账
      $sql = "insert into t_pre_payment_detail(id, supplier_id, out_money, balance_money,
                biz_date, date_created, ref_number, ref_type, biz_user_id, input_user_id,
                company_id)
              values ('%s', '%s', %f, %f, '%s', now(), '%s', '采购入库', '%s', '%s', '%s')";
      $rc = $db->execute(
        $sql,
        $this->newId(),
        $supplierId,
        $outMoney,
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

    // 同步库存账中的在途库存
    $sql = "select goods_id
            from t_pw_bill_detail
            where pwbill_id = '%s'
            order by show_order";
    $data = $db->query($sql, $id);
    foreach ($data as $v) {
      $goodsId = $v["goods_id"];

      $rc = $this->updateAfloatInventory($db, $warehouseId, $goodsId, $fmt);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 处理自动拆分
    $wspBillDAO = new WSPBillDAO($db);
    $wspBillRef = null;
    $rc = $wspBillDAO->genWSPBillFromPWBillAndCommit($id, $loginUserId, $wspBillRef);
    if ($rc) {
      return $rc;
    }

    // 操作成功
    $params["ref"] = $ref;
    $params["wspBillRef"] = $wspBillRef;
    return null;
  }

  /**
   * 查询采购入库单的数据，用于生成PDF文件
   *
   * @param array $params
   *
   * @return NULL|array
   */
  public function getDataForPDF($params)
  {
    $db = $this->db;

    $ref = $params["ref"];
    $canViewPrice = $params["canViewPrice"];

    $sql = "select p.id, p.bill_status, p.ref, p.biz_dt, u1.name as biz_user_name, u2.name as input_user_name,
              p.goods_money, w.name as warehouse_name, s.name as supplier_name,
              p.date_created, p.payment_type, p.company_id, p.money_with_tax
            from t_pw_bill p, t_warehouse w, t_supplier s, t_user u1, t_user u2
            where (p.warehouse_id = w.id) and (p.supplier_id = s.id)
            and (p.biz_user_id = u1.id) and (p.input_user_id = u2.id) 
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

    $result = [];

    $result["billStatus"] = $v["bill_status"];
    $result["supplierName"] = $v["supplier_name"];
    $result["goodsMoney"] = $canViewPrice ? $v["goods_money"] : "***";
    $result["moneyWithTax"] = $canViewPrice ? $v["money_with_tax"] : "***";
    $result["bizDT"] = $this->toYMD($v["biz_dt"]);
    $result["warehouseName"] = $v["warehouse_name"];
    $result["bizUserName"] = $v["biz_user_name"];

    $sql = "select g.code, g.name, g.spec, u.name as unit_name, 
              convert(p.goods_count, $fmt) as goods_count, p.goods_price,
              p.goods_money, p.tax_rate, p.money_with_tax
            from t_pw_bill_detail p, t_goods g, t_goods_unit u
            where p.pwbill_id = '%s' and p.goods_id = g.id and g.unit_id = u.id
            order by p.show_order ";
    $items = [];
    $data = $db->query($sql, $id);

    foreach ($data as $v) {
      $items[] = [
        "goodsCode" => $v["code"],
        "goodsName" => $v["name"],
        "goodsSpec" => $v["spec"],
        "goodsCount" => $v["goods_count"],
        "unitName" => $v["unit_name"],
        "goodsPrice" => $canViewPrice ? $v["goods_price"] : "***",
        "goodsMoney" => $canViewPrice ? $v["goods_money"] : "***",
        "taxRate" => $canViewPrice ? intval($v["tax_rate"]) : "***",
        "moneyWithTax" => $canViewPrice ? $v["money_with_tax"] : "***",
      ];
    }

    $result["items"] = $items;

    return $result;
  }

  /**
   * 通过单号查询采购入库的完整信息，包括明细入库记录
   *
   * @param string $ref
   *        	采购入库单单号
   * @return array|NULL
   */
  public function getFullBillDataByRef($ref)
  {
    $db = $this->db;

    $sql = "select p.id, s.name as supplier_name,
              w.name as  warehouse_name,
              u.name as biz_user_name, p.biz_dt, p.company_id
            from t_pw_bill p, t_supplier s, t_warehouse w, t_user u
            where p.ref = '%s' and p.supplier_id = s.id and p.warehouse_id = w.id
              and p.biz_user_id = u.id";
    $data = $db->query($sql, $ref);
    if (!$data) {
      return NULL;
    }

    $v = $data[0];
    $id = $v["id"];
    $companyId = $v["company_id"];

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $result = [
      "supplierName" => $v["supplier_name"],
      "warehouseName" => $v["warehouse_name"],
      "bizUserName" => $v["biz_user_name"],
      "bizDT" => $this->toYMD($v["biz_dt"])
    ];

    // 明细记录
    $items = [];
    $sql = "select p.id, p.goods_id, g.code, g.name, g.spec, u.name as unit_name,
              convert(p.goods_count, $fmt) as goods_count, p.goods_price, p.goods_money, p.memo,
              p.tax_rate, p.tax, p.money_with_tax
            from t_pw_bill_detail p, t_goods g, t_goods_unit u
            where p.goods_Id = g.id and g.unit_id = u.id and p.pwbill_id = '%s'
            order by p.show_order";
    $data = $db->query($sql, $id);
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
        "memo" => $v["memo"],
        "taxRate" => intval($v["tax_rate"]),
        "tax" => $v["tax"],
        "moneyWithTax" => $v["money_with_tax"]
      ];
    }

    $result["items"] = $items;

    return $result;
  }

  /**
   * 获得采购入库单商品明细记录列表
   * 采购退货模块 - 选择采购入库单
   *
   * @param array $params
   * @return array
   */
  public function pwBillDetailListForPRBill($params)
  {
    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    $db = $this->db;

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $pwbillId = $params["id"];
    $canViewPrice = $params["canViewPrice"];

    $sql = "select p.id, g.code, g.name, g.spec, u.name as unit_name,
              convert(p.goods_count, $fmt) as goods_count, p.goods_price,
              p.goods_money, p.memo, p.tax, p.tax_rate, p.money_with_tax,
              p.goods_price_with_tax, convert(p.rej_goods_count, $fmt) as rej_goods_count,
              convert(p.real_goods_count, $fmt) as real_goods_count
            from t_pw_bill_detail p, t_goods g, t_goods_unit u
            where p.pwbill_id = '%s' and p.goods_id = g.id and g.unit_id = u.id
            order by p.show_order ";
    $data = $db->query($sql, $pwbillId);
    $result = [];

    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "goodsCode" => $v["code"],
        "goodsName" => $v["name"],
        "goodsSpec" => $v["spec"],
        "unitName" => $v["unit_name"],
        "goodsCount" => $v["goods_count"],
        "rejGoodsCount" => $v["rej_goods_count"],
        "realGoodsCount" => $v["real_goods_count"],
        "goodsMoney" => $canViewPrice ? $v["goods_money"] : null,
        "goodsPrice" => $canViewPrice ? $v["goods_price"] : null,
        "memo" => $v["memo"],
        "tax" => $v["tax"],
        "taxRate" => $v["tax_rate"],
        "moneyWithTax" => $v["money_with_tax"],
        "priceWithTax" => $v["goods_price_with_tax"]
      ];
    }

    return $result;
  }

  /**
   * 生成打印采购入库单的页面
   *
   * @param array $params
   */
  public function getPWBillDataForLodopPrint($params)
  {
    $db = $this->db;

    $id = $params["id"];

    $canViewPrice = $params["canViewPrice"];

    $sql = "select p.ref, p.bill_status, p.ref, p.biz_dt, u1.name as biz_user_name, u2.name as input_user_name,
              p.goods_money, w.name as warehouse_name, s.name as supplier_name,
              p.date_created, p.payment_type, p.company_id, p.bill_memo,
              p.money_with_tax
            from t_pw_bill p, t_warehouse w, t_supplier s, t_user u1, t_user u2
            where (p.warehouse_id = w.id) and (p.supplier_id = s.id)
              and (p.biz_user_id = u1.id) and (p.input_user_id = u2.id)
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

    $result = [];

    $result["ref"] = $v["ref"];
    $result["billStatus"] = $v["bill_status"];
    $result["supplierName"] = $v["supplier_name"];
    $result["goodsMoney"] = $canViewPrice ? $v["goods_money"] : "****";
    $result["moneyWithTax"] = $canViewPrice ? $v["money_with_tax"] : "****";
    $result["bizDT"] = $this->toYMD($v["biz_dt"]);
    $result["warehouseName"] = $v["warehouse_name"];
    $result["bizUserName"] = $v["biz_user_name"];
    $result["billMemo"] = $v["bill_memo"];

    $result["printDT"] = date("Y-m-d H:i:s");

    $sql = "select g.code, g.name, g.spec, u.name as unit_name,
              convert(p.goods_count, $fmt) as goods_count, p.goods_price,
              p.goods_money, p.memo, p.tax_rate, p.money_with_tax
            from t_pw_bill_detail p, t_goods g, t_goods_unit u
            where p.pwbill_id = '%s' and p.goods_id = g.id and g.unit_id = u.id
            order by p.show_order ";
    $items = [];
    $data = $db->query($sql, $id);

    foreach ($data as $v) {
      $items[] = [
        "goodsCode" => $v["code"],
        "goodsName" => $v["name"],
        "goodsSpec" => $v["spec"],
        "goodsCount" => $v["goods_count"],
        "unitName" => $v["unit_name"],
        "goodsPrice" => $canViewPrice ? $v["goods_price"] : "****",
        "goodsMoney" => $canViewPrice ? $v["goods_money"] : "****",
        "taxRate" => $canViewPrice ? intval($v["tax_rate"]) : "****",
        "moneyWithTax" => $canViewPrice ? $v["money_with_tax"] : "****",
        "memo" => $v["memo"]
      ];
    }

    $result["items"] = $items;

    return $result;
  }
}
