<?php

namespace Home\Service;

use Home\DAO\SRBillDAO;

/**
 * 销售退货入库单Service
 *
 * @author 李静波
 */
class SRBillService extends PSIBaseExService
{
  private $LOG_CATEGORY = "销售退货入库";

  /**
   * 销售退货入库单主表信息列表
   */
  public function srbillList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();

    $dao = new SRBillDAO($this->db());
    return $dao->srbillList($params);
  }

  /**
   * 销售退货入库单明细信息列表
   */
  public function srBillDetailList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new SRBillDAO($this->db());
    return $dao->srBillDetailList($params);
  }

  /**
   * 获得退货入库单单据数据
   */
  public function srBillInfo($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();
    $params["loginUserName"] = $this->getLoginUserName();
    $params["companyId"] = $this->getCompanyId();

    $dao = new SRBillDAO($this->db());
    return $dao->srBillInfo($params);
  }

  /**
   * 列出要选择的可以做退货入库的销售出库单
   */
  public function selectWSBillList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();

    $dao = new SRBillDAO($this->db());
    return $dao->selectWSBillList($params);
  }

  /**
   * 新增或编辑销售退货入库单
   */
  public function editSRBill($params)
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

    $dao = new SRBillDAO($db);

    $id = $bill["id"];

    $log = null;

    $bill["companyId"] = $this->getCompanyId();

    if ($id) {
      // 编辑

      $bill["loginUserId"] = $this->getLoginUserId();

      $rc = $dao->updateSRBill($bill);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $ref = $bill["ref"];
      $log = "编辑销售退货入库单，单号：{$ref}";
    } else {
      // 新增

      $bill["dataOrg"] = $this->getLoginUserDataOrg();
      $bill["loginUserId"] = $this->getLoginUserId();

      $rc = $dao->addSRBill($bill);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $id = $bill["id"];
      $ref = $bill["ref"];

      $log = "新建销售退货入库单，单号：{$ref}";
    }

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 获得销售出库单的信息
   */
  public function getWSBillInfoForSRBill($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new SRBillDAO($this->db());
    return $dao->getWSBillInfoForSRBill($params);
  }

  /**
   * 删除销售退货入库单
   */
  public function deleteSRBill($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new SRBillDAO($db);
    $rc = $dao->deleteSRBill($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $ref = $params["ref"];
    $bs = new BizlogService($db);
    $log = "删除销售退货入库单，单号：{$ref}";
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 提交销售退货入库单
   */
  public function commitSRBill($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $id = $params["id"];

    $db = $this->db();
    $db->startTrans();

    $params["loginUserId"] = $this->getLoginUserId();

    $dao = new SRBillDAO($db);
    $rc = $dao->commitSRBill($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    // 记录业务日志
    $ref = $params["ref"];
    $log = "提交销售退货入库单，单号：{$ref}";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 销售退货入库单生成pdf文件
   */
  public function pdf($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $bs = new BizConfigService();
    $productionName = $bs->getProductionName();

    $ref = $params["ref"];

    $dao = new SRBillDAO($this->db());

    $bill = $dao->getDataForPDF($params);
    if (!$bill) {
      return;
    }

    // 记录业务日志
    $log = "销售退货入库单(单号：$ref)生成PDF文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    ob_start();

    $utilService = new UtilService();

    $ps = new PDFService();
    $pdf = $ps->getInstance();
    $pdf->SetTitle("销售退货入库，单号：{$ref}");

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

    $pdf->SetHeaderData("", 0, $productionName, "销售退货入库单");

    $pdf->SetFont("stsongstdlight", "", 10);
    $pdf->AddPage();

    /**
     * 注意：
     * TCPDF中，用来拼接HTML的字符串需要用单引号，否则HTML中元素的属性就不会被解析
     */
    $html = '
				<table>
					<tr><td colspan="2">单号：' . $ref . '</td></tr>
					<tr><td colspan="2">客户：' . $bill["customerName"] . '</td></tr>
					<tr><td>业务日期：' . $bill["bizDT"] . '</td><td>入库仓库:' . $bill["warehouseName"] . '</td></tr>
					<tr><td>业务员：' . $bill["bizUserName"] . '</td><td></td></tr>
          <tr><td>退货金额：' . $bill["rejMoney"] . ' (' . $utilService->moneyToCap($bill["rejMoney"]) . ')</td>
              <td>价税合计：' . $bill["moneyWithTax"] . ' (' . $utilService->moneyToCap($bill["moneyWithTax"]) . ')</td>
          </tr>
				</table>
				';
    $pdf->writeHTML($html);

    $html = '<table border="1" cellpadding="1">
					<tr><td>商品编号</td><td>商品名称</td><td>规格型号</td><td>数量</td><td>单位</td>
						<td>单价</td><td>退货金额</td><td>序列号</td><td>税率</td><td>价税合计</td>
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
      $html .= '<td>' . $v["sn"] . '</td>';
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
   * 获得打印销售退货入库单的数据
   *
   * @param array $params
   */
  public function getSRBillDataForLodopPrint($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new SRBillDAO($this->db());
    return $dao->getSRBillDataForLodopPrint($params);
  }
}
