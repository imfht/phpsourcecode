<?php

namespace Home\DAO;

use Home\Common\FIdConst;

/**
 * 仓库 DAO
 *
 * @author 李静波
 */
class WarehouseDAO extends PSIBaseExDAO
{

  private function usageTypeCodeToName($code, $limit)
  {
    $info = " - 不启用物料限制";
    if ($limit == 1) {
      $info = " - 启用物料限制";
    }
    switch ($code) {
      case 10:
        return "原材料库{$info}";
      case 20:
        return "半成品库{$info}";
      case 30:
        return "产成品库{$info}";
      case 40:
        return "商品库{$info}";
      default:
        return "";
    }
  }

  /**
   * 获得所有的仓库列表
   *
   * @param array $params
   * @return array
   */
  public function warehouseList($params)
  {
    $db = $this->db;

    $loginUserId = $params["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->emptyResult();
    }

    $sql = "select id, code, name, inited, data_org, enabled,
              org_id, sale_area, usage_type, limit_goods 
            from t_warehouse ";
    $ds = new DataOrgDAO($db);
    $queryParams = [];
    $rs = $ds->buildSQL(FIdConst::WAREHOUSE, "t_warehouse", $loginUserId);
    if ($rs) {
      $sql .= " where " . $rs[0];
      $queryParams = $rs[1];
    }

    $sql .= " order by enabled, code";

    $result = [];
    $data = $db->query($sql, $queryParams);
    foreach ($data as $v) {
      $orgId = $v["org_id"];
      $orgName = null;
      if ($orgId) {
        $sql = "select full_name from t_org where id = '%s' ";
        $d = $db->query($sql, $orgId);
        if ($d) {
          $orgName = $d[0]["full_name"];
        }
      }
      $result[] = [
        "id" => $v["id"],
        "code" => $v["code"],
        "name" => $v["name"],
        "inited" => $v["inited"],
        "dataOrg" => $v["data_org"],
        "enabled" => $v["enabled"],
        "saleArea" => $v["sale_area"] == 0 ? null : $v["sale_area"],
        "orgId" => $orgId,
        "orgName" => $orgName,
        "usageType" => $v["usage_type"],
        "limitGoods" => $v["limit_goods"],
        "usageTypeName" => $this->usageTypeCodeToName($v["usage_type"], $v["limit_goods"]),
      ];
    }

    return $result;
  }

  /**
   * 新增一个仓库
   *
   * @param array $params
   * @return NULL|array
   */
  public function addWarehouse(&$params)
  {
    $db = $this->db;

    $code = trim($params["code"]);
    $name = trim($params["name"]);
    $py = $params["py"];
    $dataOrg = $params["dataOrg"];
    $companyId = $params["companyId"];
    $orgId = $params["orgId"] ?? "";
    $saleArea = floatval($params["saleArea"] ?? 0);
    $usageType = $params["usageType"] ?? 40;

    if ($this->dataOrgNotExists($dataOrg)) {
      return $this->bad("参数dataOrg不正确");
    }

    if ($this->companyIdNotExists($companyId)) {
      return $this->bad("参数companyId不正确");
    }

    if ($this->isEmptyStringAfterTrim($code)) {
      return $this->bad("仓库编码不能为空");
    }

    if ($this->isEmptyStringAfterTrim($name)) {
      return $this->bad("仓库名称不能为空");
    }

    // 检查同编号的仓库是否存在
    $sql = "select count(*) as cnt from t_warehouse where code = '%s' ";
    $data = $db->query($sql, $code);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("编码为 [$code] 的仓库已经存在");
    }

    if ($orgId) {
      // 检查orgId是否存在
      $orgDAO = new OrgDAO($db);
      if (!$orgDAO->getOrgById($orgId)) {
        return $this->badParam("orgId");
      }
    }

    // 销售核算面积不能为负数
    if ($saleArea < 0) {
      return $this->bad("销售核算面积不能为负数");
    }

