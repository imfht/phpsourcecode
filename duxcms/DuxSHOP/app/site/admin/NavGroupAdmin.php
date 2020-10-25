<?php

/**
 * 导航分组
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\site\admin;

class NavGroupAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'SiteNavGroup';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '导航分组',
                'description' => '导航分组信息管理',
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

    protected function _delBefore($id) {
        if ($id == 1) {
            $this->error('保留分组无法删除！');
        }
        $count = target('site/SiteNav')->countList([
            'group_id' => $id
        ]);
        if ($count > 0) {
            $this->error('请先删除该分组下的导航！');
        }
    }

}