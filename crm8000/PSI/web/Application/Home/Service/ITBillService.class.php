<?php

namespace Home\Service;

use Home\DAO\ITBillDAO;

/**
 * 库间调拨Service
 *
 * @author 李静波
 */
class ITBillService extends PSIBaseExService
{
  private $LOG_CATEGORY = "库间调拨";

  /**
   * 调拨单主表列表信息
   */
  public function itbillList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();

    $dao = new ITBillDAO($this->db());
    return $dao->itbillList($params);
  }

  /**
   * 新建或编辑调拨单
   */
  public function editITBill($params)
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

    $dao = new ITBillDAO($db);

    $id = $bill["id"];

    $log = null;

    $bill["loginUserId"] = $this->getLoginUserId();
    $bill["companyId"] = $this->getCompanyId();

    if ($id) {
      // 编辑

      $rc = $dao->updateITBill($bill);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $ref = $bill["ref"];

      $log = "编辑调拨单，单号：$ref";
    } else {
      // 新建调拨单

      $bill["dataOrg"] = $this->getLoginUserDataOrg();

      $rc = $dao->addITBill($bill);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $id = $bill["id"];
      $ref = $bill["ref"];
      $log = "新建调拨单，单号：$ref";
    }

    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 查询某个调拨单的详情
   */
  public function itBillInfo($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();
    $params["loginUserName"] = $this->getLoginUserName();
    $params["companyId"] = $this->getCompanyId();

    $dao = new ITBillDAO($this->db());
    return $dao->itBillInfo($params);
  }

  /**
   * 调拨单的明细记录
   */
  public function itBillDetailList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new ITBillDAO($this->db());
    return $dao->itBillDetailList($params);
  }

  /**
   * 删除调拨单
   */
  public function deleteITBill($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new ITBillDAO($db);

    $rc = $dao->deleteITBill($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $ref = $params["ref"];

    $bs = new BizlogService($db);
    $log = "删除调拨单，单号：$ref";
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 提交调拨单
   */
  public function commitITBill($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $id = $params["id"];

    $db = $this->db();
    $db->startTrans();

    $params["companyId"] = $this->getCompanyId();

    $dao = new ITBillDAO($db);
    $rc = $dao->commitITBill($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $ref = $params["ref"];

    // 记录业务日志
    $bs = new BizlogService($db);
    $log = "提交调拨单，单号: $ref";
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 调拨单生成pdf文件
   */
  public function pdf($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $bs = new BizConfigService();
    $productionName = $bs->getProductionName();

    $ref = $params["ref"];

    $dao = new ITBillDAO($this->db());

    $bill = $dao->getDataForPDF($params);
    if (!$bill) {
      return;
    }

    // 记录业务日志
    $log = "调拨单(单号：$ref)生成PDF文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    ob_start();

    $ps = new PDFService();
    $pdf = $ps->getInstance();
    $pdf->SetTitle("调拨单，单号：{$ref}");

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

    $pdf->SetHeaderData("", 0, $productionName, "调拨单");

    $pdf->SetFont("stsongstdlight", "", 10);
    $pdf->AddPage();

    /**
     * 注意：
     * TCPDF中，用来拼接HTML的字符串需要用单引号，否则HTML中元素的属性就不会被解析
     */
    $html = '
				<table>
					<tr><td colspan="2">单号：' . $ref . '</td></tr>
					<tr><td>调出仓库：' . $bill["fromWarehouseName"] . '</td><td>调入仓库:' . $bill["toWarehouseName"] . '</td></tr>
					<tr><td>业务员：' . $bill["bizUserName"] . '</td><td>业务日期：' . $bill["bizDT"] . '</td></tr>
				</table>
				';
    $pdf->writeHTML($html);

    $html = '<table border="1" cellpadding="1">
					<tr><td>物料编码</td><td>品名</td><td>规格型号</td><td>数量</td><td>单位</td>
					</tr>
				';
    foreach ($bill["items"] as $v) {
      $html .= '<tr>';
      $html .= '<td>' . $v["goodsCode"] . '</td>';
      $html .= '<td>' . $v["goodsName"] . '</td>';
      $html .= '<td>' . $v["goodsSpec"] . '</td>';
      $html .= '<td align="right">' . $v["goodsCount"] . '</td>';
      $html .= '<td>' . $v["unitName"] . '</td>';
      $html .= '</tr>';
    }

    $html .= "";

    $html .= '</table>';
    $pdf->writeHTML($html, true, false, true, false, '');

    ob_end_clean();

    $pdf->Output("$ref.pdf", "I");
  }

  /**
   * 生成打印调拨的数据
   *
   * @param array $params
   */
  public function getITBillDataForLodopPrint($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new ITBillDAO($this->db());
    return $dao->getITBillDataForLodopPrint($params);
  }
}
