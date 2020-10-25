<?php

namespace Home\Service;

use Home\DAO\MaterialUnitDAO;
use Home\DAO\RawMaterialCategoryDAO;
use Home\DAO\RawMaterialDAO;

/**
 * 物料Service
 *
 * @author 李静波
 */
class MaterialService extends PSIBaseExService
{
  private $LOG_CATEGORY_UNIT = "物料单位";
  private $LOG_CATEGORY_RAW_MATERIAL = "原材料";
  private $LOG_CATEGORY = "物料";

  /**
   * 返回所有物料单位
   */
  public function allUnits()
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new MaterialUnitDAO($this->db());

    return $dao->allUnits();
  }

  /**
   * 新建或者编辑物料单位
   */
  public function editUnit($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $id = $params["id"];
    $name = $params["name"];

    $db = $this->db();
    $db->startTrans();

    $dao = new MaterialUnitDAO($db);

    $log = null;

    if ($id) {
      // 编辑

      $rc = $dao->updateUnit($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $log = "编辑物料单位: $name";
    } else {
      // 新增

      $params["dataOrg"] = $this->getLoginUserDataOrg();
      $params["companyId"] = $this->getCompanyId();

      $rc = $dao->addUnit($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $id = $params["id"];

      $log = "新增物料单位: $name";
    }

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY_UNIT);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 删除物料单位
   */
  public function deleteUnit($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new MaterialUnitDAO($db);

    $rc = $dao->deleteUnit($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $name = $params["name"];
    $log = "删除物料单位: $name";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY_UNIT);

    $db->commit();

    return $this->ok();
  }

  /**
   * 返回所有的原材料分类
   */
  public function allRawMaterialCategories($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();

    $dao = new RawMaterialCategoryDAO($this->db());
    return $dao->allRawMaterialCategories($params);
  }

  /**
   * 新建或者编辑原材料分类
   */
  public function editRawMaterialCategory($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $id = $params["id"];
    $code = $params["code"];
    $name = $params["name"];

    $db = $this->db();
    $db->startTrans();

    $dao = new RawMaterialCategoryDAO($db);

    if ($id) {
      // 编辑
      $rc = $dao->updateRawMaterialCategory($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $log = "编辑原材料分类: 编码 = {$code}， 分类名称 = {$name}";
    } else {
      // 新增

      $params["dataOrg"] = $this->getLoginUserDataOrg();
      $params["companyId"] = $this->getCompanyId();

      $rc = $dao->addRawMaterialCategory($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $id = $params["id"];

      $log = "新增原材料分类: 编码 = {$code}， 分类名称 = {$name}";
    }

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY_RAW_MATERIAL);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 获得某个原材料分类的详情
   */
  public function getRawMaterialCategoryInfo($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new RawMaterialCategoryDAO($this->db());
    return $dao->getRawMaterialCategoryInfo($params);
  }

  /**
   * 删除原材料分类
   */
  public function deleteRawMaterialCategory($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new RawMaterialCategoryDAO($db);

    $rc = $dao->deleteRawMaterialCategory($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $code = $params["code"];
    $name = $params["name"];
    $log = "删除原材料分类：  编码 = {$code}， 分类名称 = {$name}";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY_RAW_MATERIAL);

    $db->commit();

    return $this->ok();
  }

  /**
   * 原材料列表
   */
  public function rawMaterialList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();

    $dao = new RawMaterialDAO($this->db());
    return $dao->rawMaterialList($params);
  }

  /**
   * 获得某个原材料的详情
   */
  public function getRawMaterialInfo($id, $categoryId)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params = [
      "id" => $id,
      "categoryId" => $categoryId
    ];

    $dao = new RawMaterialDAO($this->db());
    return $dao->getRawMaterialInfo($params);
  }

  /**
   * 所有的启用的物料单位
   */
  public function allEnabledUnits()
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new MaterialUnitDAO($this->db());
    return $dao->allEnabledUnits();
  }

  /**
   * 新建或编辑原材料
   */
  public function editRawMaterial($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $id = $params["id"];
    $code = $params["code"];
    $name = $params["name"];
    $spec = $params["spec"];

    $db = $this->db();
    $db->startTrans();
    $dao = new RawMaterialDAO($db);

    $ps = new PinyinService();
    $params["py"] = $ps->toPY($name);
    $params["specPY"] = $ps->toPY($spec);

    $log = null;

    if ($id) {
      // 编辑
      $rc = $dao->updateRawMaterial($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $log = "编辑原材料: 编码 = {$code}, 名称 = {$name}, 规格型号 = {$spec}";
    } else {
      // 新增

      $params["dataOrg"] = $this->getLoginUserDataOrg();
      $params["companyId"] = $this->getCompanyId();

      $rc = $dao->addRawMaterial($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $id = $params["id"];

      $log = "新增原材料: 编码 = {$code}, 名称 = {$name}, 规格型号 = {$spec}";
    }

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY_RAW_MATERIAL);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 查询原材料种类总数
   */
  public function getTotalRawMaterialCount($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();

    $dao = new RawMaterialDAO($this->db());
    return $dao->getTotalRawMaterialCount($params);
  }

  /**
   * 删除原材料
   */
  public function deleteRawMaterial($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new RawMaterialDAO($db);

    $rc = $dao->deleteRawMaterial($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $code = $params["code"];
    $name = $params["name"];
    $spec = $params["spec"];
    $log = "删除原材料： 编码 = {$code}， 名称 = {$name}，规格型号 = {$spec}";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY_RAW_MATERIAL);

    $db->commit();

    return $this->ok();
  }
}
