<?php

namespace Home\DAO;

use Home\Common\FIdConst;

/**
 * 拆分单 DAO
 *
 * @author 李静波
 */
class WSPBillDAO extends PSIBaseExDAO
{

  /**
   * 生成新的拆分单单号
   *
   * @param string $companyId
   * @return string
   */
  private function genNewBillRef($companyId)
  {
    $db = $this->db;

    $bs = new BizConfigDAO($db);
    $pre = $bs->getWSPBillRefPre($companyId);

    $mid = date("Ymd");

    $sql = "select ref from t_wsp_bill where ref like '%s' order by ref desc limit 1";
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
   * 获得某个拆分单的商品构成
   *
   * @param array $params
   */
  public function goodsBOM($params)
  {
    $db = $this->db;

    // id - 拆分单明细id
    $id = $params["id"];

    $result = [];
    if (!$id) {
      return $result;
    }

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $sql = "select d.goods_id, g.code, g.name, g.spec, u.name as unit_name, 
              convert(d.goods_count, $fmt) as goods_count 
            from t_wsp_bill_detail d, t_goods g, t_goods_unit u
            where d.id = '%s' and d.goods_id = g.id and g.unit_id = u.id";
    $data = $db->query($sql, $id);
    if (!$data) {
      return $result;
    }
    $v = $data[0];
    $goodsCount = $v["goods_count"];

    $iconCls = "PSI-GoodsBOM";

    $top = [
      "id" => $id,
      "text" => $v["code"],
      "goodsName" => $v["name"],
      "goodsSpec" => $v["spec"],
      "unitName" => $v["unit_name"],
      "bomCount" => 1,
      "goodsCount" => $goodsCount,
      "iconCls" => $iconCls,
      "expanded" => true
    ];

    // 当前实现只展开一层BOM
    $iconClsItem = "PSI-GoodsBOMItem";
    $goodsId = $v["goods_id"];

    $sql = "select sum(cost_weight) as sum_cost_weight 
            from t_wsp_bill_detail_bom
            where wspbilldetail_id = '%s' and goods_id = '%s' ";
    $data = $db->query($sql, $id, $goodsId);
    $sumCostWeight = $data[0]["sum_cost_weight"];
    if (!$sumCostWeight) {
      $sumCostWeight = 0;
    }

    $sql = "select b.id, g.code, g.name, g.spec, u.name as unit_name,
              convert(b.sub_goods_count, $fmt) as sub_goods_count,
              b.cost_weight
            from t_wsp_bill_detail_bom b, t_goods g, t_goods_unit u
            where b.wspbilldetail_id = '%s' and b.goods_id = '%s' 
              and b.sub_goods_id = g.id and g.unit_id = u.id
            order by g.code";
    $data = $db->query($sql, $id, $goodsId);
    $children = [];
    foreach ($data as $v) {
      $costWeight = $v["cost_weight"];
      $costWeightNote = null;
      if ($costWeight == 0 || $sumCostWeight == 0) {
        $costWeight = null;
        $costWeightNote = null;
      } else {
        $percent = number_format($costWeight / $sumCostWeight * 100, 2);
        $costWeightNote = "{$costWeight}/{$sumCostWeight} = {$percent}%";
      }

      $children[] = [
        "id" => $v["id"],
        "text" => $v["code"],
        "goodsName" => $v["name"],
        "goodsSpec" => $v["spec"],
        "unitName" => $v["unit_name"],
        "bomCount" => $v["sub_goods_count"],
        "goodsCount" => $v["sub_goods_count"] * $goodsCount,
        "iconCls" => $iconClsItem,
        "expanded" => true,
        "leaf" => true,
        "costWeight" => $costWeight,
        "costWeightNote" => $costWeightNote
      ];
    }

    $top["children"] = $children;
    $top["leaf"] = count($children) == 0;

    $result[] = $top;

    return $result;
  }

  /**
   * 拆分单详情
   */
  public function wspBillInfo($params)
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

    // 拆分单主表id
    $id = $params["id"];

    if ($id) {
      // 编辑
      $sql = "select w.ref, w.bizdt, w.bill_status,
                w.from_warehouse_id, w.to_warehouse_id,
                fw.name as from_warehouse_name,
                tw.name as to_warehouse_name,
                w.biz_user_id,
                u.name as biz_user_name,
                w.bill_memo
              from t_wsp_bill w, t_warehouse fw, t_warehouse tw,
                t_user u
              where (w.from_warehouse_id = fw.id)
                and (w.to_warehouse_id = tw.id)
                and (w.biz_user_id = u.id)
                and w.id = '%s' ";
      $data = $db->query($sql, $id);
      if (!$data) {
        return $result;
      }

      $v = $data[0];
      $result = [
        "ref" => $v["ref"],
        "billStatus" => $v["bill_status"],
        "bizDT" => $this->toYMD($v["bizdt"]),
        "fromWarehouseId" => $v["from_warehouse_id"],
        "fromWarehouseName" => $v["from_warehouse_name"],
        "toWarehouseId" => $v["to_warehouse_id"],
        "toWarehouseName" => $v["to_warehouse_name"],
        "bizUserId" => $v["biz_user_id"],
        "bizUserName" => $v["biz_user_name"],
        "billMemo" => $v["bill_memo"]
      ];

      // 明细记录

      $sql = "select w.id, g.id as goods_id, g.code, g.name, g.spec, u.name as unit_name,
                convert(w.goods_count, $fmt) as goods_count, w.memo
              from t_wsp_bill_detail w, t_goods g, t_goods_unit u
              where w.wspbill_id = '%s' and w.goods_id = g.id and g.unit_id = u.id
              order by w.show_order ";

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
   * 新建拆分单
   *
   * @param array $bill
   */
  public function addWSPBill(&$bill)
  {
    $db = $this->db;

    $bizDT = $bill["bizDT"];
    $fromWarehouseId = $bill["fromWarehouseId"];
    $toWarehouseId = $bill["toWarehouseId"];
    $bizUserId = $bill["bizUserId"];
    $billMemo = $bill["billMemo"];

    // 检查业务日期
    if (!$this->dateIsValid($bizDT)) {
      return $this->bad("业务日期不正确");
    }

    // 检查仓库
    $warehouseDAO = new WarehouseDAO($db);
    $w = $warehouseDAO->getWarehouseById($fromWarehouseId);
    if (!$w) {
      return $this->bad("仓库不存在");
    }

    $w = $warehouseDAO->getWarehouseById($toWarehouseId);
    if (!$w) {
      return $this->bad("拆分后调入仓库不存在");
    }

    // 检查业务员
    $userDAO = new UserDAO($db);
    $user = $userDAO->getUserById($bizUserId);
    if (!$user) {
      return $this->bad("选择的业务员不存在，无法保存数据");
    }

    $items = $bill["items"];

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
    $sql = "insert into t_wsp_bill (id, ref, from_warehouse_id, to_warehouse_id,
            bill_status, bizdt, biz_user_id, date_created,
            input_user_id, data_org, company_id, bill_memo)
          values ('%s', '%s', '%s', '%s',
            0, '%s', '%s', now(),
            '%s', '%s', '%s', '%s')";
    $rc = $db->execute(
      $sql,
      $id,
      $ref,
      $fromWarehouseId,
      $toWarehouseId,
      $bizDT,
      $bizUserId,
      $loginUserId,
      $dataOrg,
      $companyId,
      $billMemo
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 明细表
    foreach ($items as $showOrder => $v) {
      $goodsId = $v["goodsId"];
      if (!$goodsId) {
        continue;
      }

      $goodsCount = $v["goodsCount"];
      $memo = $v["memo"];

      // 检查商品是否有子商品
      // 拆分单的明细表中不允许保存没有子商品的商品
      // 一个商品没有子商品，就不能做拆分业务
      $sql = "select count(*) as cnt from t_goods_bom where goods_id = '%s' ";
      $data = $db->query($sql, $goodsId);
      $cnt = $data[0]["cnt"];
      if ($cnt == 0) {
        $rowIndex = $showOrder + 1;
        return $this->bad("第{$rowIndex}记录中的商品没有子商品，不能做拆分业务");
      }

      // 检查拆分数量
      if ($goodsCount <= 0) {
        $rowIndex = $showOrder + 1;
        return $this->bad("第{$rowIndex}记录中的商品的拆分数量需要大于0");
      }

      $detailId = $this->newId();
      $sql = "insert into t_wsp_bill_detail (id, wspbill_id, show_order, goods_id,
                goods_count, date_created, data_org, company_id, memo)
              values ('%s', '%s', %d, '%s',
                convert(%f, $fmt), now(), '%s', '%s', '%s')";
      $rc = $db->execute(
        $sql,
        $detailId,
        $id,
        $showOrder,
        $goodsId,
        $goodsCount,
        $dataOrg,
        $companyId,
        $memo
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }

      // 复制当前商品构成BOM
      $this->copyGoodsBOM($detailId, $goodsId, $fmt);
    }

    // 展开当前商品BOM
    $this->expandGoodsBOM($id, $fmt);

    // 操作成功
    $bill["id"] = $id;
    $bill["ref"] = $ref;
    return null;
  }

  // 复制当前商品构成BOM
  // 目前的实现只复制一层BOM
  private function copyGoodsBOM($wspbillDetailId, $goodsId, $fmt)
  {
    $db = $this->db;

    $sql = "select sub_goods_id, convert(sub_goods_count, $fmt) as sub_goods_count,
              cost_weight 
            from t_goods_bom 
            where goods_id = '%s'";
    $data = $db->query($sql, $goodsId);
    foreach ($data as $v) {
      $subGoodsId = $v["sub_goods_id"];
      $subGoodsCount = $v["sub_goods_count"];
      $costWeight = $v["cost_weight"];

      $sql = "insert into t_wsp_bill_detail_bom (id, wspbilldetail_id, goods_id, sub_goods_id,
                parent_id, sub_goods_count, cost_weight) 
              values ('%s', '%s', '%s', '%s',
                null, convert(%f, $fmt), %d)";
      $rc = $db->execute(
        $sql,
        $this->newId(),
        $wspbillDetailId,
        $goodsId,
        $subGoodsId,
        $subGoodsCount,
        $costWeight
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }
  }

  // 展开商品BOM
  // 目前的实现只展开一层BOM
  private function expandGoodsBOM($wspbillId, $fmt)
  {
    $db = $this->db;

    $sql = "select id, goods_id, convert(goods_count, $fmt) as goods_count,
              data_org, company_id
            from t_wsp_bill_detail
            where wspbill_id = '%s'
            order by show_order";
    $data = $db->query($sql, $wspbillId);

    $showOrder = 0;
    foreach ($data as $v) {
      $wspbillDetailId = $v["id"];
      $goodsId = $v["goods_id"];
      $goodsCount = $v["goods_count"];
      $dataOrg = $v["data_org"];
      $companyId = $v["company_id"];

      $sql = "select sub_goods_id, convert(sub_goods_count, $fmt) as sub_goods_count
              from t_wsp_bill_detail_bom
              where wspbilldetail_id = '%s' and goods_id = '%s' ";
      $subData = $db->query($sql, $wspbillDetailId, $goodsId);
      foreach ($subData as $sv) {
        $showOrder += 1;

        $subGoodsId = $sv["sub_goods_id"];
        $subGoodsCount = $sv["sub_goods_count"] * $goodsCount;

        $sql = "insert into t_wsp_bill_detail_ex (id, wspbill_id, show_order, goods_id,
                  goods_count, date_created, data_org, company_id, from_goods_id,
                  wspbilldetail_id)
                values ('%s', '%s', %d, '%s',
                  convert(%f, $fmt), now(), '%s', '%s', '%s',
                  '%s')";

        $rc = $db->execute(
          $sql,
          $this->newId(),
          $wspbillId,
          $showOrder,
          $subGoodsId,
          $subGoodsCount,
          $dataOrg,
          $companyId,
          $goodsId,
          $wspbillDetailId
        );
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      }
    }
  }

