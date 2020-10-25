<?php

namespace Home\Service;

use Home\DAO\WSPBillDAO;

/**
 * 存货拆分Service
 *
 * @author 李静波
 */
class WSPBillService extends PSIBaseExService
{
  private $LOG_CATEGORY = "存货拆分";

  /**
   * 获得某个拆分单的商品构成
   *
   * @param array $params        	
   */
  public function goodsBOM($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new WSPBillDAO($this->db());
    return $dao->goodsBOM($params);
  }

  /**
   * 拆分单详情
   */
  public function wspBillInfo($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();
    $params["loginUserName"] = $this->getLoginUserName();
    $params["companyId"] = $this->getCompanyId();

    $dao = new WSPBillDAO($this->db());
    return $dao->wspBillInfo($params);
  }

  /**
   * 新增或编辑拆分单
   */
  public function editWSPBill($json)
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

    $dao = new WSPBillDAO($db);

    $id = $bill["id"];

    $log = null;

    $bill["companyId"] = $this->getCompanyId();

    if ($id) {
      // 编辑

      $bill["loginUserId"] = $this->getLoginUserId();

      $rc = $dao->updateWSPBill($bill);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $ref = $bill["ref"];

      $log = "编辑拆分单，单号：{$ref}";
    } else {
      // 新建

      $bill["loginUserId"] = $this->getLoginUserId();
      $bill["dataOrg"] = $this->getLoginUserDataOrg();

      $rc = $dao->addWSPBill($bill);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $id = $bill["id"];
      $ref = $bill["ref"];

      $log = "新建拆分单，单号：{$ref}";
    }

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 拆分单主表列表
   */
  public function wspbillList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();

    $dao = new WSPBillDAO($this->db());
    return $dao->wspbillList($params);
  }

  /**
   * 拆分单明细
   */
  public function wspBillDetailList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new WSPBillDAO($this->db());
    return $dao->wspBillDetailList($params);
  }

  /**
   * 拆分单明细 - 拆分后明细
   */
  public function wspBillDetailExList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new WSPBillDAO($this->db());
    return $dao->wspBillDetailExList($params);
  }

  /**
   * 删除拆分单
   */
  public function deleteWSPBill($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new WSPBillDAO($db);

    $rc = $dao->deleteWSPBill($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $ref = $params["ref"];

    $bs = new BizlogService($db);
    $log = "删除拆分单，单号：$ref";
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 提交拆分单
   */
  public function commitWSPBill($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new WSPBillDAO($db);

    $rc = $dao->commitWSPBill($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $id = $params["id"];
    $ref = $params["ref"];

    $bs = new BizlogService($db);
    $log = "提交拆分单，单号：$ref";
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 拆分单生成pdf文件
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

    $dao = new WSPBillDAO($this->db());

    $bill = $dao->getDataForPDF($params);
    if (!$bill) {
      return;
    }

    // 记录业务日志
    $log = "拆分单(单号：$ref)生成PDF文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    ob_start();

    $ps = new PDFService();
    $pdf = $ps->getInstance();
    $pdf->SetTitle("拆分单，单号：{$ref}");

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

    $pdf->SetHeaderData("", 0, $productionName, "拆分单");

    $pdf->SetFont("stsongstdlight", "", 10);
    $pdf->AddPage();

    /**
     * 注意：
     * TCPDF中，用来拼接HTML的字符串需要用单引号，否则HTML中元素的属性就不会被解析
     */
    $html = '
				<table>
					<tr><td colspan="2">单号：' . $ref . '</td></tr>
					<tr><td>仓库：' . $bill["fromWarehouseName"] . '</td><td>拆分后调入仓库：' . $bill["toWarehouseName"] . '</td></tr>
					<tr><td>业务日期：' . $bill["bizDT"] . '</td><td>业务员：' . $bill["bizUserName"] . '</td></tr>
					<tr><td colspan="2">备注：' . $bill["billMemo"] . '</td></tr>
				</table>
				';
    $pdf->writeHTML($html);

    // 拆分前商品明细
    $html = '<table border="1" cellpadding="1">
					<tr><td colspan="6" align="center">拆分前物料明细</td></tr>
					<tr><td>物料编码</td><td>品名</td><td>规格型号</td><td>拆分数量</td><td>单位</td><td>备注</td></tr>
				';
    foreach ($bill["items"] as $v) {
      $html .= '<tr>';
      $html .= '<td>' . $v["goodsCode"] . '</td>';
      $html .= '<td>' . $v["goodsName"] . '</td>';
      $html .= '<td>' . $v["goodsSpec"] . '</td>';
      $html .= '<td align="right">' . $v["goodsCount"] . '</td>';
      $html .= '<td>' . $v["unitName"] . '</td>';
      $html .= '<td>' . $v["memo"] . '</td>';
      $html .= '</tr>';
    }

    $html .= '</table>';
    $pdf->writeHTML($html, true, false, true, false, '');

    // 拆分后商品明细
    $html = '<table border="1" cellpadding="1">
					<tr><td colspan="5" align="center">拆分后物料明细</td></tr>
					<tr><td>物料编码</td><td>品名</td><td>规格型号</td><td>拆分数量</td><td>单位</td></tr>
				';
    foreach ($bill["itemsEx"] as $v) {
      $html .= '<tr>';
      $html .= '<td>' . $v["goodsCode"] . '</td>';
      $html .= '<td>' . $v["goodsName"] . '</td>';
      $html .= '<td>' . $v["goodsSpec"] . '</td>';
      $html .= '<td align="right">' . $v["goodsCount"] . '</td>';
      $html .= '<td>' . $v["unitName"] . '</td>';
      $html .= '</tr>';
    }

    $html .= '</table>';
    $pdf->writeHTML($html, true, false, true, false, '');

    ob_end_clean();

    $pdf->Output("$ref.pdf", "I");
  }

  /**
   * 生成打印拆分单的数据
   *
   * @param array $params        	
   */
  public function getWSPBillDataForLodopPrint($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new WSPBillDAO($this->db());
    return $dao->getWSPBillDataForLodopPrint($params);
  }
}
