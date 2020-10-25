<?php

namespace Home\Controller;

use Home\Common\FIdConst;
use Home\Service\SOBillService;
use Home\Service\SRBillService;
use Home\Service\UserService;
use Home\Service\WSBillService;

/**
 * 销售Controller
 *
 * @author 李静波
 *        
 */
class SaleController extends PSIBaseController
{

  /**
   * 销售订单 - 主页面
   */
  public function soIndex()
  {
    $us = new UserService();

    if ($us->hasPermission(FIdConst::SALE_ORDER)) {
      $this->initVar();

      $this->assign("title", "销售订单");

      $this->assign("pConfirm", $us->hasPermission(FIdConst::SALE_ORDER_CONFIRM) ? "1" : "0");
      $this->assign(
        "pGenWSBill",
        $us->hasPermission(FIdConst::SALE_ORDER_GEN_WSBILL) ? "1" : "0"
      );
      $this->assign(
        "pGenPOBill",
        $us->hasPermission(FIdConst::SALE_ORDER_GEN_POBILL) ? "1" : "0"
      );

      $this->assign("pAdd", $us->hasPermission(FIdConst::SALE_ORDER_ADD) ? "1" : "0");
      $this->assign("pEdit", $us->hasPermission(FIdConst::SALE_ORDER_EDIT) ? "1" : "0");
      $this->assign("pDelete", $us->hasPermission(FIdConst::SALE_ORDER_DELETE) ? "1" : "0");
      $this->assign("pGenPDF", $us->hasPermission(FIdConst::SALE_ORDER_PDF) ? "1" : "0");
      $this->assign("pPrint", $us->hasPermission(FIdConst::SALE_ORDER_PRINT) ? "1" : "0");
      $this->assign(
        "pCloseBill",
        $us->hasPermission(FIdConst::SALE_ORDER_CLOSE_BILL) ? "1" : "0"
      );
      $this->assign("showAddGoodsButton", $us->hasPermission(FIdConst::GOODS_ADD) ? "1" : "0");

      $this->display();
    } else {
      $this->gotoLoginPage("/Home/Sale/soIndex");
    }
  }

  /**
   * 销售出库 - 主页面
   */
  public function wsIndex()
  {
    $us = new UserService();

    if ($us->hasPermission(FIdConst::WAREHOUSING_SALE)) {
      $this->initVar();

      $this->assign("title", "销售出库");

      $this->assign("pAdd", $us->hasPermission(FIdConst::WAREHOUSING_SALE_ADD) ? "1" : "0");
      $this->assign("pEdit", $us->hasPermission(FIdConst::WAREHOUSING_SALE_EDIT) ? "1" : "0");
      $this->assign(
        "pDelete",
        $us->hasPermission(FIdConst::WAREHOUSING_SALE_DELETE) ? "1" : "0"
      );
      $this->assign(
        "pCommit",
        $us->hasPermission(FIdConst::WAREHOUSING_SALE_COMMIT) ? "1" : "0"
      );
      $this->assign("pGenPDF", $us->hasPermission(FIdConst::WAREHOUSING_SALE_PDF) ? "1" : "0");
      $this->assign(
        "pPrint",
        $us->hasPermission(FIdConst::WAREHOUSING_SALE_PRINT) ? "1" : "0"
      );

      $this->display();
    } else {
      $this->gotoLoginPage("/Home/Sale/wsIndex");
    }
  }

  /**
   * 获得销售出库单的信息
   */
  public function wsBillInfo()
  {
    if (IS_POST) {
      $params = array(
        "id" => I("post.id"),
        "sobillRef" => I("post.sobillRef")
      );

      $ws = new WSBillService();
      $this->ajaxReturn($ws->wsBillInfo($params));
    }
  }

  /**
   * 新建或编辑销售出库单
   */
  public function editWSBill()
  {
    if (IS_POST) {
      $params = array(
        "jsonStr" => I("post.jsonStr"),
        "checkInv" => I("post.checkInv")
      );

      $ws = new WSBillService();
      $this->ajaxReturn($ws->editWSBill($params));
    }
  }

