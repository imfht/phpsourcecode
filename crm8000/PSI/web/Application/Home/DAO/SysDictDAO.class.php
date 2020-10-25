<?php

namespace Home\DAO;

/**
 * 系统数据字典DAO
 *
 * @author 李静波
 */
class SysDictDAO extends PSIBaseExDAO
{

  /**
   * 系统数据字典分类列表
   */
  public function categoryList($params)
  {
    $db = $this->db;

    $sql = "select id, code, name
            from t_dict_table_category
            order by code";
    $data = $db->query($sql);

    $result = [];
    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "code" => $v["code"],
        "name" => $v["name"]
      ];
    }

    return $result;
  }

  /**
   * 某个分类下的数据字典
   */
  public function sysDictList($params)
  {
    $db = $this->db;

    $categoryId = $params["categoryId"];

    $sql = "select id, code, name, table_name, memo
            from t_dict_table_md
            where category_id = '%s' 
            order by code";
    $data = $db->query($sql, $categoryId);

    $result = [];

    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "code" => $v["code"],
        "name" => $v["name"],
        "tableName" => $v["table_name"],
        "memo" => $v["memo"]
      ];
    }

    return $result;
  }

  /**
   * 查询某个码表的数据
   */
  public function dictDataList($params)
  {
    $db = $this->db;

    // 数据字典元数据id
    $id = $params["id"];

    $sql = "select table_name from t_dict_table_md where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return $this->emptyResult();
    }

    $tableName = $data[0]["table_name"];

    $sql = "select id, code, code_int, name, memo
            from %s 
            order by show_order";

    $data = $db->query($sql, $tableName);

    $result = [];

    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "code" => $v["code"],
        "codeInt" => $v["code_int"],
        "name" => $v["name"],
        "memo" => $v["memo"]
      ];
    }

    return $result;
  }
}
