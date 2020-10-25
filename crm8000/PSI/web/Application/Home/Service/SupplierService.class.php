<?php

namespace Home\Service;

use Home\DAO\SubjectDAO;
use Home\DAO\SupplierDAO;

/**
 * 供应商档案Service
 *
 * @author 李静波
 */
class SupplierService extends PSIBaseExService
{
  private $LOG_CATEGORY = "基础数据-供应商档案";

  /**
   * 供应商分类列表
   */
  public function categoryList($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $params["loginUserId"] = $this->getLoginUserId();

    $dao = new SupplierDAO($this->db());
    return $dao->categoryList($params);
  }

  /**
   * 某个分类下的供应商档案列表
   */
  public function supplierList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();

    $dao = new SupplierDAO($this->db());
    return $dao->supplierList($params);
  }

  /**
   * 新建或编辑供应商分类
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

    $dao = new SupplierDAO($db);

    $log = null;

    if ($id) {
      // 编辑
      $rc = $dao->updateSupplierCategory($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $log = "编辑供应商分类: 编码 = $code, 分类名 = $name";
    } else {
      // 新增

      $params["dataOrg"] = $this->getLoginUserDataOrg();
      $params["companyId"] = $this->getCompanyId();

      $rc = $dao->addSupplierCategory($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $id = $params["id"];

      $log = "新增供应商分类：编码 = $code, 分类名 = $name";
    }

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 删除供应商分类
   */
  public function deleteCategory($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();
    $dao = new SupplierDAO($db);

    $rc = $dao->deleteSupplierCategory($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $log = "删除供应商分类： 编码 = {$params['code']}, 分类名称 = {$params['name']}";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 新建或编辑供应商档案
   */
  public function editSupplier($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $id = $params["id"];
    $code = $params["code"];
    $name = $params["name"];

    $ps = new PinyinService();
    $py = $ps->toPY($name);
    $params["py"] = $py;

    $params["dataOrg"] = $this->getLoginUserDataOrg();
    $params["companyId"] = $this->getCompanyId();

    $categoryId = $params["categoryId"];

    $db = $this->db();
    $db->startTrans();

    $dao = new SupplierDAO($db);

    $category = $dao->getSupplierCategoryById($categoryId);
    if (!$category) {
      $db->rollback();
      return $this->bad("供应商分类不存在");
    }

    $log = null;

    if ($id) {
      // 编辑
      $rc = $dao->updateSupplier($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $log = "编辑供应商：编码 = $code, 名称 = $name";
    } else {
      // 新增
      $rc = $dao->addSupplier($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $id = $params["id"];

      $log = "新增供应商：编码 = {$code}, 名称 = {$name}";
    }

    // 处理应付期初余额
    $rc = $dao->initPayables($params);
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
   * 删除供应商
   */
  public function deleteSupplier($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new SupplierDAO($db);

    $rc = $dao->deleteSupplier($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $code = $params["code"];
    $name = $params["name"];
    $log = "删除供应商档案：编码 = {$code},  名称 = {$name}";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 供应商字段， 查询数据
   */
  public function queryData($queryKey)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params = array(
      "queryKey" => $queryKey,
      "loginUserId" => $this->getLoginUserId()
    );

    $dao = new SupplierDAO($this->db());
    return $dao->queryData($params);
  }

  /**
   * 获得某个供应商档案的详情
   */
  public function supplierInfo($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new SupplierDAO($this->db());
    return $dao->supplierInfo($params);
  }

  /**
   * 判断供应商是否存在
   */
  public function supplierExists($supplierId, $db)
  {
    $dao = new SupplierDAO($db);

    $supplier = $dao->getSupplierById($supplierId);
    return $supplier != null;
  }

  /**
   * 根据供应商Id查询供应商名称
   */
  public function getSupplierNameById($supplierId, $db)
  {
    $dao = new SupplierDAO($db);

    $supplier = $dao->getSupplierById($supplierId);
    if ($supplier) {
      return $supplier["name"];
    } else {
      return "";
    }
  }

  /**
   * 关联物料 - 添加物料分类
   */
  public function addGRCategory($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new SupplierDAO($db);

    $rc = $dao->addGRCategory($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $code = $params["code"];
    $name = $params["name"];
    $categoryCode = $params["categoryCode"];
    $categoryName = $params["categoryName"];
    $log = "给供应商(编码 = {$code},  名称 = {$name})设置关联物料分类(编码={$categoryCode} 分类={$categoryName})";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 关联商品 - 已经设置的商品分类
   */
  public function grCategoryList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new SubjectDAO($this->db());
    return $dao->grCategoryList($params);
  }

  /**
   * 关联物料 - 移除物料分类
   */
  public function deleteGRCategory($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new SupplierDAO($db);

    $rc = $dao->deleteGRCategory($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $code = $params["code"];
    $name = $params["name"];
    $log = "给供应商(编码 = {$code},  名称 = {$name})移除关联物料分类";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 关联商品 - 已经设置的商品
   */
  public function grGoodsList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new SupplierDAO($this->db());
    return $dao->grGoodsList($params);
  }

  /**
   * 关联物料 - 添加个别物料
   */
  public function addGRGoods($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new SupplierDAO($db);

    $rc = $dao->addGRGoods($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $code = $params["code"];
    $name = $params["name"];
    $goodsName = $params["goodsName"];
    $log = "给供应商(编码 = {$code},  名称 = {$name})设置关联个别物料({$goodsName})";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 关联物料 - 移除物料
   */
  public function deleteGRGoods($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new SupplierDAO($db);

    $rc = $dao->deleteGRGoods($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $code = $params["code"];
    $name = $params["name"];
    $log = "给供应商(编码 = {$code},  名称 = {$name})移除关联物料";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }
}
