<?php

namespace Home\DAO;

use Home\Common\FIdConst;

/**
 * 销售报表 DAO
 *
 * @author 李静波
 */
class SaleReportDAO extends PSIBaseExDAO
{

  /**
   * 销售日报表(按商品汇总) - 查询数据
   *
   * @param array $params
   */
  public function saleDayByGoodsQueryData($params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    $start = $params["start"];
    $limit = intval($params["limit"]);
    $showAllData = $limit == -1;

    $dt = $params["dt"];

    $sort = $params["sort"];
    $sortProperty = "goods_code";
    $sortDirection = "ASC";
    if ($sort) {
      $sortJSON = json_decode(html_entity_decode($sort), true);
      if ($sortJSON) {
        $sortProperty = strtolower($sortJSON[0]["property"]);
        if ($sortProperty == strtolower("goodsCode")) {
          $sortProperty = "goods_code";
        } else if ($sortProperty == strtolower("saleMoney")) {
          $sortProperty = "sale_money";
        } else if ($sortProperty == strtolower("saleCount")) {
          $sortProperty = "sale_count";
        } else if ($sortProperty == strtolower("rejMoney")) {
          $sortProperty = "rej_money";
        } else if ($sortProperty == strtolower("rejCount")) {
          $sortProperty = "rej_count";
        }

        $sortDirection = strtoupper($sortJSON[0]["direction"]);
        if ($sortDirection != "ASC" && $sortDirection != "DESC") {
          $sortDirection = "ASC";
        }
      }
    }

    $result = [];

    // 创建临时表保存数据
    $sql = "CREATE TEMPORARY TABLE psi_sale_report (
              biz_dt datetime,
              goods_id varchar(255), goods_code varchar(255), goods_name varchar(255), goods_spec varchar(255), 
              unit_name varchar(255), sale_money decimal(19,2), sale_count decimal(19,8),
              rej_money decimal(19,2), rej_count decimal(19, 8), m decimal(19,2), c decimal(19,8),
              profit decimal(19,2), rate decimal(19, 2)
            )";
    $db->execute($sql);

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $sql = "select g.id, g.code, g.name, g.spec, u.name as unit_name
            from t_goods g, t_goods_unit u
            where g.unit_id = u.id and g.id in(
                select distinct d.goods_id
                from t_ws_bill w, t_ws_bill_detail d
                where w.id = d.wsbill_id and w.bizdt = '%s' and w.bill_status >= 1000
                  and w.company_id = '%s'
                union
                select distinct d.goods_id
                from t_sr_bill s, t_sr_bill_detail d
                where s.id = d.srbill_id and s.bizdt = '%s' and s.bill_status = 1000
                  and s.company_id = '%s'
              )
            order by g.code";
    $items = $db->query($sql, $dt, $companyId, $dt, $companyId);

