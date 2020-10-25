<?php

/**
 * 用户管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\sale\admin;

class UserAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'SaleUser';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '推广用户',
                'description' => '管理推广用户信息',
            ],
            'fun' => [
                'index' => true,
                'edit' => true,
                'del' => true,
                'status' => true,
            ]
        ];
    }

    public function _indexAssign() {

        return array(
            'levelList' =>target('sale/SaleUserLevel')->loadList()
        );
    }

    public function _indexParam() {
        return [
            'username' => 'username',
            'agent' => 'A.agent',
            'level' => 'A.level_id',
            'parent_id' => 'A.user_id'
        ];
    }

    public function _indexWhere($where) {
        if($where['username']) {
            $where['_sql'] = "B.nickname = '{$where['username']}' OR B.tel= '{$where['username']}' OR B.email= '{$where['username']}' ";
            unset($where['username']);
        }
        return $where;
    }

    public function _indexOrder() {
        return 'user_id desc';
    }

    protected function _addAssign() {
        return array(
            'levelList' =>target('sale/SaleUserLevel')->loadList()
        );
    }

    protected function _editAssign($info) {
        return array(
            'levelList' => target('sale/SaleUserLevel')->loadList()
        );
    }


    protected function _editBefore() {
        if ($_POST['parent_id'] == $_POST['user_id']) {
            $this->error('您不能将当前用户设置为上级!');
        }
        $cat = target($this->_model)->loadTreeList([], 0, '', $_POST['user_id']);
        if($cat) {
            foreach ($cat as $vo) {
                if ($_POST['parent_id'] == $vo['user_id']) {
                    $this->error('不可以将上级移动到下级');
                }
            }
        }
    }


    public function search() {
        $keyword = request('', 'keyword');
        if(empty($keyword)) {
            $this->error('参数不正确！');
        }
        $info = target($this->_model)->getWhereInfo([
            '_sql' => 'A.user_id = :keyword OR B.tel = :keyword OR B.email = :keyword',
            ':keyword' => $keyword
        ]);
        if(empty($info)) {
            $this->error('用户不存在！');
        }
        $this->success($info);

    }

}