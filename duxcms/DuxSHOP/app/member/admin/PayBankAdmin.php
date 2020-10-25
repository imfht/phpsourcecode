<?php

/**
 * 银行管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\member\admin;

class PayBankAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'PayBank';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '银行管理',
                'description' => '管理银行卡等银行相关信息',
            ],
            'fun' => [
                'index' => true,
                'add' => true,
                'status' => true,
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



}