  /**
   * 编辑拆分单
   *
   * @param array $bill
   */
  public function updateWSPBill(&$bill)
  {
    $db = $this->db;

    $id = $bill["id"];
    $oldBill = $this->getWSPBillById($id);
    if (!$oldBill) {
      return $this->bad("要编辑的拆分单不存在");
    }
    $ref = $oldBill["ref"];
    $billStatus = $oldBill["billStatus"];
    if ($billStatus > 0) {
      return $this->bad("拆分单[单号：{$ref}]已经提交，不能被编辑了");
    }
    $dataOrg = $oldBill["dataOrg"];

    $bizDT = $bill["bizDT"];
    $fromWarehouseId = $bill["fromWarehouseId"];
    $toWarehouseId = $bill["toWarehouseId"];
    $bizUserId = $bill["bizUserId"];
    $billMemo = $bill["billMemo"];

    // 检查业务日期
    if (!$this->dateIsValid($bizDT)) {
      return $this->bad("业务日期不正确");
    }

    // 检查仓库
    $warehouseDAO = new WarehouseDAO($db);
    $w = $warehouseDAO->getWarehouseById($fromWarehouseId);
    if (!$w) {
      return $this->bad("仓库不存在");
    }

    $w = $warehouseDAO->getWarehouseById($toWarehouseId);
    if (!$w) {
      return $this->bad("拆分后调入仓库不存在");
    }

    // 检查业务员
    $userDAO = new UserDAO($db);
    $user = $userDAO->getUserById($bizUserId);
    if (!$user) {
      return $this->bad("选择的业务员不存在，无法保存数据");
    }

    $companyId = $bill["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->badParam("companyId");
    }

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    // 主表
    $sql = "update t_wsp_bill
            set bizdt = '%s', from_warehouse_id = '%s',
              to_warehouse_id = '%s', biz_user_id = '%s',
              bill_memo = '%s'
            where id = '%s' ";
    $rc = $db->execute(
      $sql,
      $bizDT,
      $fromWarehouseId,
      $toWarehouseId,
      $bizUserId,
      $billMemo,
      $id
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 明细表

    // 先清空明细数据
    $sql = "select id from t_wsp_bill_detail where wspbill_id = '%s' ";
    $data = $db->query($sql, $id);
    foreach ($data as $v) {
      $detailId = $v["id"];

      $sql = "delete from t_wsp_bill_detail_bom where wspbilldetail_id = '%s' ";
      $rc = $db->execute($sql, $detailId);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }

      $sql = "delete from t_wsp_bill_detail_ex where wspbilldetail_id = '%s' ";
      $rc = $db->execute($sql, $detailId);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }

      $sql = "delete from t_wsp_bill_detail where id = '%s' ";
      $rc = $db->execute($sql, $detailId);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    $items = $bill["items"];

    // 清空明细数据后，再插入新的明细数据
    foreach ($items as $showOrder => $v) {
      $goodsId = $v["goodsId"];
      if (!$goodsId) {
        continue;
      }

      $goodsCount = $v["goodsCount"];
      $memo = $v["memo"];

      // 检查商品是否有子商品
      // 拆分单的明细表中不允许保存没有子商品的商品
      // 一个商品没有子商品，就不能做拆分业务
      $sql = "select count(*) as cnt from t_goods_bom where goods_id = '%s' ";
      $data = $db->query($sql, $goodsId);
      $cnt = $data[0]["cnt"];
      if ($cnt == 0) {
        $rowIndex = $showOrder + 1;
        return $this->bad("第{$rowIndex}记录中的商品没有子商品，不能做拆分业务");
      }

      // 检查拆分数量
      if ($goodsCount <= 0) {
        $rowIndex = $showOrder + 1;
        return $this->bad("第{$rowIndex}记录中的商品的拆分数量需要大于0");
      }

      $detailId = $this->newId();
      $sql = "insert into t_wsp_bill_detail (id, wspbill_id, show_order, goods_id,
                goods_count, date_created, data_org, company_id, memo)
              values ('%s', '%s', %d, '%s',
                convert(%f, $fmt), now(), '%s', '%s', '%s')";
      $rc = $db->execute(
        $sql,
        $detailId,
        $id,
        $showOrder,
        $goodsId,
        $goodsCount,
        $dataOrg,
        $companyId,
        $memo
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }

      // 复制当前商品构成BOM
      $this->copyGoodsBOM($detailId, $goodsId, $fmt);
    }

