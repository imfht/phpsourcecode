<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 管理菜单
 */
namespace app\system\event;
use app\common\event\Passport;

class AppConfig{

    /**
     * 管理菜单
     * @return json
     */
    public static function menuAuth($group,int $auth){
        if(empty($auth)){
            return true;
        }
        if(!empty($group['auth']) && in_array($auth,ids($group['auth'],true))){
            return true;
        }
        return false;
    }

    /**
     * 管理菜单
     * @return json
     */
    public static function menu(string $miniapp,$type = true,$menu = null){
        $file = empty($menu) ? ($type ? 'menu.php' : 'admin.php') : $menu.'.php';
        $menu = [];
        $module_config = PATH_APP.$miniapp.DS.'config'.DS.$file;
        if(is_file($module_config)){
            $menu = include $module_config;
        }
        if(is_array($menu)){
            $nav  = [];
            if($file == 'menu.php'){
                //前台
                $user = Passport::getUser();
                foreach ($menu as $key => $value) {
                    if(self::menuAuth($value,$user->auth)){
                        $nav[$key]['name'] = $value['name'];
                        $nav[$key]['icon'] = $value['icon'];
                        if(isset($value['menu'])){
                            foreach ($value['menu'] as $vo) {
                                if(self::menuAuth($vo,$user->auth)){
                                    $nav[$key]['nav'][] = $vo;
                                }
                            }
                        } 
                    }
                }
            }else{
                //后台
                foreach ($menu as $key => $value) {
                    $nav[$key]['name'] = $value['name'];
                    $nav[$key]['icon'] = $value['icon'];
                    if(isset($value['menu'])){
                        foreach ($value['menu'] as $vo) {
                           $nav[$key]['nav'][] = $vo;
                        }
                    } 
                }
            }
            return $nav;
        }

        return [];  
    }

    /**
     * 应用配置信息
     * @return json
     */
    public static function version(string $miniapp){
        $module_config = PATH_APP.$miniapp.DS.'config/version.php';
        $config = [];
        if(is_file($module_config)){
            $config = include $module_config;
        }
        return $config;  
    }

    /**
     * 读取权限
     * @return json
     */
    public static function auth(string $miniapp){
        $module_config = PATH_APP.$miniapp.DS.'config/auth.php';
        $config = [];
        if(is_file($module_config)){
            $config = include $module_config;
        }
        return $config;  
    }
}