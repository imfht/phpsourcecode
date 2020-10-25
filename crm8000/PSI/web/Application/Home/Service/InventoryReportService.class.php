<?php

namespace Home\Service;

use Home\Common\FIdConst;
use Home\DAO\BizConfigDAO;

/**
 * 库存报表Service
 *
 * @author 李静波
 */
class InventoryReportService extends PSIBaseExService
{
  private $LOG_CATEGORY = "库存报表";

  /**
   * 安全库存明细表 - 数据查询
   */
  public function safetyInventoryQueryData($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $start = $params["start"];
    $limit = intval($params["limit"]);
    $showAllData = $limit == -1;

    $result = array();

    $db = M();

    $companyId = $this->getCompanyId();
    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $ds = new DataOrgService();
    $rs = $ds->buildSQL(FIdConst::REPORT_SAFETY_INVENTORY, "w");
    $queryParams = array();

    $sql = "select w.code as warehouse_code, w.name as warehouse_name,
					g.code as goods_code, g.name as goods_name, g.spec as goods_spec,
					u.name as unit_name,
					convert(s.safety_inventory, $fmt) as safety_inventory, 
					convert(i.balance_count, $fmt) as balance_count
				from t_inventory i, t_goods g, t_goods_unit u, t_goods_si s, t_warehouse w
				where i.warehouse_id = w.id and i.goods_id = g.id and g.unit_id = u.id
					and s.warehouse_id = i.warehouse_id and s.goods_id = g.id
					and s.safety_inventory > i.balance_count ";
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = array_merge($queryParams, $rs[1]);
    }
    $sql .= " order by w.code, g.code ";
    if (!$showAllData) {
      $sql .= " limit %d, %d";
      $queryParams[] = $start;
      $queryParams[] = $limit;
    }

    $data = $db->query($sql, $queryParams);
    foreach ($data as $i => $v) {
      $result[$i]["warehouseCode"] = $v["warehouse_code"];
      $result[$i]["warehouseName"] = $v["warehouse_name"];
      $result[$i]["goodsCode"] = $v["goods_code"];
      $result[$i]["goodsName"] = $v["goods_name"];
      $result[$i]["goodsSpec"] = $v["goods_spec"];
      $result[$i]["unitName"] = $v["unit_name"];
      $result[$i]["siCount"] = $v["safety_inventory"];
      $result[$i]["invCount"] = $v["balance_count"];
      $result[$i]["delta"] = $v["safety_inventory"] - $v["balance_count"];
    }

