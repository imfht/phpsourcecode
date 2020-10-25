<?php

namespace Home\Service;

/**
 * 应收账款报表Service
 *
 * @author 李静波
 */
class ReceivablesReportService extends PSIBaseExService
{
  private $LOG_CATEGORY = "应收账款报表";

  /**
   * 应收账款账龄分析
   */
  public function receivablesAgeQueryData($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $start = $params["start"];
    $limit = intval($params["limit"]);
    $showAllData = $limit == -1;

    $result = array();

    $us = new UserService();
    $companyId = $us->getCompanyId();

    $db = M();
    $sql = "select t.ca_type, t.id, t.code, t.name, t.balance_money
				from (
					select r.ca_type, c.id, c.code, c.name, r.balance_money
					from t_receivables r, t_customer c
					where r.ca_id = c.id and r.ca_type = 'customer'
						and r.company_id = '%s'
					union
					select r.ca_type, s.id, s.code, s.name, r.balance_money
					from t_receivables r, t_supplier s
					where r.ca_id = s.id and r.ca_type = 'supplier'
						and r.company_id = '%s'
				) t
				order by t.ca_type, t.code ";
    if (!$showAllData) {
      $sql .= "limit %d, %d";
    }
    $data = $showAllData ? $db->query($sql, $companyId, $companyId) : $db->query(
      $sql,
      $companyId,
      $companyId,
      $start,
      $limit
    );

    foreach ($data as $i => $v) {
      $caType = $v["ca_type"];
      $result[$i]["caType"] = $caType == "customer" ? "客户" : "供应商";
      $caId = $v["id"];
      $result[$i]["caCode"] = $v["code"];
      $result[$i]["caName"] = $v["name"];
      $result[$i]["balanceMoney"] = $v["balance_money"];

      // 账龄30天内
      $sql = "select sum(balance_money) as balance_money
					from t_receivables_detail
					where ca_type = '%s' and ca_id = '%s'
						and datediff(current_date(), biz_date) < 30
						and company_id = '%s'
					";
      $data = $db->query($sql, $caType, $caId, $companyId);
      $bm = $data[0]["balance_money"];
      if (!$bm) {
        $bm = 0;
      }
      $result[$i]["money30"] = $bm;

      // 账龄30-60天
      $sql = "select sum(balance_money) as balance_money
					from t_receivables_detail
					where ca_type = '%s' and ca_id = '%s'
						and datediff(current_date(), biz_date) <= 60
						and datediff(current_date(), biz_date) >= 30
						and company_id = '%s'
					";
      $data = $db->query($sql, $caType, $caId, $companyId);
      $bm = $data[0]["balance_money"];
      if (!$bm) {
        $bm = 0;
      }
      $result[$i]["money30to60"] = $bm;

      // 账龄60-90天
      $sql = "select sum(balance_money) as balance_money
					from t_receivables_detail
					where ca_type = '%s' and ca_id = '%s'
						and datediff(current_date(), biz_date) <= 90
						and datediff(current_date(), biz_date) > 60
						and company_id = '%s'
					";
      $data = $db->query($sql, $caType, $caId, $companyId);
      $bm = $data[0]["balance_money"];
      if (!$bm) {
        $bm = 0;
      }
      $result[$i]["money60to90"] = $bm;

      // 账龄90天以上
      $sql = "select sum(balance_money) as balance_money
					from t_receivables_detail
					where ca_type = '%s' and ca_id = '%s'
						and datediff(current_date(), biz_date) > 90
						and company_id = '%s'
					";
      $data = $db->query($sql, $caType, $caId, $companyId);
      $bm = $data[0]["balance_money"];
      if (!$bm) {
        $bm = 0;
      }
      $result[$i]["money90"] = $bm;
    }

    $sql = "select count(*) as cnt
				from t_receivables
				where company_id = '%s'
				";
    $data = $db->query($sql, $companyId);
    $cnt = $data[0]["cnt"];

    return array(
      "dataList" => $result,
      "totalCount" => $cnt
    );
  }

  public function receivablesSummaryQueryData()
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $db = M();
    $result = array();
    $us = new UserService();
    $companyId = $us->getCompanyId();

