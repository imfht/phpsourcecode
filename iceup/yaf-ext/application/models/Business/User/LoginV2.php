<?php

namespace Business\User;

/**
 * 用户登录业务
 */
class LoginV2Model extends \Business\AbstractModel {

    /**
     * 登录业务
     * 
     * @param array $params
     * @return
     */
    public function login($params) {
        
    }

    /**
     * 登录业务
     * 
     * @var \Business\User\LoginV2Model
     */
    private static $_instance = null;

    /**
     * 单例模式获取类实例
     * 
     * @return \Business\User\LoginV2Model
     */
    public static function getInstance() {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

}