  /**
   * 销售出库单主表信息列表
   */
  public function wsbillList()
  {
    if (IS_POST) {
      $params = array(
        "billStatus" => I("post.billStatus"),
        "ref" => I("post.ref"),
        "fromDT" => I("post.fromDT"),
        "toDT" => I("post.toDT"),
        "warehouseId" => I("post.warehouseId"),
        "customerId" => I("post.customerId"),
        "receivingType" => I("post.receivingType"),
        "sn" => I("post.sn"),
        "goodsId" => I("post.goodsId"),
        "page" => I("post.page"),
        "start" => I("post.start"),
        "limit" => I("post.limit")
      );

      $ws = new WSBillService();
      $this->ajaxReturn($ws->wsbillList($params));
    }
  }

  /**
   * 销售出库单明细信息列表
   */
  public function wsBillDetailList()
  {
    if (IS_POST) {
      $params = array(
        "billId" => I("post.billId")
      );

      $ws = new WSBillService();
      $this->ajaxReturn($ws->wsBillDetailList($params));
    }
  }

  /**
   * 删除销售出库单
   */
  public function deleteWSBill()
  {
    if (IS_POST) {
      $params = array(
        "id" => I("post.id")
      );

      $ws = new WSBillService();
      $this->ajaxReturn($ws->deleteWSBill($params));
    }
  }

  /**
   * 提交销售出库单
   */
  public function commitWSBill()
  {
    if (IS_POST) {
      $params = array(
        "id" => I("post.id")
      );

      $ws = new WSBillService();
      $this->ajaxReturn($ws->commitWSBill($params));
    }
  }

  /**
   * 销售退货入库 - 主界面
   */
  public function srIndex()
  {
    $us = new UserService();

    if ($us->hasPermission(FIdConst::SALE_REJECTION)) {
      $this->initVar();

      $this->assign("title", "销售退货入库");

      $this->assign("pAdd", $us->hasPermission(FIdConst::SALE_REJECTION_ADD) ? "1" : "0");
      $this->assign("pEdit", $us->hasPermission(FIdConst::SALE_REJECTION_EDIT) ? "1" : "0");
      $this->assign(
        "pDelete",
        $us->hasPermission(FIdConst::SALE_REJECTION_DELETE) ? "1" : "0"
      );
      $this->assign(
        "pCommit",
        $us->hasPermission(FIdConst::SALE_REJECTION_COMMIT) ? "1" : "0"
      );
      $this->assign("pGenPDF", $us->hasPermission(FIdConst::SALE_REJECTION_PDF) ? "1" : "0");
      $this->assign("pPrint", $us->hasPermission(FIdConst::SALE_REJECTION_PRINT) ? "1" : "0");

      $this->display();
    } else {
      $this->gotoLoginPage("/Home/Sale/srIndex");
    }
  }

  /**
   * 销售退货入库单主表信息列表
   */
  public function srbillList()
  {
    if (IS_POST) {
      $params = array(
        "billStatus" => I("post.billStatus"),
        "ref" => I("post.ref"),
        "fromDT" => I("post.fromDT"),
        "toDT" => I("post.toDT"),
        "warehouseId" => I("post.warehouseId"),
        "customerId" => I("post.customerId"),
        "paymentType" => I("post.paymentType"),
        "sn" => I("post.sn"),
        "goodsId" => I("post.goodsId"),
        "page" => I("post.page"),
        "start" => I("post.start"),
        "limit" => I("post.limit")
      );

      $sr = new SRBillService();
      $this->ajaxReturn($sr->srbillList($params));
    }
  }

  /**
   * 销售退货入库单明细信息列表
   */
  public function srBillDetailList()
  {
    if (IS_POST) {
      $params = array(
        "id" => I("post.billId")
      );

      $sr = new SRBillService();
      $this->ajaxReturn($sr->srBillDetailList($params));
    }
  }

  /**
   * 获得销售退货入库单的信息
   */
  public function srBillInfo()
  {
    if (IS_POST) {
      $params = array(
        "id" => I("post.id")
      );

      $rs = new SRBillService();
      $this->ajaxReturn($rs->srBillInfo($params));
    }
  }

