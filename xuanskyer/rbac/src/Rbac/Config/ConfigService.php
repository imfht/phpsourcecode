<?php
/**
 * Desc: Role-Based Access Control FOR PHP
 * Created by PhpStorm.
 * User: xuanskyer | <furthestworld@iloucd.com>
 * Date: 2016/12/9 16:54
 */
namespace Rbac\Config;
class ConfigService {

    static $conf = null;

    /**
     * @desc    read config setting
     * @param string $config_name
     * @return bool
     */
    public static function get($config_name = '') {
        return isset(self::$conf[$config_name]) ? self::$conf[$config_name] : '';
    }

    public static function init($conf_setting = []) {
        $default_conf = [
            //rbac认证配置
            'USER_AUTH_ON'      => true,
            'USER_AUTH_TYPE'    => 2,        // 默认认证类型 1 登录认证 2 实时认证
            'USER_AUTH_KEY'     => 'adminV3AuthId',    // 用户认证SESSION标记
            'ADMIN_AUTH_KEY'    => 'administrator',
            'ADMIN_USER_ID'     => 22,      //超级管理员ID
            'RBAC_ROLE_TABLE'   => 'rbac_role',
            'RBAC_USER_TABLE'   => 'rbac_role_user',
            'RBAC_ACCESS_TABLE' => 'rbac_access',
            'RBAC_NODE_TABLE'   => 'rbac_node',
        ];

        if(null === self::$conf){
            self::$conf = array_merge($default_conf, $conf_setting);
        }
    }
}