<?php

namespace Home\Service;

use Home\DAO\SCBillDAO;
use PhpOffice\PhpWord\TemplateProcessor;

/**
 * 销售合同Service
 *
 * @author 李静波
 */
class SCBillService extends PSIBaseExService
{
  private $LOG_CATEGORY = "销售合同";

  /**
   * 获得销售合同主表信息列表
   *
   * @param array $params        	
   * @return array
   */
  public function scbillList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();
    $dao = new SCBillDAO($this->db());
    return $dao->scbillList($params);
  }

  /**
   * 销售合同详情
   */
  public function scBillInfo($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();
    $params["loginUserName"] = $this->getLoginUserName();
    $params["companyId"] = $this->getCompanyId();

    $dao = new SCBillDAO($this->db());
    return $dao->scBillInfo($params);
  }

  /**
   * 新增或编辑销售合同
   */
  public function editSCBill($json)
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

    $dao = new SCBillDAO($db);

    $id = $bill["id"];

    $log = null;

    $bill["companyId"] = $this->getCompanyId();

    if ($id) {
      // 编辑

      $bill["loginUserId"] = $this->getLoginUserId();

      $rc = $dao->updateSCBill($bill);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $ref = $bill["ref"];

      $log = "编辑销售合同，合同号：{$ref}";
    } else {
      // 新建销售订单

      $bill["loginUserId"] = $this->getLoginUserId();
      $bill["dataOrg"] = $this->getLoginUserDataOrg();

      $rc = $dao->addSCBill($bill);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $id = $bill["id"];
      $ref = $bill["ref"];

      $log = "新建销售合同，合同号：{$ref}";
    }

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 销售合同商品明细
   */
  public function scBillDetailList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new SCBillDAO($this->db());
    return $dao->scBillDetailList($params);
  }

  /**
   * 删除销售合同
   */
  public function deleteSCBill($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();

    $db->startTrans();

    $dao = new SCBillDAO($db);
    $rc = $dao->deleteSCBill($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $ref = $params["ref"];
    $log = "删除销售合同，合同号：{$ref}";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 审核销售合同
   */
  public function commitSCBill($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();

    $db->startTrans();

    $dao = new SCBillDAO($db);
    $rc = $dao->commitSCBill($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $ref = $params["ref"];
    $log = "审核销售合同，合同号：{$ref}";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 取消审核销售合同
   */
  public function cancelConfirmSCBill($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();

    $db->startTrans();

    $dao = new SCBillDAO($db);
    $rc = $dao->cancelConfirmSCBill($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $ref = $params["ref"];
    $log = "取消审核销售合同，合同号：{$ref}";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 销售合同生成pdf文件
   */
  public function pdf($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $params["companyId"] = $this->getCompanyId();

    $bs = new BizConfigService();
    $productionName = $bs->getProductionName();

    $ref = $params["ref"];

    $dao = new SCBillDAO($this->db());

    $bill = $dao->getDataForPDF($params);
    if (!$bill) {
      return;
    }

    // 记录业务日志
    $log = "销售合同(合同号：$ref)生成PDF文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    ob_start();

    $utileService = new UtilService();

    $ps = new PDFService();
    $pdf = $ps->getInstance();
    $pdf->SetTitle("销售合同，合同号：{$ref}");

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

    $pdf->SetHeaderData("", 0, $productionName, "销售合同");

    $pdf->SetFont("stsongstdlight", "", 10);
    $pdf->AddPage();

    /**
     * 注意：
     * TCPDF中，用来拼接HTML的字符串需要用单引号，否则HTML中元素的属性就不会被解析
     */
    $html = '
				<table>
					<tr><td>合同号：' . $ref . '</td><td>合同期限：' . $bill["beginDT"] . ' - ' . $bill["endDT"] . '</td></tr>
					<tr><td>甲方客户：' . $bill["customerName"] . '</td><td>乙方组织：' . $bill["orgName"] . '</td></tr>
					<tr><td>合同签订日期：' . $bill["bizDT"] . '</td><td>业务员：' . $bill["bizUserName"] . '</td></tr>
					<tr><td>交货日期：' . $bill["dealDate"] . '</td><td>交货地址:' . $bill["dealAddress"] . '</td></tr>
          <tr><td>合同金额：' . $bill["goodsMoney"] . ' (' . $utileService->moneyToCap($bill["goodsMoney"]) . ')</td>' .
      '<td>税金： ' . $bill["tax"] . ' (' . $utileService->moneyToCap($bill["tax"]) . ')</td></tr>
					<tr><td colspan="2">价税合计：' . $bill["moneyWithTax"] . ' (' . $utileService->moneyToCap($bill["moneyWithTax"]) . ')</td></tr>
					<tr><td colspan="2"></td></tr>
					<tr><td colspan="2">品质条款</td></tr>
					<tr><td colspan="2">' . $bill["qualityClause"] . '</td></tr>
					<tr><td colspan="2"></td></tr>
					<tr><td colspan="2">保险条款</td></tr>
					<tr><td colspan="2">' . $bill["insuranceClause"] . '</td></tr>
					<tr><td colspan="2"></td></tr>
					<tr><td colspan="2">运输条款</td></tr>
					<tr><td colspan="2">' . $bill["transportClause"] . '</td></tr>
					<tr><td colspan="2"></td></tr>
					<tr><td colspan="2">其他条款</td></tr>
					<tr><td colspan="2">' . $bill["otherClause"] . '</td></tr>
				</table>
				';
    $pdf->writeHTML($html);

    $html = '<table border="1" cellpadding="1">
					<tr><td>商品编号</td><td>商品名称</td><td>规格型号</td><td>数量</td><td>单位</td>
						<td>单价</td><td>销售金额</td><td>税率</td><td>价税合计</td>
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
      $html .= '<td align="right">' . intval($v["taxRate"]) . '%</td>';
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
   * 销售合同生成Word文件
   */
  public function word($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $params["companyId"] = $this->getCompanyId();

    $bs = new BizConfigService();
    $productionName = $bs->getProductionName();

    $ref = $params["ref"];

    $dao = new SCBillDAO($this->db());

    $bill = $dao->getDataForWord($params);
    if (!$bill) {
      die("销售合同不存在");
    }

    // 记录业务日志
    $log = "销售合同(合同号：$ref)生成Word文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    $path = __DIR__ . "/tpl/sc_template.docx";
    require_once __DIR__ . "/../Common/Word/vendor/autoload.php";
    $tp = new TemplateProcessor($path);

    // 设置成-1，用来处理大型Word
    ini_set('pcre.backtrack_limit', -1);

    // 主表
    $ref = $bill["ref"];
    $tp->setValue("ref", $ref);
    $tp->setValue("a", $bill["customerName"]);
    $tp->setValue("b", $bill["orgName"]);
    $tp->setValue("totalMoneyWithTax", $bill["moneyWithTax"]);

    // 明细表
    $items = $bill["items"];
    $detail = [];
    foreach ($items as $v) {
      $detail[] = [
        "d1" => $v["goodsCode"],
        "d2" => $v["goodsName"],
        "d3" => $v["goodsSpec"],
        "d4" => $v["goodsCount"],
        "d5" => $v["unitName"],
        "d6" => $v["moneyWithTax"],
      ];
    }
    $tp->cloneRowAndSetValues("d2", $detail);

    $dt = date("YmdHis");
    $path = __DIR__ . "/tpl/";
    if (!is_dir($path)) {
      die("tpl目录不存在");
    }
    $fileName = $path . "/sc_{$dt}.docx";
    $tp->saveAs($fileName);

    (new UtilService())->downloadFile($fileName, "销售合同_{$ref}.docx");

    // 删除临时文件
    unlink($fileName);
  }

  /**
   * 查询销售合同的数据，用于Lodop打印
   *
   * @param array $params        	
   */
  public function getSCBillDataForLodopPrint($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new SCBillDAO($this->db());
    return $dao->getSCBillDataForLodopPrint($params);
  }
}
