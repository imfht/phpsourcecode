<?php

namespace Home\DAO;

use Home\Common\FIdConst;

/**
 * 库间调拨 DAO
 *
 * @author 李静波
 */
class ITBillDAO extends PSIBaseExDAO
{

  /**
   * 生成新的调拨单单号
   *
   * @param string $companyId
   *
   * @return string
   */
  private function genNewBillRef($companyId)
  {
    $db = $this->db;

    $bs = new BizConfigDAO($db);
    $pre = $bs->getITBillRefPre($companyId);

    $mid = date("Ymd");

    $sql = "select ref from t_it_bill where ref like '%s' order by ref desc limit 1";
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
   * 调拨单主表列表信息
   *
   * @param array $params
   * @return array
   */
  public function itbillList($params)
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
    $fromWarehouseId = $params["fromWarehouseId"];
    $toWarehouseId = $params["toWarehouseId"];
    $goodsId = $params["goodsId"];

    $sql = "select t.id, t.ref, t.bizdt, t.bill_status,
					fw.name as from_warehouse_name,
					tw.name as to_warehouse_name,
					u.name as biz_user_name,
					u1.name as input_user_name,
					t.date_created, t.bill_memo
				from t_it_bill t, t_warehouse fw, t_warehouse tw,
				   t_user u, t_user u1
				where (t.from_warehouse_id = fw.id)
				  and (t.to_warehouse_id = tw.id)
				  and (t.biz_user_id = u.id)
				  and (t.input_user_id = u1.id) ";
    $queryParams = [];

    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::INVENTORY_TRANSFER, "t", $loginUserId);
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
    if ($fromWarehouseId) {
      $sql .= " and (t.from_warehouse_id = '%s') ";
      $queryParams[] = $fromWarehouseId;
    }
    if ($toWarehouseId) {
      $sql .= " and (t.to_warehouse_id = '%s') ";
      $queryParams[] = $toWarehouseId;
    }
    if ($goodsId) {
      $sql .= " and (t.id in (select distinct itbill_id from t_it_bill_detail where goods_id = '%s')) ";
      $queryParams[] = $goodsId;
    }

