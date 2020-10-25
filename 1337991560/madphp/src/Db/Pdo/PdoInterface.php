<?php

namespace Madphp\Db\Pdo;

interface PdoInterface
{
    public function insert($sql, array $parameterMap = array(), array $sqlMap = array());
    
    public function update($sql, $sqlMap = array(), $parameterMap = array());
    
    public function delete($sql, $sqlMap = array(), $parameterMap = array());
    
    public function query($sql);
    
    public function exec($sql);
    
    public function fetchAll($sql, $sqlMap = array(), $parameterMap = array());
    
    public function fetchOne($sql, $sqlMap = array(), $parameterMap = array());
    
    public function fetchColumn($sql, $columnNumber = 0, $sqlMap = array(), $parameterMap = array());
    
    public function lastInsertId($name = null);
    
    public function nextRowset();
    
    public function rowCount();
    
    public function status();
    
    public function statementErrorCode();
    
    public function statementErrorInfo();
    
    public function pdoErrorCode();
    
    public function pdoErrorInfo();
    
    public function setFetchStyle($fetchStyle);
    
    public function getFetchStyle();
    
    public function inTransaction();
    
    public function beginTransaction();
    
    public function commit();
    
    public function rollBack();
    
    public function debug($sql, $sqlMap = array(), $parameterMap = array());

    public function setDebug($is);
}