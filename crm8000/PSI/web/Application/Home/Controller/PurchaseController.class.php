<?php

namespace Home\Controller;

use Home\Common\FIdConst;
use Home\Service\POBillService;
use Home\Service\PWBillService;
use Home\Service\UserService;

require_once __DIR__ . '/../Common/Excel/PHPExcel/IOFactory.php';

/**
 * 采购Controller
 *
 * @author 李静波
 *        
 */
class PurchaseController extends PSIBaseController
{

  /**
   * 采购入库 - 主页面
   */
  public function pwbillIndex()
  {
    $us = new UserService();

    if ($us->hasPermission(FIdConst::PURCHASE_WAREHOUSE)) {
      $this->initVar();

      $this->assign("title", "采购入库");

      // 按钮权限：新建采购入库单
      $this->assign("pAdd", $us->hasPermission(FIdConst::PURCHASE_WAREHOUSE_ADD) ? "1" : "0");

      // 按钮权限：编辑采购入库单
      $this->assign(
        "pEdit",
        $us->hasPermission(FIdConst::PURCHASE_WAREHOUSE_EDIT) ? "1" : "0"
      );

      // 按钮权限：删除采购入库单
      $this->assign(
        "pDelete",
        $us->hasPermission(FIdConst::PURCHASE_WAREHOUSE_DELETE) ? "1" : "0"
      );

      // 按钮权限：提交入库
      $this->assign(
        "pCommit",
        $us->hasPermission(FIdConst::PURCHASE_WAREHOUSE_COMMIT) ? "1" : "0"
      );

      // 按钮权限：单据生成PDF
      $this->assign(
        "pGenPDF",
        $us->hasPermission(FIdConst::PURCHASE_WAREHOUSE_PDF) ? "1" : "0"
      );

      // 按钮权限：新增商品
      // 在单据编辑界面中，明细表里面的商品弹出选择框中的新增商品按钮
      $this->assign("showAddGoodsButton", $us->hasPermission(FIdConst::GOODS_ADD) ? "1" : "0");

      // 字段权限：单价可见
      $this->assign(
        "pViewPrice",
        $us->hasPermission(FIdConst::PURCHASE_WAREHOUSE_CAN_VIEW_PRICE) ? "1" : "0"
      );

      // 按钮权限：打印
      $this->assign(
        "pPrint",
        $us->hasPermission(FIdConst::PURCHASE_WAREHOUSE_PRINT) ? "1" : "0"
      );

      $this->display();
    } else {
      $this->gotoLoginPage("/Home/Purchase/pwbillIndex");
    }
  }

  /**
   * 获得采购入库单主表列表
   */
  public function pwbillList()
  {
    if (IS_POST) {
      $ps = new PWBillService();
      $params = array(
        "billStatus" => I("post.billStatus"),
        "ref" => I("post.ref"),
        "fromDT" => I("post.fromDT"),
        "toDT" => I("post.toDT"),
        "warehouseId" => I("post.warehouseId"),
        "supplierId" => I("post.supplierId"),
        "paymentType" => I("post.paymentType"),
        "goodsId" => I("post.goodsId"),
        "start" => I("post.start"),
        "limit" => I("post.limit")
      );
      $this->ajaxReturn($ps->pwbillList($params));
    }
  }

  /**
   * 获得采购入库单的商品明细记录
   */
  public function pwBillDetailList()
  {
    if (IS_POST) {
      $pwbillId = I("post.pwBillId");
      $ps = new PWBillService();
      $this->ajaxReturn($ps->pwBillDetailList($pwbillId));
    }
  }

  /**
   * 新增或编辑采购入库单
   */
  public function editPWBill()
  {
    if (IS_POST) {
      $json = I("post.jsonStr");
      $ps = new PWBillService();
      $this->ajaxReturn($ps->editPWBill($json));
    }
  }

