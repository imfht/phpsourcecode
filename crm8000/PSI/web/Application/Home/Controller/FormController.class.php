<?php

namespace Home\Controller;

use Home\Common\FIdConst;
use Home\Service\UserService;
use Home\Service\FormService;

/**
 * 自定义表单Controller
 *
 * @author 李静波
 *        
 */
class FormController extends PSIBaseController
{
  /**
   * 自定义表单运行 - 主页面
   */
  public function run()
  {
    $fid = I("get.fid");

    $us = new UserService();
    if ($us->hasPermission($fid)) {
      $this->initVar();

      $service = new FormService();

      $md = $service->getFormMetadataForViewInit($fid);

      if ($md) {
        $this->assign("title", $md["title"]);
        $this->assign("fid", $fid);

        $this->display();
      } else {

        // 错误的fid，跳转到首页
        $this->gotoLoginPage("/Home");
      }
    } else {
      $this->gotoLoginPage("/Home");
    }
  }

  /**
   * 查询表单元数据 - 运行界面用
   */
  public function getMetaDataForRuntime()
  {
    if (IS_POST) {
      $fid = I("post.fid");

      $us = new UserService;
      if (!$us->hasPermission($fid)) {
        die("没有权限");
      }

      $params = [
        "fid" => $fid
      ];

      $service = new FormService();
      $this->ajaxReturn($service->getFormMetaDataForRuntime($params));
    }
  }

  /**
   * 自定义表单 - 主页面
   */
  public function index()
  {
    $us = new UserService();

    if ($us->hasPermission(FIdConst::FORM_SYSTEM)) {
      $this->initVar();

      $this->assign("title", "自定义表单");

      $this->display();
    } else {
      $this->gotoLoginPage("/Home/Form/index");
    }
  }

