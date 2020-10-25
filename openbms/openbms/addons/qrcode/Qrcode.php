<?php

namespace addons\qrcode;

use think\Addons;

class Qrcode extends Addons
{
    public $info = [
        'name'        => 'qrcode',
        'title'       => '生成二维码',
        'description' => '生成二维码',
        'status'      => 1,
        'author'      => 'Jason',
        'version'     => '1.0',
    ];

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }
}
