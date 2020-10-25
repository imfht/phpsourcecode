<?php

namespace Home\DAO;

use Home\Common\FIdConst;

/**
 * 成品委托生产入库单 DAO
 *
 * @author 李静波
 */
class DMWBillDAO extends PSIBaseExDAO
{

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
        return "已退货";
      default:
        return "";
    }
  }

  /**
   * 生成新的成品委托生产入库单单号
   *
   * @param string $companyId
   * @return string
   */
  private function genNewBillRef($companyId)
  {
    $db = $this->db;

    $bs = new BizConfigDAO($db);
    $pre = $bs->getDMWBillRefPre($companyId);

    $mid = date("Ymd");

    $sql = "select ref from t_dmw_bill where ref like '%s' order by ref desc limit 1";
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

  public function getDMWBillById($id)
  {
    $db = $this->db;

    $sql = "select ref, bill_status, data_org, company_id
            from t_dmw_bill where id = '%s' ";
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
   * 成品委托生产入库单 - 单据详情
   */
  public function dmwBillInfo($params)
  {
    $db = $this->db;

    // 公司id
    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    // id: 成品委托生产入库单id
    $id = $params["id"];
    // dmobillRef: 成品委托生产订单单号，可以为空，为空表示直接录入入库单；不为空表示是从成品委托生产订单生成入库单
    $dmobillRef = $params["dmobillRef"];

    $result = [
      "id" => $id
    ];

    $sql = "select p.ref, p.bill_status, p.factory_id, f.name as factory_name,
              p.warehouse_id, w.name as  warehouse_name,
              p.biz_user_id, u.name as biz_user_name, p.biz_dt, p.payment_type,
              p.bill_memo
            from t_dmw_bill p, t_factory f, t_warehouse w, t_user u
            where p.id = '%s' and p.factory_id = f.id and p.warehouse_id = w.id
              and p.biz_user_id = u.id";
    $data = $db->query($sql, $id);
    if ($data) {
      $v = $data[0];
      $result["ref"] = $v["ref"];
      $result["billStatus"] = $v["bill_status"];
      $result["factoryId"] = $v["factory_id"];
      $result["factoryName"] = $v["factory_name"];
      $result["warehouseId"] = $v["warehouse_id"];
      $result["warehouseName"] = $v["warehouse_name"];
      $result["bizUserId"] = $v["biz_user_id"];
      $result["bizUserName"] = $v["biz_user_name"];
      $result["bizDT"] = $this->toYMD($v["biz_dt"]);
      $result["paymentType"] = $v["payment_type"];
      $result["billMemo"] = $v["bill_memo"];

      // 商品明细
      $items = [];
      $sql = "select p.id, p.goods_id, g.code, g.name, g.spec, u.name as unit_name,
                convert(p.goods_count, $fmt) as goods_count, p.goods_price, p.goods_money, p.memo,
                p.dmobilldetail_id, p.tax_rate, p.tax, p.money_with_tax, p.goods_price_with_tax
              from t_dmw_bill_detail p, t_goods g, t_goods_unit u
              where p.goods_Id = g.id and g.unit_id = u.id and p.dmwbill_id = '%s'
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
          "goodsPrice" => $v["goods_price"],
          "goodsMoney" => $v["goods_money"],
          "memo" => $v["memo"],
          "dmoBillDetailId" => $v["dmobilldetail_id"],
          "taxRate" => $v["tax_rate"],
          "tax" => $v["tax"],
          "moneyWithTax" => $v["money_with_tax"],
          "goodsPriceWithTax" => $goodsPriceWithTax
        ];
      }

      $result["items"] = $items;

      // 查询该单据是否是由成品委托生产订单生成的
      $sql = "select dmo_id from t_dmo_dmw where dmw_id = '%s' ";
      $data = $db->query($sql, $id);
      if ($data) {
        $result["genBill"] = true;
      } else {
        $result["genBill"] = false;
      }
    } else {
      // 新建成品委托生产入库单
      $result["bizUserId"] = $params["loginUserId"];
      $result["bizUserName"] = $params["loginUserName"];

      if ($dmobillRef) {
        // 由成品委托生产订单生成入库单
        $sql = "select p.id, p.factory_id, f.name as factory_name, p.deal_date,
                  p.payment_type, p.bill_memo
                from t_dmo_bill p, t_factory f
                where p.ref = '%s' and p.factory_id = f.id ";
        $data = $db->query($sql, $dmobillRef);
        if ($data) {
          $v = $data[0];
          $result["factoryId"] = $v["factory_id"];
          $result["factoryName"] = $v["factory_name"];
          $result["dealDate"] = $this->toYMD($v["deal_date"]);
          $result["paymentType"] = $v["payment_type"];
          $result["billMemo"] = $v["bill_memo"];

          $dmobillId = $v["id"];
          // 明细
          $items = [];
          $sql = "select p.id, p.goods_id, g.code, g.name, g.spec, u.name as unit_name,
                    convert(p.goods_count, $fmt) as goods_count,
                    p.goods_price, p.goods_money,
                    convert(p.left_count, $fmt) as left_count, p.memo, p.tax_rate,
                    p.goods_price_with_tax
                  from t_dmo_bill_detail p, t_goods g, t_goods_unit u
                  where p.dmobill_id = '%s' and p.goods_id = g.id and g.unit_id = u.id
                  order by p.show_order ";
          $data = $db->query($sql, $dmobillId);
          foreach ($data as $v) {
            $taxRate = $v["tax_rate"];

            $goodsMoney = $v["left_count"] * $v["goods_price"];
            $tax = $goodsMoney * $taxRate / 100;
            $moneyWithTax = $goodsMoney + $tax;

            $goodsPriceWithTax = $v["goods_price_with_tax"];
            if ($goodsPriceWithTax == null) {
              // 兼容旧数据
              if ($v["goods_count"] != 0) {
                $goodsPriceWithTax = $moneyWithTax / $v["goods_count"];
              }
            }

            $items[] = [
              "id" => $v["id"],
              "dmoBillDetailId" => $v["id"],
              "goodsId" => $v["goods_id"],
              "goodsCode" => $v["code"],
              "goodsName" => $v["name"],
              "goodsSpec" => $v["spec"],
              "unitName" => $v["unit_name"],
              "goodsCount" => $v["left_count"],
              "goodsPrice" => $v["goods_price"],
              "goodsMoney" => $v["left_count"] * $v["goods_price"],
              "memo" => $v["memo"],
              "taxRate" => $taxRate,
              "tax" => $tax,
              "moneyWithTax" => $moneyWithTax,
              "goodsPriceWithTax" => $goodsPriceWithTax
            ];
          }

          $result["items"] = $items;
        }
      }
    }

    return $result;
  }

  /**
   * 新建成品委托生产入库单
   *
   * @param array $bill
   * @return array|null
   */
  public function addDMWBill(&$bill)
  {
    $db = $this->db;

    // 业务日期
    $bizDT = $bill["bizDT"];

    // 仓库id
    $warehouseId = $bill["warehouseId"];

    // 工厂id
    $factoryId = $bill["factoryId"];

    // 业务员id
    $bizUserId = $bill["bizUserId"];

    // 付款方式
    // 0 - 应付账款
    $paymentType = 0;

    // 单据备注
    $billMemo = $bill["billMemo"];

    // 成品委托生产订单单号
    $dmobillRef = $bill["dmobillRef"];

    $warehouseDAO = new WarehouseDAO($db);
    $warehouse = $warehouseDAO->getWarehouseById($warehouseId);
    if (!$warehouse) {
      return $this->bad("入库仓库不存在");
    }

    $factoryDAO = new FactoryDAO($db);
    $factory = $factoryDAO->getFactoryById($factoryId);
    if (!$factory) {
      return $this->bad("工厂不存在");
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
    $sql = "insert into t_dmw_bill (id, ref, factory_id, warehouse_id, biz_dt,
              biz_user_id, bill_status, date_created, goods_money, input_user_id, payment_type,
              data_org, company_id, bill_memo)
            values ('%s', '%s', '%s', '%s', '%s', '%s', 0, now(), 0, '%s', %d, '%s', '%s', '%s')";

    $rc = $db->execute(
      $sql,
      $id,
      $ref,
      $factoryId,
      $warehouseId,
      $bizDT,
      $bizUserId,
      $loginUserId,
      $paymentType,
      $dataOrg,
      $companyId,
      $billMemo
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

      // 关于入库数量为什么允许填写0：
      // 当由订单生成入库单的时候，订单中有多种商品，但是是部分到货
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
      $dmoBillDetailId = $item["dmoBillDetailId"];

      $taxRate = $item["taxRate"];
      $tax = $item["tax"];
      $moneyWithTax = $item["moneyWithTax"];

      $sql = "insert into t_dmw_bill_detail
                (id, date_created, goods_id, goods_count, goods_price,
                goods_money,  dmwbill_id, show_order, data_org, memo, company_id,
                dmobilldetail_id, tax_rate, tax, money_with_tax, goods_price_with_tax)
              values ('%s', now(), '%s', convert(%f, $fmt), %f, %f, '%s', %d, '%s', '%s', '%s', '%s',
                %d, %f, %f, %f)";
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
        $dmoBillDetailId,
        $taxRate,
        $tax,
        $moneyWithTax,
        $goodsPriceWithTax
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 同步入库单主表中的采购金额合计值
    $sql = "select sum(goods_money) as goods_money, 
              sum(money_with_tax) as money_with_tax,
              sum(tax) as tax 
            from t_dmw_bill_detail
            where dmwbill_id = '%s' ";
    $data = $db->query($sql, $id);
    $totalMoney = $data[0]["goods_money"];
    if (!$totalMoney) {
      $totalMoney = 0;
    }
    $totalMoneyWithTax = $data[0]["money_with_tax"];
    $tax = $data[0]["tax"];

    $sql = "update t_dmw_bill
            set goods_money = %f, money_with_tax = %f, tax = %f
            where id = '%s' ";
    $rc = $db->execute($sql, $totalMoney, $totalMoneyWithTax, $tax, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    if ($dmobillRef) {
      // 从订单生成入库单
      $sql = "select id, company_id from t_dmo_bill where ref = '%s' ";
      $data = $db->query($sql, $dmobillRef);
      if (!$data) {
        // 传入了不存在的采购订单单号
        return $this->sqlError(__METHOD__, __LINE__);
      }
      $dmobillId = $data[0]["id"];
      $companyId = $data[0]["company_id"];

      $sql = "update t_dmw_bill
              set company_id = '%s'
              where id = '%s' ";
      $rc = $db->execute($sql, $companyId, $id);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }

      // 关联成品委托生产订单和成品委托生产入库单
      $sql = "insert into t_dmo_dmw(dmo_id, dmw_id) values('%s', '%s')";
      $rc = $db->execute($sql, $dmobillId, $id);
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
   * 编辑成品委托生产入库单
   *
   * @param array $bill
   * @return array|null
   */
  public function updateDMWBill(&$bill)
  {
    $db = $this->db;

    // 成品委托生产入库单id
    $id = $bill["id"];

    // 业务日期
    $bizDT = $bill["bizDT"];

    // 仓库id
    $warehouseId = $bill["warehouseId"];

    // 工厂id
    $factoryId = $bill["factoryId"];

    // 业务员id
    $bizUserId = $bill["bizUserId"];

    // 付款方式
    $paymentType = $bill["paymentType"];

    // 备注
    $billMemo = $bill["billMemo"];

    $warehouseDAO = new WarehouseDAO($db);
    $warehouse = $warehouseDAO->getWarehouseById($warehouseId);
    if (!$warehouse) {
      return $this->bad("入库仓库不存在");
    }

    $factoryDAO = new FactoryDAO($db);
    $factory = $factoryDAO->getFactoryById($factoryId);
    if (!$factory) {
      return $this->bad("工厂不存在");
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

    $oldBill = $this->getDMWBillById($id);
    if (!$oldBill) {
      return $this->bad("要编辑的成品委托生产入库单不存在");
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
      return $this->bad("当前成品委托生产入库单已经提交入库，不能再编辑");
    }
    $bill["ref"] = $ref;

    // 编辑单据的时候，先删除原来的明细记录，再新增明细记录
    $sql = "delete from t_dmw_bill_detail where dmwbill_id = '%s' ";
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

      // 关于入库数量为什么允许填写0：
      // 当由成品委托生产订单生成入库单的时候，订单中有多种商品，但是是部分到货
      // 那么就存在有些商品的数量是0的情形。
      $goodsCount = $item["goodsCount"];
      if ($goodsCount < 0) {
        return $this->bad("入库数量不能是负数");
      }

      // 入库明细记录的备注
      $memo = $item["memo"];

      // 单价
      $goodsPrice = $item["goodsPrice"];

      // 含税价
      $goodsPriceWithTax = $item["goodsPriceWithTax"];

      // 金额
      $goodsMoney = $item["goodsMoney"];

      // 成品委托生产订单明细记录id
      $dmoBillDetailId = $item["dmoBillDetailId"];

      $taxRate = $item["taxRate"];
      $tax = $item["tax"];
      $moneyWithTax = $item["moneyWithTax"];

      $sql = "insert into t_dmw_bill_detail (id, date_created, goods_id, goods_count, goods_price,
                goods_money,  dmwbill_id, show_order, data_org, memo, company_id, dmobilldetail_id,
                tax_rate, tax, money_with_tax, goods_price_with_tax)
              values ('%s', now(), '%s', convert(%f, $fmt), %f, %f, '%s', %d, '%s', '%s', '%s', '%s',
                %d, %f, %f, %f)";
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
        $dmoBillDetailId,
        $taxRate,
        $tax,
        $moneyWithTax,
        $goodsPriceWithTax
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 同步主表数据
    $sql = "select sum(goods_money) as goods_money, 
              sum(tax) as tax,
              sum(money_with_tax) as money_with_tax 
            from t_dmw_bill_detail
            where dmwbill_id = '%s' ";
    $data = $db->query($sql, $id);
    $totalMoney = $data[0]["goods_money"];
    if (!$totalMoney) {
      $totalMoney = 0;
    }
    $tax = $data[0]["tax"];
    $totalMoneyWithTax = $data[0]["money_with_tax"];

    $sql = "update t_dmw_bill
            set goods_money = %f, warehouse_id = '%s',
              factory_id = '%s', biz_dt = '%s',
              biz_user_id = '%s', payment_type = %d,
              bill_memo = '%s', money_with_tax = %f,
              tax = %f
            where id = '%s' ";
    $rc = $db->execute(
      $sql,
      $totalMoney,
      $warehouseId,
      $factoryId,
      $bizDT,
      $bizUserId,
      $paymentType,
      $billMemo,
      $totalMoneyWithTax,
      $tax,
      $id
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    return null;
  }

  /**
   * 获得成品委托生产入库单主表列表
   */
  public function dmwbillList($params)
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

    // 工厂id
    $factoryId = $params["factoryId"];

    $goodsId = $params["goodsId"];

    $loginUserId = $params["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->emptyResult();
    }

    $queryParams = [];
    $sql = "select d.id, d.bill_status, d.ref, d.biz_dt, u1.name as biz_user_name, u2.name as input_user_name,
              d.goods_money, w.name as warehouse_name, f.name as factory_name,
              d.date_created, d.payment_type, d.bill_memo, d.tax, d.money_with_tax
            from t_dmw_bill d, t_warehouse w, t_factory f, t_user u1, t_user u2
            where (d.warehouse_id = w.id) and (d.factory_id = f.id)
              and (d.biz_user_id = u1.id) and (d.input_user_id = u2.id) ";

    $ds = new DataOrgDAO($db);
    // 构建数据域SQL
    $rs = $ds->buildSQL(FIdConst::DMW, "d", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = $rs[1];
    }

    if ($billStatus != -1) {
      $sql .= " and (d.bill_status = %d) ";
      $queryParams[] = $billStatus;
    }
    if ($ref) {
      $sql .= " and (d.ref like '%s') ";
      $queryParams[] = "%{$ref}%";
    }
    if ($fromDT) {
      $sql .= " and (d.biz_dt >= '%s') ";
      $queryParams[] = $fromDT;
    }
    if ($toDT) {
      $sql .= " and (d.biz_dt <= '%s') ";
      $queryParams[] = $toDT;
    }
    if ($factoryId) {
      $sql .= " and (d.factory_id = '%s') ";
      $queryParams[] = $factoryId;
    }
    if ($warehouseId) {
      $sql .= " and (d.warehouse_id = '%s') ";
      $queryParams[] = $warehouseId;
    }
    if ($goodsId) {
      $sql .= " and (d.id in (select distinct dmwbill_id from t_dmw_bill_detail where goods_id = '%s')) ";
      $queryParams[] = $goodsId;
    }

    $sql .= " order by d.biz_dt desc, d.ref desc
				limit %d, %d";
    $queryParams[] = $start;
    $queryParams[] = $limit;
    $data = $db->query($sql, $queryParams);
    $result = [];

    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "ref" => $v["ref"],
        "bizDate" => $this->toYMD($v["biz_dt"]),
        "factoryName" => $v["factory_name"],
        "warehouseName" => $v["warehouse_name"],
        "inputUserName" => $v["input_user_name"],
        "bizUserName" => $v["biz_user_name"],
        "billStatus" => $this->billStatusCodeToName($v["bill_status"]),
        "amount" => $v["goods_money"],
        "dateCreated" => $v["date_created"],
        "paymentType" => $v["payment_type"],
        "billMemo" => $v["bill_memo"],
        "tax" => $v["tax"],
        "moneyWithTax" => $v["money_with_tax"]
      ];
    }

    $sql = "select count(*) as cnt
            from t_dmw_bill d, t_warehouse w, t_factory f, t_user u1, t_user u2
            where (d.warehouse_id = w.id) and (d.factory_id = f.id)
              and (d.biz_user_id = u1.id) and (d.input_user_id = u2.id)";
    $queryParams = [];
    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::DMW, "d", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = $rs[1];
    }
    if ($billStatus != -1) {
      $sql .= " and (d.bill_status = %d) ";
      $queryParams[] = $billStatus;
    }
    if ($ref) {
      $sql .= " and (d.ref like '%s') ";
      $queryParams[] = "%{$ref}%";
    }
    if ($fromDT) {
      $sql .= " and (d.biz_dt >= '%s') ";
      $queryParams[] = $fromDT;
    }
    if ($toDT) {
      $sql .= " and (d.biz_dt <= '%s') ";
      $queryParams[] = $toDT;
    }
    if ($factoryId) {
      $sql .= " and (d.factory_id = '%s') ";
      $queryParams[] = $factoryId;
    }
    if ($warehouseId) {
      $sql .= " and (d.warehouse_id = '%s') ";
      $queryParams[] = $warehouseId;
    }
    if ($goodsId) {
      $sql .= " and (d.id in (select distinct dmwbill_id from t_dmw_bill_detail where goods_id = '%s')) ";
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
   * 获得成品委托生产入库单的明细记录
   */
  public function dmwBillDetailList($params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    // 成品委托生产入库单id
    $id = $params["id"];

    $sql = "select p.id, g.code, g.name, g.spec, u.name as unit_name,
              convert(p.goods_count, $fmt) as goods_count, p.goods_price,
              p.goods_money, p.memo, p.tax_rate, p.tax, p.money_with_tax,
              p.goods_price_with_tax
            from t_dmw_bill_detail p, t_goods g, t_goods_unit u
            where p.dmwbill_id = '%s' and p.goods_id = g.id and g.unit_id = u.id
            order by p.show_order ";
    $data = $db->query($sql, $id);
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
        "goodsMoney" => $v["goods_money"],
        "goodsPrice" => $v["goods_price"],
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
   * 删除成品委托生产入库单
   */
  public function deleteDMWBill(&$params)
  {
    $db = $this->db;

    // 成品委托生产入库单id
    $id = $params["id"];

    $bill = $this->getDMWBillById($id);
    if (!$bill) {
      return $this->bad("要删除的成品委托生产入库单不存在");
    }

    // 单号
    $ref = $bill["ref"];

    // 单据状态
    $billStatus = $bill["billStatus"];
    if ($billStatus != 0) {
      return $this->bad("当前采购入库单已经提交入库，不能删除");
    }

    // 先删除明细记录
    $sql = "delete from t_dmw_bill_detail where dmwbill_id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 再删除主表
    $sql = "delete from t_dmw_bill where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 删除从成品委托生产订单生成的记录
    $sql = "delete from t_dmo_dmw where dmw_id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $params["ref"] = $ref;
    return null;
  }

  /**
   * 通过单号查询成品委托生产入库的完整信息，包括明细入库记录
   *
   * @param string $ref
   *        	成品委托生产入库单单号
   * @return array|NULL
   */
  public function getFullBillDataByRef($ref)
  {
    $db = $this->db;

    $sql = "select p.id, f.name as factory_name,
              w.name as  warehouse_name,
              u.name as biz_user_name, p.biz_dt, p.company_id
            from t_dmw_bill p, t_factory f, t_warehouse w, t_user u
            where p.ref = '%s' and p.factory_id = f.id and p.warehouse_id = w.id
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
      "factoryName" => $v["factory_name"],
      "warehouseName" => $v["warehouse_name"],
      "bizUserName" => $v["biz_user_name"],
      "bizDT" => $this->toYMD($v["biz_dt"])
    ];

    // 明细记录
    $items = [];
    $sql = "select p.id, p.goods_id, g.code, g.name, g.spec, u.name as unit_name,
              convert(p.goods_count, $fmt) as goods_count, p.goods_price, p.goods_money, p.memo
            from t_dmw_bill_detail p, t_goods g, t_goods_unit u
            where p.goods_Id = g.id and g.unit_id = u.id and p.dmwbill_id = '%s'
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
        "memo" => $v["memo"]
      ];
    }

    $result["items"] = $items;

    return $result;
  }

  /**
   * 查询成品委托生产入库单的数据，用于生成PDF文件
   *
   * @param array $params
   *
   * @return NULL|array
   */
  public function getDataForPDF($params)
  {
    $db = $this->db;

    $ref = $params["ref"];

    $sql = "select p.id, p.bill_status, p.ref, p.biz_dt, u1.name as biz_user_name, u2.name as input_user_name,
              p.goods_money, w.name as warehouse_name, f.name as factory_name,
              p.date_created, p.payment_type, p.company_id, p.money_with_tax
            from t_dmw_bill p, t_warehouse w, t_factory f, t_user u1, t_user u2
            where (p.warehouse_id = w.id) and (p.factory_id = f.id)
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
    $result["factoryName"] = $v["factory_name"];
    $result["goodsMoney"] = $v["goods_money"];
    $result["bizDT"] = $this->toYMD($v["biz_dt"]);
    $result["warehouseName"] = $v["warehouse_name"];
    $result["bizUserName"] = $v["biz_user_name"];
    $result["moneyWithTax"] = $v["money_with_tax"];

    $sql = "select g.code, g.name, g.spec, u.name as unit_name,
              convert(p.goods_count, $fmt) as goods_count, p.goods_price,
              p.goods_money, p.tax_rate, p.money_with_tax
            from t_dmw_bill_detail p, t_goods g, t_goods_unit u
            where p.dmwbill_id = '%s' and p.goods_id = g.id and g.unit_id = u.id
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
        "goodsPrice" => $v["goods_price"],
        "goodsMoney" => $v["goods_money"],
        "taxRate" => intval($v["tax_rate"]),
        "moneyWithTax" => $v["money_with_tax"]
      ];
    }

    $result["items"] = $items;

    return $result;
  }

  /**
   * 提交成品委托生产入库单
   */
  public function commitDMWBill(&$params)
  {
    $db = $this->db;

    // 成品委托生产入库单id
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

    $sql = "select ref, warehouse_id, bill_status, biz_dt, biz_user_id,  
              money_with_tax, factory_id,
              payment_type, company_id
            from t_dmw_bill
            where id = '%s' ";
    $data = $db->query($sql, $id);

    if (!$data) {
      return $this->bad("要提交的成品委托生产入库单不存在");
    }
    $billStatus = $data[0]["bill_status"];
    if ($billStatus != 0) {
      return $this->bad("成品委托生产入库单已经提交入库，不能再次提交");
    }

    $ref = $data[0]["ref"];
    $bizDT = $data[0]["biz_dt"];
    $bizUserId = $data[0]["biz_user_id"];

    // 应付总额
    $billPayables = $data[0]["money_with_tax"];

    $factoryId = $data[0]["factory_id"];
    $warehouseId = $data[0]["warehouse_id"];
    $paymentType = $data[0]["payment_type"];
    $companyId = $data[0]["company_id"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->badParam("companyId");
    }

    $bc = new BizConfigDAO($db);
    // true: 先进先出法
    $fifo = $bc->getInventoryMethod($companyId) == 1;

    // 检查仓库
    $warehouseDAO = new WarehouseDAO($db);
    $warehouse = $warehouseDAO->getWarehouseById($warehouseId);
    if (!$warehouse) {
      return $this->bad("要入库的仓库不存在");
    }
    $inited = $warehouse["inited"];
    if ($inited == 0) {
      return $this->bad("仓库 [{$warehouse['name']}] 还没有完成建账，不能做成品委托生产入库的操作");
    }

    // 检查工厂
    $factoryDAO = new FactoryDAO($db);
    $factory = $factoryDAO->getFactoryById($factoryId);
    if (!$factory) {
      return $this->bad("工厂不存在");
    }

    // 检查业务员是否存在
    $userDAO = new UserDAO($db);
    $user = $userDAO->getUserById($bizUserId);
    if (!$user) {
      return $this->bad("业务员不存在");
    }

    $sql = "select goods_id, convert(goods_count, $fmt) as goods_count, goods_price, goods_money, id,
              dmobilldetail_id
            from t_dmw_bill_detail
            where dmwbill_id = '%s' order by show_order";
    $items = $db->query($sql, $id);
    if (!$items) {
      return $this->bad("成品委托生产入库单没有明细记录，不能入库");
    }

    // 检查入库数量、单价、金额不能为负数
    foreach ($items as $v) {
      $goodsCount = $v["goods_count"];
      if ($goodsCount < 0) {
        return $this->bad("数量不能小于0");
      }
      $goodsPrice = floatval($v["goods_price"]);
      if ($goodsPrice < 0) {
        return $this->bad("单价不能为负数");
      }
      $goodsMoney = floatval($v["goods_money"]);
      if ($goodsMoney < 0) {
        return $this->bad("金额不能为负数");
      }
    }

    $allPaymentType = [
      0
    ];
    if (!in_array($paymentType, $allPaymentType)) {
      return $this->bad("付款方式填写不正确，无法提交");
    }

    // 遍历明细，更改库存账记录
    foreach ($items as $v) {
      $dmobillDetailId = $v["dmobilldetail_id"];

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
        // 首次入库
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
                now(), '%s', '成品委托生产入库')";
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
        // 目前的版本暂时不处理先进先出法
      }

      // 同步成品委托生产订单中的到货情况
      $sql = "select convert(goods_count, $fmt) as goods_count,
                convert(dmw_count, $fmt) as dmw_count
              from t_dmo_bill_detail
              where id = '%s' ";
      $dmoDetail = $db->query($sql, $dmobillDetailId);
      if (!$dmoDetail) {
        // 当前入库单不是由成品委托生产订单创建的
        continue;
      }

      $totalGoodsCount = $dmoDetail[0]["goods_count"];
      $totalDMWCount = $dmoDetail[0]["dmw_count"];
      $totalDMWCount += $goodsCount;
      $totalLeftCount = $totalGoodsCount - $totalDMWCount;

      $sql = "update t_dmo_bill_detail
              set dmw_count = convert(%f, $fmt), left_count = convert(%f, $fmt)
              where id = '%s' ";
      $rc = $db->execute($sql, $totalDMWCount, $totalLeftCount, $dmobillDetailId);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 修改本单据状态为已入库
    $sql = "update t_dmw_bill set bill_status = 1000 where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 同步成品委托生产订单的状态
    $sql = "select dmo_id
            from t_dmo_dmw
            where dmw_id = '%s' ";
    $data = $db->query($sql, $id);
    if ($data) {
      $dmoBillId = $data[0]["dmo_id"];

      $sql = "select count(*) as cnt from t_dmo_bill_detail
              where dmobill_id = '%s' and convert(left_count, $fmt) > 0 ";
      $data = $db->query($sql, $dmoBillId);
      $cnt = $data[0]["cnt"];
      $billStatus = 1000;
      if ($cnt > 0) {
        // 部分入库
        $billStatus = 2000;
      } else {
        // 全部入库
        $billStatus = 3000;
      }
      $sql = "update t_dmo_bill
              set bill_status = %d
              where id = '%s' ";
      $rc = $db->execute($sql, $billStatus, $dmoBillId);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    if ($paymentType == 0) {
      // 记应付账款
      // 应付明细账
      $sql = "insert into t_payables_detail (id, pay_money, act_money, balance_money,
                ca_id, ca_type, date_created, ref_number, ref_type, biz_date, company_id)
              values ('%s', %f, 0, %f, '%s', 'factory', now(), '%s', '成品委托生产入库', '%s', '%s')";
      $rc = $db->execute(
        $sql,
        $this->newId(),
        $billPayables,
        $billPayables,
        $factoryId,
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
              where ca_id = '%s' and ca_type = 'factory' and company_id = '%s' ";
      $data = $db->query($sql, $factoryId, $companyId);
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
        // 首次记录应付账款

        $payMoney = $billPayables;

        $sql = "insert into t_payables (id, pay_money, act_money, balance_money,
                  ca_id, ca_type, company_id)
                values ('%s', %f, 0, %f, '%s', 'factory', '%s')";
        $rc = $db->execute(
          $sql,
          $this->newId(),
          $payMoney,
          $payMoney,
          $factoryId,
          $companyId
        );
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      }
    }

    // 操作成功
    $params["ref"] = $ref;
    return null;
  }

  /**
   * 生成打印成品委托生产入库单的页面
   *
   * @param array $params
   */
  public function getDMWBillDataForLodopPrint($params)
  {
    $db = $this->db;

    $id = $params["id"];

    $sql = "select p.ref, p.bill_status, p.ref, p.biz_dt, u1.name as biz_user_name, u2.name as input_user_name,
              p.goods_money, w.name as warehouse_name, f.name as factory_name,
              p.date_created, p.payment_type, p.company_id, p.bill_memo,
              p.money_with_tax
            from t_dmw_bill p, t_warehouse w, t_factory f, t_user u1, t_user u2
            where (p.warehouse_id = w.id) and (p.factory_id = f.id)
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
    $result["factoryName"] = $v["factory_name"];
    $result["goodsMoney"] = $v["goods_money"];
    $result["bizDT"] = $this->toYMD($v["biz_dt"]);
    $result["warehouseName"] = $v["warehouse_name"];
    $result["bizUserName"] = $v["biz_user_name"];
    $result["billMemo"] = $v["bill_memo"];
    $result["moneyWithTax"] = $v["money_with_tax"];

    $result["printDT"] = date("Y-m-d H:i:s");

    $sql = "select g.code, g.name, g.spec, u.name as unit_name,
              convert(p.goods_count, $fmt) as goods_count, p.goods_price,
              p.goods_money, p.memo, p.tax_rate, p.money_with_tax
            from t_dmw_bill_detail p, t_goods g, t_goods_unit u
            where p.dmwbill_id = '%s' and p.goods_id = g.id and g.unit_id = u.id
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
        "goodsPrice" => $v["goods_price"],
        "goodsMoney" => $v["goods_money"],
        "memo" => $v["memo"],
        "taxRate" => intval($v["tax_rate"]),
        "moneyWithTax" => $v["money_with_tax"]
      ];
    }

    $result["items"] = $items;

    return $result;
  }
}