    foreach ($items as $v) {
      $goodsId = $v["id"];
      $goodsCode = $v["code"];
      $goodsName = $v["name"];
      $goodsSpec = $v["spec"];
      $unitName = $v["unit_name"];

      $sql = "select sum(d.goods_money) as goods_money, sum(d.inventory_money) as inventory_money,
                sum(convert(d.goods_count, $fmt)) as goods_count
              from t_ws_bill w, t_ws_bill_detail d
              where w.id = d.wsbill_id and w.bizdt = '%s' and d.goods_id = '%s'
              and w.bill_status >= 1000 and w.company_id = '%s' ";
      $data = $db->query($sql, $dt, $goodsId, $companyId);
      $saleCount = $data[0]["goods_count"];
      if (!$saleCount) {
        $saleCount = 0;
      }
      $saleMoney = $data[0]["goods_money"];
      if (!$saleMoney) {
        $saleMoney = 0;
      }
      $saleInventoryMoney = $data[0]["inventory_money"];
      if (!$saleInventoryMoney) {
        $saleInventoryMoney = 0;
      }

      $sql = "select sum(convert(d.rejection_goods_count, $fmt)) as rej_count,
                sum(d.rejection_sale_money) as rej_money,
                sum(d.inventory_money) as rej_inventory_money
              from t_sr_bill s, t_sr_bill_detail d
              where s.id = d.srbill_id and s.bizdt = '%s' and d.goods_id = '%s'
                and s.bill_status = 1000 and s.company_id = '%s' ";
      $data = $db->query($sql, $dt, $goodsId, $companyId);
      $rejCount = $data[0]["rej_count"];
      if (!$rejCount) {
        $rejCount = 0;
      }
      $rejSaleMoney = $data[0]["rej_money"];
      if (!$rejSaleMoney) {
        $rejSaleMoney = 0;
      }
      $rejInventoryMoney = $data[0]["rej_inventory_money"];
      if (!$rejInventoryMoney) {
        $rejInventoryMoney = 0;
      }

      $c = $saleCount - $rejCount;
      $m = $saleMoney - $rejSaleMoney;
      $c = number_format($c, $dataScale, ".", "");
      $profit = $saleMoney - $rejSaleMoney - $saleInventoryMoney + $rejInventoryMoney;
      $rate = 0;
      if ($m > 0) {
        $rate = $profit / $m * 100;
      }

      $sql = "insert into psi_sale_report (biz_dt, goods_code, goods_name, goods_spec, unit_name, 
                sale_money, sale_count, rej_money, rej_count, m, c, profit, rate)
              values ('%s', '%s', '%s', '%s', '%s', 
                %f, %f, %f, %f, %f, %f, %f, %f)";
      $db->execute(
        $sql,
        $dt,
        $goodsCode,
        $goodsName,
        $goodsSpec,
        $unitName,
        $saleMoney,
        $saleCount,
        $rejSaleMoney,
        $rejCount,
        $m,
        $c,
        $profit,
        $rate
      );
    }

    $sql = "select biz_dt, goods_code, goods_name, goods_spec, unit_name,
              sale_money, convert(sale_count, $fmt) as sale_count, rej_money, 
              convert(rej_count, $fmt) as rej_count, m, convert(c, $fmt) as c, profit, rate 
            from psi_sale_report
            order by %s %s ";
    if (!$showAllData) {
      $sql .= " limit %d, %d";
    }
    if ($showAllData) {
      $data = $db->query($sql, $sortProperty, $sortDirection);
    } else {
      // 分页
      $data = $db->query($sql, $sortProperty, $sortDirection, $start, $limit);
    }
    foreach ($data as $v) {
      $result[] = [
        "bizDT" => $this->toYMD($v["biz_dt"]),
        "goodsCode" => $v["goods_code"],
        "goodsName" => $v["goods_name"],
        "goodsSpec" => $v["goods_spec"],
        "saleCount" => $v["sale_count"],
        "unitName" => $v["unit_name"],
        "saleMoney" => $v["sale_money"],
        "rejCount" => $v["rej_count"],
        "rejMoney" => $v["rej_money"],
        "c" => $v["c"],
        "m" => $v["m"],
        "profit" => $v["profit"],
        "rate" => $v["rate"] == 0 ? null : sprintf("%0.2f", $v["rate"]) . "%"
      ];
    }

    $sql = "select count(*) as cnt
				from psi_sale_report
				";
    $data = $db->query($sql);
    $cnt = $data[0]["cnt"];

    // 删除临时表
    $sql = "DROP TEMPORARY TABLE IF EXISTS psi_sale_report";
    $db->execute($sql);

    return array(
      "dataList" => $result,
      "totalCount" => $cnt
    );
  }

  /**
   * 销售日报表(按客户汇总) - 查询数据
   */
  public function saleDayByCustomerQueryData($params)
  {
    $db = $this->db;
    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    $start = $params["start"];
    $limit = intval($params["limit"]);
    $showAllData = $limit == -1;

    $dt = $params["dt"];

    $sort = $params["sort"];
    $sortProperty = "customer_code";
    $sortDirection = "ASC";
    if ($sort) {
      $sortJSON = json_decode(html_entity_decode($sort), true);
      if ($sortJSON) {
        $sortProperty = strtolower($sortJSON[0]["property"]);
        if ($sortProperty == strtolower("customerCode")) {
          $sortProperty = "customer_code";
        } else if ($sortProperty == strtolower("saleMoney")) {
          $sortProperty = "sale_money";
        } else if ($sortProperty == strtolower("rejMoney")) {
          $sortProperty = "rej_money";
        }

        $sortDirection = strtoupper($sortJSON[0]["direction"]);
        if ($sortDirection != "ASC" && $sortDirection != "DESC") {
          $sortDirection = "ASC";
        }
      }
    }

    // 创建临时表保存数据
    $sql = "CREATE TEMPORARY TABLE psi_sale_report (
              biz_dt datetime,
              customer_code varchar(255), customer_name varchar(255),
              sale_money decimal(19,2),
              rej_money decimal(19,2), m decimal(19,2),
              profit decimal(19,2), rate decimal(19, 2)
            )";
    $db->execute($sql);

    $result = [];

    $sql = "select c.id, c.code, c.name
            from t_customer c
            where c.id in(
              select distinct w.customer_id
              from t_ws_bill w
              where w.bizdt = '%s' and w.bill_status >= 1000
                and w.company_id = '%s'
              union
              select distinct s.customer_id
              from t_sr_bill s
              where s.bizdt = '%s' and s.bill_status = 1000
                and s.company_id = '%s'
              )
            order by c.code";
    $items = $db->query($sql, $dt, $companyId, $dt, $companyId);
    foreach ($items as $v) {
      $customerCode = $v["code"];
      $customerName = $v["name"];

      $customerId = $v["id"];
      $sql = "select sum(w.sale_money) as goods_money, sum(w.inventory_money) as inventory_money
              from t_ws_bill w
              where w.bizdt = '%s' and w.customer_id = '%s'
                and w.bill_status >= 1000 and w.company_id = '%s' ";
      $data = $db->query($sql, $dt, $customerId, $companyId);
      $saleMoney = $data[0]["goods_money"];
      if (!$saleMoney) {
        $saleMoney = 0;
      }
      $saleInventoryMoney = $data[0]["inventory_money"];
      if (!$saleInventoryMoney) {
        $saleInventoryMoney = 0;
      }

      $sql = "select sum(s.rejection_sale_money) as rej_money,
                sum(s.inventory_money) as rej_inventory_money
              from t_sr_bill s
              where s.bizdt = '%s' and s.customer_id = '%s'
                and s.bill_status = 1000 and s.company_id = '%s' ";
      $data = $db->query($sql, $dt, $customerId, $companyId);
      $rejSaleMoney = $data[0]["rej_money"];
      if (!$rejSaleMoney) {
        $rejSaleMoney = 0;
      }
      $rejInventoryMoney = $data[0]["rej_inventory_money"];
      if (!$rejInventoryMoney) {
        $rejInventoryMoney = 0;
      }

      $m = $saleMoney - $rejSaleMoney;
      $profit = $saleMoney - $rejSaleMoney - $saleInventoryMoney + $rejInventoryMoney;
      $rate = 0;
      if ($m > 0) {
        $rate = $profit / $m * 100;
      }

      $sql = "insert into psi_sale_report (biz_dt, customer_code, customer_name,
                sale_money, rej_money, m, profit, rate)
              values ('%s', '%s', '%s',
                %f, %f, %f, %f, %f)";
      $db->execute(
        $sql,
        $dt,
        $customerCode,
        $customerName,
        $saleMoney,
        $rejSaleMoney,
        $m,
        $profit,
        $rate
      );
    }

    $sql = "select biz_dt, customer_code, customer_name,
              sale_money, rej_money,
              m, profit, rate
            from psi_sale_report
            order by %s %s ";
    if (!$showAllData) {
      $sql .= " limit %d, %d";
    }

    if ($showAllData) {
      $data = $db->query($sql, $sortProperty, $sortDirection);
    } else {
      $data = $db->query($sql, $sortProperty, $sortDirection, $start, $limit);
    }

    foreach ($data as $v) {
      $result[] = [
        "bizDT" => $this->toYMD($v["biz_dt"]),
        "customerCode" => $v["customer_code"],
        "customerName" => $v["customer_name"],
        "saleMoney" => $v["sale_money"],
        "rejMoney" => $v["rej_money"],
        "m" => $v["m"],
        "profit" => $v["profit"],
        "rate" => $v["rate"] == 0 ? null : sprintf("%0.2f", $v["rate"]) . "%"
      ];
    }

    $sql = "select count(*) as cnt
            from psi_sale_report
            ";
    $data = $db->query($sql);
    $cnt = $data[0]["cnt"];

    // 删除临时表
    $sql = "DROP TEMPORARY TABLE IF EXISTS psi_sale_report";
    $db->execute($sql);

    return array(
      "dataList" => $result,
      "totalCount" => $cnt
    );
  }

  /**
   * 销售日报表(按仓库汇总) - 查询数据
   */
  public function saleDayByWarehouseQueryData($params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    $start = $params["start"];
    $limit = intval($params["limit"]);
    $showAllData = $limit == -1;

    $dt = $params["dt"];

    $sort = $params["sort"];
    $sortProperty = "warehouse_code";
    $sortDirection = "ASC";
    if ($sort) {
      $sortJSON = json_decode(html_entity_decode($sort), true);
      if ($sortJSON) {
        $sortProperty = strtolower($sortJSON[0]["property"]);
        if ($sortProperty == strtolower("warehouseCode")) {
          $sortProperty = "warehouse_code";
        } else if ($sortProperty == strtolower("saleMoney")) {
          $sortProperty = "sale_money";
        } else if ($sortProperty == strtolower("rejMoney")) {
          $sortProperty = "rej_money";
        } else if ($sortProperty == strtolower("saleAreaRate")) {
          $sortProperty = "sale_area_rate";
        } else if ($sortProperty == strtolower("saleArea")) {
          $sortProperty = "sale_area";
        }

        $sortDirection = strtoupper($sortJSON[0]["direction"]);
        if ($sortDirection != "ASC" && $sortDirection != "DESC") {
          $sortDirection = "ASC";
        }
      }
    }

    // 创建临时表保存数据
    $sql = "CREATE TEMPORARY TABLE psi_sale_report (
              biz_dt datetime,
              warehouse_code varchar(255), warehouse_name varchar(255),
              sale_money decimal(19,2),
              rej_money decimal(19,2), m decimal(19,2),
              profit decimal(19,2), rate decimal(19, 2),
              sale_area decimal(19,2), sale_area_rate decimal(19, 2)
            )";
    $db->execute($sql);

    $sql = "select w.id, w.code, w.name, w.sale_area
            from t_warehouse w
            where w.id in(
              select distinct w.warehouse_id
              from t_ws_bill w
              where w.bizdt = '%s' and w.bill_status >= 1000
                and w.company_id = '%s'
              union
              select distinct s.warehouse_id
              from t_sr_bill s
              where s.bizdt = '%s' and s.bill_status = 1000
                and s.company_id = '%s'
              )
            order by w.code ";
    $items = $db->query($sql, $dt, $companyId, $dt, $companyId);
    foreach ($items as $v) {
      $warehouseCode = $v["code"];
      $warehouseName = $v["name"];
      $saleArea = $v["sale_area"] ?? 0;

      $warehouseId = $v["id"];
      $sql = "select sum(w.sale_money) as goods_money, sum(w.inventory_money) as inventory_money
              from t_ws_bill w
              where w.bizdt = '%s' and w.warehouse_id = '%s'
                and w.bill_status >= 1000 and w.company_id = '%s' ";
      $data = $db->query($sql, $dt, $warehouseId, $companyId);
      $saleMoney = $data[0]["goods_money"];
      if (!$saleMoney) {
        $saleMoney = 0;
      }
      $saleInventoryMoney = $data[0]["inventory_money"];
      if (!$saleInventoryMoney) {
        $saleInventoryMoney = 0;
      }

      $sql = "select sum(s.rejection_sale_money) as rej_money,
                sum(s.inventory_money) as rej_inventory_money
              from t_sr_bill s
              where s.bizdt = '%s' and s.warehouse_id = '%s'
                and s.bill_status = 1000 and s.company_id = '%s' ";
      $data = $db->query($sql, $dt, $warehouseId, $companyId);
      $rejSaleMoney = $data[0]["rej_money"];
      if (!$rejSaleMoney) {
        $rejSaleMoney = 0;
      }
      $rejInventoryMoney = $data[0]["rej_inventory_money"];
      if (!$rejInventoryMoney) {
        $rejInventoryMoney = 0;
      }

      $m = $saleMoney - $rejSaleMoney;
      $profit = $saleMoney - $rejSaleMoney - $saleInventoryMoney + $rejInventoryMoney;
      $rate = 0;
      if ($m > 0) {
        $rate = $profit / $m * 100;
      }
      $saleAreaRate = 0;
      if ($saleArea > 0) {
        $saleAreaRate = $m / $saleArea;
      }

      $sql = "insert into psi_sale_report (biz_dt, warehouse_code, warehouse_name,
                sale_money, rej_money, m, profit, rate, sale_area, sale_area_rate)
              values ('%s', '%s', '%s',
                %f, %f, %f, %f, %f, %f, %f)";
      $db->execute(
        $sql,
        $dt,
        $warehouseCode,
        $warehouseName,
        $saleMoney,
        $rejSaleMoney,
        $m,
        $profit,
        $rate,
        $saleArea,
        $saleAreaRate
      );
    }

    $result = [];
    $sql = "select biz_dt, warehouse_code, warehouse_name,
              sale_money, rej_money,
              m, profit, rate, sale_area, sale_area_rate
            from psi_sale_report
            order by %s %s ";
    if (!$showAllData) {
      $sql .= " limit %d, %d ";
    }
    if ($showAllData) {
      $data = $db->query($sql, $sortProperty, $sortDirection);
    } else {
      $data = $db->query($sql, $sortProperty, $sortDirection, $start, $limit);
    }
    foreach ($data as $v) {
      $result[] = [
        "bizDT" => $this->toYMD($v["biz_dt"]),
        "warehouseCode" => $v["warehouse_code"],
        "warehouseName" => $v["warehouse_name"],
        "saleMoney" => $v["sale_money"],
        "rejMoney" => $v["rej_money"],
        "m" => $v["m"],
        "profit" => $v["profit"],
        "rate" => $v["rate"] == 0 ? null : sprintf("%0.2f", $v["rate"]) . "%",
        "saleArea" => $v["sale_area"] == 0 ? null : $v["sale_area"],
        "saleAreaRate" => $v["sale_area_rate"] == 0 ? null : $v["sale_area_rate"]
      ];
    }

    $sql = "select count(*) as cnt
            from psi_sale_report
            ";
    $data = $db->query($sql);
    $cnt = $data[0]["cnt"];

    // 删除临时表
    $sql = "DROP TEMPORARY TABLE IF EXISTS psi_sale_report";
    $db->execute($sql);

    return array(
      "dataList" => $result,
      "totalCount" => $cnt
    );
  }

  /**
   * 销售月报表(按商品汇总) - 查询数据
   */
  public function saleMonthByGoodsQueryData($params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    $start = $params["start"];
    $limit = intval($params["limit"]);
    $showAllData = $limit == -1;

    $sort = $params["sort"];
    $sortProperty = "goods_code";
    $sortDirection = "ASC";
    if ($sort) {
      $sortJSON = json_decode(html_entity_decode($sort), true);
      if ($sortJSON) {
        $sortProperty = strtolower($sortJSON[0]["property"]);
        if ($sortProperty == strtolower("goodsCode")) {
          $sortProperty = "goods_code";
        } else if ($sortProperty == strtolower("saleMoney")) {
          $sortProperty = "sale_money";
        } else if ($sortProperty == strtolower("saleCount")) {
          $sortProperty = "sale_count";
        } else if ($sortProperty == strtolower("rejMoney")) {
          $sortProperty = "rej_money";
        } else if ($sortProperty == strtolower("rejCount")) {
          $sortProperty = "rej_count";
        }

        $sortDirection = strtoupper($sortJSON[0]["direction"]);
        if ($sortDirection != "ASC" && $sortDirection != "DESC") {
          $sortDirection = "ASC";
        }
      }
    }

    $year = $params["year"];
    $month = $params["month"];

    $dt = "";
    if ($month < 10) {
      $dt = "$year-0$month";
    } else {
      $dt = "$year-$month";
    }

    // 创建临时表保存数据
    $sql = "CREATE TEMPORARY TABLE psi_sale_report (
              biz_dt varchar(255),
              goods_id varchar(255), goods_code varchar(255), goods_name varchar(255), goods_spec varchar(255),
              unit_name varchar(255), sale_money decimal(19,2), sale_count decimal(19,8),
              rej_money decimal(19,2), rej_count decimal(19, 8), m decimal(19,2), c decimal(19,8),
              profit decimal(19,2), rate decimal(19, 2)
            )";
    $db->execute($sql);

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $sql = "select g.id, g.code, g.name, g.spec, u.name as unit_name
            from t_goods g, t_goods_unit u
            where g.unit_id = u.id and g.id in(
              select distinct d.goods_id
              from t_ws_bill w, t_ws_bill_detail d
              where w.id = d.wsbill_id and year(w.bizdt) = %d and month(w.bizdt) = %d
                and w.bill_status >= 1000
                and w.company_id = '%s'
              union
              select distinct d.goods_id
              from t_sr_bill s, t_sr_bill_detail d
              where s.id = d.srbill_id and year(s.bizdt) = %d and month(s.bizdt) = %d
                and s.bill_status = 1000
                and s.company_id = '%s'
              )
            order by g.code ";
    $items = $db->query($sql, $year, $month, $companyId, $year, $month, $companyId);
    foreach ($items as $v) {
      $goodsId = $v["id"];
      $goodsCode = $v["code"];
      $goodsName = $v["name"];
      $goodsSpec = $v["spec"];
      $unitName = $v["unit_name"];

      $sql = "select sum(d.goods_money) as goods_money, sum(d.inventory_money) as inventory_money,
                sum(convert(d.goods_count, $fmt)) as goods_count
              from t_ws_bill w, t_ws_bill_detail d
              where w.id = d.wsbill_id and year(w.bizdt) = %d and month(w.bizdt) = %d
                and d.goods_id = '%s'
                and w.bill_status >= 1000 and w.company_id = '%s' ";
      $data = $db->query($sql, $year, $month, $goodsId, $companyId);
      $saleCount = $data[0]["goods_count"];
      if (!$saleCount) {
        $saleCount = 0;
      }
      $saleMoney = $data[0]["goods_money"];
      if (!$saleMoney) {
        $saleMoney = 0;
      }
      $saleInventoryMoney = $data[0]["inventory_money"];
      if (!$saleInventoryMoney) {
        $saleInventoryMoney = 0;
      }

      $sql = "select sum(convert(d.rejection_goods_count, $fmt)) as rej_count,
                sum(d.rejection_sale_money) as rej_money,
                sum(d.inventory_money) as rej_inventory_money
              from t_sr_bill s, t_sr_bill_detail d
              where s.id = d.srbill_id and year(s.bizdt) = %d and month(s.bizdt) = %d
                and d.goods_id = '%s'
                and s.bill_status = 1000 and s.company_id = '%s' ";
      $data = $db->query($sql, $year, $month, $goodsId, $companyId);
      $rejCount = $data[0]["rej_count"];
      if (!$rejCount) {
        $rejCount = 0;
      }
      $rejSaleMoney = $data[0]["rej_money"];
      if (!$rejSaleMoney) {
        $rejSaleMoney = 0;
      }
      $rejInventoryMoney = $data[0]["rej_inventory_money"];
      if (!$rejInventoryMoney) {
        $rejInventoryMoney = 0;
      }

      $c = $saleCount - $rejCount;
      $m = $saleMoney - $rejSaleMoney;
      $c = number_format($c, $dataScale, ".", "");
      $profit = $saleMoney - $rejSaleMoney - $saleInventoryMoney + $rejInventoryMoney;
      $rate = 0;
      if ($m > 0) {
        $rate = $profit / $m * 100;
      }

      $sql = "insert into psi_sale_report (biz_dt, goods_code, goods_name, goods_spec, unit_name,
                sale_money, sale_count, rej_money, rej_count, m, c, profit, rate)
              values ('%s', '%s', '%s', '%s', '%s',
                %f, %f, %f, %f, %f, %f, %f, %f)";
      $db->execute(
        $sql,
        $dt,
        $goodsCode,
        $goodsName,
        $goodsSpec,
        $unitName,
        $saleMoney,
        $saleCount,
        $rejSaleMoney,
        $rejCount,
        $m,
        $c,
        $profit,
        $rate
      );
    }

    $sql = "select biz_dt, goods_code, goods_name, goods_spec, unit_name,
              sale_money, convert(sale_count, $fmt) as sale_count, rej_money,
              convert(rej_count, $fmt) as rej_count, m, convert(c, $fmt) as c, profit, rate
            from psi_sale_report
            order by %s %s ";
    if (!$showAllData) {
      $sql .= " limit %d, %d ";
    }
    if ($showAllData) {
      $data = $db->query($sql, $sortProperty, $sortDirection);
    } else {
      $data = $db->query($sql, $sortProperty, $sortDirection, $start, $limit);
    }
    $result = [];
    foreach ($data as $v) {
      $result[] = [
        "bizDT" => $v["biz_dt"],
        "goodsCode" => $v["goods_code"],
        "goodsName" => $v["goods_name"],
        "goodsSpec" => $v["goods_spec"],
        "saleCount" => $v["sale_count"],
        "unitName" => $v["unit_name"],
        "saleMoney" => $v["sale_money"],
        "rejCount" => $v["rej_count"],
        "rejMoney" => $v["rej_money"],
        "c" => $v["c"],
        "m" => $v["m"],
        "profit" => $v["profit"],
        "rate" => $v["rate"] == 0 ? null : sprintf("%0.2f", $v["rate"]) . "%"
      ];
    }

    $sql = "select count(*) as cnt
            from psi_sale_report
            ";
    $data = $db->query($sql);
    $cnt = $data[0]["cnt"];

    // 删除临时表
    $sql = "DROP TEMPORARY TABLE IF EXISTS psi_sale_report";
    $db->execute($sql);

    return array(
      "dataList" => $result,
      "totalCount" => $cnt
    );
  }

  /**
   * 销售月报表(按客户汇总) - 查询数据
   */
  public function saleMonthByCustomerQueryData($params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    $start = $params["start"];
    $limit = intval($params["limit"]);
    $showAllData = $limit == -1;

    $year = $params["year"];
    $month = $params["month"];

    $sort = $params["sort"];
    $sortProperty = "customer_code";
    $sortDirection = "ASC";
    if ($sort) {
      $sortJSON = json_decode(html_entity_decode($sort), true);
      if ($sortJSON) {
        $sortProperty = strtolower($sortJSON[0]["property"]);
        if ($sortProperty == strtolower("customerCode")) {
          $sortProperty = "customer_code";
        } else if ($sortProperty == strtolower("saleMoney")) {
          $sortProperty = "sale_money";
        } else if ($sortProperty == strtolower("rejMoney")) {
          $sortProperty = "rej_money";
        }

        $sortDirection = strtoupper($sortJSON[0]["direction"]);
        if ($sortDirection != "ASC" && $sortDirection != "DESC") {
          $sortDirection = "ASC";
        }
      }
    }

    // 创建临时表保存数据
    $sql = "CREATE TEMPORARY TABLE psi_sale_report (
              biz_dt varchar(255),
              customer_code varchar(255), customer_name varchar(255),
              sale_money decimal(19,2),
              rej_money decimal(19,2), m decimal(19,2),
              profit decimal(19,2), rate decimal(19, 2)
            )";
    $db->execute($sql);

    if ($month < 10) {
      $dt = "$year-0$month";
    } else {
      $dt = "$year-$month";
    }

    $sql = "select c.id, c.code, c.name
            from t_customer c
            where c.id in(
              select distinct w.customer_id
              from t_ws_bill w
              where year(w.bizdt) = %d and month(w.bizdt) = %d
                and w.bill_status >= 1000 and w.company_id = '%s'
              union
              select distinct s.customer_id
              from t_sr_bill s
              where year(s.bizdt) = %d and month(s.bizdt) = %d
                and s.bill_status = 1000 and s.company_id = '%s'
              )
            order by c.code ";
    $items = $db->query($sql, $year, $month, $companyId, $year, $month, $companyId);
    foreach ($items as $v) {

      $customerCode = $v["code"];
      $customerName = $v["name"];

      $customerId = $v["id"];
      $sql = "select sum(w.sale_money) as goods_money, sum(w.inventory_money) as inventory_money
              from t_ws_bill w
              where year(w.bizdt) = %d and month(w.bizdt) = %d
                and w.customer_id = '%s'
                and w.bill_status >= 1000 and w.company_id = '%s' ";
      $data = $db->query($sql, $year, $month, $customerId, $companyId);
      $saleMoney = $data[0]["goods_money"];
      if (!$saleMoney) {
        $saleMoney = 0;
      }
      $saleInventoryMoney = $data[0]["inventory_money"];
      if (!$saleInventoryMoney) {
        $saleInventoryMoney = 0;
      }

      $sql = "select sum(s.rejection_sale_money) as rej_money,
                sum(s.inventory_money) as rej_inventory_money
              from t_sr_bill s
              where year(s.bizdt) = %d and month(s.bizdt) = %d
                and s.customer_id = '%s'
                and s.bill_status = 1000 and s.company_id = '%s' ";
      $data = $db->query($sql, $year, $month, $customerId, $companyId);
      $rejSaleMoney = $data[0]["rej_money"];
      if (!$rejSaleMoney) {
        $rejSaleMoney = 0;
      }
      $rejInventoryMoney = $data[0]["rej_inventory_money"];
      if (!$rejInventoryMoney) {
        $rejInventoryMoney = 0;
      }

      $m = $saleMoney - $rejSaleMoney;
      $profit = $saleMoney - $rejSaleMoney - $saleInventoryMoney + $rejInventoryMoney;
      $rate = 0;
      if ($m > 0) {
        $rate = $profit / $m * 100;
      }

      $sql = "insert into psi_sale_report (biz_dt, customer_code, customer_name,
                sale_money, rej_money, m, profit, rate)
              values ('%s', '%s', '%s',
                %f, %f, %f, %f, %f)";
      $db->execute(
        $sql,
        $dt,
        $customerCode,
        $customerName,
        $saleMoney,
        $rejSaleMoney,
        $m,
        $profit,
        $rate
      );
    }

    $result = [];
    $sql = "select biz_dt, customer_code, customer_name,
              sale_money, rej_money,
              m, profit, rate
            from psi_sale_report
            order by %s %s ";
    if (!$showAllData) {
      $sql .= " limit %d, %d ";
    }
    if ($showAllData) {
      $data = $db->query($sql, $sortProperty, $sortDirection);
    } else {
      $data = $db->query($sql, $sortProperty, $sortDirection, $start, $limit);
    }
    foreach ($data as $v) {
      $result[] = [
        "bizDT" => $v["biz_dt"],
        "customerCode" => $v["customer_code"],
        "customerName" => $v["customer_name"],
        "saleMoney" => $v["sale_money"],
        "rejMoney" => $v["rej_money"],
        "m" => $v["m"],
        "profit" => $v["profit"],
        "rate" => $v["rate"] == 0 ? null : sprintf("%0.2f", $v["rate"]) . "%"
      ];
    }

    $sql = "select count(*) as cnt
            from psi_sale_report
            ";
    $data = $db->query($sql);
    $cnt = $data[0]["cnt"];

    // 删除临时表
    $sql = "DROP TEMPORARY TABLE IF EXISTS psi_sale_report";
    $db->execute($sql);

    return array(
      "dataList" => $result,
      "totalCount" => $cnt
    );
  }

  /**
   * 销售月报表(按仓库汇总) - 查询数据
   */
  public function saleMonthByWarehouseQueryData($params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    $start = $params["start"];
    $limit = intval($params["limit"]);
    $showAllData = $limit == -1;

    $year = $params["year"];
    $month = $params["month"];
    $dt = "";
    if ($month < 10) {
      $dt = "$year-0$month";
    } else {
      $dt = "$year-$month";
    }

    $sort = $params["sort"];
    $sortProperty = "warehouse_code";
    $sortDirection = "ASC";
    if ($sort) {
      $sortJSON = json_decode(html_entity_decode($sort), true);
      if ($sortJSON) {
        $sortProperty = strtolower($sortJSON[0]["property"]);
        if ($sortProperty == strtolower("warehouseCode")) {
          $sortProperty = "warehouse_code";
        } else if ($sortProperty == strtolower("saleMoney")) {
          $sortProperty = "sale_money";
        } else if ($sortProperty == strtolower("rejMoney")) {
          $sortProperty = "rej_money";
        } else if ($sortProperty == strtolower("saleAreaRate")) {
          $sortProperty = "sale_area_rate";
        } else if ($sortProperty == strtolower("saleArea")) {
          $sortProperty = "sale_area";
        }

        $sortDirection = strtoupper($sortJSON[0]["direction"]);
        if ($sortDirection != "ASC" && $sortDirection != "DESC") {
          $sortDirection = "ASC";
        }
      }
    }

    // 创建临时表保存数据
    $sql = "CREATE TEMPORARY TABLE psi_sale_report (
              biz_dt varchar(255),
              warehouse_code varchar(255), warehouse_name varchar(255),
              sale_money decimal(19,2),
              rej_money decimal(19,2), m decimal(19,2),
              profit decimal(19,2), rate decimal(19, 2),
              sale_area decimal(19,2), sale_area_rate decimal(19, 2)
            )";
    $db->execute($sql);

    $sql = "select w.id, w.code, w.name, w.sale_area
            from t_warehouse w
            where w.id in(
              select distinct w.warehouse_id
              from t_ws_bill w
              where year(w.bizdt) = %d and month(w.bizdt) = %d
                and w.bill_status >= 1000 and w.company_id = '%s'
              union
              select distinct s.warehouse_id
              from t_sr_bill s
              where year(s.bizdt) = %d and month(s.bizdt) = %d
                and s.bill_status = 1000 and w.company_id = '%s'
              )
            order by w.code ";
    $items = $db->query($sql, $year, $month, $companyId, $year, $month, $companyId);
    foreach ($items as $v) {
      $warehouseCode = $v["code"];
      $warehouseName = $v["name"];
      $saleArea = $v["sale_area"] ?? 0;

      $warehouseId = $v["id"];
      $sql = "select sum(w.sale_money) as goods_money, sum(w.inventory_money) as inventory_money
              from t_ws_bill w
              where year(w.bizdt) = %d and month(w.bizdt) = %d
                and w.warehouse_id = '%s'
                and w.bill_status >= 1000 and w.company_id = '%s' ";
      $data = $db->query($sql, $year, $month, $warehouseId, $companyId);
      $saleMoney = $data[0]["goods_money"];
      if (!$saleMoney) {
        $saleMoney = 0;
      }
      $saleInventoryMoney = $data[0]["inventory_money"];
      if (!$saleInventoryMoney) {
        $saleInventoryMoney = 0;
      }

      $sql = "select sum(s.rejection_sale_money) as rej_money,
                sum(s.inventory_money) as rej_inventory_money
              from t_sr_bill s
              where year(s.bizdt) = %d and month(s.bizdt) = %d
                and s.warehouse_id = '%s'
                and s.bill_status = 1000 and s.company_id = '%s' ";
      $data = $db->query($sql, $year, $month, $warehouseId, $companyId);
      $rejSaleMoney = $data[0]["rej_money"];
      if (!$rejSaleMoney) {
        $rejSaleMoney = 0;
      }
      $rejInventoryMoney = $data[0]["rej_inventory_money"];
      if (!$rejInventoryMoney) {
        $rejInventoryMoney = 0;
      }

      $m = $saleMoney - $rejSaleMoney;
      $profit = $saleMoney - $rejSaleMoney - $saleInventoryMoney + $rejInventoryMoney;
      $rate = 0;
      if ($m > 0) {
        $rate = $profit / $m * 100;
      }
      $saleAreaRate = 0;
      if ($saleArea > 0) {
        $saleAreaRate = $m / $saleArea;
      }

      $sql = "insert into psi_sale_report (biz_dt, warehouse_code, warehouse_name,
                sale_money, rej_money, m, profit, rate, sale_area, sale_area_rate)
              values ('%s', '%s', '%s',
                %f, %f, %f, %f, %f, %f, %f)";
      $db->execute(
        $sql,
        $dt,
        $warehouseCode,
        $warehouseName,
        $saleMoney,
        $rejSaleMoney,
        $m,
        $profit,
        $rate,
        $saleArea,
        $saleAreaRate
      );
    }

    $result = [];
    $sql = "select biz_dt, warehouse_code, warehouse_name,
              sale_money, rej_money,
              m, profit, rate, sale_area, sale_area_rate
            from psi_sale_report
            order by %s %s ";
    if (!$showAllData) {
      $sql .= " limit %d, %d ";
    }
    if ($showAllData) {
      $data = $db->query($sql, $sortProperty, $sortDirection);
    } else {
      $data = $db->query($sql, $sortProperty, $sortDirection, $start, $limit);
    }
    foreach ($data as $v) {
      $result[] = [
        "bizDT" => $v["biz_dt"],
        "warehouseCode" => $v["warehouse_code"],
        "warehouseName" => $v["warehouse_name"],
        "saleMoney" => $v["sale_money"],
        "rejMoney" => $v["rej_money"],
        "m" => $v["m"],
        "profit" => $v["profit"],
        "rate" => $v["rate"] == 0 ? null : sprintf("%0.2f", $v["rate"]) . "%",
        "saleArea" => $v["sale_area"] == 0 ? null : $v["sale_area"],
        "saleAreaRate" => $v["sale_area_rate"] == 0 ? null : $v["sale_area_rate"]
      ];
    }

    $sql = "select count(*) as cnt
            from psi_sale_report
            ";
    $data = $db->query($sql);
    $cnt = $data[0]["cnt"];

    // 删除临时表
    $sql = "DROP TEMPORARY TABLE IF EXISTS psi_sale_report";
    $db->execute($sql);

    return array(
      "dataList" => $result,
      "totalCount" => $cnt
    );
  }

  /**
   * 销售日报表(按业务员汇总) - 查询数据
   */
  public function saleDayByBizuserQueryData($params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    $start = $params["start"];
    $limit = intval($params["limit"]);
    $showAllData = $limit == -1;

    $dt = $params["dt"];

    $sort = $params["sort"];
    $sortProperty = "user_code";
    $sortDirection = "ASC";
    if ($sort) {
      $sortJSON = json_decode(html_entity_decode($sort), true);
      if ($sortJSON) {
        $sortProperty = strtolower($sortJSON[0]["property"]);
        if ($sortProperty == strtolower("userCode")) {
          $sortProperty = "user_code";
        } else if ($sortProperty == strtolower("saleMoney")) {
          $sortProperty = "sale_money";
        } else if ($sortProperty == strtolower("rejMoney")) {
          $sortProperty = "rej_money";
        }

        $sortDirection = strtoupper($sortJSON[0]["direction"]);
        if ($sortDirection != "ASC" && $sortDirection != "DESC") {
          $sortDirection = "ASC";
        }
      }
    }

    // 创建临时表保存数据
    $sql = "CREATE TEMPORARY TABLE psi_sale_report (
              biz_dt datetime,
              user_code varchar(255), user_name varchar(255),
              sale_money decimal(19,2),
              rej_money decimal(19,2), m decimal(19,2),
              profit decimal(19,2), rate decimal(19, 2)
            )";
    $db->execute($sql);

    $sql = "select u.id, u.org_code, u.name
            from t_user u
            where u.id in(
              select distinct w.biz_user_id
              from t_ws_bill w
              where w.bizdt = '%s' and w.bill_status >= 1000
                and w.company_id = '%s'
              union
              select distinct s.biz_user_id
              from t_sr_bill s
              where s.bizdt = '%s' and s.bill_status = 1000
                and s.company_id = '%s'
              )
            order by u.org_code ";
    $items = $db->query($sql, $dt, $companyId, $dt, $companyId);
    foreach ($items as $v) {
      $userName = $v["name"];
      $userCode = $v["org_code"];

      $userId = $v["id"];
      $sql = "select sum(w.sale_money) as goods_money, sum(w.inventory_money) as inventory_money
              from t_ws_bill w
              where w.bizdt = '%s' and w.biz_user_id = '%s'
                and w.bill_status >= 1000 and w.company_id = '%s' ";
      $data = $db->query($sql, $dt, $userId, $companyId);
      $saleMoney = $data[0]["goods_money"];
      if (!$saleMoney) {
        $saleMoney = 0;
      }
      $saleInventoryMoney = $data[0]["inventory_money"];
      if (!$saleInventoryMoney) {
        $saleInventoryMoney = 0;
      }

      $sql = "select sum(s.rejection_sale_money) as rej_money,
                sum(s.inventory_money) as rej_inventory_money
              from t_sr_bill s
              where s.bizdt = '%s' and s.biz_user_id = '%s'
                and s.bill_status = 1000 and s.company_id = '%s' ";
      $data = $db->query($sql, $dt, $userId, $companyId);
      $rejSaleMoney = $data[0]["rej_money"];
      if (!$rejSaleMoney) {
        $rejSaleMoney = 0;
      }
      $rejInventoryMoney = $data[0]["rej_inventory_money"];
      if (!$rejInventoryMoney) {
        $rejInventoryMoney = 0;
      }

      $m = $saleMoney - $rejSaleMoney;
      $profit = $saleMoney - $rejSaleMoney - $saleInventoryMoney + $rejInventoryMoney;

      $rate = 0;
      if ($m > 0) {
        $rate = $profit / $m * 100;
      }
      $sql = "insert into psi_sale_report (biz_dt, user_code, user_name,
                sale_money, rej_money, m, profit, rate)
              values ('%s', '%s', '%s',
                %f, %f, %f, %f, %f)";
      $db->execute(
        $sql,
        $dt,
        $userCode,
        $userName,
        $saleMoney,
        $rejSaleMoney,
        $m,
        $profit,
        $rate
      );
    }

    $result = [];
    $sql = "select biz_dt, user_code, user_name,
              sale_money, rej_money,
              m, profit, rate
            from psi_sale_report
            order by %s %s ";
    if (!$showAllData) {
      $sql .= " limit %d, %d ";
    }
    if ($showAllData) {
      $data = $db->query($sql, $sortProperty, $sortDirection);
    } else {
      $data = $db->query($sql, $sortProperty, $sortDirection, $start, $limit);
    }
    foreach ($data as $v) {
      $result[] = [
        "bizDT" => $this->toYMD($v["biz_dt"]),
        "userCode" => $v["user_code"],
        "userName" => $v["user_name"],
        "saleMoney" => $v["sale_money"],
        "rejMoney" => $v["rej_money"],
        "m" => $v["m"],
        "profit" => $v["profit"],
        "rate" => $v["rate"] == 0 ? null : sprintf("%0.2f", $v["rate"]) . "%"
      ];
    }

    $sql = "select count(*) as cnt
            from psi_sale_report
            ";
    $data = $db->query($sql);
    $cnt = $data[0]["cnt"];

    // 删除临时表
    $sql = "DROP TEMPORARY TABLE IF EXISTS psi_sale_report";
    $db->execute($sql);

    return array(
      "dataList" => $result,
      "totalCount" => $cnt
    );
  }

  /**
   * 销售月报表(按业务员汇总) - 查询数据
   */
  public function saleMonthByBizuserQueryData($params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->emptyResult();
    }

    $start = $params["start"];
    $limit = intval($params["limit"]);
    $showAllData = $limit == -1;

    $year = $params["year"];
    $month = $params["month"];
    $dt = "";
    if ($month < 10) {
      $dt = "$year-0$month";
    } else {
      $dt = "$year-$month";
    }

    $sort = $params["sort"];
    $sortProperty = "user_code";
    $sortDirection = "ASC";
    if ($sort) {
      $sortJSON = json_decode(html_entity_decode($sort), true);
      if ($sortJSON) {
        $sortProperty = strtolower($sortJSON[0]["property"]);
        if ($sortProperty == strtolower("userCode")) {
          $sortProperty = "user_code";
        } else if ($sortProperty == strtolower("saleMoney")) {
          $sortProperty = "sale_money";
        } else if ($sortProperty == strtolower("rejMoney")) {
          $sortProperty = "rej_money";
        }

        $sortDirection = strtoupper($sortJSON[0]["direction"]);
        if ($sortDirection != "ASC" && $sortDirection != "DESC") {
          $sortDirection = "ASC";
        }
      }
    }

    // 创建临时表保存数据
    $sql = "CREATE TEMPORARY TABLE psi_sale_report (
              biz_dt varchar(255),
              user_code varchar(255), user_name varchar(255),
              sale_money decimal(19,2),
              rej_money decimal(19,2), m decimal(19,2),
              profit decimal(19,2), rate decimal(19, 2)
            )";
    $db->execute($sql);

    $sql = "select u.id, u.org_code as code, u.name
            from t_user u
            where u.id in(
              select distinct w.biz_user_id
              from t_ws_bill w
              where year(w.bizdt) = %d and month(w.bizdt) = %d
                and w.bill_status >= 1000 and w.company_id = '%s'
              union
              select distinct s.biz_user_id
              from t_sr_bill s
              where year(s.bizdt) = %d and month(s.bizdt) = %d
                and s.bill_status = 1000 and s.company_id = '%s'
              )
            order by u.org_code ";
    $items = $db->query($sql, $year, $month, $companyId, $year, $month, $companyId);
    foreach ($items as $v) {
      $userCode = $v["code"];
      $userName = $v["name"];

      $userId = $v["id"];
      $sql = "select sum(w.sale_money) as goods_money, sum(w.inventory_money) as inventory_money
              from t_ws_bill w
              where year(w.bizdt) = %d and month(w.bizdt) = %d
                and w.biz_user_id = '%s'
                and w.bill_status >= 1000 and w.company_id = '%s' ";
      $data = $db->query($sql, $year, $month, $userId, $companyId);
      $saleMoney = $data[0]["goods_money"];
      if (!$saleMoney) {
        $saleMoney = 0;
      }
      $saleInventoryMoney = $data[0]["inventory_money"];
      if (!$saleInventoryMoney) {
        $saleInventoryMoney = 0;
      }

      $sql = "select sum(s.rejection_sale_money) as rej_money,
              sum(s.inventory_money) as rej_inventory_money
              from t_sr_bill s
              where year(s.bizdt) = %d and month(s.bizdt) = %d
                and s.biz_user_id = '%s'
                and s.bill_status = 1000 and s.company_id = '%s' ";
      $data = $db->query($sql, $year, $month, $userId, $companyId);
      $rejSaleMoney = $data[0]["rej_money"];
      if (!$rejSaleMoney) {
        $rejSaleMoney = 0;
      }
      $rejInventoryMoney = $data[0]["rej_inventory_money"];
      if (!$rejInventoryMoney) {
        $rejInventoryMoney = 0;
      }

      $m = $saleMoney - $rejSaleMoney;
      $profit = $saleMoney - $rejSaleMoney - $saleInventoryMoney + $rejInventoryMoney;
      $rate = 0;
      if ($m > 0) {
        $rate = $profit / $m * 100;
      }

      $sql = "insert into psi_sale_report (biz_dt, user_code, user_name,
                sale_money, rej_money, m, profit, rate)
              values ('%s', '%s', '%s',
                %f, %f, %f, %f, %f)";
      $db->execute(
        $sql,
        $dt,
        $userCode,
        $userName,
        $saleMoney,
        $rejSaleMoney,
        $m,
        $profit,
        $rate
      );
    }

    $result = [];
    $sql = "select biz_dt, user_code, user_name,
              sale_money, rej_money,
              m, profit, rate
            from psi_sale_report
            order by %s %s ";
    if (!$showAllData) {
      $sql .= " limit %d, %d ";
    }
    $data = $showAllData ? $db->query($sql, $sortProperty, $sortDirection) : $db->query(
      $sql,
      $sortProperty,
      $sortDirection,
      $start,
      $limit
    );
    foreach ($data as $v) {
      $result[] = [
        "bizDT" => $this->toYMD($v["biz_dt"]),
        "userCode" => $v["user_code"],
        "userName" => $v["user_name"],
        "saleMoney" => $v["sale_money"],
        "rejMoney" => $v["rej_money"],
        "m" => $v["m"],
        "profit" => $v["profit"],
        "rate" => $v["rate"] == 0 ? null : sprintf("%0.2f", $v["rate"]) . "%"
      ];
    }

    $sql = "select count(*) as cnt
            from psi_sale_report
            ";
    $data = $db->query($sql);
    $cnt = $data[0]["cnt"];

    // 删除临时表
    $sql = "DROP TEMPORARY TABLE IF EXISTS psi_sale_report";
    $db->execute($sql);

    return array(
      "dataList" => $result,
      "totalCount" => $cnt
    );
  }

  /**
   * 销售出库明细表 - 查询数据
   */
  public function saleDetailQueryData($params)
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

    $customerId = $params["customerId"];
    $warehouseId = $params["warehouseId"];
    $fromDT = $params["fromDT"];
    $toDT = $params["toDT"];
    $start = $params["start"];
    $limit = $params["limit"];

    // 显示全部数据，例如用于导出Excel
    $showAllData = $limit == -1;

    $sql = "select ws.id, ws.ref, ws.bizdt as biz_dt, g.code as goods_code, g.name as goods_name, 
              g.spec as goods_spec, u.name as unit_name,
              c.name as customer_name, convert(d.goods_count, $fmt) as goods_count,
              d.goods_price, d.goods_money, d.tax_rate, d.tax, d.money_with_tax,
              d.goods_price_with_tax, d.memo, w.name as warehouse_name 
            from t_ws_bill ws, t_ws_bill_detail d, t_goods g, t_goods_unit u,
              t_customer c, t_warehouse w
            where (ws.id = d.wsbill_id) and (d.goods_id = g.id) 
              and (g.unit_id = u.id) and (ws.customer_id = c.id)
              and (ws.warehouse_id = w.id) ";
    $queryParams = [];

    $ds = new DataOrgDAO($db);
    // 构建数据域SQL
    $rs = $ds->buildSQL(FIdConst::SALE_DETAIL_REPORT, "ws", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = array_merge($queryParams, $rs[1]);
    }

    if ($customerId) {
      $sql .= " and (ws.customer_id = '%s') ";
      $queryParams[] = $customerId;
    }
    if ($warehouseId) {
      $sql .= " and (ws.warehouse_id = '%s') ";
      $queryParams[] = $warehouseId;
    }
    if ($fromDT) {
      $sql .= " and (ws.bizdt >= '%s') ";
      $queryParams[] = $fromDT;
    }
    if ($toDT) {
      $sql .= " and (ws.bizdt <= '%s') ";
      $queryParams[] = $toDT;
    }
    $sql .= " order by ws.ref, d.show_order ";
    if (!$showAllData) {
      $sql .= " limit %d, %d";
      $queryParams[] = $start;
      $queryParams[] = $limit;
    }

    $data = $db->query($sql, $queryParams);
    $result = [];
    foreach ($data as $v) {
      $id = $v["id"];
      // 查询销售订单号
      $sql = "select s.ref
              from t_so_bill s, t_so_ws w
              where s.id = w.so_id and w.ws_id = '%s' ";
      $d = $db->query($sql, $id);
      $soBillRef = "";
      if ($d) {
        $soBillRef = $d[0]["ref"];
      }
      $result[] = [
        "soBillRef" => $soBillRef,
        "wsBillRef" => $v["ref"],
        "bizDate" => $this->toYMD($v["biz_dt"]),
        "customerName" => $v["customer_name"],
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
            from t_ws_bill ws, t_ws_bill_detail d, t_goods g, t_goods_unit u,
              t_customer c, t_warehouse w
            where (ws.id = d.wsbill_id) and (d.goods_id = g.id) 
              and (g.unit_id = u.id) and (ws.customer_id = c.id)
              and (ws.warehouse_id = w.id) ";
    $queryParams = [];
    // 构建数据域SQL
    $rs = $ds->buildSQL(FIdConst::SALE_DETAIL_REPORT, "ws", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = array_merge($queryParams, $rs[1]);
    }
    if ($customerId) {
      $sql .= " and (ws.customer_id = '%s') ";
      $queryParams[] = $customerId;
    }
    if ($warehouseId) {
      $sql .= " and (ws.warehouse_id = '%s') ";
      $queryParams[] = $warehouseId;
    }
    if ($fromDT) {
      $sql .= " and (ws.bizdt >= '%s') ";
      $queryParams[] = $fromDT;
    }
    if ($toDT) {
      $sql .= " and (ws.bizdt <= '%s') ";
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
