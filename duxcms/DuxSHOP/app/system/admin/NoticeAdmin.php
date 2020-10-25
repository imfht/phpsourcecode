<?php

/**
 * 通知管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\system\admin;

class NoticeAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'SystemNotice';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '通知管理',
                'description' => '查看系统内应用通知',
            ],
            'fun' => [
                'index' => true,
                'del' => true,
            ]
        ];
    }

    public function _indexParam() {
        return [
            'keyword' => 'name'
        ];
    }

}