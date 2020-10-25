<?php

namespace Home\Controller;

use Home\Common\FIdConst;
use Home\Service\CashService;
use Home\Service\PayablesService;
use Home\Service\PrePaymentService;
use Home\Service\PreReceivingService;
use Home\Service\ReceivablesService;
use Home\Service\UserService;

/**
 * 资金Controller
 *
 * @author 李静波
 *        
 */
class FundsController extends PSIBaseController
{

  /**
   * 应付账款管理 - 主页面
   */
  public function payIndex()
  {
    $us = new UserService();

    if ($us->hasPermission(FIdConst::PAYABLES)) {
      $this->initVar();

      $this->assign("title", "应付账款管理");

      $this->display();
    } else {
      $this->gotoLoginPage("/Home/Funds/payIndex");
    }
  }

  /**
   * 应付账款，查询往来单位分类
   */
  public function payCategoryList()
  {
    if (IS_POST) {
      $params = array(
        "id" => I("post.id")
      );
      $ps = new PayablesService();
      $this->ajaxReturn($ps->payCategoryList($params));
    }
  }

  /**
   * 应付账款，总账
   */
  public function payList()
  {
    if (IS_POST) {
      $params = array(
        "caType" => I("post.caType"),
        "categoryId" => I("post.categoryId"),
        "supplierId" => I("post.supplierId"),
        "customerId" => I("post.customerId"),
        "factoryId" => I("post.factoryId"),
        "page" => I("post.page"),
        "start" => I("post.start"),
        "limit" => I("post.limit")
      );
      $ps = new PayablesService();
      $this->ajaxReturn($ps->payList($params));
    }
  }

  /**
   * 应付账款，明细账
   */
  public function payDetailList()
  {
    if (IS_POST) {
      $params = array(
        "caType" => I("post.caType"),
        "caId" => I("post.caId"),
        "page" => I("post.page"),
        "start" => I("post.start"),
        "limit" => I("post.limit")
      );
      $ps = new PayablesService();
      $this->ajaxReturn($ps->payDetailList($params));
    }
  }

  /**
   * 应付账款，付款记录
   */
  public function payRecordList()
  {
    if (IS_POST) {
      $params = array(
        "refType" => I("post.refType"),
        "refNumber" => I("post.refNumber"),
        "page" => I("post.page"),
        "start" => I("post.start"),
        "limit" => I("post.limit")
      );
      $ps = new PayablesService();
      $this->ajaxReturn($ps->payRecordList($params));
    }
  }

  /**
   * 应付账款，付款时候查询信息
   */
  public function payRecInfo()
  {
    if (IS_POST) {
      $us = new UserService();

      $this->ajaxReturn(
        array(
          "bizUserId" => $us->getLoginUserId(),
          "bizUserName" => $us->getLoginUserName()
        )
      );
    }
  }

  /**
   * 应付账款，新增付款记录
   */
  public function addPayment()
  {
    if (IS_POST) {
      $params = array(
        "refType" => I("post.refType"),
        "refNumber" => I("post.refNumber"),
        "bizDT" => I("post.bizDT"),
        "actMoney" => I("post.actMoney"),
        "bizUserId" => I("post.bizUserId"),
        "remark" => I("post.remark")
      );
      $ps = new PayablesService();
      $this->ajaxReturn($ps->addPayment($params));
    }
  }

  /**
   * 刷新应付账款总账信息
   */
  public function refreshPayInfo()
  {
    if (IS_POST) {
      $params = array(
        "id" => I("post.id")
      );
      $ps = new PayablesService();
      $this->ajaxReturn($ps->refreshPayInfo($params));
    }
  }

  /**
   * 刷新应付账款明细账信息
   */
  public function refreshPayDetailInfo()
  {
    if (IS_POST) {
      $params = array(
        "id" => I("post.id")
      );
      $ps = new PayablesService();
      $this->ajaxReturn($ps->refreshPayDetailInfo($params));
    }
  }

  /**
   * 应收账款管理 - 主页面
   */
  public function rvIndex()
  {
    $us = new UserService();

    if ($us->hasPermission(FIdConst::RECEIVING)) {
      $this->initVar();

      $this->assign("title", "应收账款管理");

      $this->display();
    } else {
      $this->gotoLoginPage("/Home/Funds/rvIndex");
    }
  }

  /**
   * 获得应收账款往来单位的分类
   */
  public function rvCategoryList()
  {
    if (IS_POST) {
      $params = array(
        "id" => I("post.id")
      );
      $rs = new ReceivablesService();
      $this->ajaxReturn($rs->rvCategoryList($params));
    }
  }

