<?php

namespace Home\DAO;

use Home\Common\FIdConst;

/**
 * 成品委托生产订单 DAO
 *
 * @author 李静波
 */
class DMOBillDAO extends PSIBaseExDAO
{

  /**
   * 生成新的成品委托生产订单号
   *
   * @param string $companyId
   * @return string
   */
  private function genNewBillRef($companyId)
  {
    $db = $this->db;

    $bs = new BizConfigDAO($db);
    $pre = $bs->getDMOBillRefPre($companyId);

    $mid = date("Ymd");

    $sql = "select ref from t_dmo_bill where ref like '%s' order by ref desc limit 1";
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
   * 获得成品委托生产订单的信息
   */
  public function dmoBillInfo($params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    // 订单id
    $id = $params["id"];

    $result = [];

    $bcDAO = new BizConfigDAO($db);

    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    if ($id) {
      // 编辑采购订单
      $sql = "select p.ref, p.deal_date, p.deal_address, p.factory_id,
                f.name as factory_name, p.contact, p.tel, p.fax,
                p.org_id, o.full_name, p.biz_user_id, u.name as biz_user_name,
                p.payment_type, p.bill_memo, p.bill_status
              from t_dmo_bill p, t_factory f, t_user u, t_org o
              where p.id = '%s' and p.factory_id = f.id
                and p.biz_user_id = u.id
                and p.org_id = o.id";
      $data = $db->query($sql, $id);
      if ($data) {
        $v = $data[0];
        $result["ref"] = $v["ref"];
        $result["dealDate"] = $this->toYMD($v["deal_date"]);
        $result["dealAddress"] = $v["deal_address"];
        $result["factoryId"] = $v["factory_id"];
        $result["factoryName"] = $v["factory_name"];
        $result["contact"] = $v["contact"];
        $result["tel"] = $v["tel"];
        $result["fax"] = $v["fax"];
        $result["orgId"] = $v["org_id"];
        $result["orgFullName"] = $v["full_name"];
        $result["bizUserId"] = $v["biz_user_id"];
        $result["bizUserName"] = $v["biz_user_name"];
        $result["paymentType"] = $v["payment_type"];
        $result["billMemo"] = $v["bill_memo"];
        $result["billStatus"] = $v["bill_status"];

        // 明细表
        $sql = "select p.id, p.goods_id, g.code, g.name, g.spec,
                  convert(p.goods_count, " . $fmt . ") as goods_count,
                  p.goods_price, p.goods_money,
                  p.tax_rate, p.tax, p.money_with_tax, u.name as unit_name, p.memo,
                  p.goods_price_with_tax
                from t_dmo_bill_detail p, t_goods g, t_goods_unit u
                where p.dmobill_id = '%s' and p.goods_id = g.id and g.unit_id = u.id
                order by p.show_order";
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
      }
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
   * 新建成品委托生产订单
   *
   * @param array $bill
   * @return array|null
   */
  public function addDMOBill(&$bill)
  {
    $db = $this->db;

    $dealDate = $bill["dealDate"];
    $factoryId = $bill["factoryId"];
    $orgId = $bill["orgId"];
    $bizUserId = $bill["bizUserId"];

    // 付款方式：目前只处理应付账款
    $paymentType = 0;

    $contact = $bill["contact"];
    $tel = $bill["tel"];
    $fax = $bill["fax"];
    $dealAddress = $bill["dealAddress"];
    $billMemo = $bill["billMemo"];

    $items = $bill["items"];

    $dataOrg = $bill["dataOrg"];
    $loginUserId = $bill["loginUserId"];
    $companyId = $bill["companyId"];
    if ($this->dataOrgNotExists($dataOrg)) {
      return $this->badParam("dataOrg");
    }
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->badParam("loginUserId");
    }
    if ($this->companyIdNotExists($companyId)) {
      return $this->badParam("companyId");
    }

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    if (!$this->dateIsValid($dealDate)) {
      return $this->bad("交货日期不正确");
    }

    $factoryDAO = new FactoryDAO($db);
    $factory = $factoryDAO->getFactoryById($factoryId);
    if (!$factory) {
      return $this->bad("工厂不存在");
    }

    $orgDAO = new OrgDAO($db);
    $org = $orgDAO->getOrgById($orgId);
    if (!$org) {
      return $this->bad("组织机构不存在");
    }

    $userDAO = new UserDAO($db);
    $user = $userDAO->getUserById($bizUserId);
    if (!$user) {
      return $this->bad("业务员不存在");
    }

    $id = $this->newId();
    $ref = $this->genNewBillRef($companyId);

    // 主表
    $sql = "insert into t_dmo_bill(id, ref, bill_status, deal_date, biz_dt, org_id, biz_user_id,
              goods_money, tax, money_with_tax, input_user_id, factory_id, contact, tel, fax,
              deal_address, bill_memo, payment_type, date_created, data_org, company_id)
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
      $factoryId,
      $contact,
      $tel,
      $fax,
      $dealAddress,
      $billMemo,
      $paymentType,
      $dataOrg,
      $companyId
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 明细表
    $goodsDAO = new GoodsDAO($db);
    foreach ($items as $i => $v) {
      $goodsId = $v["goodsId"];
      if (!$goodsId) {
        continue;
      }
      $goods = $goodsDAO->getGoodsById($goodsId);
      if (!$goods) {
        continue;
      }

      $goodsCount = $v["goodsCount"];
      if ($goodsCount <= 0) {
        return $this->bad("生产数量需要大于0");
      }

      $goodsPrice = $v["goodsPrice"];
      if ($goodsPrice < 0) {
        return $this->bad("单价不能是负数");
      }

      $goodsPriceWithTax = $v["goodsPriceWithTax"];

      $goodsMoney = $v["goodsMoney"];
      $taxRate = $v["taxRate"];
      $tax = $v["tax"];
      $moneyWithTax = $v["moneyWithTax"];
      $memo = $v["memo"];

      $sql = "insert into t_dmo_bill_detail(id, date_created, goods_id, goods_count, goods_money,
                goods_price, dmobill_id, tax_rate, tax, money_with_tax, dmw_count, left_count,
                show_order, data_org, company_id, memo, goods_price_with_tax)
              values ('%s', now(), '%s', convert(%f, $fmt), %f,
                %f, '%s', %d, %f, %f, 0, convert(%f, $fmt), %d, '%s', '%s', '%s', %f)";
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
        $goodsPriceWithTax
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 同步主表的金额合计字段
    $sql = "select sum(goods_money) as sum_goods_money, sum(tax) as sum_tax,
              sum(money_with_tax) as sum_money_with_tax
            from t_dmo_bill_detail
            where dmobill_id = '%s' ";
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

    $sql = "update t_dmo_bill
            set goods_money = %f, tax = %f, money_with_tax = %f
            where id = '%s' ";
    $rc = $db->execute($sql, $sumGoodsMoney, $sumTax, $sumMoneyWithTax, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $bill["id"] = $id;
    $bill["ref"] = $ref;

    // 操作成功
    return null;
  }

  public function getDMOBillById($id)
  {
    $db = $this->db;

    $sql = "select ref, bill_status from t_dmo_bill where id = '%s' ";
    $data = $db->query($sql, $id);
    if ($data) {
      return [
        "id" => $id,
        "ref" => $data[0]["ref"],
        "billStatus" => $data[0]["bill_status"]
      ];
    } else {
      return null;
    }
  }

  /**
   * 编辑成品委托生产订单
   *
   * @param array $bill
   * @return array|null
   */
  public function updateDMOBill(&$bill)
  {
    $db = $this->db;

    $id = $bill["id"];

    $oldBill = $this->getDMOBillById($id);
    if (!$oldBill) {
      return $this->bad("要编辑的成品委托生产订单不存在");
    }
    $ref = $oldBill["ref"];
    $billStatus = $oldBill["billStatus"];
    if ($billStatus > 0) {
      return $this->bad("当前成品委托生产订单已经审核，不能再被编辑");
    }

    $dealDate = $bill["dealDate"];
    $factoryId = $bill["factoryId"];
    $orgId = $bill["orgId"];
    $bizUserId = $bill["bizUserId"];

    // 付款方式：目前只处理应付账款
    $paymentType = 0;

    $contact = $bill["contact"];
    $tel = $bill["tel"];
    $fax = $bill["fax"];
    $dealAddress = $bill["dealAddress"];
    $billMemo = $bill["billMemo"];

    $items = $bill["items"];

    $dataOrg = $bill["dataOrg"];
    $loginUserId = $bill["loginUserId"];
    $companyId = $bill["companyId"];
    if ($this->dataOrgNotExists($dataOrg)) {
      return $this->badParam("dataOrg");
    }
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->badParam("loginUserId");
    }
    if ($this->companyIdNotExists($companyId)) {
      return $this->badParam("companyId");
    }

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    if (!$this->dateIsValid($dealDate)) {
      return $this->bad("交货日期不正确");
    }

    $factoryDAO = new FactoryDAO($db);
    $factory = $factoryDAO->getFactoryById($factoryId);
    if (!$factory) {
      return $this->bad("工厂不存在");
    }

    $orgDAO = new OrgDAO($db);
    $org = $orgDAO->getOrgById($orgId);
    if (!$org) {
      return $this->bad("组织机构不存在");
    }

    $userDAO = new UserDAO($db);
    $user = $userDAO->getUserById($bizUserId);
    if (!$user) {
      return $this->bad("业务员不存在");
    }

    // 明细表

    // 先清空旧数据
    $sql = "delete from t_dmo_bill_detail where dmobill_id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 插入新明细数据
    $goodsDAO = new GoodsDAO($db);
    foreach ($items as $i => $v) {
      $goodsId = $v["goodsId"];
      if (!$goodsId) {
        continue;
      }
      $goods = $goodsDAO->getGoodsById($goodsId);
      if (!$goods) {
        continue;
      }

      $goodsCount = $v["goodsCount"];
      if ($goodsCount <= 0) {
        return $this->bad("生产数量需要大于0");
      }

      $goodsPrice = $v["goodsPrice"];
      if ($goodsPrice < 0) {
        return $this->bad("单价不能是负数");
      }

      $goodsPriceWithTax = $v["goodsPriceWithTax"];

      $goodsMoney = $v["goodsMoney"];
      $taxRate = $v["taxRate"];
      $tax = $v["tax"];
      $moneyWithTax = $v["moneyWithTax"];
      $memo = $v["memo"];

      $sql = "insert into t_dmo_bill_detail(id, date_created, goods_id, goods_count, goods_money,
                goods_price, dmobill_id, tax_rate, tax, money_with_tax, dmw_count, left_count,
                show_order, data_org, company_id, memo, goods_price_with_tax)
              values ('%s', now(), '%s', convert(%f, $fmt), %f,
                %f, '%s', %d, %f, %f, 0, convert(%f, $fmt), %d, '%s', '%s', '%s', %f)";
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
        $goodsPriceWithTax
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 同步主表的金额合计字段
    $sql = "select sum(goods_money) as sum_goods_money, sum(tax) as sum_tax,
              sum(money_with_tax) as sum_money_with_tax
            from t_dmo_bill_detail
            where dmobill_id = '%s' ";
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

    // 主表
    $sql = "update t_dmo_bill
            set goods_money = %f, tax = %f, money_with_tax = %f,
              deal_date = '%s', factory_id = '%s',
              deal_address = '%s', contact = '%s', tel = '%s', fax = '%s',
              org_id = '%s', biz_user_id = '%s', payment_type = %d,
              bill_memo = '%s', input_user_id = '%s', date_created = now()
            where id = '%s' ";
    $rc = $db->execute(
      $sql,
      $sumGoodsMoney,
      $sumTax,
      $sumMoneyWithTax,
      $dealDate,
      $factoryId,
      $dealAddress,
      $contact,
      $tel,
      $fax,
      $orgId,
      $bizUserId,
      $paymentType,
      $billMemo,
      $loginUserId,
      $id
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $bill["ref"] = $ref;
    return null;
  }

  /**
   * 获得成品委托生产订单主表信息列表
   */
  public function dmobillList($params)
  {
    $db = $this->db;

    $start = $params["start"];
    $limit = $params["limit"];

    $billStatus = $params["billStatus"];
    $ref = $params["ref"];
    $fromDT = $params["fromDT"];
    $toDT = $params["toDT"];
    $factoryId = $params["factoryId"];
    $goodsId = $params["goodsId"];

    $loginUserId = $params["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->emptyResult();
    }

    $queryParams = [];

    $result = [];
    $sql = "select d.id, d.ref, d.bill_status, d.goods_money, d.tax, d.money_with_tax,
              f.name as factory_name, d.contact, d.tel, d.fax, d.deal_address,
              d.deal_date, d.payment_type, d.bill_memo, d.date_created,
              o.full_name as org_name, u1.name as biz_user_name, u2.name as input_user_name,
              d.confirm_user_id, d.confirm_date
            from t_dmo_bill d, t_factory f, t_org o, t_user u1, t_user u2
            where (d.factory_id = f.id) and (d.org_id = o.id)
              and (d.biz_user_id = u1.id) and (d.input_user_id = u2.id) ";

    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::DMO, "d", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = $rs[1];
    }

    if ($billStatus != -1) {
      if ($billStatus < 4000) {
        $sql .= " and (d.bill_status = %d) ";
      } else {
        // 订单关闭 - 有多种状态
        $sql .= " and (d.bill_status >= %d) ";
      }
      $queryParams[] = $billStatus;
    }
    if ($ref) {
      $sql .= " and (d.ref like '%s') ";
      $queryParams[] = "%$ref%";
    }
    if ($fromDT) {
      $sql .= " and (d.deal_date >= '%s')";
      $queryParams[] = $fromDT;
    }
    if ($toDT) {
      $sql .= " and (d.deal_date <= '%s')";
      $queryParams[] = $toDT;
    }
    if ($factoryId) {
      $sql .= " and (d.factory_id = '%s')";
      $queryParams[] = $factoryId;
    }
    if ($goodsId) {
      $sql .= " and (d.id in (select distinct dmobill_id from t_dmo_bill_detail where goods_id = '%s')) ";
      $queryParams[] = $goodsId;
    }
    $sql .= " order by d.deal_date desc, d.ref desc
				  limit %d , %d";
    $queryParams[] = $start;
    $queryParams[] = $limit;
    $data = $db->query($sql, $queryParams);
    foreach ($data as $v) {
      $confirmUserName = null;
      $confirmDate = null;
      $confirmUserId = $v["confirm_user_id"];
      if ($confirmUserId) {
        $sql = "select name from t_user where id = '%s' ";
        $d = $db->query($sql, $confirmUserId);
        if ($d) {
          $confirmUserName = $d[0]["name"];
          $confirmDate = $v["confirm_date"];
        }
      }

      $result[] = [
        "id" => $v["id"],
        "ref" => $v["ref"],
        "billStatus" => $v["bill_status"],
        "dealDate" => $this->toYMD($v["deal_date"]),
        "dealAddress" => $v["deal_address"],
        "factoryName" => $v["factory_name"],
        "contact" => $v["contact"],
        "tel" => $v["tel"],
        "fax" => $v["fax"],
        "goodsMoney" => $v["goods_money"],
        "tax" => $v["tax"],
        "moneyWithTax" => $v["money_with_tax"],
        "paymentType" => $v["payment_type"],
        "billMemo" => $v["bill_memo"],
        "bizUserName" => $v["biz_user_name"],
        "orgName" => $v["org_name"],
        "inputUserName" => $v["input_user_name"],
        "dateCreated" => $v["date_created"],
        "confirmUserName" => $confirmUserName,
        "confirmDate" => $confirmDate
      ];
    }

    $sql = "select count(*) as cnt
            from t_dmo_bill d, t_factory f, t_org o, t_user u1, t_user u2
            where (d.factory_id = f.id) and (d.org_id = o.id)
              and (d.biz_user_id = u1.id) and (d.input_user_id = u2.id)
            ";
    $queryParams = [];
    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::DMO, "d", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = $rs[1];
    }
    if ($billStatus != -1) {
      if ($billStatus < 4000) {
        $sql .= " and (d.bill_status = %d) ";
      } else {
        // 订单关闭 - 有多种状态
        $sql .= " and (d.bill_status >= %d) ";
      }
      $queryParams[] = $billStatus;
    }
    if ($ref) {
      $sql .= " and (d.ref like '%s') ";
      $queryParams[] = "%$ref%";
    }
    if ($fromDT) {
      $sql .= " and (d.deal_date >= '%s')";
      $queryParams[] = $fromDT;
    }
    if ($toDT) {
      $sql .= " and (d.deal_date <= '%s')";
      $queryParams[] = $toDT;
    }
    if ($factoryId) {
      $sql .= " and (d.factory_id = '%s')";
      $queryParams[] = $factoryId;
    }
    if ($goodsId) {
      $sql .= " and (d.id in (select distinct dmobill_id from t_dmo_bill_detail where goods_id = '%s')) ";
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
   * 获得成品委托生产订单的明细信息
   */
  public function dmoBillDetailList($params)
  {
    $db = $this->db;
    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    // id: 成品委托生产订单id
    $id = $params["id"];

    $sql = "select p.id, g.code, g.name, g.spec, convert(p.goods_count, " . $fmt . ") as goods_count,
              p.goods_price, p.goods_money,
              convert(p.dmw_count, " . $fmt . ") as dmw_count,
              convert(p.left_count, " . $fmt . ") as left_count, p.memo,
              p.tax_rate, p.tax, p.money_with_tax, u.name as unit_name, p.goods_price_with_tax
            from t_dmo_bill_detail p, t_goods g, t_goods_unit u
            where p.dmobill_id = '%s' and p.goods_id = g.id and g.unit_id = u.id
            order by p.show_order";
    $result = [];
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
        "dmwCount" => $v["dmw_count"],
        "leftCount" => $v["left_count"],
        "memo" => $v["memo"],
        "goodsPriceWithTax" => $goodsPriceWithTax
      ];
    }

    return $result;
  }

