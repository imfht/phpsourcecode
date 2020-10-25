<?php

/**
 * 导航管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\site\admin;

class NavAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'SiteNav';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '导航管理',
                'description' => '管理系统中的导航',
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
            'group_id' => 'group_id',
            'keyword' => 'name'
        ];
    }

    public function _indexWhere($whereMaps) {
        if(!$whereMaps['group_id']) {
            $whereMaps['group_id'] = 1;
        }
        return $whereMaps;
    }

    public function _indexPage() {
        return 100;
    }

    public function _indexAssign($pageMaps) {
        if($pageMaps['group_id']) {
            $groupId = $pageMaps['group_id'];
        }else{
            $groupId = 1;
        }
        return array(
            'groupList' =>target('site/SiteNavGroup')->loadList([], 0, 'group_id asc'),
            'groupId' => $groupId
        );
    }

    public function _indexUrl($id) {
        return url('index', array('group_id' => request('post', 'group_id')));
    }

    public function _indexData($where = [], $limit = 0, $order = 'sort asc, nav_id asc') {
        return target($this->_model)->loadTreeList($where, 0, $order);
    }

    protected function _addAssign() {
        $groupId = request('get', 'group_id', 1);
        return array(
            'navApiList' =>target('site/SiteNav')->getSiteNav(),
            'navList' => target($this->_model)->loadTreeList(['group_id' => $groupId]),
            'groupInfo' => target('site/SiteNavGroup')->getInfo($groupId),
            'groupId' => $groupId
        );
    }

    protected function _editAssign($info) {
        return array(
            'navApiList' =>target('site/SiteNav')->getSiteNav(),
            'navList' => target($this->_model)->loadTreeList(['group_id' => $info['group_id']]),
            'groupInfo' => target('site/SiteNavGroup')->getInfo($info['group_id']),
            'groupId' => $info['group_id']
        );
    }

    protected function _delBefore($id) {
        $cat = target($this->_model)->loadTreeList('', 0, '', $id);
        if($cat) {
            $this->error('清先删除子导航!');
        }
    }

}