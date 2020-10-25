<?php

namespace Home\DAO;

/**
 * 数据库操作助手 DAO
 *
 * @author 李静波
 */
class DBUtilDAO extends PSIBaseExDAO
{

  /**
   * 判断表是否存在
   * 
   * @param string $tableName        	
   * @return boolean true - 表存在
   */
  public function tableExists($tableName)
  {
    $db = $this->db;
    $dbName = C('DB_NAME');
    $sql = "select count(*) as cnt
            from information_schema.columns
            where table_schema = '%s'
              and table_name = '%s' ";
    $data = $db->query($sql, $dbName, $tableName);
    return $data[0]["cnt"] != 0;
  }

  /**
   * 判断表的列是否存在
   * 
   * @param string $tableName        	
   * @param string $columnName        	
   * @return boolean true - 列存在
   */
  public function columnExists($tableName, $columnName)
  {
    $db = $this->db;

    $dbName = C('DB_NAME');

    $sql = "select count(*) as cnt
            from information_schema.columns
            where table_schema = '%s'
              and table_name = '%s'
              and column_name = '%s' ";
    $data = $db->query($sql, $dbName, $tableName, $columnName);
    $cnt = $data[0]["cnt"];
    return $cnt == 1;
  }
}