  /**
   * 成品委托生产订单的入库情况列表
   */
  public function dmoBillDMWBillList($params)
  {
    $db = $this->db;

    // id: 成品委托生产订单id
    $id = $params["id"];

    $sql = "select b.id, b.bill_status, b.ref, b.biz_dt, u1.name as biz_user_name, u2.name as input_user_name,
              b.goods_money, w.name as warehouse_name, f.name as factory_name,
              b.date_created, b.payment_type
            from t_dmw_bill b, t_warehouse w, t_factory f, t_user u1, t_user u2,
              t_dmo_dmw dmow
            where (dmow.dmo_id = '%s') and (dmow.dmw_id = b.id)
              and (b.warehouse_id = w.id) and (b.factory_id = f.id)
              and (b.biz_user_id = u1.id) and (b.input_user_id = u2.id)
            order by b.ref ";
    $data = $db->query($sql, $id);
    $result = [];

    foreach ($data as $v) {
      $billStatus = $v["bill_status"];
      $bs = "";
      if ($billStatus == 0) {
        $bs = "待入库";
      } else if ($billStatus == 1000) {
        $bs = "已入库";
      } else if ($billStatus == 2000) {
        $bs = "已退货";
      }

      $result[] = [
        "id" => $v["id"],
        "ref" => $v["ref"],
        "bizDate" => $this->toYMD($v["biz_dt"]),
        "factoryName" => $v["factory_name"],
        "warehouseName" => $v["warehouse_name"],
        "inputUserName" => $v["input_user_name"],
        "bizUserName" => $v["biz_user_name"],
        "billStatus" => $bs,
        "amount" => $v["goods_money"],
        "dateCreated" => $v["date_created"],
        "paymentType" => $v["payment_type"]
      ];
    }

    return $result;
  }

