<?php

/**
 * 系统设置
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\member\admin;

class MemberConfigManageAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'MemberInfo';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '会员配置管理',
                'description' => '管理会员设置信息参数',
            ],
            'fun' => [
                'index' => true,
                'add' => true,
                'edit' => true,
                'del' => true,
            ]
        ];
    }

    public function _indexParam() {
        return [
            'keyword' => 'name'
        ];
    }

    public function _indexWhere($where) {
        $where['reserve'] = 0;
        return $where;
    }

}