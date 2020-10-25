<?php

/**
 * 用户管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\member\admin;

class MemberUserAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'MemberUser';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '用户管理',
                'description' => '管理注册用户',
            ],
            'fun' => [
                'index' => true,
                'add' => true,
                'edit' => true,
                'del' => true,
                'status' => true,
            ]
        ];
    }

    public function _indexParam() {
        return [
            'keyword' => 'A.tel,A.email,A.nickname'
        ];
    }

    public function _indexOrder() {
        return 'user_id desc';
    }

    public function _indexAssign() {
        return [
            'payTypeList' => target($this->_model)->typeList()
        ];
    }

    protected function _addAssign() {
        return array(
            'roleList' =>target('member/MemberRole')->loadList(),
            'gradeList' =>target('member/MemberGrade')->loadList()
        );
    }

    protected function _editAssign($info) {
        return array(
            'roleList' => target('member/MemberRole')->loadList(),
            'gradeList' =>target('member/MemberGrade')->loadList()
        );
    }

    protected function _delBefore($id) {
        if ($id == 1) {
            $this->error('保留用户无法删除！');
        }
    }

    public function dialogUser() {
        $userId = request('get', 'id', 0 , 'intval');
        if(empty($userId)) {
            $this->systemDialogError('该用户不存在!');
        }
        $info = target('member/MemberUser')->getInfo($userId);
        $account = target('member/PayAccount')->getWhereInfo([
            'A.user_id' => $userId
        ]);
        $this->assign('info', $info);
        $this->assign('account', $account);
        $this->dialogDisplay();
    }

    public function ajaxList() {
        $keyword = request('get', 'q', '', 'html_clear');
        $list = target($this->_model)->loadList(['_sql' => 'A.nickname like "%'.$keyword.'%" OR A.email like "%'.$keyword.'%" OR A.tel like "%'.$keyword.'%"']);
        foreach ($list as $key => $vo) {
            $desc = [];
            $desc[] = $vo['email'] ? $vo['email'] : '';
            $desc[] = $vo['tel'] ? $vo['tel'] : '';
            $list[$key]['id'] = $vo['user_id'];
            $list[$key]['text'] = $vo['show_name'];
            $list[$key]['image'] = $vo['avatar'];
            $list[$key]['desc'] =  implode(' ', $desc);
        }

        $this->json([
            'items' => $list,
            'total_count' => count($list),
            'incomplete_results' => false
        ]);
    }

}