  /**
   * 选择销售出库单
   */
  public function selectWSBillList()
  {
    if (IS_POST) {
      $params = array(
        "ref" => I("post.ref"),
        "customerId" => I("post.customerId"),
        "warehouseId" => I("post.warehouseId"),
        "fromDT" => I("post.fromDT"),
        "toDT" => I("post.toDT"),
        "sn" => I("post.sn"),
        "page" => I("post.page"),
        "start" => I("post.start"),
        "limit" => I("post.limit")
      );

      $rs = new SRBillService();
      $this->ajaxReturn($rs->selectWSBillList($params));
    }
  }

  /**
   * 新增或者编辑销售退货入库单
   */
  public function editSRBill()
  {
    if (IS_POST) {
      $params = array(
        "jsonStr" => I("post.jsonStr")
      );

      $rs = new SRBillService();
      $this->ajaxReturn($rs->editSRBill($params));
    }
  }

  /**
   * 查询要退货的销售出库单信息
   */
  public function getWSBillInfoForSRBill()
  {
    if (IS_POST) {
      $params = array(
        "id" => I("post.id")
      );

      $rs = new SRBillService();
      $this->ajaxReturn($rs->getWSBillInfoForSRBill($params));
    }
  }

  /**
   * 删除销售退货入库单
   */
  public function deleteSRBill()
  {
    if (IS_POST) {
      $params = array(
        "id" => I("post.id")
      );

      $rs = new SRBillService();
      $this->ajaxReturn($rs->deleteSRBill($params));
    }
  }

  /**
   * 提交销售退货入库单
   */
  public function commitSRBill()
  {
    if (IS_POST) {
      $params = array(
        "id" => I("post.id")
      );

      $rs = new SRBillService();
      $this->ajaxReturn($rs->commitSRBill($params));
    }
  }

  /**
   * 销售出库单生成pdf文件
   */
  public function pdf()
  {
    $params = array(
      "ref" => I("get.ref")
    );

    $ws = new WSBillService();
    $ws->pdf($params);
  }

  /**
   * 获得销售订单主表信息列表
   */
  public function sobillList()
  {
    if (IS_POST) {
      $ps = new SOBillService();
      $params = array(
        "billStatus" => I("post.billStatus"),
        "ref" => I("post.ref"),
        "fromDT" => I("post.fromDT"),
        "toDT" => I("post.toDT"),
        "customerId" => I("post.customerId"),
        "receivingType" => I("post.receivingType"),
        "goodsId" => I("post.goodsId"),
        "userId" => I("post.userId"),
        "start" => I("post.start"),
        "limit" => I("post.limit")
      );
      $this->ajaxReturn($ps->sobillList($params));
    }
  }

  /**
   * 获得销售订单的信息
   */
  public function soBillInfo()
  {
    if (IS_POST) {
      $params = array(
        "id" => I("post.id"),
        "genBill" => I("post.genBill"),
        "scbillRef" => I("post.scbillRef")
      );

      $ps = new SOBillService();
      $this->ajaxReturn($ps->soBillInfo($params));
    }
  }

  /**
   * 新增或编辑销售订单
   */
  public function editSOBill()
  {
    if (IS_POST) {
      $json = I("post.jsonStr");
      $ps = new SOBillService();
      $this->ajaxReturn($ps->editSOBill($json));
    }
  }

  /**
   * 获得销售订单的明细信息
   */
  public function soBillDetailList()
  {
    if (IS_POST) {
      $params = array(
        "id" => I("post.id")
      );

      $ps = new SOBillService();
      $this->ajaxReturn($ps->soBillDetailList($params));
    }
  }

  /**
   * 删除销售订单
   */
  public function deleteSOBill()
  {
    if (IS_POST) {
      $params = array(
        "id" => I("post.id")
      );

      $ps = new SOBillService();
      $this->ajaxReturn($ps->deleteSOBill($params));
    }
  }

  /**
   * 审核销售订单
   */
  public function commitSOBill()
  {
    if (IS_POST) {
      $params = array(
        "id" => I("post.id")
      );

      $ps = new SOBillService();
      $this->ajaxReturn($ps->commitSOBill($params));
    }
  }

