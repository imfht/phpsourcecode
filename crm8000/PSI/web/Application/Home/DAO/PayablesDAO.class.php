<?php

namespace Home\DAO;

use Home\Common\FIdConst;

/**
 * 应付账款 DAO
 *
 * @author 李静波
 */
class PayablesDAO extends PSIBaseExDAO
{

  /**
   * 往来单位分类
   *
   * @param array $params
   * @return array
   */
  public function payCategoryList($params)
  {
    $db = $this->db;

    $loginUserId = $params["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->emptyResult();
    }

    $result = array();
    $result[0]["id"] = "";
    $result[0]["name"] = "[全部]";

    $id = $params["id"];
    if ($id == "supplier") {
      $sql = "select id, name from t_supplier_category ";
      $queryParams = array();
      $ds = new DataOrgDAO($db);
      $rs = $ds->buildSQL(FIdConst::PAYABLES, "t_supplier_category", $loginUserId);
      if ($rs) {
        $sql .= " where " . $rs[0];
        $queryParams = $rs[1];
      }
      $sql .= " order by code";
      $data = $db->query($sql, $queryParams);
      foreach ($data as $i => $v) {
        $result[$i + 1]["id"] = $v["id"];
        $result[$i + 1]["name"] = $v["name"];
      }
    } else if ($id == "factory") {
      $sql = "select id, name from t_factory_category ";
      $queryParams = array();
      $ds = new DataOrgDAO($db);
      $rs = $ds->buildSQL(FIdConst::PAYABLES, "t_factory_category", $loginUserId);
      if ($rs) {
        $sql .= " where " . $rs[0];
        $queryParams = $rs[1];
      }
      $sql .= " order by code";
      $data = $db->query($sql, $queryParams);
      foreach ($data as $i => $v) {
        $result[$i + 1]["id"] = $v["id"];
        $result[$i + 1]["name"] = $v["name"];
      }
    } else {
      $sql = "select id,  code, name from t_customer_category ";
      $queryParams = array();
      $ds = new DataOrgDAO($db);
      $rs = $ds->buildSQL(FIdConst::PAYABLES, "t_customer_category", $loginUserId);
      if ($rs) {
        $sql .= " where " . $rs[0];
        $queryParams = $rs[1];
      }
      $sql .= " order by code";
      $data = $db->query($sql, $queryParams);
      foreach ($data as $i => $v) {
        $result[$i + 1]["id"] = $v["id"];
        $result[$i + 1]["name"] = $v["name"];
      }
    }

    return $result;
  }

