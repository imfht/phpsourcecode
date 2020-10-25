<?php

/*
 * hook钩子调用方法，模仿Drupal7
 */

class Hook extends Eloquent {

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