  /**
   * 取消销售订单审核
   */
  public function cancelConfirmSOBill()
  {
    if (IS_POST) {
      $params = array(
        "id" => I("post.id")
      );

      $ps = new SOBillService();
      $this->ajaxReturn($ps->cancelConfirmSOBill($params));
    }
  }

  /**
   * 销售订单 - 订单变更
   */
  public function changeSaleOrder()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id"),
        "goodsCount" => I("post.goodsCount"),
        "goodsPrice" => I("post.goodsPrice")
      ];

      $ps = new SOBillService();
      $this->ajaxReturn($ps->changeSaleOrder($params));
    }
  }

  /**
   * 查询主表金额相关数据 - 订单变更后刷新界面用
   */
  public function getSOBillDataAterChangeOrder()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id")
      ];

      $ps = new SOBillService();
      $this->ajaxReturn($ps->getSOBillDataAterChangeOrder($params));
    }
  }

  /**
   * 销售订单生成pdf文件
   */
  public function soBillPdf()
  {
    $params = array(
      "ref" => I("get.ref")
    );

    $ws = new SOBillService();
    $ws->pdf($params);
  }

  /**
   * 销售退货入库单生成pdf文件
   */
  public function srBillPdf()
  {
    $params = array(
      "ref" => I("get.ref")
    );

    $ws = new SRBillService();
    $ws->pdf($params);
  }

  /**
   * 查询销售订单出库情况
   */
  public function soBillWSBillList()
  {
    if (IS_POST) {
      $soBillId = I("post.id");

      $ws = new WSBillService();
      $this->ajaxReturn($ws->soBillWSBillList($soBillId));
    }
  }

  /**
   * 关闭销售订单
   */
  public function closeSOBill()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id")
      ];

      $service = new SOBillService();
      $this->ajaxReturn($service->closeSOBill($params));
    }
  }

  /**
   * 取消订单关闭状态
   */
  public function cancelClosedSOBill()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id")
      ];

      $service = new SOBillService();
      $this->ajaxReturn($service->cancelClosedSOBill($params));
    }
  }

  /**
   * 根据销售订单单号查询其生成的采购订单
   * 用于：在销售订单生成采购订单之前，提醒用户
   */
  public function getPOBillRefListBySOBillRef()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::SALE_ORDER_GEN_POBILL)) {
        die("没有权限");
      }

      $params = [
        "soRef" => I("post.soRef")
      ];

      $service = new SOBillService();
      $this->ajaxReturn($service->getPOBillRefListBySOBillRef($params));
    }
  }

  /**
   * 根据销售订单单号查询其生成的销售出库单
   * 用于：在销售订单生成销售出库单之前，提醒用户
   */
  public function getWSBillRefListBySOBillRef()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::SALE_ORDER_GEN_WSBILL)) {
        die("没有权限");
      }

      $params = [
        "soRef" => I("post.soRef")
      ];

      $service = new SOBillService();
      $this->ajaxReturn($service->getWSBillRefListBySOBillRef($params));
    }
  }

  /**
   * 销售出库单明细信息列表
   * 销售退货入库 - 选择销售出库单
   */
  public function wsBillDetailListForSRBill()
  {
    if (IS_POST) {
      $params = array(
        "billId" => I("post.billId")
      );

      $ws = new WSBillService();
      $this->ajaxReturn($ws->wsBillDetailListForSRBill($params));
    }
  }

  /**
   * 生成打印销售订单的页面
   */
  public function genSOBillPrintPage()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id")
      ];

      $ss = new SOBillService();
      $data = $ss->getSOBillDataForLodopPrint($params);
      $this->assign("data", $data);
      $this->display();
    }
  }

  /**
   * 生成打印销售出库单的页面
   */
  public function genWSBillPrintPage()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id")
      ];

      $ss = new WSBillService();
      $data = $ss->getWSBillDataForLodopPrint($params);
      $this->assign("data", $data);
      $this->display();
    }
  }

  /**
   * 生成打印销售退货入库单的页面
   */
  public function genSRBillPrintPage()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id")
      ];

      $ss = new SRBillService();
      $data = $ss->getSRBillDataForLodopPrint($params);
      $this->assign("data", $data);
      $this->display();
    }
  }
}
