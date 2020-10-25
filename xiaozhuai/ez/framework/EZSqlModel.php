<?php

/**
 * Created by PhpStorm.
 * User: xiaozhuai
 * Date: 16/12/14
 * Time: 下午6:45
 */
class EZSqlModel extends EZModel
{


    protected $pdo;
    protected $table;

    function __construct($_table=""){
        $this->table = $_table;
        $this->pdo = EZSqlPdo::getInstance()->pdo;
    }

    public function getPDO(){
        return $this->pdo;
    }

    public function getTable(){
        return $this->table;
    }

    public function setTable($_table){
        $this->table = $_table;
    }

    public function getErrInfo(){
        return $this->pdo->errorInfo();
    }

    public function checkErr($errMsg){
        $errInfo = $this->getErrInfo();
        if($errInfo[0]!="00000"){
            EZErr::err(500, sprintf("pdo err, SQLSTATE[%s] [%s] %s, msg: %s", $errInfo[0], $errInfo[1], $errInfo[2], $errMsg));
        }
    }

    public function normalizeVal(&$val){
        $tmpArr = explode(":", EZConfig()->PDO_DB_DSN);
        $databaseType = $tmpArr[0];
        switch ($databaseType){
            case "mysql":
            default:
                $val = str_replace("'", "''", $val);
                $val = str_replace("\\", "\\\\", $val);
                break;
            case "sqlite":
                $val = str_replace("'", "''", $val);
                break;
        }
    }

    public function insert($row, $useReplace=false) {
        if ( !is_array($row) || count($row)==0 ) {
            EZErr::err(500, "pdo err, insert row failed, row data is not an array or it's length is 0");
        }
        $fields = "";
        $vals   = "";
        foreach ($row as $k => $v){
            $fields .= "`$k`,";
            if(is_null($v)){
                $vals   .= "null,";
            }else if (is_int($v)){
                $vals   .= "$v,";
            }else{
                $this->normalizeVal($v);
                $vals   .= "'$v',";
            }
        }
        $fields = rtrim($fields, ',');
        $vals   = rtrim($vals,   ',');
        $sql = ($useReplace ? "REPLACE" : "INSERT")." INTO `{$this->table}` ({$fields}) VALUES ({$vals})";
        $this->pdo->exec($sql);
        $this->checkErr("pdo err, insert row failed, row data: \n" . var_export($row, true));
        return $this->pdo->lastInsertId();
    }

    public function update($row = null, $condition = null) {
        if ( !is_array($row) || count($row)==0) {
            EZErr::err(500, "pdo err, update rows failed, row data is not an array or it's length is 0");
        }
        $upStr = '';
        foreach ($row as $k => $v) {
            if(is_null($v)){
                $upStr .= "`$k`=null,";
            }else if (is_int($v)){
                $upStr .= "`$k`=$v,";
            }else{
                $this->normalizeVal($v);
                $upStr .= "`$k`='$v',";
            }
        }
        $upStr = rtrim($upStr, ',');
        $whereStr = empty($condition)?"":"WHERE {$condition}";
        $sql = "UPDATE `{$this->table}` SET {$upStr} {$whereStr}";
        $affected = $this->pdo->exec($sql);
        $this->checkErr("pdo err, update rows failed, row data: \n" . var_export($row, true));
        return $affected;
    }

    public function del($condition = null) {
        $whereStr = empty($condition)?"":"WHERE {$condition}";
        $sql = "DELETE FROM `{$this->table}` {$whereStr}";
        $affected = $this->pdo->exec($sql);
        $this->checkErr("pdo err, del rows failed");
        return $affected;
    }

    public function total($condition = null) {
        $whereStr = empty($condition)?"":"WHERE {$condition}";
        $sql = "SELECT COUNT(*) as total FROM `{$this->table}` {$whereStr}";
        $result = $this->pdo->query($sql);
        $result = $result->fetch();
        $this->checkErr("pdo err, get rows total counts failed");
        return $result['total'];
    }

    public function exists($condition=null) {
        if ($this->total($condition)) {
            return true;
        } else {
            return false;
        }
    }

    public function getAllRows($fields = '*', $condition = null, $order = null, $limit = -1) {
        $whereStr = empty($condition)?"":"WHERE {$condition}";
        $orderStr = empty($order)?"":"ORDER BY {$order}";
        $limitStr = ($limit==-1)?"":"LIMIT {$limit}";
        $sql = "SELECT {$fields} FROM `{$this->table}` {$whereStr} {$orderStr} {$limitStr}";
        $result = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $this->checkErr("pdo err, get all rows failed");
        return (count($result)==0)?null:$result;
    }

    public function getOneRow($fields = "*", $condition = null, $order = null) {
        $result = $this->getAllRows($fields, $condition, $order, 1);
        return isset($result[0])?$result[0]:null;
    }

    public function getAllRowsOneField($field, $condition = null, $order = null, $limit = -1){
        if(empty($field)){
            EZErr::err(500, "pdo err, get all rows one field failed, field must not be empty");
        }
        if(strpos($field, ",")){
            EZErr::err(500, "pdo err, get all rows one field failed, field must only contains one field");
        }
        $result = $this->getAllRows($field, $condition, $order, $limit);
        if(isset($result[0])){      // size > 0
            $keys = array_keys($result[0]);
            if(isset($keys[0])){
                $key = $keys[0];
                $tmp = array();
                $counts = count($result);
                for($i=0; $i<$counts; $i++){
                    array_push($tmp, $result[$i][$key]);
                }
                return $tmp;
            }else{                  // no field
                return null;
            }
        }else{                      // size == 0
            return null;
        }
    }

    public function getOneRowOneField($field, $condition = null, $order = null){
        if(empty($field)){
            EZErr::err(500, "pdo err, get one row one field failed, field must not be empty");
        }
        if(strpos($field, ",")){
            EZErr::err(500, "pdo err, get one row one field failed, field must only contains one field");
        }
        $result = $this->getOneRow($field, $condition, $order);
        if(count($result)>0){      // size > 0
            $keys = array_keys($result);
            if(isset($keys[0])){
                $key = $keys[0];
                $tmp = $result[$key];
                return $tmp;
            }else{                  // no field
                return null;
            }
        }else{                      // size == 0
            return null;
        }
    }

}