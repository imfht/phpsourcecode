<?php

namespace Home\Service;

use Home\DAO\SOBillDAO;
use Home\Common\FIdConst;

/**
 * 销售订单Service
 *
 * @author 李静波
 */
class SOBillService extends PSIBaseExService
{
  private $LOG_CATEGORY = "销售订单";

  /**
   * 获得销售订单主表信息列表
   *
   * @param array $params        	
   * @return array
   */
  public function sobillList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();
    $dao = new SOBillDAO($this->db());
    return $dao->sobillList($params);
  }

  /**
   * 获得销售订单的信息
   */
  public function soBillInfo($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();
    $params["loginUserName"] = $this->getLoginUserName();
    $params["companyId"] = $this->getCompanyId();

    $dao = new SOBillDAO($this->db());
    return $dao->soBillInfo($params);
  }

  /**
   * 新增或编辑销售订单
   */
  public function editSOBill($json)
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

    $dao = new SOBillDAO($db);

    $id = $bill["id"];

    $log = null;

    $bill["companyId"] = $this->getCompanyId();

    if ($id) {
      // 编辑

      $bill["loginUserId"] = $this->getLoginUserId();

      $rc = $dao->updateSOBill($bill);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $ref = $bill["ref"];

      $log = "编辑销售订单，单号：{$ref}";
    } else {
      // 新建销售订单

      $bill["loginUserId"] = $this->getLoginUserId();
      $bill["dataOrg"] = $this->getLoginUserDataOrg();

      $rc = $dao->addSOBill($bill);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $id = $bill["id"];
      $ref = $bill["ref"];

      $scbillRef = $bill["scbillRef"];
      if ($scbillRef) {
        // 从销售合同生成销售订单
        $log = "从销售合同(合同号：{$scbillRef})生成销售订单: 单号 = {$ref}";
      } else {
        // 手工创建销售订单
        $log = "新建销售订单，单号：{$ref}";
      }
    }

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 获得销售订单的明细信息
   */
  public function soBillDetailList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new SOBillDAO($this->db());
    return $dao->soBillDetailList($params);
  }

  /**
   * 删除销售订单
   */
  public function deleteSOBill($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();

    $db->startTrans();

    $dao = new SOBillDAO($db);
    $rc = $dao->deleteSOBill($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $ref = $params["ref"];
    $log = "删除销售订单，单号：{$ref}";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 审核销售订单
   */
  public function commitSOBill($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $id = $params["id"];
    $db = $this->db();

    $db->startTrans();

    $dao = new SOBillDAO($db);

    $params["loginUserId"] = $this->getLoginUserId();

    $rc = $dao->commitSOBill($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    // 记录业务日志
    $ref = $params["ref"];
    $log = "审核销售订单，单号：{$ref}";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 取消销售订单审核
   */
  public function cancelConfirmSOBill($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $id = $params["id"];
    $db = $this->db();

    $db->startTrans();

    $dao = new SOBillDAO($db);
    $rc = $dao->cancelConfirmSOBill($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    // 记录业务日志
    $ref = $params["ref"];
    $log = "取消审核销售订单，单号：{$ref}";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 销售订单生成pdf文件
   */
  public function pdf($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $bs = new BizConfigService();
    $productionName = $bs->getProductionName();

    $ref = $params["ref"];

    $dao = new SOBillDAO($this->db());

    $bill = $dao->getDataForPDF($params);
    if (!$bill) {
      return;
    }

    // 记录业务日志
    $log = "销售订单(单号：$ref)生成PDF文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY);

    ob_start();

    $utilService = new UtilService();

    $ps = new PDFService();
    $pdf = $ps->getInstance();
    $pdf->SetTitle("销售订单，单号：{$ref}");

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

    $pdf->SetHeaderData("", 0, $productionName, "销售订单");

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
					<tr><td>交货日期：' . $bill["dealDate"] . '</td><td>交货地址:' . $bill["dealAddress"] . '</td></tr>
					<tr><td>业务员：' . $bill["bizUserName"] . '</td><td>税金：' . $bill["tax"] . ' (' . $utilService->moneyToCap($bill["tax"]) . ')</td></tr>
          <tr><td>销售金额:' . $bill["saleMoney"] . ' (' . $utilService->moneyToCap($bill["saleMoney"]) . ')</td>'
      . '<td>价税合计：' . $bill["moneyWithTax"] . ' (' . $utilService->moneyToCap($bill["moneyWithTax"]) . ')</td></tr>
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
   * 获得打印销售订单的数据
   *
   * @param array $params        	
   */
  public function getSOBillDataForLodopPrint($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new SOBillDAO($this->db());
    return $dao->getSOBillDataForLodopPrint($params);
  }

  /**
   * 销售订单 - 订单变更
   */
  public function changeSaleOrder($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $us = new UserService();
    if (!$us->hasPermission(FIdConst::SALE_ORDER_CONFIRM)) {
      return $this->bad("您没有订单变更的权限(拥有订单审核权限的用户才能做订单变更)");
    }

    $params["companyId"] = $this->getCompanyId();

    $db = $this->db();
    $db->startTrans();
    $dao = new SOBillDAO($db);
    $rc = $dao->changeSaleOrder($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $id = $params["id"];
    $ref = $params["ref"];

    // 记录业务日志
    $log = "销售订单[单号={$ref}]变更明细记录";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 查询主表金额相关数据 - 订单变更后刷新界面用
   */
  public function getSOBillDataAterChangeOrder($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new SOBillDAO($this->db());
    return $dao->getSOBillDataAterChangeOrder($params);
  }

  /**
   * 关闭销售订单
   */
  public function closeSOBill($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $id = $params["id"];

    $db = $this->db();
    $db->startTrans();
    $dao = new SOBillDAO($this->db());
    $rc = $dao->closeSOBill($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $ref = $params["ref"];

    // 记录业务日志
    $log = "关闭销售订单，单号：{$ref}";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 取消订单关闭状态
   */
  public function cancelClosedSOBill($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $id = $params["id"];

    $db = $this->db();
    $db->startTrans();
    $dao = new SOBillDAO($this->db());
    $rc = $dao->cancelClosedSOBill($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $ref = $params["ref"];

    // 记录业务日志
    $log = "取消销售订单[单号：{$ref}]的关闭状态";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 根据销售订单单号查询其生成的采购订单
   * 用于：在销售订单生成采购订单之前，提醒用户
   */
  public function getPOBillRefListBySOBillRef($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new SOBillDAO($this->db());
    return $dao->getPOBillRefListBySOBillRef($params);
  }

  /**
   * 根据销售订单单号查询其生成的销售出库单
   * 用于：在销售订单生成销售出库单之前，提醒用户
   */
  public function getWSBillRefListBySOBillRef($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new SOBillDAO($this->db());
    return $dao->getWSBillRefListBySOBillRef($params);
  }
}
