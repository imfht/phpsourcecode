<?php

namespace Home\DAO;

/**
 * 商品计量单位 DAO
 *
 * @author 李静波
 */
class GoodsUnitDAO extends PSIBaseExDAO
{

  /**
   * 返回所有商品计量单位
   *
   * @return array
   */
  public function allUnits()
  {
    $db = $this->db;

    $sql = "select id, name, code, record_status
            from t_goods_unit
            order by record_status, code";

    $data = $db->query($sql);

    $result = [];

    foreach ($data as $v) {
      $sql = "select count(*) as cnt 
              from t_goods
              where unit_id = '%s' ";
      $d = $db->query($sql, $v["id"]);
      $goodsCount = $d[0]["cnt"];

      $sql = "select count(*) as cnt
              from t_goods
              where unit_id = '%s' and record_status = 1000";
      $d = $db->query($sql, $v["id"]);
      $goodsEnabledCount = $d[0]["cnt"];

      $result[] = [
        "id" => $v["id"],
        "code" => $v["code"],
        "name" => $v["name"],
        "goodsCount" => $goodsCount,
        "goodsEnabledCount" => $goodsEnabledCount,
        "goodsDisabledCount" => $goodsCount - $goodsEnabledCount,
        "recordStatus" => $v["record_status"]
      ];
    }

    return $result;
  }

  /**
   * 所有的启用的商品计量单位
   *
   * @param string $goodsId        	
   */
  public function allEnabledUnits($goodsId)
  {
    $db = $this->db;

    $sql = "select id, name
            from t_goods_unit
            where record_status = 1
            order by code, name";
    $data = $db->query($sql, $goodsId);
    $result = [];

    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "name" => $v["name"]
      ];
    }

    if ($goodsId) {
      // 如果商品的计量单位被停用了，在指定$goodsId的情况下
      // 把它的计量单位也插入到结果中
      // 这个场景用于编辑商品资料界面中，计量单位的数据来源
      $sql = "select u.id, u.name
              from t_goods g, t_goods_unit u
              where g.id = '%s' and g.unit_id = u.id and u.record_status = 2";
      $data = $db->query($sql, $goodsId);
      if ($data) {
        $v = $data[0];
        $result[] = [
          "id" => $v["id"],
          "name" => $v["name"]
        ];
      }
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
      return $this->bad("计量单位不能为空");
    }

    if ($this->stringBeyondLimit($name, 10)) {
      return $this->bad("计量单位不能超过10位");
    }

    return null;
  }

  /**
   * 新增商品计量单位
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

    // 检查计量单位是否存在
    $sql = "select count(*) as cnt from t_goods_unit where name = '%s' ";
    $data = $db->query($sql, $name);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("计量单位 [$name] 已经存在");
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

    $sql = "insert into t_goods_unit(id, name, data_org, company_id, code, record_status)
            values ('%s', '%s', '%s', '%s', '%s', %d) ";
    $rc = $db->execute($sql, $id, $name, $dataOrg, $companyId, $code, $recordStatus);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    return null;
  }

  /**
   * 编辑商品计量单位
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

    // 检查计量单位是否存在
    $sql = "select count(*) as cnt from t_goods_unit where name = '%s' and id <> '%s' ";
    $data = $db->query($sql, $name, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("计量单位 [$name] 已经存在");
    }

    if ($recordStatus == 2) {
      // 停用计量单位的时候，需要检查是否有没有停用的商品使用该计量单位
      $sql = "select count(*) as cnt
              from t_goods
              where unit_id = '%s' and record_status = 1000";
      $data = $db->query($sql, $id);
      $cnt = $data[0]["cnt"];
      if ($cnt > 0) {
        return $this->bad("仍有商品在使用计量单位[{$name}]，不能把计量单位修改为停用状态");
      }
    } else {
      $recordStatus = 1;
    }

    $sql = "update t_goods_unit 
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
   * 通过id查询商品计量单位
   *
   * @param string $id        	
   * @return array|NULL
   */
  public function getGoodsUnitById($id)
  {
    $db = $this->db;

    $sql = "select name, record_status from t_goods_unit where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return null;
    } else {
      return array(
        "name" => $data[0]["name"],
        "recordStatus" => $data[0]["record_status"]
      );
    }
  }

  /**
   * 删除商品计量单位
   *
   * @param array $params        	
   * @return NULL|array
   */
  public function deleteUnit(&$params)
  {
    $db = $this->db;

    $id = $params["id"];

    $goodsUnit = $this->getGoodsUnitById($id);
    if (!$goodsUnit) {
      return $this->bad("要删除的商品计量单位不存在");
    }

    $name = $goodsUnit["name"];

    // 检查记录单位是否被使用
    $sql = "select count(*) as cnt from t_goods where unit_id = '%s' ";
    $data = $db->query($sql, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("商品计量单位 [$name] 已经被使用，不能删除");
    }

    $sql = "delete from t_goods_unit where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $params["name"] = $name;

    // 操作成功
    return null;
  }
}
