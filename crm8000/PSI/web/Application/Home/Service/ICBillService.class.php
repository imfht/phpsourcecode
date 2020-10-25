<?php

namespace Home\Service;

use Home\DAO\ICBillDAO;

/**
 * 库存盘点Service
 *
 * @author 李静波
 */
class ICBillService extends PSIBaseExService
{
  private $LOG_CATEGORY = "库存盘点";

  /**
   * 获得某个盘点单的详情
   */
  public function icBillInfo($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();
    $params["loginUserName"] = $this->getLoginUserName();
    $params["companyId"] = $this->getCompanyId();

    $dao = new ICBillDAO($this->db());

    return $dao->icBillInfo($params);
  }

  /**
   * 新建或编辑盘点单
   */
  public function editICBill($params)
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

    $dao = new ICBillDAO($db);

    $id = $bill["id"];

    $log = null;

    $bill["companyId"] = $this->getCompanyId();

    if ($id) {
      // 编辑单据

      $bill["loginUserId"] = $this->getLoginUserId();
      $rc = $dao->updateICBill($bill);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $ref = $bill["ref"];
      $log = "编辑盘点单，单号：$ref";
    } else {
      // 新建单据

      $bill["dataOrg"] = $this->getLoginUserDataOrg();
      $bill["loginUserId"] = $this->getLoginUserId();

      $rc = $dao->addICBill($bill);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $ref = $bill["ref"];
      $log = "新建盘点单，单号：$ref";
    }

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 盘点单列表
   */
  public function icbillList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();

    $dao = new ICBillDAO($this->db());
    return $dao->icbillList($params);
  }

  /**
   * 盘点单明细记录
   */
  public function icBillDetailList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new ICBillDAO($this->db());
    return $dao->icBillDetailList($params);
  }

  /**
   * 删除盘点单
   */
  public function deleteICBill($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new ICBillDAO($db);
    $rc = $dao->deleteICBill($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $bs = new BizlogService($db);

    $ref = $params["ref"];
    $log = "删除盘点单，单号：$ref";
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 提交盘点单
   */
  public function commitICBill($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $params["companyId"] = $this->getCompanyId();

    $dao = new ICBillDAO($db);
    $rc = $dao->commitICBill($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    // 记录业务日志
    $bs = new BizlogService($db);
    $ref = $params["ref"];
    $log = "提交盘点单，单号：$ref";
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    $id = $params["id"];
    return $this->ok($id);
  }

  /**
   * 盘点单生成pdf文件
   */
  public function pdf($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $bs = new BizConfigService();
    $productionName = $bs->getProductionName();

    $ref = $params["ref"];

    $dao = new ICBillDAO($this->db());

    $bill = $dao->getDataForPDF($params);
    if (!$bill) {
      return;
    }

    // 记录业务日志
    $log = "盘点单(单号：$ref)生成PDF文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    ob_start();

    $ps = new PDFService();
    $pdf = $ps->getInstance();
    $pdf->SetTitle("盘点单，单号：{$ref}");

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

    $pdf->SetHeaderData("", 0, $productionName, "盘点单");

    $pdf->SetFont("stsongstdlight", "", 10);
    $pdf->AddPage();

    /**
     * 注意：
     * TCPDF中，用来拼接HTML的字符串需要用单引号，否则HTML中元素的属性就不会被解析
     */
    $html = '
				<table>
					<tr><td colspan="2">单号：' . $ref . '</td></tr>
					<tr><td>盘点仓库：' . $bill["warehouseName"] . '</td><td></td></tr>
					<tr><td>业务员：' . $bill["bizUserName"] . '</td><td>业务日期：' . $bill["bizDT"] . '</td></tr>
					<tr><td colspan="2">备注：' . $bill["billMemo"] . '</td></tr>
				</table>
				';
    $pdf->writeHTML($html);

    $html = '<table border="1" cellpadding="1">
					<tr><td>物料编码</td><td>品名</td><td>规格型号</td><td>盘点后库存数量</td><td>单位</td>
						<td>盘点后库存金额</td><td>备注</td>
					</tr>
				';
    foreach ($bill["items"] as $v) {
      $html .= '<tr>';
      $html .= '<td>' . $v["goodsCode"] . '</td>';
      $html .= '<td>' . $v["goodsName"] . '</td>';
      $html .= '<td>' . $v["goodsSpec"] . '</td>';
      $html .= '<td align="right">' . $v["goodsCount"] . '</td>';
      $html .= '<td>' . $v["unitName"] . '</td>';
      $html .= '<td align="right">' . $v["goodsMoney"] . '</td>';
      $html .= '<td>' . $v["memo"] . '</td>';
      $html .= '</tr>';
    }

    $html .= "";

    $html .= '</table>';
    $pdf->writeHTML($html, true, false, true, false, '');

    ob_end_clean();

    $pdf->Output("$ref.pdf", "I");
  }

  /**
   * 生成打印盘点单的数据
   *
   * @param array $params
   */
  public function getICBillDataForLodopPrint($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new ICBillDAO($this->db());
    return $dao->getICBillDataForLodopPrint($params);
  }
}
