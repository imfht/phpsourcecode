<?php

namespace Home\Service;

use Home\DAO\SaleReportDAO;

/**
 * 销售报表Service
 *
 * @author 李静波
 */
class SaleReportService extends PSIBaseExService
{
  private $LOG_CATEGORY = "销售报表";

  /**
   * 销售日报表(按商品汇总) - 查询数据
   */
  public function saleDayByGoodsQueryData($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new SaleReportDAO($this->db());
    return $dao->saleDayByGoodsQueryData($params);
  }

  /**
   * 销售日报表(按商品汇总) - 查询数据，用于Lodop打印
   *
   * @param array $params
   * @return array
   */
  public function getSaleDayByGoodsDataForLodopPrint($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new SaleReportDAO($this->db());
    $items = $dao->saleDayByGoodsQueryData($params);

    $data = $this->saleDaySummaryQueryData($params);
    $v = $data[0];

    return [
      "bizDate" => $params["dt"],
      "printDT" => date("Y-m-d H:i:s"),
      "saleMoney" => $v["saleMoney"],
      "rejMoney" => $v["rejMoney"],
      "m" => $v["m"],
      "profit" => $v["profit"],
      "rate" => $v["rate"],
      "items" => $items["dataList"]
    ];
  }

  /**
   * 销售日报表(按商品汇总) - 生成PDF文件
   *
   * @param array $params
   */
  public function saleDayByGoodsPdf($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $params["companyId"] = $this->getCompanyId();

    $bizDT = $params["dt"];

    $bs = new BizConfigService();
    $productionName = $bs->getProductionName();

    $dao = new SaleReportDAO($this->db());

    $data = $dao->saleDayByGoodsQueryData($params);
    $items = $data["dataList"];

    $data = $this->saleDaySummaryQueryData($params);
    $summary = $data[0];

    // 记录业务日志
    $log = "销售日报表(按商品汇总)导出PDF文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    ob_start();

    $ps = new PDFService();
    $pdf = $ps->getInstanceForReport();
    $pdf->SetTitle("销售日报表(按商品汇总)");

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

    $pdf->SetHeaderData("", 0, $productionName, "销售日报表(按商品汇总)");

    $pdf->SetFont("stsongstdlight", "", 10);
    $pdf->AddPage();

    /**
     * 注意：
     * TCPDF中，用来拼接HTML的字符串需要用单引号，否则HTML中元素的属性就不会被解析
     */
    $html = '
				<table>
					<tr>
						<td>业务日期：' . $bizDT . '</td>
						<td>销售出库金额：' . $summary["saleMoney"] . '</td>
						<td>退回入库金额：' . $summary["rejMoney"] . '</td>
						<td>净销售金额：' . $summary["m"] . '</td>
					</tr>
					<tr>
						<td>毛利：' . $summary["profit"] . '</td>
						<td>毛利率：' . $summary["rate"] . '</td>
						<td></td>
						<td></td>
					</tr>
				</table>
				';
    $pdf->writeHTML($html);

    $html = '<table border="1" cellpadding="1">
					<tr><td>商品编号</td><td>商品名称</td><td>规格型号</td><td>销售出库数量</td><td>单位</td>
						<td>销售出库金额</td><td>退货入库数量</td><td>退货入库金额</td><td>净销售数量</td>
						<td>净销售金额</td><td>毛利</td><td>毛利率</td>
					</tr>
				';
    foreach ($items as $v) {
      $html .= '<tr>';
      $html .= '<td>' . $v["goodsCode"] . '</td>';
      $html .= '<td>' . $v["goodsName"] . '</td>';
      $html .= '<td>' . $v["goodsSpec"] . '</td>';
      $html .= '<td align="right">' . $v["saleCount"] . '</td>';
      $html .= '<td>' . $v["unitName"] . '</td>';
      $html .= '<td align="right">' . $v["saleMoney"] . '</td>';
      $html .= '<td align="right">' . $v["rejCount"] . '</td>';
      $html .= '<td align="right">' . $v["rejMoney"] . '</td>';
      $html .= '<td align="right">' . $v["c"] . '</td>';
      $html .= '<td align="right">' . $v["m"] . '</td>';
      $html .= '<td align="right">' . $v["profit"] . '</td>';
      $html .= '<td align="right">' . $v["rate"] . '</td>';
      $html .= '</tr>';
    }

    $html .= '</table>';
    $pdf->writeHTML($html, true, false, true, false, '');

    ob_end_clean();

    $dt = date("YmdHis");

    $pdf->Output("SDG_{$dt}.pdf", "I");
  }