  /**
   * 表单分类列表
   */
  public function categoryList()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::FORM_SYSTEM)) {
        die("没有权限");
      }

      $service = new FormService();
      $this->ajaxReturn($service->categoryList());
    }
  }

  /**
   * 新增或编辑表单分类
   */
  public function editFormCategory()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::FORM_SYSTEM)) {
        die("没有权限");
      }

      $params = [
        "id" => I("post.id"),
        "code" => I("post.code"),
        "name" => I("post.name")
      ];

      $service = new FormService();
      $this->ajaxReturn($service->editFormCategory($params));
    }
  }

  /**
   * 删除表单分类
   */
  public function deleteFormCategory()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::FORM_SYSTEM)) {
        die("没有权限");
      }

      $params = [
        "id" => I("post.id"),
      ];

      $service = new FormService();
      $this->ajaxReturn($service->deleteFormCategory($params));
    }
  }

  /**
   * 表单分类自定义字段 - 查询数据
   */
  public function queryDataForCategory()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::FORM_SYSTEM)) {
        die("没有权限");
      }

      $params = [
        "queryKey" => I("post.queryKey")
      ];

      $service = new FormService();
      $this->ajaxReturn($service->queryDataForCategory($params));
    }
  }

  /**
   * 新增或编辑表单
   */
  public function editForm()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::FORM_SYSTEM)) {
        die("没有权限");
      }

      $params = [
        "id" => I("post.id"),
        "categoryId" => I("post.categoryId"),
        "code" => I("post.code"),
        "name" => I("post.name"),
        "tableName" => I("post.tableName"),
        "moduleName" => I("post.moduleName"),
        "memo" => I("post.memo"),
      ];

      $service = new FormService();
      $this->ajaxReturn($service->editForm($params));
    }
  }

  /**
   * 某个分类下的表单列表
   */
  public function formList()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::FORM_SYSTEM)) {
        die("没有权限");
      }

      $params = [
        "categoryId" => I("post.categoryId")
      ];

      $service = new FormService();
      $this->ajaxReturn($service->formList($params));
    }
  }

  /**
   * 表单主表列
   */
  public function formColList()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::FORM_SYSTEM)) {
        die("没有权限");
      }

      $params = [
        "id" => I("post.id")
      ];

      $service = new FormService();
      $this->ajaxReturn($service->formColList($params));
    }
  }

  /**
   * 表单明细表列表
   */
  public function formDetailList()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::FORM_SYSTEM)) {
        die("没有权限");
      }

      $params = [
        "id" => I("post.id")
      ];

      $service = new FormService();
      $this->ajaxReturn($service->formDetailList($params));
    }
  }

  /**
   * 表单明细表的列的列表
   */
  public function formDetailColList()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::FORM_SYSTEM)) {
        die("没有权限");
      }

      $params = [
        "id" => I("post.id")
      ];

      $service = new FormService();
      $this->ajaxReturn($service->formDetailColList($params));
    }
  }

  /**
   * 删除表单元数据
   */
  public function deleteForm()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::FORM_SYSTEM)) {
        die("没有权限");
      }

      $params = [
        "id" => I("post.id")
      ];

      $service = new FormService();
      $this->ajaxReturn($service->deleteForm($params));
    }
  }

  /**
   * 获得表单主表元数据
   */
  public function formInfo()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::FORM_SYSTEM)) {
        die("没有权限");
      }

      $params = [
        "id" => I("post.id")
      ];

      $service = new FormService();
      $this->ajaxReturn($service->formInfo($params));
    }
  }

  /**
   * 返回表单主表列的信息
   */
  public function formColInfo()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::FORM_SYSTEM)) {
        die("没有权限");
      }

      $params = [
        "id" => I("post.id"),
        "formId" => ("post.formId")
      ];

      $service = new FormService();
      $this->ajaxReturn($service->formColInfo($params));
    }
  }

  /**
   * 新增或编辑表单主表列
   */
  public function editFormCol()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::FORM_SYSTEM)) {
        die("没有权限");
      }

      $params = [
        "id" => I("post.id"),
        "formId" => I("post.formId"),
        "caption" => I("post.caption"),
        "fieldName" => I("post.fieldName"),
        "fieldType" => I("post.fieldType"),
        "fieldLength" => I("post.fieldLength"),
        "fieldDecimal" => I("post.fieldDecimal"),
        "valueFrom" => I("post.valueFrom"),
        "valueFromTableName" => I("post.valueFromTableName"),
        "valueFromColName" => I("post.valueFromColName"),
        "valueFromColNameDisplay" => I("post.valueFromColNameDisplay"),
        "isVisible" => I("post.isVisible"),
        "mustInput" => I("post.mustInput"),
        "showOrder" => I("post.showOrder"),
        "editorXtype" => I("post.editorXtype"),
        "colSpan" => I("post.colSpan"),
        "widthInView" => I("post.widthInView"),
        "showOrderInView" => I("post.showOrderInView"),
        "memo" => I("post.memo")
      ];

      $service = new FormService();
      $this->ajaxReturn($service->editFormCol($params));
    }
  }

  /**
   * 删除表单主表列元数据
   */
  public function deleteFormCol()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::FORM_SYSTEM)) {
        die("没有权限");
      }

      $params = [
        "id" => I("post.id"),
        "formId" => I("post.formId"),
      ];

      $service = new FormService();
      $this->ajaxReturn($service->deleteFormCol($params));
    }
  }

  /**
   * 返回表单主表列的信息
   */
  public function formDetailColInfo()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::FORM_SYSTEM)) {
        die("没有权限");
      }

      $params = [
        "id" => I("post.id"),
        "formId" => ("post.formId")
      ];

      $service = new FormService();
      $this->ajaxReturn($service->formDetailColInfo($params));
    }
  }

  /**
   * 新增或编辑表单明细表列
   */
  public function editFormDetailCol()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::FORM_SYSTEM)) {
        die("没有权限");
      }

      $params = [
        "id" => I("post.id"),
        "formId" => I("post.formId"),
        "caption" => I("post.caption"),
        "fieldName" => I("post.fieldName"),
        "fieldType" => I("post.fieldType"),
        "fieldLength" => I("post.fieldLength"),
        "fieldDecimal" => I("post.fieldDecimal"),
        "valueFrom" => I("post.valueFrom"),
        "valueFromTableName" => I("post.valueFromTableName"),
        "valueFromColName" => I("post.valueFromColName"),
        "valueFromColNameDisplay" => I("post.valueFromColNameDisplay"),
        "isVisible" => I("post.isVisible"),
        "mustInput" => I("post.mustInput"),
        "showOrder" => I("post.showOrder"),
        "editorXtype" => I("post.editorXtype"),
        "widthInView" => I("post.widthInView"),
        "memo" => I("post.memo")
      ];

      $service = new FormService();
      $this->ajaxReturn($service->editFormDetailCol($params));
    }
  }

  /**
   * 删除表单明细表列元数据
   */
  public function deleteFormDetailCol()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::FORM_SYSTEM)) {
        die("没有权限");
      }

      $params = [
        "id" => I("post.id"),
        "formId" => I("post.formId"),
      ];

      $service = new FormService();
      $this->ajaxReturn($service->deleteFormDetailCol($params));
    }
  }
}
