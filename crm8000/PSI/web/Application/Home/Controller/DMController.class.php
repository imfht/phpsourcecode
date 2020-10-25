<?php

namespace Home\Controller;

use Home\Common\FIdConst;
use Home\Service\UserService;
use Home\Service\DMOBillService;
use Home\Service\DMWBillService;

/**
 * 成品委托生产Controller
 *
 * @author 李静波
 *        
 */
class DMController extends PSIBaseController
{

  /**
   * 成品委托生产订单 - 主页面
   */
  public function dmobillIndex()
  {
    $us = new UserService();

    if ($us->hasPermission(FIdConst::DMO)) {
      $this->initVar();

      $this->assign("pAdd", $us->hasPermission(FIdConst::DMO_ADD) ? "1" : "0");
      $this->assign("pEdit", $us->hasPermission(FIdConst::DMO_EDIT) ? "1" : "0");
      $this->assign("pDelete", $us->hasPermission(FIdConst::DMO_DELETE) ? "1" : "0");
      $this->assign("pCommit", $us->hasPermission(FIdConst::DMO_COMMIT) ? "1" : "0");
      $this->assign("pGenDMWBill", $us->hasPermission(FIdConst::DMO_GEN_DMW_BILL) ? "1" : "0");
      $this->assign("showAddGoodsButton", $us->hasPermission(FIdConst::GOODS_ADD) ? "1" : "0");
      $this->assign(
        "showAddFactoryButton",
        $us->hasPermission(FIdConst::FACTORY_ADD) ? "1" : "0"
      );
      $this->assign("pCloseBill", $us->hasPermission(FIdConst::DMO_CLOSE_BILL) ? "1" : "0");
      $this->assign("pGenPDF", $us->hasPermission(FIdConst::DMO_PDF) ? "1" : "0");
      $this->assign("pPrint", $us->hasPermission(FIdConst::DMO_PRINT) ? "1" : "0");

      $this->assign("title", "成品委托生产订单");

      $this->display();
    } else {
      $this->gotoLoginPage("/Home/DM/dmobillIndex");
    }
  }