  /**
   * 应收账款，总账
   */
  public function rvList()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::RECEIVING)) {
        die("没有权限");
      }

      $params = [
        "caType" => I("post.caType"),
        "categoryId" => I("post.categoryId"),
        "customerId" => I("post.customerId"),
        "supplierId" => I("post.supplierId"),
        "hasBalance" => I("post.hasBalance"),
        "page" => I("post.page"),
        "start" => I("post.start"),
        "limit" => I("post.limit")
      ];
      $rs = new ReceivablesService();
      $this->ajaxReturn($rs->rvList($params));
    }
  }

  /**
   * 应收账款，明细账
   */
  public function rvDetailList()
  {
    if (IS_POST) {
      $params = array(
        "caType" => I("post.caType"),
        "caId" => I("post.caId"),
        "page" => I("post.page"),
        "start" => I("post.start"),
        "limit" => I("post.limit")
      );
      $rs = new ReceivablesService();
      $this->ajaxReturn($rs->rvDetailList($params));
    }
  }

  /**
   * 应收账款，收款记录
   */
  public function rvRecordList()
  {
    if (IS_POST) {
      $params = array(
        "refType" => I("post.refType"),
        "refNumber" => I("post.refNumber"),
        "page" => I("post.page"),
        "start" => I("post.start"),
        "limit" => I("post.limit")
      );
      $rs = new ReceivablesService();
      $this->ajaxReturn($rs->rvRecordList($params));
    }
  }

  /**
   * 应收账款收款时候，查询信息
   */
  public function rvRecInfo()
  {
    if (IS_POST) {
      $us = new UserService();

      $this->ajaxReturn(
        array(
          "bizUserId" => $us->getLoginUserId(),
          "bizUserName" => $us->getLoginUserName()
        )
      );
    }
  }

  /**
   * 记录收款记录
   */
  public function addRvRecord()
  {
    if (IS_POST) {
      $params = array(
        "refType" => I("post.refType"),
        "refNumber" => I("post.refNumber"),
        "bizDT" => I("post.bizDT"),
        "actMoney" => I("post.actMoney"),
        "bizUserId" => I("post.bizUserId"),
        "remark" => I("post.remark")
      );
      $rs = new ReceivablesService();
      $this->ajaxReturn($rs->addRvRecord($params));
    }
  }

  /**
   * 刷新应收账款总账信息
   */
  public function refreshRvInfo()
  {
    if (IS_POST) {
      $params = array(
        "id" => I("post.id")
      );
      $rs = new ReceivablesService();
      $this->ajaxReturn($rs->refreshRvInfo($params));
    }
  }

  /**
   * 刷新应收账款明细账信息
   */
  public function refreshRvDetailInfo()
  {
    if (IS_POST) {
      $params = array(
        "id" => I("post.id")
      );
      $rs = new ReceivablesService();
      $this->ajaxReturn($rs->refreshRvDetailInfo($params));
    }
  }

  /**
   * 现金收支查询 - 主页面
   */
  public function cashIndex()
  {
    $us = new UserService();

    if ($us->hasPermission(FIdConst::CASH_INDEX)) {
      $this->initVar();

      $this->assign("title", "现金收支查询");

      $this->display();
    } else {
      $this->gotoLoginPage("/Home/Funds/cashIndex");
    }
  }

  /**
   * 现金收支，总账
   */
  public function cashList()
  {
    if (IS_POST) {
      $params = array(
        "dtFrom" => I("post.dtFrom"),
        "dtTo" => I("post.dtTo"),
        "page" => I("post.page"),
        "start" => I("post.start"),
        "limit" => I("post.limit")
      );
      $cs = new CashService();
      $this->ajaxReturn($cs->cashList($params));
    }
  }

  /**
   * 现金收支，明细账
   */
  public function cashDetailList()
  {
    if (IS_POST) {
      $params = array(
        "bizDT" => I("post.bizDT"),
        "page" => I("post.page"),
        "start" => I("post.start"),
        "limit" => I("post.limit")
      );
      $cs = new CashService();
      $this->ajaxReturn($cs->cashDetailList($params));
    }
  }

  /**
   * 预收款管理
   */
  public function prereceivingIndex()
  {
    $us = new UserService();

    if ($us->hasPermission(FIdConst::PRE_RECEIVING)) {
      $this->initVar();

      $this->assign("title", "预收款管理");

      $this->display();
    } else {
      $this->gotoLoginPage("/Home/Funds/prereceivingIndex");
    }
  }

  /**
   * 收取预收款时候，查询信息
   */
  public function addPreReceivingInfo()
  {
    if (IS_POST) {
      $ps = new PreReceivingService();
      $this->ajaxReturn($ps->addPreReceivingInfo());
    }
  }

  /**
   * 退回预收款时候，查询信息
   */
  public function returnPreReceivingInfo()
  {
    if (IS_POST) {
      $ps = new PreReceivingService();
      $this->ajaxReturn($ps->returnPreReceivingInfo());
    }
  }

  /**
   * 收取预收款
   */
  public function addPreReceiving()
  {
    if (IS_POST) {
      $params = array(
        "customerId" => I("post.customerId"),
        "bizUserId" => I("post.bizUserId"),
        "bizDT" => I("post.bizDT"),
        "inMoney" => I("post.inMoney"),
        "memo" => I("post.memo"),
      );

      $ps = new PreReceivingService();
      $this->ajaxReturn($ps->addPreReceiving($params));
    }
  }

  /**
   * 退回预收款
   */
  public function returnPreReceiving()
  {
    if (IS_POST) {
      $params = array(
        "customerId" => I("post.customerId"),
        "bizUserId" => I("post.bizUserId"),
        "bizDT" => I("post.bizDT"),
        "outMoney" => I("post.outMoney"),
        "memo" => I("post.memo"),
      );

      $ps = new PreReceivingService();
      $this->ajaxReturn($ps->returnPreReceiving($params));
    }
  }

  /**
   * 预收款，总账
   */
  public function prereceivingList()
  {
    if (IS_POST) {
      $params = array(
        "categoryId" => I("post.categoryId"),
        "customerId" => I("post.customerId"),
        "page" => I("post.page"),
        "start" => I("post.start"),
        "limit" => I("post.limit")
      );

      $ps = new PreReceivingService();
      $this->ajaxReturn($ps->prereceivingList($params));
    }
  }

  /**
   * 预收款，明细账
   */
  public function prereceivingDetailList()
  {
    if (IS_POST) {
      $params = array(
        "customerId" => I("post.customerId"),
        "dtFrom" => I("post.dtFrom"),
        "dtTo" => I("post.dtTo"),
        "page" => I("post.page"),
        "start" => I("post.start"),
        "limit" => I("post.limit")
      );

      $ps = new PreReceivingService();
      $this->ajaxReturn($ps->prereceivingDetailList($params));
    }
  }

  /**
   * 预付款管理
   */
  public function prepaymentIndex()
  {
    $us = new UserService();

    if ($us->hasPermission(FIdConst::PRE_PAYMENT)) {
      $this->initVar();

      $this->assign("title", "预付款管理");

      $this->display();
    } else {
      $this->gotoLoginPage("/Home/Funds/prepaymentIndex");
    }
  }

  /**
   * 付预付款时候，查询信息
   */
  public function addPrePaymentInfo()
  {
    if (IS_POST) {
      $ps = new PrePaymentService();
      $this->ajaxReturn($ps->addPrePaymentInfo());
    }
  }

  /**
   * 付预付款
   */
  public function addPrePayment()
  {
    if (IS_POST) {
      $params = array(
        "supplierId" => I("post.supplierId"),
        "bizUserId" => I("post.bizUserId"),
        "bizDT" => I("post.bizDT"),
        "inMoney" => I("post.inMoney"),
        "memo" => I("post.memo"),
      );

      $ps = new PrePaymentService();
      $this->ajaxReturn($ps->addPrePayment($params));
    }
  }

  /**
   * 预付款，总账
   */
  public function prepaymentList()
  {
    if (IS_POST) {
      $params = array(
        "categoryId" => I("post.categoryId"),
        "supplierId" => I("post.supplierId"),
        "page" => I("post.page"),
        "start" => I("post.start"),
        "limit" => I("post.limit")
      );

      $ps = new PrePaymentService();
      $this->ajaxReturn($ps->prepaymentList($params));
    }
  }

  /**
   * 预付款，明细账
   */
  public function prepaymentDetailList()
  {
    if (IS_POST) {
      $params = array(
        "supplierId" => I("post.supplierId"),
        "dtFrom" => I("post.dtFrom"),
        "dtTo" => I("post.dtTo"),
        "start" => I("post.start"),
        "limit" => I("post.limit")
      );

      $ps = new PrePaymentService();
      $this->ajaxReturn($ps->prepaymentDetailList($params));
    }
  }

  /**
   * 返回预付款时候，查询信息
   */
  public function returnPrePaymentInfo()
  {
    if (IS_POST) {
      $ps = new PrePaymentService();
      $this->ajaxReturn($ps->returnPrePaymentInfo());
    }
  }

  /**
   * 供应商返回预付款
   */
  public function returnPrePayment()
  {
    if (IS_POST) {
      $params = array(
        "supplierId" => I("post.supplierId"),
        "bizUserId" => I("post.bizUserId"),
        "bizDT" => I("post.bizDT"),
        "inMoney" => I("post.inMoney"),
        "memo" => I("post.memo")
      );

      $ps = new PrePaymentService();
      $this->ajaxReturn($ps->returnPrePayment($params));
    }
  }
}
