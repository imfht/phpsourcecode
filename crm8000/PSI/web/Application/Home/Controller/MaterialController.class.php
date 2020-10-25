<?php

namespace Home\Controller;

use Home\Common\FIdConst;
use Home\Service\UserService;
use Home\Service\BankService;
use Home\Service\MaterialService;

/**
 * 物料Controller
 *
 * @author 李静波
 *        
 */
class MaterialController extends PSIBaseController
{

  /**
   * 物料单位 - 主页面
   */
  public function unitIndex()
  {
    $us = new UserService();

    if ($us->hasPermission(FIdConst::MATERIAL_UNIT)) {
      $this->initVar();

      $this->assign("title", "物料单位");

      $this->display();
    } else {
      $this->gotoLoginPage("/Home/Material/unitIndex");
    }
  }

  /**
   * 获得所有的物料单位列表
   *
   */
  public function allUnits()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::MATERIAL_UNIT)) {
        $this->ajaxReturn([]);
        return;
      }

      $service = new MaterialService();
      $this->ajaxReturn($service->allUnits());
    }
  }

  /**
   * 新增或编辑物料单位
   */
  public function editUnit()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::MATERIAL_UNIT)) {
        $this->ajaxReturn($this->noPermission("物料单位"));
        return;
      }

      $params = [
        "id" => I("post.id"),
        "name" => I("post.name"),
        "code" => I("post.code"),
        "recordStatus" => I("post.recordStatus")
      ];

      $service = new MaterialService();
      $this->ajaxReturn($service->editUnit($params));
    }
  }

  /**
   * 删除物料单位
   */
  public function deleteUnit()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::MATERIAL_UNIT)) {
        $this->ajaxReturn($this->noPermission("物料单位"));
        return;
      }

      $params = [
        "id" => I("post.id")
      ];
      $service = new MaterialService();
      $this->ajaxReturn($service->deleteUnit($params));
    }
  }

  /**
   * 原材料主页面
   */
  public function rmIndex()
  {
    $us = new UserService();

    if ($us->hasPermission(FIdConst::RAW_MATERIAL)) {
      $this->initVar();

      $this->assign("title", "原材料");

      // 按钮权限：新增原材料分类
      $this->assign("pAddCategory", $us->hasPermission(FIdConst::RAW_MATERIAL_CATEGORY_ADD) ? 1 : 0);

      // 按钮权限：编辑原材料分类
      $this->assign(
        "pEditCategory",
        $us->hasPermission(FIdConst::RAW_MATERIAL_CATEGORY_EDIT) ? 1 : 0
      );

      // 按钮权限：删除原材料分类
      $this->assign(
        "pDeleteCategory",
        $us->hasPermission(FIdConst::RAW_MATERIAL_CATEGORY_DELETE) ? 1 : 0
      );

      // 按钮权限：新增原材料
      $this->assign("pAddRawMaterial", $us->hasPermission(FIdConst::RAW_MATERIAL_ADD) ? 1 : 0);

      // 按钮权限：编辑原材料
      $this->assign("pEditRawMaterial", $us->hasPermission(FIdConst::RAW_MATERIAL_EDIT) ? 1 : 0);

      // 按钮权限：删除原材料
      $this->assign("pDeleteRawMaterial", $us->hasPermission(FIdConst::RAW_MATERIAL_DELETE) ? 1 : 0);

      $this->display();
    } else {
      $this->gotoLoginPage("/Home/Material/rmIndex");
    }
  }

  /**
   * 获得原材料分类
   */
  public function allRawMaterialCategories()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::RAW_MATERIAL)) {
        die("没有权限");
      }

      $params = [
        "code" => I("post.code"),
        "name" => I("post.name"),
        "spec" => I("post.spec"),
      ];

      $service = new MaterialService();
      $this->ajaxReturn($service->allRawMaterialCategories($params));
    }
  }

  /**
   * 新增或编辑原材料分类
   */
  public function editRawMaterialCategory()
  {
    if (IS_POST) {
      $us = new UserService();
      if (I("post.id")) {
        // 编辑原材料分类
        if (!$us->hasPermission(FIdConst::RAW_MATERIAL_CATEGORY_EDIT)) {
          die("没有权限");
        }
      } else {
        // 新增原材料分类
        if (!$us->hasPermission(FIdConst::RAW_MATERIAL_CATEGORY_ADD)) {
          die("没有权限");
        }
      }

      $params = [
        "id" => I("post.id"),
        "code" => strtoupper(I("post.code")),
        "name" => I("post.name"),
        "parentId" => I("post.parentId"),
        "taxRate" => I("post.taxRate")
      ];

      $service = new MaterialService();
      $this->ajaxReturn($service->editRawMaterialCategory($params));
    }
  }

  /**
   * 获得某个分类的信息
   */
  public function getRawMaterialCategoryInfo()
  {
    if (IS_POST) {
      $us = new UserService;
      if (!$us->hasPermission(FIdConst::RAW_MATERIAL_CATEGORY_EDIT)) {
        die("没有权限");
      }

      $params = [
        "id" => I("post.id")
      ];

      $service = new MaterialService();
      $this->ajaxReturn($service->getRawMaterialCategoryInfo($params));
    }
  }

  /**
   * 删除原材料分类
   */
  public function deleteRawMaterialCategory()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::RAW_MATERIAL_CATEGORY_DELETE)) {
        die("没有权限");
      }

      $params = [
        "id" => I("post.id")
      ];

      $service = new MaterialService();
      $this->ajaxReturn($service->deleteRawMaterialCategory($params));
    }
  }

  /**
   * 获得原材料列表
   */
  public function rawMaterialList()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::RAW_MATERIAL)) {
        die("没有权限");
      }

      $params = [
        "categoryId" => I("post.categoryId"),
        "code" => I("post.code"),
        "name" => I("post.name"),
        "spec" => I("post.spec"),
        "start" => I("post.start"),
        "limit" => I("post.limit")
      ];

      $service = new MaterialService();
      $this->ajaxReturn($service->rawMaterialList($params));
    }
  }

  /**
   * 查询某个原材料的信息
   */
  public function rawMaterialInfo()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::RAW_MATERIAL)) {
        die("没有权限");
      }

      $id = I("post.id");
      $categoryId = I("post.categoryId");
      $service = new MaterialService();

      $data = $service->getRawMaterialInfo($id, $categoryId);
      $data["units"] = $service->allEnabledUnits();

      $this->ajaxReturn($data);
    }
  }

  /**
   * 新增或编辑原材料
   */
  public function editRawMaterial()
  {
    if (IS_POST) {
      $us = new UserService();
      if (I("post.id")) {
        // 编辑
        if (!$us->hasPermission(FIdConst::RAW_MATERIAL_EDIT)) {
          die("没有权限");
        }
      } else {
        // 新增
        if (!$us->hasPermission(FIdConst::RAW_MATERIAL_ADD)) {
          die("没有权限");
        }
      }

      $params = [
        "id" => I("post.id"),
        "categoryId" => I("post.categoryId"),
        "code" => strtoupper(I("post.code")),
        "name" => I("post.name"),
        "spec" => I("post.spec"),
        "unitId" => I("post.unitId"),
        "purchasePrice" => I("post.purchasePrice"),
        "memo" => I("post.memo"),
        "recordStatus" => I("post.recordStatus"),
        "taxRate" => I("post.taxRate")
      ];
      $service = new MaterialService();
      $this->ajaxReturn($service->editRawMaterial($params));
    }
  }

  /**
   * 获得所有的原材料种类数
   */
  public function getTotalRawMaterialCount()
  {
    if (IS_POST) {
      $params = [
        "code" => I("post.code"),
        "name" => I("post.name"),
        "spec" => I("post.spec"),
      ];

      $service = new MaterialService();
      $this->ajaxReturn($service->getTotalRawMaterialCount($params));
    }
  }

  /**
   * 删除原材料
   */
  public function deleteRawMaterial()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::RAW_MATERIAL_DELETE)) {
        die("没有权限");
      }

      $params = [
        "id" => I("post.id")
      ];
      $gs = new MaterialService();
      $this->ajaxReturn($gs->deleteRawMaterial($params));
    }
  }
}
