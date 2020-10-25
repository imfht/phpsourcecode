<?php

namespace Home\Service;

use Home\DAO\CustomerDAO;

/**
 * 客户Service
 *
 * @author 李静波
 */
class CustomerService extends PSIBaseExService
{
  private $LOG_CATEGORY = "客户关系-客户资料";

  /**
   * 客户分类列表
   *
   * @param array $params
   * @return array
   */
  public function categoryList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();

    $dao = new CustomerDAO($this->db());
    return $dao->categoryList($params);
  }

  /**
   * 新建或编辑客户分类
   */
  public function editCategory($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $id = $params["id"];
    $code = $params["code"];
    $name = $params["name"];

    $db = $this->db();
    $db->startTrans();

    $dao = new CustomerDAO($db);

    $log = null;

    if ($id) {
      // 编辑
      $rc = $dao->updateCustomerCategory($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $log = "编辑客户分类: 编码 = {$code}, 分类名 = {$name}";
    } else {
      // 新增

      $params["dataOrg"] = $this->getLoginUserDataOrg();
      $params["companyId"] = $this->getCompanyId();

      $rc = $dao->addCustomerCategory($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $id = $params["id"];

      $log = "新增客户分类：编码 = {$code}, 分类名 = {$name}";
    }

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 删除客户分类
   *
   * @param array $params
   * @return array
   */
  public function deleteCategory($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new CustomerDAO($db);

    $rc = $dao->deleteCustomerCategory($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $log = "删除客户分类： 编码 = {$params['code']}, 分类名称 = {$params['name']}";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 新建或编辑客户资料
   *
   * @param array $params
   * @return array
   */
  public function editCustomer($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $id = $params["id"];
    $code = $params["code"];
    $name = $params["name"];

    $ps = new PinyinService();
    $params["py"] = $ps->toPY($name);

    $db = $this->db();
    $db->startTrans();

    $dao = new CustomerDAO($db);

    $params["dataOrg"] = $this->getLoginUserDataOrg();
    $params["companyId"] = $this->getCompanyId();

    $category = $dao->getCustomerCategoryById($params["categoryId"]);
    if (!$category) {
      $db->rollback();
      return $this->bad("客户分类不存在");
    }

    $log = null;

    if ($id) {
      // 编辑
      $rc = $dao->updateCustomer($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $log = "编辑客户：编码 = {$code}, 名称 = {$name}";
    } else {
      // 新增
      $rc = $dao->addCustomer($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $id = $params["id"];

      $log = "新增客户：编码 = {$code}, 名称 = {$name}";
    }

    // 处理应收账款
    $rc = $dao->initReceivables($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 获得某个分类的客户列表
   *
   * @param array $params
   * @return array
   */
  public function customerList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();

    $dao = new CustomerDAO($this->db());
    return $dao->customerList($params);
  }

  /**
   * 删除客户资料
   *
   * @param array $params
   * @return array
   */
  public function deleteCustomer($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new CustomerDAO($db);

    $rc = $dao->deleteCustomer($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $code = $params["code"];
    $name = $params["name"];
    $log = "删除客户资料：编码 = {$code},  名称 = {$name}";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 客户字段，查询数据
   *
   * @param array $params
   * @return array
   */
  public function queryData($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();

    $dao = new CustomerDAO($this->db());
    return $dao->queryData($params);
  }

  /**
   * 获得某个客户的详情
   *
   * @param string $id
   *        	客户资料id
   * @return array
   */
  public function customerInfo($id)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new CustomerDAO($this->db());
    return $dao->customerInfo($id);
  }

  /**
   * 判断给定id的客户是否存在
   *
   * @param string $customerId
   *
   * @return true: 存在
   */
  public function customerExists($customerId, $db)
  {
    $dao = new CustomerDAO($db);

    $customer = $dao->getCustomerById($customerId);

    return $customer != null;
  }

  /**
   * 根据客户Id查询客户名称
   */
  public function getCustomerNameById($customerId, $db)
  {
    $dao = new CustomerDAO($db);

    $customer = $dao->getCustomerById($customerId);
    if ($customer) {
      return $customer["name"];
    } else {
      return "";
    }
  }

  /**
   * 获得所有的价格体系中的价格
   */
  public function priceSystemList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new CustomerDAO($this->db());
    return $dao->priceSystemList($params);
  }
}
