<?php
namespace Admin\Model;
use Think\Model;

class TableModel{
    private $tablesName=array();
    private $model = null;
    private $prefix ='';

    public function __construct() {
        $this->model= new Model();
        $this->prefix=C("DB_PREFIX");
    }

    //获取全部表名
    //sql:show tables;
    public function getTablesName() {
        if(empty($this->tablesName)){
            $data = $this->model->query("SHOW TABLES");
            foreach ($data as $k => $v) {
                $tables[] = $v['tables_in_' . C("DB_NAME")];
            }
            $this->tablesName=$tables;
        }
        return $this->tablesName;
    }
    //检查表是否存在 
    public function tableExist($table) {
        $tables = $this->getTablesName();
        return in_array($this->prefix . $table, $tables) ? true : false;
    }

    //获取生成表的SQL语句
    //sql:show create table sy_admin_log;
    public function getCreateSql($table) {
        if($this->tableExist($table)){
            $result=$this->model->query("show create table ".$this->prefix.$table);
            return $result[0]['create table'];
        }else{
            return false;
        }
    }

    //获取单表结构;
    //sql:show columns from sy_admin_log;
    public function getTableColumns($table) {
        if($this->tableExist($table)){
            return $this->model->query("show columns from ".$this->prefix.$table);
        }else{
            return false;
        }
    }
    //获取单表字段
    public function getTableField($table) {
        $data = $this->getTableColumns($table);
        foreach ($data as $v) {
            $fields[$v['field']] = $v['type'];
        }
        return $fields;
    }
    //检查表中字段是否存在
    public function fieldExist($field,$table) {
        $fields = $this->getTableField($table);
        return array_key_exists($field, $fields);
    }

    //删除表
    //sql:drop table sy_admin_log
    public function dropTable($table) {
        $this->model->execute('DROP TABLE '.$this->prefix . $table);
    }
    //清空表
    //sql:truncate table sy_admin_log
    public function truncateTable($table){
        $this->model->execute('TRUNCATE TABLE '.$this->prefix . $table);
    }

}
