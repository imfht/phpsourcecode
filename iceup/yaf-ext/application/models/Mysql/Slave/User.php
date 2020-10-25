<?php

namespace Mysql\Slave;

/**
 * 用户表连接类
 */
class UserModel extends \Mysql\Slave\AbstractModel {

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
     * 类实例

     * @var \Mysql\Slave\UserModel
     */
    private static $_instance = null;

    /**
     * 获取类实例
     * 
     * @return \Mysql\Slave\UserModel
     */
    public static function getInstance() {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

}
