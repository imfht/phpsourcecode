<?php

namespace Home\Service;

use Home\Common\FIdConst;
use Home\DAO\BizConfigDAO;

/**
 * 库存建账Service
 *
 * @author 李静波
 */
class InitInventoryService extends PSIBaseExService
{
  private $LOG_CATEGORY = "库存建账";

  /**
   * 获得仓库列表
   */
  public function warehouseList()
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $sql = "select id, code, name, inited from t_warehouse ";
    $queryParams = array();

    $ds = new DataOrgService();
    $rs = $ds->buildSQL(FIdConst::INVENTORY_INIT, "t_warehouse");
    if ($rs) {
      $sql .= " where " . $rs[0];
      $queryParams = $rs[1];
    }

    $sql .= "order by code";

    return M()->query($sql, $queryParams);
  }

  /**
   * 某个仓库的建账信息
   */
  public function initInfoList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $warehouseId = $params["warehouseId"];
    $start = $params["start"];
    $limit = $params["limit"];

    $db = M();

    $companyId = $this->getCompanyId();
    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $sql = "select v.id, g.code, g.name, g.spec, convert(v.balance_count, $fmt) as balance_count, v.balance_price, 
				v.balance_money, u.name as unit_name, v.biz_date 
				from t_inventory_detail v, t_goods g, t_goods_unit u 
				where v.goods_id = g.id and g.unit_id = u.id and v.warehouse_id = '%s' 
				and ref_type = '库存建账' 
				order by g.code 
				limit " . $start . ", " . $limit;
    $data = $db->query($sql, $warehouseId);
    $result = [];
    foreach ($data as $i => $v) {
      $result[$i]["id"] = $v["id"];
      $result[$i]["goodsCode"] = $v["code"];
      $result[$i]["goodsName"] = $v["name"];
      $result[$i]["goodsSpec"] = $v["spec"];
      $result[$i]["goodsCount"] = $v["balance_count"];
      $result[$i]["goodsUnit"] = $v["unit_name"];
      $result[$i]["goodsMoney"] = $v["balance_money"];
      $result[$i]["goodsPrice"] = $v["balance_price"];
      $result[$i]["initDate"] = date("Y-m-d", strtotime($v["biz_date"]));
    }

    $sql = "select count(*) as cnt from t_inventory_detail 
				where warehouse_id = '%s' and ref_type = '库存建账' ";
    $data = $db->query($sql, $warehouseId);

    return array(
      "initInfoList" => $result,
      "totalCount" => $data[0]["cnt"]
    );
  }

  /**
   * 获得商品分类列表
   */
  public function goodsCategoryList()
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $sql = "select id, code, name, full_name from t_goods_category ";
    $queryParams = array();

    $ds = new DataOrgService();
    $rs = $ds->buildSQL(FIdConst::INVENTORY_INIT, "t_goods_category");
    if ($rs) {
      $sql .= " where " . $rs[0];
      $queryParams = $rs[1];
    }

    $sql .= " order by code";

    $result = array();
    $db = M();
    $data = $db->query($sql, $queryParams);
    foreach ($data as $i => $v) {
      $result[$i]["id"] = $v["id"];
      $result[$i]["code"] = $v["code"];
      $fullName = $v["full_name"];
      if (!$fullName) {
        $fullName = $v["name"];
      }
      $result[$i]["name"] = $fullName;
    }

    return $result;
  }

  /**
   * 获得某个分类下的商品列表
   */
  public function goodsList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $warehouseId = $params["warehouseId"];
    $categoryId = $params["categoryId"];
    $start = $params["start"];
    $limit = $params["limit"];

    $db = M();

    $companyId = $this->getCompanyId();

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    $sql = "select g.id, g.code, g.name, g.spec, convert(v.balance_count, $fmt) as balance_count, v.balance_price, 
				v.balance_money, u.name as unit_name, v.biz_date 
				from t_goods g inner join t_goods_unit u 
				on g.unit_id = u.id and g.category_id = '%s' 
				left join t_inventory_detail v
				on g.id = v.goods_id and v.ref_type = '库存建账' 
				and v.warehouse_id = '%s' 
				order by g.code 
				limit " . $start . ", " . $limit;
    $data = $db->query($sql, $categoryId, $warehouseId);
    $result = [];
    foreach ($data as $i => $v) {
      $result[$i]["id"] = $v["id"];
      $result[$i]["goodsCode"] = $v["code"];
      $result[$i]["goodsName"] = $v["name"];
      $result[$i]["goodsSpec"] = $v["spec"];
      $result[$i]["goodsCount"] = $v["balance_count"];
      $result[$i]["unitName"] = $v["unit_name"];
      $result[$i]["goodsMoney"] = $v["balance_money"];
      $result[$i]["goodsPrice"] = $v["balance_price"];
      $result[$i]["initDate"] = $v["biz_date"];
    }

    $sql = "select count(*) as cnt from t_goods where category_id = '%s' ";
    $data = $db->query($sql, $categoryId);

    return array(
      "goodsList" => $result,
      "totalCount" => $data[0]["cnt"]
    );
  }

  private function deleteInitInventoryGoods($params)
  {
    $warehouseId = $params["warehouseId"];
    $goodsId = $params["goodsId"];

    $db = M();
    $db->startTrans();

    $sql = "select name, inited from t_warehouse where id = '%s' ";
    $data = $db->query($sql, $warehouseId);
    if (!$data) {
      $db->rollback();
      return $this->bad("仓库不存在");
    }
    if ($data[0]["inited"] != 0) {
      $db->rollback();
      return $this->bad("仓库 [{$data[0]["name"]}] 已经建账完成，不能再次建账");
    }

    $sql = "select name from t_goods where id = '%s' ";
    $data = $db->query($sql, $goodsId);
    if (!$data) {
      $db->rollback();
      return $this->bad("商品不存在");
    }
    $sql = "select count(*) as cnt from t_inventory_detail
				where warehouse_id = '%s' and goods_id = '%s' and ref_type <> '库存建账' ";
    $data = $db->query($sql, $warehouseId, $goodsId);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      $db->rollback();
      return $this->bad("当前商品已经有业务发生，不能再建账");
    }

    // 清除明细账中记录
    $sql = "delete from t_inventory_detail
				where warehouse_id = '%s' and goods_id = '%s' ";
    $rc = $db->execute($sql, $warehouseId, $goodsId);
    if ($rc === false) {
      $db->rollback();
      return $this->sqlError(__LINE__);
    }

    // 清除总账中记录
    $sql = "delete from t_inventory
				where warehouse_id = '%s' and goods_id = '%s' ";
    $rc = $db->execute($sql, $warehouseId, $goodsId);
    if ($rc === false) {
      $db->rollback();
      return $this->sqlError(__LINE__);
    }

    $db->commit();

    return $this->ok();
  }

  /**
   * 提交建账信息
   */
  public function commitInitInventoryGoods($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $warehouseId = $params["warehouseId"];
    $goodsId = $params["goodsId"];
    $goodsCount = floatval($params["goodsCount"]);
    $goodsMoney = floatval($params["goodsMoney"]);

    if ($goodsCount < 0) {
      return $this->bad("期初数量不能为负数");
    }

    if ($goodsMoney < 0) {
      return $this->bad("期初金额不能为负数");
    }

    if (abs($goodsCount) <= 0.000000001) {
      return $this->deleteInitInventoryGoods($params);
    }

    $goodsPrice = $goodsMoney / $goodsCount;

    $db = M();
    $db->startTrans();

    $sql = "select name, inited from t_warehouse where id = '%s' ";
    $data = $db->query($sql, $warehouseId);
    if (!$data) {
      $db->rollback();
      return $this->bad("仓库不存在");
    }
    if ($data[0]["inited"] != 0) {
      $db->rollback();
      return $this->bad("仓库 [{$data[0]["name"]}] 已经建账完成，不能再次建账");
    }

    $sql = "select name from t_goods where id = '%s' ";
    $data = $db->query($sql, $goodsId);
    if (!$data) {
      $db->rollback();
      return $this->bad("商品不存在");
    }
    $sql = "select count(*) as cnt from t_inventory_detail 
				where warehouse_id = '%s' and goods_id = '%s' and ref_type <> '库存建账' ";
    $data = $db->query($sql, $warehouseId, $goodsId);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      $db->rollback();
      return $this->bad("当前商品已经有业务发生，不能再建账");
    }

    $us = new UserService();
    $dataOrg = $us->getLoginUserDataOrg();

    $companyId = $this->getCompanyId();

    $bcDAO = new BizConfigDAO($db);
    $dataScale = $bcDAO->getGoodsCountDecNumber($companyId);
    $fmt = "decimal(19, " . $dataScale . ")";

    // 总账
    $sql = "select id from t_inventory where warehouse_id = '%s' and goods_id = '%s' ";
    $data = $db->query($sql, $warehouseId, $goodsId);
    if (!$data) {
      $sql = "insert into t_inventory (warehouse_id, goods_id, in_count, in_price, 
						in_money, balance_count, balance_price, balance_money, data_org) 
						values ('%s', '%s', convert(%f, $fmt), %f, %f, convert(%f, $fmt), %f, %f, '%s') ";
      $rc = $db->execute(
        $sql,
        $warehouseId,
        $goodsId,
        $goodsCount,
        $goodsPrice,
        $goodsMoney,
        $goodsCount,
        $goodsPrice,
        $goodsMoney,
        $dataOrg
      );
      if ($rc === false) {
        $db->rollback();
        return $this->sqlError(__LINE__);
      }
    } else {
      $id = $data[0]["id"];
      $sql = "update t_inventory  
						set in_count = convert(%f, $fmt), in_price = %f, in_money = %f, 
						balance_count = convert(%f, $fmt), balance_price = %f, balance_money = %f 
						where id = %d ";
      $rc = $db->execute(
        $sql,
        $goodsCount,
        $goodsPrice,
        $goodsMoney,
        $goodsCount,
        $goodsPrice,
        $goodsMoney,
        $id
      );
      if ($rc === false) {
        $db->rollback();
        return $this->sqlError(__LINE__);
      }
    }

    // 明细账
    $sql = "select id from t_inventory_detail  
					where warehouse_id = '%s' and goods_id = '%s' and ref_type = '库存建账' ";
    $data = $db->query($sql, $warehouseId, $goodsId);
    if (!$data) {
      $sql = "insert into t_inventory_detail (warehouse_id, goods_id,  in_count, in_price,
						in_money, balance_count, balance_price, balance_money,
						biz_date, biz_user_id, date_created,  ref_number, ref_type, data_org)
						values ('%s', '%s', convert(%f, $fmt), %f, %f, convert(%f, $fmt), 
								%f, %f, curdate(), '%s', now(), '', '库存建账', '%s')";
      $rc = $db->execute(
        $sql,
        $warehouseId,
        $goodsId,
        $goodsCount,
        $goodsPrice,
        $goodsMoney,
        $goodsCount,
        $goodsPrice,
        $goodsMoney,
        $us->getLoginUserId(),
        $dataOrg
      );
      if ($rc === false) {
        $db->rollback();
        return $this->sqlError(__LINE__);
      }
    } else {
      $id = $data[0]["id"];
      $sql = "update t_inventory_detail 
						set in_count = convert(%f, $fmt), in_price = %f, in_money = %f,
						balance_count = convert(%f, $fmt), balance_price = %f, balance_money = %f,
						biz_date = curdate()  
						where id = %d ";
      $rc = $db->execute(
        $sql,
        $goodsCount,
        $goodsPrice,
        $goodsMoney,
        $goodsCount,
        $goodsPrice,
        $goodsMoney,
        $id
      );
      if ($rc === false) {
        $db->rollback();
        return $this->sqlError(__LINE__);
      }
    }

    $db->commit();

    return $this->ok();
  }

  /**
   * 完成建账
   */
  public function finish($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $warehouseId = $params["warehouseId"];

    $db = M();
    $db->startTrans();

    $sql = "select name, inited from t_warehouse where id = '%s' ";
    $data = $db->query($sql, $warehouseId);
    if (!$data) {
      $db->rollback();
      return $this->bad("仓库不存在");
    }
    $inited = $data[0]["inited"];
    $name = $data[0]["name"];
    if ($inited == 1) {
      $db->rollback();
      return $this->bad("仓库 [{$name}] 已经建账完毕");
    }

    $sql = "update t_warehouse set inited = 1 where id = '%s' ";
    $rc = $db->execute($sql, $warehouseId);
    if ($rc === false) {
      $db->rollback();
      return $this->sqlError(__LINE__);
    }

    $sql = "update t_inventory_detail set biz_date = curdate() 
					where warehouse_id = '%s' and ref_type = '库存建账' ";
    $rc = $db->execute($sql, $warehouseId);
    if ($rc === false) {
      $db->rollback();
      return $this->sqlError(__LINE__);
    }

    $log = "仓库 [{$name}] 建账完毕";
    $bs = new BizlogService();
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 取消库存建账标志
   */
  public function cancel($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $warehouseId = $params["warehouseId"];

    $db = M();
    $db->startTrans();

    $sql = "select name, inited from t_warehouse where id = '%s' ";
    $data = $db->query($sql, $warehouseId);
    if (!$data) {
      $db->rollback();
      return $this->bad("仓库不存在");
    }
    $inited = $data[0]["inited"];
    $name = $data[0]["name"];
    if ($inited != 1) {
      $db->rollback();
      return $this->bad("仓库 [{$name}] 还没有标记为建账完毕，无需取消建账标志");
    }
    $sql = "select count(*) as cnt from t_inventory_detail 
				where warehouse_id = '%s' and ref_type <> '库存建账' ";
    $data = $db->query($sql, $warehouseId);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      $db->rollback();
      return $this->bad("仓库 [{$name}] 中已经发生出入库业务，不能再取消建账标志");
    }

    $sql = "update t_warehouse set inited = 0 where id = '%s' ";
    $rc = $db->execute($sql, $warehouseId);
    if ($rc === false) {
      $db->rollback();
      return $this->sqlError(__LINE__);
    }

    $log = "仓库 [{$name}] 取消建账完毕标志";
    $bs = new BizlogService();
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }
}
