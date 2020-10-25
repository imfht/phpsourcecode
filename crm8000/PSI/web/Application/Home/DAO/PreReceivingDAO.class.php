<?php

namespace Home\DAO;

/**
 * 预收款 DAO
 *
 * @author 李静波
 */
class PreReceivingDAO extends PSIBaseExDAO
{

  /**
   * 收预收款
   *
   * @param array $params
   * @return NULL|array
   */
  public function addPreReceiving(&$params)
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

    $customerId = $params["customerId"];
    $bizUserId = $params["bizUserId"];
    $bizDT = $params["bizDT"];
    $inMoney = $params["inMoney"];
    $memo = $params["memo"];

    // 检查客户
    $customerDAO = new CustomerDAO($db);
    $customer = $customerDAO->getCustomerById($customerId);
    if (!$customer) {
      return $this->bad("客户不存在，无法预收款");
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

    $sql = "select in_money, balance_money from t_pre_receiving
            where customer_id = '%s' and company_id = '%s' ";
    $data = $db->query($sql, $customerId, $companyId);
    if (!$data) {
      // 总账
      $sql = "insert into t_pre_receiving(id, customer_id, in_money, balance_money, company_id)
              values ('%s', '%s', %f, %f, '%s')";
      $rc = $db->execute($sql, $this->newId(), $customerId, $inMoney, $inMoney, $companyId);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }

      // 明细账
      $sql = "insert into t_pre_receiving_detail(id, customer_id, in_money, balance_money, date_created,
                ref_number, ref_type, biz_user_id, input_user_id, biz_date, company_id, memo)
              values('%s', '%s', %f, %f, now(), '', '收预收款', '%s', '%s', '%s', '%s', '%s')";
      $rc = $db->execute(
        $sql,
        $this->newId(),
        $customerId,
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
      $sql = "update t_pre_receiving
              set in_money = %f, balance_money = %f
              where customer_id = '%s' and company_id = '%s' ";
      $rc = $db->execute($sql, $totalInMoney, $totalBalanceMoney, $customerId, $companyId);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }

      // 明细账
      $sql = "insert into t_pre_receiving_detail(id, customer_id, in_money, balance_money, date_created,
                ref_number, ref_type, biz_user_id, input_user_id, biz_date, company_id, memo)
              values('%s', '%s', %f, %f, now(), '', '收预收款', '%s', '%s', '%s', '%s', '%s')";
      $rc = $db->execute(
        $sql,
        $this->newId(),
        $customerId,
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

    $params["customerName"] = $customer["name"];

    // 操作成功
    return null;
  }

  /**
   * 退还预收款
   *
   * @param array $params
   * @return NULL|array
   */
  public function returnPreReceiving(&$params)
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

    $customerId = $params["customerId"];
    $bizUserId = $params["bizUserId"];
    $bizDT = $params["bizDT"];
    $outMoney = $params["outMoney"];
    $memo = $params["memo"];

    // 检查客户
    $customerDAO = new CustomerDAO($db);
    $customer = $customerDAO->getCustomerById($customerId);
    if (!$customer) {
      return $this->bad("客户不存在，无法预收款");
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

    $outMoney = floatval($outMoney);
    if ($outMoney <= 0) {
      return $this->bad("收款金额需要是正数");
    }

    $customerName = $customer["name"];

    $sql = "select balance_money, out_money
            from t_pre_receiving
            where customer_id = '%s' and company_id = '%s' ";
    $data = $db->query($sql, $customerId, $companyId);
    $balanceMoney = $data[0]["balance_money"];
    if (!$balanceMoney) {
      $balanceMoney = 0;
    }

    if ($balanceMoney < $outMoney) {
      $info = "退款金额{$outMoney}元超过余额。<br /><br />客户[{$customerName}]的预付款余额是{$balanceMoney}元";
      return $this->bad($info);
    }

    $totalOutMoney = $data[0]["out_money"];
    if (!$totalOutMoney) {
      $totalOutMoney = 0;
    }

    // 总账
    $sql = "update t_pre_receiving
            set out_money = %f, balance_money = %f
            where customer_id = '%s' and company_id = '%s' ";
    $totalOutMoney += $outMoney;
    $balanceMoney -= $outMoney;
    $rc = $db->execute($sql, $totalOutMoney, $balanceMoney, $customerId, $companyId);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 明细账
    $sql = "insert into t_pre_receiving_detail(id, customer_id, out_money, balance_money,
              biz_date, date_created, ref_number, ref_type, biz_user_id, input_user_id, company_id, memo)
            values ('%s', '%s', %f, %f, '%s', now(), '', '退预收款', '%s', '%s', '%s', '%s')";
    $rc = $db->execute(
      $sql,
      $this->newId(),
      $customerId,
      $outMoney,
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

    $params["customerName"] = $customerName;

    // 操作陈功
    return null;
  }

  /**
   * 预收款列表
   *
   * @param array $params
   * @return array
   */
  public function prereceivingList($params)
  {
    $db = $this->db;

    $start = $params["start"];
    $limit = $params["limit"];

    $categoryId = $params["categoryId"];
    $customerId = $params["customerId"];
    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    $queryParams = [];
    $sql = "select r.id, c.id as customer_id, c.code, c.name,
              r.in_money, r.out_money, r.balance_money
            from t_pre_receiving r, t_customer c
            where r.customer_id = c.id and r.company_id = '%s' ";
    $queryParams[] = $companyId;

    if ($customerId) {
      $sql .= " and c.id = '%s' ";
      $queryParams[] = $customerId;
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
      $result[$i]["customerId"] = $v["customer_id"];
      $result[$i]["code"] = $v["code"];
      $result[$i]["name"] = $v["name"];
      $result[$i]["inMoney"] = $v["in_money"];
      $result[$i]["outMoney"] = $v["out_money"];
      $result[$i]["balanceMoney"] = $v["balance_money"];
    }

    $queryParams = [];
    $sql = "select count(*) as cnt
            from t_pre_receiving r, t_customer c
            where r.customer_id = c.id and r.company_id = '%s'
            ";
    $queryParams[] = $companyId;
    if ($customerId) {
      $sql .= " and c.id = '%s' ";
      $queryParams[] = $customerId;
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
   * 预收款详情列表
   *
   * @param array $params
   * @return array
   */
  public function prereceivingDetailList($params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    $start = $params["start"];
    $limit = $params["limit"];

    $customerId = $params["customerId"];
    $dtFrom = $params["dtFrom"];
    $dtTo = $params["dtTo"];

    $sql = "select d.id, d.ref_type, d.ref_number, d.in_money, d.out_money, d.balance_money,
              d.biz_date, d.date_created, d.memo,
              u1.name as biz_user_name, u2.name as input_user_name
            from t_pre_receiving_detail d, t_user u1, t_user u2
            where d.customer_id = '%s' and d.biz_user_id = u1.id and d.input_user_id = u2.id
              and (d.biz_date between '%s' and '%s')
              and d.company_id = '%s'
            order by d.date_created
            limit %d , %d
            ";
    $data = $db->query($sql, $customerId, $dtFrom, $dtTo, $companyId, $start, $limit);
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
            from t_pre_receiving_detail d, t_user u1, t_user u2
            where d.customer_id = '%s' and d.biz_user_id = u1.id and d.input_user_id = u2.id
              and (d.biz_date between '%s' and '%s')
              and d.company_id = '%s'
            ";

    $data = $db->query($sql, $customerId, $dtFrom, $dtTo, $companyId);
    $cnt = $data[0]["cnt"];

    return array(
      "dataList" => $result,
      "totalCount" => $cnt
    );
  }
}