    $sql = "select count(*) as cnt
				from t_inventory i, t_goods g, t_goods_unit u, t_goods_si s, t_warehouse w
				where i.warehouse_id = w.id and i.goods_id = g.id and g.unit_id = u.id
					and s.warehouse_id = i.warehouse_id and s.goods_id = g.id
					and s.safety_inventory > i.balance_count ";
    $queryParams = array();
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = array_merge($queryParams, $rs[1]);;
    }
    $data = $db->query($sql, $queryParams);
    $cnt = $data[0]["cnt"];

    return array(
      "dataList" => $result,
      "totalCount" => $cnt
    );
  }

  /**
   * 库存超上限明细表 - 数据查询
   */
  public function inventoryUpperQueryData($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $start = $params["start"];
    $limit = intval($params["limit"]);
    $showAllData = $limit == -1;

    $result = array();

    $companyId = $this->getCompanyId();

    $db = M();

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $ds = new DataOrgService();
    $rs = $ds->buildSQL(FIdConst::REPORT_INVENTORY_UPPER, "w");

    $sql = "select w.code as warehouse_code, w.name as warehouse_name,
					g.code as goods_code, g.name as goods_name, g.spec as goods_spec,
					u.name as unit_name,
					convert(s.inventory_upper, $fmt) as inventory_upper, 
					convert(i.balance_count, $fmt) as balance_count
				from t_inventory i, t_goods g, t_goods_unit u, t_goods_si s, t_warehouse w
				where i.warehouse_id = w.id and i.goods_id = g.id and g.unit_id = u.id
					and s.warehouse_id = i.warehouse_id and s.goods_id = g.id
					and s.inventory_upper < i.balance_count
					and s.inventory_upper <> 0 and s.inventory_upper is not null ";
    $queryParams = array();
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = array_merge($queryParams, $rs[1]);
    }
    $sql .= " order by w.code, g.code ";
    if (!$showAllData) {
      $sql .= " limit %d, %d";
      $queryParams[] = $start;
      $queryParams[] = $limit;
    }
    $data = $db->query($sql, $queryParams);
    foreach ($data as $i => $v) {
      $result[$i]["warehouseCode"] = $v["warehouse_code"];
      $result[$i]["warehouseName"] = $v["warehouse_name"];
      $result[$i]["goodsCode"] = $v["goods_code"];
      $result[$i]["goodsName"] = $v["goods_name"];
      $result[$i]["goodsSpec"] = $v["goods_spec"];
      $result[$i]["unitName"] = $v["unit_name"];
      $result[$i]["iuCount"] = $v["inventory_upper"];
      $result[$i]["invCount"] = $v["balance_count"];
      $result[$i]["delta"] = $v["balance_count"] - $v["inventory_upper"];
    }

    $sql = "select count(*) as cnt
				from t_inventory i, t_goods g, t_goods_unit u, t_goods_si s, t_warehouse w
				where i.warehouse_id = w.id and i.goods_id = g.id and g.unit_id = u.id
					and s.warehouse_id = i.warehouse_id and s.goods_id = g.id
					and s.inventory_upper < i.balance_count
					and s.inventory_upper <> 0 and s.inventory_upper is not null
				";
    $queryParams = array();
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = array_merge($queryParams, $rs[1]);
    }

    $data = $db->query($sql, $queryParams);
    $cnt = $data[0]["cnt"];

    return array(
      "dataList" => $result,
      "totalCount" => $cnt
    );
  }

  /**
   * 安全库存明细表 - 查询数据，用于Lodop打印
   *
   * @param array $params
   */
  public function getSafetyInventoryDataForLodopPrint($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $items = $this->safetyInventoryQueryData($params);

    return [
      "printDT" => date("Y-m-d H:i:s"),
      "items" => $items["dataList"]
    ];
  }

  /**
   * 安全库存明细表 - 生成PDF文件
   *
   * @param array $params
   */
  public function safetyInventoryPdf($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $params["companyId"] = $this->getCompanyId();

    $bs = new BizConfigService();
    $productionName = $bs->getProductionName();

    $data = $this->safetyInventoryQueryData($params);
    $items = $data["dataList"];

    // 记录业务日志
    $log = "安全库存明细表导出PDF文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    ob_start();

    $ps = new PDFService();
    $pdf = $ps->getInstanceForReport();
    $pdf->SetTitle("安全库存明细表");

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

    $pdf->SetHeaderData("", 0, $productionName, "安全库存明细表");

    $pdf->SetFont("stsongstdlight", "", 10);
    $pdf->AddPage();

    /**
     * 注意：
     * TCPDF中，用来拼接HTML的字符串需要用单引号，否则HTML中元素的属性就不会被解析
     */
    $html = '<table border="1" cellpadding="1">
					<tr><td>仓库编码</td><td>仓库</td>
						<td>物料编码</td><td>品名</td>
						<td>规格型号</td><td>安全库存</td><td>当前库存</td>
						<td>存货缺口</td><td>计量单位</td>
					</tr>
				';
    foreach ($items as $v) {
      $html .= '<tr>';
      $html .= '<td>' . $v["warehouseCode"] . '</td>';
      $html .= '<td>' . $v["warehouseName"] . '</td>';
      $html .= '<td>' . $v["goodsCode"] . '</td>';
      $html .= '<td>' . $v["goodsName"] . '</td>';
      $html .= '<td>' . $v["goodsSpec"] . '</td>';
      $html .= '<td align="right">' . $v["siCount"] . '</td>';
      $html .= '<td align="right">' . $v["invCount"] . '</td>';
      $html .= '<td align="right">' . $v["delta"] . '</td>';
      $html .= '<td>' . $v["unitName"] . '</td>';
      $html .= '</tr>';
    }

    $html .= '</table>';
    $pdf->writeHTML($html, true, false, true, false, '');

    ob_end_clean();

    $dt = date("YmdHis");

    $pdf->Output("SI_{$dt}.pdf", "I");
  }

  /**
   * 安全库存明细表 - 生成Excel文件
   *
   * @param array $params
   */
  public function safetyInventoryExcel($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $params["companyId"] = $this->getCompanyId();

    $data = $this->safetyInventoryQueryData($params);
    $items = $data["dataList"];

    // 记录业务日志
    $log = "安全库存明细表导出Excel文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    $excel = new \PHPExcel();

    $sheet = $excel->getActiveSheet();
    if (!$sheet) {
      $sheet = $excel->createSheet();
    }

    $sheet->setTitle("安全库存明细表");

    $sheet->getRowDimension('1')->setRowHeight(22);

    $sheet->getColumnDimension('A')->setWidth(15);
    $sheet->setCellValue("A2", "仓库编码");

    $sheet->getColumnDimension('B')->setWidth(40);
    $sheet->setCellValue("B2", "仓库");

    $sheet->getColumnDimension('C')->setWidth(15);
    $sheet->setCellValue("C2", "物料编码");

    $sheet->getColumnDimension('D')->setWidth(40);
    $sheet->setCellValue("D2", "品名");

    $sheet->getColumnDimension('E')->setWidth(40);
    $sheet->setCellValue("E2", "规格型号");

    $sheet->getColumnDimension('F')->setWidth(15);
    $sheet->setCellValue("F2", "安全库存");

    $sheet->getColumnDimension('G')->setWidth(15);
    $sheet->setCellValue("G2", "当前库存");

    $sheet->getColumnDimension('H')->setWidth(15);
    $sheet->setCellValue("H2", "存货缺口");

    $sheet->getColumnDimension('I')->setWidth(15);
    $sheet->setCellValue("I2", "计量单位");

    foreach ($items as $i => $v) {
      $row = $i + 3;
      $sheet->setCellValue("A" . $row, $v["warehouseCode"]);
      $sheet->setCellValue("B" . $row, $v["warehouseName"]);
      $sheet->setCellValue("C" . $row, $v["goodsCode"]);
      $sheet->setCellValue("D" . $row, $v["goodsName"]);
      $sheet->setCellValue("E" . $row, $v["goodsSpec"]);
      $sheet->setCellValue("F" . $row, $v["siCount"]);
      $sheet->setCellValue("G" . $row, $v["invCount"]);
      $sheet->setCellValue("H" . $row, $v["delta"]);
      $sheet->setCellValue("I" . $row, $v["unitName"]);
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
    header('Content-Disposition: attachment;filename="安全库存明细表_' . $dt . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = \PHPExcel_IOFactory::createWriter($excel, "Excel2007");
    $writer->save("php://output");
  }

  /**
   * 库存超上限明细表 - 查询数据，用于Lodop打印
   *
   * @param array $params
   */
  public function getInventoryUpperDataForLodopPrint($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $items = $this->inventoryUpperQueryData($params);

    return [
      "printDT" => date("Y-m-d H:i:s"),
      "items" => $items["dataList"]
    ];
  }

  /**
   * 库存超上限明细表 - 生成PDF文件
   *
   * @param array $params
   */
  public function inventoryUpperPdf($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $params["companyId"] = $this->getCompanyId();

    $bs = new BizConfigService();
    $productionName = $bs->getProductionName();

    $data = $this->inventoryUpperQueryData($params);
    $items = $data["dataList"];

    // 记录业务日志
    $log = "库存超上限明细表导出PDF文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    ob_start();

    $ps = new PDFService();
    $pdf = $ps->getInstanceForReport();
    $pdf->SetTitle("库存超上限明细表");

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

    $pdf->SetHeaderData("", 0, $productionName, "库存超上限明细表");

    $pdf->SetFont("stsongstdlight", "", 10);
    $pdf->AddPage();

    /**
     * 注意：
     * TCPDF中，用来拼接HTML的字符串需要用单引号，否则HTML中元素的属性就不会被解析
     */
    $html = '<table border="1" cellpadding="1">
					<tr><td>仓库编码</td><td>仓库</td>
						<td>物料编码</td><td>品名</td>
						<td>规格型号</td><td>库存上限</td><td>当前库存</td>
						<td>存货超量</td><td>计量单位</td>
					</tr>
				';
    foreach ($items as $v) {
      $html .= '<tr>';
      $html .= '<td>' . $v["warehouseCode"] . '</td>';
      $html .= '<td>' . $v["warehouseName"] . '</td>';
      $html .= '<td>' . $v["goodsCode"] . '</td>';
      $html .= '<td>' . $v["goodsName"] . '</td>';
      $html .= '<td>' . $v["goodsSpec"] . '</td>';
      $html .= '<td align="right">' . $v["iuCount"] . '</td>';
      $html .= '<td align="right">' . $v["invCount"] . '</td>';
      $html .= '<td align="right">' . $v["delta"] . '</td>';
      $html .= '<td>' . $v["unitName"] . '</td>';
      $html .= '</tr>';
    }

    $html .= '</table>';
    $pdf->writeHTML($html, true, false, true, false, '');

    ob_end_clean();

    $dt = date("YmdHis");

    $pdf->Output("IU_{$dt}.pdf", "I");
  }

  /**
   * 库存超上限明细表 - 生成Excel文件
   *
   * @param array $params
   */
  public function inventoryUpperExcel($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $params["companyId"] = $this->getCompanyId();

    $data = $this->inventoryUpperQueryData($params);
    $items = $data["dataList"];

    // 记录业务日志
    $log = "库存超上限明细表导出Excel文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    $excel = new \PHPExcel();

    $sheet = $excel->getActiveSheet();
    if (!$sheet) {
      $sheet = $excel->createSheet();
    }

    $sheet->setTitle("库存超上限明细表");

    $sheet->getRowDimension('1')->setRowHeight(22);

    $sheet->getColumnDimension('A')->setWidth(15);
    $sheet->setCellValue("A2", "仓库编码");

    $sheet->getColumnDimension('B')->setWidth(40);
    $sheet->setCellValue("B2", "仓库");

    $sheet->getColumnDimension('C')->setWidth(15);
    $sheet->setCellValue("C2", "物料编码");

    $sheet->getColumnDimension('D')->setWidth(40);
    $sheet->setCellValue("D2", "品名");

    $sheet->getColumnDimension('E')->setWidth(40);
    $sheet->setCellValue("E2", "规格型号");

    $sheet->getColumnDimension('F')->setWidth(15);
    $sheet->setCellValue("F2", "库存上限");

    $sheet->getColumnDimension('G')->setWidth(15);
    $sheet->setCellValue("G2", "当前库存");

    $sheet->getColumnDimension('H')->setWidth(15);
    $sheet->setCellValue("H2", "存货超量");

    $sheet->getColumnDimension('I')->setWidth(15);
    $sheet->setCellValue("I2", "计量单位");

    foreach ($items as $i => $v) {
      $row = $i + 3;
      $sheet->setCellValue("A" . $row, $v["warehouseCode"]);
      $sheet->setCellValue("B" . $row, $v["warehouseName"]);
      $sheet->setCellValue("C" . $row, $v["goodsCode"]);
      $sheet->setCellValue("D" . $row, $v["goodsName"]);
      $sheet->setCellValue("E" . $row, $v["goodsSpec"]);
      $sheet->setCellValue("F" . $row, $v["iuCount"]);
      $sheet->setCellValue("G" . $row, $v["invCount"]);
      $sheet->setCellValue("H" . $row, $v["delta"]);
      $sheet->setCellValue("I" . $row, $v["unitName"]);
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
    header('Content-Disposition: attachment;filename="库存超上限明细表_' . $dt . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = \PHPExcel_IOFactory::createWriter($excel, "Excel2007");
    $writer->save("php://output");
  }
}
