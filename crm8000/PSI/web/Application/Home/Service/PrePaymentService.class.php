<?php

namespace Home\Service;

use Home\DAO\PrePaymentDAO;

/**
 * 预付款Service
 *
 * @author 李静波
 */
class PrePaymentService extends PSIBaseExService
{
  private $LOG_CATEGORY = "预付款管理";

  public function addPrePaymentInfo()
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    return array(
      "bizUserId" => $this->getLoginUserId(),
      "bizUserName" => $this->getLoginUserName()
    );
  }

  public function returnPrePaymentInfo()
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    return array(
      "bizUserId" => $this->getLoginUserId(),
      "bizUserName" => $this->getLoginUserName()
    );
  }

  /**
   * 向供应商付预付款
   */
  public function addPrePayment($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $params["companyId"] = $this->getCompanyId();
    $params["loginUserId"] = $this->getLoginUserId();

    $db = $this->db();
    $db->startTrans();

    $dao = new PrePaymentDAO($db);
    $rc = $dao->addPrePayment($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    // 记录业务日志
    $bs = new BizlogService($db);
    $supplierName = $params["supplierName"];
    $inMoney = $params["inMoney"];
    $log = "付供应商[{$supplierName}]预付款：{$inMoney}元";
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 供应商退回预收款
   */
  public function returnPrePayment($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $params["companyId"] = $this->getCompanyId();
    $params["loginUserId"] = $this->getLoginUserId();

    $db = $this->db();
    $db->startTrans();

    $dao = new PrePaymentDAO($db);
    $rc = $dao->returnPrePayment($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    // 记录业务日志
    $bs = new BizlogService($db);
    $supplierName = $params["supplierName"];
    $inMoney = $params["inMoney"];
    $log = "供应商[{$supplierName}]退回采购预付款：{$inMoney}元";
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  public function prepaymentList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new PrePaymentDAO($this->db());
    return $dao->prepaymentList($params);
  }

  public function prepaymentDetailList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new PrePaymentDAO($this->db());
    return $dao->prepaymentDetailList($params);
  }
}
