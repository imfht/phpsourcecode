<?php

namespace Home\Service;

use Home\DAO\POBillDAO;
use Home\Common\FIdConst;

/**
 * 采购订单Service
 *
 * @author 李静波
 */
class POBillService extends PSIBaseExService
{
  private $LOG_CATEGORY = "采购订单";

  /**
   * 获得采购订单主表信息列表
   */
  public function pobillList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $us = new UserService();
    $params["loginUserId"] = $us->getLoginUserId();

    $dao = new POBillDAO($this->db());
    return $dao->pobillList($params);
  }

  /**
   * 新建或编辑采购订单
   */
  public function editPOBill($json)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $bill = json_decode(html_entity_decode($json), true);
    if ($bill == null) {
      return $this->bad("传入的参数错误，不是正确的JSON格式");
    }

    $db = $this->db();

    $db->startTrans();

    $dao = new POBillDAO($db);

    $us = new UserService();
    $bill["companyId"] = $us->getCompanyId();
    $bill["loginUserId"] = $us->getLoginUserId();
    $bill["dataOrg"] = $us->getLoginUserDataOrg();

    $id = $bill["id"];

    $log = null;
    if ($id) {
      // 编辑

      $rc = $dao->updatePOBill($bill);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $ref = $bill["ref"];

      $log = "编辑采购订单，单号：{$ref}";
    } else {
      // 新建采购订单

      $rc = $dao->addPOBill($bill);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $id = $bill["id"];
      $ref = $bill["ref"];

      $sobillRef = $bill["sobillRef"];
      if ($sobillRef) {
        $log = "从销售订单( 单号：{$sobillRef} )生成采购订单( 单号:{$ref} )";
      } else {
        $log = "新建采购订单，单号：{$ref}";
      }
    }

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 获得采购订单的信息
   */
  public function poBillInfo($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();
    $params["loginUserId"] = $this->getLoginUserId();
    $params["loginUserName"] = $this->getLoginUserName();

    $dao = new POBillDAO($this->db());
    return $dao->poBillInfo($params);
  }

  /**
   * 采购订单的商品明细
   */
  public function poBillDetailList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new POBillDAO($this->db());
    return $dao->poBillDetailList($params);
  }

  /**
   * 审核采购订单
   */
  public function commitPOBill($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new POBillDAO($db);

    $params["loginUserId"] = $this->getLoginUserId();

    $rc = $dao->commitPOBill($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $ref = $params["ref"];
    $id = $params["id"];

    // 记录业务日志
    $log = "审核采购订单，单号：{$ref}";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 删除采购订单
   */
  public function deletePOBill($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();

    $db->startTrans();

    $dao = new POBillDAO($db);

    $rc = $dao->deletePOBill($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $ref = $params["ref"];
    $log = "删除采购订单，单号：{$ref}";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 取消审核采购订单
   */
  public function cancelConfirmPOBill($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $id = $params["id"];

    $db = $this->db();
    $db->startTrans();

    $dao = new POBillDAO($db);
    $rc = $dao->cancelConfirmPOBill($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $ref = $params["ref"];

    // 记录业务日志
    $log = "取消审核采购订单，单号：{$ref}";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 采购订单生成Excel文件
   */
  public function excel($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $bs = new BizConfigService();
    $productionName = $bs->getProductionName();

    $ref = $params["ref"];

    $dao = new POBillDAO($this->db());

    // 用生成PDF数据的方法来生成Excel，临时先投个懒
    $bill = $dao->getDataForPDF($params);
    if (!$bill) {
      echo "采购订单不存在";
      return;
    }

    // 记录业务日志
    $log = "采购订单(单号：$ref)生成Excel文件";
    $bs = new BizlogService($this->db());
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $excel = new \PHPExcel();
    $sheet = $excel->getActiveSheet();
    if (!$sheet) {
      $sheet = $excel->createSheet();
    }

    $sheet->setTitle("采购订单({$ref})");
    $sheet->getRowDimension('1')->setRowHeight(22);
    $sheet->setCellValue("C1", "采购订单");
    $sheet->setCellValue("A2", "供应商");
    $sheet->setCellValue("B2", $bill["supplierName"]);
    $sheet->setCellValue("A3", "交货地址");
    $sheet->setCellValue("B3", $bill["dealAddress"]);
    $sheet->setCellValue("D3", "交货日期");
    $sheet->setCellValue("E3", $bill["dealDate"]);
    $sheet->setCellValue("A4", "货款：" . $bill["goodsMoney"] . " 价税合计：" . $bill["moneyWithTax"]);

    $captionIndex = 6;
    $sheet->setCellValue("A{$captionIndex}", "物料编码");
    $sheet->getColumnDimension('B')->setWidth(40);
    $sheet->setCellValue("B{$captionIndex}", "品名");
    $sheet->getColumnDimension('C')->setWidth(40);
    $sheet->setCellValue("C{$captionIndex}", "规格型号");
    $sheet->setCellValue("D{$captionIndex}", "数量");
    $sheet->setCellValue("E{$captionIndex}", "单位");
    $sheet->setCellValue("F{$captionIndex}", "单价");
    $sheet->setCellValue("G{$captionIndex}", "金额");
    $sheet->setCellValue("H{$captionIndex}", "税率");
    $sheet->setCellValue("I{$captionIndex}", "价税合计");

    $items = $bill["items"];
    $startIndex = $captionIndex + 1;
    foreach ($items as $i => $v) {
      $row = $i + $startIndex;
      $sheet->setCellValue("A" . $row, $v["goodsCode"]);
      $sheet->setCellValue("B" . $row, $v["goodsName"]);
      $sheet->setCellValue("C" . $row, $v["goodsSpec"]);
      $sheet->setCellValue("D" . $row, $v["goodsCount"]);
      $sheet->setCellValue("E" . $row, $v["unitName"]);
      $sheet->setCellValue("F" . $row, $v["goodsPrice"]);
      $sheet->setCellValue("G" . $row, $v["goodsMoney"]);
      $sheet->setCellValue("H" . $row, $v["taxRate"]);
      $sheet->setCellValue("I" . $row, $v["moneyWithTax"]);
    }

    // 画表格边框
    $styleArray = [
      'borders' => [
        'allborders' => [
          'style' => 'thin'
        ]
      ]
    ];
    $idx = $startIndex - 1;
    $lastRow = count($items) + $startIndex - 1;
    $sheet->getStyle("A{$idx}:I{$lastRow}")->applyFromArray($styleArray);

    $dt = date("YmdHis");

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="采购订单(' . $ref . ')_' . $dt . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = \PHPExcel_IOFactory::createWriter($excel, "Excel2007");
    $writer->save("php://output");
  }

  /**
   * 采购订单生成pdf文件
   */
  public function pdf($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $bs = new BizConfigService();
    $productionName = $bs->getProductionName();

    $ref = $params["ref"];

    $dao = new POBillDAO($this->db());

    $bill = $dao->getDataForPDF($params);
    if (!$bill) {
      return;
    }

    // 记录业务日志
    $log = "采购订单(单号：$ref)生成PDF文件";
    $bs = new BizlogService($this->db());
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    ob_start();

    $utilService = new UtilService();

    $ps = new PDFService();
    $pdf = $ps->getInstance();
    $pdf->SetTitle("采购订单，单号：{$ref}");

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

    $pdf->SetHeaderData("", 0, $productionName, "采购订单");

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
					<tr><td>交货日期：' . $bill["dealDate"] . '</td><td>交货地址:' . $bill["dealAddress"] . '</td></tr>
					<tr><td>业务员：' . $bill["bizUserName"] . '</td><td>税金：' . $bill["tax"] . ' (' . $utilService->moneyToCap($bill["tax"]) . ')</td></tr>
          <tr><td>采购货款:' . $bill["goodsMoney"] . ' (' . $utilService->moneyToCap($bill["goodsMoney"]) . ')</td>
              <td>价税合计：' . $bill["moneyWithTax"] . ' (' . $utilService->moneyToCap($bill["moneyWithTax"]) . ')</td>
          </tr>
				</table>
				';
    $pdf->writeHTML($html);

    $html = '<table border="1" cellpadding="1">
					<tr><td>物料编码</td><td>品名</td><td>规格型号</td><td>数量</td><td>单位</td>
						<td>采购单价</td><td>采购金额</td><td>税率</td><td>价税合计</td>
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
   * 关闭采购订单
   *
   * @param array $params        	
   * @return array
   */
  public function closePOBill($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $id = $params["id"];

    $db = $this->db();
    $db->startTrans();
    $dao = new POBillDAO($this->db());
    $rc = $dao->closePOBill($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $ref = $params["ref"];

    // 记录业务日志
    $log = "关闭采购订单，单号：{$ref}";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 取消关闭采购订单
   *
   * @param array $params        	
   * @return array
   */
  public function cancelClosedPOBill($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $id = $params["id"];

    $db = $this->db();
    $db->startTrans();
    $dao = new POBillDAO($db);
    $rc = $dao->cancelClosedPOBill($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $ref = $params["ref"];

    // 记录业务日志
    $log = "取消关闭采购订单，单号：{$ref}";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 为使用Lodop打印准备数据
   *
   * @param array $params        	
   */
  public function getPOBillDataForLodopPrint($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new POBillDAO($this->db());

    return $dao->getPOBillDataForLodopPrint($params);
  }

  /**
   * 采购订单 - 订单变更
   */
  public function changePurchaseOrder($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $us = new UserService();
    if (!$us->hasPermission(FIdConst::PURCHASE_ORDER_CONFIRM)) {
      return $this->bad("您没有订单变更的权限(拥有订单审核权限的用户才能做订单变更)");
    }

    $params["companyId"] = $this->getCompanyId();

    $db = $this->db();
    $db->startTrans();
    $dao = new POBillDAO($db);
    $rc = $dao->changePurchaseOrder($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $id = $params["id"];
    $ref = $params["ref"];

    // 记录业务日志
    $log = "采购订单[单号={$ref}]变更明细记录";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 查询主表金额相关数据 - 订单变更后刷新界面用
   */
  public function getPOBillDataAterChangeOrder($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new POBillDAO($this->db());
    return $dao->getPOBillDataAterChangeOrder($params);
  }
}
