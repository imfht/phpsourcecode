<?php

/**
 * 文字回复
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\wechat\admin;


class ReplyTextAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'WechatReplyText';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return array(
            'info' => array(
                'name' => '文字回复',
                'description' => '管理微信自动回复内容',
            ),
            'fun' => [
                'index' => true,
                'add' => true,
                'edit' => true,
                'del' => true,
            ]
        );
    }

    public function _indexParam() {
        return [
            'keyword' => 'name'
        ];
    }


}