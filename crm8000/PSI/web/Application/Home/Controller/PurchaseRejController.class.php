<?php

namespace Home\Controller;

use Home\Common\FIdConst;
use Home\Service\PRBillService;
use Home\Service\UserService;
use Home\Service\PWBillService;

/**
 * 采购退货出库Controller
 *
 * @author 李静波
 *        
 */
class PurchaseRejController extends PSIBaseController
{

  /**
   * 采购退货出库 - 主页面
   */
  public function index()
  {
    $us = new UserService();

    if ($us->hasPermission(FIdConst::PURCHASE_REJECTION)) {
      $this->initVar();

      $this->assign("title", "采购退货出库");

      $this->assign("pAdd", $us->hasPermission(FIdConst::PURCHASE_REJECTION_ADD) ? "1" : "0");
      $this->assign(
        "pEdit",
        $us->hasPermission(FIdConst::PURCHASE_REJECTION_EDIT) ? "1" : "0"
      );
      $this->assign(
        "pDelete",
        $us->hasPermission(FIdConst::PURCHASE_REJECTION_DELETE) ? "1" : "0"
      );
      $this->assign(
        "pCommit",
        $us->hasPermission(FIdConst::PURCHASE_REJECTION_COMMIT) ? "1" : "0"
      );
      $this->assign(
        "pGenPDF",
        $us->hasPermission(FIdConst::PURCHASE_REJECTION_PDF) ? "1" : "0"
      );
      $this->assign(
        "pPrint",
        $us->hasPermission(FIdConst::PURCHASE_REJECTION_PRINT) ? "1" : "0"
      );

      $this->display();
    } else {
      $this->gotoLoginPage("/Home/PurchaseRej/index");
    }
  }

  /**
   * 获得采购退货出库单主表信息列表
   */
  public function prbillList()
  {
    if (IS_POST) {
      $params = array(
        "billStatus" => I("post.billStatus"),
        "ref" => I("post.ref"),
        "fromDT" => I("post.fromDT"),
        "toDT" => I("post.toDT"),
        "warehouseId" => I("post.warehouseId"),
        "supplierId" => I("post.supplierId"),
        "receivingType" => I("post.receivingType"),
        "goodsId" => I("post.goodsId"),
        "page" => I("post.page"),
        "start" => I("post.start"),
        "limit" => I("post.limit")
      );

      $pr = new PRBillService();
      $this->ajaxReturn($pr->prbillList($params));
    }
  }

  /**
   * 获得采购退货出库单的信息
   */
  public function prBillInfo()
  {
    if (IS_POST) {
      $params = array(
        "id" => I("post.id")
      );

      $pr = new PRBillService();

      $this->ajaxReturn($pr->prBillInfo($params));
    }
  }

  /**
   * 新建或编辑采购退货出库单
   */
  public function editPRBill()
  {
    if (IS_POST) {
      $params = array(
        "jsonStr" => I("post.jsonStr")
      );

      $pr = new PRBillService();

      $this->ajaxReturn($pr->editPRBill($params));
    }
  }

  /**
   * 选择采购入库单
   */
  public function selectPWBillList()
  {
    if (IS_POST) {
      $params = array(
        "ref" => I("post.ref"),
        "supplierId" => I("post.supplierId"),
        "warehouseId" => I("post.warehouseId"),
        "fromDT" => I("post.fromDT"),
        "toDT" => I("post.toDT"),
        "page" => I("post.page"),
        "start" => I("post.start"),
        "limit" => I("post.limit")
      );

      $pr = new PRBillService();

      $this->ajaxReturn($pr->selectPWBillList($params));
    }
  }

  /**
   * 获得采购入库单的商品明细记录
   */
  public function pwBillDetailList()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.pwBillId")
      ];
      $ps = new PWBillService();
      $this->ajaxReturn($ps->pwBillDetailListForPRBill($params));
    }
  }

  /**
   * 查询要退货的采购入库单的信息
   */
  public function getPWBillInfoForPRBill()
  {
    if (IS_POST) {
      $params = array(
        "id" => I("post.id")
      );

      $pr = new PRBillService();

      $this->ajaxReturn($pr->getPWBillInfoForPRBill($params));
    }
  }

  /**
   * 采购退货出库单的明细
   */
  public function prBillDetailList()
  {
    if (IS_POST) {
      $params = array(
        "id" => I("post.id")
      );

      $pr = new PRBillService();

      $this->ajaxReturn($pr->prBillDetailList($params));
    }
  }

  /**
   * 删除采购退货出库单
   */
  public function deletePRBill()
  {
    if (IS_POST) {
      $params = array(
        "id" => I("post.id")
      );

      $pr = new PRBillService();

      $this->ajaxReturn($pr->deletePRBill($params));
    }
  }

  /**
   * 提交采购退货出库单
   */
  public function commitPRBill()
  {
    if (IS_POST) {
      $params = array(
        "id" => I("post.id")
      );

      $pr = new PRBillService();

      $this->ajaxReturn($pr->commitPRBill($params));
    }
  }

  /**
   * 采购退货出库单生成PDF文件
   */
  public function prBillPdf()
  {
    $params = array(
      "ref" => I("get.ref")
    );

    $ps = new PRBillService();
    $ps->pdf($params);
  }

  /**
   * 生成打印采购退货出库单的页面
   */
  public function genPRBillPrintPage()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id")
      ];

      $ss = new PRBillService();
      $data = $ss->getPRBillDataForLodopPrint($params);
      $this->assign("data", $data);
      $this->display();
    }
  }
}
