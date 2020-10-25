<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\dao;

use nb\Pool;
use PDO;

/**
 * Mysql
 *
 * @package nb\dao
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/11/27
 *
 */
class Mysql extends Driver {

    protected $server = [
        'driver'	=> 'mysql',
        'host' 		=> 'localhost',
        'port' 		=> '3306',
        'user' 		=> 'root',
        'pass' 		=> '123456',
        'connect'   => 'false',
        'host'      => '127.0.0.1',
        'charset' 	=> 'UTF8',
        'prefix'    => '', // 数据库表前缀
        'object'    => false,
    ];

    /**
     * 设置要查询的字段
     * @param $fieldName
     * @return Driver
     */
    function field($fieldName) {
        if ($fieldName) {
            if ($this->fields && $this->fields != '*') {
                if ($fieldName == 'SQL_CALC_FOUND_ROWS *') {
                    $this->fields = 'SQL_CALC_FOUND_ROWS' . " $this->fields";
                }
                else if ($fieldName != '*') {
                    $this->fields = $fieldName . ",$this->fields";
                }
                //else {
                //    $this->fields .= ',' . $fieldName;
                //}
            }
            else {
                $this->fields = $fieldName;
            }
        }
        return $this;
    }

    /**
     * 获取与数据库的连接对象，即PDO
     * @var PDO
     */
    protected function _db(){
        $server = $this->server;
        $options = null;
        $dsn = "{$server['driver']}:host={$server['host']};port={$server['port']};dbname={$server['dbname']}";
        $options = [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_PERSISTENT => $server['connect'],#pdo默认为false
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES '.$server['charset']
        ];
        return Pool::object($dsn,'\\PDO',[
            $dsn,$server['user'],$server['pass'],$options
        ]);
    }

    protected function _host() {
        return $this->server['host'];
    }

    /**
     * mysql 专属方法
     * 不存在则插入，存在则更新
     * @param array $arr
     * @param string $upstr
     * @return boolean
     */
    function insertOrUpdate($arr, $upstr = null) {
        if (empty($arr)) return false;
        $comma = '';
        $setFields = '';
        foreach ($arr as $key => $value) {
            if (is_array($value)) {
                $setFields .= "{$comma} `{$key}`=" . current($value);
            }
            else {
                $params[] = $value;
                $setFields .= "$comma `$key`=?";
            }
            $comma = ',';
        }
        $upstr = empty($upstr) ? $setFields : $upstr;
        $sql = "INSERT INTO  `{$this->table}` SET {$setFields} ON DUPLICATE KEY UPDATE {$upstr}";
        $sql = str_replace('table.',$this->table.'.',$sql);
        return $this->db->sql($sql, $params,false);
    }

    /**
     * 获取结果集和数量
     * @return [int,array]
     */
    public function paginate($fetchMode = PDO::FETCH_ASSOC) {
        $this->fields = $this->fields?$this->fields:'*';
        $this->fields = 'SQL_CALC_FOUND_ROWS '.$this->fields;
        $result = $this->query()->fetchAll($fetchMode);
        $result = $this->_data($result,true);
        $num = $this->sql('SELECT FOUND_ROWS()')->fetchColumn();
        return [$num,$result];
    }


}