    $sql .= " order by t.bizdt desc, t.ref desc
				limit %d , %d
				";
    $queryParams[] = $start;
    $queryParams[] = $limit;
    $data = $db->query($sql, $queryParams);
    $result = [];
    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "ref" => $v["ref"],
        "bizDate" => $this->toYMD($v["bizdt"]),
        "billStatus" => $v["bill_status"] == 0 ? "待调拨" : "已调拨",
        "fromWarehouseName" => $v["from_warehouse_name"],
        "toWarehouseName" => $v["to_warehouse_name"],
        "bizUserName" => $v["biz_user_name"],
        "inputUserName" => $v["input_user_name"],
        "dateCreated" => $v["date_created"],
        "billMemo" => $v["bill_memo"]
      ];
    }

    $sql = "select count(*) as cnt
				from t_it_bill t, t_warehouse fw, t_warehouse tw,
				   t_user u, t_user u1
				where (t.from_warehouse_id = fw.id)
				  and (t.to_warehouse_id = tw.id)
				  and (t.biz_user_id = u.id)
				  and (t.input_user_id = u1.id)
				";
    $queryParams = [];

    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::INVENTORY_TRANSFER, "t", $loginUserId);
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
    if ($fromWarehouseId) {
      $sql .= " and (t.from_warehouse_id = '%s') ";
      $queryParams[] = $fromWarehouseId;
    }
    if ($toWarehouseId) {
      $sql .= " and (t.to_warehouse_id = '%s') ";
      $queryParams[] = $toWarehouseId;
    }
    if ($goodsId) {
      $sql .= " and (t.id in (select distinct itbill_id from t_it_bill_detail where goods_id = '%s')) ";
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
   * 调拨单的明细记录
   *
   * @param array $params
   * @return array
   */
  public function itBillDetailList($params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    // id: 调拨单id
    $id = $params["id"];

    $result = [];

    $sql = "select t.id, g.code, g.name, g.spec, u.name as unit_name, convert(t.goods_count, $fmt) as goods_count,
					t.memo
				from t_it_bill_detail t, t_goods g, t_goods_unit u
				where t.itbill_id = '%s' and t.goods_id = g.id and g.unit_id = u.id
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
        "memo" => $v["memo"]
      ];
    }

    return $result;
  }

  /**
   * 新建调拨单
   *
   * @param array $bill
   * @return NULL|array
   */
  public function addITBill(&$bill)
  {
    $db = $this->db;

    $bizDT = $bill["bizDT"];
    $fromWarehouseId = $bill["fromWarehouseId"];

    $warehouseDAO = new WarehouseDAO($db);
    $fromWarehouse = $warehouseDAO->getWarehouseById($fromWarehouseId);
    if (!$fromWarehouse) {
      return $this->bad("调出仓库不存在，无法保存");
    }

    $toWarehouseId = $bill["toWarehouseId"];
    $toWarehouse = $warehouseDAO->getWarehouseById($toWarehouseId);
    if (!$toWarehouse) {
      return $this->bad("调入仓库不存在，无法保存");
    }

    $bizUserId = $bill["bizUserId"];
    $userDAO = new UserDAO($db);
    $user = $userDAO->getUserById($bizUserId);
    if (!$user) {
      return $this->bad("业务人员不存在，无法保存");
    }

    if ($fromWarehouseId == $toWarehouseId) {
      return $this->bad("调出仓库和调入仓库不能是同一个仓库");
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

    // 新增
    $sql = "insert into t_it_bill(id, bill_status, bizdt, biz_user_id,
					date_created, input_user_id, ref, from_warehouse_id,
					to_warehouse_id, data_org, company_id, bill_memo)
				values ('%s', 0, '%s', '%s', now(), '%s', '%s', '%s', '%s', '%s', '%s', '%s')";
    $id = $this->newId();
    $ref = $this->genNewBillRef($companyId);

    $rc = $db->execute(
      $sql,
      $id,
      $bizDT,
      $bizUserId,
      $loginUserId,
      $ref,
      $fromWarehouseId,
      $toWarehouseId,
      $dataOrg,
      $companyId,
      $billMemo
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $sql = "insert into t_it_bill_detail(id, date_created, goods_id, goods_count,
					show_order, itbill_id, data_org, company_id, memo)
				values ('%s', now(), '%s', convert(%f, $fmt), %d, '%s', '%s', '%s', '%s')";
    foreach ($items as $i => $v) {
      $goodsId = $v["goodsId"];
      if (!$goodsId) {
        continue;
      }

      $goodsCount = $v["goodsCount"];

      $memo = $v["memo"];

      $rc = $db->execute(
        $sql,
        $this->newId(),
        $goodsId,
        $goodsCount,
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

  public function getITBillById($id)
  {
    $db = $this->db;

    $sql = "select ref, bill_status, data_org, company_id from t_it_bill where id = '%s' ";
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
   * 编辑调拨单
   *
   * @param array $bill
   * @return NULL|array
   */
  public function updateITBill(&$bill)
  {
    $db = $this->db;

    $id = $bill["id"];

    $oldBill = $this->getITBillById($id);
    if (!$oldBill) {
      return $this->bad("要编辑的调拨单不存在");
    }
    $ref = $oldBill["ref"];
    $dataOrg = $oldBill["dataOrg"];
    $companyId = $oldBill["companyId"];
    $billStatus = $oldBill["billStatus"];
    if ($billStatus != 0) {
      return $this->bad("调拨单(单号：$ref)已经提交，不能被编辑");
    }

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $bizDT = $bill["bizDT"];
    $fromWarehouseId = $bill["fromWarehouseId"];

    $warehouseDAO = new WarehouseDAO($db);
    $fromWarehouse = $warehouseDAO->getWarehouseById($fromWarehouseId);
    if (!$fromWarehouse) {
      return $this->bad("调出仓库不存在，无法保存");
    }

    $toWarehouseId = $bill["toWarehouseId"];
    $toWarehouse = $warehouseDAO->getWarehouseById($toWarehouseId);
    if (!$toWarehouse) {
      return $this->bad("调入仓库不存在，无法保存");
    }

    $bizUserId = $bill["bizUserId"];
    $userDAO = new UserDAO($db);
    $user = $userDAO->getUserById($bizUserId);
    if (!$user) {
      return $this->bad("业务人员不存在，无法保存");
    }

    if ($fromWarehouseId == $toWarehouseId) {
      return $this->bad("调出仓库和调入仓库不能是同一个仓库");
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

    $sql = "update t_it_bill
				set bizdt = '%s', biz_user_id = '%s', date_created = now(),
				    input_user_id = '%s', from_warehouse_id = '%s', to_warehouse_id = '%s',
					bill_memo = '%s'
				where id = '%s' ";

    $rc = $db->execute(
      $sql,
      $bizDT,
      $bizUserId,
      $loginUserId,
      $fromWarehouseId,
      $toWarehouseId,
      $billMemo,
      $id
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 明细记录
    $sql = "delete from t_it_bill_detail where itbill_id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $sql = "insert into t_it_bill_detail(id, date_created, goods_id, goods_count,
					show_order, itbill_id, data_org, company_id, memo)
				values ('%s', now(), '%s', convert(%f, $fmt), %d, '%s', '%s', '%s', '%s')";
    foreach ($items as $i => $v) {
      $goodsId = $v["goodsId"];
      if (!$goodsId) {
        continue;
      }

      $goodsCount = $v["goodsCount"];

      $memo = $v["memo"];

      $rc = $db->execute(
        $sql,
        $this->newId(),
        $goodsId,
        $goodsCount,
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
   * 删除调拨单
   *
   * @param array $params
   * @return NULL|array
   */
  public function deleteITBill(&$params)
  {
    $db = $this->db;

    $id = $params["id"];

    $bill = $this->getITBillById($id);
    if (!$bill) {
      return $this->bad("要删除的调拨单不存在");
    }

    $ref = $bill["ref"];
    $billStatus = $bill["billStatus"];

    if ($billStatus != 0) {
      return $this->bad("调拨单(单号：$ref)已经提交，不能被删除");
    }

    $sql = "delete from t_it_bill_detail where itbill_id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $sql = "delete from t_it_bill where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $params["ref"] = $ref;

    // 操作成功
    return null;
  }

  /**
   * 提交调拨单
   *
   * @param array $params
   * @return NULL|array
   */
  public function commitITBill(&$params)
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

    $sql = "select ref, bill_status, from_warehouse_id, to_warehouse_id,
					bizdt, biz_user_id
				from t_it_bill
				where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return $this->bad("要提交的调拨单不存在，无法提交");
    }
    $ref = $data[0]["ref"];
    $billStatus = $data[0]["bill_status"];
    if ($billStatus != 0) {
      return $this->bad("调拨单(单号：$ref)已经提交，不能再次提交");
    }

    $bizUserId = $data[0]["biz_user_id"];
    $bizDT = date("Y-m-d", strtotime($data[0]["bizdt"]));

    $fromWarehouseId = $data[0]["from_warehouse_id"];
    $toWarehouseId = $data[0]["to_warehouse_id"];

    // 检查仓库是否存在，仓库是否已经完成建账
    $warehouseDAO = new WarehouseDAO($db);
    $fromWarehouse = $warehouseDAO->getWarehouseById($fromWarehouseId);
    if (!$fromWarehouse) {
      return $this->bad("调出仓库不存在，无法进行调拨操作");
    }
    $warehouseName = $fromWarehouse["name"];
    $inited = $fromWarehouse["inited"];
    if ($inited != 1) {
      return $this->bad("仓库：$warehouseName 还没有完成建账，无法进行调拨操作");
    }

    $toWarehouse = $warehouseDAO->getWarehouseById($toWarehouseId);
    if (!$toWarehouse) {
      return $this->bad("调入仓库不存在，无法进行调拨操作");
    }
    $warehouseName = $toWarehouse["name"];
    $inited = $toWarehouse["inited"];
    if ($inited != 1) {
      return $this->bad("仓库：$warehouseName 还没有完成建账，无法进行调拨操作");
    }

    if ($fromWarehouseId == $toWarehouseId) {
      return $this->bad("调出仓库和调入仓库不能是同一个仓库");
    }

    $sql = "select goods_id, convert(goods_count, $fmt) as goods_count
				from t_it_bill_detail
				where itbill_id = '%s'
				order by show_order";
    $items = $db->query($sql, $id);
    foreach ($items as $i => $v) {
      $goodsId = $v["goods_id"];
      $goodsCount = $v["goods_count"];
      // 检查商品Id是否存在
      $sql = "select code, name, spec from t_goods where id = '%s' ";
      $data = $db->query($sql, $goodsId);
      if (!$data) {
        $index = $i + 1;
        return $this->bad("第{$index}条明细记录中的商品不存在，无法完成提交");
      }
      $goodsCode = $data[0]["code"];
      $goodsName = $data[0]["name"];
      $goodsSpec = $data[0]["spec"];

      // 检查调出数量是否为正数
      if ($goodsCount <= 0) {
        $index = $i + 1;
        return $this->bad("第{$index}条明细记录中的调拨数量不是正数，无法完成提交");
      }

      // 检查调出库存是否足够
      $sql = "select convert(balance_count, $fmt) as balance_count, balance_price, balance_money, 
						convert(out_count, $fmt) as out_count, out_money
					from t_inventory
					where warehouse_id = '%s' and goods_id = '%s' ";
      $data = $db->query($sql, $fromWarehouseId, $goodsId);
      if (!$data) {
        return $this->bad("商品[$goodsCode $goodsName $goodsSpec]库存不足，无法调拨");
      }
      $balanceCount = $data[0]["balance_count"];
      $balancePrice = $data[0]["balance_price"];
      $balanceMoney = $data[0]["balance_money"];
      if ($balanceCount < $goodsCount) {
        return $this->bad("商品[$goodsCode $goodsName $goodsSpec]库存不足，无法调拨");
      }
      $totalOutCount = $data[0]["out_count"];
      $totalOutMoney = $data[0]["out_money"];

      // 调出库 - 明细账
      $outPrice = $balancePrice;
      $outCount = $goodsCount;
      $outMoney = $outCount * $outPrice;
      if ($outCount == $balanceCount) {
        // 全部出库，这个时候金额全部转移
        $outMoney = $balanceMoney;
        $balanceCount = 0;
        $balanceMoney = 0;
      } else {
        $balanceCount -= $outCount;
        $balanceMoney -= $outMoney;
      }
      $totalOutCount += $outCount;
      $totalOutMoney += $outMoney;
      $totalOutPrice = $totalOutMoney / $totalOutCount;
      $sql = "insert into t_inventory_detail(out_count, out_price, out_money, balance_count,
					balance_price, balance_money, warehouse_id, goods_id, biz_date, biz_user_id, date_created,
					ref_number, ref_type)
					values (convert(%f, $fmt), %f, %f, convert(%f, $fmt), %f, %f, '%s', '%s', '%s', '%s', now(),
					'%s', '调拨出库')";
      $rc = $db->execute(
        $sql,
        $outCount,
        $outPrice,
        $outMoney,
        $balanceCount,
        $balancePrice,
        $balanceMoney,
        $fromWarehouseId,
        $goodsId,
        $bizDT,
        $bizUserId,
        $ref
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }

      // 调出库 - 总账
      $sql = "update t_inventory
					set out_count = convert(%f, $fmt), out_price = %f, out_money = %f,
						balance_count = convert(%f, $fmt), balance_price = %f, balance_money = %f
					where warehouse_id = '%s' and goods_id = '%s'";
      $rc = $db->execute(
        $sql,
        $totalOutCount,
        $totalOutPrice,
        $totalOutMoney,
        $balanceCount,
        $balancePrice,
        $balanceMoney,
        $fromWarehouseId,
        $goodsId
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }

      // 调入库 - 总账
      $inCount = $outCount;
      $inPrice = $outPrice;
      $inMoney = $outMoney;
      $balanceCount = 0;
      $balanceMoney = 0;
      $balancePrice = 0;
      $sql = "select convert(balance_count, $fmt) as balance_count, balance_money, 
						convert(in_count, $fmt) as in_count, in_money 
					from t_inventory
					where warehouse_id = '%s' and goods_id = '%s' ";
      $data = $db->query($sql, $toWarehouseId, $goodsId);
      if (!$data) {
        // 在总账中还没有记录
        $balanceCount = $inCount;
        $balanceMoney = $inMoney;
        $balancePrice = $inPrice;

        $sql = "insert into t_inventory(in_count, in_price, in_money, balance_count,
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
          $toWarehouseId,
          $goodsId
        );
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      } else {
        $balanceCount = $data[0]["balance_count"];
        $balanceMoney = $data[0]["balance_money"];
        $totalInCount = $data[0]["in_count"];
        $totalInMoney = $data[0]["in_money"];

        $balanceCount += $inCount;
        $balanceMoney += $inMoney;
        $balancePrice = $balanceMoney / $balanceCount;
        $totalInCount += $inCount;
        $totalInMoney += $inMoney;
        $totalInPrice = $totalInMoney / $totalInCount;

        $sql = "update t_inventory
						set in_count = convert(%f, $fmt), in_price = %f, in_money = %f,
						    balance_count = convert(%f, $fmt), balance_price = %f, balance_money = %f
						where warehouse_id = '%s' and goods_id = '%s' ";
        $rc = $db->execute(
          $sql,
          $totalInCount,
          $totalInPrice,
          $totalInMoney,
          $balanceCount,
          $balancePrice,
          $balanceMoney,
          $toWarehouseId,
          $goodsId
        );
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      }

      // 调入库 - 明细账
      $sql = "insert into t_inventory_detail(in_count, in_price, in_money, balance_count,
					balance_price, balance_money, warehouse_id, goods_id, ref_number, ref_type,
					biz_date, biz_user_id, date_created)
					values (convert(%f, $fmt), %f, %f, convert(%f, $fmt), %f, %f, '%s', '%s', '%s', '调拨入库', '%s', '%s', now())";
      $rc = $db->execute(
        $sql,
        $inCount,
        $inPrice,
        $inMoney,
        $balanceCount,
        $balancePrice,
        $balanceMoney,
        $toWarehouseId,
        $goodsId,
        $ref,
        $bizDT,
        $bizUserId
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 修改调拨单单据状态为已调拨
    $sql = "update t_it_bill
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
   * 查询某个调拨单的详情
   *
   * @param array $params
   * @return array
   */
  public function itBillInfo($params)
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

    $result = [];

    if ($id) {
      // 编辑
      $sql = "select t.ref, t.bill_status, t.bizdt, t.biz_user_id, u.name as biz_user_name,
						wf.id as from_warehouse_id, wf.name as from_warehouse_name,
						wt.id as to_warehouse_id, wt.name as to_warehouse_name, t.bill_memo
					from t_it_bill t, t_user u, t_warehouse wf, t_warehouse wt
					where t.id = '%s' and t.biz_user_id = u.id
					      and t.from_warehouse_id = wf.id
					      and t.to_warehouse_id = wt.id";
      $data = $db->query($sql, $id);
      if (!$data) {
        return $result;
      }

      $result["bizUserId"] = $data[0]["biz_user_id"];
      $result["bizUserName"] = $data[0]["biz_user_name"];
      $result["ref"] = $data[0]["ref"];
      $result["billStatus"] = $data[0]["bill_status"];
      $result["bizDT"] = date("Y-m-d", strtotime($data[0]["bizdt"]));
      $result["fromWarehouseId"] = $data[0]["from_warehouse_id"];
      $result["fromWarehouseName"] = $data[0]["from_warehouse_name"];
      $result["toWarehouseId"] = $data[0]["to_warehouse_id"];
      $result["toWarehouseName"] = $data[0]["to_warehouse_name"];
      $result["billMemo"] = $data[0]["bill_memo"];

      $items = [];
      $sql = "select t.id, g.id as goods_id, g.code, g.name, g.spec, u.name as unit_name, 
						convert(t.goods_count, $fmt) as goods_count, t.memo
					from t_it_bill_detail t, t_goods g, t_goods_unit u
					where t.itbill_id = '%s' and t.goods_id = g.id and g.unit_id = u.id
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
   * 调拨单生成pdf文件
   */
  public function getDataForPDF($params)
  {
    $ref = $params["ref"];

    $db = $this->db;
    $sql = "select t.id, t.bizdt, t.bill_status,
					fw.name as from_warehouse_name,
					tw.name as to_warehouse_name,
					u.name as biz_user_name,
					u1.name as input_user_name,
					t.date_created, t.company_id
				from t_it_bill t, t_warehouse fw, t_warehouse tw,
				   t_user u, t_user u1
				where (t.from_warehouse_id = fw.id)
				  and (t.to_warehouse_id = tw.id)
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

    $bill = [
      "bizDT" => $this->toYMD($data[0]["bizdt"]),
      "fromWarehouseName" => $data[0]["from_warehouse_name"],
      "toWarehouseName" => $data[0]["to_warehouse_name"],
      "bizUserName" => $data[0]["biz_user_name"],
      "saleMoney" => $data[0]["sale_money"]
    ];

    // 明细表
    $sql = "select t.id, g.code, g.name, g.spec, u.name as unit_name, 
						convert(t.goods_count, $fmt) as goods_count
				from t_it_bill_detail t, t_goods g, t_goods_unit u
				where t.itbill_id = '%s' and t.goods_id = g.id and g.unit_id = u.id
				order by t.show_order ";
    $data = $db->query($sql, $id);
    $items = [];
    foreach ($data as $v) {
      $items[] = [
        "goodsCode" => $v["code"],
        "goodsName" => $v["name"],
        "goodsSpec" => $v["spec"],
        "unitName" => $v["unit_name"],
        "goodsCount" => $v["goods_count"]
      ];
    }
    $bill["items"] = $items;

    return $bill;
  }

  /**
   * 通过调拨单单号查询调拨单完整数据，包括明细记录
   *
   * @param string $ref
   *        	调拨单单号
   * @return array|NULL
   */
  public function getFullBillDataByRef($ref)
  {
    $db = $this->db;
    $sql = "select t.id, t.bizdt, u.name as biz_user_name,
					wf.name as from_warehouse_name,
					wt.name as to_warehouse_name, t.company_id
				from t_it_bill t, t_user u, t_warehouse wf, t_warehouse wt
				where t.ref = '%s' and t.biz_user_id = u.id
				      and t.from_warehouse_id = wf.id
				      and t.to_warehouse_id = wt.id";
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
      "fromWarehouseName" => $data[0]["from_warehouse_name"],
      "toWarehouseName" => $data[0]["to_warehouse_name"]
    ];

    $items = [];
    $sql = "select t.id, g.id as goods_id, g.code, g.name, g.spec, u.name as unit_name, 
					convert(t.goods_count, $fmt) as goods_count
				from t_it_bill_detail t, t_goods g, t_goods_unit u
				where t.itbill_id = '%s' and t.goods_id = g.id and g.unit_id = u.id
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
        "goodsCount" => $v["goods_count"]
      ];
    }

    $result["items"] = $items;

    return $result;
  }

  /**
   * 生成打印调拨的数据
   *
   * @param array $params
   */
  public function getITBillDataForLodopPrint($params)
  {
    $db = $this->db;

    $id = $params["id"];

    $sql = "select t.ref, t.bizdt, t.bill_status,
					fw.name as from_warehouse_name,
					tw.name as to_warehouse_name,
					u.name as biz_user_name,
					u1.name as input_user_name,
					t.date_created, t.company_id
				from t_it_bill t, t_warehouse fw, t_warehouse tw,
				   t_user u, t_user u1
				where (t.from_warehouse_id = fw.id)
				  and (t.to_warehouse_id = tw.id)
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

    $bill = [
      "ref" => $data[0]["ref"],
      "bizDT" => $this->toYMD($data[0]["bizdt"]),
      "fromWarehouseName" => $data[0]["from_warehouse_name"],
      "toWarehouseName" => $data[0]["to_warehouse_name"],
      "bizUserName" => $data[0]["biz_user_name"],
      "saleMoney" => $data[0]["sale_money"],
      "printDT" => date("Y-m-d H:i:s")
    ];

    // 明细表
    $sql = "select t.id, g.code, g.name, g.spec, u.name as unit_name,
				convert(t.goods_count, $fmt) as goods_count
				from t_it_bill_detail t, t_goods g, t_goods_unit u
				where t.itbill_id = '%s' and t.goods_id = g.id and g.unit_id = u.id
				order by t.show_order ";
    $data = $db->query($sql, $id);
    $items = [];
    foreach ($data as $v) {
      $items[] = [
        "goodsCode" => $v["code"],
        "goodsName" => $v["name"],
        "goodsSpec" => $v["spec"],
        "unitName" => $v["unit_name"],
        "goodsCount" => $v["goods_count"]
      ];
    }
    $bill["items"] = $items;

    return $bill;
  }
}
