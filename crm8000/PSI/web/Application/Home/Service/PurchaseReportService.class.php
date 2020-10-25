<?php

namespace Home\Service;

use Home\DAO\PurchaseReportDAO;

/**
 * 采购报表Service
 *
 * @author 李静波
 */
class PurchaseReportService extends PSIBaseExService
{
  private $LOG_CATEGORY = "采购报表";

  /**
   * 采购入库明细表 - 数据查询
   */
  public function purchaseDetailQueryData($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();
    $params["loginUserId"] = $this->getLoginUserId();

    $dao = new PurchaseReportDAO($this->db());

    return $dao->purchaseDetailQueryData($params);
  }

  /**
   * 采购入库明细表 - 导出Excel
   */
  public function purchaseDetailExcel($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new PurchaseReportDAO($this->db());
    $data = $dao->purchaseDetailQueryData($params);

    $items = $data["dataList"];

    // 记录业务日志
    $log = "采购入库明细表导出Excel文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    $excel = new \PHPExcel();

    $sheet = $excel->getActiveSheet();
    if (!$sheet) {
      $sheet = $excel->createSheet();
    }

    $sheet->setTitle("采购入库明细表");

    $sheet->getRowDimension('1')->setRowHeight(22);

    $sheet->getColumnDimension('A')->setWidth(15);
    $sheet->setCellValue("A2", "采购单号");

    $sheet->getColumnDimension('B')->setWidth(15);
    $sheet->setCellValue("B2", "入库单单号");

    $sheet->getColumnDimension('C')->setWidth(15);
    $sheet->setCellValue("C2", "入库单业务日期");

    $sheet->getColumnDimension('D')->setWidth(40);
    $sheet->setCellValue("D2", "入库仓库");

    $sheet->getColumnDimension('E')->setWidth(40);
    $sheet->setCellValue("E2", "供应商");

    $sheet->getColumnDimension('F')->setWidth(15);
    $sheet->setCellValue("F2", "物料编码");

    $sheet->getColumnDimension('G')->setWidth(15);
    $sheet->setCellValue("G2", "品名");

    $sheet->getColumnDimension('H')->setWidth(15);
    $sheet->setCellValue("H2", "规格型号");

    $sheet->getColumnDimension('I')->setWidth(15);
    $sheet->setCellValue("I2", "入库数量");

    $sheet->getColumnDimension('J')->setWidth(15);
    $sheet->setCellValue("J2", "单位");

    $sheet->getColumnDimension('K')->setWidth(15);
    $sheet->setCellValue("K2", "采购单价");

    $sheet->getColumnDimension('L')->setWidth(15);
    $sheet->setCellValue("L2", "采购金额");

    $sheet->getColumnDimension('M')->setWidth(15);
    $sheet->setCellValue("M2", "税率(%)");

    $sheet->getColumnDimension('N')->setWidth(15);
    $sheet->setCellValue("N2", "税金");

    $sheet->getColumnDimension('O')->setWidth(15);
    $sheet->setCellValue("O2", "价税合计");

    $sheet->getColumnDimension('P')->setWidth(15);
    $sheet->setCellValue("P2", "含税价");

    $sheet->getColumnDimension('Q')->setWidth(30);
    $sheet->setCellValue("Q2", "备注");

    foreach ($items as $i => $v) {
      $row = $i + 3;
      $sheet->setCellValue("A" . $row, $v["poBillRef"]);
      $sheet->setCellValue("B" . $row, $v["pwBillRef"]);
      $sheet->setCellValue("C" . $row, $v["bizDate"]);
      $sheet->setCellValue("D" . $row, $v["warehouseName"]);
      $sheet->setCellValue("E" . $row, $v["supplierName"]);
      $sheet->setCellValue("F" . $row, $v["goodsCode"]);
      $sheet->setCellValue("G" . $row, $v["goodsName"]);
      $sheet->setCellValue("H" . $row, $v["goodsSpec"]);
      $sheet->setCellValue("I" . $row, $v["goodsCount"]);
      $sheet->setCellValue("J" . $row, $v["unitName"]);
      $sheet->setCellValue("K" . $row, $v["goodsPrice"]);
      $sheet->setCellValue("L" . $row, $v["goodsMoney"]);
      $sheet->setCellValue("M" . $row, $v["taxRate"]);
      $sheet->setCellValue("N" . $row, $v["tax"]);
      $sheet->setCellValue("O" . $row, $v["moneyWithTax"]);
      $sheet->setCellValue("P" . $row, $v["goodsPriceWithTax"]);
      $sheet->setCellValue("Q" . $row, $v["memo"]);
    }

    // 画表格边框
    $styleArray = [
      'borders' => [
        'allborders' => [
          'style' => 'thin'
        ]
      ]
    ];
    $lastRow = count($items) + 2;
    $sheet->getStyle('A2:Q' . $lastRow)->applyFromArray($styleArray);

    $dt = date("YmdHis");

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="采购入库明细表_' . $dt . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = \PHPExcel_IOFactory::createWriter($excel, "Excel2007");
    $writer->save("php://output");
  }

