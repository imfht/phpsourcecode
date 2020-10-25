<?php

namespace Mysql;

/**
 * 用户表连接类
 */
class UserModel extends \Mysql\AbstractModel {

    /**
     * 表名
     * 
     * @var string
     */
    protected $_tableName = 'user';

    /**
     * 主键
     * 
     * @var string
     */
    protected $_primaryKey = 'user_id';

    /**
     * 重写父类fetchAll方法，定位到从库
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
        $slave = \Mysql\Slave\UserModel::getInstance();
        return $slave->fetchAll($columns, $where, $order, $count, $offset, $group);
    }

    /**
     * 类实例

     * @var \Mysql\UserModel
     */
    private static $_instance = null;

    /**
     * 获取类实例
     * 
     * @return \Mysql\UserModel
     */
    public static function getInstance() {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

}
