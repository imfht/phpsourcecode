<?php

namespace Home\Service;

use Home\Common\FIdConst;
use Home\DAO\BizConfigDAO;

/**
 * 业务设置Service
 *
 * @author 李静波
 */
class BizConfigService extends PSIBaseExService
{
  private $LOG_CATEGORY = "业务设置";

  /**
   * 返回所有的配置项
   */
  public function allConfigs($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new BizConfigDAO($this->db());

    return $dao->allConfigs($params);
  }

  /**
   * 返回所有的配置项，附带着附加数据集
   */
  public function allConfigsWithExtData($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $us = new UserService();
    $params["loginUserId"] = $us->getLoginUserId();

    $dao = new BizConfigDAO($this->db());

    return $dao->allConfigsWithExtData($params);
  }

  /**
   * 保存配置项
   */
  public function edit($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();

    $db->startTrans();

    $params["isDemo"] = $this->isDemo();

    $dao = new BizConfigDAO($db);
    $rc = $dao->edit($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $db->commit();

    return $this->ok();
  }

  /**
   * 获得增值税税率
   */
  public function getTaxRate()
  {
    $us = new UserService();

    $dao = new BizConfigDAO($this->db());
    return $dao->getTaxRate($us->getCompanyId());
  }

  /**
   * 获得本产品名称，默认值是：PSI
   */
  public function getProductionName()
  {
    $us = new UserService();
    $params = array(
      "companyId" => $us->getCompanyId()
    );

    $dao = new BizConfigDAO($this->db());

    return $dao->getProductionName($params);
  }

  /**
   * 模块打开方式
   *
   * @return string
   */
  public function getModuleOpenType(): string
  {
    $us = new UserService();
    $companyId = $us->getCompanyId();

    if ($companyId == null) {
      return "0";
    }

    $dao = new BizConfigDAO($this->db());

    return $dao->getModuleOpenType($companyId);
  }

  /**
   * 获得存货计价方法
   * 0： 移动平均法
   * 1：先进先出法
   */
  public function getInventoryMethod()
  {
    // 2015-11-19 为发布稳定版本，临时取消先进先出法
    $result = 0;

    return $result;
  }

  /**
   * 获得采购订单单号前缀
   */
  public function getPOBillRefPre()
  {
    $result = "PO";

    $db = $this->db();
    $companyId = $this->getCompanyId();

    $id = "9003-01";
    $sql = "select value from t_config 
				where id = '%s' and company_id = '%s' ";
    $data = $db->query($sql, $id, $companyId);
    if ($data) {
      $result = $data[0]["value"];

      if ($result == null || $result == "") {
        $result = "PO";
      }
    }

    return $result;
  }

  /**
   * 获得采购入库单单号前缀
   */
  public function getPWBillRefPre()
  {
    $result = "PW";

    $db = $this->db();
    $companyId = $this->getCompanyId();

    $id = "9003-02";
    $sql = "select value from t_config 
				where id = '%s' and company_id = '%s' ";
    $data = $db->query($sql, $id, $companyId);
    if ($data) {
      $result = $data[0]["value"];

      if ($result == null || $result == "") {
        $result = "PW";
      }
    }

    return $result;
  }

  /**
   * 获得采购退货出库单单号前缀
   */
  public function getPRBillRefPre()
  {
    $result = "PR";

    $db = $this->db();
    $companyId = $this->getCompanyId();

    $id = "9003-03";
    $sql = "select value from t_config 
				where id = '%s' and company_id = '%s' ";
    $data = $db->query($sql, $id, $companyId);
    if ($data) {
      $result = $data[0]["value"];

      if ($result == null || $result == "") {
        $result = "PR";
      }
    }

    return $result;
  }

  /**
   * 获得销售出库单单号前缀
   */
  public function getWSBillRefPre()
  {
    $result = "WS";

    $db = $this->db();
    $companyId = $this->getCompanyId();

    $id = "9003-04";
    $sql = "select value from t_config 
				where id = '%s' and company_id = '%s' ";
    $data = $db->query($sql, $id, $companyId);
    if ($data) {
      $result = $data[0]["value"];

      if ($result == null || $result == "") {
        $result = "WS";
      }
    }

    return $result;
  }

  /**
   * 获得销售退货入库单单号前缀
   */
  public function getSRBillRefPre()
  {
    $result = "SR";

    $db = $this->db();
    $companyId = $this->getCompanyId();

    $id = "9003-05";
    $sql = "select value from t_config 
				where id = '%s' and company_id = '%s' ";
    $data = $db->query($sql, $id, $companyId);
    if ($data) {
      $result = $data[0]["value"];

      if ($result == null || $result == "") {
        $result = "SR";
      }
    }

    return $result;
  }

  /**
   * 获得调拨单单号前缀
   */
  public function getITBillRefPre()
  {
    $result = "IT";

    $db = $this->db();
    $companyId = $this->getCompanyId();

    $id = "9003-06";
    $sql = "select value from t_config 
				where id = '%s' and company_id = '%s' ";
    $data = $db->query($sql, $id, $companyId);
    if ($data) {
      $result = $data[0]["value"];

      if ($result == null || $result == "") {
        $result = "IT";
      }
    }

    return $result;
  }

  /**
   * 获得盘点单单号前缀
   */
  public function getICBillRefPre()
  {
    $result = "IC";

    $db = $this->db();
    $companyId = $this->getCompanyId();

    $id = "9003-07";
    $sql = "select value from t_config 
				where id = '%s' and company_id = '%s' ";
    $data = $db->query($sql, $id, $companyId);
    if ($data) {
      $result = $data[0]["value"];

      if ($result == null || $result == "") {
        $result = "IC";
      }
    }

    return $result;
  }

  /**
   * 获得当前用户可以设置的公司
   */
  public function getCompany()
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $db = $this->db();
    $result = array();

    $sql = "select id, name
				from t_org
				where (parent_id is null) ";
    $queryParams = array();

    $ds = new DataOrgService();
    $rs = $ds->buildSQL(FIdConst::BIZ_CONFIG, "t_org");

    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = array_merge($queryParams, $rs[1]);
    }

