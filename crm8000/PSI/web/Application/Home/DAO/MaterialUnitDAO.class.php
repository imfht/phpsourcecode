<?php

namespace Home\DAO;

/**
 * 物料单位 DAO
 *
 * @author 李静波
 */
class MaterialUnitDAO extends PSIBaseExDAO
{
  /**
   * 返回所有物料单位
   *
   * @return array
   */
  public function allUnits()
  {
    $db = $this->db;

    $sql = "select id, name, code, record_status
            from t_material_unit
            order by record_status, code";

    $data = $db->query($sql);

    $result = [];

    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "code" => $v["code"],
        "name" => $v["name"],
        "recordStatus" => $v["record_status"]
      ];
    }

    return $result;
  }

  /**
   * 检查参数
   *
   * @param array $params        	
   * @return array|NULL null: 没有错误
   */
  private function checkParams($params)
  {
    $name = trim($params["name"]);

    if ($this->isEmptyStringAfterTrim($name)) {
      return $this->bad("物料单位不能为空");
    }

    if ($this->stringBeyondLimit($name, 10)) {
      return $this->bad("物料单位不能超过10位");
    }

    return null;
  }


  /**
   * 新增物料单位
   *
   * @param array $params        	
   * @return NULL|array
   */
  public function addUnit(&$params)
  {
    $db = $this->db;

    $name = trim($params["name"]);
    $code = $params["code"];
    $recordStatus = $params["recordStatus"];

    $result = $this->checkParams($params);
    if ($result) {
      return $result;
    }

    // 检查物料单位是否存在
    $sql = "select count(*) as cnt from t_material_unit where name = '%s' ";
    $data = $db->query($sql, $name);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("物料单位 [$name] 已经存在");
    }

    $dataOrg = $params["dataOrg"];
    $companyId = $params["companyId"];
    if ($this->dataOrgNotExists($dataOrg)) {
      return $this->badParam("dataOrg");
    }
    if ($this->companyIdNotExists($companyId)) {
      return $this->badParam("companyId");
    }

    $id = $this->newId();
    $params["id"] = $id;

    $sql = "insert into t_material_unit(id, name, data_org, company_id, code, record_status)
            values ('%s', '%s', '%s', '%s', '%s', %d) ";
    $rc = $db->execute($sql, $id, $name, $dataOrg, $companyId, $code, $recordStatus);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    return null;
  }

  /**
   * 编辑物料单位
   *
   * @param array $params        	
   * @return array
   */
  public function updateUnit(&$params)
  {
    $db = $this->db;

    $id = $params["id"];
    $name = trim($params["name"]);
    $code = $params["code"];
    $recordStatus = intval($params["recordStatus"]);

    $result = $this->checkParams($params);
    if ($result) {
      return $result;
    }

    // 检查物料单位是否存在
    $sql = "select count(*) as cnt from t_material_unit where name = '%s' and id <> '%s' ";
    $data = $db->query($sql, $name, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("物料单位 [$name] 已经存在");
    }

    if ($recordStatus == 2) {
      //TODO 停用物料单位的时候，需要检查是否有没有停用的物料使用该单位
      return $this->todo("停用物料单位的时候，需要检查是否有没有停用的物料使用该单位");
    } else {
      $recordStatus = 1;
    }

    $sql = "update t_material_unit 
            set name = '%s', code = '%s', record_status = %d 
            where id = '%s' ";
    $rc = $db->execute($sql, $name, $code, $recordStatus, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    return null;
  }

  /**
   * 删除商品计量单位
   */
  public function deleteUnit(&$params)
  {
    return $this->todo("在原材材、半成品、产成品这些模块开发完后再实现删除功能");
  }

  /**
   * 所有的启用的物料单位
   *
   */
  public function allEnabledUnits()
  {
    $db = $this->db;

    $sql = "select id, name
            from t_material_unit
            where record_status = 1
            order by code, name";
    $data = $db->query($sql);
    $result = [];

    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "name" => $v["name"]
      ];
    }

    return $result;
  }

  /**
   * 通过id查询物料单位
   */
  public function getMaterialUnitById($id)
  {
    $db = $this->db;

    $sql = "select name, record_status from t_material_unit where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return null;
    } else {
      return [
        "name" => $data[0]["name"],
        "recordStatus" => $data[0]["record_status"]
      ];
    }
  }
}
