<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 管理菜单
 */
namespace app\system\widget;

class Menu{

    /**
     * 管理菜单
     * @return json
     */
    public static function config(string $miniapp,$type = true){
        $file = $type ? 'menu.php' : 'admin.php';
        $menu = [];
        $module_config = PATH_APP.$miniapp.DS.'config'.DS.$file;
        if(is_file($module_config)){
            $menu = include $module_config;
        }
        if(is_array($menu)){
            $nav = [];
            foreach ($menu as $key => $value) {
                $nav[$key]['name'] = $value['name'];
                $nav[$key]['icon'] = $value['icon'];
                if(isset($value['menu'])){
                    foreach ($value['menu'] as $vo) {
                        $nav[$key]['nav'][] = $vo;
                    }
                }else{
                    $nav[$key]['nav'] = [];
                }
            }
            return $nav;
        }
        return [];  
    }
}