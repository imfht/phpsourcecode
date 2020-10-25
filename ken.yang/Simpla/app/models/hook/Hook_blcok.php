<?php

/*
 * 区块钩子
 * Hook_block
 */

class Hook_block extends Eloquent {

    /**
     * hook_block_info
     */
    public static function block_info() {
        $hook = 'block_info';
        $module_list = Base::get_active_module();
        $block_list = array();
        foreach ($module_list as $module) {
            if (Hook::module_invoke($module, $hook)) {
                $block_info = Hook::module_invoke($module, $hook);
                $block_list = array_merge_recursive($block_list, $block_info);
            }
        }
        return $block_list;
    }

}
