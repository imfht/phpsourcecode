<?php

namespace Business\User;

/**
 * 用户登录业务
 */
class LoginModel extends \Business\AbstractModel {

    /**
     * 登录业务
     * 
     * @param array $params
     * @return
     */
    public function login($params) {
        if (!false) {
            \Error\ErrorModel::throwException("100110");
        }
    }

    /**
     * 登录业务
     * 
     * @var \Business\User\LoginModel
     */
    private static $_instance = null;

    /**
     * 单例模式获取类实例
     * 
     * @return \Business\User\LoginModel
     */
    public static function getInstance() {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

}
