<?php

namespace Home\DAO;

/**
 * 预付款 DAO
 *
 * @author 李静波
 */
class PrePaymentDAO extends PSIBaseExDAO
{

  /**
   * 向供应商付预付款
   *
   * @param array $params
   * @return NULL|array
   */
  public function addPrePayment(&$params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    $loginUserId = $params["loginUserId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->badParam("companyId");
    }
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->badParam("loginUserId");
    }

    $supplierId = $params["supplierId"];
    $bizUserId = $params["bizUserId"];
    $bizDT = $params["bizDT"];
    $inMoney = $params["inMoney"];
    $memo = $params["memo"];

    // 检查供应商
    $supplierDAO = new SupplierDAO($db);
    $supplier = $supplierDAO->getSupplierById($supplierId);
    if (!$supplier) {
      return $this->bad("供应商不存在，无法付预付款");
    }

    // 检查业务日期
    if (!$this->dateIsValid($bizDT)) {
      return $this->bad("业务日期不正确");
    }

    // 检查收款人是否存在
    $userDAO = new UserDAO($db);
    $user = $userDAO->getUserById($bizUserId);
    if (!$user) {
      return $this->bad("收款人不存在");
    }

    $inMoney = floatval($inMoney);
    if ($inMoney <= 0) {
      return $this->bad("付款金额需要是正数");
    }

    $sql = "select in_money, balance_money from t_pre_payment
            where supplier_id = '%s' and company_id = '%s' ";
    $data = $db->query($sql, $supplierId, $companyId);
    if (!$data) {
      // 总账
      $sql = "insert into t_pre_payment(id, supplier_id, in_money, balance_money, company_id)
              values ('%s', '%s', %f, %f, '%s')";
      $rc = $db->execute($sql, $this->newId(), $supplierId, $inMoney, $inMoney, $companyId);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }

