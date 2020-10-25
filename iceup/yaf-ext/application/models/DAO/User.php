<?php

namespace DAO;

/**
 * 用户数据层
 */
class UserModel extends \DAO\AbstractModel {

    /**
     * 根据用户编号查找数据
     * 
     * @param int $userId
     * @return array
     */
    public function find($userId) {
        $redis = \Redis\Db0\UserModel::getInstance();
        $user  = $redis->find($userId);
        if (!$user) {
            $mysql = \Mysql\UserModel::getInstance();
            $user  = $mysql->find($userId);
            if ($user) {
                $redis->update($userId, $user);
            }
        }
        return $user;
    }

    /**
     * 类实例
     * 
     * @var \DAO\UserModel
     */
    private static $_instance = null;

    /**
     * 获取类实例
     * 
     * @return \DAO\UserModel
     */
    public static function getInstance() {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

}