  /**
   * 获得采购入库单的信息
   */
  public function pwBillInfo()
  {
    if (IS_POST) {
      $params = array(
        "id" => I("post.id"),
        "pobillRef" => I("post.pobillRef")
      );

      $ps = new PWBillService();
      $this->ajaxReturn($ps->pwBillInfo($params));
    }
  }

  /**
   * 删除采购入库单
   */
  public function deletePWBill()
  {
    if (IS_POST) {
      $id = I("post.id");
      $ps = new PWBillService();
      $this->ajaxReturn($ps->deletePWBill($id));
    }
  }

  /**
   * 提交采购入库单
   */
  public function commitPWBill()
  {
    if (IS_POST) {
      $id = I("post.id");
      $ps = new PWBillService();
      $this->ajaxReturn($ps->commitPWBill($id));
    }
  }

  /**
   * 采购订单 - 主页面
   */
  public function pobillIndex()
  {
    $us = new UserService();

    if ($us->hasPermission(FIdConst::PURCHASE_ORDER)) {
      $this->initVar();

      $this->assign("title", "采购订单");

      $this->assign("pAdd", $us->hasPermission(FIdConst::PURCHASE_ORDER_ADD) ? "1" : "0");
      $this->assign("pEdit", $us->hasPermission(FIdConst::PURCHASE_ORDER_EDIT) ? "1" : "0");
      $this->assign(
        "pDelete",
        $us->hasPermission(FIdConst::PURCHASE_ORDER_DELETE) ? "1" : "0"
      );
      $this->assign(
        "pConfirm",
        $us->hasPermission(FIdConst::PURCHASE_ORDER_CONFIRM) ? "1" : "0"
      );
      $this->assign(
        "pGenPWBill",
        $us->hasPermission(FIdConst::PURCHASE_ORDER_GEN_PWBILL) ? "1" : "0"
      );
      $this->assign("showAddGoodsButton", $us->hasPermission(FIdConst::GOODS_ADD) ? "1" : "0");
      $this->assign(
        "pCloseBill",
        $us->hasPermission(FIdConst::PURCHASE_ORDER_CLOSE) ? "1" : "0"
      );
      $this->assign("pGenPDF", $us->hasPermission(FIdConst::PURCHASE_ORDER_PDF) ? "1" : "0");
      $this->assign("pGenExcel", $us->hasPermission(FIdConst::PURCHASE_ORDER_EXCEL) ? "1" : "0");
      $this->assign("pPrint", $us->hasPermission(FIdConst::PURCHASE_ORDER_PRINT) ? "1" : "0");

      $this->display();
    } else {
      $this->gotoLoginPage("/Home/Purchase/pobillIndex");
    }
  }

  /**
   * 获得采购订单主表信息列表
   */
  public function pobillList()
  {
    if (IS_POST) {
      $ps = new POBillService();
      $params = array(
        "billStatus" => I("post.billStatus"),
        "ref" => I("post.ref"),
        "fromDT" => I("post.fromDT"),
        "toDT" => I("post.toDT"),
        "supplierId" => I("post.supplierId"),
        "paymentType" => I("post.paymentType"),
        "goodsId" => I("post.goodsId"),
        "start" => I("post.start"),
        "limit" => I("post.limit")
      );
      $this->ajaxReturn($ps->pobillList($params));
    }
  }

  /**
   * 新增或编辑采购订单
   */
  public function editPOBill()
  {
    if (IS_POST) {
      $json = I("post.jsonStr");
      $ps = new POBillService();
      $this->ajaxReturn($ps->editPOBill($json));
    }
  }

  /**
   * 获得采购订单的信息
   */
  public function poBillInfo()
  {
    if (IS_POST) {
      $params = array(
        "id" => I("post.id"),
        "genBill" => I("post.genBill"),
        "sobillRef" => I("post.sobillRef")
      );

      $ps = new POBillService();
      $this->ajaxReturn($ps->poBillInfo($params));
    }
  }

