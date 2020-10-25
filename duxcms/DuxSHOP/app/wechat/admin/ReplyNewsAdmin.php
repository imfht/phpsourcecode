<?php

/**
 * 图文回复
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\wechat\admin;


class ReplyNewsAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'WechatReplyMedia';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return array(
            'info' => array(
                'name' => '图文回复',
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
        $whereMaps['B.type'] = 'news';
        return $whereMaps;

    }

    public function _editAssign($info) {

        $where = [];
        $where['media_id'] = $info['media_id'];
        $materialInfo = target('wechat/WechatMaterialNews')->getWhereInfo($where);

        $materialInfo['data'] = unserialize($materialInfo['data']);

        return [
            'materialInfo' => $materialInfo
        ];



    }


}