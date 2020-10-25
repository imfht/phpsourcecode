<?php

namespace Home\Controller;

use Home\Common\FIdConst;
use Home\Service\UserService;
use Home\Service\SubjectService;

/**
 * 会计科目Controller
 *
 * @author 李静波
 *        
 */
class SubjectController extends PSIBaseController
{

  /**
   * 会计科目 - 主页面
   */
  public function index()
  {
    $us = new UserService();

    if ($us->hasPermission(FIdConst::GL_SUBJECT)) {
      $this->initVar();

      $this->assign("title", "会计科目");

      $this->display();
    } else {
      $this->gotoLoginPage("/Home/Subject/index");
    }
  }

  /**
   * 返回所有的公司列表
   */
  public function companyList()
  {
    if (IS_POST) {
      $service = new SubjectService();
      $this->ajaxReturn($service->companyList());
    }
  }

  /**
   * 某个公司的科目码列表
   */
  public function subjectList()
  {
    if (IS_POST) {
      $params = [
        "companyId" => I("post.companyId")
      ];

      $service = new SubjectService();
      $this->ajaxReturn($service->subjectList($params));
    }
  }

  /**
   * 初始国家标准科目
   */
  public function init()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id")
      ];

      $service = new SubjectService();
      $this->ajaxReturn($service->init($params));
    }
  }

  public function editSubject()
  {
    if (IS_POST) {
      $params = [
        "companyId" => I("post.companyId"),
        "id" => I("post.id"),
        "parentCode" => I("post.parentCode"),
        "code" => I("post.code"),
        "name" => I("post.name"),
        "isLeaf" => I("post.isLeaf")
      ];

      $service = new SubjectService();
      $this->ajaxReturn($service->editSubject($params));
    }
  }

  /**
   * 上级科目字段 - 查询数据
   */
  public function queryDataForParentSubject()
  {
    if (IS_POST) {
      $queryKey = I("post.queryKey");
      $companyId = I("post.companyId");

      $service = new SubjectService();
      $this->ajaxReturn($service->queryDataForParentSubject($queryKey, $companyId));
    }
  }

  /**
   * 某个科目的详情
   */
  public function subjectInfo()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id")
      ];

      $service = new SubjectService();
      $this->ajaxReturn($service->subjectInfo($params));
    }
  }

  /**
   * 删除科目
   */
  public function deleteSubject()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id")
      ];

      $service = new SubjectService();
      $this->ajaxReturn($service->deleteSubject($params));
    }
  }

  /**
   * 初始化科目的标准账样
   */
  public function initFmt()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id"),
        "companyId" => I("post.companyId")
      ];

      $service = new SubjectService();
      $this->ajaxReturn($service->initFmt($params));
    }
  }

  /**
   * 某个科目账样的属性列表
   */
  public function fmtPropList()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id"),
        "companyId" => I("post.companyId")
      ];

      $service = new SubjectService();
      $this->ajaxReturn($service->fmtPropList($params));
    }
  }

  /**
   * 某个科目账样的字段列表
   */
  public function fmtColsList()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id"),
        "companyId" => I("post.companyId")
      ];

      $service = new SubjectService();
      $this->ajaxReturn($service->fmtColsList($params));
    }
  }

  /**
   * 清空科目的标准账样
   */
  public function undoInitFmt()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id"),
        "companyId" => I("post.companyId")
      ];

      $service = new SubjectService();
      $this->ajaxReturn($service->undoInitFmt($params));
    }
  }

  /**
   * 新增或编辑账样字段
   */
  public function editFmtCol()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id"),
        "companyId" => I("post.companyId"),
        "subjectCode" => I("post.subjectCode"),
        "fieldName" => I("post.fieldName"),
        "fieldCaption" => I("post.fieldCaption"),
        "fieldType" => I("post.fieldType")
      ];

      $service = new SubjectService();
      $this->ajaxReturn($service->editFmtCol($params));
    }
  }

  /**
   * 获得某个账样字段的详情
   */
  public function fmtColInfo()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id")
      ];

      $service = new SubjectService();
      $this->ajaxReturn($service->fmtColInfo($params));
    }
  }

  /**
   * 删除某个账样字段
   */
  public function deleteFmtCol()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id")
      ];

      $service = new SubjectService();
      $this->ajaxReturn($service->deleteFmtCol($params));
    }
  }

  /**
   * 某个账样所有字段 - 设置字段显示次序用
   */
  public function fmtGridColsList()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id")
      ];

      $service = new SubjectService();
      $this->ajaxReturn($service->fmtGridColsList($params));
    }
  }

  /**
   * 编辑账样字段的显示次序
   */
  public function editFmtColShowOrder()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id"), // 科目id
        "idList" => I("post.idList")
      ];

      $service = new SubjectService();
      $this->ajaxReturn($service->editFmtColShowOrder($params));
    }
  }
}
