<?php

namespace Home\DAO;

use Home\Common\FIdConst;

/**
 * 盘点单 DAO
 *
 * @author 李静波
 */
class ICBillDAO extends PSIBaseExDAO
{

  /**
   * 生成新的盘点单单号
   *
   * @param string $companyId
   *
   * @return string
   */
  private function genNewBillRef($companyId)
  {
    $db = $this->db;

    $bs = new BizConfigDAO($db);
    $pre = $bs->getICBillRefPre($companyId);

    $mid = date("Ymd");

    $sql = "select ref from t_ic_bill where ref like '%s' order by ref desc limit 1";
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
   * 盘点单列表
   *
   * @param array $params
   * @return array
   */
  public function icbillList($params)
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
    $goodsId = $params["goodsId"];

    $sql = "select t.id, t.ref, t.bizdt, t.bill_status,
              w.name as warehouse_name,
              u.name as biz_user_name,
              u1.name as input_user_name,
              t.date_created, t.bill_memo
            from t_ic_bill t, t_warehouse w, t_user u, t_user u1
            where (t.warehouse_id = w.id)
              and (t.biz_user_id = u.id)
              and (t.input_user_id = u1.id) ";
    $queryParams = [];

    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::INVENTORY_CHECK, "t", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = $rs[1];
    }

    if ($billStatus != -1) {
      $sql .= " and (t.bill_status = %d) ";
      $queryParams[] = $billStatus;
    }
    if ($ref) {
      $sql .= " and (t.ref like '%s') ";
      $queryParams[] = "%{$ref}%";
    }
    if ($fromDT) {
      $sql .= " and (t.bizdt >= '%s') ";
      $queryParams[] = $fromDT;
    }
    if ($toDT) {
      $sql .= " and (t.bizdt <= '%s') ";
      $queryParams[] = $toDT;
    }
    if ($warehouseId) {
      $sql .= " and (t.warehouse_id = '%s') ";
      $queryParams[] = $warehouseId;
    }
    if ($goodsId) {
      $sql .= " and (t.id in (select distinct icbill_id from t_ic_bill_detail where goods_id = '%s')) ";
      $queryParams[] = $goodsId;
    }