    $sql = "select sum(balance_money) as balance_money
				from t_receivables
				where company_id = '%s' ";
    $data = $db->query($sql, $companyId);
    $balance = $data[0]["balance_money"];
    if (!$balance) {
      $balance = 0;
    }
    $result[0]["balanceMoney"] = $balance;

    // 账龄30天内
    $sql = "select sum(balance_money) as balance_money
				from t_receivables_detail
				where datediff(current_date(), biz_date) < 30
					and company_id = '%s' ";
    $data = $db->query($sql, $companyId);
    $balance = $data[0]["balance_money"];
    if (!$balance) {
      $balance = 0;
    }
    $result[0]["money30"] = $balance;

    // 账龄30-60天
    $sql = "select sum(balance_money) as balance_money
				from t_receivables_detail
				where datediff(current_date(), biz_date) <= 60
					and datediff(current_date(), biz_date) >= 30
					and company_id = '%s' ";
    $data = $db->query($sql, $companyId);
    $balance = $data[0]["balance_money"];
    if (!$balance) {
      $balance = 0;
    }
    $result[0]["money30to60"] = $balance;

    // 账龄60-90天
    $sql = "select sum(balance_money) as balance_money
				from t_receivables_detail
				where datediff(current_date(), biz_date) <= 90
					and datediff(current_date(), biz_date) > 60
					and company_id = '%s' ";
    $data = $db->query($sql, $companyId);
    $balance = $data[0]["balance_money"];
    if (!$balance) {
      $balance = 0;
    }
    $result[0]["money60to90"] = $balance;

    // 账龄大于90天
    $sql = "select sum(balance_money) as balance_money
				from t_receivables_detail
				where datediff(current_date(), biz_date) > 90
					and company_id = '%s' ";
    $data = $db->query($sql, $companyId);
    $balance = $data[0]["balance_money"];
    if (!$balance) {
      $balance = 0;
    }
    $result[0]["money90"] = $balance;

