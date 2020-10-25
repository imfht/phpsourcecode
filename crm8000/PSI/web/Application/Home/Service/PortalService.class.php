<?php

namespace Home\Service;

use Home\Common\FIdConst;

/**
 * Portal Service
 *
 * @author 李静波
 */
class PortalService extends PSIBaseExService
{

  public function inventoryPortal()
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $result = array();

    $db = $this->db();
    $sql = "select id, name 
				from t_warehouse 
				where (inited = 1) and (enabled = 1) ";
    $queryParams = array();
    $ds = new DataOrgService();
    $rs = $ds->buildSQL(FIdConst::PORTAL_INVENTORY, "t_warehouse");
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = $rs[1];
    }

    $sql .= " order by code ";
    $data = $db->query($sql, $queryParams);
    foreach ($data as $i => $v) {
      $result[$i]["warehouseName"] = $v["name"];
      $warehouseId = $v["id"];

      // 库存金额
      $sql = "select sum(balance_money) as balance_money 
					from t_inventory
					where warehouse_id = '%s' ";
      $d = $db->query($sql, $warehouseId);
      if ($d) {
        $m = $d[0]["balance_money"];
        $result[$i]["inventoryMoney"] = $m ? $m : 0;
      } else {
        $result[$i]["inventoryMoney"] = 0;
      }
      // 低于安全库存数量的商品种类
      $sql = "select count(*) as cnt
					from t_inventory i, t_goods_si s
					where i.goods_id = s.goods_id and i.warehouse_id = s.warehouse_id
						and s.safety_inventory > i.balance_count
						and i.warehouse_id = '%s' ";
      $d = $db->query($sql, $warehouseId);
      $result[$i]["siCount"] = $d[0]["cnt"];

      // 超过库存上限的商品种类
      $sql = "select count(*) as cnt
					from t_inventory i, t_goods_si s
					where i.goods_id = s.goods_id and i.warehouse_id = s.warehouse_id
						and s.inventory_upper < i.balance_count 
						and (s.inventory_upper <> 0 and s.inventory_upper is not null)
						and i.warehouse_id = '%s' ";
      $d = $db->query($sql, $warehouseId);
      $result[$i]["iuCount"] = $d[0]["cnt"];
    }

    return $result;
  }

  public function salePortal()
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $result = array();

    $db = $this->db();

    // 当月
    $sql = "select year(now()) as y, month(now()) as m";
    $data = $db->query($sql);
    $year = $data[0]["y"];
    $month = $data[0]["m"];

    for ($i = 0; $i < 6; $i++) {
      if ($month < 10) {
        $result[$i]["month"] = "$year-0$month";
      } else {
        $result[$i]["month"] = "$year-$month";
      }

      $sql = "select sum(w.sale_money) as sale_money, sum(w.profit) as profit
              from t_ws_bill w
              where w.bill_status >= 1000
                and year(w.bizdt) = %d
                and month(w.bizdt) = %d";
      $queryParams = array();
      $queryParams[] = $year;
      $queryParams[] = $month;
      $ds = new DataOrgService();
      $rs = $ds->buildSQL(FIdConst::PORTAL_SALE, "w");
      if ($rs) {
        $sql .= " and " . $rs[0];
        $queryParams = array_merge($queryParams, $rs[1]);
      }

      $data = $db->query($sql, $queryParams);
      $saleMoney = $data[0]["sale_money"];
      if (!$saleMoney) {
        $saleMoney = 0;
      }
      $profit = $data[0]["profit"];
      if (!$profit) {
        $profit = 0;
      }

      // 扣除退货
      $sql = "select sum(s.rejection_sale_money) as rej_sale_money,
                sum(s.profit) as rej_profit
              from t_sr_bill s
              where s.bill_status = 1000
                and year(s.bizdt) = %d
                and month(s.bizdt) = %d";
      $queryParams = array();
      $queryParams[] = $year;
      $queryParams[] = $month;
      $ds = new DataOrgService();
      $rs = $ds->buildSQL(FIdConst::PORTAL_SALE, "s");
      if ($rs) {
        $sql .= " and " . $rs[0];
        $queryParams = array_merge($queryParams, $rs[1]);
      }

      $data = $db->query($sql, $queryParams);
      $rejSaleMoney = $data[0]["rej_sale_money"];
      if (!$rejSaleMoney) {
        $rejSaleMoney = 0;
      }
      $rejProfit = $data[0]["rej_profit"];
      if (!$rejProfit) {
        $rejProfit = 0;
      }

      $saleMoney -= $rejSaleMoney;
      $profit += $rejProfit; // 这里是+号，因为$rejProfit是负数

      $result[$i]["saleMoney"] = $saleMoney;
      $result[$i]["profit"] = $profit;

      if ($saleMoney != 0) {
        $result[$i]["rate"] = sprintf("%0.2f", $profit / $saleMoney * 100) . "%";
      } else {
        $result[$i]["rate"] = "";
      }

      // 获得上个月
      if ($month == 1) {
        $month = 12;
        $year -= 1;
      } else {
        $month -= 1;
      }
    }

    return $result;
  }

  public function purchasePortal()
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $result = array();

    $db = $this->db();

    // 当月
    $sql = "select year(now()) as y, month(now()) as m";
    $data = $db->query($sql);
    $year = $data[0]["y"];
    $month = $data[0]["m"];

    for ($i = 0; $i < 6; $i++) {
      if ($month < 10) {
        $result[$i]["month"] = "$year-0$month";
      } else {
        $result[$i]["month"] = "$year-$month";
      }

      $sql = "select sum(w.goods_money) as goods_money
					from t_pw_bill w
					where w.bill_status >= 1000
						and year(w.biz_dt) = %d
						and month(w.biz_dt) = %d";
      $queryParams = array();
      $queryParams[] = $year;
      $queryParams[] = $month;
      $ds = new DataOrgService();
      $rs = $ds->buildSQL(FIdConst::PORTAL_PURCHASE, "w");
      if ($rs) {
        $sql .= " and " . $rs[0];
        $queryParams = array_merge($queryParams, $rs[1]);
      }

      $data = $db->query($sql, $queryParams);
      $goodsMoney = $data[0]["goods_money"];
      if (!$goodsMoney) {
        $goodsMoney = 0;
      }

      // 扣除退货
      $sql = "select sum(s.rejection_money) as rej_money
					from t_pr_bill s
					where s.bill_status = 1000
						and year(s.bizdt) = %d
						and month(s.bizdt) = %d";
      $queryParams = array();
      $queryParams[] = $year;
      $queryParams[] = $month;
      $ds = new DataOrgService();
      $rs = $ds->buildSQL(FIdConst::PORTAL_PURCHASE, "s");
      if ($rs) {
        $sql .= " and " . $rs[0];
        $queryParams = array_merge($queryParams, $rs[1]);
      }

      $data = $db->query($sql, $queryParams);
      $rejMoney = $data[0]["rej_money"];
      if (!$rejMoney) {
        $rejMoney = 0;
      }

      $goodsMoney -= $rejMoney;

      $result[$i]["purchaseMoney"] = $goodsMoney;

      // 获得上个月
      if ($month == 1) {
        $month = 12;
        $year -= 1;
      } else {
        $month -= 1;
      }
    }

    return $result;
  }

  public function moneyPortal()
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $result = array();

    $db = $this->db();
    $us = new UserService();
    $companyId = $us->getCompanyId();

    // 应收账款
    $result[0]["item"] = "应收账款";
    $sql = "select sum(balance_money) as balance_money
				from t_receivables 
				where company_id = '%s' ";
    $data = $db->query($sql, $companyId);
    $balance = $data[0]["balance_money"];
    if (!$balance) {
      $balance = 0;
    }
    $result[0]["balanceMoney"] = $balance;

    // 账龄30天内
    $sql = "select sum(balance_money) as balance_money
				from t_receivables_detail
				where datediff(current_date(), biz_date) < 30
					and company_id = '%s' ";
    $data = $db->query($sql, $companyId);
    $balance = $data[0]["balance_money"];
    if (!$balance) {
      $balance = 0;
    }
    $result[0]["money30"] = $balance;

    // 账龄30-60天
    $sql = "select sum(balance_money) as balance_money
				from t_receivables_detail
				where datediff(current_date(), biz_date) <= 60
					and datediff(current_date(), biz_date) >= 30
					and company_id = '%s' ";
    $data = $db->query($sql, $companyId);
    $balance = $data[0]["balance_money"];
    if (!$balance) {
      $balance = 0;
    }
    $result[0]["money30to60"] = $balance;

    // 账龄60-90天
    $sql = "select sum(balance_money) as balance_money
				from t_receivables_detail
				where datediff(current_date(), biz_date) <= 90
					and datediff(current_date(), biz_date) > 60
					and company_id = '%s' ";
    $data = $db->query($sql, $companyId);
    $balance = $data[0]["balance_money"];
    if (!$balance) {
      $balance = 0;
    }
    $result[0]["money60to90"] = $balance;

    // 账龄大于90天
    $sql = "select sum(balance_money) as balance_money
				from t_receivables_detail
				where datediff(current_date(), biz_date) > 90
					and company_id = '%s' ";
    $data = $db->query($sql, $companyId);
    $balance = $data[0]["balance_money"];
    if (!$balance) {
      $balance = 0;
    }
    $result[0]["money90"] = $balance;

    // 应付账款
    $result[1]["item"] = "应付账款";
    $sql = "select sum(balance_money) as balance_money
				from t_payables 
				where company_id = '%s' ";
    $data = $db->query($sql, $companyId);
    $balance = $data[0]["balance_money"];
    if (!$balance) {
      $balance = 0;
    }
    $result[1]["balanceMoney"] = $balance;

    // 账龄30天内
    $sql = "select sum(balance_money) as balance_money
				from t_payables_detail
				where datediff(current_date(), biz_date) < 30
					and company_id = '%s' ";
    $data = $db->query($sql, $companyId);
    $balance = $data[0]["balance_money"];
    if (!$balance) {
      $balance = 0;
    }
    $result[1]["money30"] = $balance;

    // 账龄30-60天
    $sql = "select sum(balance_money) as balance_money
				from t_payables_detail
				where datediff(current_date(), biz_date) <= 60
					and datediff(current_date(), biz_date) >= 30
					and company_id = '%s' ";
    $data = $db->query($sql, $companyId);
    $balance = $data[0]["balance_money"];
    if (!$balance) {
      $balance = 0;
    }
    $result[1]["money30to60"] = $balance;

    // 账龄60-90天
    $sql = "select sum(balance_money) as balance_money
				from t_payables_detail
				where datediff(current_date(), biz_date) <= 90
					and datediff(current_date(), biz_date) > 60
					and company_id = '%s' ";
    $data = $db->query($sql, $companyId);
    $balance = $data[0]["balance_money"];
    if (!$balance) {
      $balance = 0;
    }
    $result[1]["money60to90"] = $balance;

    // 账龄大于90天
    $sql = "select sum(balance_money) as balance_money
				from t_payables_detail
				where datediff(current_date(), biz_date) > 90
					and company_id = '%s' ";
    $data = $db->query($sql, $companyId);
    $balance = $data[0]["balance_money"];
    if (!$balance) {
      $balance = 0;
    }
    $result[1]["money90"] = $balance;

    return $result;
  }
}
