<?php

namespace Home\Service;

use Home\DAO\PayablesDAO;

/**
 * 应付账款Service
 *
 * @author 李静波
 */
class PayablesService extends PSIBaseExService
{
  private $LOG_CATEGORY = "应付账款管理";

  /**
   * 往来单位分类
   */
  public function payCategoryList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();

    $dao = new PayablesDAO($this->db());
    return $dao->payCategoryList($params);
  }

  /**
   * 应付账款列表
   */
  public function payList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();

    $dao = new PayablesDAO($this->db());
    return $dao->payList($params);
  }

  /**
   * 每笔应付账款的明细记录
   */
  public function payDetailList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new PayablesDAO($this->db());
    return $dao->payDetailList($params);
  }

  /**
   * 应付账款的付款记录
   */
  public function payRecordList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new PayablesDAO($this->db());
    return $dao->payRecordList($params);
  }

  /**
   * 付款记录
   */
  public function addPayment($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $params["companyId"] = $this->getCompanyId();
    $params["dataOrg"] = $this->getLoginUserDataOrg();
    $params["loginUserId"] = $this->getLoginUserId();

    $db = $this->db();
    $db->startTrans();

    $dao = new PayablesDAO($db);
    $rc = $dao->addPayment($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $refType = $params["refType"];
    $refNumber = $params["refNumber"];
    $actMoney = $params["actMoney"];
    $log = "为 {$refType} - 单号：{$refNumber} 付款：{$actMoney}元";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  public function refreshPayInfo($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new PayablesDAO($this->db());
    return $dao->refreshPayInfo($params);
  }

  public function refreshPayDetailInfo($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new PayablesDAO($this->db());
    return $dao->refreshPayDetailInfo($params);
  }
}
