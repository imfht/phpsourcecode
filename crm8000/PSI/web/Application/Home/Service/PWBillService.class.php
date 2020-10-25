<?php

namespace Home\Service;

use Home\DAO\PWBillDAO;
use Home\DAO\POBillDAO;
use Home\Common\FIdConst;

/**
 * 采购入库Service
 *
 * @author 李静波
 */
class PWBillService extends PSIBaseExService
{
  private $LOG_CATEGORY = "采购入库";

  /**
   * 获得采购入库单主表列表
   */
  public function pwbillList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $us = new UserService();
    // 字段权限：金额和单价是否可见
    $canViewPrice = $us->hasPermission(FIdConst::PURCHASE_WAREHOUSE_CAN_VIEW_PRICE);

    $params["loginUserId"] = $this->getLoginUserId();
    $params["canViewPrice"] = $canViewPrice;

    $dao = new PWBillDAO($this->db());
    return $dao->pwbillList($params);
  }

  /**
   * 获得采购入库单商品明细记录列表
   */
  public function pwBillDetailList($pwbillId)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $us = new UserService();
    // 字段权限：金额和单价是否可见
    $canViewPrice = $us->hasPermission(FIdConst::PURCHASE_WAREHOUSE_CAN_VIEW_PRICE);

    $params = [
      "id" => $pwbillId,
      "companyId" => $this->getCompanyId(),
      "canViewPrice" => $canViewPrice
    ];

    $dao = new PWBillDAO($this->db());
    return $dao->pwBillDetailList($params);
  }

  /**
   * 新建或编辑采购入库单
   */
  public function editPWBill($json)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $bill = json_decode(html_entity_decode($json), true);
    if ($bill == null) {
      return $this->bad("传入的参数错误，不是正确的JSON格式");
    }

    $id = $bill["id"];

    $db = $this->db();

    $db->startTrans();

    $dao = new PWBillDAO($db);

    $log = null;

    $bill["companyId"] = $this->getCompanyId();

    if ($id) {
      // 编辑采购入库单

      $rc = $dao->updatePWBill($bill);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $ref = $bill["ref"];

      $log = "编辑采购入库单: 单号 = {$ref}";
    } else {
      // 新建采购入库单

      $bill["loginUserId"] = $this->getLoginUserId();
      $bill["dataOrg"] = $this->getLoginUserDataOrg();

      $rc = $dao->addPWBill($bill);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $id = $bill["id"];
      $ref = $bill["ref"];

      $pobillRef = $bill["pobillRef"];
      if ($pobillRef) {
        // 从采购订单生成采购入库单
        $log = "从采购订单(单号：{$pobillRef})生成采购入库单: 单号 = {$ref}";
      } else {
        // 手工新建采购入库单
        $log = "新建采购入库单: 单号 = {$ref}";
      }
    }

    // 同步库存账中的在途库存
    $rc = $dao->updateAfloatInventoryByPWBill($bill);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 获得某个采购入库单的信息
   */
  public function pwBillInfo($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $us = new UserService();
    // 字段权限：金额和单价是否可见
    $canViewPrice = $us->hasPermission(FIdConst::PURCHASE_WAREHOUSE_CAN_VIEW_PRICE);

    $params["canViewPrice"] = $canViewPrice;

    $params["loginUserId"] = $this->getLoginUserId();
    $params["loginUserName"] = $this->getLoginUserName();
    $params["companyId"] = $this->getCompanyId();

    $dao = new PWBillDAO($this->db());
    return $dao->pwBillInfo($params);
  }

  /**
   * 删除采购入库单
   */
  public function deletePWBill($id)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new PWBillDAO($db);
    $params = array(
      "id" => $id,
      "companyId" => $this->getCompanyId()
    );
    $rc = $dao->deletePWBill($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    // 记录业务日志
    $ref = $params["ref"];
    $log = "删除采购入库单: 单号 = {$ref}";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 提交采购入库单
   */
  public function commitPWBill($id)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $params = [
      "id" => $id,
      "loginUserId" => $this->getLoginUserId(),
      "companyId" => $this->getCompanyId()
    ];

    $dao = new PWBillDAO($db);

    $rc = $dao->commitPWBill($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $ref = $params["ref"];

    // 业务日志
    $log = "提交采购入库单: 单号 = {$ref}";
    $wspBillRef = $params["wspBillRef"];
    if ($wspBillRef) {
      $log .= ", 并自动执行拆分业务(拆分单单号：{$wspBillRef})";
    }
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 采购入库单生成pdf文件
   */
  public function pdf($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $us = new UserService();
    // 字段权限：金额和单价是否可见
    $canViewPrice = $us->hasPermission(FIdConst::PURCHASE_WAREHOUSE_CAN_VIEW_PRICE);

    $bs = new BizConfigService();
    $productionName = $bs->getProductionName();

    $ref = $params["ref"];

    $params["canViewPrice"] = $canViewPrice;
    $dao = new PWBillDAO($this->db());

    $bill = $dao->getDataForPDF($params);
    if (!$bill) {
      return;
    }

    // 记录业务日志
    $log = "采购入库单(单号：$ref)生成PDF文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    ob_start();

    $utilService = new UtilService();

    $ps = new PDFService();
    $pdf = $ps->getInstance();
    $pdf->SetTitle("采购入库单，单号：{$ref}");

    $pdf->setHeaderFont(array(
      "stsongstdlight",
      "",
      16
    ));

    $pdf->setFooterFont(array(
      "stsongstdlight",
      "",
      14
    ));

    $pdf->SetHeaderData("", 0, $productionName, "采购入库单");

    $pdf->SetFont("stsongstdlight", "", 10);
    $pdf->AddPage();

    /**
     * 注意：
     * TCPDF中，用来拼接HTML的字符串需要用单引号，否则HTML中元素的属性就不会被解析
     */
    $html = '
				<table>
					<tr><td colspan="2">单号：' . $ref . '</td></tr>
					<tr><td colspan="2">供应商：' . $bill["supplierName"] . '</td></tr>
					<tr><td>业务日期：' . $bill["bizDT"] . '</td><td>入库仓库:' . $bill["warehouseName"] . '</td></tr>
					<tr><td>业务员：' . $bill["bizUserName"] . '</td><td></td></tr>';
    if ($canViewPrice) {
      $html .= '<tr><td>采购货款:' . $bill["goodsMoney"] . ' (' . $utilService->moneyToCap($bill["goodsMoney"]) . ')</td>' .
        '<td>价税合计：' . $bill["moneyWithTax"] . ' (' . $utilService->moneyToCap($bill["moneyWithTax"]) . ')</td></tr>';
    }
    $html .= '</table>';

    $pdf->writeHTML($html);

    $html = '<table border="1" cellpadding="1">
					<tr><td>物料编码</td><td>品名</td><td>规格型号</td><td>数量</td><td>单位</td>';
    if ($canViewPrice) {
      $html .= '<td>采购单价</td><td>采购金额</td><td>税率</td><td>价税合计</td>';
    }
    $html .= '</tr>';
    foreach ($bill["items"] as $v) {
      $html .= '<tr>';
      $html .= '<td>' . $v["goodsCode"] . '</td>';
      $html .= '<td>' . $v["goodsName"] . '</td>';
      $html .= '<td>' . $v["goodsSpec"] . '</td>';
      $html .= '<td align="right">' . $v["goodsCount"] . '</td>';
      $html .= '<td>' . $v["unitName"] . '</td>';
      if ($canViewPrice) {
        $html .= '<td align="right">' . $v["goodsPrice"] . '</td>';
        $html .= '<td align="right">' . $v["goodsMoney"] . '</td>';
        $html .= '<td align="right">' . $v["taxRate"] . '%</td>';
        $html .= '<td align="right">' . $v["moneyWithTax"] . '</td>';
      }
      $html .= '</tr>';
    }

    $html .= '</table>';
    $pdf->writeHTML($html, true, false, true, false, '');

    ob_end_clean();

    $pdf->Output("$ref.pdf", "I");
  }

  /**
   * 采购订单执行的采购入库单信息
   *
   * @param array $params
   */
  public function poBillPWBillList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new POBillDAO($this->db());
    return $dao->poBillPWBillList($params);
  }

  /**
   * 获得采购入库单商品明细记录列表
   * 采购退货模块中 - 选择采购入库单
   */
  public function pwBillDetailListForPRBill($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $us = new UserService();
    // 字段权限：金额和单价是否可见
    $canViewPrice = $us->hasPermission(FIdConst::PURCHASE_WAREHOUSE_CAN_VIEW_PRICE);

    $params["canViewPrice"] = $canViewPrice;
    $params["companyId"] = $this->getCompanyId();

    $dao = new PWBillDAO($this->db());
    return $dao->pwBillDetailListForPRBill($params);
  }

  /**
   * 生成打印采购入库单的页面
   *
   * @param array $params
   */
  public function getPWBillDataForLodopPrint($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $us = new UserService();
    // 字段权限：金额和单价是否可见
    $canViewPrice = $us->hasPermission(FIdConst::PURCHASE_WAREHOUSE_CAN_VIEW_PRICE);

    $params["canViewPrice"] = $canViewPrice;
    $params["companyId"] = $this->getCompanyId();

    $dao = new PWBillDAO($this->db());
    return $dao->getPWBillDataForLodopPrint($params);
  }
}
