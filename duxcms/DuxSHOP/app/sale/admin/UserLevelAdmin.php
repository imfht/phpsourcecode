<?php

/**
 * 等级管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\sale\admin;

class UserLevelAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'SaleUserLevel';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '等级管理',
                'description' => '管理推广等级信息',
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

    public function _indexOrder() {
        return 'special asc, level_where asc, level_id asc';
    }

    public function _indexAssign() {
        return [
            'saleConfig' => target('SaleConfig')->getConfig(),
            'levelWhereText' => target('SaleConfig')->levelWhereText()
        ];

    }

    public function _addAssign() {
        return [
            'saleConfig' => target('SaleConfig')->getConfig(),
            'levelWhereText' => target('SaleConfig')->levelWhereText()
        ];
    }


    public function _editAssign($info) {
        $info['comm_rate'] = unserialize($info['comm_rate']);
        return [
            'info' => $info,
            'saleConfig' => target('SaleConfig')->getConfig(),
            'levelWhereText' => target('SaleConfig')->levelWhereText()
        ];
    }

    public function _addBefore() {
        $_POST['comm_rate'] = serialize($_POST['comm_rate']);
    }

    public function _editBefore() {
        $_POST['comm_rate'] = serialize($_POST['comm_rate']);
    }

    protected function _delBefore($id) {
        if ($id == 1) {
            $this->error('保留等级无法删除！');
        }
        $countUser = target('sale/SaleUser')->countList([
            'A.level_id' => $id
        ]);
        $countUser = $countUser[0];
        if ($countUser > 0) {
            $this->error('请先删除该等级下的用户！');
        }
    }

}