  /**
   * 销售日报表(按商品汇总) - 生成Excel文件
   *
   * @param array $params
   */
  public function saleDayByGoodsExcel($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $params["companyId"] = $this->getCompanyId();

    $bizDT = $params["dt"];

    $dao = new SaleReportDAO($this->db());

    $data = $dao->saleDayByGoodsQueryData($params);
    $items = $data["dataList"];

    $data = $this->saleDaySummaryQueryData($params);
    $summary = $data[0];

    // 记录业务日志
    $log = "销售日报表(按商品汇总)导出Excel文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    $excel = new \PHPExcel();

    $sheet = $excel->getActiveSheet();
    if (!$sheet) {
      $sheet = $excel->createSheet();
    }

    $sheet->setTitle("销售日报表(按商品汇总)");

    $sheet->getRowDimension('1')->setRowHeight(22);
    $info = "业务日期: " . $bizDT . " 销售出库金额: " . $summary["saleMoney"] . " 退货入库金额: " . $summary["rejMoney"] . " 毛利: " . $summary["profit"] . " 毛利率: " . $summary["rate"];
    $sheet->setCellValue("A1", $info);

    $sheet->getColumnDimension('A')->setWidth(15);
    $sheet->setCellValue("A2", "商品编码");

    $sheet->getColumnDimension('B')->setWidth(40);
    $sheet->setCellValue("B2", "商品名称");

    $sheet->getColumnDimension('C')->setWidth(40);
    $sheet->setCellValue("C2", "规格型号");

    $sheet->getColumnDimension('D')->setWidth(15);
    $sheet->setCellValue("D2", "销售出库数量");

    $sheet->getColumnDimension('E')->setWidth(10);
    $sheet->setCellValue("E2", "单位");

    $sheet->getColumnDimension('F')->setWidth(15);
    $sheet->setCellValue("F2", "销售出库金额");

    $sheet->getColumnDimension('G')->setWidth(15);
    $sheet->setCellValue("G2", "退货入库数量");

    $sheet->getColumnDimension('H')->setWidth(15);
    $sheet->setCellValue("H2", "退货入库金额");

    $sheet->getColumnDimension('I')->setWidth(15);
    $sheet->setCellValue("I2", "净销售数量");

    $sheet->getColumnDimension('J')->setWidth(15);
    $sheet->setCellValue("J2", "净销售金额");

    $sheet->getColumnDimension('K')->setWidth(15);
    $sheet->setCellValue("K2", "毛利");

    $sheet->getColumnDimension('L')->setWidth(15);
    $sheet->setCellValue("L2", "毛利率");

    foreach ($items as $i => $v) {
      $row = $i + 3;
      $sheet->setCellValue("A" . $row, $v["goodsCode"]);
      $sheet->setCellValue("B" . $row, $v["goodsName"]);
      $sheet->setCellValue("C" . $row, $v["goodsSpec"]);
      $sheet->setCellValue("D" . $row, $v["saleCount"]);
      $sheet->setCellValue("E" . $row, $v["unitName"]);
      $sheet->setCellValue("F" . $row, $v["saleMoney"]);
      $sheet->setCellValue("G" . $row, $v["rejCount"]);
      $sheet->setCellValue("H" . $row, $v["rejMoney"]);
      $sheet->setCellValue("I" . $row, $v["c"]);
      $sheet->setCellValue("J" . $row, $v["m"]);
      $sheet->setCellValue("K" . $row, $v["profit"]);
      $sheet->setCellValue("L" . $row, $v["rate"]);
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
    $sheet->getStyle('A2:L' . $lastRow)->applyFromArray($styleArray);

    $dt = date("YmdHis");

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="销售日报表(按商品汇总)_' . $dt . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = \PHPExcel_IOFactory::createWriter($excel, "Excel2007");
    $writer->save("php://output");
  }

  private function saleDaySummaryQueryData($params)
  {
    $dt = $params["dt"];

    $result = array();
    $result[0]["bizDT"] = $dt;

    $us = new UserService();
    $companyId = $us->getCompanyId();

    $db = M();
    $sql = "select sum(d.goods_money) as goods_money, sum(d.inventory_money) as inventory_money
					from t_ws_bill w, t_ws_bill_detail d
					where w.id = d.wsbill_id and w.bizdt = '%s'
						and w.bill_status >= 1000 and w.company_id = '%s' ";
    $data = $db->query($sql, $dt, $companyId);
    $saleMoney = $data[0]["goods_money"];
    if (!$saleMoney) {
      $saleMoney = 0;
    }
    $saleInventoryMoney = $data[0]["inventory_money"];
    if (!$saleInventoryMoney) {
      $saleInventoryMoney = 0;
    }
    $result[0]["saleMoney"] = $saleMoney;

    $sql = "select  sum(d.rejection_sale_money) as rej_money,
						sum(d.inventory_money) as rej_inventory_money
					from t_sr_bill s, t_sr_bill_detail d
					where s.id = d.srbill_id and s.bizdt = '%s'
						and s.bill_status = 1000 and s.company_id = '%s' ";
    $data = $db->query($sql, $dt, $companyId);
    $rejSaleMoney = $data[0]["rej_money"];
    if (!$rejSaleMoney) {
      $rejSaleMoney = 0;
    }
    $rejInventoryMoney = $data[0]["rej_inventory_money"];
    if (!$rejInventoryMoney) {
      $rejInventoryMoney = 0;
    }

    $result[0]["rejMoney"] = $rejSaleMoney;

    $m = $saleMoney - $rejSaleMoney;
    $result[0]["m"] = $m;
    $profit = $saleMoney - $rejSaleMoney - $saleInventoryMoney + $rejInventoryMoney;
    $result[0]["profit"] = $profit;
    if ($m > 0) {
      $result[0]["rate"] = sprintf("%0.2f", $profit / $m * 100) . "%";
    }

    return $result;
  }

  /**
   * 销售日报表(按商品汇总) - 查询汇总数据
   */
  public function saleDayByGoodsSummaryQueryData($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    return $this->saleDaySummaryQueryData($params);
  }

  /**
   * 销售日报表(按客户汇总) - 查询数据
   */
  public function saleDayByCustomerQueryData($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new SaleReportDAO($this->db());
    return $dao->saleDayByCustomerQueryData($params);
  }

  /**
   * 销售日报表(按客户汇总) - 查询汇总数据
   */
  public function saleDayByCustomerSummaryQueryData($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    return $this->saleDaySummaryQueryData($params);
  }

  /**
   * 销售日报表(按客户汇总) - 查询数据，用于Lodop打印
   *
   * @param array $params
   * @return array
   */
  public function getSaleDayByCustomerDataForLodopPrint($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new SaleReportDAO($this->db());
    $items = $dao->saleDayByCustomerQueryData($params);

    $data = $this->saleDaySummaryQueryData($params);
    $v = $data[0];

    return [
      "bizDate" => $params["dt"],
      "printDT" => date("Y-m-d H:i:s"),
      "saleMoney" => $v["saleMoney"],
      "rejMoney" => $v["rejMoney"],
      "m" => $v["m"],
      "profit" => $v["profit"],
      "rate" => $v["rate"],
      "items" => $items["dataList"]
    ];
  }

  /**
   * 销售日报表(按客户汇总) - 生成PDF文件
   *
   * @param array $params
   */
  public function saleDayByCustomerPdf($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $params["companyId"] = $this->getCompanyId();

    $bizDT = $params["dt"];

    $bs = new BizConfigService();
    $productionName = $bs->getProductionName();

    $dao = new SaleReportDAO($this->db());

    $data = $dao->saleDayByCustomerQueryData($params);
    $items = $data["dataList"];

    $data = $this->saleDaySummaryQueryData($params);
    $summary = $data[0];

    // 记录业务日志
    $log = "销售日报表(按客户汇总)导出PDF文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    ob_start();

    $ps = new PDFService();
    $pdf = $ps->getInstanceForReport();
    $pdf->SetTitle("销售日报表(按客户汇总)");

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

    $pdf->SetHeaderData("", 0, $productionName, "销售日报表(按客户汇总)");

    $pdf->SetFont("stsongstdlight", "", 10);
    $pdf->AddPage();

    /**
     * 注意：
     * TCPDF中，用来拼接HTML的字符串需要用单引号，否则HTML中元素的属性就不会被解析
     */
    $html = '
				<table>
					<tr>
						<td>业务日期：' . $bizDT . '</td>
						<td>销售出库金额：' . $summary["saleMoney"] . '</td>
						<td>退回入库金额：' . $summary["rejMoney"] . '</td>
						<td>净销售金额：' . $summary["m"] . '</td>
					</tr>
					<tr>
						<td>毛利：' . $summary["profit"] . '</td>
						<td>毛利率：' . $summary["rate"] . '</td>
						<td></td>
						<td></td>
					</tr>
				</table>
				';
    $pdf->writeHTML($html);

    $html = '<table border="1" cellpadding="1">
					<tr><td>客户编号</td><td>客户名称</td>
						<td>销售出库金额</td><td>退货入库金额</td>
						<td>净销售金额</td><td>毛利</td><td>毛利率</td>
					</tr>
				';
    foreach ($items as $v) {
      $html .= '<tr>';
      $html .= '<td>' . $v["customerCode"] . '</td>';
      $html .= '<td>' . $v["customerName"] . '</td>';
      $html .= '<td align="right">' . $v["saleMoney"] . '</td>';
      $html .= '<td align="right">' . $v["rejMoney"] . '</td>';
      $html .= '<td align="right">' . $v["m"] . '</td>';
      $html .= '<td align="right">' . $v["profit"] . '</td>';
      $html .= '<td align="right">' . $v["rate"] . '</td>';
      $html .= '</tr>';
    }

    $html .= '</table>';
    $pdf->writeHTML($html, true, false, true, false, '');

    ob_end_clean();

    $dt = date("YmdHis");

    $pdf->Output("SDC_{$dt}.pdf", "I");
  }

  /**
   * 销售日报表(按客户汇总) - 生成Excel文件
   *
   * @param array $params
   */
  public function saleDayByCustomerExcel($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $params["companyId"] = $this->getCompanyId();

    $bizDT = $params["dt"];

    $dao = new SaleReportDAO($this->db());

    $data = $dao->saleDayByCustomerQueryData($params);
    $items = $data["dataList"];

    $data = $this->saleDaySummaryQueryData($params);
    $summary = $data[0];

    // 记录业务日志
    $log = "销售日报表(按客户汇总)导出Excel文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    $excel = new \PHPExcel();

    $sheet = $excel->getActiveSheet();
    if (!$sheet) {
      $sheet = $excel->createSheet();
    }

    $sheet->setTitle("销售日报表(按客户汇总)");

    $sheet->getRowDimension('1')->setRowHeight(22);
    $info = "业务日期: " . $bizDT . " 销售出库金额: " . $summary["saleMoney"] . " 退货入库金额: " . $summary["rejMoney"] . " 毛利: " . $summary["profit"] . " 毛利率: " . $summary["rate"];
    $sheet->setCellValue("A1", $info);

    $sheet->getColumnDimension('A')->setWidth(15);
    $sheet->setCellValue("A2", "客户编码");

    $sheet->getColumnDimension('B')->setWidth(40);
    $sheet->setCellValue("B2", "客户名称");

    $sheet->getColumnDimension('C')->setWidth(15);
    $sheet->setCellValue("C2", "销售出库金额");

    $sheet->getColumnDimension('D')->setWidth(15);
    $sheet->setCellValue("D2", "退货入库金额");

    $sheet->getColumnDimension('E')->setWidth(15);
    $sheet->setCellValue("E2", "净销售金额");

    $sheet->getColumnDimension('F')->setWidth(15);
    $sheet->setCellValue("F2", "毛利");

    $sheet->getColumnDimension('G')->setWidth(15);
    $sheet->setCellValue("G2", "毛利率");

    foreach ($items as $i => $v) {
      $row = $i + 3;
      $sheet->setCellValue("A" . $row, $v["customerCode"]);
      $sheet->setCellValue("B" . $row, $v["customerName"]);
      $sheet->setCellValue("C" . $row, $v["saleMoney"]);
      $sheet->setCellValue("D" . $row, $v["rejMoney"]);
      $sheet->setCellValue("E" . $row, $v["m"]);
      $sheet->setCellValue("F" . $row, $v["profit"]);
      $sheet->setCellValue("G" . $row, $v["rate"]);
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
    $sheet->getStyle('A2:G' . $lastRow)->applyFromArray($styleArray);

    $dt = date("YmdHis");

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="销售日报表(按客户汇总)_' . $dt . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = \PHPExcel_IOFactory::createWriter($excel, "Excel2007");
    $writer->save("php://output");
  }

  /**
   * 销售日报表(按仓库汇总) - 查询数据
   */
  public function saleDayByWarehouseQueryData($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new SaleReportDAO($this->db());
    return $dao->saleDayByWarehouseQueryData($params);
  }

  /**
   * 销售日报表(按仓库汇总) - 查询汇总数据
   */
  public function saleDayByWarehouseSummaryQueryData($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    return $this->saleDaySummaryQueryData($params);
  }

  /**
   * 销售日报表(按仓库汇总) - 查询数据，用于Lodop打印
   *
   * @param array $params
   * @return array
   */
  public function getSaleDayByWarehouseDataForLodopPrint($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new SaleReportDAO($this->db());
    $items = $dao->saleDayByWarehouseQueryData($params);

    $data = $this->saleDaySummaryQueryData($params);
    $v = $data[0];

    return [
      "bizDate" => $params["dt"],
      "printDT" => date("Y-m-d H:i:s"),
      "saleMoney" => $v["saleMoney"],
      "rejMoney" => $v["rejMoney"],
      "m" => $v["m"],
      "profit" => $v["profit"],
      "rate" => $v["rate"],
      "items" => $items["dataList"]
    ];
  }

  /**
   * 销售日报表(按仓库汇总) - 生成PDF文件
   *
   * @param array $params
   */
  public function saleDayByWarehousePdf($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $params["companyId"] = $this->getCompanyId();

    $bizDT = $params["dt"];

    $bs = new BizConfigService();
    $productionName = $bs->getProductionName();

    $dao = new SaleReportDAO($this->db());

    $data = $dao->saleDayByWarehouseQueryData($params);
    $items = $data["dataList"];

    $data = $this->saleDaySummaryQueryData($params);
    $summary = $data[0];

    // 记录业务日志
    $log = "销售日报表(按仓库汇总)导出PDF文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    ob_start();

    $ps = new PDFService();
    $pdf = $ps->getInstanceForReport();
    $pdf->SetTitle("销售日报表(按仓库汇总)");

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

    $pdf->SetHeaderData("", 0, $productionName, "销售日报表(按仓库汇总)");

    $pdf->SetFont("stsongstdlight", "", 10);
    $pdf->AddPage();

    /**
     * 注意：
     * TCPDF中，用来拼接HTML的字符串需要用单引号，否则HTML中元素的属性就不会被解析
     */
    $html = '
				<table>
					<tr>
						<td>业务日期：' . $bizDT . '</td>
						<td>销售出库金额：' . $summary["saleMoney"] . '</td>
						<td>退回入库金额：' . $summary["rejMoney"] . '</td>
						<td>净销售金额：' . $summary["m"] . '</td>
					</tr>
					<tr>
						<td>毛利：' . $summary["profit"] . '</td>
						<td>毛利率：' . $summary["rate"] . '</td>
						<td></td>
						<td></td>
					</tr>
				</table>
				';
    $pdf->writeHTML($html);

    $html = '<table border="1" cellpadding="1">
					<tr><td>仓库编码</td><td>仓库</td>
						<td>销售出库金额</td><td>退货入库金额</td>
						<td>净销售金额</td><td>毛利</td><td>毛利率</td>
						<td>每平方米销售额</td><td>仓库销售核算面积</td>
					</tr>
				';
    foreach ($items as $v) {
      $html .= '<tr>';
      $html .= '<td>' . $v["warehouseCode"] . '</td>';
      $html .= '<td>' . $v["warehouseName"] . '</td>';
      $html .= '<td align="right">' . $v["saleMoney"] . '</td>';
      $html .= '<td align="right">' . $v["rejMoney"] . '</td>';
      $html .= '<td align="right">' . $v["m"] . '</td>';
      $html .= '<td align="right">' . $v["profit"] . '</td>';
      $html .= '<td align="right">' . $v["rate"] . '</td>';
      $html .= '<td align="right">' . $v["saleAreaRate"] . '</td>';
      $html .= '<td align="right">' . $v["saleArea"] . '</td>';
      $html .= '</tr>';
    }

    $html .= '</table>';
    $pdf->writeHTML($html, true, false, true, false, '');

    ob_end_clean();

    $dt = date("YmdHis");

    $pdf->Output("SDW_{$dt}.pdf", "I");
  }

  /**
   * 销售日报表(按仓库汇总) - 生成Excel文件
   *
   * @param array $params
   */
  public function saleDayByWarehouseExcel($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $params["companyId"] = $this->getCompanyId();

    $bizDT = $params["dt"];

    $dao = new SaleReportDAO($this->db());

    $data = $dao->saleDayByWarehouseQueryData($params);
    $items = $data["dataList"];

    $data = $this->saleDaySummaryQueryData($params);
    $summary = $data[0];

    // 记录业务日志
    $log = "销售日报表(按仓库汇总)导出Excel文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    $excel = new \PHPExcel();

    $sheet = $excel->getActiveSheet();
    if (!$sheet) {
      $sheet = $excel->createSheet();
    }

    $sheet->setTitle("销售日报表(按仓库汇总)");

    $sheet->getRowDimension('1')->setRowHeight(22);
    $info = "业务日期: " . $bizDT . " 销售出库金额: " . $summary["saleMoney"] . " 退货入库金额: " . $summary["rejMoney"] . " 毛利: " . $summary["profit"] . " 毛利率: " . $summary["rate"];
    $sheet->setCellValue("A1", $info);

    $sheet->getColumnDimension('A')->setWidth(15);
    $sheet->setCellValue("A2", "仓库编码");

    $sheet->getColumnDimension('B')->setWidth(40);
    $sheet->setCellValue("B2", "仓库");

    $sheet->getColumnDimension('C')->setWidth(15);
    $sheet->setCellValue("C2", "销售出库金额");

    $sheet->getColumnDimension('D')->setWidth(15);
    $sheet->setCellValue("D2", "退货入库金额");

    $sheet->getColumnDimension('E')->setWidth(15);
    $sheet->setCellValue("E2", "净销售金额");

    $sheet->getColumnDimension('F')->setWidth(15);
    $sheet->setCellValue("F2", "毛利");

    $sheet->getColumnDimension('G')->setWidth(15);
    $sheet->setCellValue("G2", "毛利率");

    $sheet->getColumnDimension('H')->setWidth(20);
    $sheet->setCellValue("H2", "每平方米销售额");

    $sheet->getColumnDimension('I')->setWidth(20);
    $sheet->setCellValue("I2", "仓库销售核算面积");

    foreach ($items as $i => $v) {
      $row = $i + 3;
      $sheet->setCellValue("A" . $row, $v["warehouseCode"]);
      $sheet->setCellValue("B" . $row, $v["warehouseName"]);
      $sheet->setCellValue("C" . $row, $v["saleMoney"]);
      $sheet->setCellValue("D" . $row, $v["rejMoney"]);
      $sheet->setCellValue("E" . $row, $v["m"]);
      $sheet->setCellValue("F" . $row, $v["profit"]);
      $sheet->setCellValue("G" . $row, $v["rate"]);
      $sheet->setCellValue("H" . $row, $v["saleAreaRate"]);
      $sheet->setCellValue("I" . $row, $v["saleArea"]);
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
    $sheet->getStyle('A2:I' . $lastRow)->applyFromArray($styleArray);

    $dt = date("YmdHis");

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="销售日报表(按仓库汇总)_' . $dt . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = \PHPExcel_IOFactory::createWriter($excel, "Excel2007");
    $writer->save("php://output");
  }

  /**
   * 销售日报表(按业务员汇总) - 查询数据
   */
  public function saleDayByBizuserQueryData($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new SaleReportDAO($this->db());
    return $dao->saleDayByBizuserQueryData($params);
  }

  /**
   * 销售日报表(按业务员汇总) - 查询汇总数据
   */
  public function saleDayByBizuserSummaryQueryData($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    return $this->saleDaySummaryQueryData($params);
  }

  /**
   * 销售日报表(按业务员汇总) - 查询数据，用于Lodop打印
   *
   * @param array $params
   * @return array
   */
  public function getSaleDayByBizuserDataForLodopPrint($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new SaleReportDAO($this->db());
    $items = $dao->saleDayByBizuserQueryData($params);

    $data = $this->saleDaySummaryQueryData($params);
    $v = $data[0];

    return [
      "bizDate" => $params["dt"],
      "printDT" => date("Y-m-d H:i:s"),
      "saleMoney" => $v["saleMoney"],
      "rejMoney" => $v["rejMoney"],
      "m" => $v["m"],
      "profit" => $v["profit"],
      "rate" => $v["rate"],
      "items" => $items["dataList"]
    ];
  }

  /**
   * 销售日报表(按业务员汇总) - 生成PDF文件
   *
   * @param array $params
   */
  public function saleDayByBizuserPdf($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $params["companyId"] = $this->getCompanyId();

    $bizDT = $params["dt"];

    $bs = new BizConfigService();
    $productionName = $bs->getProductionName();

    $dao = new SaleReportDAO($this->db());

    $data = $dao->saleDayByBizuserQueryData($params);
    $items = $data["dataList"];

    $data = $this->saleDaySummaryQueryData($params);
    $summary = $data[0];

    // 记录业务日志
    $log = "销售日报表(按业务员汇总)导出PDF文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    ob_start();

    $ps = new PDFService();
    $pdf = $ps->getInstanceForReport();
    $pdf->SetTitle("销售日报表(按业务员汇总)");

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

    $pdf->SetHeaderData("", 0, $productionName, "销售日报表(按业务员汇总)");

    $pdf->SetFont("stsongstdlight", "", 10);
    $pdf->AddPage();

    /**
     * 注意：
     * TCPDF中，用来拼接HTML的字符串需要用单引号，否则HTML中元素的属性就不会被解析
     */
    $html = '
				<table>
					<tr>
						<td>业务日期：' . $bizDT . '</td>
						<td>销售出库金额：' . $summary["saleMoney"] . '</td>
						<td>退回入库金额：' . $summary["rejMoney"] . '</td>
						<td>净销售金额：' . $summary["m"] . '</td>
					</tr>
					<tr>
						<td>毛利：' . $summary["profit"] . '</td>
						<td>毛利率：' . $summary["rate"] . '</td>
						<td></td>
						<td></td>
					</tr>
				</table>
				';
    $pdf->writeHTML($html);

    $html = '<table border="1" cellpadding="1">
					<tr><td>业务员编码</td><td>业务员</td>
						<td>销售出库金额</td><td>退货入库金额</td>
						<td>净销售金额</td><td>毛利</td><td>毛利率</td>
					</tr>
				';
    foreach ($items as $v) {
      $html .= '<tr>';
      $html .= '<td>' . $v["userCode"] . '</td>';
      $html .= '<td>' . $v["userName"] . '</td>';
      $html .= '<td align="right">' . $v["saleMoney"] . '</td>';
      $html .= '<td align="right">' . $v["rejMoney"] . '</td>';
      $html .= '<td align="right">' . $v["m"] . '</td>';
      $html .= '<td align="right">' . $v["profit"] . '</td>';
      $html .= '<td align="right">' . $v["rate"] . '</td>';
      $html .= '</tr>';
    }

    $html .= '</table>';
    $pdf->writeHTML($html, true, false, true, false, '');

    ob_end_clean();

    $dt = date("YmdHis");

    $pdf->Output("SDU_{$dt}.pdf", "I");
  }

  /**
   * 销售日报表(按业务员汇总) - 生成Excel文件
   *
   * @param array $params
   */
  public function saleDayByBizuserExcel($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $params["companyId"] = $this->getCompanyId();

    $bizDT = $params["dt"];

    $dao = new SaleReportDAO($this->db());

    $data = $dao->saleDayByBizuserQueryData($params);
    $items = $data["dataList"];

    $data = $this->saleDaySummaryQueryData($params);
    $summary = $data[0];

    // 记录业务日志
    $log = "销售日报表(按业务员汇总)导出Excel文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    $excel = new \PHPExcel();

    $sheet = $excel->getActiveSheet();
    if (!$sheet) {
      $sheet = $excel->createSheet();
    }

    $sheet->setTitle("销售日报表(按业务员汇总)");

    $sheet->getRowDimension('1')->setRowHeight(22);
    $info = "业务日期: " . $bizDT . " 销售出库金额: " . $summary["saleMoney"] . " 退货入库金额: " . $summary["rejMoney"] . " 毛利: " . $summary["profit"] . " 毛利率: " . $summary["rate"];
    $sheet->setCellValue("A1", $info);

    $sheet->getColumnDimension('A')->setWidth(15);
    $sheet->setCellValue("A2", "业务员编码");

    $sheet->getColumnDimension('B')->setWidth(40);
    $sheet->setCellValue("B2", "业务员");

    $sheet->getColumnDimension('C')->setWidth(15);
    $sheet->setCellValue("C2", "销售出库金额");

    $sheet->getColumnDimension('D')->setWidth(15);
    $sheet->setCellValue("D2", "退货入库金额");

    $sheet->getColumnDimension('E')->setWidth(15);
    $sheet->setCellValue("E2", "净销售金额");

    $sheet->getColumnDimension('F')->setWidth(15);
    $sheet->setCellValue("F2", "毛利");

    $sheet->getColumnDimension('G')->setWidth(15);
    $sheet->setCellValue("G2", "毛利率");

    foreach ($items as $i => $v) {
      $row = $i + 3;
      $sheet->setCellValue("A" . $row, $v["userCode"]);
      $sheet->setCellValue("B" . $row, $v["userName"]);
      $sheet->setCellValue("C" . $row, $v["saleMoney"]);
      $sheet->setCellValue("D" . $row, $v["rejMoney"]);
      $sheet->setCellValue("E" . $row, $v["m"]);
      $sheet->setCellValue("F" . $row, $v["profit"]);
      $sheet->setCellValue("G" . $row, $v["rate"]);
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
    $sheet->getStyle('A2:G' . $lastRow)->applyFromArray($styleArray);

    $dt = date("YmdHis");

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="销售日报表(按业务员汇总)_' . $dt . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = \PHPExcel_IOFactory::createWriter($excel, "Excel2007");
    $writer->save("php://output");
  }

  /**
   * 销售月报表(按商品汇总) - 查询数据
   */
  public function saleMonthByGoodsQueryData($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new SaleReportDAO($this->db());
    return $dao->saleMonthByGoodsQueryData($params);
  }

  private function saleMonthSummaryQueryData($params)
  {
    $year = $params["year"];
    $month = $params["month"];

    $result = array();
    if ($month < 10) {
      $result[0]["bizDT"] = "$year-0$month";
    } else {
      $result[0]["bizDT"] = "$year-$month";
    }

    $us = new UserService();
    $companyId = $us->getCompanyId();

    $db = M();
    $sql = "select sum(d.goods_money) as goods_money, sum(d.inventory_money) as inventory_money
					from t_ws_bill w, t_ws_bill_detail d
					where w.id = d.wsbill_id and year(w.bizdt) = %d and month(w.bizdt) = %d
						and w.bill_status >= 1000 and w.company_id = '%s' ";
    $data = $db->query($sql, $year, $month, $companyId);
    $saleMoney = $data[0]["goods_money"];
    if (!$saleMoney) {
      $saleMoney = 0;
    }
    $saleInventoryMoney = $data[0]["inventory_money"];
    if (!$saleInventoryMoney) {
      $saleInventoryMoney = 0;
    }
    $result[0]["saleMoney"] = $saleMoney;

    $sql = "select  sum(d.rejection_sale_money) as rej_money,
						sum(d.inventory_money) as rej_inventory_money
					from t_sr_bill s, t_sr_bill_detail d
					where s.id = d.srbill_id and year(s.bizdt) = %d and month(s.bizdt) = %d
						and s.bill_status = 1000 and s.company_id = '%s' ";
    $data = $db->query($sql, $year, $month, $companyId);
    $rejSaleMoney = $data[0]["rej_money"];
    if (!$rejSaleMoney) {
      $rejSaleMoney = 0;
    }
    $rejInventoryMoney = $data[0]["rej_inventory_money"];
    if (!$rejInventoryMoney) {
      $rejInventoryMoney = 0;
    }

    $result[0]["rejMoney"] = $rejSaleMoney;

    $m = $saleMoney - $rejSaleMoney;
    $result[0]["m"] = $m;
    $profit = $saleMoney - $rejSaleMoney - $saleInventoryMoney + $rejInventoryMoney;
    $result[0]["profit"] = $profit;
    if ($m > 0) {
      $result[0]["rate"] = sprintf("%0.2f", $profit / $m * 100) . "%";
    }

    return $result;
  }

  /**
   * 销售月报表(按商品汇总) - 查询汇总数据
   */
  public function saleMonthByGoodsSummaryQueryData($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    return $this->saleMonthSummaryQueryData($params);
  }

  /**
   * 销售月报表(按商品汇总) - 查询数据，用于Lodop打印
   *
   * @param array $params
   * @return array
   */
  public function getSaleMonthByGoodsDataForLodopPrint($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $year = $params["year"];
    $month = $params["month"];

    $bizDT = "";
    if ($month < 10) {
      $bizDT = "$year-0$month";
    } else {
      $bizDT = "$year-$month";
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new SaleReportDAO($this->db());
    $items = $dao->saleMonthByGoodsQueryData($params);

    $data = $this->saleMonthSummaryQueryData($params);
    $v = $data[0];

    return [
      "bizDate" => $bizDT,
      "printDT" => date("Y-m-d H:i:s"),
      "saleMoney" => $v["saleMoney"],
      "rejMoney" => $v["rejMoney"],
      "m" => $v["m"],
      "profit" => $v["profit"],
      "rate" => $v["rate"],
      "items" => $items["dataList"]
    ];
  }

  /**
   * 销售月报表(按商品汇总) - 生成PDF文件
   *
   * @param array $params
   */
  public function saleMonthByGoodsPdf($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $params["companyId"] = $this->getCompanyId();

    $year = $params["year"];
    $month = $params["month"];

    $bizDT = "";
    if ($month < 10) {
      $bizDT = "$year-0$month";
    } else {
      $bizDT = "$year-$month";
    }

    $bs = new BizConfigService();
    $productionName = $bs->getProductionName();

    $dao = new SaleReportDAO($this->db());

    $data = $dao->saleMonthByGoodsQueryData($params);
    $items = $data["dataList"];

    $data = $this->saleMonthSummaryQueryData($params);
    $summary = $data[0];

    // 记录业务日志
    $log = "销售月报表(按商品汇总)导出PDF文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    ob_start();

    $ps = new PDFService();
    $pdf = $ps->getInstanceForReport();
    $pdf->SetTitle("销售月报表(按商品汇总)");

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

    $pdf->SetHeaderData("", 0, $productionName, "销售月报表(按商品汇总)");

    $pdf->SetFont("stsongstdlight", "", 10);
    $pdf->AddPage();

    /**
     * 注意：
     * TCPDF中，用来拼接HTML的字符串需要用单引号，否则HTML中元素的属性就不会被解析
     */
    $html = '
				<table>
					<tr>
						<td>月份：' . $bizDT . '</td>
						<td>销售出库金额：' . $summary["saleMoney"] . '</td>
						<td>退回入库金额：' . $summary["rejMoney"] . '</td>
						<td>净销售金额：' . $summary["m"] . '</td>
					</tr>
					<tr>
						<td>毛利：' . $summary["profit"] . '</td>
						<td>毛利率：' . $summary["rate"] . '</td>
						<td></td>
						<td></td>
					</tr>
				</table>
				';
    $pdf->writeHTML($html);

    $html = '<table border="1" cellpadding="1">
					<tr><td>商品编号</td><td>商品名称</td><td>规格型号</td><td>销售出库数量</td><td>单位</td>
						<td>销售出库金额</td><td>退货入库数量</td><td>退货入库金额</td><td>净销售数量</td>
						<td>净销售金额</td><td>毛利</td><td>毛利率</td>
					</tr>
				';
    foreach ($items as $v) {
      $html .= '<tr>';
      $html .= '<td>' . $v["goodsCode"] . '</td>';
      $html .= '<td>' . $v["goodsName"] . '</td>';
      $html .= '<td>' . $v["goodsSpec"] . '</td>';
      $html .= '<td align="right">' . $v["saleCount"] . '</td>';
      $html .= '<td>' . $v["unitName"] . '</td>';
      $html .= '<td align="right">' . $v["saleMoney"] . '</td>';
      $html .= '<td align="right">' . $v["rejCount"] . '</td>';
      $html .= '<td align="right">' . $v["rejMoney"] . '</td>';
      $html .= '<td align="right">' . $v["c"] . '</td>';
      $html .= '<td align="right">' . $v["m"] . '</td>';
      $html .= '<td align="right">' . $v["profit"] . '</td>';
      $html .= '<td align="right">' . $v["rate"] . '</td>';
      $html .= '</tr>';
    }

    $html .= '</table>';
    $pdf->writeHTML($html, true, false, true, false, '');

    ob_end_clean();

    $dt = date("YmdHis");

    $pdf->Output("SMG_{$dt}.pdf", "I");
  }

  /**
   * 销售月报表(按商品汇总) - 生成Excel文件
   *
   * @param array $params
   */
  public function saleMonthByGoodsExcel($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $params["companyId"] = $this->getCompanyId();

    $year = $params["year"];
    $month = $params["month"];

    $bizDT = "";
    if ($month < 10) {
      $bizDT = "$year-0$month";
    } else {
      $bizDT = "$year-$month";
    }

    $dao = new SaleReportDAO($this->db());

    $data = $dao->saleMonthByGoodsQueryData($params);
    $items = $data["dataList"];

    $data = $this->saleMonthSummaryQueryData($params);
    $summary = $data[0];

    // 记录业务日志
    $log = "销售月报表(按商品汇总)导出Excel文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    $excel = new \PHPExcel();

    $sheet = $excel->getActiveSheet();
    if (!$sheet) {
      $sheet = $excel->createSheet();
    }

    $sheet->setTitle("销售月报表(按商品汇总)");

    $sheet->getRowDimension('1')->setRowHeight(22);
    $info = "月份: " . $bizDT . " 销售出库金额: " . $summary["saleMoney"] . " 退货入库金额: " . $summary["rejMoney"] . " 毛利: " . $summary["profit"] . " 毛利率: " . $summary["rate"];
    $sheet->setCellValue("A1", $info);

    $sheet->getColumnDimension('A')->setWidth(15);
    $sheet->setCellValue("A2", "商品编码");

    $sheet->getColumnDimension('B')->setWidth(40);
    $sheet->setCellValue("B2", "商品名称");

    $sheet->getColumnDimension('C')->setWidth(40);
    $sheet->setCellValue("C2", "规格型号");

    $sheet->getColumnDimension('D')->setWidth(15);
    $sheet->setCellValue("D2", "销售出库数量");

    $sheet->getColumnDimension('E')->setWidth(10);
    $sheet->setCellValue("E2", "单位");

    $sheet->getColumnDimension('F')->setWidth(15);
    $sheet->setCellValue("F2", "销售出库金额");

    $sheet->getColumnDimension('G')->setWidth(15);
    $sheet->setCellValue("G2", "退货入库数量");

    $sheet->getColumnDimension('H')->setWidth(15);
    $sheet->setCellValue("H2", "退货入库金额");

    $sheet->getColumnDimension('I')->setWidth(15);
    $sheet->setCellValue("I2", "净销售数量");

    $sheet->getColumnDimension('J')->setWidth(15);
    $sheet->setCellValue("J2", "净销售金额");

    $sheet->getColumnDimension('K')->setWidth(15);
    $sheet->setCellValue("K2", "毛利");

    $sheet->getColumnDimension('L')->setWidth(15);
    $sheet->setCellValue("L2", "毛利率");

    foreach ($items as $i => $v) {
      $row = $i + 3;
      $sheet->setCellValue("A" . $row, $v["goodsCode"]);
      $sheet->setCellValue("B" . $row, $v["goodsName"]);
      $sheet->setCellValue("C" . $row, $v["goodsSpec"]);
      $sheet->setCellValue("D" . $row, $v["saleCount"]);
      $sheet->setCellValue("E" . $row, $v["unitName"]);
      $sheet->setCellValue("F" . $row, $v["saleMoney"]);
      $sheet->setCellValue("G" . $row, $v["rejCount"]);
      $sheet->setCellValue("H" . $row, $v["rejMoney"]);
      $sheet->setCellValue("I" . $row, $v["c"]);
      $sheet->setCellValue("J" . $row, $v["m"]);
      $sheet->setCellValue("K" . $row, $v["profit"]);
      $sheet->setCellValue("L" . $row, $v["rate"]);
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
    $sheet->getStyle('A2:L' . $lastRow)->applyFromArray($styleArray);

    $dt = date("YmdHis");

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="销售月报表(按商品汇总)_' . $dt . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = \PHPExcel_IOFactory::createWriter($excel, "Excel2007");
    $writer->save("php://output");
  }

  /**
   * 销售月报表(按客户汇总) - 查询数据
   */
  public function saleMonthByCustomerQueryData($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new SaleReportDAO($this->db());
    return $dao->saleMonthByCustomerQueryData($params);
  }

  /**
   * 销售月报表(按客户汇总) - 查询汇总数据
   */
  public function saleMonthByCustomerSummaryQueryData($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    return $this->saleMonthSummaryQueryData($params);
  }

  /**
   * 销售月报表(按客户汇总) - 查询数据，用于Lodop打印
   *
   * @param array $params
   * @return array
   */
  public function getSaleMonthByCustomerDataForLodopPrint($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $year = $params["year"];
    $month = $params["month"];

    $bizDT = "";
    if ($month < 10) {
      $bizDT = "$year-0$month";
    } else {
      $bizDT = "$year-$month";
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new SaleReportDAO($this->db());
    $items = $dao->saleMonthByCustomerQueryData($params);

    $data = $this->saleMonthSummaryQueryData($params);
    $v = $data[0];

    return [
      "bizDate" => $bizDT,
      "printDT" => date("Y-m-d H:i:s"),
      "saleMoney" => $v["saleMoney"],
      "rejMoney" => $v["rejMoney"],
      "m" => $v["m"],
      "profit" => $v["profit"],
      "rate" => $v["rate"],
      "items" => $items["dataList"]
    ];
  }

  /**
   * 销售月报表(按客户汇总) - 生成PDF文件
   *
   * @param array $params
   */
  public function saleMonthByCustomerPdf($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $params["companyId"] = $this->getCompanyId();

    $year = $params["year"];
    $month = $params["month"];

    $bizDT = "";
    if ($month < 10) {
      $bizDT = "$year-0$month";
    } else {
      $bizDT = "$year-$month";
    }

    $bs = new BizConfigService();
    $productionName = $bs->getProductionName();

    $dao = new SaleReportDAO($this->db());

    $data = $dao->saleMonthByCustomerQueryData($params);
    $items = $data["dataList"];

    $data = $this->saleMonthSummaryQueryData($params);
    $summary = $data[0];

    // 记录业务日志
    $log = "销售月报表(按客户汇总)导出PDF文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    ob_start();

    $ps = new PDFService();
    $pdf = $ps->getInstanceForReport();
    $pdf->SetTitle("销售月报表(按客户汇总)");

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

    $pdf->SetHeaderData("", 0, $productionName, "销售月报表(按客户汇总)");

    $pdf->SetFont("stsongstdlight", "", 10);
    $pdf->AddPage();

    /**
     * 注意：
     * TCPDF中，用来拼接HTML的字符串需要用单引号，否则HTML中元素的属性就不会被解析
     */
    $html = '
				<table>
					<tr>
						<td>月份：' . $bizDT . '</td>
						<td>销售出库金额：' . $summary["saleMoney"] . '</td>
						<td>退回入库金额：' . $summary["rejMoney"] . '</td>
						<td>净销售金额：' . $summary["m"] . '</td>
					</tr>
					<tr>
						<td>毛利：' . $summary["profit"] . '</td>
						<td>毛利率：' . $summary["rate"] . '</td>
						<td></td>
						<td></td>
					</tr>
				</table>
				';
    $pdf->writeHTML($html);

    $html = '<table border="1" cellpadding="1">
					<tr><td>客户编号</td><td>客户名称</td>
						<td>销售出库金额</td><td>退货入库金额</td>
						<td>净销售金额</td><td>毛利</td><td>毛利率</td>
					</tr>
				';
    foreach ($items as $v) {
      $html .= '<tr>';
      $html .= '<td>' . $v["customerCode"] . '</td>';
      $html .= '<td>' . $v["customerName"] . '</td>';
      $html .= '<td align="right">' . $v["saleMoney"] . '</td>';
      $html .= '<td align="right">' . $v["rejMoney"] . '</td>';
      $html .= '<td align="right">' . $v["m"] . '</td>';
      $html .= '<td align="right">' . $v["profit"] . '</td>';
      $html .= '<td align="right">' . $v["rate"] . '</td>';
      $html .= '</tr>';
    }

    $html .= '</table>';
    $pdf->writeHTML($html, true, false, true, false, '');

    ob_end_clean();

    $dt = date("YmdHis");

    $pdf->Output("SMC_{$dt}.pdf", "I");
  }

  /**
   * 销售月报表(按客户汇总) - 生成Excel文件
   *
   * @param array $params
   */
  public function saleMonthByCustomerExcel($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $params["companyId"] = $this->getCompanyId();

    $year = $params["year"];
    $month = $params["month"];

    $bizDT = "";
    if ($month < 10) {
      $bizDT = "$year-0$month";
    } else {
      $bizDT = "$year-$month";
    }

    $dao = new SaleReportDAO($this->db());

    $data = $dao->saleMonthByCustomerQueryData($params);
    $items = $data["dataList"];

    $data = $this->saleMonthSummaryQueryData($params);
    $summary = $data[0];

    // 记录业务日志
    $log = "销售月报表(按客户汇总)导出Excel文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    $excel = new \PHPExcel();

    $sheet = $excel->getActiveSheet();
    if (!$sheet) {
      $sheet = $excel->createSheet();
    }

    $sheet->setTitle("销售月报表(按客户汇总)");

    $sheet->getRowDimension('1')->setRowHeight(22);
    $info = "月份: " . $bizDT . " 销售出库金额: " . $summary["saleMoney"] . " 退货入库金额: " . $summary["rejMoney"] . " 毛利: " . $summary["profit"] . " 毛利率: " . $summary["rate"];
    $sheet->setCellValue("A1", $info);

    $sheet->getColumnDimension('A')->setWidth(15);
    $sheet->setCellValue("A2", "客户编码");

    $sheet->getColumnDimension('B')->setWidth(40);
    $sheet->setCellValue("B2", "客户名称");

    $sheet->getColumnDimension('C')->setWidth(15);
    $sheet->setCellValue("C2", "销售出库金额");

    $sheet->getColumnDimension('D')->setWidth(15);
    $sheet->setCellValue("D2", "退货入库金额");

    $sheet->getColumnDimension('E')->setWidth(15);
    $sheet->setCellValue("E2", "净销售金额");

    $sheet->getColumnDimension('F')->setWidth(15);
    $sheet->setCellValue("F2", "毛利");

    $sheet->getColumnDimension('G')->setWidth(15);
    $sheet->setCellValue("G2", "毛利率");

    foreach ($items as $i => $v) {
      $row = $i + 3;
      $sheet->setCellValue("A" . $row, $v["customerCode"]);
      $sheet->setCellValue("B" . $row, $v["customerName"]);
      $sheet->setCellValue("C" . $row, $v["saleMoney"]);
      $sheet->setCellValue("D" . $row, $v["rejMoney"]);
      $sheet->setCellValue("E" . $row, $v["m"]);
      $sheet->setCellValue("F" . $row, $v["profit"]);
      $sheet->setCellValue("G" . $row, $v["rate"]);
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
    $sheet->getStyle('A2:G' . $lastRow)->applyFromArray($styleArray);

    $dt = date("YmdHis");

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="销售月报表(按客户汇总)_' . $dt . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = \PHPExcel_IOFactory::createWriter($excel, "Excel2007");
    $writer->save("php://output");
  }

  /**
   * 销售月报表(按仓库汇总) - 查询数据
   */
  public function saleMonthByWarehouseQueryData($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new SaleReportDAO($this->db());
    return $dao->saleMonthByWarehouseQueryData($params);
  }

  /**
   * 销售月报表(按仓库汇总) - 查询汇总数据
   */
  public function saleMonthByWarehouseSummaryQueryData($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    return $this->saleMonthSummaryQueryData($params);
  }

  /**
   * 销售月报表(按仓库汇总) - 查询数据，用于Lodop打印
   *
   * @param array $params
   * @return array
   */
  public function getSaleMonthByWarehouseDataForLodopPrint($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $year = $params["year"];
    $month = $params["month"];

    $bizDT = "";
    if ($month < 10) {
      $bizDT = "$year-0$month";
    } else {
      $bizDT = "$year-$month";
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new SaleReportDAO($this->db());
    $items = $dao->saleMonthByWarehouseQueryData($params);

    $data = $this->saleMonthSummaryQueryData($params);
    $v = $data[0];

    return [
      "bizDate" => $bizDT,
      "printDT" => date("Y-m-d H:i:s"),
      "saleMoney" => $v["saleMoney"],
      "rejMoney" => $v["rejMoney"],
      "m" => $v["m"],
      "profit" => $v["profit"],
      "rate" => $v["rate"],
      "items" => $items["dataList"]
    ];
  }

  /**
   * 销售月报表(按仓库汇总) - 生成PDF文件
   *
   * @param array $params
   */
  public function saleMonthByWarehousePdf($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $params["companyId"] = $this->getCompanyId();

    $year = $params["year"];
    $month = $params["month"];

    $bizDT = "";
    if ($month < 10) {
      $bizDT = "$year-0$month";
    } else {
      $bizDT = "$year-$month";
    }

    $bs = new BizConfigService();
    $productionName = $bs->getProductionName();

    $dao = new SaleReportDAO($this->db());

    $data = $dao->saleMonthByWarehouseQueryData($params);
    $items = $data["dataList"];

    $data = $this->saleMonthSummaryQueryData($params);
    $summary = $data[0];

    // 记录业务日志
    $log = "销售月报表(按仓库汇总)导出PDF文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    ob_start();

    $ps = new PDFService();
    $pdf = $ps->getInstanceForReport();
    $pdf->SetTitle("销售月报表(按仓库汇总)");

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

    $pdf->SetHeaderData("", 0, $productionName, "销售月报表(按仓库汇总)");

    $pdf->SetFont("stsongstdlight", "", 10);
    $pdf->AddPage();

    /**
     * 注意：
     * TCPDF中，用来拼接HTML的字符串需要用单引号，否则HTML中元素的属性就不会被解析
     */
    $html = '
				<table>
					<tr>
						<td>月份：' . $bizDT . '</td>
						<td>销售出库金额：' . $summary["saleMoney"] . '</td>
						<td>退回入库金额：' . $summary["rejMoney"] . '</td>
						<td>净销售金额：' . $summary["m"] . '</td>
					</tr>
					<tr>
						<td>毛利：' . $summary["profit"] . '</td>
						<td>毛利率：' . $summary["rate"] . '</td>
						<td></td>
						<td></td>
					</tr>
				</table>
				';
    $pdf->writeHTML($html);

    $html = '<table border="1" cellpadding="1">
					<tr><td>仓库编码</td><td>仓库</td>
						<td>销售出库金额</td><td>退货入库金额</td>
						<td>净销售金额</td><td>毛利</td><td>毛利率</td>
						<td>每平方米销售额</td><td>仓库销售核算面积</td>
					</tr>
				';
    foreach ($items as $v) {
      $html .= '<tr>';
      $html .= '<td>' . $v["warehouseCode"] . '</td>';
      $html .= '<td>' . $v["warehouseName"] . '</td>';
      $html .= '<td align="right">' . $v["saleMoney"] . '</td>';
      $html .= '<td align="right">' . $v["rejMoney"] . '</td>';
      $html .= '<td align="right">' . $v["m"] . '</td>';
      $html .= '<td align="right">' . $v["profit"] . '</td>';
      $html .= '<td align="right">' . $v["rate"] . '</td>';
      $html .= '<td align="right">' . $v["saleAreaRate"] . '</td>';
      $html .= '<td align="right">' . $v["saleArea"] . '</td>';
      $html .= '</tr>';
    }

    $html .= '</table>';
    $pdf->writeHTML($html, true, false, true, false, '');

    ob_end_clean();

    $dt = date("YmdHis");

    $pdf->Output("SMW_{$dt}.pdf", "I");
  }

  /**
   * 销售月报表(按仓库汇总) - 生成Excel文件
   *
   * @param array $params
   */
  public function saleMonthByWarehouseExcel($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $params["companyId"] = $this->getCompanyId();

    $year = $params["year"];
    $month = $params["month"];

    $bizDT = "";
    if ($month < 10) {
      $bizDT = "$year-0$month";
    } else {
      $bizDT = "$year-$month";
    }

    $dao = new SaleReportDAO($this->db());

    $data = $dao->saleMonthByWarehouseQueryData($params);
    $items = $data["dataList"];

    $data = $this->saleMonthSummaryQueryData($params);
    $summary = $data[0];

    // 记录业务日志
    $log = "销售月报表(按仓库汇总)导出Excel文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    $excel = new \PHPExcel();

    $sheet = $excel->getActiveSheet();
    if (!$sheet) {
      $sheet = $excel->createSheet();
    }

    $sheet->setTitle("销售月报表(按仓库汇总)");

    $sheet->getRowDimension('1')->setRowHeight(22);
    $info = "月份: " . $bizDT . " 销售出库金额: " . $summary["saleMoney"] . " 退货入库金额: " . $summary["rejMoney"] . " 毛利: " . $summary["profit"] . " 毛利率: " . $summary["rate"];
    $sheet->setCellValue("A1", $info);

    $sheet->getColumnDimension('A')->setWidth(15);
    $sheet->setCellValue("A2", "仓库编码");

    $sheet->getColumnDimension('B')->setWidth(40);
    $sheet->setCellValue("B2", "仓库");

    $sheet->getColumnDimension('C')->setWidth(15);
    $sheet->setCellValue("C2", "销售出库金额");

    $sheet->getColumnDimension('D')->setWidth(15);
    $sheet->setCellValue("D2", "退货入库金额");

    $sheet->getColumnDimension('E')->setWidth(15);
    $sheet->setCellValue("E2", "净销售金额");

    $sheet->getColumnDimension('F')->setWidth(15);
    $sheet->setCellValue("F2", "毛利");

    $sheet->getColumnDimension('G')->setWidth(15);
    $sheet->setCellValue("G2", "毛利率");

    $sheet->getColumnDimension('H')->setWidth(20);
    $sheet->setCellValue("H2", "每平方米销售额");

    $sheet->getColumnDimension('I')->setWidth(20);
    $sheet->setCellValue("I2", "仓库销售核算面积");

    foreach ($items as $i => $v) {
      $row = $i + 3;
      $sheet->setCellValue("A" . $row, $v["warehouseCode"]);
      $sheet->setCellValue("B" . $row, $v["warehouseName"]);
      $sheet->setCellValue("C" . $row, $v["saleMoney"]);
      $sheet->setCellValue("D" . $row, $v["rejMoney"]);
      $sheet->setCellValue("E" . $row, $v["m"]);
      $sheet->setCellValue("F" . $row, $v["profit"]);
      $sheet->setCellValue("G" . $row, $v["rate"]);
      $sheet->setCellValue("H" . $row, $v["saleAreaRate"]);
      $sheet->setCellValue("I" . $row, $v["saleArea"]);
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
    $sheet->getStyle('A2:I' . $lastRow)->applyFromArray($styleArray);

    $dt = date("YmdHis");

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="销售月报表(按仓库汇总)_' . $dt . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = \PHPExcel_IOFactory::createWriter($excel, "Excel2007");
    $writer->save("php://output");
  }

  /**
   * 销售月报表(按业务员汇总) - 查询数据
   */
  public function saleMonthByBizuserQueryData($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new SaleReportDAO($this->db());
    return $dao->saleMonthByBizuserQueryData($params);
  }

  /**
   * 销售月报表(按业务员汇总) - 查询汇总数据
   */
  public function saleMonthByBizuserSummaryQueryData($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    return $this->saleMonthSummaryQueryData($params);
  }

  /**
   * 销售月报表(按业务员汇总) - 查询数据，用于Lodop打印
   *
   * @param array $params
   * @return array
   */
  public function getSaleMonthByBizuserDataForLodopPrint($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $year = $params["year"];
    $month = $params["month"];

    $bizDT = "";
    if ($month < 10) {
      $bizDT = "$year-0$month";
    } else {
      $bizDT = "$year-$month";
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new SaleReportDAO($this->db());
    $items = $dao->saleMonthByBizuserQueryData($params);

    $data = $this->saleMonthSummaryQueryData($params);
    $v = $data[0];

    return [
      "bizDate" => $bizDT,
      "printDT" => date("Y-m-d H:i:s"),
      "saleMoney" => $v["saleMoney"],
      "rejMoney" => $v["rejMoney"],
      "m" => $v["m"],
      "profit" => $v["profit"],
      "rate" => $v["rate"],
      "items" => $items["dataList"]
    ];
  }

  /**
   * 销售月报表(按业务员汇总) - 生成PDF文件
   *
   * @param array $params
   */
  public function saleMonthByBizuserPdf($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $params["companyId"] = $this->getCompanyId();

    $year = $params["year"];
    $month = $params["month"];

    $bizDT = "";
    if ($month < 10) {
      $bizDT = "$year-0$month";
    } else {
      $bizDT = "$year-$month";
    }

    $bs = new BizConfigService();
    $productionName = $bs->getProductionName();

    $dao = new SaleReportDAO($this->db());

    $data = $dao->saleMonthByBizuserQueryData($params);
    $items = $data["dataList"];

    $data = $this->saleMonthSummaryQueryData($params);
    $summary = $data[0];

    // 记录业务日志
    $log = "销售月报表(按业务员汇总)导出PDF文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    ob_start();

    $ps = new PDFService();
    $pdf = $ps->getInstanceForReport();
    $pdf->SetTitle("销售月报表(按业务员汇总)");

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

    $pdf->SetHeaderData("", 0, $productionName, "销售月报表(按业务员汇总)");

    $pdf->SetFont("stsongstdlight", "", 10);
    $pdf->AddPage();

    /**
     * 注意：
     * TCPDF中，用来拼接HTML的字符串需要用单引号，否则HTML中元素的属性就不会被解析
     */
    $html = '
				<table>
					<tr>
						<td>月份：' . $bizDT . '</td>
						<td>销售出库金额：' . $summary["saleMoney"] . '</td>
						<td>退回入库金额：' . $summary["rejMoney"] . '</td>
						<td>净销售金额：' . $summary["m"] . '</td>
					</tr>
					<tr>
						<td>毛利：' . $summary["profit"] . '</td>
						<td>毛利率：' . $summary["rate"] . '</td>
						<td></td>
						<td></td>
					</tr>
				</table>
				';
    $pdf->writeHTML($html);

    $html = '<table border="1" cellpadding="1">
					<tr><td>业务员编码</td><td>业务员</td>
						<td>销售出库金额</td><td>退货入库金额</td>
						<td>净销售金额</td><td>毛利</td><td>毛利率</td>
					</tr>
				';
    foreach ($items as $v) {
      $html .= '<tr>';
      $html .= '<td>' . $v["userCode"] . '</td>';
      $html .= '<td>' . $v["userName"] . '</td>';
      $html .= '<td align="right">' . $v["saleMoney"] . '</td>';
      $html .= '<td align="right">' . $v["rejMoney"] . '</td>';
      $html .= '<td align="right">' . $v["m"] . '</td>';
      $html .= '<td align="right">' . $v["profit"] . '</td>';
      $html .= '<td align="right">' . $v["rate"] . '</td>';
      $html .= '</tr>';
    }

    $html .= '</table>';
    $pdf->writeHTML($html, true, false, true, false, '');

    ob_end_clean();

    $dt = date("YmdHis");

    $pdf->Output("SMU_{$dt}.pdf", "I");
  }

  /**
   * 销售月报表(按业务员汇总) - 生成Excel文件
   *
   * @param array $params
   */
  public function saleMonthByBizuserExcel($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $params["companyId"] = $this->getCompanyId();

    $year = $params["year"];
    $month = $params["month"];

    $bizDT = "";
    if ($month < 10) {
      $bizDT = "$year-0$month";
    } else {
      $bizDT = "$year-$month";
    }

    $dao = new SaleReportDAO($this->db());

    $data = $dao->saleMonthByBizuserQueryData($params);
    $items = $data["dataList"];

    $data = $this->saleMonthSummaryQueryData($params);
    $summary = $data[0];

    // 记录业务日志
    $log = "销售月报表(按业务员汇总)导出Excel文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    $excel = new \PHPExcel();

    $sheet = $excel->getActiveSheet();
    if (!$sheet) {
      $sheet = $excel->createSheet();
    }

    $sheet->setTitle("销售月报表(按业务员汇总)");

    $sheet->getRowDimension('1')->setRowHeight(22);
    $info = "月份: " . $bizDT . " 销售出库金额: " . $summary["saleMoney"] . " 退货入库金额: " . $summary["rejMoney"] . " 毛利: " . $summary["profit"] . " 毛利率: " . $summary["rate"];
    $sheet->setCellValue("A1", $info);

    $sheet->getColumnDimension('A')->setWidth(15);
    $sheet->setCellValue("A2", "业务员编码");

    $sheet->getColumnDimension('B')->setWidth(40);
    $sheet->setCellValue("B2", "业务员");

    $sheet->getColumnDimension('C')->setWidth(15);
    $sheet->setCellValue("C2", "销售出库金额");

    $sheet->getColumnDimension('D')->setWidth(15);
    $sheet->setCellValue("D2", "退货入库金额");

    $sheet->getColumnDimension('E')->setWidth(15);
    $sheet->setCellValue("E2", "净销售金额");

    $sheet->getColumnDimension('F')->setWidth(15);
    $sheet->setCellValue("F2", "毛利");

    $sheet->getColumnDimension('G')->setWidth(15);
    $sheet->setCellValue("G2", "毛利率");

    foreach ($items as $i => $v) {
      $row = $i + 3;
      $sheet->setCellValue("A" . $row, $v["userCode"]);
      $sheet->setCellValue("B" . $row, $v["userName"]);
      $sheet->setCellValue("C" . $row, $v["saleMoney"]);
      $sheet->setCellValue("D" . $row, $v["rejMoney"]);
      $sheet->setCellValue("E" . $row, $v["m"]);
      $sheet->setCellValue("F" . $row, $v["profit"]);
      $sheet->setCellValue("G" . $row, $v["rate"]);
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
    $sheet->getStyle('A2:G' . $lastRow)->applyFromArray($styleArray);

    $dt = date("YmdHis");

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="销售月报表(按业务员汇总)_' . $dt . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = \PHPExcel_IOFactory::createWriter($excel, "Excel2007");
    $writer->save("php://output");
  }

  /**
   * 销售出库明细表 - 查询数据
   */
  public function saleDetailQueryData($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();
    $params["loginUserId"] = $this->getLoginUserId();

    $dao = new SaleReportDAO($this->db());

    return $dao->saleDetailQueryData($params);
  }

  /**
   * 销售出库明细表 - 导出Excel
   */
  public function saleDetailExcel($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new SaleReportDAO($this->db());
    $data = $dao->saleDetailQueryData($params);

    $items = $data["dataList"];

    // 记录业务日志
    $log = "销售出库明细表导出Excel文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    $excel = new \PHPExcel();

    $sheet = $excel->getActiveSheet();
    if (!$sheet) {
      $sheet = $excel->createSheet();
    }

    $sheet->setTitle("销售出库明细表");

    $sheet->getRowDimension('1')->setRowHeight(22);

    $sheet->getColumnDimension('A')->setWidth(15);
    $sheet->setCellValue("A2", "销售订单号");

    $sheet->getColumnDimension('B')->setWidth(15);
    $sheet->setCellValue("B2", "出库单单号");

    $sheet->getColumnDimension('C')->setWidth(15);
    $sheet->setCellValue("C2", "出库单业务日期");

    $sheet->getColumnDimension('D')->setWidth(40);
    $sheet->setCellValue("D2", "出库仓库");

    $sheet->getColumnDimension('E')->setWidth(40);
    $sheet->setCellValue("E2", "客户");

    $sheet->getColumnDimension('F')->setWidth(15);
    $sheet->setCellValue("F2", "商品编码");

    $sheet->getColumnDimension('G')->setWidth(15);
    $sheet->setCellValue("G2", "商品名称");

    $sheet->getColumnDimension('H')->setWidth(15);
    $sheet->setCellValue("H2", "规格型号");

    $sheet->getColumnDimension('I')->setWidth(15);
    $sheet->setCellValue("I2", "出库数量");

    $sheet->getColumnDimension('J')->setWidth(15);
    $sheet->setCellValue("J2", "单位");

    $sheet->getColumnDimension('K')->setWidth(15);
    $sheet->setCellValue("K2", "销售单价");

    $sheet->getColumnDimension('L')->setWidth(15);
    $sheet->setCellValue("L2", "销售金额");

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
      $sheet->setCellValue("A" . $row, $v["soBillRef"]);
      $sheet->setCellValue("B" . $row, $v["wsBillRef"]);
      $sheet->setCellValue("C" . $row, $v["bizDate"]);
      $sheet->setCellValue("D" . $row, $v["warehouseName"]);
      $sheet->setCellValue("E" . $row, $v["customerName"]);
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
    header('Content-Disposition: attachment;filename="销售出库明细表_' . $dt . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = \PHPExcel_IOFactory::createWriter($excel, "Excel2007");
    $writer->save("php://output");
  }

  /**
   * 销售出库明细表 - 导出PDF
   */
  public function saleDetailPdf($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    ini_set('max_execution_time', 300); // 300 seconds = 5 minutes

    $bs = new BizConfigService();
    $productionName = $bs->getProductionName();

    $params["companyId"] = $this->getCompanyId();

    $dao = new SaleReportDAO($this->db());
    $data = $dao->saleDetailQueryData($params);

    $items = $data["dataList"];

    // 记录业务日志
    $log = "销售出库明细表导出PDF文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    ob_start();

    $ps = new PDFService();
    $pdf = $ps->getInstanceForReport();
    $pdf->SetTitle("销售出库明细表");

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

    $pdf->SetHeaderData("", 0, $productionName, "销售出库明细表");

    $pdf->SetFont("stsongstdlight", "", 10);
    $pdf->AddPage();

    /**
     * 注意：
     * TCPDF中，用来拼接HTML的字符串需要用单引号，否则HTML中元素的属性就不会被解析
     */
    $html = '<table border="1" cellpadding="1">
					<tr><td>销售订单号</td><td>出库单单号</td>
            <td>出库单业务日期</td>
            <td>出库仓库</td><td>客户</td>
            <td>商品编码</td><td>商品名称</td>
						<td>规格型号</td><td>出库数量</td><td>单位</td>
            <td>销售单价</td><td>销售金额</td>
            <td>税率(%)</td><td>税金</td>
            <td>价税合计</td><td>含税价</td><td>备注</td>
					</tr>
				';
    foreach ($items as $v) {
      $html .= '<tr>';
      $html .= '<td>' . $v["soBillRef"] . '</td>';
      $html .= '<td>' . $v["wsBillRef"] . '</td>';
      $html .= '<td>' . $v["bizDate"] . '</td>';
      $html .= '<td>' . $v["warehouseName"] . '</td>';
      $html .= '<td>' . $v["customerName"] . '</td>';
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

    $pdf->Output("SD_{$dt}.pdf", "I");
  }
}