  /**
   * 获得采购订单的明细信息
   */
  public function poBillDetailList()
  {
    if (IS_POST) {
      $us = new UserService();
      $companyId = $us->getCompanyId();

      $params = [
        "id" => I("post.id"),
        "companyId" => $companyId
      ];

      $ps = new POBillService();
      $this->ajaxReturn($ps->poBillDetailList($params));
    }
  }

  /**
   * 删除采购订单
   */
  public function deletePOBill()
  {
    if (IS_POST) {
      $params = array(
        "id" => I("post.id")
      );

      $ps = new POBillService();
      $this->ajaxReturn($ps->deletePOBill($params));
    }
  }

  /**
   * 审核采购订单
   */
  public function commitPOBill()
  {
    if (IS_POST) {
      $params = array(
        "id" => I("post.id")
      );

      $ps = new POBillService();
      $this->ajaxReturn($ps->commitPOBill($params));
    }
  }

  /**
   * 取消审核采购订单
   */
  public function cancelConfirmPOBill()
  {
    if (IS_POST) {
      $params = array(
        "id" => I("post.id")
      );

      $ps = new POBillService();
      $this->ajaxReturn($ps->cancelConfirmPOBill($params));
    }
  }

  /**
   * 采购订单 - 订单变更
   */
  public function changePurchaseOrder()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id"),
        "goodsCount" => I("post.goodsCount"),
        "goodsPrice" => I("post.goodsPrice")
      ];

      $ps = new POBillService();
      $this->ajaxReturn($ps->changePurchaseOrder($params));
    }
  }

  /**
   * 查询主表金额相关数据 - 订单变更后刷新界面用
   */
  public function getPOBillDataAterChangeOrder()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id")
      ];

      $ps = new POBillService();
      $this->ajaxReturn($ps->getPOBillDataAterChangeOrder($params));
    }
  }

  /**
   * 采购订单生成PDF文件
   */
  public function poBillPdf()
  {
    $params = array(
      "ref" => I("get.ref")
    );

    $ps = new POBillService();
    $ps->pdf($params);
  }

  /**
   * 采购订单生成Excel文件
   */
  public function poBillExcel()
  {
    // 检查权限
    $us = new UserService();
    if (!$us->hasPermission(FIdConst::PURCHASE_ORDER_EXCEL)) {
      die("没有权限");
    }

    $params = [
      "ref" => I("get.ref")
    ];

    $ps = new POBillService();
    $ps->excel($params);
  }

  /**
   * 采购入库单生成PDF文件
   */
  public function pwBillPdf()
  {
    $params = array(
      "ref" => I("get.ref")
    );

    $ps = new PWBillService();
    $ps->pdf($params);
  }

  /**
   * 采购订单执行的采购入库单信息
   */
  public function poBillPWBillList()
  {
    if (IS_POST) {
      $params = array(
        "id" => I("post.id")
      );

      $ps = new PWBillService();
      $this->ajaxReturn($ps->poBillPWBillList($params));
    }
  }

  /**
   * 关闭采购订单
   */
  public function closePOBill()
  {
    if (IS_POST) {
      $params = array(
        "id" => I("post.id")
      );

      $ps = new POBillService();
      $this->ajaxReturn($ps->closePOBill($params));
    }
  }

  /**
   * 取消关闭采购订单
   */
  public function cancelClosedPOBill()
  {
    if (IS_POST) {
      $params = array(
        "id" => I("post.id")
      );

      $ps = new POBillService();
      $this->ajaxReturn($ps->cancelClosedPOBill($params));
    }
  }

  /**
   * 生成打印采购订单的页面
   */
  public function genPOBillPrintPage()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id")
      ];

      $ss = new POBillService();
      $data = $ss->getPOBillDataForLodopPrint($params);
      $this->assign("data", $data);
      $this->display();
    }
  }

  /**
   * 生成打印采购入库单的页面
   */
  public function genPWBillPrintPage()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id")
      ];

      $ss = new PWBillService();
      $data = $ss->getPWBillDataForLodopPrint($params);
      $this->assign("data", $data);
      $this->display();
    }
  }
}
