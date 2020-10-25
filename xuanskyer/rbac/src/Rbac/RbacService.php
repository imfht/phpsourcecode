<?php
/**
 * Desc: Role-Based Access Control FOR PHP
 * Created by PhpStorm.
 * User: xuanskyer | <furthestworld@iloucd.com>
 * Date: 2016/12/9 16:54
 */
namespace Rbac;

use Rbac\Config\ConfigService;
use Rbac\Model\Model;
use Rbac\Model\NodeModel;

class RbacService {
    public static $model_container  = array();
    public static $current_model;
    public static $rbacCacheService = null;

    //用于检测用户权限的方法,并保存到Session中
    static function saveAccessList($authId = null) {
        if (null === $authId) $authId = $_SESSION[ConfigService::get('USER_AUTH_KEY')]['id'];
        // 如果使用普通权限模式，保存当前用户的访问权限列表
        // 对管理员开发所有权限
        if (2 != ConfigService::get('USER_AUTH_TYPE') && !$_SESSION[ConfigService::get('ADMIN_AUTH_KEY')])
            $_SESSION['_ACCESS_LIST'] = self::getAccessList($authId);
        return;
    }

    // 取得模块的所属记录访问权限列表 返回有权限的记录ID数组
    static function getRecordAccessList($authId = null, $module = '') {
        if (null === $authId) $authId = $_SESSION[ConfigService::get('USER_AUTH_KEY')]['id'];
        if (empty($module)) return [];
        //获取权限访问列表
        $accessList = self::getModuleAccessList($authId, $module);
        return $accessList;
    }