  /**
   * 应付账款列表
   *
   * @param array $params
   * @return array
   */
  public function payList($params)
  {
    $db = $this->db;

    $loginUserId = $params["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->emptyResult();
    }

    $caType = $params["caType"];
    $categoryId = $params["categoryId"];
    $customerId = $params["customerId"];
    $supplierId = $params["supplierId"];
    $factoryId = $params["factoryId"];
    $start = $params["start"];
    $limit = $params["limit"];

    if ($caType == "supplier") {
      $queryParams = array();
      $sql = "select p.id, p.pay_money, p.act_money, p.balance_money, s.id as ca_id, s.code, s.name
              from t_payables p, t_supplier s
              where p.ca_id = s.id and p.ca_type = 'supplier' ";
      if ($supplierId) {
        $sql .= " and s.id = '%s' ";
        $queryParams[] = $supplierId;
      } else if ($categoryId) {
        $sql .= " and s.category_id = '%s' ";
        $queryParams[] = $categoryId;
      }
      $ds = new DataOrgDAO($db);
      $rs = $ds->buildSQL(FIdConst::PAYABLES, "s", $loginUserId);
      if ($rs) {
        $sql .= " and " . $rs[0];
        $queryParams = array_merge($queryParams, $rs[1]);
      }
      $sql .= " order by s.code
					limit %d , %d ";
      $queryParams[] = $start;
      $queryParams[] = $limit;
      $data = $db->query($sql, $queryParams);
      $result = array();
      foreach ($data as $i => $v) {
        $result[$i]["id"] = $v["id"];
        $result[$i]["caId"] = $v["ca_id"];
        $result[$i]["code"] = $v["code"];
        $result[$i]["name"] = $v["name"];
        $result[$i]["payMoney"] = $v["pay_money"];
        $result[$i]["actMoney"] = $v["act_money"];
        $result[$i]["balanceMoney"] = $v["balance_money"];
      }

      $queryParams[] = array();
      $sql = "select count(*) as cnt from t_payables p, t_supplier s
              where p.ca_id = s.id and p.ca_type = 'supplier' ";
      if ($supplierId) {
        $sql .= " and s.id = '%s' ";
        $queryParams[] = $supplierId;
      } else if ($categoryId) {
        $sql .= " and s.category_id = '%s' ";
        $queryParams[] = $categoryId;
      }
      $ds = new DataOrgDAO($db);
      $rs = $ds->buildSQL(FIdConst::PAYABLES, "s", $loginUserId);
      if ($rs) {
        $sql .= " and " . $rs[0];
        $queryParams = array_merge($queryParams, $rs[1]);
      }
      $data = $db->query($sql, $queryParams);
      $cnt = $data[0]["cnt"];

      return array(
        "dataList" => $result,
        "totalCount" => $cnt
      );
    } else if ($caType == "factory") {
      $queryParams = array();
      $sql = "select p.id, p.pay_money, p.act_money, p.balance_money, s.id as ca_id, s.code, s.name
              from t_payables p, t_factory s
              where p.ca_id = s.id and p.ca_type = 'factory' ";
      if ($factoryId) {
        $sql .= " and s.id = '%s' ";
        $queryParams[] = $factoryId;
      } else if ($categoryId) {
        $sql .= " and s.category_id = '%s' ";
        $queryParams[] = $categoryId;
      }
      $ds = new DataOrgDAO($db);
      $rs = $ds->buildSQL(FIdConst::PAYABLES, "s", $loginUserId);
      if ($rs) {
        $sql .= " and " . $rs[0];
        $queryParams = array_merge($queryParams, $rs[1]);
      }
      $sql .= " order by s.code
                limit %d , %d ";
      $queryParams[] = $start;
      $queryParams[] = $limit;
      $data = $db->query($sql, $queryParams);
      $result = array();
      foreach ($data as $i => $v) {
        $result[$i]["id"] = $v["id"];
        $result[$i]["caId"] = $v["ca_id"];
        $result[$i]["code"] = $v["code"];
        $result[$i]["name"] = $v["name"];
        $result[$i]["payMoney"] = $v["pay_money"];
        $result[$i]["actMoney"] = $v["act_money"];
        $result[$i]["balanceMoney"] = $v["balance_money"];
      }

      $queryParams[] = array();
      $sql = "select count(*) as cnt from t_payables p, t_supplier s
              where p.ca_id = s.id and p.ca_type = 'factory' ";
      if ($factoryId) {
        $sql .= " and s.id = '%s' ";
        $queryParams[] = $factoryId;
      } else if ($categoryId) {
        $sql .= " and s.category_id = '%s' ";
        $queryParams[] = $categoryId;
      }
      $ds = new DataOrgDAO($db);
      $rs = $ds->buildSQL(FIdConst::PAYABLES, "s", $loginUserId);
      if ($rs) {
        $sql .= " and " . $rs[0];
        $queryParams = array_merge($queryParams, $rs[1]);
      }
      $data = $db->query($sql, $queryParams);
      $cnt = $data[0]["cnt"];

      return array(
        "dataList" => $result,
        "totalCount" => $cnt
      );
    } else {
      // 客户
      $queryParams = array();
      $sql = "select p.id, p.pay_money, p.act_money, p.balance_money, s.id as ca_id, s.code, s.name
              from t_payables p, t_customer s
              where p.ca_id = s.id and p.ca_type = 'customer' ";
      if ($customerId) {
        $sql .= " and s.id = '%s' ";
        $queryParams[] = $customerId;
      } else if ($categoryId) {
        $sql .= " and s.category_id = '%s' ";
        $queryParams[] = $categoryId;
      }
      $ds = new DataOrgDAO($db);
      $rs = $ds->buildSQL(FIdConst::PAYABLES, "s", $loginUserId);
      if ($rs) {
        $sql .= " and " . $rs[0];
        $queryParams = array_merge($queryParams, $rs[1]);
      }
      $sql .= " order by s.code
                limit %d , %d";
      $queryParams[] = $start;
      $queryParams[] = $limit;
      $data = $db->query($sql, $queryParams);
      $result = array();
      foreach ($data as $i => $v) {
        $result[$i]["id"] = $v["id"];
        $result[$i]["caId"] = $v["ca_id"];
        $result[$i]["code"] = $v["code"];
        $result[$i]["name"] = $v["name"];
        $result[$i]["payMoney"] = $v["pay_money"];
        $result[$i]["actMoney"] = $v["act_money"];
        $result[$i]["balanceMoney"] = $v["balance_money"];
      }

      $queryParams = array();
      $sql = "select count(*) as cnt from t_payables p, t_customer s
              where p.ca_id = s.id and p.ca_type = 'customer' ";
      if ($customerId) {
        $sql .= " and s.id = '%s' ";
        $queryParams[] = $customerId;
      } else if ($categoryId) {
        $sql .= " and s.category_id = '%s' ";
        $queryParams[] = $categoryId;
      }
      $ds = new DataOrgDAO($db);
      $rs = $ds->buildSQL(FIdConst::PAYABLES, "s", $loginUserId);
      if ($rs) {
        $sql .= " and " . $rs[0];
        $queryParams = array_merge($queryParams, $rs[1]);
      }
      $data = $db->query($sql, $queryParams);
      $cnt = $data[0]["cnt"];

      return array(
        "dataList" => $result,
        "totalCount" => $cnt
      );
    }
  }

