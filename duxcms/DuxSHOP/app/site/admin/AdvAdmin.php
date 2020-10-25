<?php

/**
 * 广告管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\site\admin;

class AdvAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'SiteAdv';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '广告管理',
                'description' => '管理站点广告信息',
            ],
            'fun' => [
                'index' => true,
                'add' => true,
                'edit' => true,
                'status' => true,
                'del' => true,
            ]
        ];
    }

    public function _indexParam() {
        return [
            'keyword' => 'title',
            'pos_id' => 'pos_id'
        ];
    }

    public function _indexOrder() {
        return 'adv_id desc';
    }

    protected function _indexAssign($pageMaps) {
        $posList = target('site/SiteAdvPosition')->loadList([], 0, 'sort asc');
        $id = $pageMaps['pos_id'];
        return array(
            'posList' => $posList,
            'posId' => $id
        );
    }

    protected function _addAssign() {
        $posId = request('get', 'pos_id', 0);
        $info = [];
        if($posId) {
            $info['pos_id'] = $posId;
        }
        $config = target('system/SystemInfo')->getConfig();
        $urlField = explode(',', $config['adv_url_field']);
        return array(
            'posList' =>target('site/SiteAdvPosition')->loadList([], 0, 'sort asc'),
            'urlField' => $urlField,
            'info' => $info
        );
    }

    protected function _editAssign($info) {
        $config = target('system/SystemInfo')->getConfig();
        $urlField = explode(',', $config['adv_url_field']);
        return array(
            'posList' =>target('site/SiteAdvPosition')->loadList([], 0, 'sort asc'),
            'urlField' => $urlField,
        );
    }

}