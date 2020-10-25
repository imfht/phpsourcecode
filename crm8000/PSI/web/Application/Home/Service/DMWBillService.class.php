<?php

namespace Home\Service;

use Home\DAO\DMWBillDAO;
use Home\Common\FIdConst;

/**
 * 成品委托生产入库单Service
 *
 * @author 李静波
 */
class DMWBillService extends PSIBaseExService
{
  private $LOG_CATEGORY = "成品委托生产入库";

  /**
   * 成品委托生产入库单 - 单据详情
   */
  public function dmwBillInfo($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();
    $params["loginUserId"] = $this->getLoginUserId();
    $params["loginUserName"] = $this->getLoginUserName();

    $dao = new DMWBillDAO($this->db());
    return $dao->dmwBillInfo($params);
  }

  /**
   * 新建或编辑成品委托生产入库单
   */
  public function editDMWBill($json)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $bill = json_decode(html_entity_decode($json), true);
    if ($bill == null) {
      return $this->bad("传入的参数错误，不是正确的JSON格式");
    }

    $id = $bill["id"];

    // 判断权限
    $us = new UserService();
    if ($id) {
      if (!$us->hasPermission(FIdConst::DMW_EDIT)) {
        return $this->bad("您没有编辑成品委托生产入库单的权限");
      }
    } else {
      if (!$us->hasPermission(FIdConst::DMW_ADD)) {
        return $this->bad("您没有新建成品委托生产入库单的权限");
      }
    }

    $db = $this->db();

    $db->startTrans();

    $dao = new DMWBillDAO($db);

    $us = new UserService();
    $bill["companyId"] = $us->getCompanyId();
    $bill["loginUserId"] = $us->getLoginUserId();
    $bill["dataOrg"] = $us->getLoginUserDataOrg();

    $log = null;
    if ($id) {
      // 编辑

      $rc = $dao->updateDMWBill($bill);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $ref = $bill["ref"];

      $log = "编辑成品委托生产入库单，单号：{$ref}";
    } else {
      // 新建

      $rc = $dao->addDMWBill($bill);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $id = $bill["id"];
      $ref = $bill["ref"];

      $dmobillRef = $bill["dmobillRef"];
      if ($dmobillRef) {
        // 从成品委托生产订单生成入库单
        $log = "从成品委托生产订单(单号：{$dmobillRef})生成成品委托生产入库单: 单号 = {$ref}";
      } else {
        // 手工新建入库单
        $log = "新建成品委托生产入库单，单号：{$ref}";
      }
    }

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 获得成品委托生产入库单主表列表
   */
  public function dmwbillList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();

    $dao = new DMWBillDAO($this->db());
    return $dao->dmwbillList($params);
  }

  /**
   * 获得成品委托生产入库单的明细记录
   */
  public function dmwBillDetailList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new DMWBillDAO($this->db());
    return $dao->dmwBillDetailList($params);
  }

  /**
   * 删除成品委托生产入库单
   */
  public function deleteDMWBill($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new DMWBillDAO($db);
    $rc = $dao->deleteDMWBill($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    // 记录业务日志
    $ref = $params["ref"];
    $log = "删除成品委托生产入库单: 单号 = {$ref}";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 成品委托生产入库单生成pdf文件
   */
  public function pdf($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $bs = new BizConfigService();
    $productionName = $bs->getProductionName();

    $ref = $params["ref"];

    $dao = new DMWBillDAO($this->db());

    $bill = $dao->getDataForPDF($params);
    if (!$bill) {
      return;
    }

    // 记录业务日志
    $log = "成品委托生产入库单(单号：$ref)生成PDF文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    ob_start();

    $ps = new PDFService();
    $pdf = $ps->getInstance();
    $pdf->SetTitle("成品委托生产入库单，单号：{$ref}");

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

    $pdf->SetHeaderData("", 0, $productionName, "成品委托生产入库单");

    $pdf->SetFont("stsongstdlight", "", 10);
    $pdf->AddPage();

    $canViewPrice = true;

    /**
     * 注意：
     * TCPDF中，用来拼接HTML的字符串需要用单引号，否则HTML中元素的属性就不会被解析
     */
    $html = '
				<table>
					<tr><td colspan="2">单号：' . $ref . '</td></tr>
					<tr><td colspan="2">工厂：' . $bill["factoryName"] . '</td></tr>
					<tr><td>业务日期：' . $bill["bizDT"] . '</td><td>入库仓库:' . $bill["warehouseName"] . '</td></tr>
					<tr><td>业务员：' . $bill["bizUserName"] . '</td><td></td></tr>';
    if ($canViewPrice) {
      $html .= '<tr><td>货款:' . $bill["goodsMoney"] . '</td><td>价税合计：' . $bill["moneyWithTax"] . '</td></tr>';
    }
    $html .= '</table>';

    $pdf->writeHTML($html);

    $html = '<table border="1" cellpadding="1">
					<tr><td>物料编码</td><td>品名</td><td>规格型号</td><td>数量</td><td>单位</td>';
    if ($canViewPrice) {
      $html .= '<td>单价</td><td>金额</td><td>税率</td><td>价税合计</td>';
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
   * 提交成品委托生产入库单
   */
  public function commitDMWBill($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $params["loginUserId"] = $this->getLoginUserId();
    $params["companyId"] = $this->getCompanyId();

    $dao = new DMWBillDAO($db);

    $rc = $dao->commitDMWBill($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $ref = $params["ref"];

    // 业务日志
    $log = "提交成品委托入库单: 单号 = {$ref}";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    $id = $params["id"];
    return $this->ok($id);
  }

  /**
   * 生成打印成品委托生产入库单的页面
   *
   * @param array $params
   */
  public function getDMWBillDataForLodopPrint($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new DMWBillDAO($this->db());
    return $dao->getDMWBillDataForLodopPrint($params);
  }
}
