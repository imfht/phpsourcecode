<?php

namespace Home\Controller;

use Home\Service\UserService;
use Home\Common\FIdConst;
use Home\Service\SCBillService;

/**
 * 销售合同Controller
 *
 * @author 李静波
 *        
 */
class SaleContractController extends PSIBaseController
{

  /**
   * 销售合同 - 主页面
   */
  public function index()
  {
    $us = new UserService();

    if ($us->hasPermission(FIdConst::SALE_CONTRACT)) {
      $this->initVar();

      $this->assign("title", "销售合同");

      $this->assign("pCommit", $us->hasPermission(FIdConst::SALE_CONTRACT_COMMIT) ? "1" : "0");
      $this->assign(
        "pGenSOBill",
        $us->hasPermission(FIdConst::SALE_CONTRACT_GEN_SOBILL) ? "1" : "0"
      );

      $this->assign("pAdd", $us->hasPermission(FIdConst::SALE_CONTRACT_ADD) ? "1" : "0");
      $this->assign("pEdit", $us->hasPermission(FIdConst::SALE_CONTRACT_EDIT) ? "1" : "0");
      $this->assign("pDelete", $us->hasPermission(FIdConst::SALE_CONTRACT_DELETE) ? "1" : "0");
      $this->assign("pGenPDF", $us->hasPermission(FIdConst::SALE_CONTRACT_PDF) ? "1" : "0");
      $this->assign("pPrint", $us->hasPermission(FIdConst::SALE_CONTRACT_PRINT) ? "1" : "0");
      $this->assign("showAddGoodsButton", $us->hasPermission(FIdConst::GOODS_ADD) ? "1" : "0");

      $this->display();
    } else {
      $this->gotoLoginPage("/Home/SaleContract/index");
    }
  }

  /**
   * 销售合同主表列表
   */
  public function scbillList()
  {
    if (IS_POST) {
      $params = [
        "billStatus" => I("post.billStatus"),
        "ref" => I("post.ref"),
        "fromDT" => I("post.fromDT"),
        "toDT" => I("post.toDT"),
        "customerId" => I("post.customerId"),
        "goodsId" => I("post.goodsId"),
        "start" => I("post.start"),
        "limit" => I("post.limit")
      ];

      $service = new SCBillService();
      $this->ajaxReturn($service->scbillList($params));
    }
  }

  /**
   * 销售合同详情
   */
  public function scBillInfo()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id")
      ];

      $service = new SCBillService();
      $this->ajaxReturn($service->scBillInfo($params));
    }
  }

  /**
   * 新增或编辑销售合同
   */
  public function editSCBill()
  {
    if (IS_POST) {
      $json = I("post.jsonStr");
      $ps = new SCBillService();
      $this->ajaxReturn($ps->editSCBill($json));
    }
  }

  /**
   * 销售合同商品明细
   */
  public function scBillDetailList()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id")
      ];

      $service = new SCBillService();
      $this->ajaxReturn($service->scBillDetailList($params));
    }
  }

  /**
   * 删除销售合同
   */
  public function deleteSCBill()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id")
      ];

      $service = new SCBillService();
      $this->ajaxReturn($service->deleteSCBill($params));
    }
  }

  /**
   * 审核销售合同
   */
  public function commitSCBill()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id")
      ];

      $service = new SCBillService();
      $this->ajaxReturn($service->commitSCBill($params));
    }
  }

  /**
   * 取消审核销售合同
   */
  public function cancelConfirmSCBill()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id")
      ];

      $service = new SCBillService();
      $this->ajaxReturn($service->cancelConfirmSCBill($params));
    }
  }

  /**
   * 销售合同生成pdf文件
   */
  public function scBillPdf()
  {
    $params = [
      "ref" => I("get.ref")
    ];

    $ws = new SCBillService();
    $ws->pdf($params);
  }

  /**
   * 销售合同生成Word文件
   */
  public function scBillWord()
  {
    $params = [
      "ref" => I("get.ref")
    ];

    $ws = new SCBillService();
    $ws->word($params);
  }

  /**
   * 生成打印销售合同的页面
   */
  public function genSCBillPrintPage()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id")
      ];

      $service = new SCBillService();
      $data = $service->getSCBillDataForLodopPrint($params);
      $this->assign("data", $data);
      $this->display();
    }
  }
}
