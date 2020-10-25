<?php

namespace Home\Controller;

use Think\Controller;
use Home\Service\UserService;
use Home\Service\BizConfigService;
use Home\Common\FIdConst;
use Home\Service\BillViewService;

/**
 * 查看单据Controller
 *
 * @author 李静波
 *        
 */
class BillController extends Controller
{

  /**
   * 判断当前用户对给定的fid是否有权限
   *
   * @param string $fid        	
   * @return bool true: 有权限
   */
  private function hasPermission($fid)
  {
    $pm = false;
    $idArray = array(
      FIdConst::INVENTORY_QUERY,
      FIdConst::PAYABLES,
      FIdConst::RECEIVING,
      FIdConst::CASH_INDEX,
      FIdConst::PRE_RECEIVING,
      FIdConst::PRE_PAYMENT,
      FIdConst::PURCHASE_ORDER,
      FIdConst::SALE_ORDER,
      FIdConst::PURCHASE_WAREHOUSE,
      FIdConst::PURCHASE_ORDER,
      FIdConst::DMW
    );

    if (in_array($fid, $idArray)) {
      $us = new UserService();
      $pm = $us->hasPermission($fid);
    }
    return $pm;
  }

  /**
   * 查看单据 - 跳转页面
   */
  public function viewIndex()
  {
    $fid = I("get.fid");
    $refType = I("get.refType");
    $ref = I("get.ref");

    switch ($refType) {
      case "采购订单":
        redirect(__ROOT__ . "/Home/Bill/viewPOBill?fid={$fid}&ref={$ref}");
        break;
      case "采购入库":
        redirect(__ROOT__ . "/Home/Bill/viewPWBill?fid={$fid}&ref={$ref}");
        break;
      case "成品委托生产入库":
        redirect(__ROOT__ . "/Home/Bill/viewDMWBill?fid={$fid}&ref={$ref}");
        break;
      case "采购退货出库":
        redirect(__ROOT__ . "/Home/Bill/viewPRBill?fid={$fid}&ref={$ref}");
        break;
      case "销售订单":
        redirect(__ROOT__ . "/Home/Bill/viewSOBill?fid={$fid}&ref={$ref}");
        break;
      case "销售出库":
        redirect(__ROOT__ . "/Home/Bill/viewWSBill?fid={$fid}&ref={$ref}");
        break;
      case "销售退货入库":
        redirect(__ROOT__ . "/Home/Bill/viewSRBill?fid={$fid}&ref={$ref}");
        break;
      case "调拨入库":
      case "调拨出库":
        redirect(__ROOT__ . "/Home/Bill/viewITBill?fid={$fid}&ref={$ref}");
        break;
      case "库存盘点-盘亏出库":
      case "库存盘点-盘盈入库":
        redirect(__ROOT__ . "/Home/Bill/viewICBill?fid={$fid}&ref={$ref}");
        break;
      case "存货拆分":
        redirect(__ROOT__ . "/Home/Bill/viewWSPBill?fid={$fid}&ref={$ref}");
        break;
      default:
        $this->display();
    }
  }

  /**
   * 查看采购订单
   */
  public function viewPOBill()
  {
    $fid = I("get.fid");
    if (!$this->hasPermission($fid)) {
      return;
    }

    $bcs = new BizConfigService();
    $this->assign("productionName", $bcs->getProductionName());

    $ref = I("get.ref");
    $this->assign("ref", $ref);

    $this->assign("title", "查看采购订单");
    $this->assign("uri", __ROOT__ . "/");

    $dtFlag = getdate();
    $this->assign("dtFlag", $dtFlag[0]);

    $this->display();
  }

  /**
   * 查看采购入库单
   */
  public function viewPWBill()
  {
    $fid = I("get.fid");
    if (!$this->hasPermission($fid)) {
      return;
    }

    $bcs = new BizConfigService();
    $this->assign("productionName", $bcs->getProductionName());

    $ref = I("get.ref");
    $this->assign("ref", $ref);

    $this->assign("title", "查看采购入库单");
    $this->assign("uri", __ROOT__ . "/");

    $dtFlag = getdate();
    $this->assign("dtFlag", $dtFlag[0]);

    $this->display();
  }

  /**
   * 查看成品委托生产入库单
   */
  public function viewDMWBill()
  {
    $fid = I("get.fid");
    if (!$this->hasPermission($fid)) {
      return;
    }

    $bcs = new BizConfigService();
    $this->assign("productionName", $bcs->getProductionName());

    $ref = I("get.ref");
    $this->assign("ref", $ref);

    $this->assign("title", "查看成品委托生产入库单");
    $this->assign("uri", __ROOT__ . "/");

    $dtFlag = getdate();
    $this->assign("dtFlag", $dtFlag[0]);

    $this->display();
  }

  /**
   * 采购订单 - 数据查询
   */
  public function poBillInfo()
  {
    if (IS_POST) {
      $ref = I("post.ref");

      $bs = new BillViewService();
      $this->ajaxReturn($bs->poBillInfo($ref));
    }
  }

  /**
   * 采购入库单 - 数据查询
   */
  public function pwBillInfo()
  {
    if (IS_POST) {
      $ref = I("post.ref");

      $bs = new BillViewService();
      $this->ajaxReturn($bs->pwBillInfo($ref));
    }
  }

  /**
   * 成品委托生产入库单 - 数据查询
   */
  public function dmwBillInfo()
  {
    if (IS_POST) {
      $ref = I("post.ref");

      $bs = new BillViewService();
      $this->ajaxReturn($bs->dmwBillInfo($ref));
    }
  }