    return $result;
  }

  /**
   * 应收账款账龄分析表 - 查询数据，用于Lodop打印
   *
   * @param array $params
   */
  public function getReceivablesAgeDataForLodopPrint($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $items = $this->receivablesAgeQueryData($params);

    $data = $this->receivablesSummaryQueryData();
    $v = $data[0];

    return [
      "balanceMoney" => $v["balanceMoney"],
      "money30" => $v["money30"],
      "money30to60" => $v["money30to60"],
      "money60to90" => $v["money60to90"],
      "money90" => $v["money90"],
      "printDT" => date("Y-m-d H:i:s"),
      "items" => $items["dataList"]
    ];
  }

  /**
   * 应收账款账龄分析 - 生成PDF文件
   *
   * @param array $params
   */
  public function receivablesAgePdf($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $params["companyId"] = $this->getCompanyId();

    $bs = new BizConfigService();
    $productionName = $bs->getProductionName();

    $data = $this->receivablesAgeQueryData($params);
    $items = $data["dataList"];

    $data = $this->receivablesSummaryQueryData();
    $summary = $data[0];

    // 记录业务日志
    $log = "应收账款账龄分析表导出PDF文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    ob_start();

    $ps = new PDFService();
    $pdf = $ps->getInstanceForReport();
    $pdf->SetTitle("应收账款账龄分析表");

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

    $pdf->SetHeaderData("", 0, $productionName, "应收账款账龄分析表");

    $pdf->SetFont("stsongstdlight", "", 10);
    $pdf->AddPage();

    /**
     * 注意：
     * TCPDF中，用来拼接HTML的字符串需要用单引号，否则HTML中元素的属性就不会被解析
     */
    $html = '
				<table>
					<tr>
						<td>当期余额：' . $summary["balanceMoney"] . '</td>
						<td>账龄30天内：' . $summary["money30"] . '</td>
						<td>账龄30-60天：' . $summary["money30to60"] . '</td>
						<td>账龄60-90天：' . $summary["money60to90"] . '</td>
					</tr>
					<tr>
						<td>账龄大于90天：' . $summary["money90"] . '</td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
				</table>
				';
    $pdf->writeHTML($html);

    $html = '<table border="1" cellpadding="1">
					<tr><td>往来单位性质</td><td>往来单位编码</td>
						<td>往来单位</td><td>当前余额</td>
						<td>账龄30天内</td><td>账龄30-60天</td>
						<td>账龄60-90天</td><td>账龄大于90天</td>
					</tr>
				';
    foreach ($items as $v) {
      $html .= '<tr>';
      $html .= '<td>' . $v["caType"] . '</td>';
      $html .= '<td>' . $v["caCode"] . '</td>';
      $html .= '<td>' . $v["caName"] . '</td>';
      $html .= '<td align="right">' . $v["balanceMoney"] . '</td>';
      $html .= '<td align="right">' . $v["money30"] . '</td>';
      $html .= '<td align="right">' . $v["money30to60"] . '</td>';
      $html .= '<td align="right">' . $v["money60to90"] . '</td>';
      $html .= '<td align="right">' . $v["money90"] . '</td>';
      $html .= '</tr>';
    }

    $html .= '</table>';
    $pdf->writeHTML($html, true, false, true, false, '');

    ob_end_clean();

    $dt = date("YmdHis");

    $pdf->Output("RA_{$dt}.pdf", "I");
  }

  /**
   * 应收账款账龄分析表 - 生成Excel文件
   *
   * @param array $params
   */
  public function receivablesAgeExcel($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $params["companyId"] = $this->getCompanyId();

    $data = $this->receivablesAgeQueryData($params);
    $items = $data["dataList"];

    $data = $this->receivablesSummaryQueryData();
    $summary = $data[0];

    // 记录业务日志
    $log = "应收账款账龄分析表导出Excel文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    $excel = new \PHPExcel();

    $sheet = $excel->getActiveSheet();
    if (!$sheet) {
      $sheet = $excel->createSheet();
    }

    $sheet->setTitle("应收账款账龄分析表");

    $sheet->getRowDimension('1')->setRowHeight(22);
    $info = "当前余额: " . $summary["balanceMoney"] . " 账龄30天内: " . $summary["money30"] . " 账龄30-60天: " . $summary["money30to60"] . " 账龄60-90天: " . $summary["money60to90"] . " 账龄大于90天: " . $summary["money90"];
    $sheet->setCellValue("A1", $info);

    $sheet->getColumnDimension('A')->setWidth(15);
    $sheet->setCellValue("A2", "往来单位性质");

    $sheet->getColumnDimension('B')->setWidth(15);
    $sheet->setCellValue("B2", "往来单位编码");

    $sheet->getColumnDimension('C')->setWidth(40);
    $sheet->setCellValue("C2", "往来单位");

    $sheet->getColumnDimension('D')->setWidth(15);
    $sheet->setCellValue("D2", "当前余额");

    $sheet->getColumnDimension('E')->setWidth(15);
    $sheet->setCellValue("E2", "账龄30天内");

    $sheet->getColumnDimension('F')->setWidth(15);
    $sheet->setCellValue("F2", "账龄30-60天");

    $sheet->getColumnDimension('G')->setWidth(15);
    $sheet->setCellValue("G2", "账龄60-90天");

    $sheet->getColumnDimension('H')->setWidth(15);
    $sheet->setCellValue("H2", "账龄大于90天");

    foreach ($items as $i => $v) {
      $row = $i + 3;
      $sheet->setCellValue("A" . $row, $v["caType"]);
      $sheet->setCellValue("B" . $row, $v["caCode"]);
      $sheet->setCellValue("C" . $row, $v["caName"]);
      $sheet->setCellValue("D" . $row, $v["balanceMoney"]);
      $sheet->setCellValue("E" . $row, $v["money30"]);
      $sheet->setCellValue("F" . $row, $v["money30to60"]);
      $sheet->setCellValue("G" . $row, $v["money60to90"]);
      $sheet->setCellValue("H" . $row, $v["money90"]);
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
    $sheet->getStyle('A2:H' . $lastRow)->applyFromArray($styleArray);

    $dt = date("YmdHis");

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="应收账款账龄分析表_' . $dt . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = \PHPExcel_IOFactory::createWriter($excel, "Excel2007");
    $writer->save("php://output");
  }
}
