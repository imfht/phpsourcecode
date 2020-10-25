<?php

namespace addons\editor;

use think\Addons;

class Editor extends Addons
{
    public $info = [
        'name'        => 'editor',
        'title'       => '富文本编辑器',
        'description' => '富文本编辑器',
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

    /**
     * @param $data
     * @return mixed
     * @throws \Exception
     */
    public function editor($data)
    {
        return $this->fetch($data['type'], ['id' => $data['id']]);
    }
}