  /**
   * 每笔应付账款的明细记录
   *
   * @param array $params
   * @return array
   */
  public function payDetailList($params)
  {
    $db = $this->db;

    $caType = $params["caType"];
    $caId = $params["caId"];
    $start = $params["start"];
    $limit = $params["limit"];

    $sql = "select id, ref_type, ref_number, pay_money, act_money, balance_money, date_created, biz_date
            from t_payables_detail
            where ca_type = '%s' and ca_id = '%s'
            order by biz_date desc, date_created desc
            limit %d , %d ";
    $data = $db->query($sql, $caType, $caId, $start, $limit);
    $result = array();
    foreach ($data as $i => $v) {
      $result[$i]["id"] = $v["id"];
      $result[$i]["refType"] = $v["ref_type"];
      $result[$i]["refNumber"] = $v["ref_number"];
      $result[$i]["bizDT"] = date("Y-m-d", strtotime($v["biz_date"]));
      $result[$i]["dateCreated"] = $v["date_created"];
      $result[$i]["payMoney"] = $v["pay_money"];
      $result[$i]["actMoney"] = $v["act_money"];
      $result[$i]["balanceMoney"] = $v["balance_money"];
    }

    $sql = "select count(*) as cnt from t_payables_detail
            where ca_type = '%s' and ca_id = '%s' ";
    $data = $db->query($sql, $caType, $caId);
    $cnt = $data[0]["cnt"];

    return array(
      "dataList" => $result,
      "totalCount" => $cnt
    );
  }

  /**
   * 应付账款的付款记录
   *
   * @param array $params
   * @return array
   */
  public function payRecordList($params)
  {
    $db = $this->db;

    $refType = $params["refType"];
    $refNumber = $params["refNumber"];
    $start = $params["start"];
    $limit = $params["limit"];

    $sql = "select u.name as biz_user_name, bu.name as input_user_name, p.id,
              p.act_money, p.biz_date, p.date_created, p.remark
            from t_payment p, t_user u, t_user bu
            where p.ref_type = '%s' and p.ref_number = '%s'
              and  p.pay_user_id = u.id and p.input_user_id = bu.id
            order by p.date_created desc
            limit %d, %d ";
    $data = $db->query($sql, $refType, $refNumber, $start, $limit);
    $result = array();
    foreach ($data as $i => $v) {
      $result[$i]["id"] = $v["id"];
      $result[$i]["actMoney"] = $v["act_money"];
      $result[$i]["bizDate"] = date("Y-m-d", strtotime($v["biz_date"]));
      $result[$i]["dateCreated"] = $v["date_created"];
      $result[$i]["bizUserName"] = $v["biz_user_name"];
      $result[$i]["inputUserName"] = $v["input_user_name"];
      $result[$i]["remark"] = $v["remark"];
    }

    $sql = "select count(*) as cnt from t_payment
            where ref_type = '%s' and ref_number = '%s' ";
    $data = $db->query($sql, $refType, $refNumber);
    $cnt = $data[0]["cnt"];

    return array(
      "dataList" => $result,
      "totalCount" => $cnt
    );
  }

  /**
   * 新增付款记录
   *
   * @param array $params
   * @return NULL|array
   */
  public function addPayment($params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    $dataOrg = $params["dataOrg"];
    $loginUserId = $params["loginUserId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->badParam("companyId");
    }
    if ($this->dataOrgNotExists($dataOrg)) {
      return $this->badParam("dataOrg");
    }
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->badParam("loginUserId");
    }

