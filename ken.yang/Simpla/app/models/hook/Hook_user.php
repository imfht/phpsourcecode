<?php

/*
 * 用户钩子
 * Hook_user
 */

class Hook_user extends Eloquent {

    /**
     * hook_user_page_load
     */
    public static function user_page_load($data) {
        $hook = 'user_page_load';
        $module_list = Base::get_active_module();
        foreach ($module_list as $module) {
            if (Hook::module_invoke($module, $hook, $data)) {
                $data = Hook::module_invoke($module, $hook, $data);
            }
        }
        return $data;
    }

    /**
     * hook_user_login_before
     */
    public static function user_login_before($input, $username_or_email, $password, $remember) {
        $hook = 'user_login_before';
        $module_list = Base::get_active_module();
        foreach ($module_list as $module) {
            if (Hook::module_invoke($module, $hook, $input, $username_or_email, $password, $remember)) {
                list($input, $username_or_email, $password, $remember) = Hook::module_invoke($module, $hook, $input, $username_or_email, $password, $remember);
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
            if (Hook::module_invoke($module, $hook, $user)) {
                $user = Hook::module_invoke($module, $hook, $user);
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
            if (Hook::module_invoke($module, $hook, $input)) {
                $input = Hook::module_invoke($module, $hook, $input);
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
            if (Hook::module_invoke($module, $hook)) {
                Hook::module_invoke($module, $hook);
            }
        }
        return true;
    }

}