    $sql .= " order by t.bizdt desc, t.ref desc
              limit %d , %d ";
    $queryParams[] = $start;
    $queryParams[] = $limit;
    $data = $db->query($sql, $queryParams);
    $result = [];
    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "ref" => $v["ref"],
        "bizDate" => $this->toYMD($v["bizdt"]),
        "billStatus" => $v["bill_status"] == 0 ? "待盘点" : "已盘点",
        "warehouseName" => $v["warehouse_name"],
        "bizUserName" => $v["biz_user_name"],
        "inputUserName" => $v["input_user_name"],
        "dateCreated" => $v["date_created"],
        "billMemo" => $v["bill_memo"]
      ];
    }

    $sql = "select count(*) as cnt
            from t_ic_bill t, t_warehouse w, t_user u, t_user u1
            where (t.warehouse_id = w.id)
              and (t.biz_user_id = u.id)
              and (t.input_user_id = u1.id)
				";
    $queryParams = [];

    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::INVENTORY_CHECK, "t", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = $rs[1];
    }

    if ($billStatus != -1) {
      $sql .= " and (t.bill_status = %d) ";
      $queryParams[] = $billStatus;
    }
    if ($ref) {
      $sql .= " and (t.ref like '%s') ";
      $queryParams[] = "%{$ref}%";
    }
    if ($fromDT) {
      $sql .= " and (t.bizdt >= '%s') ";
      $queryParams[] = $fromDT;
    }
    if ($toDT) {
      $sql .= " and (t.bizdt <= '%s') ";
      $queryParams[] = $toDT;
    }
    if ($warehouseId) {
      $sql .= " and (t.warehouse_id = '%s') ";
      $queryParams[] = $warehouseId;
    }
    if ($goodsId) {
      $sql .= " and (t.id in (select distinct icbill_id from t_ic_bill_detail where goods_id = '%s')) ";
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
   * 盘点单明细记录
   *
   * @param array $params
   * @return array
   */
  public function icBillDetailList($params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    // id:盘点单id
    $id = $params["id"];

    $result = [];

    $sql = "select t.id, g.code, g.name, g.spec, u.name as unit_name, 
              convert(t.goods_count, $fmt) as goods_count, t.goods_money, t.memo
            from t_ic_bill_detail t, t_goods g, t_goods_unit u
            where t.icbill_id = '%s' and t.goods_id = g.id and g.unit_id = u.id
            order by t.show_order ";

    $data = $db->query($sql, $id);
    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "goodsCode" => $v["code"],
        "goodsName" => $v["name"],
        "goodsSpec" => $v["spec"],
        "unitName" => $v["unit_name"],
        "goodsCount" => $v["goods_count"],
        "goodsMoney" => $v["goods_money"],
        "memo" => $v["memo"]
      ];
    }

    return $result;
  }

  /**
   * 新建盘点单
   *
   * @param array $bill
   * @return NULL|array
   */
  public function addICBill(&$bill)
  {
    $db = $this->db;

    $bizDT = $bill["bizDT"];
    $warehouseId = $bill["warehouseId"];

    $warehouseDAO = new WarehouseDAO($db);
    $warehouse = $warehouseDAO->getWarehouseById($warehouseId);
    if (!$warehouse) {
      return $this->bad("盘点仓库不存在，无法保存");
    }

    $bizUserId = $bill["bizUserId"];
    $userDAO = new UserDAO($db);
    $user = $userDAO->getUserById($bizUserId);
    if (!$user) {
      return $this->bad("业务人员不存在，无法保存");
    }

    // 检查业务日期
    if (!$this->dateIsValid($bizDT)) {
      return $this->bad("业务日期不正确");
    }

    $billMemo = $bill["billMemo"];

    $items = $bill["items"];

    $dataOrg = $bill["dataOrg"];
    if ($this->dataOrgNotExists($dataOrg)) {
      return $this->badParam("dataOrg");
    }
    $loginUserId = $bill["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->badParam("loginUserId");
    }
    $companyId = $bill["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->badParam("companyId");
    }

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $id = $this->newId();
    $ref = $this->genNewBillRef($companyId);

    // 主表
    $sql = "insert into t_ic_bill(id, bill_status, bizdt, biz_user_id, date_created,
              input_user_id, ref, warehouse_id, data_org, company_id, bill_memo)
            values ('%s', 0, '%s', '%s', now(), '%s', '%s', '%s', '%s', '%s', '%s')";
    $rc = $db->execute(
      $sql,
      $id,
      $bizDT,
      $bizUserId,
      $loginUserId,
      $ref,
      $warehouseId,
      $dataOrg,
      $companyId,
      $billMemo
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 明细表
    $sql = "insert into t_ic_bill_detail(id, date_created, goods_id, goods_count, goods_money,
              show_order, icbill_id, data_org, company_id, memo)
            values ('%s', now(), '%s', convert(%f, $fmt), %f, %d, '%s', '%s', '%s', '%s')";
    foreach ($items as $i => $v) {
      $goodsId = $v["goodsId"];
      if (!$goodsId) {
        continue;
      }
      $goodsCount = $v["goodsCount"];
      $goodsMoney = $v["goodsMoney"];
      $memo = $v["memo"];

      $rc = $db->execute(
        $sql,
        $this->newId(),
        $goodsId,
        $goodsCount,
        $goodsMoney,
        $i,
        $id,
        $dataOrg,
        $companyId,
        $memo
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    $bill["id"] = $id;
    $bill["ref"] = $ref;

    // 操作成功
    return null;
  }

  /**
   * 通过盘点单id查询盘点单
   *
   * @param string $id
   * @return array|NULL
   */
  public function getICBillById($id)
  {
    $db = $this->db;
    $sql = "select ref, bill_status, data_org, company_id from t_ic_bill where id = '%s' ";
    $data = $db->query($sql, $id);
    if ($data) {
      return array(
        "ref" => $data[0]["ref"],
        "billStatus" => $data[0]["bill_status"],
        "dataOrg" => $data[0]["data_org"],
        "companyId" => $data[0]["company_id"]
      );
    } else {
      return null;
    }
  }

  /**
   * 编辑盘点单
   *
   * @param array $bill
   * @return NULL|array
   */
  public function updateICBill(&$bill)
  {
    $db = $this->db;

    $id = $bill["id"];

    $bizDT = $bill["bizDT"];
    $warehouseId = $bill["warehouseId"];

    $warehouseDAO = new WarehouseDAO($db);
    $warehouse = $warehouseDAO->getWarehouseById($warehouseId);
    if (!$warehouse) {
      return $this->bad("盘点仓库不存在，无法保存");
    }

    $bizUserId = $bill["bizUserId"];
    $userDAO = new UserDAO($db);
    $user = $userDAO->getUserById($bizUserId);
    if (!$user) {
      return $this->bad("业务人员不存在，无法保存");
    }

    // 检查业务日期
    if (!$this->dateIsValid($bizDT)) {
      return $this->bad("业务日期不正确");
    }

    $billMemo = $bill["billMemo"];

    $items = $bill["items"];

    $loginUserId = $bill["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->badParam("loginUserId");
    }

    $companyId = $bill["companyId"];
    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $oldBill = $this->getICBillById($id);
    if (!$oldBill) {
      return $this->bad("要编辑的盘点单不存在，无法保存");
    }

    $ref = $oldBill["ref"];
    $dataOrg = $oldBill["dataOrg"];
    $companyId = $oldBill["companyId"];
    $billStatus = $oldBill["billStatus"];
    if ($billStatus != 0) {
      return $this->bad("盘点单(单号：$ref)已经提交，不能再编辑");
    }

    // 主表
    $sql = "update t_ic_bill
            set bizdt = '%s', biz_user_id = '%s', date_created = now(),
              input_user_id = '%s', warehouse_id = '%s', bill_memo = '%s'
            where id = '%s' ";
    $rc = $db->execute($sql, $bizDT, $bizUserId, $loginUserId, $warehouseId, $billMemo, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 明细表
    $sql = "delete from t_ic_bill_detail where icbill_id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $sql = "insert into t_ic_bill_detail(id, date_created, goods_id, goods_count, goods_money,
              show_order, icbill_id, data_org, company_id, memo)
            values ('%s', now(), '%s', convert(%f, $fmt), %f, %d, '%s', '%s', '%s', '%s')";
    foreach ($items as $i => $v) {
      $goodsId = $v["goodsId"];
      if (!$goodsId) {
        continue;
      }
      $goodsCount = $v["goodsCount"];
      $goodsMoney = $v["goodsMoney"];
      $memo = $v["memo"];

      $rc = $db->execute(
        $sql,
        $this->newId(),
        $goodsId,
        $goodsCount,
        $goodsMoney,
        $i,
        $id,
        $dataOrg,
        $companyId,
        $memo
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    $bill["ref"] = $ref;

    // 操作成功
    return null;
  }

  /**
   * 获得某个盘点单的详情
   *
   * @param array $params
   * @return array
   */
  public function icBillInfo($params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    // id:盘点单id
    $id = $params["id"];

    $result = [];

    if ($id) {
      // 编辑
      $sql = "select t.ref, t.bill_status, t.bizdt, t.biz_user_id, u.name as biz_user_name,
                w.id as warehouse_id, w.name as warehouse_name, t.bill_memo
              from t_ic_bill t, t_user u, t_warehouse w
              where t.id = '%s' and t.biz_user_id = u.id
                and t.warehouse_id = w.id";
      $data = $db->query($sql, $id);
      if (!$data) {
        return $result;
      }

      $result["bizUserId"] = $data[0]["biz_user_id"];
      $result["bizUserName"] = $data[0]["biz_user_name"];
      $result["ref"] = $data[0]["ref"];
      $result["billStatus"] = $data[0]["bill_status"];
      $result["bizDT"] = date("Y-m-d", strtotime($data[0]["bizdt"]));
      $result["warehouseId"] = $data[0]["warehouse_id"];
      $result["warehouseName"] = $data[0]["warehouse_name"];
      $result["billMemo"] = $data[0]["bill_memo"];

      $items = [];
      $sql = "select t.id, g.id as goods_id, g.code, g.name, g.spec, u.name as unit_name,
                convert(t.goods_count, $fmt) as goods_count, t.goods_money, t.memo
              from t_ic_bill_detail t, t_goods g, t_goods_unit u
              where t.icbill_id = '%s' and t.goods_id = g.id and g.unit_id = u.id
              order by t.show_order ";

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
          "goodsMoney" => $v["goods_money"],
          "memo" => $v["memo"]
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
   * 删除盘点单
   *
   * @param array $params
   * @return NULL|array
   */
  public function deleteICBill(&$params)
  {
    $db = $this->db;

    $id = $params["id"];

    $bill = $this->getICBillById($id);

    if (!$bill) {
      return $this->bad("要删除的盘点单不存在");
    }

    $ref = $bill["ref"];
    $billStatus = $bill["billStatus"];

    if ($billStatus != 0) {
      return $this->bad("盘点单(单号：$ref)已经提交，不能被删除");
    }

    $sql = "delete from t_ic_bill_detail where icbill_id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $sql = "delete from t_ic_bill where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $params["ref"] = $ref;

    // 操作成功
    return null;
  }

  /**
   * 提交盘点单
   *
   * @param array $params
   * @return NULL|array
   */
  public function commitICBill(&$params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->badParam("companyId");
    }

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $id = $params["id"];

    $sql = "select ref, bill_status, warehouse_id, bizdt, biz_user_id
            from t_ic_bill
            where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return $this->bad("要提交的盘点单不存在");
    }
    $ref = $data[0]["ref"];
    $billStatus = $data[0]["bill_status"];
    if ($billStatus != 0) {
      return $this->bad("盘点单(单号：$ref)已经提交，不能再次提交");
    }
    $warehouseId = $data[0]["warehouse_id"];
    $bizDT = date("Y-m-d", strtotime($data[0]["bizdt"]));
    $bizUserId = $data[0]["biz_user_id"];

    $warehouseDAO = new WarehouseDAO($db);
    $warehouse = $warehouseDAO->getWarehouseById($warehouseId);
    if (!$warehouse) {
      return $this->bad("要盘点的仓库不存在");
    }
    $inited = $warehouse["inited"];
    $warehouseName = $warehouse["name"];
    if ($inited != 1) {
      return $this->bad("仓库[$warehouseName]还没有建账，无法做盘点操作");
    }

    $userDAO = new UserDAO($db);
    $user = $userDAO->getUserById($bizUserId);
    if (!$user) {
      return $this->bad("业务人员不存在，无法完成提交");
    }

    $sql = "select goods_id, convert(goods_count, $fmt) as goods_count, goods_money
            from t_ic_bill_detail
            where icbill_id = '%s'
            order by show_order ";
    $items = $db->query($sql, $id);
    if (!$items) {
      return $this->bad("盘点单没有明细信息，无法完成提交");
    }

    foreach ($items as $i => $v) {
      $goodsId = $v["goods_id"];
      $goodsCount = $v["goods_count"];
      $goodsMoney = $v["goods_money"];

      // 检查商品是否存在
      $sql = "select code, name, spec from t_goods where id = '%s' ";
      $data = $db->query($sql, $goodsId);
      if (!$data) {
        $index = $i + 1;
        return $this->bad("第{$index}条记录的商品不存在，无法完成提交");
      }

      if ($goodsCount < 0) {
        $index = $i + 1;
        return $this->bad("第{$index}条记录的商品盘点后库存数量不能为负数");
      }
      if ($goodsMoney < 0) {
        $index = $i + 1;
        return $this->bad("第{$index}条记录的商品盘点后库存金额不能为负数");
      }
      if ($goodsCount == 0) {
        if ($goodsMoney != 0) {
          $index = $i + 1;
          return $this->bad("第{$index}条记录的商品盘点后库存数量为0的时候，库存金额也必须为0");
        }
      }

      $sql = "select convert(balance_count, $fmt) as balance_count, balance_money, 
                convert(in_count, $fmt) as in_count, in_money, 
                convert(out_count, $fmt) as out_count, out_money
              from t_inventory
              where warehouse_id = '%s' and goods_id = '%s' ";
      $data = $db->query($sql, $warehouseId, $goodsId);
      if (!$data) {
        // 这种情况是：没有库存，做盘盈入库
        $inCount = $goodsCount;
        $inMoney = $goodsMoney;
        $inPrice = 0;
        if ($inCount != 0) {
          $inPrice = $inMoney / $inCount;
        }

        // 库存总账
        $sql = "insert into t_inventory(in_count, in_price, in_money, balance_count, balance_price,
                  balance_money, warehouse_id, goods_id)
                values (convert(%f, $fmt), %f, %f, convert(%f, $fmt), %f, %f, '%s', '%s')";
        $rc = $db->execute(
          $sql,
          $inCount,
          $inPrice,
          $inMoney,
          $inCount,
          $inPrice,
          $inMoney,
          $warehouseId,
          $goodsId
        );
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }

        // 库存明细账
        $sql = "insert into t_inventory_detail(in_count, in_price, in_money, balance_count, balance_price,
                balance_money, warehouse_id, goods_id, biz_date, biz_user_id, date_created, ref_number,
                ref_type)
              values (convert(%f, $fmt), %f, %f, convert(%f, $fmt), %f, %f, '%s', '%s', '%s', '%s', now(), '%s', '库存盘点-盘盈入库')";
        $rc = $db->execute(
          $sql,
          $inCount,
          $inPrice,
          $inMoney,
          $inCount,
          $inPrice,
          $inMoney,
          $warehouseId,
          $goodsId,
          $bizDT,
          $bizUserId,
          $ref
        );
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      } else {
        $balanceCount = $data[0]["balance_count"];
        $balanceMoney = $data[0]["balance_money"];

        if ($goodsCount > $balanceCount) {
          // 盘盈入库
          $inCount = $goodsCount - $balanceCount;
          $inMoney = $goodsMoney - $balanceMoney;
          $inPrice = $inMoney / $inCount;
          $balanceCount = $goodsCount;
          $balanceMoney = $goodsMoney;
          $balancePrice = $balanceMoney / $balanceCount;
          $totalInCount = $data[0]["in_count"] + $inCount;
          $totalInMoney = $data[0]["in_money"] + $inMoney;
          $totalInPrice = $totalInMoney / $totalInCount;

          // 库存总账
          $sql = "update t_inventory
                  set in_count = convert(%f, $fmt), in_price = %f, in_money = %f,
                    balance_count = convert(%f, $fmt), balance_price = %f,
                    balance_money = %f
                  where warehouse_id = '%s' and goods_id = '%s' ";
          $rc = $db->execute(
            $sql,
            $totalInCount,
            $totalInPrice,
            $totalInMoney,
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
          $sql = "insert into t_inventory_detail(in_count, in_price, in_money, balance_count, balance_price,
                    balance_money, warehouse_id, goods_id, biz_date, biz_user_id, date_created, ref_number,
                    ref_type)
                  values (convert(%f, $fmt), %f, %f, convert(%f, $fmt), %f, %f, '%s', '%s', '%s', '%s', now(), '%s', '库存盘点-盘盈入库')";
          $rc = $db->execute(
            $sql,
            $inCount,
            $inPrice,
            $inMoney,
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
        } else {
          // 盘亏出库
          $outCount = $balanceCount - $goodsCount;
          $outMoney = $balanceMoney - $goodsMoney;
          $outPrice = 0;
          if ($outCount != 0) {
            $outPrice = $outMoney / $outCount;
          }
          $balanceCount = $goodsCount;
          $balanceMoney = $goodsMoney;
          $balancePrice = 0;
          if ($balanceCount != 0) {
            $balancePrice = $balanceMoney / $balanceCount;
          }

          $totalOutCount = $data[0]["out_count"] + $outCount;
          $totalOutMoney = $data[0]["out_money"] + $outMoney;
          $totalOutPrice = $totalOutMoney / $totalOutCount;

          // 库存总账
          $sql = "update t_inventory
                  set out_count = convert(%f, $fmt), out_price = %f, out_money = %f,
                    balance_count = convert(%f, $fmt), balance_price = %f,
                    balance_money = %f
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
          $sql = "insert into t_inventory_detail(out_count, out_price, out_money, balance_count, balance_price,
                    balance_money, warehouse_id, goods_id, biz_date, biz_user_id, date_created, ref_number,
                    ref_type)
                  values (convert(%f, $fmt), %f, %f, convert(%f, $fmt), %f, %f, '%s', '%s', '%s', '%s', now(), '%s', '库存盘点-盘亏出库')";
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
    }

    // 修改单据本身状态
    $sql = "update t_ic_bill
            set bill_status = 1000
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
   * 盘点单生成pdf文件
   *
   * @param array $params
   * @return array
   */
  public function getDataForPDF($params)
  {
    $ref = $params["ref"];

    $db = $this->db;
    $sql = "select t.id, t.bizdt, t.bill_status,
              w.name as warehouse_name,
              u.name as biz_user_name,
              u1.name as input_user_name,
            t.date_created, t.bill_memo, t.company_id
            from t_ic_bill t, t_warehouse w, t_user u, t_user u1
            where (t.warehouse_id = w.id)
              and (t.biz_user_id = u.id)
              and (t.input_user_id = u1.id) 
              and (t.ref = '%s')";
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
    $bill["warehouseName"] = $data[0]["warehouse_name"];
    $bill["bizUserName"] = $data[0]["biz_user_name"];
    $bill["billMemo"] = $data[0]["bill_memo"];

    // 明细表
    $sql = "select t.id, g.code, g.name, g.spec, u.name as unit_name, 
              convert(t.goods_count, $fmt) as goods_count, t.goods_money,
              t.memo
            from t_ic_bill_detail t, t_goods g, t_goods_unit u
            where t.icbill_id = '%s' and t.goods_id = g.id and g.unit_id = u.id
            order by t.show_order ";
    $data = $db->query($sql, $id);
    $items = array();
    foreach ($data as $v) {
      $items[] = [
        "goodsCode" => $v["code"],
        "goodsName" => $v["name"],
        "goodsSpec" => $v["spec"],
        "unitName" => $v["unit_name"],
        "goodsCount" => $v["goods_count"],
        "goodsMoney" => $v["goods_money"],
        "memo" => $v["memo"]
      ];
    }
    $bill["items"] = $items;

    return $bill;
  }

  /**
   * 根据盘点单单号查询盘点单的完整数据，包括明细记录
   *
   * @param string $ref
   *        	盘点单单号
   * @return array|NULL
   */
  public function getFullBillDataByRef($ref)
  {
    $db = $this->db;
    $sql = "select t.id, t.bizdt, u.name as biz_user_name,
              w.name as warehouse_name, t.company_id
            from t_ic_bill t, t_user u, t_warehouse w
            where t.ref = '%s' and t.biz_user_id = u.id
              and t.warehouse_id = w.id";
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
      "bizDT" => $this->toYMD($data[0]["bizdt"]),
      "warehouseName" => $data[0]["warehouse_name"]
    ];

    $items = [];
    $sql = "select t.id, g.id as goods_id, g.code, g.name, g.spec, u.name as unit_name,
              convert(t.goods_count, $fmt) as goods_count, t.goods_money
            from t_ic_bill_detail t, t_goods g, t_goods_unit u
            where t.icbill_id = '%s' and t.goods_id = g.id and g.unit_id = u.id
            order by t.show_order ";

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
        "goodsMoney" => $v["goods_money"]
      ];
    }

    $result["items"] = $items;

    return $result;
  }

  /**
   * 生成打印盘点单的数据
   *
   * @param array $params
   */
  public function getICBillDataForLodopPrint($params)
  {
    $db = $this->db;

    $id = $params["id"];

    $sql = "select t.ref, t.bizdt, t.bill_status,
              w.name as warehouse_name,
              u.name as biz_user_name,
              u1.name as input_user_name,
              t.date_created, t.bill_memo, t.company_id
            from t_ic_bill t, t_warehouse w, t_user u, t_user u1
            where (t.warehouse_id = w.id)
              and (t.biz_user_id = u.id)
              and (t.input_user_id = u1.id)
              and (t.id = '%s')";
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
    $bill["warehouseName"] = $data[0]["warehouse_name"];
    $bill["bizUserName"] = $data[0]["biz_user_name"];
    $bill["billMemo"] = $data[0]["bill_memo"];

    $bill["printDT"] = date("Y-m-d H:i:s");

    // 明细表
    $sql = "select t.id, g.code, g.name, g.spec, u.name as unit_name,
              convert(t.goods_count, $fmt) as goods_count, t.goods_money,
              t.memo
            from t_ic_bill_detail t, t_goods g, t_goods_unit u
            where t.icbill_id = '%s' and t.goods_id = g.id and g.unit_id = u.id
            order by t.show_order ";
    $data = $db->query($sql, $id);
    $items = array();
    foreach ($data as $v) {
      $items[] = [
        "goodsCode" => $v["code"],
        "goodsName" => $v["name"],
        "goodsSpec" => $v["spec"],
        "unitName" => $v["unit_name"],
        "goodsCount" => $v["goods_count"],
        "goodsMoney" => $v["goods_money"],
        "memo" => $v["memo"]
      ];
    }
    $bill["items"] = $items;

    return $bill;
  }
}
