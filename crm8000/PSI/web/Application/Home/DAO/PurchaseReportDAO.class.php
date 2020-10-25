<?php

namespace Home\DAO;

use Home\Common\FIdConst;

/**
 * 采购报表 DAO
 *
 * @author 李静波
 */
class PurchaseReportDAO extends PSIBaseExDAO
{

  /**
   * 采购入库明细表 - 查询数据
   *
   * @param array $params
   */
  public function purchaseDetailQueryData($params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    $loginUserId = $params["loginUserId"];

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $supplierId = $params["supplierId"];
    $warehouseId = $params["warehouseId"];
    $fromDT = $params["fromDT"];
    $toDT = $params["toDT"];
    $start = $params["start"];
    $limit = $params["limit"];

    // 显示全部数据，例如用于导出Excel
    $showAllData = $limit == -1;

    $sql = "select p.id, p.ref, p.biz_dt, g.code as goods_code, g.name as goods_name, 
              g.spec as goods_spec, u.name as unit_name,
              s.name as supplier_name, convert(d.goods_count, $fmt) as goods_count,
              d.goods_price, d.goods_money, d.tax_rate, d.tax, d.money_with_tax,
              d.goods_price_with_tax, d.memo, w.name as warehouse_name 
            from t_pw_bill p, t_pw_bill_detail d, t_goods g, t_goods_unit u,
              t_supplier s, t_warehouse w
            where (p.id = d.pwbill_id) and (d.goods_id = g.id) 
              and (g.unit_id = u.id) and (p.supplier_id = s.id)
              and (p.warehouse_id = w.id) ";
    $queryParams = [];

    $ds = new DataOrgDAO($db);
    // 构建数据域SQL
    $rs = $ds->buildSQL(FIdConst::PURCHASE_DETAIL_REPORT, "p", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = array_merge($queryParams, $rs[1]);
    }

    if ($supplierId) {
      $sql .= " and (p.supplier_id = '%s') ";
      $queryParams[] = $supplierId;
    }
    if ($warehouseId) {
      $sql .= " and (p.warehouse_id = '%s') ";
      $queryParams[] = $warehouseId;
    }
    if ($fromDT) {
      $sql .= " and (p.biz_dt >= '%s') ";
      $queryParams[] = $fromDT;
    }
    if ($toDT) {
      $sql .= " and (p.biz_dt <= '%s') ";
      $queryParams[] = $toDT;
    }
    $sql .= " order by p.ref, d.show_order ";
    if (!$showAllData) {
      $sql .= " limit %d, %d";
      $queryParams[] = $start;
      $queryParams[] = $limit;
    }

    $data = $db->query($sql, $queryParams);
    $result = [];
    foreach ($data as $v) {
      $id = $v["id"];
      // 查询采购订单号
      $sql = "select p.ref
              from t_po_bill p, t_po_pw w
              where p.id = w.po_id and w.pw_id = '%s' ";
      $d = $db->query($sql, $id);
      $poBillRef = "";
      if ($d) {
        $poBillRef = $d[0]["ref"];
      }
      $result[] = [
        "poBillRef" => $poBillRef,
        "pwBillRef" => $v["ref"],
        "bizDate" => $this->toYMD($v["biz_dt"]),
        "supplierName" => $v["supplier_name"],
        "goodsCode" => $v["goods_code"],
        "goodsName" => $v["goods_name"],
        "goodsSpec" => $v["goods_spec"],
        "goodsCount" => $v["goods_count"],
        "unitName" => $v["unit_name"],
        "goodsPrice" => $v["goods_price"],
        "goodsMoney" => $v["goods_money"],
        "taxRate" => $v["tax_rate"],
        "tax" => $v["tax"],
        "moneyWithTax" => $v["money_with_tax"],
        "goodsPriceWithTax" => $v["goods_price_with_tax"],
        "memo" => $v["memo"],
        "warehouseName" => $v["warehouse_name"],
      ];
    }

    $sql = "select count(*) as cnt
            from t_pw_bill p, t_pw_bill_detail d, t_goods g, t_goods_unit u,
              t_supplier s, t_warehouse w
            where (p.id = d.pwbill_id) and (d.goods_id = g.id) 
              and (g.unit_id = u.id) and (p.supplier_id = s.id)
              and (p.warehouse_id = w.id) ";
    $queryParams = [];
    if ($supplierId) {
      $sql .= " and (p.supplier_id = '%s') ";
      $queryParams[] = $supplierId;
    }
    if ($warehouseId) {
      $sql .= " and (p.warehouse_id = '%s') ";
      $queryParams[] = $warehouseId;
    }
    if ($fromDT) {
      $sql .= " and (p.biz_dt >= '%s') ";
      $queryParams[] = $fromDT;
    }
    if ($toDT) {
      $sql .= " and (p.biz_dt <= '%s') ";
      $queryParams[] = $toDT;
    }
    $data = $db->query($sql, $queryParams);
    $cnt = $data[0]["cnt"];

    return [
      "dataList" => $result,
      "totalCount" => $cnt
    ];
  }
}