    $refType = $params["refType"];
    $refNumber = $params["refNumber"];
    $bizDT = $params["bizDT"];
    $actMoney = $params["actMoney"];
    $bizUserId = $params["bizUserId"];
    $remark = $params["remark"];
    if (!$remark) {
      $remark = "";
    }

    $billId = "";
    if ($refType == "采购入库") {
      $sql = "select id from t_pw_bill where ref = '%s' ";
      $data = $db->query($sql, $refNumber);
      if (!$data) {
        return $this->bad("单号为 {$refNumber} 的采购入库不存在，无法付款");
      }
      $billId = $data[0]["id"];
    }

    // 检查付款人是否存在
    $userDAO = new UserDAO($db);
    $user = $userDAO->getUserById($bizUserId);
    if (!$user) {
      return $this->bad("付款人不存在，无法付款");
    }

    // 检查付款日期是否正确
    if (!$this->dateIsValid($bizDT)) {
      return $this->bad("付款日期不正确");
    }

    $sql = "insert into t_payment (id, act_money, biz_date, date_created, input_user_id,
              pay_user_id,  bill_id,  ref_type, ref_number, remark, data_org, company_id)
            values ('%s', %f, '%s', now(), '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')";

    $rc = $db->execute(
      $sql,
      $this->newId(),
      $actMoney,
      $bizDT,
      $loginUserId,
      $bizUserId,
      $billId,
      $refType,
      $refNumber,
      $remark,
      $dataOrg,
      $companyId
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 应付明细账
    $sql = "select balance_money, act_money, ca_type, ca_id, company_id
            from t_payables_detail
            where ref_type = '%s' and ref_number = '%s' ";
    $data = $db->query($sql, $refType, $refNumber);
    if (!$data) {
      return $this->sqlError(__METHOD__, __LINE__);
    }
    $caType = $data[0]["ca_type"];
    $caId = $data[0]["ca_id"];
    $companyId = $data[0]["company_id"];
    $balanceMoney = $data[0]["balance_money"];
    $actMoneyNew = $data[0]["act_money"];
    $actMoneyNew += $actMoney;
    $balanceMoney -= $actMoney;
    $sql = "update t_payables_detail
            set act_money = %f, balance_money = %f
            where ref_type = '%s' and ref_number = '%s'
              and ca_id = '%s' and ca_type = '%s' ";
    $rc = $db->execute($sql, $actMoneyNew, $balanceMoney, $refType, $refNumber, $caId, $caType);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 应付总账
    $sql = "select sum(pay_money) as sum_pay_money, sum(act_money) as sum_act_money
            from t_payables_detail
            where ca_type = '%s' and ca_id = '%s' and company_id = '%s' ";
    $data = $db->query($sql, $caType, $caId, $companyId);
    if (!$data) {
      return $this->sqlError(__METHOD__, __LINE__);
    }
    $sumPayMoney = $data[0]["sum_pay_money"];
    $sumActMoney = $data[0]["sum_act_money"];
    if (!$sumPayMoney) {
      $sumPayMoney = 0;
    }
    if (!$sumActMoney) {
      $sumActMoney = 0;
    }
    $sumBalanceMoney = $sumPayMoney - $sumActMoney;

    $sql = "update t_payables
            set act_money = %f, balance_money = %f
            where ca_type = '%s' and ca_id = '%s' and company_id = '%s' ";
    $rc = $db->execute($sql, $sumActMoney, $sumBalanceMoney, $caType, $caId, $companyId);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    return null;
  }

  /**
   * 刷新付款信息 - 总账
   *
   * @param array $params
   * @return array
   */
  public function refreshPayInfo($params)
  {
    $db = $this->db;

    $id = $params["id"];
    $data = $db->query("select act_money, balance_money from t_payables  where id = '%s' ", $id);
    return array(
      "actMoney" => $data[0]["act_money"],
      "balanceMoney" => $data[0]["balance_money"]
    );
  }

  /**
   * 刷新付款信息 - 明细账
   *
   * @param array $params
   * @return array
   */
  public function refreshPayDetailInfo($params)
  {
    $db = $this->db;

    $id = $params["id"];
    $data = $db->query(
      "select act_money, balance_money from t_payables_detail  where id = '%s' ",
      $id
    );
    return array(
      "actMoney" => $data[0]["act_money"],
      "balanceMoney" => $data[0]["balance_money"]
    );
  }
}
