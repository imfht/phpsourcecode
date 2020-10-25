<?php

/**
 * 验证码管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\member\admin;

class MemberVerifyAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'MemberVerify';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '验证码管理',
                'description' => '管理会员发送验证码',
            ],
            'fun' => [
                'index' => true,
                'status' => true,
                'del' => true,
            ]
        ];
    }

    public function _indexParam() {
        return [
            'keyword' => 'code'
        ];
    }

    public function _indexOrder() {
        return 'verify_id desc';
    }

}