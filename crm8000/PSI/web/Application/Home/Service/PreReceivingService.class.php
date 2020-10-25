<?php

namespace Home\Service;

use Home\DAO\PreReceivingDAO;

/**
 * 预收款Service
 *
 * @author 李静波
 */
class PreReceivingService extends PSIBaseExService
{
  private $LOG_CATEGORY = "预收款管理";

  public function addPreReceivingInfo()
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    return array(
      "bizUserId" => $this->getLoginUserId(),
      "bizUserName" => $this->getLoginUserName()
    );
  }

  public function returnPreReceivingInfo()
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
   * 收预收款
   */
  public function addPreReceiving($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $params["companyId"] = $this->getCompanyId();
    $params["loginUserId"] = $this->getLoginUserId();

    $db = $this->db();
    $db->startTrans();

    $dao = new PreReceivingDAO($db);
    $rc = $dao->addPreReceiving($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    // 记录业务日志
    $bs = new BizlogService($db);
    $customerName = $params["customerName"];
    $inMoney = $params["inMoney"];
    $log = "收取客户[{$customerName}]预收款：{$inMoney}元";
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 退还预收款
   */
  public function returnPreReceiving($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $params["companyId"] = $this->getCompanyId();
    $params["loginUserId"] = $this->getLoginUserId();

    $db = $this->db();
    $db->startTrans();

    $dao = new PreReceivingDAO($db);
    $rc = $dao->returnPreReceiving($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    // 记录业务日志
    $bs = new BizlogService($db);
    $customerName = $params["customerName"];
    $outMoney = $params["outMoney"];
    $log = "退还客户[{$customerName}]预收款：{$outMoney}元";
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  public function prereceivingList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new PreReceivingDAO($this->db());
    return $dao->prereceivingList($params);
  }

  public function prereceivingDetailList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new PreReceivingDAO($this->db());
    return $dao->prereceivingDetailList($params);
  }
}
