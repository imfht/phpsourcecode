<?php

namespace addon\tipoffs;

use app\common\controller\AddonBase;
use addon\AddonInterface;

/**
 * 举报功能插件
 */
class Tipoffs extends AddonBase implements AddonInterface
{

    public $admin_list = [
        'listKey' => [
            'userName'    => '被举报人',
            'nickname'    => '举报人',
            'contentId'   => '内容',
            'report'      => '补充',
            'status'      => '状态',
            'create_time' => '创建时间',
        ],
        'model'   => 'tipoffs',
        'order'   => 'm.create_time desc,m.id asc',
        'field'   => 'm.*, user.nickname, user1.nickname as userName',
        'join'    => [['user|user', 'm.prosecutorId=user.id'],
            ['user|user1', 'm.defendantId=user1.id']]
    ];

    public $custom_adminlist = 'adminlist.html';


    public function TipoffsJavaScriptHook()
    {
        $this->addonTemplate('hookjs');
    }


    /**
     * 插件安装
     */
    public function addonInstall()
    {
        $arr = $this->addonInfo();
        $this->getisHook('TipoffsJavaScriptHook', $arr['name'], $arr['describe']);
        /*$this->getisHook('TopicTerryeditView2', $arr['name'], $arr['describe']);*/
        $this->installAddon($arr);

        return [RESULT_SUCCESS, '安装成功'];
    }

    /**
     * 插件卸载
     */
    public function addonUninstall()
    {
        $arr = $this->addonInfo();
        $this->deleteHook('TipoffsJavaScriptHook');
        $this->uninstallAddon($arr['name']);


        return [RESULT_SUCCESS, '卸载成功'];
    }

    /**
     * 插件基本信息
     */
    public function addonInfo()
    {

        return [
            'name'          => 'Tipoffs',
            'title'         => '举报功能',
            'describe'      => '举报功能',
            'author'        => 'MCEDK',
            'version'       => '1.0',
            'has_adminlist' => '1'
        ];
    }

}