<?php

namespace Home\DAO;

use Home\Common\FIdConst;

/**
 * 商品安全库存 DAO
 *
 * @author 李静波
 */
class GoodsSiDAO extends PSIBaseExDAO
{

  /**
   * 获得某个商品的安全库存列表
   *
   * @param array $params        	
   * @return array
   */
  public function goodsSafetyInventoryList($params)
  {
    $db = $this->db;

    $id = $params["id"];
    $loginUserId = $params["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->emptyResult();
    }

    $result = array();

    $sql = "select u.name
            from t_goods g, t_goods_unit u
            where g.id = '%s' and g.unit_id = u.id";
    $data = $db->query($sql, $id);
    if (!$data) {
      return $result;
    }
    $goodsUnitName = $data[0]["name"];

    $sql = "select w.id as warehouse_id, w.code as warehouse_code, w.name as warehouse_name,
              s.safety_inventory, s.inventory_upper
            from t_warehouse w
            left join t_goods_si s
              on w.id = s.warehouse_id and s.goods_id = '%s'
            where w.inited = 1 ";
    $queryParams = array();
    $queryParams[] = $id;
    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::GOODS, "w", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = array_merge($queryParams, $rs[1]);
    }
    $sql .= " order by w.code";
    $data = $db->query($sql, $queryParams);
    $r = array();
    foreach ($data as $i => $v) {
      $r[$i]["warehouseId"] = $v["warehouse_id"];
      $r[$i]["warehouseCode"] = $v["warehouse_code"];
      $r[$i]["warehouseName"] = $v["warehouse_name"];
      $r[$i]["safetyInventory"] = $v["safety_inventory"];
      $r[$i]["inventoryUpper"] = $v["inventory_upper"];
      $r[$i]["unitName"] = $goodsUnitName;
    }

    foreach ($r as $i => $v) {
      $sql = "select balance_count
              from t_inventory
              where warehouse_id = '%s' and goods_id = '%s' ";
      $data = $db->query($sql, $v["warehouseId"], $id);
      if (!$data) {
        $result[$i]["inventoryCount"] = 0;
      } else {
        $result[$i]["inventoryCount"] = $data[0]["balance_count"];
      }

      $result[$i]["warehouseCode"] = $v["warehouseCode"];
      $result[$i]["warehouseName"] = $v["warehouseName"];
      $result[$i]["safetyInventory"] = $v["safetyInventory"];
      $result[$i]["inventoryUpper"] = $v["inventoryUpper"];
      $result[$i]["unitName"] = $goodsUnitName;
    }

    return $result;
  }

  /**
   * 获得某个商品安全库存的详情
   *
   * @param array $params        	
   * @return array
   */
  public function siInfo($params)
  {
    $db = $this->db;

    $id = $params["id"];
    $loginUserId = $params["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->emptyResult();
    }

    $result = array();

    $sql = "select u.name
            from t_goods g, t_goods_unit u
            where g.id = '%s' and g.unit_id = u.id";
    $data = $db->query($sql, $id);
    if (!$data) {
      return $result;
    }
    $goodsUnitName = $data[0]["name"];

    $sql = "select w.id as warehouse_id, w.code as warehouse_code,
              w.name as warehouse_name,
              s.safety_inventory, s.inventory_upper
            from t_warehouse w
            left join t_goods_si s
              on w.id = s.warehouse_id and s.goods_id = '%s'
            where w.inited = 1 ";
    $queryParams = array();
    $queryParams[] = $id;

    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::GOODS, "w", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = array_merge($queryParams, $rs[1]);
    }

    $sql .= " order by w.code ";
    $data = $db->query($sql, $queryParams);
    foreach ($data as $i => $v) {
      $result[$i]["warehouseId"] = $v["warehouse_id"];
      $result[$i]["warehouseCode"] = $v["warehouse_code"];
      $result[$i]["warehouseName"] = $v["warehouse_name"];
      $result[$i]["safetyInventory"] = $v["safety_inventory"] ? $v["safety_inventory"] : 0;
      $result[$i]["inventoryUpper"] = $v["inventory_upper"] ? $v["inventory_upper"] : 0;
      $result[$i]["unitName"] = $goodsUnitName;
    }

    return $result;
  }

  /**
   * 设置商品的安全
   *
   * @param array $bill        	
   * @return NULL|array
   */
  public function editSafetyInventory(&$bill)
  {
    $db = $this->db;

    $id = $bill["id"];
    $items = $bill["items"];

    $goodsDAO = new GoodsDAO($db);
    $goods = $goodsDAO->getGoodsById($id);

    if (!$goods) {
      return $this->bad("商品不存在，无法设置商品安全库存");
    }

    $sql = "delete from t_goods_si where goods_id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $warehouseDAO = new WarehouseDAO($db);

    foreach ($items as $v) {
      $warehouseId = $v["warehouseId"];
      if (!$warehouseDAO->getWarehouseById($warehouseId)) {
        return $this->bad("仓库不存在(id={$warehouseId})");
      }

      $si = $v["si"];
      if (!$si) {
        $si = 0;
      }
      if ($si < 0) {
        $si = 0;
      }
      $upper = $v["invUpper"];
      if (!$upper) {
        $upper = 0;
      }
      if ($upper < 0) {
        $upper = 0;
      }

      $sql = "insert into t_goods_si(id, goods_id, warehouse_id, safety_inventory, inventory_upper)
              values ('%s', '%s', '%s', %d, %d)";
      $rc = $db->execute($sql, $this->newId(), $id, $warehouseId, $si, $upper);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    $bill["code"] = $goods["code"];
    $bill["name"] = $goods["name"];
    $bill["spec"] = $goods["spec"];

    // 操作成功
    return null;
  }
}