    // 展开当前商品BOM
    $this->expandGoodsBOM($id, $fmt);

    // 操作成功
    $bill["ref"] = $ref;
    return null;
  }

  /**
   * 拆分单主表列表
   */
  public function wspbillList($params)
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

    $sql = "select w.id, w.ref, w.bizdt, w.bill_status,
              fw.name as from_warehouse_name,
              tw.name as to_warehouse_name,
              u.name as biz_user_name,
              u1.name as input_user_name,
              w.date_created, w.bill_memo
            from t_wsp_bill w, t_warehouse fw, t_warehouse tw,
              t_user u, t_user u1
            where (w.from_warehouse_id = fw.id)
              and (w.to_warehouse_id = tw.id)
              and (w.biz_user_id = u.id)
              and (w.input_user_id = u1.id) ";
    $queryParams = [];

    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::WSP, "w", $loginUserId);
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
    if ($fromWarehouseId) {
      $sql .= " and (w.from_warehouse_id = '%s') ";
      $queryParams[] = $fromWarehouseId;
    }
    if ($toWarehouseId) {
      $sql .= " and (w.to_warehouse_id = '%s') ";
      $queryParams[] = $toWarehouseId;
    }
    if ($goodsId) {
      $sql .= " and (w.id in (select distinct wspbill_id from t_wsp_bill_detail where goods_id = '%s')) ";
      $queryParams[] = $goodsId;
    }

    $sql .= " order by w.bizdt desc, w.ref desc
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
        "billStatus" => $v["bill_status"],
        "fromWarehouseName" => $v["from_warehouse_name"],
        "toWarehouseName" => $v["to_warehouse_name"],
        "bizUserName" => $v["biz_user_name"],
        "inputUserName" => $v["input_user_name"],
        "dateCreated" => $v["date_created"],
        "billMemo" => $v["bill_memo"]
      ];
    }

    $sql = "select count(*) as cnt
            from t_wsp_bill w, t_warehouse fw, t_warehouse tw,
              t_user u, t_user u1
            where (w.from_warehouse_id = fw.id)
              and (w.to_warehouse_id = tw.id)
              and (w.biz_user_id = u.id)
              and (w.input_user_id = u1.id) ";
    $queryParams = [];

    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::WSP, "w", $loginUserId);
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
    if ($fromWarehouseId) {
      $sql .= " and (w.from_warehouse_id = '%s') ";
      $queryParams[] = $fromWarehouseId;
    }
    if ($toWarehouseId) {
      $sql .= " and (w.to_warehouse_id = '%s') ";
      $queryParams[] = $toWarehouseId;
    }
    if ($goodsId) {
      $sql .= " and (w.id in (select distinct wspbill_id from t_wsp_bill_detail where goods_id = '%s')) ";
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
   * 拆分单明细
   */
  public function wspBillDetailList($params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    // id: 拆分单id
    $id = $params["id"];

    $result = [];

    $sql = "select w.id, g.code, g.name, g.spec, u.name as unit_name, 
              convert(w.goods_count, $fmt) as goods_count, w.memo
            from t_wsp_bill_detail w, t_goods g, t_goods_unit u
            where w.wspbill_id = '%s' and w.goods_id = g.id and g.unit_id = u.id
            order by w.show_order ";

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
   * 拆分单明细 - 拆分后明细
   */
  public function wspBillDetailExList($params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    // id: 拆分单id
    $id = $params["id"];

    $result = [];

    $sql = "select w.id, g.code, g.name, g.spec, u.name as unit_name,
              convert(w.goods_count, $fmt) as goods_count
            from t_wsp_bill_detail_ex w, t_goods g, t_goods_unit u
            where w.wspbill_id = '%s' and w.goods_id = g.id and g.unit_id = u.id
            order by w.show_order ";

    $data = $db->query($sql, $id);
    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "goodsCode" => $v["code"],
        "goodsName" => $v["name"],
        "goodsSpec" => $v["spec"],
        "unitName" => $v["unit_name"],
        "goodsCount" => $v["goods_count"]
      ];
    }

    return $result;
  }

  public function getWSPBillById($id)
  {
    $db = $this->db;

    $sql = "select ref, bill_status, data_org from t_wsp_bill where id = '%s' ";
    $data = $db->query($sql, $id);
    if ($data) {
      return [
        "ref" => $data[0]["ref"],
        "billStatus" => $data[0]["bill_status"],
        "dataOrg" => $data[0]["data_org"]
      ];
    } else {
      return null;
    }
  }

  /**
   * 删除拆分单
   */
  public function deleteWSPBill(&$params)
  {
    $db = $this->db;

    // 拆分单id
    $id = $params["id"];

    $bill = $this->getWSPBillById($id);
    if (!$bill) {
      return $this->bad("要删除的拆分单不存在");
    }

    $ref = $bill["ref"];
    $billStatus = $bill["billStatus"];

    if ($billStatus > 0) {
      return $this->bad("拆分单[单号：{$ref}]已经提交，不能再删除");
    }

    // 明细
    $sql = "select id from t_wsp_bill_detail where wspbill_id = '%s' ";
    $data = $db->query($sql, $id);
    foreach ($data as $v) {
      $detailId = $v["id"];

      $sql = "delete from t_wsp_bill_detail_bom where wspbilldetail_id = '%s' ";
      $rc = $db->execute($sql, $detailId);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }

      $sql = "delete from t_wsp_bill_detail_ex where wspbilldetail_id = '%s' ";
      $rc = $db->execute($sql, $detailId);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }

      $sql = "delete from t_wsp_bill_detail where id = '%s' ";
      $rc = $db->execute($sql, $detailId);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 主表
    $sql = "delete from t_wsp_bill where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $params["ref"] = $ref;
    return null;
  }

  /**
   * 提交拆分单
   */
  public function commitWSPBill(&$params)
  {
    $db = $this->db;

    // 拆分单主表id
    $id = $params["id"];

    $sql = "select ref, bill_status , bizdt, from_warehouse_id, to_warehouse_id,
              company_id, data_org, biz_user_id
            from t_wsp_bill
            where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return $this->bad("要提交的拆分单不存在");
    }
    $v = $data[0];
    $ref = $v["ref"];
    $billStatus = $v["bill_status"];
    if ($billStatus > 0) {
      return $this->bad("拆分单[单号：{$ref}]已经提交，不能再次提交");
    }
    $bizDT = $this->toYMD($v["bizdt"]);
    if (!$this->dateIsValid($bizDT)) {
      return $this->bad("业务日期不正确");
    }
    $fromWarehouseId = $v["from_warehouse_id"];
    $toWarehouseId = $v["to_warehouse_id"];

    $warehouseDAO = new WarehouseDAO($db);
    $fromWarehouse = $warehouseDAO->getWarehouseById($fromWarehouseId);
    if (!$fromWarehouse) {
      return $this->bad("仓库不存在");
    }
    $inited = $fromWarehouse["inited"];
    if ($inited != 1) {
      $fromWarehouseName = $fromWarehouse["name"];
      return $this->bad("仓库[{$fromWarehouseName}]还没有完成库存建账，不能进行业务操作");
    }
    $toWarehouse = $warehouseDAO->getWarehouseById($toWarehouseId);
    if (!$toWarehouse) {
      return $this->bad("拆分后调入仓库不存在");
    }
    $inited = $toWarehouse["inited"];
    if ($inited != 1) {
      $toWarehouseName = $toWarehouse["name"];
      return $this->bad("拆分后调入仓库[{$toWarehouseName}]还没有完成库存建账，不能进行业务操作");
    }

    $companyId = $v["company_id"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->badParam("companyId");
    }
    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $dataOrg = $v["data_org"];
    $bizUserId = $v["biz_user_id"];

    // 取明细
    $sql = "select id, goods_id, convert(goods_count, $fmt) as goods_count
            from t_wsp_bill_detail
            where wspbill_id = '%s' 
            order by show_order";
    $items = $db->query($sql, $id);

    foreach ($items as $showOrder => $v) {
      $detailId = $v["id"];
      $goodsId = $v["goods_id"];
      $goodsCount = $v["goods_count"];
      $recordIndex = $showOrder + 1;

      // 要拆分的商品出库
      // 确认出库数量足够
      $sql = "select convert(balance_count, $fmt) as balance_count, 
                balance_money, balance_price, out_count, out_money, out_price 
              from t_inventory
              where warehouse_id = '%s' and goods_id = '%s' ";
      $data = $db->query($sql, $fromWarehouseId, $goodsId);
      if (!$data) {
        return $this->bad("第{$recordIndex}条商品没有库存，无法完成拆分操作");
      }
      $v = $data[0];
      $balanceCount = $v["balance_count"];
      $balanceMoney = $v["balance_money"];
      $balancePrice = $v["balance_price"];
      $totalOutCount = $v["out_count"];
      $totalOutMoney = $v["out_money"];
      $totalOutPrice = $v["out_price"];

      if ($goodsCount > $balanceCount) {
        return $this->bad("第{$recordIndex}条商品库存量($balanceCount)小于拆分量($goodsCount)，无法完成拆分操作");
      }

      // 出库：更新总账
      $outCount = $goodsCount;
      $outMoney = $outCount * $balancePrice;
      if ($outCount == $balanceCount) {
        // 库存全部出库的时候，把金额也全部出库
        $outMoney = $balanceMoney;

        $balanceMoney = 0;
        $balanceCount = 0;
      } else {
        if ($outMoney > $balanceMoney) {
          // 这是由于单价小数位数带来的误差
          $outMoney = $balanceMoney;
        }

        $balanceMoney -= $outMoney;
        $balanceCount -= $outCount;
      }

      $outPrice = $outMoney / $outCount;

      $totalOutCount += $outCount;
      $totalOutMoney += $outMoney;
      $totalOutPrice = $totalOutMoney / $totalOutCount;

      $sql = "update t_inventory
              set balance_count = convert(%f, $fmt), balance_money = %f,
                out_count = convert(%f, $fmt), out_money = %f, out_price = %f
              where warehouse_id = '%s' and goods_id = '%s' ";
      $rc = $db->execute(
        $sql,
        $balanceCount,
        $balanceMoney,
        $totalOutCount,
        $totalOutMoney,
        $totalOutPrice,
        $fromWarehouseId,
        $goodsId
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }

      // 出库：更新明细账
      $sql = "insert into t_inventory_detail (warehouse_id, goods_id, out_count, out_money, out_price,
                balance_count, balance_money, balance_price, biz_date, biz_user_id,
                date_created, ref_number, ref_type, data_org, company_id)
              values ('%s', '%s', convert(%f, $fmt), %f, %f,
                convert(%f, $fmt), %f, %f, '%s', '%s',
                now(), '%s', '存货拆分', '%s', '%s')";
      $rc = $db->execute(
        $sql,
        $fromWarehouseId,
        $goodsId,
        $outCount,
        $outMoney,
        $outPrice,
        $balanceCount,
        $balanceMoney,
        $balancePrice,
        $bizDT,
        $bizUserId,
        $ref,
        $dataOrg,
        $companyId
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }

      // 拆分后的商品入库
      $sql = "select sum(cost_weight) as sum_cost_weight 
              from t_wsp_bill_detail_bom
              where wspbilldetail_id = '%s' and goods_id = '%s' ";
      $data = $db->query($sql, $detailId, $goodsId);
      $sumCostWeight = $data[0]["sum_cost_weight"];
      if (!$sumCostWeight || $sumCostWeight < 0) {
        $sumCostWeight = 0;
      }

      // 总得待分摊的成本
      $sumCost = $outMoney;

      $sql = "select sub_goods_id, convert(sub_goods_count, $fmt) as sub_goods_count,
                cost_weight
              from t_wsp_bill_detail_bom
              where wspbilldetail_id = '%s' and goods_id = '%s' ";
      $subItems = $db->query($sql, $detailId, $goodsId);
      $subItemsCount = count($subItems);
      foreach ($subItems as $svIndex => $sv) {
        // 分摊成本
        $subGoodsId = $sv["sub_goods_id"];
        $subGoodsCount = $sv["sub_goods_count"] * $goodsCount;
        $costWeight = $sv["cost_weight"];

        $inMoney = 0;
        if ($sumCostWeight > 0) {
          $inMoney = $sumCost * ($costWeight / $sumCostWeight);
        }
        if ($svIndex == $subItemsCount - 1) {
          // 把剩余的成本全部分摊给最后一个商品
          $inMoney = $sumCost;
        }
        $sumCost -= $inMoney;

        $inCount = $subGoodsCount;
        $inPrice = $inMoney / $inCount;

        // 入库：更新总账
        $balanceCountSI = $inCount;
        $balanceMoneySI = $inMoney;
        $balancePriceSI = $inPrice;
        $sql = "select convert(in_count, $fmt) as in_count, in_money, 
                  convert(balance_count, $fmt) as balance_count, balance_money
                from t_inventory
                where warehouse_id = '%s' and goods_id = '%s' ";
        $data = $db->query($sql, $toWarehouseId, $subGoodsId);

        if (!$data) {
          // 首次入库

          $sql = "insert into t_inventory (warehouse_id, goods_id, in_count, in_money, in_price,
                    balance_count, balance_money, balance_price)
                  values ('%s', '%s', convert(%f, $fmt), %f, %f,
                    convert(%f, $fmt), %f, %f)";
          $rc = $db->execute(
            $sql,
            $toWarehouseId,
            $subGoodsId,
            $inCount,
            $inMoney,
            $inPrice,
            $balanceCountSI,
            $balanceMoneySI,
            $balancePriceSI
          );
          if ($rc === false) {
            return $this->sqlError(__METHOD__, __LINE__);
          }
        } else {
          $totalInCount = $data[0]["in_count"];
          $totalInCount += $inCount;
          $totalInMoney = $data[0]["in_money"];
          $totalInMoney += $inMoney;
          $totalInPrice = $totalInMoney / $totalInCount;

          $balanceCountSI = $data[0]["balance_count"];
          $balanceCountSI += $inCount;
          $balanceMoneySI = $data[0]["balance_money"];
          $balanceMoneySI += $inMoney;
          $balancePriceSI = $balanceMoneySI / $balanceCountSI;

          $sql = "update t_inventory
                  set in_count = convert(%f, $fmt), in_money = %f, in_price = %f,
                    balance_count = convert(%f, $fmt), balance_money = %f, balance_price = %f
                  where warehouse_id = '%s' and goods_id = '%s' ";
          $rc = $db->execute(
            $sql,
            $totalInCount,
            $totalInMoney,
            $totalInPrice,
            $balanceCountSI,
            $balanceMoneySI,
            $balancePriceSI,
            $toWarehouseId,
            $subGoodsId
          );
          if ($rc === false) {
            return $this->sqlError(__METHOD__, __LINE__);
          }
        }

        // 入库：更新明细账
        $sql = "insert into t_inventory_detail (warehouse_id, goods_id, in_count, in_money, in_price,
                  balance_count, balance_money, balance_price, ref_number, ref_type,
                  biz_date, biz_user_id, date_created, data_org, company_id)
                values ('%s', '%s', convert(%f, $fmt), %f, %f,
                  convert(%f, $fmt), %f, %f, '%s', '存货拆分',
                  '%s', '%s', now(), '%s', '%s')";
        $rc = $db->execute(
          $sql,
          $toWarehouseId,
          $subGoodsId,
          $inCount,
          $inMoney,
          $inPrice,
          $balanceCountSI,
          $balanceMoneySI,
          $balancePriceSI,
          $ref,
          $bizDT,
          $bizUserId,
          $dataOrg,
          $companyId
        );
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      } // end of foreach $subItems
    } // end of foreach $items

    // 更新本单据状态
    $sql = "update t_wsp_bill set bill_status = 1000 where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $params["ref"] = $ref;
    return null;
  }

  /**
   * 获得生成PDF文件的数据
   *
   * @param array $params
   */
  public function getDataForPDF($params)
  {
    $db = $this->db;

    $result = [];

    $ref = $params["ref"];

    $sql = "select w.id, w.bizdt,
              fw.name as from_warehouse_name,
              tw.name as to_warehouse_name,
              u.name as biz_user_name,
              w.bill_memo, w.company_id
            from t_wsp_bill w, t_warehouse fw, t_warehouse tw,
              t_user u
            where (w.from_warehouse_id = fw.id)
              and (w.to_warehouse_id = tw.id)
              and (w.biz_user_id = u.id)
              and w.ref = '%s' ";
    $data = $db->query($sql, $ref);
    if (!$data) {
      return $result;
    }
    $v = $data[0];

    $companyId = $v["company_id"];
    if ($this->companyIdNotExists($companyId)) {
      $companyId = $params["companyId"];
    }

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $id = $v["id"];
    $result = [
      "ref" => $ref,
      "bizDT" => $this->toYMD($v["bizdt"]),
      "fromWarehouseName" => $v["from_warehouse_name"],
      "toWarehouseName" => $v["to_warehouse_name"],
      "bizUserName" => $v["biz_user_name"],
      "billMemo" => $v["bill_memo"]
    ];

    // 拆分前明细
    $sql = "select g.code, g.name, g.spec, u.name as unit_name,
              convert(w.goods_count, $fmt) as goods_count, w.memo
            from t_wsp_bill_detail w, t_goods g, t_goods_unit u
            where w.wspbill_id = '%s' and w.goods_id = g.id and g.unit_id = u.id
            order by w.show_order ";

    $data = $db->query($sql, $id);
    $items = [];
    foreach ($data as $v) {
      $items[] = [
        "goodsCode" => $v["code"],
        "goodsName" => $v["name"],
        "goodsSpec" => $v["spec"],
        "unitName" => $v["unit_name"],
        "goodsCount" => $v["goods_count"],
        "memo" => $v["memo"]
      ];
    }

    $result["items"] = $items;

    // 拆分后明细
    $sql = "select g.code, g.name, g.spec, u.name as unit_name,
              convert(w.goods_count, $fmt) as goods_count
            from t_wsp_bill_detail_ex w, t_goods g, t_goods_unit u
            where w.wspbill_id = '%s' and w.goods_id = g.id and g.unit_id = u.id
            order by w.show_order ";

    $data = $db->query($sql, $id);
    $itemsEx = [];
    foreach ($data as $v) {
      $itemsEx[] = [
        "goodsCode" => $v["code"],
        "goodsName" => $v["name"],
        "goodsSpec" => $v["spec"],
        "unitName" => $v["unit_name"],
        "goodsCount" => $v["goods_count"]
      ];
    }

    $result["itemsEx"] = $itemsEx;

    return $result;
  }

  /**
   * 生成打印拆分单的数据
   *
   * @param array $params
   */
  public function getWSPBillDataForLodopPrint($params)
  {
    $db = $this->db;

    $id = $params["id"];

    $sql = "select w.ref, w.bizdt,
              fw.name as from_warehouse_name,
              tw.name as to_warehouse_name,
              u.name as biz_user_name,
              w.bill_memo, w.company_id
            from t_wsp_bill w, t_warehouse fw, t_warehouse tw,
              t_user u
            where (w.from_warehouse_id = fw.id)
              and (w.to_warehouse_id = tw.id)
              and (w.biz_user_id = u.id)
              and w.id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return null;
    }
    $v = $data[0];

    $companyId = $v["company_id"];

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $bill = [
      "ref" => $v["ref"],
      "bizDT" => $this->toYMD($v["bizdt"]),
      "fromWarehouseName" => $v["from_warehouse_name"],
      "toWarehouseName" => $v["to_warehouse_name"],
      "bizUserName" => $v["biz_user_name"],
      "printDT" => date("Y-m-d H:i:s")
    ];

    // 拆分前明细
    $sql = "select g.code, g.name, g.spec, u.name as unit_name,
              convert(w.goods_count, $fmt) as goods_count, w.memo
            from t_wsp_bill_detail w, t_goods g, t_goods_unit u
            where w.wspbill_id = '%s' and w.goods_id = g.id and g.unit_id = u.id
            order by w.show_order ";

    $data = $db->query($sql, $id);
    $items = [];
    foreach ($data as $v) {
      $items[] = [
        "goodsCode" => $v["code"],
        "goodsName" => $v["name"],
        "goodsSpec" => $v["spec"],
        "unitName" => $v["unit_name"],
        "goodsCount" => $v["goods_count"],
        "memo" => $v["memo"]
      ];
    }

    $bill["items"] = $items;

    // 拆分后明细
    $sql = "select g.code, g.name, g.spec, u.name as unit_name,
              convert(w.goods_count, $fmt) as goods_count
            from t_wsp_bill_detail_ex w, t_goods g, t_goods_unit u
            where w.wspbill_id = '%s' and w.goods_id = g.id and g.unit_id = u.id
            order by w.show_order ";

    $data = $db->query($sql, $id);
    $itemsEx = [];
    foreach ($data as $v) {
      $itemsEx[] = [
        "goodsCode" => $v["code"],
        "goodsName" => $v["name"],
        "goodsSpec" => $v["spec"],
        "unitName" => $v["unit_name"],
        "goodsCount" => $v["goods_count"]
      ];
    }

    $bill["itemsEx"] = $itemsEx;

    return $bill;
  }

  /**
   * 从采购入库单生成拆分单，并提交拆分单
   *
   * @param string $pwBillId
   *        	采购入库单主表id
   *        	
   * @param string $loginUserId
   *        	当前登录用户的id
   *        	
   * @param string $wspBillRef
   *        	生成的拆分单的单号
   *        	
   * @return array|NULL
   */
  public function genWSPBillFromPWBillAndCommit($pwBillId, $loginUserId, &$wspBillRef)
  {
    $db = $this->db;

    $wspBillRef = null;

    // 检查采购入库单是否存在
    $sql = "select ref, bill_status, expand_by_bom, company_id,
              data_org, warehouse_id, biz_dt, biz_user_id 
            from t_pw_bill where id = '%s' ";
    $data = $db->query($sql, $pwBillId);
    if (!$data) {
      return $this->bad("采购入库单不存");
    }
    $v = $data[0];
    $expandByBOM = $v["expand_by_bom"];
    if ($expandByBOM != 1) {
      // 不处理
      return null;
    }
    $billStatus = $v["bill_status"];
    if ($billStatus != 1000) {
      return $this->bad("采购入库单不是提交状态");
    }
    $pwBillRef = $v["ref"];
    $companyId = $v["company_id"];
    $bizDT = $this->toYMD($v["biz_dt"]);
    $bizUserId = $v["biz_user_id"];
    $dataOrg = $v["data_org"];
    $warehouseId = $v["warehouse_id"];

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    // 检查采购入库单里面商品有没有子商品
    // 如果所有的商品都没有子商品，即使是expandByBOM == 1也不需要生成拆分单
    $sql = "select goods_id, convert(goods_count, $fmt) as goods_count
            from t_pw_bill_detail 
            where pwbill_id = '%s'
            order by show_order";
    $data = $db->query($sql, $pwBillId);
    $items = [];
    foreach ($data as $v) {
      $goodsId = $v["goods_id"];
      $goodsCount = $v["goods_count"];

      $sql = "select count(*) as cnt from t_goods_bom where goods_id = '%s' ";
      $d = $db->query($sql, $goodsId);
      $cnt = $d[0]["cnt"];
      if ($cnt > 0) {
        $items[] = [
          "goodsId" => $goodsId,
          "goodsCount" => $goodsCount
        ];
      }
    }

    if (count($items) == 0) {
      // 所有商品都没有子商品，不需要进一步的操作了
      return null;
    }

    // 生成拆分主表
    $id = $this->newId();
    $ref = $this->genNewBillRef($companyId);
    $fromWarehouseId = $warehouseId;
    $toWarehouseId = $warehouseId;
    $billMemo = "从采购入库单(单号：{$pwBillRef})生成";
    $sql = "insert into t_wsp_bill (id, ref, from_warehouse_id, to_warehouse_id,
              bill_status, bizdt, biz_user_id, date_created,
              input_user_id, data_org, company_id, bill_memo)
            values ('%s', '%s', '%s', '%s',
              0, '%s', '%s', now(),
              '%s', '%s', '%s', '%s')";
    $rc = $db->execute(
      $sql,
      $id,
      $ref,
      $fromWarehouseId,
      $toWarehouseId,
      $bizDT,
      $bizUserId,
      $loginUserId,
      $dataOrg,
      $companyId,
      $billMemo
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 生成拆分单明细
    foreach ($items as $showOrder => $v) {
      $goodsId = $v["goodsId"];
      if (!$goodsId) {
        continue;
      }

      $goodsCount = $v["goodsCount"];
      $memo = $billMemo;

      // 检查商品是否有子商品
      // 拆分单的明细表中不允许保存没有子商品的商品
      // 一个商品没有子商品，就不能做拆分业务
      $sql = "select count(*) as cnt from t_goods_bom where goods_id = '%s' ";
      $data = $db->query($sql, $goodsId);
      $cnt = $data[0]["cnt"];
      if ($cnt == 0) {
        $rowIndex = $showOrder + 1;
        return $this->bad("第{$rowIndex}记录中的商品没有子商品，不能做拆分业务");
      }

      // 检查拆分数量
      if ($goodsCount <= 0) {
        $rowIndex = $showOrder + 1;
        return $this->bad("第{$rowIndex}记录中的商品的拆分数量需要大于0");
      }

      $detailId = $this->newId();
      $sql = "insert into t_wsp_bill_detail (id, wspbill_id, show_order, goods_id,
                goods_count, date_created, data_org, company_id, memo)
              values ('%s', '%s', %d, '%s',
                convert(%f, $fmt), now(), '%s', '%s', '%s')";
      $rc = $db->execute(
        $sql,
        $detailId,
        $id,
        $showOrder,
        $goodsId,
        $goodsCount,
        $dataOrg,
        $companyId,
        $memo
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }

      // 复制当前商品构成BOM
      $this->copyGoodsBOM($detailId, $goodsId, $fmt);

      // 展开当前商品BOM
      $this->expandGoodsBOM($id, $fmt);
    }

    // 提交拆分单
    $params = [
      "id" => $id
    ];
    $rc = $this->commitWSPBill($params);
    if ($rc) {
      return $rc;
    }

    // 关联采购入库单和拆分单
    $sql = "update t_pw_bill
            set wspbill_id = '%s'
            where id = '%s' ";
    $rc = $db->execute($sql, $id, $pwBillId);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $wspBillRef = $ref;
    return null;
  }

  /**
   * 根据拆分单单号查询拆分单的完整数据，包括明细记录
   *
   * @param string $ref
   *        	拆分单单号
   * @return array|NULL
   */
  public function getFullBillDataByRef($ref)
  {
    $db = $this->db;

    $sql = "select w.id, w.bizdt,
              fw.name as from_warehouse_name,
              tw.name as to_warehouse_name,
              u.name as biz_user_name,
              w.bill_memo, w.company_id
            from t_wsp_bill w, t_warehouse fw, t_warehouse tw,
              t_user u
            where (w.from_warehouse_id = fw.id)
              and (w.to_warehouse_id = tw.id)
              and (w.biz_user_id = u.id)
              and w.ref = '%s' ";
    $data = $db->query($sql, $ref);
    if (!$data) {
      return null;
    }
    $v = $data[0];

    $companyId = $v["company_id"];

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $id = $v["id"];
    $result = [
      "ref" => $ref,
      "bizDT" => $this->toYMD($v["bizdt"]),
      "fromWarehouseName" => $v["from_warehouse_name"],
      "toWarehouseName" => $v["to_warehouse_name"],
      "bizUserName" => $v["biz_user_name"],
      "billMemo" => $v["bill_memo"]
    ];

    // 拆分前明细
    $sql = "select g.code, g.name, g.spec, u.name as unit_name,
              convert(w.goods_count, $fmt) as goods_count, w.memo
            from t_wsp_bill_detail w, t_goods g, t_goods_unit u
            where w.wspbill_id = '%s' and w.goods_id = g.id and g.unit_id = u.id
            order by w.show_order ";

    $data = $db->query($sql, $id);
    $items = [];
    foreach ($data as $v) {
      $items[] = [
        "goodsCode" => $v["code"],
        "goodsName" => $v["name"],
        "goodsSpec" => $v["spec"],
        "unitName" => $v["unit_name"],
        "goodsCount" => $v["goods_count"],
        "memo" => $v["memo"]
      ];
    }

    $result["items"] = $items;

    // 拆分后明细
    $sql = "select g.code, g.name, g.spec, u.name as unit_name,
              convert(w.goods_count, $fmt) as goods_count
            from t_wsp_bill_detail_ex w, t_goods g, t_goods_unit u
            where w.wspbill_id = '%s' and w.goods_id = g.id and g.unit_id = u.id
            order by w.show_order ";

    $data = $db->query($sql, $id);
    $itemsEx = [];
    foreach ($data as $v) {
      $itemsEx[] = [
        "goodsCode" => $v["code"],
        "goodsName" => $v["name"],
        "goodsSpec" => $v["spec"],
        "unitName" => $v["unit_name"],
        "goodsCount" => $v["goods_count"]
      ];
    }

    $result["itemsEx"] = $itemsEx;

    return $result;
  }
}
