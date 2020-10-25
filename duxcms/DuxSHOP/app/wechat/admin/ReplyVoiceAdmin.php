<?php

/**
 * 语音回复
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\wechat\admin;


class ReplyVoiceAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'WechatReplyMedia';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return array(
            'info' => array(
                'name' => '语音回复',
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

    public function _indexWhere($whereMaps) {
        $whereMaps['B.type'] = 'voice';
        return $whereMaps;

    }

    public function _editAssign($info) {

        $where = [];
        $where['media_id'] = $info['media_id'];
        $materialInfo = target('wechat/WechatMaterialVoice')->getWhereInfo($where);

        return [
            'materialInfo' => $materialInfo
        ];

    }


}