      // 明细账
      $sql = "insert into t_pre_payment_detail(id, supplier_id, in_money, balance_money, date_created,
                ref_number, ref_type, biz_user_id, input_user_id, biz_date, company_id, memo)
              values('%s', '%s', %f, %f, now(), '', '预付供应商采购货款', '%s', '%s', '%s', '%s', '%s')";
      $rc = $db->execute(
        $sql,
        $this->newId(),
        $supplierId,
        $inMoney,
        $inMoney,
        $bizUserId,
        $loginUserId,
        $bizDT,
        $companyId,
        $memo
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    } else {
      $totalInMoney = $data[0]["in_money"];
      $totalBalanceMoney = $data[0]["balance_money"];
      if (!$totalInMoney) {
        $totalInMoney = 0;
      }
      if (!$totalBalanceMoney) {
        $totalBalanceMoney = 0;
      }

      $totalInMoney += $inMoney;
      $totalBalanceMoney += $inMoney;
      // 总账
      $sql = "update t_pre_payment
              set in_money = %f, balance_money = %f
              where supplier_id = '%s' and company_id = '%s' ";
      $rc = $db->execute($sql, $totalInMoney, $totalBalanceMoney, $supplierId, $companyId);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }

      // 明细账
      $sql = "insert into t_pre_payment_detail(id, supplier_id, in_money, balance_money, date_created,
                ref_number, ref_type, biz_user_id, input_user_id, biz_date, company_id, memo)
              values('%s', '%s', %f, %f, now(), '', '预付供应商采购货款', '%s', '%s', '%s', '%s', '%s')";
      $rc = $db->execute(
        $sql,
        $this->newId(),
        $supplierId,
        $inMoney,
        $totalBalanceMoney,
        $bizUserId,
        $loginUserId,
        $bizDT,
        $companyId,
        $memo
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    $params["supplierName"] = $supplier["name"];

    // 操作成功
    return null;
  }

  /**
   * 供应商退回预收款
   *
   * @param array $params
   * @return NULL|array
   */
  public function returnPrePayment(&$params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    $loginUserId = $params["loginUserId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->badParam("companyId");
    }
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->badParam("loginUserId");
    }

    $supplierId = $params["supplierId"];
    $bizUserId = $params["bizUserId"];
    $bizDT = $params["bizDT"];
    $inMoney = $params["inMoney"];
    $memo = $params["memo"];

    // 检查供应商
    $supplierDAO = new SupplierDAO($db);
    $supplier = $supplierDAO->getSupplierById($supplierId);
    if (!$supplier) {
      return $this->bad("供应商不存在，无法收款");
    }

    // 检查业务日期
    if (!$this->dateIsValid($bizDT)) {
      return $this->bad("业务日期不正确");
    }

    // 检查收款人是否存在
    $userDAO = new UserDAO($db);
    $user = $userDAO->getUserById($bizUserId);
    if (!$user) {
      return $this->bad("收款人不存在");
    }

    $inMoney = floatval($inMoney);
    if ($inMoney <= 0) {
      return $this->bad("收款金额需要是正数");
    }

    $supplierName = $supplier["name"];

    $sql = "select balance_money, in_money from t_pre_payment
            where supplier_id = '%s' and company_id = '%s' ";
    $data = $db->query($sql, $supplierId, $companyId);
    $balanceMoney = $data[0]["balance_money"];
    if (!$balanceMoney) {
      $balanceMoney = 0;
    }

    if ($balanceMoney < $inMoney) {
      $info = "退款金额{$inMoney}元超过余额。<br /><br />供应商[{$supplierName}]的预付款余额是{$balanceMoney}元";
      return $this->bad($info);
    }
    $totalInMoney = $data[0]["in_money"];
    if (!$totalInMoney) {
      $totalInMoney = 0;
    }

    // 总账
    $sql = "update t_pre_payment
            set in_money = %f, balance_money = %f
            where supplier_id = '%s' and company_id = '%s' ";
    $totalInMoney -= $inMoney;
    $balanceMoney -= $inMoney;
    $rc = $db->execute($sql, $totalInMoney, $balanceMoney, $supplierId, $companyId);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 明细账
    $sql = "insert into t_pre_payment_detail(id, supplier_id, in_money, balance_money,
              biz_date, date_created, ref_number, ref_type, biz_user_id, input_user_id,
              company_id, memo)
            values ('%s', '%s', %f, %f, '%s', now(), '', '供应商退回采购预付款', '%s', '%s', '%s', '%s')";
    $rc = $db->execute(
      $sql,
      $this->newId(),
      $supplierId,
      -$inMoney,
      $balanceMoney,
      $bizDT,
      $bizUserId,
      $loginUserId,
      $companyId,
      $memo
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $params["supplierName"] = $supplierName;

    return null;
  }

  /**
   * 预付款列表
   *
   * @param array $params
   * @return array
   */
  public function prepaymentList($params)
  {
    $db = $this->db;

    $start = $params["start"];
    $limit = $params["limit"];

    $categoryId = $params["categoryId"];
    $supplierId = $params["supplierId"];
    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    $queryParams = [];
    $sql = "select r.id, c.id as supplier_id, c.code, c.name,
              r.in_money, r.out_money, r.balance_money
            from t_pre_payment r, t_supplier c
            where r.supplier_id = c.id and r.company_id = '%s' ";
    $queryParams[] = $companyId;
    if ($supplierId) {
      $sql .= " and c.id = '%s' ";
      $queryParams[] = $supplierId;
    } else if ($categoryId) {
      $sql .= " and c.category_id = '%s' ";
      $queryParams[] = $categoryId;
    }
    $sql .= " order by c.code
				limit %d , %d
				";
    $queryParams[] = $start;
    $queryParams[] = $limit;
    $data = $db->query($sql, $queryParams);

    $result = array();
    foreach ($data as $i => $v) {
      $result[$i]["id"] = $v["id"];
      $result[$i]["supplierId"] = $v["supplier_id"];
      $result[$i]["code"] = $v["code"];
      $result[$i]["name"] = $v["name"];
      $result[$i]["inMoney"] = $v["in_money"];
      $result[$i]["outMoney"] = $v["out_money"];
      $result[$i]["balanceMoney"] = $v["balance_money"];
    }

    $queryParams = [];
    $sql = "select count(*) as cnt
				from t_pre_payment r, t_supplier c
				where r.supplier_id = c.id 
					and r.company_id = '%s'
				";
    $queryParams[] = $companyId;
    if ($supplierId) {
      $sql .= " and c.id = '%s' ";
      $queryParams[] = $supplierId;
    } else if ($categoryId) {
      $sql .= " and c.category_id = '%s' ";
      $queryParams[] = $categoryId;
    }
    $data = $db->query($sql, $queryParams);
    $cnt = $data[0]["cnt"];

    return array(
      "dataList" => $result,
      "totalCount" => $cnt
    );
  }

  /**
   * 预付款详情列表
   *
   * @param array $params
   * @return array
   */
  public function prepaymentDetailList($params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    $start = $params["start"];
    $limit = $params["limit"];

    $supplerId = $params["supplierId"];
    $dtFrom = $params["dtFrom"];
    $dtTo = $params["dtTo"];

    $sql = "select d.id, d.ref_type, d.ref_number, d.in_money, d.out_money, d.balance_money,
              d.biz_date, d.date_created, d.memo,
              u1.name as biz_user_name, u2.name as input_user_name
            from t_pre_payment_detail d, t_user u1, t_user u2
            where d.supplier_id = '%s' and d.biz_user_id = u1.id and d.input_user_id = u2.id
              and (d.biz_date between '%s' and '%s')
              and d.company_id = '%s'
            order by d.date_created
            limit %d , %d
            ";
    $data = $db->query($sql, $supplerId, $dtFrom, $dtTo, $companyId, $start, $limit);
    $result = array();
    foreach ($data as $i => $v) {
      $result[$i]["id"] = $v["id"];
      $result[$i]["refType"] = $v["ref_type"];
      $result[$i]["refNumber"] = $v["ref_number"];
      $result[$i]["inMoney"] = $v["in_money"];
      $result[$i]["outMoney"] = $v["out_money"];
      $result[$i]["balanceMoney"] = $v["balance_money"];
      $result[$i]["bizDT"] = $this->toYMD($v["biz_date"]);
      $result[$i]["dateCreated"] = $v["date_created"];
      $result[$i]["bizUserName"] = $v["biz_user_name"];
      $result[$i]["inputUserName"] = $v["input_user_name"];
      $result[$i]["memo"] = $v["memo"];
    }

    $sql = "select count(*) as cnt
            from t_pre_payment_detail d, t_user u1, t_user u2
            where d.supplier_id = '%s' and d.biz_user_id = u1.id and d.input_user_id = u2.id
              and (d.biz_date between '%s' and '%s')
              and d.company_id = '%s'
            ";

    $data = $db->query($sql, $supplerId, $companyId, $dtFrom, $dtTo);
    $cnt = $data[0]["cnt"];

    return array(
      "dataList" => $result,
      "totalCount" => $cnt
    );
  }
}