  /**
   * 采购入库明细表 - 导出PDF
   */
  public function purchaseDetailPdf($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    ini_set('max_execution_time', 300); // 300 seconds = 5 minutes

    $bs = new BizConfigService();
    $productionName = $bs->getProductionName();

    $params["companyId"] = $this->getCompanyId();

    $dao = new PurchaseReportDAO($this->db());
    $data = $dao->purchaseDetailQueryData($params);

    $items = $data["dataList"];

    // 记录业务日志
    $log = "采购入库明细表导出PDF文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    ob_start();

    $ps = new PDFService();
    $pdf = $ps->getInstanceForReport();
    $pdf->SetTitle("采购入库明细表");

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

    $pdf->SetHeaderData("", 0, $productionName, "采购入库明细表");

    $pdf->SetFont("stsongstdlight", "", 10);
    $pdf->AddPage();

    /**
     * 注意：
     * TCPDF中，用来拼接HTML的字符串需要用单引号，否则HTML中元素的属性就不会被解析
     */
    $html = '<table border="1" cellpadding="1">
					<tr><td>采购单号</td><td>入库单单号</td>
            <td>入库单业务日期</td>
            <td>入库仓库</td><td>供应商</td>
            <td>物料编码</td><td>品名</td>
						<td>规格型号</td><td>入库数量</td><td>单位</td>
            <td>采购单价</td><td>采购金额</td>
            <td>税率(%)</td><td>税金</td>
            <td>价税合计</td><td>含税价</td><td>备注</td>
					</tr>
				';
    foreach ($items as $v) {
      $html .= '<tr>';
      $html .= '<td>' . $v["poBillRef"] . '</td>';
      $html .= '<td>' . $v["pwBillRef"] . '</td>';
      $html .= '<td>' . $v["bizDate"] . '</td>';
      $html .= '<td>' . $v["warehouseName"] . '</td>';
      $html .= '<td>' . $v["supplierName"] . '</td>';
      $html .= '<td>' . $v["goodsCode"] . '</td>';
      $html .= '<td>' . $v["goodsName"] . '</td>';
      $html .= '<td>' . $v["goodsSpec"] . '</td>';
      $html .= '<td align="right">' . $v["goodsCount"] . '</td>';
      $html .= '<td>' . $v["unitName"] . '</td>';
      $html .= '<td align="right">' . $v["goodsPrice"] . '</td>';
      $html .= '<td align="right">' . $v["goodsMoney"] . '</td>';
      $html .= '<td align="right">' . $v["taxRate"] . '</td>';
      $html .= '<td align="right">' . $v["tax"] . '</td>';
      $html .= '<td align="right">' . $v["moneyWithTax"] . '</td>';
      $html .= '<td align="right">' . $v["goodsPriceWithTax"] . '</td>';
      $html .= '<td>' . $v["memo"] . '</td>';
      $html .= '</tr>';
    }

    $html .= '</table>';
    $pdf->writeHTML($html, true, false, true, false, '');

    ob_end_clean();

    $dt = date("YmdHis");

    $pdf->Output("PD_{$dt}.pdf", "I");
  }
}
