<?php

/*
 * 分类钩子
 * Hook_category
 */

class Hook_category extends Eloquent {

    /**
     * 分类页面钩子
     * hook_category_load
     * $data=>array('nodes','category','content','paginate')
     */
    public static function category_page_load($data) {
        $hook = 'category_page_load';
        $module_list = Base::get_active_module();
        foreach ($module_list as $module) {
            if (Hook::module_invoke($module, $hook, $data)) {
                $data = Hook::module_invoke($module, $hook, $data);
            }
        }
        return $data;
    }

    /**
     * 获取分类钩子
     * hook_category_load
     * @param type $category
     * @return type
     */
    public static function category_load($category) {
        $hook = 'category_load';
        $module_list = Base::get_active_module();
        foreach ($module_list as $module) {
            if (Hook::module_invoke($module, $hook, $category)) {
                $category = Hook::module_invoke($module, $hook, $category);
            }
        }
        return $category;
    }

}
