<?php

namespace Mysql\Slave;

/**
 * 主库的从库抽象模型
 *
 * @package Mysql\Slave
 */
abstract class AbstractModel {

    /**
     * 表名
     * 
     * @var string
     */
    protected $_tableName = null;

    /**
     * 表的主键名
     * 
     * @var type 
     */
    protected $_primaryKey = "id";

    /**
     * 返回 Zend 的适配器
     * @return \Zend\Db\Adapter\Adapter
     */
    public function _getAdapter() {
        static $dbAdapter = null;

        if (!$dbAdapter) {
            $conf = \Yaf\Registry::get('config')->get('resources.database.slave.params');
            if (!$conf) {
                throw new \Exception('数据库连接必须设置');
            }
            $dbAdapter = new \Zend\Db\Adapter\Adapter($conf->toArray());
        }

        return $dbAdapter;
    }

    /**
     * 返回Zend的Select对象
     * 
     * @return Zend\Db\Sql\Select
     */
    protected function _getDbSelect() {
        $sql = new \Zend\Db\Sql\Sql($this->_getAdapter(), $this->_tableName);
        return $sql->select();
    }

    /**
     * 根据各个参数筛选出合适的数据
     * 
     * @param array         $columns    需要查找的字段
     * @param array         $where      筛选条件
     * @param array         $order      排序条件
     * @param int           $count      条数
     * @param int           $offset     偏移量
     * @param array         $group      分组条件
     * @return array
     */
    public function fetchAll($columns = null, $where = null, $order = null, $count = null, $offset = null, $group = null) {
        $adapter = $this->_getAdapter();
        $select  = $this->_getDbSelect();
        if ($columns) {
            $select->columns($columns);
        }
        if ($where) {
            $select->where($where);
        }
        if ($count) {
            $select->limit($count);
        }
        if ($offset) {
            $select->offset($offset);
        }
        if ($order) {
            $select->order($order);
        }
        if ($group) {
            $select->group($group);
        }
        $selectString = $select->getSqlString($adapter->getPlatform());
        $rows         = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE)->toArray();

        return $rows;
    }

    /**
     * 禁止clone
     */
    public function __clone() {
        trigger_error('Clone is not allow!', E_USER_ERROR);
    }

}
