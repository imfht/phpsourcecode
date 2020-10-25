<?php

namespace Home\Controller;

use Home\Common\FIdConst;
use Home\Service\InventoryService;
use Home\Service\UserService;

require __DIR__ . '/../Common/Excel/PHPExcel/IOFactory.php';

/**
 * 库存Controller
 *
 * @author 李静波
 *        
 */
class InventoryController extends PSIBaseController
{

  /**
   * 库存建账 - 主页面
   */
  public function initIndex()
  {
    $us = new UserService();

    if ($us->hasPermission(FIdConst::INVENTORY_INIT)) {
      $this->initVar();

      $this->assign("title", "库存建账");

      $this->display();
    } else {
      $this->gotoLoginPage("/Home/Inventory/initIndex");
    }
  }

  /**
   * 库存账查询
   */
  public function inventoryQuery()
  {
    $us = new UserService();

    if ($us->hasPermission(FIdConst::INVENTORY_QUERY)) {
      $this->initVar();

      // 按钮权限：总账导出Excel
      $this->assign("pExcel", $us->hasPermission(FIdConst::INVENTORY_QUERY_EXPORT_EXCEL) ? "1" : "0");

      $this->assign("title", "库存账查询");

      $this->display();
    } else {
      $this->gotoLoginPage("/Home/Inventory/inventoryQuery");
    }
  }

  /**
   * 获得所有仓库列表
   */
  public function warehouseList()
  {
    if (IS_POST) {
      $is = new InventoryService();
      $this->ajaxReturn($is->warehouseList());
    }
  }

  /**
   * 库存总账
   */
  public function inventoryList()
  {
    if (IS_POST) {
      $params = [
        "warehouseId" => I("post.warehouseId"),
        "code" => I("post.code"),
        "name" => I("post.name"),
        "spec" => I("post.spec"),
        "brandId" => I("post.brandId"),
        "hasInv" => I("post.hasInv"),
        "sort" => I("post.sort"),
        "page" => I("post.page"),
        "start" => I("post.start"),
        "limit" => I("post.limit")
      ];
      $this->ajaxReturn((new InventoryService())->inventoryList($params));
    }
  }

  /**
   * 库存明细账
   */
  public function inventoryDetailList()
  {
    if (IS_POST) {
      $params = array(
        "warehouseId" => I("post.warehouseId"),
        "goodsId" => I("post.goodsId"),
        "dtFrom" => I("post.dtFrom"),
        "dtTo" => I("post.dtTo"),
        "page" => I("post.page"),
        "start" => I("post.start"),
        "limit" => I("post.limit")
      );
      $is = new InventoryService();
      $this->ajaxReturn($is->inventoryDetailList($params));
    }
  }

  /**
   * 总账导出Excel
   */
  public function exportExcel()
  {
    $us = new UserService();

    if ($us->hasPermission(FIdConst::INVENTORY_QUERY_EXPORT_EXCEL)) {
      $params = [
        "code" => I("get.code"),
        "name" => I("get.name"),
        "spec" => I("get.spec"),
        "brandId" => I("get.brandId"),
        "hasInv" => I("get.hasInv"),
      ];
      $service = new InventoryService();
      $service->exportExcel($params);
    } else {
      echo "没有导出Excel的权限";
    }
  }
}