  /**
   * 查看采购退货出库单
   */
  public function viewPRBill()
  {
    $fid = I("get.fid");
    if (!$this->hasPermission($fid)) {
      return;
    }

    $bcs = new BizConfigService();
    $this->assign("productionName", $bcs->getProductionName());

    $ref = I("get.ref");
    $this->assign("ref", $ref);

    $this->assign("title", "查看采购退货出库单");
    $this->assign("uri", __ROOT__ . "/");

    $dtFlag = getdate();
    $this->assign("dtFlag", $dtFlag[0]);

    $this->display();
  }

  /**
   * 采购退货出库单 - 数据查询
   */
  public function prBillInfo()
  {
    if (IS_POST) {
      $ref = I("post.ref");

      $bs = new BillViewService();
      $this->ajaxReturn($bs->prBillInfo($ref));
    }
  }

  /**
   * 查看销售出库单
   */
  public function viewWSBill()
  {
    $fid = I("get.fid");
    if (!$this->hasPermission($fid)) {
      return;
    }

    $bcs = new BizConfigService();
    $this->assign("productionName", $bcs->getProductionName());

    $ref = I("get.ref");
    $this->assign("ref", $ref);

    $this->assign("title", "查看销售出库单");
    $this->assign("uri", __ROOT__ . "/");

    $dtFlag = getdate();
    $this->assign("dtFlag", $dtFlag[0]);

    $this->display();
  }

  /**
   * 销售出库单 - 数据查询
   */
  public function wsBillInfo()
  {
    if (IS_POST) {
      $ref = I("post.ref");

      $bs = new BillViewService();
      $this->ajaxReturn($bs->wsBillInfo($ref));
    }
  }

  /**
   * 查看销售退货入库单
   */
  public function viewSRBill()
  {
    $fid = I("get.fid");
    if (!$this->hasPermission($fid)) {
      return;
    }

    $bcs = new BizConfigService();
    $this->assign("productionName", $bcs->getProductionName());

    $ref = I("get.ref");
    $this->assign("ref", $ref);

    $this->assign("title", "查看销售退货入库单");
    $this->assign("uri", __ROOT__ . "/");

    $dtFlag = getdate();
    $this->assign("dtFlag", $dtFlag[0]);

    $this->display();
  }

  /**
   * 销售退货入库单 - 数据查询
   */
  public function srBillInfo()
  {
    if (IS_POST) {
      $ref = I("post.ref");

      $bs = new BillViewService();
      $this->ajaxReturn($bs->srBillInfo($ref));
    }
  }

  /**
   * 查看调拨单
   */
  public function viewITBill()
  {
    $fid = I("get.fid");
    if (!$this->hasPermission($fid)) {
      return;
    }

    $bcs = new BizConfigService();
    $this->assign("productionName", $bcs->getProductionName());

    $ref = I("get.ref");
    $this->assign("ref", $ref);

    $this->assign("title", "查看调拨单");
    $this->assign("uri", __ROOT__ . "/");

    $dtFlag = getdate();
    $this->assign("dtFlag", $dtFlag[0]);

    $this->display();
  }

  /**
   * 调拨单 - 数据查询
   */
  public function itBillInfo()
  {
    if (IS_POST) {
      $ref = I("post.ref");

      $bs = new BillViewService();
      $this->ajaxReturn($bs->itBillInfo($ref));
    }
  }

  /**
   * 查看盘点单
   */
  public function viewICBill()
  {
    $fid = I("get.fid");
    if (!$this->hasPermission($fid)) {
      return;
    }

    $bcs = new BizConfigService();
    $this->assign("productionName", $bcs->getProductionName());

    $ref = I("get.ref");
    $this->assign("ref", $ref);

    $this->assign("title", "查看盘点单");
    $this->assign("uri", __ROOT__ . "/");

    $dtFlag = getdate();
    $this->assign("dtFlag", $dtFlag[0]);

    $this->display();
  }

  /**
   * 盘点单 - 数据查询
   */
  public function icBillInfo()
  {
    if (IS_POST) {
      $ref = I("post.ref");

      $bs = new BillViewService();
      $this->ajaxReturn($bs->icBillInfo($ref));
    }
  }

  /**
   * 查看拆分单
   */
  public function viewWSPBill()
  {
    $fid = I("get.fid");
    if (!$this->hasPermission($fid)) {
      return;
    }

    $bcs = new BizConfigService();
    $this->assign("productionName", $bcs->getProductionName());

    $ref = I("get.ref");
    $this->assign("ref", $ref);

    $this->assign("title", "查看拆分单");
    $this->assign("uri", __ROOT__ . "/");

    $dtFlag = getdate();
    $this->assign("dtFlag", $dtFlag[0]);

    $this->display();
  }

  /**
   * 拆分单 - 数据查询
   */
  public function wspBillInfo()
  {
    if (IS_POST) {
      $ref = I("post.ref");

      $bs = new BillViewService();
      $this->ajaxReturn($bs->wspBillInfo($ref));
    }
  }

  /**
   * 查看销售订单
   */
  public function viewSOBill()
  {
    $fid = I("get.fid");
    if (!$this->hasPermission($fid)) {
      return;
    }

    $bcs = new BizConfigService();
    $this->assign("productionName", $bcs->getProductionName());

    $ref = I("get.ref");
    $this->assign("ref", $ref);

    $this->assign("title", "查看销售订单");
    $this->assign("uri", __ROOT__ . "/");

    $dtFlag = getdate();
    $this->assign("dtFlag", $dtFlag[0]);

    $this->display();
  }

  /**
   * 销售订单 - 数据查询
   */
  public function soBillInfo()
  {
    if (IS_POST) {
      $ref = I("post.ref");

      $bs = new BillViewService();
      $this->ajaxReturn($bs->soBillInfo($ref));
    }
  }
}
