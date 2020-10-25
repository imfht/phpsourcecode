<?php
/**
 * MySQL数据库操作封装，仅适用于单台服务器，不适用读写分离的集群。
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2013-05 v1.0.0
 */

namespace herosphp\db\driver;

use herosphp\core\Log;
use herosphp\db\DBException;
use \PDO;
use \PDOException;

class Mysql {

    /**
     * PDO 数据库连接实例
     * @var \PDO
     */
    private $link;

    /**
     * 数据库配置参数
     * @var array
     */
    private $config = array();

    /**
     * 事务的级数，解决事务的嵌套问题
     * @var int
     */
    private $transactions = 0;

    /**
     * 创建一个数据库操作对象,初始化配置参数
     * @param $config
     */
    public  function __construct( $config ) {

        if ( !is_array($config) || empty($config) ) E("必须传入数据库的配置信息！");
        $this->config = $config;

        $this->connect(); //连接数据库
    }

    /**
     * @throws DBException
     * @return Resource
     */
    public function connect()
    {
        if ( $this->link != null ) return true;
        $_config = $this->config;
        $_dsn="{$_config['db_type']}:host={$_config['db_host']};port={$_config['db_port']};dbname={$_config['db_name']}";
        try {
            $this->link = new PDO($_dsn, $_config['db_user'], $_config['db_pass'], array(PDO::ATTR_PERSISTENT=>false));
            $this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //设置数据库编码，默认使用UTF8编码
            $_charset = $_config['db_charset'];
            if ( !$_charset ) $_charset = 'UTF8';
            $this->link->query("SET names {$_charset}");
            $this->link->query("SET character_set_client = {$_charset}");
            $this->link->query("SET character_set_results = {$_charset}");

        } catch (PDOException $e ) {
            if ( APP_DEBUG ) {
                E("数据库连接失败".$e->getMessage());
                Log::error("数据库连接失败".$e->getMessage(), 'sql');
            }
        }
        return $this->link;
    }

    /**
     * 执行一条sql语句，直接返回结果
     * @param string $sql
     * @return \PDOStatement
     * @throws DBException
     */
    public function execute($sql) {

        //如果开启了调试模式，那么把所有对数据库写操作的语句记录下来
        if ( SQL_LOG &&
            (strpos($sql, 'INSERT') !== false
                || strpos($sql, 'DELETE') !== false
                || strpos($sql, 'REPLACE') !== false
                    || strpos($sql, 'UPDATE') !== false)) {
            Log::info($sql, 'sql');
        }
        if ( $this->link == null ) $this->connect();
        try {
            $result = $this->link->query($sql);
        } catch ( PDOException $e ) {
            $exception = new DBException("SQL错误:" . $e->getMessage());
            $exception->setCode($e->getCode());
            $exception->setQuery($sql);
            if ( APP_DEBUG ) {
                __print($sql);
            }
            throw $exception;
        }
        return $result;
    }

    /**
     * 插入数据, 如果主键时自增的，返回主键的值，否则返回true | false
     * @param string $table 数据表或者集合名称
     * @param array $data 数据，必须为数组
     * @return mixed
     */
    public function insert($table, $data)
    {
		$_fileds = '';
		$_values = '';
		$_T_fields = $this->getTableFields($table);
		foreach ( $data as $_key => $_val ) {

			//自动过滤掉不存在的字段
			if ( !in_array( $_key, $_T_fields ) ) continue;

			$_fileds .= ( $_fileds=='' ) ? "`{$_key}`" : ", `{$_key}`" ;
            if ( is_null($_val) ) {
                $_val = 'NULL';
            } else {
                $_val = "'{$_val}'";
            }
			$_values .= ( $_values=='' ) ? "{$_val}" : ",{$_val}";

		}

		if ( $_fileds != '' ) {
			$_query = "INSERT INTO {$table}(" . $_fileds . ") VALUES(" . $_values . ")";

			if ( $this->execute( $_query ) != false ) {
                $last_insert_id = $this->link->lastInsertId();
                if ( $last_insert_id > 0 ) { //返回自增id
                    return $last_insert_id;
                } else {
                    return true;
                }
			}
		}
        return false;
    }

