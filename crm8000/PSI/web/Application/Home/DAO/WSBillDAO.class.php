<?php

namespace Home\DAO;

use Home\Common\FIdConst;

/**
 * 销售出库单 DAO
 *
 * @author 李静波
 */
class WSBillDAO extends PSIBaseExDAO
{

  /**
   * 生成新的销售出库单单号
   *
   * @param string $companyId
   * @return string
   */
  private function genNewBillRef($companyId)
  {
    $db = $this->db;

    $bs = new BizConfigDAO($db);
    $pre = $bs->getWSBillRefPre($companyId);

    $mid = date("Ymd");

    $sql = "select ref from t_ws_bill where ref like '%s' order by ref desc limit 1";
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

  private function billStatusCodeToName($code)
  {
    switch ($code) {
      case 0:
        return "待出库";
      case 1000:
        return "已出库";
      case 2000:
        return "已退货";
      default:
        return "";
    }
  }

  /**
   * 获得销售出库单主表列表
   *
   * @param array $params
   * @return array
   */
  public function wsbillList($params)
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
    $receivingType = $params["receivingType"];
    $goodsId = $params["goodsId"];

    $sql = "select w.id, w.ref, w.bizdt, c.name as customer_name, u.name as biz_user_name,
              user.name as input_user_name, h.name as warehouse_name, w.sale_money,
              w.bill_status, w.date_created, w.receiving_type, w.memo, w.deal_address,
              w.tax, w.money_with_tax
            from t_ws_bill w, t_customer c, t_user u, t_user user, t_warehouse h
            where (w.customer_id = c.id) and (w.biz_user_id = u.id)
              and (w.input_user_id = user.id) and (w.warehouse_id = h.id) ";
    $queryParams = [];

    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::WAREHOUSING_SALE, "w", $loginUserId);
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
      $sql .= " and (w.id in
					(select d.wsbill_id from t_ws_bill_detail d
					 where d.sn_note like '%s'))";
      $queryParams[] = "%$sn%";
    }
    if ($receivingType != -1) {
      $sql .= " and (w.receiving_type = %d) ";
      $queryParams[] = $receivingType;
    }
    if ($goodsId) {
      $sql .= " and (w.id in
					(select d.wsbill_id from t_ws_bill_detail d
					 where d.goods_id = '%s'))";
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
        "billStatus" => $this->billStatusCodeToName($v["bill_status"]),
        "amount" => $v["sale_money"],
        "dateCreated" => $v["date_created"],
        "receivingType" => $v["receiving_type"],
        "memo" => $v["memo"],
        "dealAddress" => $v["deal_address"],
        "tax" => $v["tax"],
        "moneyWithTax" => $v["money_with_tax"]
      ];
    }

    $sql = "select count(*) as cnt
            from t_ws_bill w, t_customer c, t_user u, t_user user, t_warehouse h
            where (w.customer_id = c.id) and (w.biz_user_id = u.id)
              and (w.input_user_id = user.id) and (w.warehouse_id = h.id) ";
    $queryParams = [];

    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::WAREHOUSING_SALE, "w", $loginUserId);
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
      $sql .= " and (w.id in
					(select d.wsbill_id from t_ws_bill_detail d
					 where d.sn_note like '%s'))";
      $queryParams[] = "%$sn%";
    }
    if ($receivingType != -1) {
      $sql .= " and (w.receiving_type = %d) ";
      $queryParams[] = $receivingType;
    }
    if ($goodsId) {
      $sql .= " and (w.id in
					(select d.wsbill_id from t_ws_bill_detail d
					 where d.goods_id = '%s'))";
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
   * 获得某个销售出库单的明细记录列表
   *
   * @param array $params
   * @return array
   */
  public function wsBillDetailList($params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $billId = $params["billId"];
    $sql = "select d.id, g.code, g.name, g.spec, u.name as unit_name, 
              convert(d.goods_count, $fmt) as goods_count,
              d.goods_price, d.goods_money, d.sn_note, d.memo,
              d.tax_rate, d.tax, d.money_with_tax, d.goods_price_with_tax
            from t_ws_bill_detail d, t_goods g, t_goods_unit u
            where d.wsbill_id = '%s' and d.goods_id = g.id and g.unit_id = u.id
            order by d.show_order";
    $data = $db->query($sql, $billId);
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
        "goodsPrice" => $v["goods_price"],
        "goodsMoney" => $v["goods_money"],
        "sn" => $v["sn_note"],
        "memo" => $v["memo"],
        "taxRate" => $v["tax_rate"],
        "tax" => $v["tax"],
        "moneyWithTax" => $v["money_with_tax"],
        "goodsPriceWithTax" => $goodsPriceWithTax
      ];
    }

    return $result;
  }

  /**
   * 获得某个销售出库单的明细记录列表
   * 销售退货入库 - 选择销售出库单
   *
   * @param array $params
   * @return array
   */
  public function wsBillDetailListForSRBill($params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $billId = $params["billId"];
    $sql = "select d.id, g.code, g.name, g.spec, u.name as unit_name,
              convert(d.goods_count, $fmt) as goods_count,
              d.goods_price, d.goods_money, d.sn_note, d.memo,
              d.tax_rate, d.tax, d.money_with_tax, d.goods_price_with_tax
            from t_ws_bill_detail d, t_goods g, t_goods_unit u
            where d.wsbill_id = '%s' and d.goods_id = g.id and g.unit_id = u.id
            order by d.show_order";
    $data = $db->query($sql, $billId);
    $result = [];

    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "goodsCode" => $v["code"],
        "goodsName" => $v["name"],
        "goodsSpec" => $v["spec"],
        "unitName" => $v["unit_name"],
        "goodsCount" => $v["goods_count"],
        "goodsPrice" => $v["goods_price"],
        "goodsMoney" => $v["goods_money"],
        "sn" => $v["sn_note"],
        "memo" => $v["memo"],
        "taxRate" => $v["tax_rate"],
        "tax" => $v["tax"],
        "moneyWithTax" => $v["money_with_tax"],
        "goodsPriceWithTax" => $v["goods_price_with_tax"]
      ];
    }

    return $result;
  }

  /**
   * 新建销售出库单
   *
   * @param array $bill
   * @return NULL|array
   */
  public function addWSBill(&$bill)
  {
    $db = $this->db;

    $bizDT = $bill["bizDT"];
    $warehouseId = $bill["warehouseId"];
    $customerId = $bill["customerId"];
    $bizUserId = $bill["bizUserId"];
    $receivingType = $bill["receivingType"];
    $billMemo = $bill["billMemo"];
    $items = $bill["items"];
    $dealAddress = $bill["dealAddress"];

    $sobillRef = $bill["sobillRef"];

    // 检查客户
    $customerDAO = new CustomerDAO($db);
    $customer = $customerDAO->getCustomerById($customerId);
    if (!$customer) {
      return $this->bad("选择的客户不存在，无法保存数据");
    }

    // 检查仓库
    $warehouseDAO = new WarehouseDAO($db);
    $warehouse = $warehouseDAO->getWarehouseById($warehouseId);
    if (!$warehouse) {
      return $this->bad("选择的仓库不存在，无法保存数据");
    }

    // 检查业务员
    $userDAO = new UserDAO($db);
    $user = $userDAO->getUserById($bizUserId);
    if (!$user) {
      return $this->bad("选择的业务员不存在，无法保存数据");
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

    // 主表
    $id = $this->newId();
    $ref = $this->genNewBillRef($companyId);
    $sql = "insert into t_ws_bill(id, bill_status, bizdt, biz_user_id, customer_id,  date_created,
              input_user_id, ref, warehouse_id, receiving_type, data_org, company_id, memo, deal_address)
            values ('%s', 0, '%s', '%s', '%s', now(), '%s', '%s', '%s', %d, '%s', '%s', '%s', '%s')";

    $rc = $db->execute(
      $sql,
      $id,
      $bizDT,
      $bizUserId,
      $customerId,
      $loginUserId,
      $ref,
      $warehouseId,
      $receivingType,
      $dataOrg,
      $companyId,
      $billMemo,
      $dealAddress
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 明细表
    $sql = "insert into t_ws_bill_detail (id, date_created, goods_id,
              goods_count, goods_price, goods_money,
              show_order, wsbill_id, sn_note, data_org, memo, company_id, sobilldetail_id,
              tax_rate, tax, money_with_tax, goods_price_with_tax)
            values ('%s', now(), '%s', convert(%f, $fmt), %f, %f, %d, '%s', '%s', '%s', '%s', '%s', '%s',
              %d, %f, %f, %f)";
    foreach ($items as $i => $v) {
      $goodsId = $v["goodsId"];
      if ($goodsId) {
        $goodsCount = $v["goodsCount"];
        $goodsPrice = floatval($v["goodsPrice"]);
        $goodsMoney = floatval($v["goodsMoney"]);
        $goodsPriceWithTax = $v["goodsPriceWithTax"];

        $sn = $v["sn"];
        $memo = $v["memo"];

        $soBillDetailId = $v["soBillDetailId"];

        $taxRate = $v["taxRate"];
        $tax = $v["tax"];
        $moneyWithTax = $v["moneyWithTax"];

        $rc = $db->execute(
          $sql,
          $this->newId(),
          $goodsId,
          $goodsCount,
          $goodsPrice,
          $goodsMoney,
          $i,
          $id,
          $sn,
          $dataOrg,
          $memo,
          $companyId,
          $soBillDetailId,
          $taxRate,
          $tax,
          $moneyWithTax,
          $goodsPriceWithTax
        );
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      }
    }
    $sql = "select sum(goods_money) as sum_goods_money,
              sum(tax) as tax, sum(money_with_tax) as money_with_tax 
            from t_ws_bill_detail where wsbill_id = '%s' ";
    $data = $db->query($sql, $id);
    $sumGoodsMoney = $data[0]["sum_goods_money"];
    if (!$sumGoodsMoney) {
      $sumGoodsMoney = 0;
    }
    $sumTax = $data[0]["tax"];
    $sumMoneyWithTax = $data[0]["money_with_tax"];

    $sql = "update t_ws_bill 
            set sale_money = %f, tax = %f, money_with_tax = %f 
            where id = '%s' ";
    $rc = $db->execute($sql, $sumGoodsMoney, $sumTax, $sumMoneyWithTax, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    if ($sobillRef) {
      // 从销售订单生成销售出库单
      $sql = "select id, company_id from t_so_bill where ref = '%s' ";
      $data = $db->query($sql, $sobillRef);
      if (!$data) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
      $sobillId = $data[0]["id"];
      $companyId = $data[0]["company_id"];

      $sql = "update t_ws_bill
              set company_id = '%s'
              where id = '%s' ";
      $rc = $db->execute($sql, $companyId, $id);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }

      $sql = "insert into t_so_ws(so_id, ws_id) values('%s', '%s')";
      $rc = $db->execute($sql, $sobillId, $id);
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
   * 通过销售出库单id查询销售出库单
   *
   * @param string $id
   *        	销售出库单id
   * @return array|NULL
   */
  public function getWSBillById($id)
  {
    $db = $this->db;

    $sql = "select ref, bill_status, data_org, company_id from t_ws_bill where id = '%s' ";
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
   * 检查库存数量是否够出库
   */
  public function checkInv(&$bill)
  {
    $db = $this->db;
    $warehouseId = $bill["warehouseId"];
    $items = $bill["items"];

    $list = [];
    foreach ($items as $v) {
      $goodsId = $v["goodsId"];
      if (!$goodsId) {
        continue;
      }
      $goodsCount = $v["goodsCount"];

      // 合并多条同一个商品的数量
      $found = false;
      foreach ($list as $li) {
        if ($li["id"] == $goodsId) {
          $li["cnt"] += $goodsCount;
          $found = true;
          break;
        }
      }
      if (!$found) {
        $list[] = ["id" => $goodsId, "cnt" => $goodsCount];
      }
    }

    foreach ($list as $v) {
      $goodsId = $v["id"];
      $goodsCount = $v["cnt"];

      $sql = "select balance_count from t_inventory
              where warehouse_id = '%s' and goods_id = '%s' ";
      $data = $db->query($sql, $warehouseId, $goodsId);

      $hasInv = true;
      if (!$data) {
        // 没有库存 
        $hasInv = false;
      } else {
        $cnt = $data[0]["balance_count"];
        if ($cnt < $goodsCount) {
          $hasInv = false;
        }
      }
      if (!$hasInv) {
        $goodsDAO = new GoodsDAO($db);
        $goods = $goodsDAO->getGoodsById($goodsId);
        $code = $goods["code"];
        $name = $goods["name"];
        $info = "商品[编码={$code} 名称={$name}]库存不足，是否仍然保存数据?";
        return [
          "success" => false,
          "msg" => $info,
          "checkInv" => "1"
        ];
      }
    }

    // 返回null表述库存数量足够
    return null;
  }

  /**
   * 编辑销售出库单
   *
   * @param array $bill
   * @return NULL|array
   */
  public function updateWSBill(&$bill)
  {
    $db = $this->db;

    $id = $bill["id"];

    $bizDT = $bill["bizDT"];
    $warehouseId = $bill["warehouseId"];
    $customerId = $bill["customerId"];
    $bizUserId = $bill["bizUserId"];
    $receivingType = $bill["receivingType"];
    $billMemo = $bill["billMemo"];
    $items = $bill["items"];
    $dealAddress = $bill["dealAddress"];

    // 检查客户
    $customerDAO = new CustomerDAO($db);
    $customer = $customerDAO->getCustomerById($customerId);
    if (!$customer) {
      return $this->bad("选择的客户不存在，无法保存数据");
    }

    // 检查仓库
    $warehouseDAO = new WarehouseDAO($db);
    $warehouse = $warehouseDAO->getWarehouseById($warehouseId);
    if (!$warehouse) {
      return $this->bad("选择的仓库不存在，无法保存数据");
    }

    // 检查业务员
    $userDAO = new UserDAO($db);
    $user = $userDAO->getUserById($bizUserId);
    if (!$user) {
      return $this->bad("选择的业务员不存在，无法保存数据");
    }

    // 检查业务日期
    if (!$this->dateIsValid($bizDT)) {
      return $this->bad("业务日期不正确");
    }

    $loginUserId = $bill["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->badParam("loginUserId");
    }

    $oldBill = $this->getWSBillById($id);
    if (!$oldBill) {
      return $this->bad("要编辑的销售出库单不存在");
    }
    $ref = $oldBill["ref"];
    $billStatus = $oldBill["billStatus"];
    if ($billStatus != 0) {
      return $this->bad("销售出库单[单号：{$ref}]已经提交出库了，不能再编辑");
    }
    $dataOrg = $oldBill["dataOrg"];
    $companyId = $oldBill["companyId"];

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $sql = "delete from t_ws_bill_detail where wsbill_id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $sql = "insert into t_ws_bill_detail (id, date_created, goods_id,
              goods_count, goods_price, goods_money,
              show_order, wsbill_id, sn_note, data_org, memo, company_id, sobilldetail_id,
              tax_rate, tax, money_with_tax, goods_price_with_tax)
            values ('%s', now(), '%s', convert(%f, $fmt), %f, %f, %d, '%s', '%s', '%s', '%s', '%s', '%s',
              %d, %f, %f, %f)";
    foreach ($items as $i => $v) {
      $goodsId = $v["goodsId"];
      if ($goodsId) {
        $goodsCount = $v["goodsCount"];
        $goodsPrice = floatval($v["goodsPrice"]);
        $goodsMoney = floatval($v["goodsMoney"]);
        $goodsPriceWithTax = $v["goodsPriceWithTax"];

        $sn = $v["sn"];
        $memo = $v["memo"];

        $soBillDetailId = $v["soBillDetailId"];

        $taxRate = $v["taxRate"];
        $tax = $v["tax"];
        $moneyWithTax = $v["moneyWithTax"];

        $rc = $db->execute(
          $sql,
          $this->newId(),
          $goodsId,
          $goodsCount,
          $goodsPrice,
          $goodsMoney,
          $i,
          $id,
          $sn,
          $dataOrg,
          $memo,
          $companyId,
          $soBillDetailId,
          $taxRate,
          $tax,
          $moneyWithTax,
          $goodsPriceWithTax
        );
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      }
    }
    $sql = "select sum(goods_money) as sum_goods_money,
              sum(tax) as tax, sum(money_with_tax) as money_with_tax
            from t_ws_bill_detail where wsbill_id = '%s' ";
    $data = $db->query($sql, $id);
    $sumGoodsMoney = $data[0]["sum_goods_money"];
    if (!$sumGoodsMoney) {
      $sumGoodsMoney = 0;
    }
    $sumTax = $data[0]["tax"];
    $sumMoneyWithTax = $data[0]["money_with_tax"];

    $sql = "update t_ws_bill
            set sale_money = %f, customer_id = '%s', warehouse_id = '%s',
              biz_user_id = '%s', bizdt = '%s', receiving_type = %d,
              memo = '%s', deal_address = '%s',
              tax = %f, money_with_tax = %f
            where id = '%s' ";
    $rc = $db->execute(
      $sql,
      $sumGoodsMoney,
      $customerId,
      $warehouseId,
      $bizUserId,
      $bizDT,
      $receivingType,
      $billMemo,
      $dealAddress,
      $sumTax,
      $sumMoneyWithTax,
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
   * 判断是否可以编辑商品销售单价
   *
   * @param string $companyId
   * @param string $userId
   *
   * @return boolean true:可以编辑销售单价
   */
  private function canEditGoodsPrice($companyId, $userId)
  {
    // 首先判断业务设置中是否允许销售出库编辑销售单价（全局控制）
    $db = $this->db;
    $sql = "select value from t_config 
            where id = '2002-01' and company_id = '%s' ";
    $data = $db->query($sql, $companyId);
    if (!$data) {
      return false;
    }

    $v = intval($data[0]["value"]);
    if ($v == 0) {
      return false;
    }

    $us = new UserDAO($db);
    // 在业务设置中启用编辑的前提下，还需要判断对应的权限（具体的用户）
    return $us->hasPermission($userId, "2002-01");
  }

  /**
   * 新建或编辑的时候，获得销售出库单的详情
   *
   * @param array $params
   * @return array
   */
  public function wsBillInfo($params)
  {
    $db = $this->db;

    $id = $params["id"];
    $sobillRef = $params["sobillRef"];

    $companyId = $params["companyId"];
    $loginUserId = $params["loginUserId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->emptyResult();
    }

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $result = [];

    $userDAO = new UserDAO($db);
    $result["canEditGoodsPrice"] = $this->canEditGoodsPrice($companyId, $loginUserId);
    $result["showAddCustomerButton"] = $userDAO->hasPermission($loginUserId, FIdConst::CUSTOMER);

    if (!$id) {
      // 新建销售出库单
      $result["bizUserId"] = $loginUserId;
      $result["bizUserName"] = $params["loginUserName"];

      $sql = "select value from t_config 
              where id = '2002-02' and company_id = '%s' ";
      $data = $db->query($sql, $companyId);
      if ($data) {
        $warehouseId = $data[0]["value"];
        $sql = "select id, name from t_warehouse where id = '%s' ";
        $data = $db->query($sql, $warehouseId);
        if ($data) {
          $result["warehouseId"] = $data[0]["id"];
          $result["warehouseName"] = $data[0]["name"];
        }
      }

      if ($sobillRef) {
        // 由销售订单生成销售出库单
        $sql = "select s.id, s.customer_id, c.name as customer_name, s.deal_date,
                  s.receiving_type, s.bill_memo, s.deal_address
                from t_so_bill s, t_customer c
                where s.ref = '%s' and s.customer_id = c.id ";
        $data = $db->query($sql, $sobillRef);
        if ($data) {
          $v = $data[0];
          $result["customerId"] = $v["customer_id"];
          $result["customerName"] = $v["customer_name"];
          $result["dealDate"] = $this->toYMD($v["deal_date"]);
          $result["receivingType"] = $v["receiving_type"];
          $result["memo"] = $v["bill_memo"];
          $result["dealAddress"] = $v["deal_address"];

          $customerDAO = new CustomerDAO($db);
          $warehosue = $customerDAO->getSalesWarehouse($v["customer_id"]);
          if ($warehosue) {
            $result["warehouseId"] = $warehosue["id"];
            $result["warehouseName"] = $warehosue["name"];
          }

          $pobillId = $v["id"];
          // 销售订单的明细
          $items = [];
          $sql = "select s.id, s.goods_id, g.code, g.name, g.spec, u.name as unit_name,
                    convert(s.goods_count, " . $fmt . ") as goods_count, 
                    s.goods_price, s.goods_money, 
                    convert(s.left_count, " . $fmt . ") as left_count, s.memo,
                    s.tax_rate, s.tax, s.money_with_tax, s.goods_price_with_tax
                  from t_so_bill_detail s, t_goods g, t_goods_unit u
                  where s.sobill_id = '%s' and s.goods_id = g.id and g.unit_id = u.id
                  order by s.show_order ";
          $data = $db->query($sql, $pobillId);
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
              "goodsCount" => $v["left_count"],
              "goodsPrice" => $v["goods_price"],
              "goodsMoney" => $v["left_count"] * $v["goods_price"],
              "soBillDetailId" => $v["id"],
              "memo" => $v["memo"],
              "taxRate" => $v["tax_rate"],
              "tax" => $v["tax"],
              "moneyWithTax" => $v["money_with_tax"],
              "goodsPriceWithTax" => $goodsPriceWithTax
            ];
          }

          $result["items"] = $items;
        }
      } else {
        // 销售出库单默认收款方式
        $bc = new BizConfigDAO($db);
        $result["receivingType"] = $bc->getWSBillDefaultReceving($companyId);
      }

      return $result;
    } else {
      // 编辑
      $sql = "select w.id, w.ref, w.bill_status, w.bizdt, c.id as customer_id, c.name as customer_name,
                u.id as biz_user_id, u.name as biz_user_name,
                h.id as warehouse_id, h.name as warehouse_name,
                w.receiving_type, w.memo, w.deal_address
              from t_ws_bill w, t_customer c, t_user u, t_warehouse h
              where w.customer_id = c.id and w.biz_user_id = u.id
                and w.warehouse_id = h.id
                and w.id = '%s' ";
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
        $result["receivingType"] = $data[0]["receiving_type"];
        $result["memo"] = $data[0]["memo"];
        $result["dealAddress"] = $data[0]["deal_address"];
      }

      $sql = "select d.id, g.id as goods_id, g.code, g.name, g.spec, u.name as unit_name, 
                convert(d.goods_count, $fmt) as goods_count,
                d.goods_price, d.goods_money, d.sn_note, d.memo, d.sobilldetail_id,
                d.tax_rate, d.tax, d.money_with_tax, d.goods_price_with_tax
              from t_ws_bill_detail d, t_goods g, t_goods_unit u
              where d.wsbill_id = '%s' and d.goods_id = g.id and g.unit_id = u.id
              order by d.show_order";
      $data = $db->query($sql, $id);
      $items = [];
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
          "goodsPrice" => $v["goods_price"],
          "goodsMoney" => $v["goods_money"],
          "sn" => $v["sn_note"],
          "memo" => $v["memo"],
          "soBillDetailId" => $v["sobilldetail_id"],
          "taxRate" => $v["tax_rate"],
          "tax" => $v["tax"],
          "moneyWithTax" => $v["money_with_tax"],
          "goodsPriceWithTax" => $goodsPriceWithTax
        ];
      }

      $result["items"] = $items;

      return $result;
    }
  }

  /**
   * 删除销售出库单
   *
   * @param array $params
   * @return NULL|array
   */
  public function deleteWSBill(&$params)
  {
    $db = $this->db;

    $id = $params["id"];

    $bill = $this->getWSBillById($id);

    if (!$bill) {
      return $this->bad("要删除的销售出库单不存在");
    }
    $ref = $bill["ref"];
    $billStatus = $bill["billStatus"];
    if ($billStatus != 0) {
      return $this->bad("销售出库单已经提交出库，不能删除");
    }

    $sql = "delete from t_ws_bill_detail where wsbill_id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $sql = "delete from t_ws_bill where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 删除从销售订单生成的记录
    $sql = "delete from t_so_ws where ws_id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $params["ref"] = $ref;

    // 操作成功
    return null;
  }

  /**
   * 查询数据，用于销售出库单生成pdf文件
   *
   * @param array $params
   * @return array|null
   */
  public function getDataForPDF($params)
  {
    $ref = $params["ref"];

    $db = $this->db;
    $sql = "select w.id, w.bizdt, c.name as customer_name,
              u.name as biz_user_name,
              h.name as warehouse_name,
              w.sale_money, w.memo, w.deal_address, w.company_id,
              w.money_with_tax
            from t_ws_bill w, t_customer c, t_user u, t_warehouse h
            where w.customer_id = c.id and w.biz_user_id = u.id
              and w.warehouse_id = h.id
              and w.ref = '%s' ";
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

    $bill["bizDT"] = $this->toYMD($data[0]["bizdt"]);
    $bill["customerName"] = $data[0]["customer_name"];
    $bill["warehouseName"] = $data[0]["warehouse_name"];
    $bill["bizUserName"] = $data[0]["biz_user_name"];
    $bill["saleMoney"] = $data[0]["sale_money"];
    $bill["memo"] = $data[0]["memo"];
    $bill["dealAddress"] = $data[0]["deal_address"];
    $bill["moneyWithTax"] = $data[0]["money_with_tax"];

    // 明细表
    $sql = "select g.code, g.name, g.spec, u.name as unit_name, 
              convert(d.goods_count, $fmt) as goods_count,
              d.goods_price, d.goods_money, d.sn_note,
              d.tax_rate, d.money_with_tax
            from t_ws_bill_detail d, t_goods g, t_goods_unit u
            where d.wsbill_id = '%s' and d.goods_id = g.id and g.unit_id = u.id
            order by d.show_order";
    $data = $db->query($sql, $id);
    $items = [];
    foreach ($data as $v) {
      $items[] = [
        "goodsCode" => $v["code"],
        "goodsName" => $v["name"],
        "goodsSpec" => $v["spec"],
        "unitName" => $v["unit_name"],
        "goodsCount" => $v["goods_count"],
        "goodsPrice" => $v["goods_price"],
        "goodsMoney" => $v["goods_money"],
        "sn" => $v["sn_note"],
        "taxRate" => intval($v["tax_rate"]),
        "moneyWithTax" => $v["money_with_tax"]
      ];
    }
    $bill["items"] = $items;

    return $bill;
  }

  /**
   * 提交销售出库单
   *
   * @param array $params
   * @return NULL|array
   */
  public function commitWSBill(&$params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->badParam("companyId");
    }
    $loginUserId = $params["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->badParam("loginUserId");
    }

    $bs = new BizConfigDAO($db);
    // true: 先进先出
    $fifo = $bs->getInventoryMethod($companyId) == 1;

    $dataScale = $bs->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $id = $params["id"];

    $sql = "select ref, bill_status, customer_id, warehouse_id, biz_user_id, bizdt, sale_money,
              receiving_type, company_id, money_with_tax, tax
            from t_ws_bill where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return $this->bad("要提交的销售出库单不存在");
    }
    $ref = $data[0]["ref"];
    $bizDT = $data[0]["bizdt"];
    $bizUserId = $data[0]["biz_user_id"];
    $billStatus = $data[0]["bill_status"];
    $receivingType = $data[0]["receiving_type"];
    // money_with_tax是后加的字段，可能没值
    $saleMoney = $data[0]["money_with_tax"] ?? $data[0]["sale_money"];
    $tax = $data[0]["tax"] ?? 0;
    $companyId = $data[0]["company_id"];
    if ($billStatus != 0) {
      return $this->bad("销售出库单已经提交出库，不能再次提交");
    }
    $customerId = $data[0]["customer_id"];
    $warehouseId = $data[0]["warehouse_id"];

    $customerDAO = new CustomerDAO($db);
    $customer = $customerDAO->getCustomerById($customerId);
    if (!$customer) {
      return $this->bad("客户不存在");
    }

    $warehouseDAO = new WarehouseDAO($db);
    $warehouse = $warehouseDAO->getWarehouseById($warehouseId);
    if (!$warehouse) {
      return $this->bad("仓库不存在");
    }
    $warehouseName = $warehouse["name"];
    $inited = $warehouse["inited"];
    if ($inited != 1) {
      return $this->bad("仓库 [{$warehouseName}]还没有建账，不能进行出库操作");
    }

    // 检查销售出库仓库是否正确
    $salesWarehouse = $customerDAO->getSalesWarehouse($customerId);
    if ($salesWarehouse) {
      $salesWarehouseId = $salesWarehouse["id"];
      if ($salesWarehouseId != $warehouseId) {
        $salesWarehouseName = $salesWarehouse["name"];
        $info = "当前客户的销售出库仓库只能是：{$salesWarehouseName}<br/><br/>";
        $info .= "请重新编辑出库单，选择正确的销售出库仓库";
        return $this->bad($info);
      }
    }

    $userDAO = new UserDAO($db);
    $user = $userDAO->getUserById($bizUserId);
    if (!$user) {
      return $this->bad("业务员不存在");
    }

    $allReceivingType = array(
      0,
      1,
      2
    );

    if (!in_array($receivingType, $allReceivingType)) {
      return $this->bad("收款方式不正确，无法完成提交操作");
    }

    $sql = "select id, goods_id, convert(goods_count, $fmt) as goods_count, goods_price, sobilldetail_id
            from t_ws_bill_detail
            where wsbill_id = '%s'
            order by show_order ";
    $items = $db->query($sql, $id);
    if (!$items) {
      return $this->bad("销售出库单没有出库商品明细记录，无法出库");
    }

    $bizConfigDAO = new BizConfigDAO($db);
    // 销售出库数量控制，true - 出库数量不能超过销售订单未出库数量
    $countLimit = $bizConfigDAO->getWSCountLimit($companyId) == "1";

    $sql = "select so_id
            from t_so_ws
            where ws_id = '%s' ";
    $data = $db->query($sql, $id);
    $soId = null;
    if ($data) {
      $soId = $data[0]["so_id"];
    }

    // 检查销售出库数量控制
    foreach ($items as $i => $v) {
      if (!$countLimit) {
        continue;
      }
      if (!$soId) {
        continue;
      }

      $goodsCount = $v["goods_count"];
      $soBillDetailId = $v["sobilldetail_id"];
      $sql = "select convert(left_count, $fmt) as left_count
              from t_so_bill_detail
              where id = '%s' ";
      $data = $db->query($sql, $soBillDetailId);
      if (!$data) {
        continue;
      }
      $leftCount = $data[0]["left_count"];
      if ($goodsCount > $leftCount) {
        $index = $i + 1;
        $info = "第{$index}条出库记录中销售出库数量超过销售订单上未出库数量<br/><br/>";
        $info .= "出库数量是: {$goodsCount}<br/>销售订单中未出库数量是: {$leftCount}";
        return $this->bad($info);
      }
    }

    foreach ($items as $v) {
      $itemId = $v["id"];
      $goodsId = $v["goods_id"];
      $goodsCount = $v["goods_count"];

      $sql = "select code, name from t_goods where id = '%s' ";
      $data = $db->query($sql, $goodsId);
      if (!$data) {
        return $this->bad("要出库的商品不存在(商品后台id = {$goodsId})");
      }
      $goodsCode = $data[0]["code"];
      $goodsName = $data[0]["name"];
      if ($goodsCount <= 0) {
        // 忽略非正数
        // 一个原因：由销售订单生产销售出库单的时候，如果是部分发货，就有一些商品的数量是0
        // 当然了，数量字段出现负数基本上是其他bug导致的，这里就简单地一并忽略了
        continue;
        // return $this->bad("商品[{$goodsCode} {$goodsName}]的出库数量需要是正数");
      }

      $soBillDetailId = $v["sobilldetail_id"];

      if ($fifo) {
        // 先进先出法

        // 库存总账
        $sql = "select out_count, out_money, balance_count, balance_price,
                  balance_money from t_inventory
                where warehouse_id = '%s' and goods_id = '%s' ";
        $data = $db->query($sql, $warehouseId, $goodsId);
        if (!$data) {
          return $this->bad(
            "商品 [{$goodsCode} {$goodsName}] 在仓库 [{$warehouseName}] 中没有存货，无法出库"
          );
        }
        $balanceCount = $data[0]["balance_count"];
        if ($balanceCount < $goodsCount) {
          return $this->bad(
            "商品 [{$goodsCode} {$goodsName}] 在仓库 [{$warehouseName}] 中存货数量不足，无法出库"
          );
        }
        $balancePrice = $data[0]["balance_price"];
        $balanceMoney = $data[0]["balance_money"];
        $outCount = $data[0]["out_count"];
        $outMoney = $data[0]["out_money"];

        $sql = "select id, balance_count, balance_price, balance_money,
                  out_count, out_price, out_money, date_created
                from t_inventory_fifo
                where warehouse_id = '%s' and goods_id = '%s'
                  and balance_count > 0
                order by date_created ";
        $data = $db->query($sql, $warehouseId, $goodsId);
        if (!$data) {
          return $this->sqlError(__METHOD__, __LINE__);
        }

        $gc = $goodsCount;
        $fifoMoneyTotal = 0;
        for ($i = 0; $i < count($data); $i++) {
          if ($gc == 0) {
            break;
          }

          $fv = $data[$i];
          $fvBalanceCount = $fv["balance_count"];
          $fvId = $fv["id"];
          $fvBalancePrice = $fv["balance_price"];
          $fvBalanceMoney = $fv["balance_money"];
          $fvOutCount = $fv["out_count"];
          $fvOutMoney = $fv["out_money"];
          $fvDateCreated = $fv["date_created"];

          if ($fvBalanceCount >= $gc) {
            if ($fvBalanceCount > $gc) {
              $fifoMoney = $fvBalancePrice * $gc;
            } else {
              $fifoMoney = $fvBalanceMoney;
            }
            $fifoMoneyTotal += $fifoMoney;

            $fifoPrice = $fifoMoney / $gc;

            $fvOutCount += $gc;
            $fvOutMoney += $fifoMoney;
            $fvOutPrice = $fvOutMoney / $fvOutCount;

            $fvBalanceCount -= $gc;
            $fvBalanceMoney -= $fifoMoney;

            $sql = "update t_inventory_fifo
                    set out_count = %d, out_price = %f, out_money = %f,
                      balance_count = %d, balance_money = %f
                    where id = %d ";
            $rc = $db->execute(
              $sql,
              $fvOutCount,
              $fvOutPrice,
              $fvOutMoney,
              $fvBalanceCount,
              $fvBalanceMoney,
              $fvId
            );
            if ($rc === false) {
              return $this->sqlError(__METHOD__, __LINE__);
            }

            // fifo 的明细记录
            $sql = "insert into t_inventory_fifo_detail(out_count, out_price, out_money,
                      balance_count, balance_price, balance_money, warehouse_id, goods_id,
                      date_created, wsbilldetail_id)
                    values (%d, %f, %f, %d, %f, %f, '%s', '%s', '%s', '%s')";
            $rc = $db->execute(
              $sql,
              $gc,
              $fifoPrice,
              $fifoMoney,
              $fvBalanceCount,
              $fvBalancePrice,
              $fvBalanceMoney,
              $warehouseId,
              $goodsId,
              $fvDateCreated,
              $itemId
            );
            if ($rc === false) {
              return $this->sqlError(__METHOD__, __LINE__);
            }

            $gc = 0;
          } else {
            $fifoMoneyTotal += $fvBalanceMoney;

            $sql = "update t_inventory_fifo
                    set out_count = in_count, out_price = in_price, out_money = in_money,
                      balance_count = 0, balance_money = 0
                    where id = %d ";
            $rc = $db->execute($sql, $fvId);
            if ($rc === false) {
              return $this->sqlError(__METHOD__, __LINE__);
            }

            // fifo 的明细记录
            $sql = "insert into t_inventory_fifo_detail(out_count, out_price, out_money,
                      balance_count, balance_price, balance_money, warehouse_id, goods_id,
                      date_created, wsbilldetail_id)
                    values (%d, %f, %f, %d, %f, %f, '%s', '%s', '%s', '%s')";
            $rc = $db->execute(
              $sql,
              $fvBalanceCount,
              $fvBalancePrice,
              $fvBalanceMoney,
              0,
              0,
              0,
              $warehouseId,
              $goodsId,
              $fvDateCreated,
              $itemId
            );
            if ($rc === false) {
              return $this->sqlError(__METHOD__, __LINE__);
            }

            $gc -= $fvBalanceCount;
          }
        }

        $fifoPrice = $fifoMoneyTotal / $goodsCount;

        // 更新总账
        $outCount += $goodsCount;
        $outMoney += $fifoMoneyTotal;
        $outPrice = $outMoney / $outCount;
        $balanceCount -= $goodsCount;
        if ($balanceCount == 0) {
          $balanceMoney = 0;
          $balancePrice = 0;
        } else {
          $balanceMoney -= $fifoMoneyTotal;
          $balancePrice = $balanceMoney / $balanceCount;
        }

        $sql = "update t_inventory
                set out_count = %d, out_price = %f, out_money = %f,
                  balance_count = %d, balance_price = %f, balance_money = %f
                where warehouse_id = '%s' and goods_id = '%s' ";
        $rc = $db->execute(
          $sql,
          $outCount,
          $outPrice,
          $outMoney,
          $balanceCount,
          $balancePrice,
          $balanceMoney,
          $warehouseId,
          $goodsId
        );
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }

        // 更新明细账
        $sql = "insert into t_inventory_detail(out_count, out_price, out_money,
                  balance_count, balance_price, balance_money, warehouse_id,
                  goods_id, biz_date, biz_user_id, date_created, ref_number, ref_type)
                values(%d, %f, %f, %d, %f, %f, '%s', '%s', '%s', '%s', now(), '%s', '销售出库')";
        $rc = $db->execute(
          $sql,
          $goodsCount,
          $fifoPrice,
          $fifoMoneyTotal,
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

        // 更新单据本身的记录
        $sql = "update t_ws_bill_detail
                set inventory_price = %f, inventory_money = %f
                where id = '%s' ";
        $rc = $db->execute($sql, $fifoPrice, $fifoMoneyTotal, $id);
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      } else {
        // 移动平均法

        // 库存总账
        $sql = "select convert(out_count, $fmt) as out_count, out_money, 
                  convert(balance_count, $fmt) as balance_count, balance_price,
                  balance_money from t_inventory
                where warehouse_id = '%s' and goods_id = '%s' ";
        $data = $db->query($sql, $warehouseId, $goodsId);
        if (!$data) {
          return $this->bad(
            "商品 [{$goodsCode} {$goodsName}] 在仓库 [{$warehouseName}] 中没有存货，无法出库"
          );
        }
        $balanceCount = $data[0]["balance_count"];
        if ($balanceCount < $goodsCount) {
          return $this->bad(
            "商品 [{$goodsCode} {$goodsName}] 在仓库 [{$warehouseName}] 中存货数量不足，无法出库"
          );
        }
        $balancePrice = $data[0]["balance_price"];
        $balanceMoney = $data[0]["balance_money"];
        $outCount = $data[0]["out_count"];
        $outMoney = $data[0]["out_money"];
        $balanceCount -= $goodsCount;
        if ($balanceCount == 0) {
          // 当全部出库的时候，金额也需要全部转出去
          $outMoney += $balanceMoney;
          $outPriceDetail = $balanceMoney / $goodsCount;
          $outMoneyDetail = $balanceMoney;
          $balanceMoney = 0;
        } else {
          if ($goodsCount * $balancePrice > $balanceMoney) {
            // 因为单价是保存两位小数，所以当单价小，数量大的时候会出现这种情形
            $outMoney = $balanceMoney;
            $outPriceDetail = $balancePrice;
            $outMoneyDetail = $balanceMoney;
            $balanceMoney = 0;
          } else {
            $outMoney += $goodsCount * $balancePrice;
            $outPriceDetail = $balancePrice;
            $outMoneyDetail = $goodsCount * $balancePrice;
            $balanceMoney -= $goodsCount * $balancePrice;
          }
        }
        $outCount += $goodsCount;
        $outPrice = $outMoney / $outCount;

        $sql = "update t_inventory
                set out_count = convert(%f, $fmt), out_price = %f, out_money = %f,
                  balance_count = convert(%f, $fmt), balance_money = %f
                where warehouse_id = '%s' and goods_id = '%s' ";
        $rc = $db->execute(
          $sql,
          $outCount,
          $outPrice,
          $outMoney,
          $balanceCount,
          $balanceMoney,
          $warehouseId,
          $goodsId
        );
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }

        // 库存明细账
        $sql = "insert into t_inventory_detail(out_count, out_price, out_money,
                  balance_count, balance_price, balance_money, warehouse_id,
                  goods_id, biz_date, biz_user_id, date_created, ref_number, ref_type)
                values(convert(%f, $fmt), %f, %f, convert(%f, $fmt), %f, %f, '%s', '%s', '%s', '%s', now(), '%s', '销售出库')";
        $rc = $db->execute(
          $sql,
          $goodsCount,
          $outPriceDetail,
          $outMoneyDetail,
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

        // 单据本身的记录
        $sql = "update t_ws_bill_detail
                set inventory_price = %f, inventory_money = %f
                where id = '%s' ";
        $rc = $db->execute($sql, $outPriceDetail, $outMoneyDetail, $itemId);
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      }

      // 同步销售订单中的出库量
      $sql = "select convert(goods_count, $fmt) as goods_count, 
                convert(ws_count, $fmt) as ws_count,
                scbilldetail_id
              from t_so_bill_detail
              where id = '%s' ";
      $soDetail = $db->query($sql, $soBillDetailId);
      if (!$soDetail) {
        // 不是由销售订单创建的出库单
        continue;
      }
      $scbillDetailId = $soDetail[0]["scbilldetail_id"];

      $totalGoodsCount = $soDetail[0]["goods_count"];
      $totalWSCount = $soDetail[0]["ws_count"];
      $totalWSCount += $goodsCount;
      $totalLeftCount = $totalGoodsCount - $totalWSCount;
      $sql = "update t_so_bill_detail
              set ws_count = convert(%f, $fmt), left_count = convert(%f, $fmt)
              where id = '%s' ";
      $rc = $db->execute($sql, $totalWSCount, $totalLeftCount, $soBillDetailId);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }

      // 同步销售合同中的订单执行量
      if ($scbillDetailId) {
        $sql = "select convert(goods_count, $fmt) as goods_count, 
                  convert(so_count, $fmt) as so_count
                from t_sc_bill_detail
                where id = '%s' ";
        $data = $db->query($sql, $scbillDetailId);
        if (!$data) {
          // 如果执行到这里，多半是数据库数据错误了
          continue;
        }

        $scGoodsCount = $data[0]["goods_count"];
        $scSoCount = $data[0]["so_count"];
        $scSoCount += $goodsCount;
        $scLeftCount = $scGoodsCount - $scSoCount;

        $sql = "update t_sc_bill_detail
                set so_count = convert(%f, $fmt), left_count = convert(%f, $fmt)
                where id = '%s' ";
        $rc = $db->execute($sql, $scSoCount, $scLeftCount, $scbillDetailId);
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
              where ca_id = '%s' and ca_type = 'customer' and company_id = '%s' ";
      $data = $db->query($sql, $customerId, $companyId);
      if ($data) {
        $rvMoney = $data[0]["rv_money"];
        $balanceMoney = $data[0]["balance_money"];

        $rvMoney += $saleMoney;
        $balanceMoney += $saleMoney;

        $sql = "update t_receivables
                set rv_money = %f,  balance_money = %f
                where ca_id = '%s' and ca_type = 'customer'
                  and company_id = '%s' ";
        $rc = $db->execute($sql, $rvMoney, $balanceMoney, $customerId, $companyId);
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      } else {
        $sql = "insert into t_receivables (id, rv_money, act_money, balance_money,
                  ca_id, ca_type, company_id)
                values ('%s', %f, 0, %f, '%s', 'customer', '%s')";
        $rc = $db->execute(
          $sql,
          $this->newId(),
          $saleMoney,
          $saleMoney,
          $customerId,
          $companyId
        );
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      }

      // 应收明细账
      $sql = "insert into t_receivables_detail (id, rv_money, act_money, balance_money,
                ca_id, ca_type, date_created, ref_number, ref_type, biz_date, company_id)
              values('%s', %f, 0, %f, '%s', 'customer', now(), '%s', '销售出库', '%s', '%s')";

      $rc = $db->execute(
        $sql,
        $this->newId(),
        $saleMoney,
        $saleMoney,
        $customerId,
        $ref,
        $bizDT,
        $companyId
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    } else if ($receivingType == 1) {
      // 现金收款
      $inCash = $saleMoney;

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
                values (%f, %f, '%s', '销售出库', '%s', now(), '%s')";
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
        $rc = $db->execute($sql, $sumInMoney, $balanceCash, $bizDT, $companyId);
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }

        // 记现金明细账
        $sql = "insert into t_cash_detail(in_money, balance_money, biz_date, ref_type,
                  ref_number, date_created, company_id)
                values (%f, %f, '%s', '销售出库', '%s', now(), '%s')";
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
    } else if ($receivingType == 2) {
      // 2: 用预收款支付

      $outMoney = $saleMoney;

      // 预收款总账
      $sql = "select out_money, balance_money from t_pre_receiving
              where customer_id = '%s' and company_id = '%s' ";
      $data = $db->query($sql, $customerId, $companyId);
      $totalOutMoney = $data[0]["out_money"];
      if (!$totalOutMoney) {
        $totalOutMoney = 0;
      }
      $totalBalanceMoney = $data[0]["balance_money"];
      if (!$totalBalanceMoney) {
        $totalBalanceMoney = 0;
      }
      if ($totalBalanceMoney < $outMoney) {
        return $this->bad("付余款余额是{$totalBalanceMoney}元，小于销售金额，无法付款");
      }

      $totalOutMoney += $outMoney;
      $totalBalanceMoney -= $outMoney;
      $sql = "update t_pre_receiving
              set out_money = %f, balance_money = %f
              where customer_id = '%s' and company_id = '%s' ";
      $rc = $db->execute($sql, $totalOutMoney, $totalBalanceMoney, $customerId, $companyId);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }

      // 预收款明细账
      $sql = "insert into t_pre_receiving_detail (id, customer_id, out_money, balance_money,
                biz_date, date_created, ref_number, ref_type, biz_user_id, input_user_id, company_id)
              values ('%s', '%s', %f, %f, '%s', now(), '%s', '销售出库', '%s', '%s', '%s')";
      $rc = $db->execute(
        $sql,
        $this->newId(),
        $customerId,
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

    // 把单据本身设置为已经提交出库
    $sql = "select sum(inventory_money) as sum_inventory_money
            from t_ws_bill_detail
            where wsbill_id = '%s' ";
    $data = $db->query($sql, $id);
    $sumInventoryMoney = $data[0]["sum_inventory_money"];
    if (!$sumInventoryMoney) {
      $sumInventoryMoney = 0;
    }

    $profit = $saleMoney - $sumInventoryMoney - $tax;

    // 更新本单据的状态
    $sql = "update t_ws_bill
            set bill_status = 1000, inventory_money = %f, profit = %f
            where id = '%s' ";
    $rc = $db->execute($sql, $sumInventoryMoney, $profit, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 同步销售订单的状态
    $sql = "select so_id
            from t_so_ws
            where ws_id = '%s' ";
    $data = $db->query($sql, $id);
    if ($data) {
      $soBillId = $data[0]["so_id"];
      $sql = "select count(*) as cnt
              from t_so_bill_detail
              where sobill_id = '%s' and  left_count > 0";
      $data = $db->query($sql, $soBillId);
      $cnt = $data[0]["cnt"];
      $billStatus = 1000;
      if ($cnt > 0) {
        // 部分出库
        $billStatus = 2000;
      } else {
        $billStatus = 3000;
      }
      $sql = "update t_so_bill
              set bill_status = %d
              where id = '%s' ";
      $rc = $db->execute($sql, $billStatus, $soBillId);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    $params["ref"] = $ref;

    // 操作成功
    return null;
  }

  /**
   * 通过销售出库单单号查询销售出库单完整数据，包括明细记录
   *
   * @param string $ref
   * @return array|NULL
   */
  public function getFullBillDataByRef($ref)
  {
    $db = $this->db;

    $sql = "select w.id, w.bizdt, c.name as customer_name,
              u.name as biz_user_name,
              h.name as warehouse_name, w.memo, w.company_id
            from t_ws_bill w, t_customer c, t_user u, t_warehouse h
            where w.customer_id = c.id and w.biz_user_id = u.id
              and w.warehouse_id = h.id
              and w.ref = '%s' ";
    $data = $db->query($sql, $ref);
    if (!$data) {
      return NULL;
    }

    $id = $data[0]["id"];
    $companyId = $data[0]["company_id"];

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $result = array(
      "bizDT" => $this->toYMD($data[0]["bizdt"]),
      "customerName" => $data[0]["customer_name"],
      "warehouseName" => $data[0]["warehouse_name"],
      "bizUserName" => $data[0]["biz_user_name"],
      "memo" => $data[0]["memo"]
    );

    // 明细表
    $sql = "select d.id, g.id as goods_id, g.code, g.name, g.spec, u.name as unit_name, 
              convert(d.goods_count, $fmt) as goods_count,
              d.goods_price, d.goods_money, d.sn_note, d.memo,
              d.tax_rate, d.tax, d.money_with_tax
            from t_ws_bill_detail d, t_goods g, t_goods_unit u
            where d.wsbill_id = '%s' and d.goods_id = g.id and g.unit_id = u.id
            order by d.show_order";
    $data = $db->query($sql, $id);
    $items = array();
    foreach ($data as $v) {
      $item = array(
        "id" => $v["id"],
        "goodsId" => $v["goods_id"],
        "goodsCode" => $v["code"],
        "goodsName" => $v["name"],
        "goodsSpec" => $v["spec"],
        "unitName" => $v["unit_name"],
        "goodsCount" => $v["goods_count"],
        "goodsPrice" => $v["goods_price"],
        "goodsMoney" => $v["goods_money"],
        "sn" => $v["sn_note"],
        "memo" => $v["memo"],
        "taxRate" => $v["tax_rate"],
        "tax" => $v["tax"],
        "moneyWithTax" => $v["money_with_tax"]
      );
      $items[] = $item;
    }

    $result["items"] = $items;

    return $result;
  }

  /**
   * 根据销售订单id查询出库情况
   *
   * @param string $soBillId
   *        	销售订单
   * @return array
   */
  public function soBillWSBillList($soBillId)
  {
    $db = $this->db;

    $sql = "select w.id, w.ref, w.bizdt, c.name as customer_name, u.name as biz_user_name,
              user.name as input_user_name, h.name as warehouse_name, w.sale_money,
              w.bill_status, w.date_created, w.receiving_type, w.memo
            from t_ws_bill w, t_customer c, t_user u, t_user user, t_warehouse h,
              t_so_ws s
            where (w.customer_id = c.id) and (w.biz_user_id = u.id)
              and (w.input_user_id = user.id) and (w.warehouse_id = h.id) 
              and (w.id = s.ws_id) and (s.so_id = '%s')";

    $data = $db->query($sql, $soBillId);
    $result = array();

    foreach ($data as $v) {
      $item = array(
        "id" => $v["id"],
        "ref" => $v["ref"],
        "bizDate" => $this->toYMD($v["bizdt"]),
        "customerName" => $v["customer_name"],
        "warehouseName" => $v["warehouse_name"],
        "inputUserName" => $v["input_user_name"],
        "bizUserName" => $v["biz_user_name"],
        "billStatus" => $this->billStatusCodeToName($v["bill_status"]),
        "amount" => $v["sale_money"],
        "dateCreated" => $v["date_created"],
        "receivingType" => $v["receiving_type"],
        "memo" => $v["memo"]
      );

      $result[] = $item;
    }

    return $result;
  }

  /**
   * 获得打印销售出库单的数据
   *
   * @param array $params
   * @return array
   */
  public function getWSBillDataForLodopPrint($params)
  {
    $db = $this->db;

    $id = $params["id"];

    $sql = "select w.ref, w.bizdt, c.name as customer_name,
              u.name as biz_user_name,
              h.name as warehouse_name,
              w.sale_money, w.memo, w.deal_address, w.company_id,
              w.money_with_tax
            from t_ws_bill w, t_customer c, t_user u, t_warehouse h
            where w.customer_id = c.id and w.biz_user_id = u.id
              and w.warehouse_id = h.id
              and w.id = '%s' ";
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
    $bill["warehouseName"] = $data[0]["warehouse_name"];
    $bill["bizUserName"] = $data[0]["biz_user_name"];
    $bill["saleMoney"] = $data[0]["sale_money"];
    $bill["billMemo"] = $data[0]["memo"];
    $bill["dealAddress"] = $data[0]["deal_address"];
    $bill["moneyWithTax"] = $data[0]["money_with_tax"];

    $bill["printDT"] = date("Y-m-d H:i:s");

    // 明细表
    $sql = "select g.code, g.name, g.spec, u.name as unit_name,
              convert(d.goods_count, $fmt) as goods_count,
              d.goods_price, d.goods_money, d.sn_note, d.tax_rate, d.money_with_tax
            from t_ws_bill_detail d, t_goods g, t_goods_unit u
            where d.wsbill_id = '%s' and d.goods_id = g.id and g.unit_id = u.id
            order by d.show_order";
    $data = $db->query($sql, $id);
    $items = [];
    foreach ($data as $v) {
      $items[] = [
        "goodsCode" => $v["code"],
        "goodsName" => $v["name"],
        "goodsSpec" => $v["spec"],
        "unitName" => $v["unit_name"],
        "goodsCount" => $v["goods_count"],
        "goodsPrice" => $v["goods_price"],
        "goodsMoney" => $v["goods_money"],
        "sn" => $v["sn_note"],
        "taxRate" => intval($v["tax_rate"]),
        "moneyWithTax" => $v["money_with_tax"]
      ];
    }
    $bill["items"] = $items;

    return $bill;
  }
}