  /**
   * 获得成品委托生产订单的信息
   */
  public function dmoBillInfo()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id")
      ];

      $service = new DMOBillService();
      $this->ajaxReturn($service->dmoBillInfo($params));
    }
  }

  /**
   * 新增或编辑成品委托生产订单
   */
  public function editDMOBill()
  {
    if (IS_POST) {
      $json = I("post.jsonStr");
      $service = new DMOBillService();
      $this->ajaxReturn($service->editDMOBill($json));
    }
  }

  /**
   * 获得成品委托生产订单主表信息列表
   */
  public function dmobillList()
  {
    if (IS_POST) {
      $params = [
        "billStatus" => I("post.billStatus"),
        "ref" => I("post.ref"),
        "fromDT" => I("post.fromDT"),
        "toDT" => I("post.toDT"),
        "factoryId" => I("post.factoryId"),
        "goodsId" => I("post.goodsId"),
        "start" => I("post.start"),
        "limit" => I("post.limit")
      ];

      $service = new DMOBillService();
      $this->ajaxReturn($service->dmobillList($params));
    }
  }

  /**
   * 获得成品委托生产订单的明细信息
   */
  public function dmoBillDetailList()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id")
      ];

      $service = new DMOBillService();
      $this->ajaxReturn($service->dmoBillDetailList($params));
    }
  }

  /**
   * 成品委托生产订单的入库情况列表
   */
  public function dmoBillDMWBillList()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id")
      ];

      $service = new DMOBillService();
      $this->ajaxReturn($service->dmoBillDMWBillList($params));
    }
  }

  /**
   * 删除成品委托生产订单
   */
  public function deleteDMOBill()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::DMO_DELETE)) {
        $this->ajaxReturn($this->noPermission("删除成品委托生产订单"));
        return;
      }

      $params = [
        "id" => I("post.id")
      ];

      $service = new DMOBillService();
      $this->ajaxReturn($service->deleteDMOBill($params));
    }
  }

  /**
   * 审核成品委托生产订单
   */
  public function commitDMOBill()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::DMO_COMMIT)) {
        $this->ajaxReturn($this->noPermission("审核成品委托生产订单"));
        return;
      }

      $params = [
        "id" => I("post.id")
      ];

      $service = new DMOBillService();
      $this->ajaxReturn($service->commitDMOBill($params));
    }
  }

  /**
   * 取消审核成品委托生产订单
   */
  public function cancelConfirmDMOBill()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::DMO_COMMIT)) {
        $this->ajaxReturn($this->noPermission("取消审核成品委托生产订单"));
        return;
      }

      $params = [
        "id" => I("post.id")
      ];

      $service = new DMOBillService();
      $this->ajaxReturn($service->cancelConfirmDMOBill($params));
    }
  }

  /**
   * 关闭成品委托生产订单
   */
  public function closeDMOBill()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::DMO_CLOSE_BILL)) {
        $this->ajaxReturn($this->noPermission("关闭成品委托生产订单"));
        return;
      }

      $params = [
        "id" => I("post.id")
      ];

      $service = new DMOBillService();
      $this->ajaxReturn($service->closeDMOBill($params));
    }
  }

  /**
   * 取消关闭成品委托生产订单
   */
  public function cancelClosedDMOBill()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::DMO_CLOSE_BILL)) {
        $this->ajaxReturn($this->noPermission("取消关闭成品委托生产订单"));
        return;
      }

      $params = [
        "id" => I("post.id")
      ];

      $service = new DMOBillService();
      $this->ajaxReturn($service->cancelClosedDMOBill($params));
    }
  }

  /**
   * 成品委托生产订单生成PDF文件
   */
  public function dmoBillPdf()
  {
    $params = [
      "ref" => I("get.ref")
    ];

    $ps = new DMOBillService();
    $ps->pdf($params);
  }

  /**
   * 生成打印成品委托生产订单的页面
   */
  public function genDMOBillPrintPage()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id")
      ];

      $ss = new DMOBillService();
      $data = $ss->getDMOBillDataForLodopPrint($params);
      $this->assign("data", $data);
      $this->display();
    }
  }

  /**
   * 成品委托生产入库 - 主页面
   */
  public function dmwbillIndex()
  {
    $us = new UserService();

    if ($us->hasPermission(FIdConst::DMW)) {
      $this->initVar();

      $this->assign("pAdd", $us->hasPermission(FIdConst::DMW_ADD) ? "1" : "0");
      $this->assign("pEdit", $us->hasPermission(FIdConst::DMW_EDIT) ? "1" : "0");
      $this->assign("pDelete", $us->hasPermission(FIdConst::DMW_DELETE) ? "1" : "0");
      $this->assign("pCommit", $us->hasPermission(FIdConst::DMW_COMMIT) ? "1" : "0");
      $this->assign("pGenPDF", $us->hasPermission(FIdConst::DMW_PDF) ? "1" : "0");
      $this->assign("pPrint", $us->hasPermission(FIdConst::DMW_PRINT) ? "1" : "0");

      $this->assign("title", "成品委托生产入库");

      $this->display();
    } else {
      $this->gotoLoginPage("/Home/DM/dmwbillIndex");
    }
  }

  /**
   * 成品委托生产入库单 - 单据详情
   */
  public function dmwBillInfo()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id"),
        "dmobillRef" => I("post.dmobillRef")
      ];

      $ps = new DMWBillService();
      $this->ajaxReturn($ps->dmwBillInfo($params));
    }
  }

  /**
   * 新增或编辑成品委托生产入库单
   */
  public function editDMWBill()
  {
    if (IS_POST) {
      $json = I("post.jsonStr");
      $service = new DMWBillService();
      $this->ajaxReturn($service->editDMWBill($json));
    }
  }

  /**
   * 获得成品委托生产入库单主表列表
   */
  public function dmwbillList()
  {
    if (IS_POST) {
      $ps = new DMWBillService();
      $params = [
        "billStatus" => I("post.billStatus"),
        "ref" => I("post.ref"),
        "fromDT" => I("post.fromDT"),
        "toDT" => I("post.toDT"),
        "warehouseId" => I("post.warehouseId"),
        "factoryId" => I("post.factoryId"),
        "paymentType" => I("post.paymentType"),
        "goodsId" => I("post.goodsId"),
        "start" => I("post.start"),
        "limit" => I("post.limit")
      ];
      $this->ajaxReturn($ps->dmwbillList($params));
    }
  }

  /**
   * 获得成品委托生产入库单的明细记录
   */
  public function dmwBillDetailList()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id")
      ];
      $service = new DMWBillService();
      $this->ajaxReturn($service->dmwBillDetailList($params));
    }
  }

  /**
   * 删除成品委托生产入库单
   */
  public function deleteDMWBill()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id")
      ];
      $service = new DMWBillService();
      $this->ajaxReturn($service->deleteDMWBill($params));
    }
  }

  /**
   * 成品委托生产入库单生成PDF文件
   */
  public function dmwBillPdf()
  {
    $params = [
      "ref" => I("get.ref")
    ];

    $service = new DMWBillService();
    $service->pdf($params);
  }

  /**
   * 提交成品委托生产入库单
   */
  public function commitDMWBill()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id")
      ];
      $service = new DMWBillService();
      $this->ajaxReturn($service->commitDMWBill($params));
    }
  }

  /**
   * 生成打印成品委托生产入库单的页面
   */
  public function genDMWBillPrintPage()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id")
      ];

      $ss = new DMWBillService();
      $data = $ss->getDMWBillDataForLodopPrint($params);
      $this->assign("data", $data);
      $this->display();
    }
  }
}
