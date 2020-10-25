<?php

namespace Home\Service;

use Home\DAO\FormViewDAO;

/**
 * 视图开发助手Service
 *
 * @author 李静波
 */
class FormViewService extends PSIBaseExService
{
  private $LOG_CATEGORY = "视图开发助手";

  /**
   * 视图分类列表
   */
  public function categoryList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new FormViewDAO($this->db());
    return $dao->categoryList($params);
  }

  /**
   * 新增或编辑视图分类
   */
  public function editViewCategory($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $id = $params["id"];
    $name = $params["name"];

    $db = $this->db();
    $db->startTrans();

    $log = null;
    $dao = new FormViewDAO($db);
    if ($id) {
      // 编辑
      $rc = $dao->updateViewCategory($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $log = "编辑视图分类：{$name}";
    } else {
      // 新增
      $rc = $dao->addViewCategory($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $id = $params["id"];
      $log = "新增视图分类：{$name}";
    }

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 删除视图分类
   */
  public function deleteViewCategory($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new FormViewDAO($db);
    $rc = $dao->deleteViewCategory($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $name = $params["name"];
    $log = "删除视图分类：{$name}";

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 视图的列表
   */
  public function fvList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new FormViewDAO($this->db());
    return $dao->fvList($params);
  }

  /**
   * 视图分类自定义字段 - 查询数据
   */
  public function queryDataForFvCategory($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new FormViewDAO($this->db());
    return $dao->queryDataForFvCategory($params);
  }

  /**
   * 新增或编辑视图
   */
  public function editFv($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $id = $params["id"];
    $name = $params["name"];

    $pyService = new PinyinService();
    $py = $pyService->toPY($name);
    $params["py"] = $py;

    $db = $this->db();
    $db->startTrans();

    $log = null;
    $dao = new FormViewDAO($db);
    if ($id) {
      // 编辑
      $rc = $dao->updateFv($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $name = $params["name"];
      $log = "编辑视图[{$name}]的元数据";
    } else {
      // 新增
      $rc = $dao->addFv($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $id = $params["id"];
      $log = "新增视图：{$name}";
    }

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 某个视图的详情
   */
  public function fvInfo($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new FormViewDAO($this->db());
    return $dao->fvInfo($params);
  }

  /**
   * 查询某个fid的元数据
   */
  public function getMetadataForRuntimeInit($params)
  {
    if ($this->isNotOnline()) {
      return null;
    }

    $dao = new FormViewDAO($this->db());
    return $dao->getMetadataForRuntimeInit($params);
  }

  /**
   * 查询某个fid的完整元数据，用于创建UI
   */
  public function fetchMetaDataForRuntime($params)
  {
    if ($this->isNotOnline()) {
      return null;
    }

    $dao = new FormViewDAO($this->db());
    return $dao->fetchMetaDataForRuntime($params);
  }

  /**
   * 删除视图
   */
  public function deleteFv($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new FormViewDAO($db);
    $rc = $dao->deleteFv($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $name = $params["name"];
    $log = "删除视图：{$name}";

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 视图某个列的详情
   */
  public function fvColInfo($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new FormViewDAO($this->db());
    return $dao->fvColInfo($params);
  }

  /**
   * 新增或编辑视图列
   */
  public function editFvCol($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $id = $params["id"];

    $db = $this->db();
    $db->startTrans();

    $dao = new FormViewDAO($db);
    if ($id) {
      // 编辑列
      $rc = $dao->updateFvCol($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }
    } else {
      // 新增列 
      $rc = $dao->addFvCol($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }
      $id = $params["id"];
    }

    // 记录业务日志
    $log = $params["log"];
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 视图列
   */
  public function colList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new FormViewDAO($this->db());
    return $dao->colList($params);
  }

  /**
   * 删除视图列
   */
  public function deleteFvCol($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new FormViewDAO($db);
    $rc = $dao->deleteFvCol($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    // 记录业务日志
    $log = $params["log"];
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }
}
