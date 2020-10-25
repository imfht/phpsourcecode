<?php
/**
 * Created by PhpStorm.
 * User: wanz
 * Date: 18-11-11
 * Time: 下午10:52
 */

namespace vue;


use think\facade\App;
use think\facade\Config;
use think\facade\Env;

class Vue extends \think\template\TagLib
{

    // 标签定义
    protected $tags = [
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'vue'        => ['attr' => 'name', 'close'=>0],
    ];

    public function tagVue($attr)
    {
        $assets = json_decode(file_get_contents(App::getRootPath() . 'assets.json'), true);

        $str = '';
        if (!Config::has('load_common_js')) {
            Config::set('load_common_js', true);
            $str .= '<script src="/vendors.js"></script>';
        }
        $fs = $assets[$attr['name']];
        if (isset($fs['css'])) {
            $str .= '<link rel="stylesheet" href="' . $assets[$attr['name']]['css'] . '"/>';
        }
        if (isset($fs['js'])) {
            $str .= '<script src="' . $assets[$attr['name']]['js'] . '"></script>';
        }
        return $str;
    }

}