    $limitGoods = $params["limitGoods"];
    if ($limitGoods == "1" || $limitGoods == "0") {
      // do nothing
    } else {
      // 错误的值，设置为默认值0
      $limitGoods = "0";
    }

    $id = $this->newId();
    $params["id"] = $id;

    $sql = "insert into t_warehouse(id, code, name, inited, py, data_org, 
              company_id, org_id, sale_area, usage_type, limit_goods)
            values ('%s', '%s', '%s', 0, '%s', '%s', '%s', '%s', %f, %d, %d)";
    $rc = $db->execute(
      $sql,
      $id,
      $code,
      $name,
      $py,
      $dataOrg,
      $companyId,
      $orgId,
      $saleArea,
      $usageType,
      $limitGoods
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    return null;
  }

  /**
   * 修改仓库
   *
   * @param array $params
   * @return NULL|array
   */
  public function updateWarehouse(&$params)
  {
    $id = $params["id"];
    $code = trim($params["code"]);
    $name = trim($params["name"]);
    $py = $params["py"];
    $enabled = intval($params["enabled"]);
    if ($enabled != 1) {
      // 仓库的状态只能是 1 和 2
      $enabled = 2;
    }
    $orgId = $params["orgId"] ?? "";
    $saleArea = floatval($params["saleArea"] ?? 0);
    $usageType = $params["usageType"] ?? 40;

    if ($this->isEmptyStringAfterTrim($code)) {
      return $this->bad("仓库编码不能为空");
    }

    if ($this->isEmptyStringAfterTrim($name)) {
      return $this->bad("仓库名称不能为空");
    }

    $limitGoods = $params["limitGoods"];
    if ($limitGoods == "1" || $limitGoods == "0") {
      // do nothing
    } else {
      // 错误的值，设置为默认值0
      $limitGoods = "0";
    }

    $db = $this->db;

    // 检查同编号的仓库是否存在
    $sql = "select count(*) as cnt from t_warehouse where code = '%s' and id <> '%s' ";
    $data = $db->query($sql, $code, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("编码为 [$code] 的仓库已经存在");
    }

    $warehouse = $this->getWarehouseById($id);
    if (!$warehouse) {
      return $this->bad("要编辑的仓库不存在");
    }

    if ($orgId) {
      // 检查orgId是否存在
      $orgDAO = new OrgDAO($db);
      if (!$orgDAO->getOrgById($orgId)) {
        return $this->badParam("orgId");
      }
    }

    // 销售核算面积不能为负数
    if ($saleArea < 0) {
      return $this->bad("销售核算面积不能为负数");
    }

    $sql = "update t_warehouse
            set code = '%s', name = '%s', py = '%s',
              enabled = %d, org_id = '%s', sale_area = %f,
              usage_type = %d, limit_goods = %d
            where id = '%s' ";
    $rc = $db->execute($sql, $code, $name, $py, $enabled, $orgId, $saleArea, $usageType, $limitGoods, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    return null;
  }

  /**
   * 删除仓库
   *
   * @param array $params
   * @return NULL|array
   */
  public function deleteWarehouse(&$params)
  {
    $db = $this->db;

    $id = $params["id"];

    // 判断仓库是否能删除
    $warehouse = $this->getWarehouseById($id);
    if (!$warehouse) {
      return $this->bad("要删除的仓库不存在");
    }
    $params["code"] = $warehouse["code"];
    $params["name"] = $warehouse["name"];

    $warehouseName = $warehouse["name"];
    if ($warehouse["inited"] == 1) {
      return $this->bad("仓库[{$warehouseName}]已经建账，不能删除");
    }

    // 判断仓库是否在采购入库单中使用
    $sql = "select count(*) as cnt from t_pw_bill where warehouse_id = '%s' ";
    $data = $db->query($sql, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("仓库[$warehouseName]已经在采购入库单中使用，不能删除");
    }

    // 判断仓库是否在采购退货出库单中使用
    $sql = "select count(*) as cnt from t_pr_bill where warehouse_id = '%s' ";
    $data = $db->query($sql, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("仓库[$warehouseName]已经在采购退货出库单中使用，不能删除");
    }

    // 判断仓库是否在销售出库单中使用
    $sql = "select count(*) as cnt from t_ws_bill where warehouse_id = '%s' ";
    $data = $db->query($sql, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("仓库[$warehouseName]已经在销售出库单中使用，不能删除");
    }

    // 判断仓库是否在销售退货入库单中使用
    $sql = "select count(*) as cnt from t_sr_bill where warehouse_id = '%s' ";
    $data = $db->query($sql, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("仓库[$warehouseName]已经在销售退货入库单中使用，不能删除");
    }

    // 判断仓库是否在调拨单中使用
    $sql = "select count(*) as cnt from t_it_bill
            where from_warehouse_id = '%s' or to_warehouse_id = '%s' ";
    $data = $db->query($sql, $id, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("仓库[$warehouseName]已经在调拨单中使用，不能删除");
    }

    // 判断仓库是否在盘点单中使用
    $sql = "select count(*) as cnt from t_ic_bill where warehouse_id = '%s' ";
    $data = $db->query($sql, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("仓库[$warehouseName]已经在盘点单中使用，不能删除");
    }

    // 判断仓库是否在业务设置中使用
    $sql = "select o.name
            from t_config c, t_org o
            where c.company_id = o.id
              and c.value = '%s' ";
    $data = $db->query($sql, $id);
    if ($data) {
      $companyName = $data[0]["name"];
      return $this->bad("仓库[$warehouseName]已经在公司[$companyName]的业务设置中使用，不能删除");
    }

    $sql = "delete from t_warehouse where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    return null;
  }

  /**
   * 通过仓库id查询仓库
   *
   * @param string $id
   * @return array|NULL
   */
  public function getWarehouseById($id)
  {
    $db = $this->db;
    $sql = "select code, name, data_org, inited from t_warehouse where id = '%s' ";
    $data = $db->query($sql, $id);

    if (!$data) {
      return null;
    }

    return array(
      "code" => $data[0]["code"],
      "name" => $data[0]["name"],
      "dataOrg" => $data[0]["data_org"],
      "inited" => $data[0]["inited"]
    );
  }

  /**
   * 编辑仓库数据域
   *
   * @param array $params
   * @return NULL|array
   */
  public function editDataOrg(&$params)
  {
    $db = $this->db;

    $id = $params["id"];
    $dataOrg = $params["dataOrg"];

    $sql = "select name, data_org from t_warehouse where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return $this->bad("要编辑数据域的仓库不存在");
    }

    $name = $data[0]["name"];
    $oldDataOrg = $data[0]["data_org"];
    if ($oldDataOrg == $dataOrg) {
      return $this->bad("[{$name}]的数据域没有改动，不用保存");
    }

    // 检查新数据域是否存在
    $sql = "select count(*) as cnt from t_user where data_org = '%s' ";
    $data = $db->query($sql, $dataOrg);
    $cnt = $data[0]["cnt"];
    if ($cnt != 1) {
      return $this->bad("数据域[{$dataOrg}]不存在");
    }

    $sql = "update t_warehouse
            set data_org = '%s'
            where id = '%s' ";
    $rc = $db->execute($sql, $dataOrg, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    return null;
  }

  /**
   * 查询数据，用于仓库自定义字段
   *
   * @param array $params
   * @return array
   */
  public function queryData($params)
  {
    $db = $this->db;

    $loginUserId = $params["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->emptyResult();
    }

    $queryKey = $params["queryKey"];
    if ($queryKey == null) {
      $queryKey = "";
    }

    $sql = "select id, code, name from t_warehouse
            where (enabled = 1) 
              and (code like '%s' or name like '%s' or py like '%s' ) ";
    $key = "%{$queryKey}%";
    $queryParams = [];
    $queryParams[] = $key;
    $queryParams[] = $key;
    $queryParams[] = $key;

    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::WAREHOUSE_BILL, "t_warehouse", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = array_merge($queryParams, $rs[1]);
    }

    $sql .= " order by code";

    return $db->query($sql, $queryParams);
  }
}
