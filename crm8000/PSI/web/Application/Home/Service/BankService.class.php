<?php

namespace Home\Service;

use Home\DAO\BankDAO;
use Home\Common\FIdConst;
use Home\DAO\OrgDAO;

/**
 * 银行账户Service
 *
 * @author 李静波
 */
class BankService extends PSIBaseExService
{
  private $LOG_CATEGORY = "银行账户";

  /**
   * 返回所有的公司列表
   *
   * @return array
   */
  public function companyList()
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params = [
      "loginUserId" => $this->getLoginUserId(),
      "fid" => FIdConst::GL_BANK_ACCOUNT
    ];

    $dao = new OrgDAO($this->db());
    return $dao->getCompanyExList($params);
  }

  /**
   * 某个公司的银行账户
   *
   * @param array $params        	
   */
  public function bankList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();

    $dao = new BankDAO($this->db());
    return $dao->bankList($params);
  }

  /**
   * 新增或编辑银行账户
   *
   * @param array $params        	
   */
  public function editBank($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $id = $params["id"];
    $bankName = $params["bankName"];
    $bankNumber = $params["bankNumber"];

    $params["dataOrg"] = $this->getLoginUserDataOrg();

    $db = $this->db();
    $db->startTrans();

    $log = null;
    $dao = new BankDAO($db);
    if ($id) {
      // 编辑
      $rc = $dao->updateBank($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $log = "编辑银行账户：{$bankName}-{$bankNumber}";
    } else {
      // 新增
      $rc = $dao->addBank($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $id = $params["id"];
      $log = "新增银行账户：{$bankName}-{$bankNumber}";
    }

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 删除银行账户
   *
   * @param array $params        	
   */
  public function deleteBank($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new BankDAO($db);

    $rc = $dao->deleteBank($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    // 记录业务日志
    $bankName = $params["bankName"];
    $bankNumber = $params["bankNumber"];
    $log = "删除银行账户：{$bankName}-{$bankNumber}";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }
}
