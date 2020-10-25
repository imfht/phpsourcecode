<?php
namespace app\wechat\service;
/**
 * 菜单接口
 */
class MenuService {
    /**
     * 获取菜单结构
     */
    public function getSystemMenu() {
        return array(
            'wechat' => array(
                'name' => '微信',
                'icon' => 'user',
                'order' => 96,
                'menu' => array(
                    array(
                        'name' => '设置',
                        'icon' => 'cog',
                        'order' => 0,
                        'menu' => array(
                            array(
                                'name' => '基本设置',
                                'icon' => 'cog',
                                'url' => url('wechat/WechatConfig/index'),
                                'order' => 0
                            ),
                            array(
                                'name' => '回复设置',
                                'icon' => 'bars',
                                'url' => url('wechat/ReplyConfig/index'),
                                'order' => 1
                            ),
                            array(
                                'name' => '自定义菜单',
                                'icon' => 'bars',
                                'url' => url('wechat/MenuConfig/index'),
                                'order' => 2
                            ),
                        )
                    ),
                    array(
                        'name' => '素材管理',
                        'icon' => 'bars',
                        'order' => 1,
                        'menu' => array(
                            array(
                                'name' => '图片素材',
                                'icon' => 'bars',
                                'url' => url('wechat/MaterialImage/index'),
                                'order' => 0
                            ),
                            array(
                                'name' => '视频素材',
                                'icon' => 'bars',
                                'url' => url('wechat/MaterialVideo/index'),
                                'order' => 1
                            ),
                            array(
                                'name' => '语音素材',
                                'icon' => 'bars',
                                'url' => url('wechat/MaterialVoice/index'),
                                'order' => 2
                            ),
                            array(
                                'name' => '图文素材',
                                'icon' => 'bars',
                                'url' => url('wechat/MaterialNews/index'),
                                'order' => 3
                            ),
                        )
                    ),
                    array(
                        'name' => '回复管理',
                        'order' => 2,
                        'icon' => 'reply',
                        'menu' => array(
                            array(
                                'name' => '文字回复',
                                'icon' => 'bars',
                                'url' => url('wechat/ReplyText/index'),
                                'order' => 1
                            ),
                            array(
                                'name' => '图片回复',
                                'icon' => 'bars',
                                'url' => url('wechat/ReplyImage/index'),
                                'order' => 2
                            ),
                            array(
                                'name' => '视频回复',
                                'icon' => 'bars',
                                'url' => url('wechat/ReplyVideo/index'),
                                'order' => 3
                            ),
                            array(
                                'name' => '语音回复',
                                'icon' => 'bars',
                                'url' => url('wechat/ReplyVoice/index'),
                                'order' => 4
                            ),
                            array(
                                'name' => '图文回复',
                                'icon' => 'bars',
                                'url' => url('wechat/ReplyNews/index'),
                                'order' => 5
                            ),
                        )
                    ),
                ),
            ),
        );
    }
}

