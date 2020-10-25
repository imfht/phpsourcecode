<?php

namespace Home\Service;

use Home\DAO\PRBillDAO;

/**
 * 采购退货出库单Service
 *
 * @author 李静波
 */
class PRBillService extends PSIBaseExService
{
  private $LOG_CATEGORY = "采购退货出库";

  public function prBillInfo($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();
    $params["loginUserName"] = $this->getLoginUserName();
    $params["companyId"] = $this->getCompanyId();

    $dao = new PRBillDAO($this->db());
    return $dao->prBillInfo($params);
  }

  /**
   * 新建或编辑采购退货出库单
   */
  public function editPRBill($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $json = $params["jsonStr"];
    $bill = json_decode(html_entity_decode($json), true);
    if ($bill == null) {
      return $this->bad("传入的参数错误，不是正确的JSON格式");
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new PRBillDAO($db);

    $id = $bill["id"];

    $log = null;

    $bill["companyId"] = $this->getCompanyId();

    if ($id) {
      // 编辑采购退货出库单
      $bill["loginUserId"] = $this->getLoginUserId();

      $rc = $dao->updatePRBill($bill);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $ref = $bill["ref"];
      $log = "编辑采购退货出库单，单号：$ref";
    } else {
      $bill["dataOrg"] = $this->getLoginUserDataOrg();
      $bill["loginUserId"] = $this->getLoginUserId();

      $rc = $dao->addPRBill($bill);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $id = $bill["id"];
      $ref = $bill["ref"];

      $log = "新建采购退货出库单，单号：$ref";
    }

    // 记录业务日志
    $bs = new BizlogService();
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 选择可以退货的采购入库单
   */
  public function selectPWBillList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();

    $dao = new PRBillDAO($this->db());
    return $dao->selectPWBillList($params);
  }

  /**
   * 查询采购入库单的详细信息
   */
  public function getPWBillInfoForPRBill($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new PRBillDAO($this->db());
    return $dao->getPWBillInfoForPRBill($params);
  }

  /**
   * 采购退货出库单列表
   */
  public function prbillList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();

    $dao = new PRBillDAO($this->db());
    return $dao->prbillList($params);
  }

  /**
   * 采购退货出库单明细列表
   */
  public function prBillDetailList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new PRBillDAO($this->db());
    return $dao->prBillDetailList($params);
  }

  /**
   * 删除采购退货出库单
   */
  public function deletePRBill($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();

    $db->startTrans();

    $dao = new PRBillDAO($db);
    $rc = $dao->deletePRBill($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $ref = $params["ref"];
    $bs = new BizlogService();
    $log = "删除采购退货出库单，单号：$ref";
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 提交采购退货出库单
   */
  public function commitPRBill($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $id = $params["id"];

    $db = $this->db();

    $db->startTrans();

    $dao = new PRBillDAO($db);

    $rc = $dao->commitPRBill($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    // 记录业务日志
    $ref = $params["ref"];
    $bs = new BizlogService($db);
    $log = "提交采购退货出库单，单号：$ref";
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 采购退货出库单生成pdf文件
   */
  public function pdf($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $bs = new BizConfigService();
    $productionName = $bs->getProductionName();

    $ref = $params["ref"];

    $dao = new PRBillDAO($this->db());

    $bill = $dao->getDataForPDF($params);
    if (!$bill) {
      return;
    }

    // 记录业务日志
    $log = "采购退货出库单(单号：$ref)生成PDF文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    ob_start();

    $utilService = new UtilService();

    $ps = new PDFService();
    $pdf = $ps->getInstance();
    $pdf->SetTitle("采购退货出库单，单号：{$ref}");

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

    $pdf->SetHeaderData("", 0, $productionName, "采购退货出库单");

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
					<tr><td>业务日期：' . $bill["bizDT"] . '</td><td>出库仓库:' . $bill["warehouseName"] . '</td></tr>
					<tr><td>业务员：' . $bill["bizUserName"] . '</td><td></td></tr>
          <tr><td>退货金额：' . $bill["goodsMoney"] . ' (' . $utilService->moneyToCap($bill["goodsMoney"]) . ')</td>
              <td>价税合计：' . $bill["moneyWithTax"] . ' (' . $utilService->moneyToCap($bill["moneyWithTax"]) . ')</td>
          </tr>
				</table>
				';
    $pdf->writeHTML($html);

    $html = '<table border="1" cellpadding="1">
					<tr><td>物料编码</td><td>品名</td><td>规格型号</td><td>数量</td><td>单位</td>
						<td>退货单价</td><td>退货金额</td><td>税率</td><td>价税合计</td>
					</tr>
				';
    foreach ($bill["items"] as $v) {
      $html .= '<tr>';
      $html .= '<td>' . $v["goodsCode"] . '</td>';
      $html .= '<td>' . $v["goodsName"] . '</td>';
      $html .= '<td>' . $v["goodsSpec"] . '</td>';
      $html .= '<td align="right">' . $v["goodsCount"] . '</td>';
      $html .= '<td>' . $v["unitName"] . '</td>';
      $html .= '<td align="right">' . $v["goodsPrice"] . '</td>';
      $html .= '<td align="right">' . $v["goodsMoney"] . '</td>';
      $html .= '<td align="right">' . $v["taxRate"] . '%</td>';
      $html .= '<td align="right">' . $v["moneyWithTax"] . '</td>';
      $html .= '</tr>';
    }

    $html .= "";

    $html .= '</table>';
    $pdf->writeHTML($html, true, false, true, false, '');

    ob_end_clean();

    $pdf->Output("$ref.pdf", "I");
  }

  /**
   * 生成打印采购退货出库单的页面
   *
   * @param array $params
   * @return array
   */
  public function getPRBillDataForLodopPrint($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new PRBillDAO($this->db());
    return $dao->getPRBillDataForLodopPrint($params);
  }
}