    /**
     * 插入数据，如果数据已经存在，则替换数据
     * @see insert()
     */
    public function replace($table, $data) {

        $_fileds = '';
        $_values = '';
        $_T_fields = $this->getTableFields($table);
        foreach ( $data as $_key => $_val ) {

            //自动过滤掉不存在的字段
            if ( !in_array( $_key, $_T_fields ) ) continue;

            $_fileds .= ( $_fileds=='' ) ? "`{$_key}`" : ", `{$_key}`";
            if ( is_null($_val) ) {
                $_val = 'NULL';
            } else {
                $_val = "'{$_val}'";
            }
            $_values .= ( $_values=='' ) ? "{$_val}" : ",{$_val}";
        }

        if ( $_fileds != '' ) {
            $_query = "REPLACE INTO {$table}(" . $_fileds . ") VALUES(" . $_values . ")";
            if ( $this->execute($_query) != false ) {
                return true;
            }

        }
        return false;
    }

    /**
     * <p>更新数据, 失败返回false， 成功返回本次更新影响的记录条数</p>
     * @param string $table 数据表名称
     * @param array $data 数据
     * @param array $condition 查询条件
     * @return bool|int
     */
    public function update($table, $data, $condition)
    {
        if ( empty($condition) ) return false;
        $_T_fields = $this->getTableFields($table);
        $_keys = '';
        foreach ( $data as $_key => $_val ) {

            //过滤不存在的字段
            if ( !in_array($_key, $_T_fields) ) continue;
            if ( is_null($_val) ) {
                $_val = 'NULL';
            } else {
                $_val = "'{$_val}'";
            }
            $_keys .= $_keys == ''? "`{$_key}`={$_val}" : ", `{$_key}`={$_val}";
        }
        if ( $_keys !== '' ) {
            $_query = "UPDATE {$table} SET " . $_keys . $condition;
            $result = $this->execute($_query);
            if ( $result != false ) {
                return $result->rowCount() > 0 ? $result->rowCount() : true;
            }
        }
        return false;
    }

    /**
     * 获取数据列表
     * @param string $query
     * @return mixed
     */
    public function &getList($query)
    {
        $_result = array();
        $_ret = $this->execute($query);
        if ( $_ret != false ) {

            while ( ($_rows = $_ret->fetch(PDO::FETCH_ASSOC)) != false )
                $_result[]  = $_rows;
        }
        return $_result;
    }

    /**
     * <p>删除数据, 失败返回false， 成功返回本次删除影响的记录条数</p>
     * @param $table
     * @param array $condition 删除条件
     * @return bool|int
     */
    public function delete($table, $condition)
    {
        if ( !$condition ) return false; //防止误删除所有的数据，所以必须传入删除条件

        $sql = "DELETE FROM {$table} {$condition}";
        $result = $this->execute($sql);
        if ( $result ) {
            return $result->rowCount();
        }
        return false;
    }



    /**
     * 获取一行数据
     * @param string $query
     * @return mixed
     */
    public function &getOneRow($query)
    {
        $result = $this->execute($query);
        if ( $result != false ) {
            return $result->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    /**
     * 获取某个条件匹配的总记录数
     * @param string $sql
     * @return int
     */
    public function count($sql)
    {
        $result = $this->execute($sql);
        $res = $result->fetch(PDO::FETCH_ASSOC);
        return $res['total'];
    }

    /**
     * begin transaction (事物开启)
     */
    public function beginTransaction()
    {
        if ( $this->link == null ) {
            $this->connect();
        }
        ++$this->transactions;

        if ( $this->transactions == 1 ) {
            $this->link->beginTransaction();
        }


    }

    /**
     * commit transaction (事物提交)
     */
    public function commit()
    {
        if ( $this->link == null ) {
            $this->connect();
        }
        if ( $this->transactions == 1 ) {
            $this->link->commit();
        }

        --$this->transactions;
    }

    /**
     * roll back (事物回滚)
     */
    public function rollBack()
    {
        if ( $this->link == null ) {
            $this->connect();
        }
        if ( $this->transactions == 1 ) {

            $this->transactions = 0;
            $this->link->rollBack();

        } else {
            --$this->transactions;
        }

    }

    /**
     * 检查是否开启了事物
     * @return boolean
     */
    public function inTransaction()
    {
        if ( $this->link == null ) $this->connect();
        return $this->link->inTransaction();
    }

    /***
     * 获取指定数据表的所有字段
     * @param		string 		$_table		table name
     * @return 		array		fields array of table
     */
    protected function getTableFields( $_table ) {

        $_sql = "SHOW COLUMNS FROM {$_table}";
        $_ret = $this->execute( $_sql );
        $_fields = array();
        if ( $_ret != false ) {
            while ( ($_rows = $_ret->fetch()) != false ) {
                $_fields[] = $_rows[0];
            }
        }
        return $_fields;
    }

    /**
     * 释放资源
     */
    public function __destruct() {

        if ( $this->link ) $this->link = null;
    }

}
