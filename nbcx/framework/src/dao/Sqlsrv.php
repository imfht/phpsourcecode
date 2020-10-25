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
 * Sqlite
 *
 * @package nb\dao
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/11/27
 */
class Sqlsrv extends Driver {

    /**
     * 获取与数据库的连接对象，即PDO
     * @var PDO
     */
    public function _db(){
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


    /**
     *
     * @param string $name 存储过程的名字
     * @param string|array $in 输入参数
     * @param string $out 输出参数
     * @return Ambigous <NULL, array>
     */
    public function call($name,$in = null,$out = null){
        $sql = 'CALL ' . $name . '(';
        if($in != null){
            if(is_array($in)){
                $comma = '';
                foreach ($in as $v){
                    $sql .= $comma.'?'; $comma = ',';
                }
            }
            else {
                $sql .= $in.','; $in = null;
            }
        }
        if($out != null){
            if(!empty($in)) $sql .= ','; $sql .= $out;
        }
        $sql .= ')';
        $row = $this->execute($sql,$in);
        $data = null;
        do{
            $result = $row -> fetchAll();
            if($result != null) {
                $data['table'][] = $result;
            }
        }
        while ($row -> nextRowset());
        if($out != null){
            $data['out'] = $this ->execute('select ' . $out) -> fetch();
        }
        return $data;
    }

}