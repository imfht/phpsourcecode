<?php

namespace Home\Service;

use Home\DAO\WSBillDAO;

/**
 * 销售出库Service
 *
 * @author 李静波
 */
class WSBillService extends PSIBaseExService
{
  private $LOG_CATEGORY = "销售出库";

  /**
   * 新建或编辑的时候，获得销售出库单的详情
   */
  public function wsBillInfo($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();
    $params["loginUserName"] = $this->getLoginUserName();
    $params["companyId"] = $this->getCompanyId();

    $dao = new WSBillDAO($this->db());
    return $dao->wsBillInfo($params);
  }

  /**
   * 新增或编辑销售出库单
   */
  public function editWSBill($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $json = $params["jsonStr"];
    $bill = json_decode(html_entity_decode($json), true);
    if ($bill == null) {
      return $this->bad("传入的参数错误，不是正确的JSON格式");
    }

    $id = $bill["id"];

    $sobillRef = $bill["sobillRef"];

    $db = $this->db();
    $db->startTrans();

    $dao = new WSBillDAO($db);

    $log = null;

    $bill["companyId"] = $this->getCompanyId();

    $checkInv = $params["checkInv"] == "1";
    if ($checkInv) {
      // 检查库存数量是否够出库
      $rc = $dao->checkInv($bill);
      if ($rc) {
        $db->rollback();
        return $rc;
      }
    }

    if ($id) {
      // 编辑

      $bill["loginUserId"] = $this->getLoginUserId();

      $rc = $dao->updateWSBill($bill);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $ref = $bill["ref"];
      $log = "编辑销售出库单，单号 = {$ref}";
    } else {
      // 新建销售出库单

      $bill["dataOrg"] = $this->getLoginUserDataOrg();
      $bill["loginUserId"] = $this->getLoginUserId();

      $rc = $dao->addWSBill($bill);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $id = $bill["id"];
      $ref = $bill["ref"];
      if ($sobillRef) {
        // 从销售订单生成销售出库单
        $log = "从销售订单(单号：{$sobillRef})生成销售出库单: 单号 = {$ref}";
      } else {
        // 手工新建销售出库单
        $log = "新增销售出库单，单号 = {$ref}";
      }
    }

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 获得销售出库单主表列表
   */
  public function wsbillList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();

    $dao = new WSBillDAO($this->db());
    return $dao->wsbillList($params);
  }

  /**
   * 获得某个销售出库单的明细记录列表
   */
  public function wsBillDetailList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new WSBillDAO($this->db());
    return $dao->wsBillDetailList($params);
  }

  /**
   * 获得某个销售出库单的明细记录列表
   * 销售退货入库 - 选择销售出库单
   */
  public function wsBillDetailListForSRBill($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new WSBillDAO($this->db());
    return $dao->wsBillDetailListForSRBill($params);
  }

  /**
   * 删除销售出库单
   */
  public function deleteWSBill($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new WSBillDAO($db);
    $rc = $dao->deleteWSBill($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $ref = $params["ref"];
    $log = "删除销售出库单，单号: {$ref}";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 提交销售出库单
   */
  public function commitWSBill($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $id = $params["id"];

    $params["companyId"] = $this->getCompanyId();
    $params["loginUserId"] = $this->getLoginUserId();

    $db = $this->db();
    $db->startTrans();

    $dao = new WSBillDAO($db);
    $rc = $dao->commitWSBill($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $ref = $params["ref"];
    $log = "提交销售出库单，单号 = {$ref}";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 销售出库单生成pdf文件
   */
  public function pdf($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $bs = new BizConfigService();
    $productionName = $bs->getProductionName();

    $ref = $params["ref"];

    $dao = new WSBillDAO($this->db());

    $bill = $dao->getDataForPDF($params);
    if (!$bill) {
      return;
    }

    // 记录业务日志
    $log = "销售出库单(单号：$ref)生成PDF文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    ob_start();

    $utilService = new UtilService();

    $ps = new PDFService();
    $pdf = $ps->getInstance();
    $pdf->SetTitle("销售出库单，单号：{$ref}");

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

    $pdf->SetHeaderData("", 0, $productionName, "销售出库单");

    $pdf->SetFont("stsongstdlight", "", 10);
    $pdf->AddPage();

    /**
     * 注意：
     * TCPDF中，用来拼接HTML的字符串需要用单引号，否则HTML中元素的属性就不会被解析
     */
    $html = '
				<table>
					<tr><td>单号：' . $ref . '</td><td>业务日期：' . $bill["bizDT"] . '</td></tr>
					<tr><td colspan="2">客户：' . $bill["customerName"] . '</td></tr>
					<tr><td>业务员：' . $bill["bizUserName"] . '</td><td>出库仓库:' . $bill["warehouseName"] . '</td></tr>
          <tr><td>销售金额：' . $bill["saleMoney"] . ' (' . $utilService->moneyToCap($bill["saleMoney"]) . ')</td>'
      . '<td>价税合计：' . $bill["moneyWithTax"] . ' (' . $utilService->moneyToCap($bill["moneyWithTax"]) . ')</td></tr>
					<tr><td colspan="2">送货地址:' . $bill["dealAddress"] . '</td></tr>
					<tr><td colspan="2">备注:' . $bill["memo"] . '</td></tr>
				</table>
				';
    $pdf->writeHTML($html);

    $html = '<table border="1" cellpadding="1">
					<tr><td>商品编号</td><td>商品名称</td><td>规格型号</td><td>数量</td><td>单位</td>
						<td>单价</td><td>销售金额</td><td>税率</td><td>价税合计</td><td>序列号</td>
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
      $html .= '<td>' . $v["sn"] . '</td>';
      $html .= '</tr>';
    }

    $html .= "";

    $html .= '</table>';
    $pdf->writeHTML($html, true, false, true, false, '');

    ob_end_clean();

    $pdf->Output("$ref.pdf", "I");
  }

  /**
   * 根据销售订单id查询出库情况
   *
   * @param string $soBillId
   *        	销售订单id
   * @return array
   */
  public function soBillWSBillList($soBillId)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new WSBillDAO($this->db());
    return $dao->soBillWSBillList($soBillId);
  }

  /**
   * 获得打印销售出库单的数据
   *
   * @param array $params        	
   * @return array
   */
  public function getWSBillDataForLodopPrint($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new WSBillDAO($this->db());
    return $dao->getWSBillDataForLodopPrint($params);
  }
}
