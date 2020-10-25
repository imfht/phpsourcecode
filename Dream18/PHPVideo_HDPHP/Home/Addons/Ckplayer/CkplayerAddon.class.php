<?php
/**
 * Ckplayer 插件
 * @author 楚羽幽 <Name_Cyu@Foxmail.com>
 */
class CkplayerAddon extends Addon
{

    //插件信息
    public $info = array(
        'name' => 'Ckplayer',
        'title' => 'Ckplayer播放器',
        'description' => 'Ckplayer6.7播放器插件',
        'status' => 1,
        'author' => '楚羽幽',
        'version' => '1.0',
        'has_adminlist' => 1,
    );

    //安装
    public function install()
    {
        return true;
    }

    //卸载
    public function uninstall()
    {
        return true;
    }}