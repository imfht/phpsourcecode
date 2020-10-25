<?php

/*
 * 基础默认公共方法
 */

class Hook_old extends Eloquent {
    /** ============================================================
     * node内容
      =============================================================== */

    /**
     * hook_node_load
     */
    public static function node_load($node) {
        $hook = 'node_load';
        $module_list = Base::get_active_module();
        foreach ($module_list as $module) {
            if (self::module_invoke($module, $hook, $node)) {
                $node = self::module_invoke($module, $hook, $node);
            }
        }
        return $node;
    }

    /**
     * hook_node_page_load
     */
    public static function node_page_load($node) {
        $hook = 'node_page_load';
        $module_list = Base::get_active_module();
        foreach ($module_list as $module) {
            if (self::module_invoke($module, $hook, $node)) {
                $node = self::module_invoke($module, $hook, $node);
            }
        }
        return $node;
    }

    /** ============================================================
     * category分类
      =============================================================== */

    /**
     * hook_category_load
     */
    public static function category_page_load($category, $nodes, $paginate) {
        $hook = 'category_page_load';
        $module_list = Base::get_active_module();
        foreach ($module_list as $module) {
            if (self::module_invoke($module, $hook, $category, $nodes, $paginate)) {
                list($category, $nodes, $paginate) = self::module_invoke($module, $hook, $category, $nodes, $paginate);
            }
        }
        return array($category, $nodes, $paginate);
    }

    /** ============================================================
     * 首页
      =============================================================== */

    /**
     * hook_home_page_load
     */
    public static function home_page_load($nodes, $paginate) {
        $hook = 'home_page_load';
        $module_list = Base::get_active_module();
        foreach ($module_list as $module) {
            if (self::module_invoke($module, $hook, $nodes, $paginate)) {
                list($nodes, $paginate) = self::module_invoke($module, $hook, $nodes, $paginate);
            }
        }
        return array($nodes, $paginate);
    }

    /** ============================================================
     * 用户
      =============================================================== */

    /**
     * hook_user_page_load
     */
    public static function user_page_load($user, $nodes) {
        $hook = 'user_page_load';
        $module_list = Base::get_active_module();
        foreach ($module_list as $module) {
            if (self::module_invoke($module, $hook, $nodes, $nodes)) {
                list($user, $nodes) = self::module_invoke($module, $hook, $user, $nodes);
            }
        }
        return array($user, $nodes);
    }

    /**
     * hook_user_login_before
     */
    public static function user_login_before($input, $username_or_email, $password, $remember) {
        $hook = 'user_login_before';
        $module_list = Base::get_active_module();
        foreach ($module_list as $module) {
            if (self::module_invoke($module, $hook, $input, $username_or_email, $password, $remember)) {
                list($input, $username_or_email, $password, $remember) = self::module_invoke($module, $hook, $input, $username_or_email, $password, $remember);
            }
        }
        return array($input, $username_or_email, $password, $remember);
    }

    /**
     * hook_user_login_before
     */
    public static function user_login_after($user) {
        $hook = 'user_login_after';
        $module_list = Base::get_active_module();
        foreach ($module_list as $module) {
            if (self::module_invoke($module, $hook, $user)) {
                $user = self::module_invoke($module, $hook, $user);
            }
        }
        return $user;
    }

    /**
     * hook_user_register_before
     */
    public static function user_register_before($input) {
        $hook = 'user_register_before';
        $module_list = Base::get_active_module();
        foreach ($module_list as $module) {
            if (self::module_invoke($module, $hook, $input)) {
                $input = self::module_invoke($module, $hook, $input);
            }
        }
        return $input;
    }

    /**
     * hook_user_register_after
     */
    public static function user_register_after() {
        $hook = 'user_register_after';
        $module_list = Base::get_active_module();
        foreach ($module_list as $module) {
            if (self::module_invoke($module, $hook)) {
                self::module_invoke($module, $hook);
            }
        }
        return true;
    }

    /** ============================================================
     * 区块
      =============================================================== */

    /**
     * hook_block_info
     */
    public static function block_info() {
        $hook = 'block_info';
        $module_list = Base::get_active_module();
        $block_list = array();
        foreach ($module_list as $module) {
            if (self::module_invoke($module, $hook)) {
                $block_info = self::module_invoke($module, $hook);
                $block_list = array_merge_recursive($block_list, $block_info);
            }
        }
        return $block_list;
    }

    /** ============================================================
     * 后台用户
      =============================================================== */

    /**
     * hook_access
     */
    public static function access() {
        $hook = 'access';
        $module_list = Base::get_active_module();
        $access_routes = array();
        if (file_exists(dirname(dirname(__DIR__)) . '/access.php')) {
            $access_routes = require_once dirname(dirname(__DIR__)) . '/access.php';
        }
        foreach ($module_list as $module) {
            if (self::module_invoke($module, $hook)) {
                $module_route_access = self::module_invoke($module, $hook);
                $access_routes = array_merge_recursive($access_routes, $module_route_access);
            }
        }
        return $access_routes;
    }

    /** ============================================================
     * hook钩子调用方法，模仿Drupal7
      =============================================================== */

    /**
     * hook调用
     * @param type $module
     * @param type $hook
     * @return type
     */
    public static function module_invoke($module, $hook) {
        $args = func_get_args();
        // Remove $module and $hook from the arguments.
        unset($args[0], $args[1]);
        if (self::module_hook($module, $hook)) {
            return call_user_func_array($module . '_' . $hook, $args);
        }
    }

    /**
     * 判断是否存在该hook方法
     * @param type $module
     * @param type $hook
     * @return boolean
     */
    public static function module_hook($module, $hook) {
        require_once dirname(dirname(__DIR__)) . '/modules/' . $module . '/module.php';
        $function = $module . '_' . $hook;
        if (function_exists($function)) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * 来自于drupal的module_hook
     * @param type $module
     * @param type $hook
     */
//    function module_invoke($module, $hook,$args) {
//        $args = func_get_args();
//        // Remove $module and $hook from the arguments.
//        unset($args[0], $args[1]);
//        if (module_hook($module, $hook)) {
//            return call_user_func_array($module . '_' . $hook, $args);
//        }
//    }
//
//    function module_hook($module, $hook) {
//        $function = $module . '_' . $hook;
//        if (function_exists($function)) {
//            return TRUE;
//        }
//        // If the hook implementation does not exist, check whether it may live in an
//        // optional include file registered via hook_hook_info().
//        $hook_info = module_hook_info();
//        if (isset($hook_info[$hook]['group'])) {
//            module_load_include('inc', $module, $module . '.' . $hook_info[$hook]['group']);
//            if (function_exists($function)) {
//                returnTRUE;
//            }
//        }
//        returnFALSE;
//    }
}
