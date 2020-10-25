<?php

namespace Home\Service;

use Home\DAO\SubjectDAO;
use Home\Common\FIdConst;
use Home\DAO\OrgDAO;

/**
 * 会计科目 Service
 *
 * @author 李静波
 */
class SubjectService extends PSIBaseExService
{
  private $LOG_CATEGORY = "会计科目";

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
      "fid" => FIdConst::GL_SUBJECT
    ];

    $dao = new OrgDAO($this->db());
    return $dao->getCompanyExList($params);
  }

  /**
   * 某个公司的科目码列表
   *
   * @param array $params
   * @return array
   */
  public function subjectList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new SubjectDAO($this->db());
    return $dao->subjectList($params);
  }

  /**
   * 初始国家标准科目
   */
  public function init($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $params["dataOrg"] = $this->getLoginUserDataOrg();

    $db = $this->db();
    $db->startTrans();

    $dao = new SubjectDAO($db);
    $rc = $dao->init($params, new PinyinService());
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    // 记录业务日志
    $companyName = $params["companyName"];
    $log = "为[{$companyName}]初始化国家标准会计科目";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 新增或编辑会计科目
   *
   * @param array $params
   * @return array
   */
  public function editSubject($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $id = $params["id"];
    $code = $params["code"];

    $params["dataOrg"] = $this->getLoginUserDataOrg();

    $db = $this->db();
    $db->startTrans();

    $log = null;
    $dao = new SubjectDAO($db);
    if ($id) {
      // 编辑
      $rc = $dao->updateSubject($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }
      $log = "编辑科目：{$code}";
    } else {
      // 新增
      $rc = $dao->addSubject($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }
      $id = $params["id"];

      $log = "新增科目：{$code}";
    }

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 上级科目字段 - 查询数据
   *
   * @param string $queryKey
   */
  public function queryDataForParentSubject($queryKey, $companyId)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new SubjectDAO($this->db());
    return $dao->queryDataForParentSubject($queryKey, $companyId);
  }

  /**
   * 某个科目的详情
   *
   * @param array $params
   */
  public function subjectInfo($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new SubjectDAO($this->db());
    return $dao->subjectInfo($params);
  }

  /**
   * 删除科目
   *
   * @param array $params
   */
  public function deleteSubject($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();

    $db->startTrans();
    $dao = new SubjectDAO($db);

    $rc = $dao->deleteSubject($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    // 记录业务日志
    $code = $params["code"];
    $log = "删除科目: $code";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 初始化科目的标准账样
   *
   * @param array $params
   */
  public function initFmt($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();

    $db->startTrans();
    $dao = new SubjectDAO($db);

    $params["dataOrg"] = $this->getLoginUserDataOrg();
    $rc = $dao->initFmt($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    // 记录业务日志
    $code = $params["code"];
    $log = "初始化科目: $code 的标准账样";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 某个科目的账样属性
   *
   * @param array $params
   */
  public function fmtPropList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new SubjectDAO($this->db());
    return $dao->fmtPropList($params);
  }

  /**
   * 某个科目的账样字段列表
   *
   * @param array $params
   */
  public function fmtColsList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new SubjectDAO($this->db());
    return $dao->fmtColsList($params);
  }

  /**
   * 清空科目的标准账样
   */
  public function undoInitFmt($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();

    $db->startTrans();
    $dao = new SubjectDAO($db);

    $rc = $dao->undoInitFmt($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    // 记录业务日志
    $code = $params["code"];
    $log = "清空科目: $code 的标准账样";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 新增或编辑账样字段
   */
  public function editFmtCol($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $id = $params["id"];
    $subjectCode = $params["subjectCode"];
    $fieldCaption = $params["fieldCaption"];

    $db = $this->db();

    $db->startTrans();
    $dao = new SubjectDAO($db);

    $log = null;
    if ($id) {
      // 编辑账样字段
      $rc = $dao->updateFmtCol($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $log = "编辑科目[{$subjectCode}]的账样字段[{$fieldCaption}]";
    } else {
      // 新增账样字段
      $rc = $dao->addFmtCol($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }
      $log = "新增科目[{$subjectCode}]的账样字段[{$fieldCaption}]";

      $id = $params["id"];
    }

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 获得某个账样字段的详情
   */
  public function fmtColInfo($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new SubjectDAO($this->db());
    return $dao->fmtColInfo($params);
  }

  /**
   * 删除某个账样字段
   */
  public function deleteFmtCol($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();

    $db->startTrans();
    $dao = new SubjectDAO($db);

    $rc = $dao->deleteFmtCol($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    // 记录业务日志
    $caption = $params["caption"];
    $code = $params["subjectCode"];
    $accNumber = $params["accNumber"];
    $log = "删除科目[{$code}]账样[账样号 = {$accNumber}]的字段: $caption";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 某个账样所有字段 - 设置字段显示次序用
   */
  public function fmtGridColsList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new SubjectDAO($this->db());
    return $dao->fmtGridColsList($params);
  }

  /**
   * 编辑账样字段的显示次序
   */
  public function editFmtColShowOrder($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();

    $db->startTrans();
    $dao = new SubjectDAO($db);

    $rc = $dao->editFmtColShowOrder($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    // 记录业务日志
    $code = $params["subjectCode"];
    $log = "编辑科目[{$code}]账样字段的显示次序";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }
}
