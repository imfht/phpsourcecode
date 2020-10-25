<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2013 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace app\common\behavior;
use think\Hook;
use think\Db;

// 初始化钩子信息
class InitHook {

    // 行为扩展的执行入口必须是run
    public function run(&$content){

        if(defined('BIND_MODULE') && BIND_MODULE === 'install') return;
        $data = cache('hooks');

        if(!$data){
            $hooks = Db::name('Hooks')->field('name,addons')->select();
         //   $hooksArray = $hooks->toArray();

            foreach ($hooks as $key => $value) {
                if($value['addons']){
                    $map['status']  =   1;
                    $names          =   explode(',',$value['addons']);
                    $map['name']    =   array('IN',$names);
                    $data = Db::name('Addons')->field('id,name')->where($map)->select();
                    if($data){
                        $dataCol = array_column($data, 'name','id');

                        $addons = array_intersect($names, $dataCol);

                        Hook::add($value['name'],array_filter(array_map('get_addon_class',$addons)));
                    }
                }
            }
            cache('hooks',Hook::get());    //这里缓存出错了先注释掉，TODO:启用缓存
        }else{
            Hook::import($data,false);
        }
    }
}