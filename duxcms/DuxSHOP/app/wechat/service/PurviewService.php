<?php
namespace app\wechat\service;
/**
 * 权限接口
 */
class PurviewService {
    /**
     * 获取模块权限
     */
    public function getSystemPurview() {
        return array(
            'WechatConfig' => array(
                'name' => '微信设置',
                'auth' => array(
                    'index' => '设置',
                )
            ),
            'ReplyConfig' => array(
                'name' => '回复设置',
                'auth' => array(
                    'index' => '设置',
                )
            ),
            'MenuConfig' => array(
                'name' => '菜单设置',
                'auth' => array(
                    'index' => '设置',
                )
            ),
            'MaterialImage' => array(
                'name' => '图片素材',
                'auth' => array(
                    'index' => '列表',
                )
            ),
            'MaterialVideo' => array(
                'name' => '视频素材',
                'auth' => array(
                    'index' => '列表',
                )
            ),
            'MaterialVoice' => array(
                'name' => '语音素材',
                'auth' => array(
                    'index' => '列表',
                )
            ),
            'MaterialNews' => array(
                'name' => '图文素材',
                'auth' => array(
                    'index' => '列表',
                )
            ),
            'ReplyText' => array(
                'name' => '文本回复',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'status' => '状态',
                    'del' => '删除',
                )
            ),
            'ReplyImage' => array(
                'name' => '图片回复',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'status' => '状态',
                    'del' => '删除',
                )
            ),
            'ReplyVideo' => array(
                'name' => '视频回复',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'status' => '状态',
                    'del' => '删除',
                )
            ),
            'ReplyVoice' => array(
                'name' => '语音回复',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'status' => '状态',
                    'del' => '删除',
                )
            ),
            'ReplyNews' => array(
                'name' => '图文回复',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'status' => '状态',
                    'del' => '删除',
                )
            ),
        );
    }


}