    //检查当前操作是否需要认证
    static function checkAccess($controller_name = '', $action_name = '') {
        //如果项目要求认证，并且当前模块需要认证，则进行权限认证
        if (ConfigService::get('USER_AUTH_ON')) {
            $_module = array();
            $_action = array();
            if ("" != ConfigService::get('REQUIRE_AUTH_MODULE')) {
                //需要认证的模块
                $_module['yes'] = explode(',', strtoupper(ConfigService::get('REQUIRE_AUTH_MODULE')));
            } else {
                //无需认证的模块
                $_module['no'] = explode(',', strtoupper(ConfigService::get('NOT_AUTH_MODULE')));
            }
            //检查当前模块是否需要认证
            if ((!empty($_module['no']) && !in_array(strtoupper($controller_name), $_module['no'])) || (!empty($_module['yes']) && in_array(strtoupper($controller_name), $_module['yes']))) {
                if ("" != ConfigService::get('REQUIRE_AUTH_ACTION')) {
                    //需要认证的操作
                    $_action['yes'] = explode(',', strtoupper(ConfigService::get('REQUIRE_AUTH_ACTION')));
                } else {
                    //无需认证的操作
                    $_action['no'] = explode(',', strtoupper(ConfigService::get('NOT_AUTH_ACTION')));
                }
                //检查当前操作是否需要认证
                if ((!empty($_action['no']) && !in_array(strtoupper($action_name), $_action['no'])) || (!empty($_action['yes']) && in_array(strtoupper($action_name), $_action['yes']))) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * @desc 权限认证的过滤器方法
     * @param array  $get_params
     * @param string $appName
     * @param string $controller_name
     * @param string $action_name
     * @return bool
     */
    static public function AccessDecision($get_params = array(), $appName = '', $controller_name = '', $action_name = '') {
        //检查是否需要认证
        $action_name = in_array($action_name, array('operate')) ? strtolower($get_params['op']) : $action_name;

        if (self::checkAccess($controller_name, $action_name)) {
            //存在认证识别号，则进行进一步的访问决策
            $accessGuid = md5($appName . $controller_name . $action_name);
            if ($_SESSION[ConfigService::get('USER_AUTH_KEY')]['id'] != ConfigService::get('ADMIN_USER_ID') && empty($_SESSION[ConfigService::get('ADMIN_AUTH_KEY')])) {
                if (ConfigService::get('USER_AUTH_TYPE') == 2) {
                    //加强验证和即时验证模式 更加安全 后台权限修改可以即时生效
                    //通过数据库进行访问检查
                    $accessList = self::getAccessList($_SESSION[ConfigService::get('USER_AUTH_KEY')]['id']);
                } else {
                    // 如果是管理员或者当前操作已经认证过，无需再次认证
                    if ($_SESSION[$accessGuid]) {
                        return true;
                    }
                    //登录验证模式，比较登录后保存的权限访问列表
                    $accessList = $_SESSION['_ACCESS_LIST'];
                }
                //判断是否为组件化模式，如果是，验证其全模块名
                if (!isset($accessList[strtoupper($appName)][strtoupper($controller_name)][strtoupper($action_name)])) {
                    $_SESSION[$accessGuid] = false;
                    return false;
                } else {
                    $_SESSION[$accessGuid] = true;
                }
            } else {
                //管理员无需认证
                return true;
            }
        }
        return true;
    }

    /**
     * 取得当前认证号的所有权限列表
     * @param $authId
     * @return array
     */
    static public function getAccessList($authId) {
        if (method_exists(self::$rbacCacheService, 'getCacheByMethod')) {
            $access = self::$rbacCacheService->getCacheByMethod($authId, 'GetAccessList');
        } else {
            $access = null;
        }
        if (empty($access)) {
            self::init();
            // Db方式权限数据
            $table = array('role' => ConfigService::get('RBAC_ROLE_TABLE'), 'user' => ConfigService::get('RBAC_USER_TABLE'), 'access' => ConfigService::get('RBAC_ACCESS_TABLE'), 'node' => ConfigService::get('RBAC_NODE_TABLE'));
            $sql   = "select node.id,node.name from " .
                $table['role'] . " as role," .
                $table['user'] . " as user," .
                $table['access'] . " as access ," .
                $table['node'] . " as node " .
                "where user.user_id='{$authId}' and user.role_id=role.id and ( access.role_id=role.id  or (access.role_id=role.pid and role.pid!=0 ) ) and role.status=1 and access.node_id=node.id and node.level=1 and node.status=1";

            $apps   = self::$current_model->selectBySql($sql);
            $access = array();
            foreach ($apps as $key => $app) {
                $appId   = $app['id'];
                $appName = $app['name'];
                // 读取项目的模块权限
                $access[strtoupper($appName)] = array();
                $sql                          = "select node.id,node.name from " .
                    $table['role'] . " as role," .
                    $table['user'] . " as user," .
                    $table['access'] . " as access ," .
                    $table['node'] . " as node " .
                    "where user.user_id='{$authId}' and user.role_id=role.id and ( access.role_id=role.id  or (access.role_id=role.pid and role.pid!=0 ) ) and role.status=1 and access.node_id=node.id and node.level=2 and node.pid={$appId} and node.status=1";
                $modules                      = self::$current_model->selectBySql($sql);
                // 判断是否存在公共模块的权限
                $publicAction = array();
                foreach ($modules as $key => $module) {
                    $moduleId   = $module['id'];
                    $moduleName = $module['name'];
                    if ('PUBLIC' == strtoupper($moduleName)) {
                        $sql = "select node.id,node.name from " .
                            $table['role'] . " as role," .
                            $table['user'] . " as user," .
                            $table['access'] . " as access ," .
                            $table['node'] . " as node " .
                            "where user.user_id='{$authId}' and user.role_id=role.id and ( access.role_id=role.id  or (access.role_id=role.pid and role.pid!=0 ) ) and role.status=1 and access.node_id=node.id and node.level=3 and node.pid={$moduleId} and node.status=1";
                        $rs  = self::$current_model->selectBySql($sql);
                        foreach ($rs as $a) {
                            $publicAction[$a['name']] = $a['id'];
                        }
                        unset($modules[$key]);
                        break;
                    }
                }
                // 依次读取模块的操作权限
                foreach ($modules as $key => $module) {
                    $moduleId   = $module['id'];
                    $moduleName = $module['name'];
                    $sql        = "select node.id,node.name from " .
                        $table['role'] . " as role," .
                        $table['user'] . " as user," .
                        $table['access'] . " as access ," .
                        $table['node'] . " as node " .
                        "where user.user_id='{$authId}' and user.role_id=role.id and ( access.role_id=role.id  or (access.role_id=role.pid and role.pid!=0 ) ) and role.status=1 and access.node_id=node.id and node.level=3 and node.pid={$moduleId} and node.status=1";
                    $rs         = self::$current_model->selectBySql($sql);
                    $action     = array();
                    foreach ($rs as $a) {
                        $action[$a['name']] = $a['id'];
                    }
                    // 和公共模块的操作权限合并
                    $action += $publicAction;
                    $access[strtoupper($appName)][strtoupper($moduleName)] = array_change_key_case($action, CASE_UPPER);
                }
            }
            if (method_exists(self::$rbacCacheService, 'addCacheByMethod')) {
                self::$rbacCacheService->addCacheByMethod($authId, $access, 'GetAccessList');
            }
        }

        return $access;
    }

    // 读取模块所属的记录访问权限
    static public function getModuleAccessList($authId, $module) {
        if (method_exists(self::$rbacCacheService, 'getCacheByMethod')) {
            $access = self::$rbacCacheService->getCacheByMethod(array($authId, $module), 'GetModuleAccessList');
        } else {
            $access = null;
        }
        if (empty($access)) {
            self::init();
            // Db方式
            $table  = array('role' => ConfigService::get('RBAC_ROLE_TABLE'), 'user' => ConfigService::get('RBAC_USER_TABLE'), 'access' => ConfigService::get('RBAC_ACCESS_TABLE'));
            $sql    = "select access.node_id from " .
                $table['role'] . " as role," .
                $table['user'] . " as user," .
                $table['access'] . " as access " .
                "where user.user_id='{$authId}' and user.role_id=role.id and ( access.role_id=role.id  or (access.role_id=role.pid and role.pid!=0 ) ) and role.status=1 and  access.module='{$module}' and access.status=1";
            $rs     = self::$current_model->selectBySql($sql);
            $access = array();
            foreach ($rs as $node) {
                $access[] = $node['node_id'];
            }
            if (method_exists(self::$rbacCacheService, 'addCacheByMethod')) {
                self::$rbacCacheService->addCacheByMethod(array($authId, $module), $access, 'GetModuleAccessList');
            }
        }

        return $access;
    }

    /**
     * 读取用户所有权限节点中，level为1的节点列表
     * @param $authId
     * @return bool
     */
    static public function getUserLevel1List($authId) {
        if (method_exists(self::$rbacCacheService, 'getCacheByMethod')) {
            $level1 = self::$rbacCacheService->getCacheByMethod($authId, 'GetUserLevel1List');
        } else {
            $level1 = null;
        }
        if (empty($level1)) {
            self::init();
            $table = array('role' => ConfigService::get('RBAC_ROLE_TABLE'), 'user' => ConfigService::get('RBAC_USER_TABLE'), 'access' => ConfigService::get('RBAC_ACCESS_TABLE'), 'node' => ConfigService::get('RBAC_NODE_TABLE'));
            $sql   = "SELECT * FROM {$table['node']}
             WHERE id IN (
                      SELECT node_id
                      FROM {$table['access']}
                      where role_id =
                          (SELECT role_id FROM {$table['user']} WHERE user_id = {$authId} limit 1)
                          AND `level` = 1
             )
             AND status = 1
             AND `level` = 1 ";
            $list  = self::$current_model->selectBySql($sql);
            if (is_array($list) && !empty($list)) {
                foreach ($list as $node) {
                    $level1[$node['id']] = $node;
                }
            }
            if (method_exists(self::$rbacCacheService, 'addCacheByMethod')) {
                self::$rbacCacheService->addCacheByMethod($authId, $level1, 'GetUserLevel1List');
            }
        }

        return $level1;
    }

    static public function getUserLevel2List($authId, $pid = 0) {
        if (method_exists(self::$rbacCacheService, 'getCacheByMethod')) {
            $level2 = self::$rbacCacheService->getCacheByMethod(array($authId, $pid), 'GetUserLevel2List');
        } else {
            $level2 = null;
        }
        if (empty($level2)) {
            self::init();
            $user_id = intval($authId);
            $pid     = intval($pid);
            $level2  = array();
            if ($user_id && $pid) {
                $table = array('role' => ConfigService::get('RBAC_ROLE_TABLE'), 'user' => ConfigService::get('RBAC_USER_TABLE'), 'access' => ConfigService::get('RBAC_ACCESS_TABLE'), 'node' => ConfigService::get('RBAC_NODE_TABLE'));
                $sql   = " SELECT * FROM {$table['access']}
                      where role_id = (SELECT role_id FROM {$table['user']} where user_id = {$user_id} limit 1)
                            and level = 2
                            and node_id in (SELECT id from {$table['node']} where `level` = 2 and pid = {$pid} )
            ";
                $list  = self::$current_model->selectBySql($sql);
                if (is_array($list) && !empty($list)) {
                    foreach ($list as $node) {
                        $res[$node['node_id']] = $node;
                    }
                    $level2 = self::getNodeListByIds(array_keys($res));
                }
            }
            if (method_exists(self::$rbacCacheService, 'addCacheByMethod')) {
                self::$rbacCacheService->addCacheByMethod(array($authId, $pid), $level2, 'GetUserLevel2List');
            }
        }


        return $level2;
    }

    static public function getNodeIdByName($node_name = '') {
        $node_id = 0;
        if ($node_name) {
            if (method_exists(self::$rbacCacheService, 'getCacheByMethod')) {
                $node_id = self::$rbacCacheService->getCacheByMethod($node_name, 'GetNodeIdByName');
            } else {
                $node_id = null;
            }
            if (empty($node_id)) {
                self::init();
                $table = array('role' => ConfigService::get('RBAC_ROLE_TABLE'), 'user' => ConfigService::get('RBAC_USER_TABLE'), 'access' => ConfigService::get('RBAC_ACCESS_TABLE'), 'node' => ConfigService::get('RBAC_NODE_TABLE'));
                $sql   = "SELECT id FROM {$table['node']} WHERE name = '{$node_name}'";
                $res   = self::$current_model->selectBySql($sql);
                isset($res[0]['id']) && ($node_id = $res[0]['id']);
                if (method_exists(self::$rbacCacheService, 'addCacheByMethod')) {
                    self::$rbacCacheService->addCacheByMethod($node_name, $node_id, 'GetNodeIdByName');
                }
            }
        }
        return $node_id;
    }

    static public function getNodeListByIds($node_ids = array()) {
        $node_list = array();
        if (is_array($node_ids) && !empty($node_ids)) {
            if (method_exists(self::$rbacCacheService, 'getCacheByMethod')) {
                $node_list = self::$rbacCacheService->getCacheByMethod($node_ids, 'GetNodeListByIds');
            } else {
                $node_list = null;
            }
            if (empty($node_list)) {
                self::init();
                $ids       = implode(',', $node_ids);
                $table     = array('role' => ConfigService::get('RBAC_ROLE_TABLE'), 'user' => ConfigService::get('RBAC_USER_TABLE'), 'access' => ConfigService::get('RBAC_ACCESS_TABLE'), 'node' => ConfigService::get('RBAC_NODE_TABLE'));
                $sql       = "SELECT * FROM {$table['node']} WHERE id IN ( {$ids} ) ";
                $node_list = self::$current_model->selectBySql($sql);
            }
            if (method_exists(self::$rbacCacheService, 'addCacheByMethod')) {
                self::$rbacCacheService->addCacheByMethod($node_ids, $node_list, 'GetNodeListByIds');
            }
        }

        return $node_list;
    }

    /**
     * 获取用户所有权限节点列表
     * @param int $authId
     * @return array
     */
    static public function getUserAccessNodeList($authId = 0) {
        $node_tree = array();
        $role_id   = self::getUserRoleId($authId);
        if ($role_id) {
            $node_ids  = self::getRoleAccessNodeIds($role_id);
            $node_list = self::getNodeListByIds($node_ids);
            self::build_node_tree($node_list, $node_tree);

        }
        return $node_tree;
    }

    /**
     * 获取用户角色ID
     * @param int $authId
     * @return int|null
     */
    static public function getUserRoleId($authId = 0) {
        $authId  = intval($authId);
        $role_id = 0;
        if ($authId) {
            if (method_exists(self::$rbacCacheService, 'getCacheByMethod')) {
                $role_id = self::$rbacCacheService->getCacheByMethod($authId, 'GetUserRoleId');
            } else {
                $role_id = null;
            }
            if (empty($role_id)) {
                self::init();
                $table = array('role' => ConfigService::get('RBAC_ROLE_TABLE'), 'user' => ConfigService::get('RBAC_USER_TABLE'), 'access' => ConfigService::get('RBAC_ACCESS_TABLE'), 'node' => ConfigService::get('RBAC_NODE_TABLE'));
                $sql   = "SELECT role_id FROM {$table['user']} WHERE user_id = {$authId} ";
                $res   = self::$current_model->selectBySql($sql);
                isset($res[0]['role_id']) && ($role_id = $res[0]['role_id']);
            }
            if (method_exists(self::$rbacCacheService, 'addCacheByMethod')) {
                self::$rbacCacheService->addCacheByMethod($authId, $role_id, 'GetUserRoleId');
            }
        }
        return $role_id;
    }

    /**
     * 获取角色拥有权限的节点ID
     * @param int $role_id
     * @return array
     */
    static public function getRoleAccessNodeIds($role_id = 0) {
        $node_ids = array();
        $role_id  = intval($role_id);
        if ($role_id) {
            if (method_exists(self::$rbacCacheService, 'getCacheByMethod')) {
                $node_ids = self::$rbacCacheService->getCacheByMethod($role_id, 'GetRoleAccessNodeIds');
            } else {
                $node_ids = null;
            }
            if (empty($node_ids)) {
                self::init();
                $table     = array('role' => ConfigService::get('RBAC_ROLE_TABLE'), 'user' => ConfigService::get('RBAC_USER_TABLE'), 'access' => ConfigService::get('RBAC_ACCESS_TABLE'), 'node' => ConfigService::get('RBAC_NODE_TABLE'));
                $sql       = "SELECT node_id FROM {$table['access']} WHERE role_id = {$role_id} ";
                $node_list = self::$current_model->selectBySql($sql);
                if (is_array($node_list) && !empty($node_list)) {
                    foreach ($node_list as $key => $node) {
                        $node_ids[] = $node['node_id'];
                    }
                }
            }
            if (method_exists(self::$rbacCacheService, 'addCacheByMethod')) {
                self::$rbacCacheService->addCacheByMethod($role_id, $node_ids, 'GetRoleAccessNodeIds');
            }
        }
        return $node_ids;
    }

    /**
     * 格式化权限节点名称为树形结构
     * @param array $node_list
     * @param array $tree
     * @param int   $pid
     * @param int   $level
     */
    static public function build_node_tree($node_list = array(), &$tree = array(), $pid = 0, $level = 0) {
        if (is_array($node_list) && !empty($node_list)) {
            foreach ($node_list as $key => $node) {
                if ($pid == $node['pid'] && $level == $node['level']) {
                    $tree[strtolower($node['name'])] = array();
                    unset($node_list[$key]);
                    self::build_node_tree($node_list, $tree[strtolower($node['name'])], $node['id'], $node['level'] + 1);
                }
            }
        }
        return;
    }

    /**
     * 初始化模型对象
     * @param array $models
     */
    static function setModelInstances($models = array()) {
        if (is_array($models) && !empty($models)) {
            foreach ($models as $name => $obj) {
                !empty($name) && ($obj instanceof Model) && self::$model_container[$name] = $obj;
            }
            self::$current_model = array_pop(self::$model_container);
        }
    }

    /**
     * 初始化RBAC服务
     * @param array $model_setting
     * @param array $config_setting
     * @param null  $cacheService
     */
    static function init($model_setting = [], $config_setting = [], $cacheService = null) {
        ConfigService::init($config_setting);
        if (!isset($model_setting['table_name']) || empty($model_setting['table_name'])) {
            $model_setting['table_name'] = ConfigService::get('RBAC_NODE_TABLE');
        }
        if (is_object($cacheService) && null == self::$rbacCacheService) {
            self::$rbacCacheService = $cacheService;
        }
        if (!self::$current_model instanceof Model) {
            self::$current_model = new NodeModel($model_setting);
        }
    }
}