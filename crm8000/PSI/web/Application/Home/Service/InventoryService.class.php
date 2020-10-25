<?php

namespace Home\Service;

use Home\Common\FIdConst;
use Home\DAO\BizConfigDAO;

/**
 * 库存 Service
 *
 * @author 李静波
 */
class InventoryService extends PSIBaseService
{

  public function warehouseList()
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $sql = "select id, code, name, enabled from t_warehouse 
				where (inited = 1) ";
    $queryParams = [];

    $ds = new DataOrgService();
    $rs = $ds->buildSQL(FIdConst::INVENTORY_QUERY, "t_warehouse");
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = array_merge($queryParams, $rs[1]);
    }

    $sql .= " order by enabled, code";

    return M()->query($sql, $queryParams);
  }

  public function inventoryList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $db = M();

    $companyId = (new UserService())->getCompanyId();
    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $warehouseId = $params["warehouseId"];
    $code = $params["code"];
    $name = $params["name"];
    $spec = $params["spec"];
    $brandId = $params["brandId"];
    $start = $params["start"];
    $limit = $params["limit"];
    $hasInv = $params["hasInv"] == "1";

    $sort = $params["sort"];
    $sortProperty = "g.code";
    $sortDirection = "ASC";
    if ($sort) {
      $sortJSON = json_decode(html_entity_decode($sort), true);
      if ($sortJSON) {
        $sortProperty = strtolower($sortJSON[0]["property"]);
        if ($sortProperty == strtolower("goodsCode")) {
          $sortProperty = "g.code";
        } else if ($sortProperty == strtolower("afloatCount")) {
          $sortProperty = "v.afloat_count";
        } else if ($sortProperty == strtolower("afloatPrice")) {
          $sortProperty = "v.afloat_price";
        } else if ($sortProperty == strtolower("afloatMoney")) {
          $sortProperty = "v.afloat_money";
        } else if ($sortProperty == strtolower("inCount")) {
          $sortProperty = "v.in_count";
        } else if ($sortProperty == strtolower("inPrice")) {
          $sortProperty = "v.in_price";
        } else if ($sortProperty == strtolower("inMoney")) {
          $sortProperty = "v.in_money";
        } else if ($sortProperty == strtolower("outCount")) {
          $sortProperty = "v.out_count";
        } else if ($sortProperty == strtolower("outPrice")) {
          $sortProperty = "v.out_price";
        } else if ($sortProperty == strtolower("outMoney")) {
          $sortProperty = "v.out_money";
        } else if ($sortProperty == strtolower("balanceCount")) {
          $sortProperty = "v.balance_count";
        } else if ($sortProperty == strtolower("balancePrice")) {
          $sortProperty = "v.balance_price";
        } else if ($sortProperty == strtolower("balanceMoney")) {
          $sortProperty = "v.balance_money";
        }

        $sortDirection = strtoupper($sortJSON[0]["direction"]);
        if ($sortDirection != "ASC" && $sortDirection != "DESC") {
          $sortDirection = "ASC";
        }
      }
    }

    $queryParams = [];
    $queryParams[] = $warehouseId;

    $sql = "select g.id, g.code, g.name, g.spec, u.name as unit_name,
              convert(v.in_count, $fmt) as in_count, 
              v.in_price, v.in_money, convert(v.out_count, $fmt) as out_count, v.out_price, v.out_money,
              convert(v.balance_count, $fmt) as balance_count, v.balance_price, v.balance_money, 
              convert(v.afloat_count, $fmt) as afloat_count,
              v.afloat_money, v.afloat_price
            from t_inventory v, t_goods g, t_goods_unit u
            where (v.warehouse_id = '%s') and (v.goods_id = g.id) and (g.unit_id = u.id) ";
    if ($code) {
      $sql .= " and (g.code like '%s')";
      $queryParams[] = "%{$code}%";
    }
    if ($name) {
      $sql .= " and (g.name like '%s' or g.py like '%s')";
      $queryParams[] = "%{$name}%";
      $queryParams[] = "%{$name}%";
    }
    if ($spec) {
      $sql .= " and (g.spec like '%s')";
      $queryParams[] = "%{$spec}%";
    }
    if ($brandId) {
      $sql .= " and (g.brand_id = '%s')";
      $queryParams[] = $brandId;
    }
    if ($hasInv) {
      $sql .= " and (convert(v.balance_count, $fmt) > 0) ";
    }
    $sql .= " order by %s %s
              limit %d, %d";
    $queryParams[] = $sortProperty;
    $queryParams[] = $sortDirection;
    $queryParams[] = $start;
    $queryParams[] = $limit;

    $data = $db->query($sql, $queryParams);

    $result = [];

    foreach ($data as $i => $v) {
      $result[$i]["goodsId"] = $v["id"];
      $result[$i]["goodsCode"] = $v["code"];
      $result[$i]["goodsName"] = $v["name"];
      $result[$i]["goodsSpec"] = $v["spec"];
      $result[$i]["unitName"] = $v["unit_name"];
      $result[$i]["inCount"] = $v["in_count"];
      $result[$i]["inPrice"] = $v["in_price"];
      $result[$i]["inMoney"] = $v["in_money"];
      $result[$i]["outCount"] = $v["out_count"];
      $result[$i]["outPrice"] = $v["out_price"];
      $result[$i]["outMoney"] = $v["out_money"];
      $result[$i]["balanceCount"] = $v["balance_count"];
      $result[$i]["balancePrice"] = $v["balance_price"];
      $result[$i]["balanceMoney"] = $v["balance_money"];
      $result[$i]["afloatCount"] = $v["afloat_count"];
      $result[$i]["afloatPrice"] = $v["afloat_price"];
      $result[$i]["afloatMoney"] = $v["afloat_money"];
    }

    $queryParams = [];
    $queryParams[] = $warehouseId;
    $sql = "select count(*) as cnt 
            from t_inventory v, t_goods g, t_goods_unit u
            where (v.warehouse_id = '%s') and (v.goods_id = g.id) and (g.unit_id = u.id) ";
    if ($code) {
      $sql .= " and (g.code like '%s')";
      $queryParams[] = "%{$code}%";
    }
    if ($name) {
      $sql .= " and (g.name like '%s' or g.py like '%s')";
      $queryParams[] = "%{$name}%";
      $queryParams[] = "%{$name}%";
    }
    if ($spec) {
      $sql .= " and (g.spec like '%s')";
      $queryParams[] = "%{$spec}%";
    }
    if ($brandId) {
      $sql .= " and (g.brand_id = '%s')";
      $queryParams[] = $brandId;
    }
    if ($hasInv) {
      $sql .= " and (convert(v.balance_count, $fmt) > 0) ";
    }

    $data = $db->query($sql, $queryParams);
    $cnt = $data[0]["cnt"];

    return array(
      "dataList" => $result,
      "totalCount" => $cnt
    );
  }

  public function inventoryDetailList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $db = M();

    $companyId = (new UserService())->getCompanyId();
    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $warehouseId = $params["warehouseId"];
    $goodsId = $params["goodsId"];
    $dtFrom = $params["dtFrom"];
    $dtTo = $params["dtTo"];
    $start = $params["start"];
    $limit = $params["limit"];

    $sql = "select g.id, g.code, g.name, g.spec, u.name as unit_name,
              convert(v.in_count, $fmt) as in_count, v.in_price, v.in_money, 
              convert(v.out_count, $fmt) as out_count, v.out_price, v.out_money,
              convert(v.balance_count, $fmt) as balance_count, v.balance_price, v.balance_money,
              v.biz_date,  user.name as biz_user_name, v.ref_number, v.ref_type 
            from t_inventory_detail v, t_goods g, t_goods_unit u, t_user user
            where v.warehouse_id = '%s' and v.goods_id = '%s' 
              and v.goods_id = g.id and g.unit_id = u.id 
              and v.biz_user_id = user.id 
              and (v.biz_date between '%s' and '%s' ) 
            order by v.id 
            limit %d, %d";
    $data = $db->query($sql, $warehouseId, $goodsId, $dtFrom, $dtTo, $start, $limit);

    $result = [];

    foreach ($data as $i => $v) {
      $result[$i]["goodsId"] = $v["id"];
      $result[$i]["goodsCode"] = $v["code"];
      $result[$i]["goodsName"] = $v["name"];
      $result[$i]["goodsSpec"] = $v["spec"];
      $result[$i]["unitName"] = $v["unit_name"];
      $result[$i]["inCount"] = $v["in_count"];
      $result[$i]["inPrice"] = $v["in_price"];
      $result[$i]["inMoney"] = $v["in_money"];
      $result[$i]["outCount"] = $v["out_count"];
      $result[$i]["outPrice"] = $v["out_price"];
      $result[$i]["outMoney"] = $v["out_money"];
      $result[$i]["balanceCount"] = $v["balance_count"];
      $result[$i]["balancePrice"] = $v["balance_price"];
      $result[$i]["balanceMoney"] = $v["balance_money"];
      $result[$i]["bizDT"] = date("Y-m-d", strtotime($v["biz_date"]));
      $result[$i]["bizUserName"] = $v["biz_user_name"];
      $result[$i]["refNumber"] = $v["ref_number"];
      $result[$i]["refType"] = $v["ref_type"];
    }

    $sql = "select count(*) as cnt from t_inventory_detail
            where warehouse_id = '%s' and goods_id = '%s'      
              and (biz_date between '%s' and '%s')";
    $data = $db->query($sql, $warehouseId, $goodsId, $dtFrom, $dtTo);

    return array(
      "details" => $result,
      "totalCount" => $data[0]["cnt"]
    );
  }

  private function getDataForExcel($params)
  {
    $db = M();
    $sql = "select id, code, name, enabled from t_warehouse 
            where (inited = 1) ";
    $queryParams = [];

    $ds = new DataOrgService();
    $rs = $ds->buildSQL(FIdConst::INVENTORY_QUERY, "t_warehouse");
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = array_merge($queryParams, $rs[1]);
    }

    $sql .= " order by enabled, code";

    $data = $db->query($sql, $queryParams);
    $result = [];

    $companyId = (new UserService())->getCompanyId();
    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $code = $params["code"];
    $name = $params["name"];
    $spec = $params["spec"];
    $brandId = $params["brandId"];
    $hasInv = $params["hasInv"] == "1";

    foreach ($data as $v) {
      $warehouseId = $v["id"];
      $queryParams = [];
      $queryParams[] = $warehouseId;

      $sql = "select g.code, g.name, g.spec, u.name as unit_name,
                convert(v.in_count, $fmt) as in_count, 
                v.in_price, v.in_money, convert(v.out_count, $fmt) as out_count, v.out_price, v.out_money,
                convert(v.balance_count, $fmt) as balance_count, v.balance_price, v.balance_money, 
                convert(v.afloat_count, $fmt) as afloat_count,
                v.afloat_money, v.afloat_price
              from t_inventory v, t_goods g, t_goods_unit u
              where (v.warehouse_id = '%s') and (v.goods_id = g.id) and (g.unit_id = u.id) ";
      if ($code) {
        $sql .= " and (g.code like '%s')";
        $queryParams[] = "%{$code}%";
      }
      if ($name) {
        $sql .= " and (g.name like '%s' or g.py like '%s')";
        $queryParams[] = "%{$name}%";
        $queryParams[] = "%{$name}%";
      }
      if ($spec) {
        $sql .= " and (g.spec like '%s')";
        $queryParams[] = "%{$spec}%";
      }
      if ($brandId) {
        $sql .= " and (g.brand_id = '%s')";
        $queryParams[] = $brandId;
      }
      if ($hasInv) {
        $sql .= " and (convert(v.balance_count, $fmt) > 0) ";
      }
      $sql .= " order by g.code";

      $d = $db->query($sql, $queryParams);

      $result[] = [
        "warehouseCode" => $v["code"],
        "warehouseName" => $v["name"],
        "goodsDataList" => $d
      ];
    }

    return $result;
  }

  /**
   * 导出Excel
   */
  public function exportExcel($params)
  {
    if ($this->isNotOnline()) {
      return;
    }

    $db = M();

    $data = $this->getDataForExcel($params);

    // 记录业务日志
    $log = "库存总账导出Excel文件";
    $bls = new BizlogService($db);
    $bls->insertBizlog($log, "库存账查询");

    $excel = new \PHPExcel();

    // 删除Excel自动创建的Sheet
    $n = $excel->getSheetCount() - 1;
    for ($i = $n; $i >= 0; $i--) {
      $excel->removeSheetByIndex($i);
    }

    // 每个仓库创建一个Sheet
    foreach ($data as $i => $w) {
      $sheet = $excel->createSheet($i);

      $warehouseName = $w["warehouseName"];
      $sheet->setTitle($warehouseName);

      $sheet->getRowDimension('1')->setRowHeight(22);
      $sheet->setCellValue("A1", $warehouseName);

      $sheet->getColumnDimension('A')->setWidth(15);
      $sheet->setCellValue("A2", "物料编码");

      $sheet->getColumnDimension('B')->setWidth(40);
      $sheet->setCellValue("B2", "品名");

      $sheet->getColumnDimension('C')->setWidth(40);
      $sheet->setCellValue("C2", "规格型号");

      $sheet->getColumnDimension('D')->setWidth(10);
      $sheet->setCellValue("D2", "单位");

      $sheet->getColumnDimension('E')->setWidth(15);
      $sheet->setCellValue("E2", "在途数量");
      $sheet->getColumnDimension('F')->setWidth(15);
      $sheet->setCellValue("F2", "在途单价");
      $sheet->getColumnDimension('G')->setWidth(15);
      $sheet->setCellValue("G2", "在途金额");

      $sheet->getColumnDimension('H')->setWidth(15);
      $sheet->setCellValue("H2", "入库数量");
      $sheet->getColumnDimension('I')->setWidth(15);
      $sheet->setCellValue("I2", "入库平均单价");
      $sheet->getColumnDimension('J')->setWidth(15);
      $sheet->setCellValue("J2", "入库总金额");

      $sheet->getColumnDimension('K')->setWidth(15);
      $sheet->setCellValue("K2", "出库数量");
      $sheet->getColumnDimension('L')->setWidth(15);
      $sheet->setCellValue("L2", "出库平均单价");
      $sheet->getColumnDimension('M')->setWidth(15);
      $sheet->setCellValue("M2", "出库总金额");

      $sheet->getColumnDimension('N')->setWidth(15);
      $sheet->setCellValue("N2", "余额数量");
      $sheet->getColumnDimension('O')->setWidth(15);
      $sheet->setCellValue("O2", "余额平均单价");
      $sheet->getColumnDimension('P')->setWidth(15);
      $sheet->setCellValue("P2", "余额总金额");

      $goodsDataList = $w["goodsDataList"];
      foreach ($goodsDataList as $i => $v) {
        $row = $i + 3;
        $sheet->setCellValue("A" . $row, $v["code"]);
        $sheet->setCellValue("B" . $row, $v["name"]);
        $sheet->setCellValue("C" . $row, $v["spec"]);
        $sheet->setCellValue("D" . $row, $v["unit_name"]);
        $sheet->setCellValue("E" . $row, $v["afloat_count"]);
        $sheet->setCellValue("F" . $row, $v["afloat_price"]);
        $sheet->setCellValue("G" . $row, $v["afloat_money"]);
        $sheet->setCellValue("H" . $row, $v["in_count"]);
        $sheet->setCellValue("I" . $row, $v["in_price"]);
        $sheet->setCellValue("J" . $row, $v["in_money"]);
        $sheet->setCellValue("K" . $row, $v["out_count"]);
        $sheet->setCellValue("L" . $row, $v["out_price"]);
        $sheet->setCellValue("M" . $row, $v["out_money"]);
        $sheet->setCellValue("N" . $row, $v["balance_count"]);
        $sheet->setCellValue("O" . $row, $v["balance_price"]);
        $sheet->setCellValue("P" . $row, $v["balance_money"]);
      }

      // 画表格边框
      $styleArray = [
        'borders' => [
          'allborders' => [
            'style' => 'thin'
          ]
        ]
      ];
      $lastRow = count($goodsDataList) + 2;
      $sheet->getStyle('A2:P' . $lastRow)->applyFromArray($styleArray);
    }

    $excel->setActiveSheetIndex(0);

    $dt = date("YmdHis");
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="库存总账_' . $dt . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = \PHPExcel_IOFactory::createWriter($excel, "Excel2007");
    $writer->save("php://output");
  }
}
