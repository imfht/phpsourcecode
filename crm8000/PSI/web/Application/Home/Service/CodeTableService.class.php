<?php

namespace Home\Service;

use Home\DAO\CodeTableDAO;

/**
 * 码表Service
 *
 * @author 李静波
 */
class CodeTableService extends PSIBaseExService
{
  private $LOG_CATEGORY = "码表设置";

  /**
   * 码表分类列表
   */
  public function categoryList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new CodeTableDAO($this->db());
    return $dao->categoryList($params);
  }

  /**
   * 新增或编辑码表分类
   */
  public function editCodeTableCategory($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $id = $params["id"];
    $name = $params["name"];

    $db = $this->db();
    $db->startTrans();

    $log = null;
    $dao = new CodeTableDAO($db);
    if ($id) {
      // 编辑
      $rc = $dao->updateCodeTableCategory($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $log = "编辑码表分类：{$name}";
    } else {
      // 新增
      $rc = $dao->addCodeTableCategory($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $id = $params["id"];
      $log = "新增码表分类：{$name}";
    }

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 删除码表分类
   */
  public function deleteCodeTableCategory($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new CodeTableDAO($db);
    $rc = $dao->deleteCodeTableCategory($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $name = $params["name"];
    $log = "删除码表分类：{$name}";

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 码表列表
   */
  public function codeTableList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new CodeTableDAO($this->db());
    return $dao->codeTableList($params);
  }

  /**
   * 码表分类自定义字段 - 查询数据
   */
  public function queryDataForCategory($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new CodeTableDAO($this->db());
    return $dao->queryDataForCategory($params);
  }

  /**
   * 新增或编辑码表
   */
  public function editCodeTable($params)
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
    $dao = new CodeTableDAO($db);
    if ($id) {
      // 编辑
      $rc = $dao->updateCodeTable($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $log = "编辑码表[{$name}]的元数据";
    } else {
      // 新增
      $rc = $dao->addCodeTable($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $id = $params["id"];
      $log = "新增码表：{$name}";
    }

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 某个码表的列
   */
  public function codeTableColsList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new CodeTableDAO($this->db());
    return $dao->codeTableColsList($params);
  }

  /**
   * 删除码表
   */
  public function deleteCodeTable($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new CodeTableDAO($db);
    $rc = $dao->deleteCodeTable($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $name = $params["name"];
    $log = "删除码表[{$name}]的元数据";

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 查询码表主表元数据
   */
  public function codeTableInfo($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new CodeTableDAO($this->db());
    return $dao->codeTableInfo($params);
  }

  /**
   * 根据fid获得码表的元数据
   *
   * @param string $fid
   * @return array
   */
  public function getMetaDataByFid($fid)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new CodeTableDAO($this->db());
    return $dao->getMetaDataByFid($fid);
  }

  /**
   * 查询码表元数据 - 运行界面用
   */
  public function getMetaDataForRuntime($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();
    $params["userService"] = new UserService();

    $dao = new CodeTableDAO($this->db());
    return $dao->getMetaDataForRuntime($params);
  }

  /**
   * 新增或编辑码表记录
   */
  public function editCodeTableRecord($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $id = $params["id"];

    $params["companyId"] = $this->getCompanyId();
    $params["loginUserId"] = $this->getLoginUserId();
    $params["dataOrg"] = $this->getLoginUserDataOrg();
    $params["userService"] = new UserService();

    $db = $this->db();
    $db->startTrans();

    $dao = new CodeTableDAO($db);
    if ($id) {
      // 编辑
      $rc = $dao->updateRecord($params, new PinyinService());
      if ($rc) {
        $db->rollback();
        return $rc;
      }
    } else {
      // 新增
      $rc = $dao->addRecord($params, new PinyinService());
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $id = $params["id"];
    }

    // 记录业务日志
    $log = $params["log"];
    $logCategory = $params["logCategory"];
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $logCategory);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 码表记录列表
   */
  public function codeTableRecordList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();
    $params["userService"] = new UserService();

    $dao = new CodeTableDAO($this->db());
    return $dao->codeTableRecordList($params);
  }

  /**
   * 码表记录 - 树状结构
   */
  public function codeTableRecordListForTreeView($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();
    $params["userService"] = new UserService();
    $dao = new CodeTableDAO($this->db());
    return $dao->codeTableRecordListForTreeView($params);
  }

  /**
   * 新增或编辑码表列
   */
  public function editCodeTableCol($params)
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
    $dao = new CodeTableDAO($db);
    if ($id) {
      // 编辑
      $rc = $dao->updateCodeTableCol($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }
    } else {
      // 新增
      $rc = $dao->addCodeTableCol($params);
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
   * 查询码表记录的详情
   */
  public function recordInfo($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();
    $params["userService"] = new UserService();

    $dao = new CodeTableDAO($this->db());
    return $dao->recordInfo($params);
  }


  /**
   * 删除码表记录
   */
  public function deleteCodeTableRecord($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $params["loginUserId"] = $this->getLoginUserId();
    $params["userService"] = new UserService();

    $db = $this->db();
    $db->startTrans();

    $dao = new CodeTableDAO($db);
    $rc = $dao->deleteRecord($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $log = $params["log"];
    $logCategory = $params["logCategory"];

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $logCategory);

    $db->commit();

    return $this->ok();
  }

  /**
   * 码表某列的详细信息
   */
  public function codeTableColInfo($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new CodeTableDAO($this->db());
    return $dao->codeTableColInfo($params);
  }

  /**
   * 删除码表列
   */
  public function deleteCodeTableCol($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new CodeTableDAO($db);
    $rc = $dao->deleteCodeTableCol($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $log = $params["log"];

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 码表记录引用字段 - 查询数据
   */
  public function queryDataForRecordRef($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();

    $dao = new CodeTableDAO($this->db());
    return $dao->queryDataForRecordRef($params);
  }

  /**
   * 把码表转化为系统固有码表
   */
  public function convertCodeTable($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    if ($this->isDemo()) {
      return $this->bad("在演示环境下，不能使用本功能，请见谅");
    }


    $db = $this->db();
    $db->startTrans();

    $dao = new CodeTableDAO($db);
    $rc = $dao->convertCodeTable($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $name = $params["name"];
    $log = "把码表[{$name}]转化为系统固有码表";

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 保存列视图布局
   */
  public function saveColViewLayout($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new CodeTableDAO($db);
    $rc = $dao->saveColViewLayout($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $name = $params["name"];
    $log = "保存码表[{$name}]列视图布局";

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 查询码表编辑界面字段的显示次序
   */
  public function queryCodeTableEditColShowOrder($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new CodeTableDAO($this->db());
    return $dao->queryCodeTableEditColShowOrder($params);
  }

  /**
   * 保存编辑界面字段显示次序
   */
  public function saveColEditShowOrder($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new CodeTableDAO($db);
    $rc = $dao->saveColEditShowOrder($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $name = $params["name"];
    $log = "保存码表[{$name}]编辑界面字段显示次序";

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 码表生成SQL
   */
  public function codeTableGenSQL($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new CodeTableDAO($this->db());
    return $dao->codeTableGenSQL($params);
  }
}
