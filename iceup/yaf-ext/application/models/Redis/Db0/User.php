<?php

namespace Redis\Db0;

/**
 * 用户信息缓存
 */
class UserModel extends \Redis\Db0\AbstractModel {

    /**
     * 表名
     * 
     * @var string
     */
    protected $_tableName = 'user';

    /**
     * 计算key
     * 
     * @param int $id
     * @return string
     */
    public function calcKey($id) {
        return $this->_tableName . self::DELIMITER . $id;
    }

    /**
     * 根据id查找用户信息
     * 
     * @param int $id 
     * @return array
     */
    public function find($id) {
        $result = $this->get($this->calcKey($id));
        if ($result) {
            return json_decode($result, true);
        }
        return null;
    }

    /**
     * 更新数据
     * 
     * @param int $id
     * @param array $data
     */
    public function update($id, $data) {
        return $this->set($this->calcKey($id), json_encode($data));
    }

    /**
     * 类实例
     * 
     * @var \Redis\Db0\UserModel
     */
    private static $_instance = null;

    /**
     * 获取类实例
     * 
     * @return \Redis\Db0\UserModel
     */
    public static function getInstance() {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

}