    $sql .= " order by org_code ";

    $data = $db->query($sql, $queryParams);
    foreach ($data as $i => $v) {
      $result[$i]["id"] = $v["id"];
      $result[$i]["name"] = $v["name"];
    }

    return $result;
  }

  /**
   * 获得销售订单单号前缀
   */
  public function getSOBillRefPre()
  {
    $result = "PO";

    $db = $this->db();
    $companyId = $this->getCompanyId();

    $id = "9003-08";
    $sql = "select value from t_config
				where id = '%s' and company_id = '%s' ";
    $data = $db->query($sql, $id, $companyId);
    if ($data) {
      $result = $data[0]["value"];

      if ($result == null || $result == "") {
        $result = "SO";
      }
    }

    return $result;
  }

  /**
   * 获得采购订单默认付款方式
   */
  public function getPOBillDefaultPayment()
  {
    $result = "0";

    $db = $this->db();
    $companyId = $this->getCompanyId();

    $id = "2001-02";
    $sql = "select value from t_config
				where id = '%s' and company_id = '%s' ";
    $data = $db->query($sql, $id, $companyId);
    if ($data) {
      $result = $data[0]["value"];

      if ($result == null || $result == "") {
        $result = "0";
      }
    }

    return $result;
  }

  /**
   * 获得采购入库单默认付款方式
   */
  public function getPWBillDefaultPayment()
  {
    $result = "0";

    $db = $this->db();
    $companyId = $this->getCompanyId();

    $id = "2001-03";
    $sql = "select value from t_config
				where id = '%s' and company_id = '%s' ";
    $data = $db->query($sql, $id, $companyId);
    if ($data) {
      $result = $data[0]["value"];

      if ($result == null || $result == "") {
        $result = "0";
      }
    }

    return $result;
  }

  /**
   * 获得销售出库单默认收款方式
   */
  public function getWSBillDefaultReceving()
  {
    $result = "0";

    $db = M();
    $us = new UserService();
    $companyId = $us->getCompanyId();

    $id = "2002-03";
    $sql = "select value from t_config
				where id = '%s' and company_id = '%s' ";
    $data = $db->query($sql, $id, $companyId);
    if ($data) {
      $result = $data[0]["value"];

      if ($result == null || $result == "") {
        $result = "0";
      }
    }

    return $result;
  }

  /**
   * 获得销售订单默认收款方式
   */
  public function getSOBillDefaultReceving()
  {
    $result = "0";

    $db = M();
    $us = new UserService();
    $companyId = $us->getCompanyId();

    $id = "2002-04";
    $sql = "select value from t_config
				where id = '%s' and company_id = '%s' ";
    $data = $db->query($sql, $id, $companyId);
    if ($data) {
      $result = $data[0]["value"];

      if ($result == null || $result == "") {
        $result = "0";
      }
    }

    return $result;
  }

  /**
   * 获得商品数量小数位数
   *
   * @return int
   */
  public function getGoodsCountDecNumber(): int
  {
    $us = new UserService();
    $companyId = $us->getCompanyId();

    if (!$companyId) {
      return 0;
    }

    $dao = new BizConfigDAO($this->db());

    return $dao->getGoodsCountDecNumber($companyId);
  }
}