  /**
   * 删除成品委托生产订单
   */
  public function deleteDMOBill(&$params)
  {
    $db = $this->db;

    // 成品委托生产订单id
    $id = $params["id"];

    $bill = $this->getDMOBillById($id);

    if (!$bill) {
      return $this->bad("要删除的成品委托生产订单不存在");
    }
    $ref = $bill["ref"];
    $billStatus = $bill["billStatus"];
    if ($billStatus > 0) {
      return $this->bad("成品委托生产订单(单号：{$ref})已经审核，不能被删除");
    }

    $sql = "delete from t_dmo_bill_detail where dmobill_id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $sql = "delete from t_dmo_bill where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $params["ref"] = $ref;
    return null;
  }

  /**
   * 审核成品委托生产订单
   */
  public function commitDMOBill(&$params)
  {
    $db = $this->db;

    $id = $params["id"];
    $loginUserId = $params["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->badParam("loginUserId");
    }

    $bill = $this->getDMOBillById($id);
    if (!$bill) {
      return $this->bad("要审核的成品委托生产订单不存在");
    }
    $ref = $bill["ref"];
    $billStatus = $bill["billStatus"];
    if ($billStatus > 0) {
      return $this->bad("成品委托生产订单(单号：$ref)已经被审核，不能再次审核");
    }

    $sql = "update t_dmo_bill
            set bill_status = 1000,
              confirm_user_id = '%s',
              confirm_date = now()
            where id = '%s' ";
    $rc = $db->execute($sql, $loginUserId, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $params["ref"] = $ref;
    return null;
  }

  /**
   * 取消审核成品委托生产订单
   */
  public function cancelConfirmDMOBill(&$params)
  {
    $db = $this->db;

    // 成品委托生产订单id
    $id = $params["id"];

    $bill = $this->getDMOBillById($id);
    if (!$bill) {
      return $this->bad("要取消审核的成品委托生产订单不存在");
    }

    $ref = $bill["ref"];

    $billStatus = $bill["billStatus"];
    if ($billStatus > 1000) {
      return $this->bad("成品委托生产订单(单号:{$ref})不能取消审核");
    }

    if ($billStatus == 0) {
      return $this->bad("成品委托生产订单(单号:{$ref})还没有审核，无需进行取消审核操作");
    }

    $sql = "select count(*) as cnt from t_dmo_dmw where dmo_id = '%s' ";
    $data = $db->query($sql, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("成品委托生产订单(单号:{$ref})已经生成了成品委托生产入库单，不能取消审核");
    }

    $sql = "update t_dmo_bill
            set bill_status = 0, confirm_user_id = null, confirm_date = null
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
   * 关闭成品委托生产订单
   */
  public function closeDMOBill(&$params)
  {
    $db = $this->db;

    // 成品委托生产订单id
    $id = $params["id"];

    $bill = $this->getDMOBillById($id);
    if (!$bill) {
      return $this->bad("要关闭的成品委托生产订单不存在");
    }

    $ref = $bill["ref"];
    $billStatus = $bill["billStatus"];

    if ($billStatus >= 4000) {
      return $this->bad("成品委托生产订单已经被关闭");
    }

    // 检查该成品委托生产订单是否有生成的成品委托生产入库单，并且这些成品委托生产入库单是没有提交入库的
    // 如果存在这类成品委托生产入库单，那么该成品委托生产订单不能关闭。
    $sql = "select count(*) as cnt
            from t_dmw_bill w, t_dmo_dmw p
            where w.id = p.dmw_id and p.dmo_id = '%s'
              and w.bill_status = 0 ";
    $data = $db->query($sql, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      $info = "当前成品委托生产订单生成的入库单中还有没提交的<br/><br/>把这些入库单删除后，才能关闭采购订单";
      return $this->bad($info);
    }

    if ($billStatus < 1000) {
      return $this->bad("当前成品委托生产订单还没有审核，没有审核的成品委托生产订单不能关闭");
    }

    $newBillStatus = -1;
    if ($billStatus == 1000) {
      // 当前订单只是审核了
      $newBillStatus = 4000;
    } else if ($billStatus == 2000) {
      // 部分入库
      $newBillStatus = 4001;
    } else if ($billStatus == 3000) {
      // 全部入库
      $newBillStatus = 4002;
    }

    if ($newBillStatus == -1) {
      return $this->bad("当前成品委托生产订单的订单状态是不能识别的状态码：{$billStatus}");
    }

    $sql = "update t_dmo_bill
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
   * 取消关闭成品委托生产订单
   */
  public function cancelClosedDMOBill(&$params)
  {
    $db = $this->db;

    // 成品委托生产订单id
    $id = $params["id"];

    $bill = $this->getDMOBillById($id);

    if (!$bill) {
      return $this->bad("要关闭的成品委托生产订单不存在");
    }

    $ref = $bill["ref"];
    $billStatus = $bill["billStatus"];

    if ($billStatus < 4000) {
      return $this->bad("成品委托生产订单没有被关闭，无需取消");
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
      return $this->bad("当前成品委托生产订单的订单状态是不能识别的状态码：{$billStatus}");
    }

    $sql = "update t_dmo_bill
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
   * 查询成品委托生产订单的数据，用于生成PDF文件
   *
   * @param array $params
   *
   * @return NULL|array
   */
  public function getDataForPDF($params)
  {
    $db = $this->db;

    $ref = $params["ref"];

    $sql = "select p.id, p.bill_status, p.goods_money, p.tax, p.money_with_tax,
              f.name as factory_name, p.contact, p.tel, p.fax, p.deal_address,
              p.deal_date, p.payment_type, p.bill_memo, p.date_created,
              o.full_name as org_name, u1.name as biz_user_name, u2.name as input_user_name,
              p.confirm_user_id, p.confirm_date, p.company_id, p.money_with_tax
            from t_dmo_bill p, t_factory f, t_org o, t_user u1, t_user u2
            where (p.factory_id = f.id) and (p.org_id = o.id)
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
    $result["tax"] = $v["tax"];
    $result["moneyWithTax"] = $v["money_with_tax"];
    $result["dealDate"] = $this->toYMD($v["deal_date"]);
    $result["dealAddress"] = $v["deal_address"];
    $result["bizUserName"] = $v["biz_user_name"];
    $result["moneyWithTax"] = $v["money_with_tax"];

    $sql = "select p.id, g.code, g.name, g.spec, convert(p.goods_count, $fmt) as goods_count,
              p.goods_price, p.goods_money,
              p.tax_rate, p.tax, p.money_with_tax, u.name as unit_name
            from t_dmo_bill_detail p, t_goods g, t_goods_unit u
            where p.dmobill_id = '%s' and p.goods_id = g.id and g.unit_id = u.id
            order by p.show_order";
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
   * 为使用Lodop打印准备数据
   *
   * @param array $params
   */
  public function getDMOBillDataForLodopPrint($params)
  {
    $db = $this->db;
    $result = [];

    $id = $params["id"];

    $sql = "select p.ref, p.bill_status, p.goods_money, p.tax, p.money_with_tax,
              f.name as factory_name, p.contact, p.tel, p.fax, p.deal_address,
              p.deal_date, p.payment_type, p.bill_memo, p.date_created,
              o.full_name as org_name, u1.name as biz_user_name, u2.name as input_user_name,
              p.confirm_user_id, p.confirm_date, p.company_id
            from t_dmo_bill p, t_factory f, t_org o, t_user u1, t_user u2
            where (p.factory_id = f.id) and (p.org_id = o.id)
              and (p.biz_user_id = u1.id) and (p.input_user_id = u2.id)
              and (p.id = '%s')";

    $data = $db->query($sql, $id);
    if (!$data) {
      return $result;
    }

    $v = $data[0];
    $result["ref"] = $v["ref"];
    $result["goodsMoney"] = $v["goods_money"];
    $result["tax"] = $v["tax"];
    $result["moneyWithTax"] = $v["money_with_tax"];
    $result["factoryName"] = $v["factory_name"];
    $result["contact"] = $v["contact"];
    $result["tel"] = $v["tel"];
    $result["dealDate"] = $this->toYMD($v["deal_date"]);
    $result["dealAddress"] = $v["deal_address"];
    $result["billMemo"] = $v["bill_memo"];

    $result["printDT"] = date("Y-m-d H:i:s");

    $companyId = $v["company_id"];
    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $sql = "select p.id, g.code, g.name, g.spec, convert(p.goods_count, $fmt) as goods_count,
              p.goods_price, p.goods_money,
              p.tax_rate, p.tax, p.money_with_tax, u.name as unit_name
            from t_dmo_bill_detail p, t_goods g, t_goods_unit u
            where p.dmobill_id = '%s' and p.goods_id = g.id and g.unit_id = u.id
            order by p.show_order";
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
        "goodsMoneyWithTax" => $v["money_with_tax"]
      ];
    }

    $result["items"] = $items;

    return $result;
  }
}
