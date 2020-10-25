<?php

/**
 * 银行管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\member\admin;

class PayCardAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'PayCard';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '银行卡管理',
                'description' => '管理会员银行卡信息',
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
            'keyword' => 'account'
        ];
    }

    public function _indexOrder() {
        return 'card_id desc';
    }

    public function _addAssign() {
        return [
            'cardType' => target('member/PayBank')->getType(),
            'bankList' => target('member/PayBank')->loadList()
        ];
    }

    public function _editAssign() {
        return [
            'cardType' => target('member/PayBank')->getType(),
            'bankList' => target('member/PayBank')->loadList()
        ];
    }

    public function _addBefore() {
        $label = $_POST['label'];
        $info = target('member/PayBank')->getWhereInfo([
            'label' => $label
        ]);
        $_POST['bank'] = $info['name'];
        $_POST['bank_color'] = $info['bank_color'];
    }

    public function _editBefore() {
        $label = $_POST['label'];
        $info = target('member/PayBank')->getWhereInfo([
            'label' => $label
        ]);
        $_POST['bank'] = $info['name'];
        $_POST['bank_color'] = $info['bank_color'];
    }


}