<?php

/**
 * 广告位管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\site\admin;

class AdvPositionAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'SiteAdvPosition';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '广告位管理',
                'description' => '管理站点广告位信息',
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
	
	protected function _indexAssign($pageMaps) {
        $regionList = target('site/SiteAdvRegion')->loadList();
        $id = $pageMaps['region_id'];
        return array(
            'regionList' => $regionList,
            'regionId' => $id
        );
    }

    protected function _addAssign() {
        $regionId = request('get', 'region_id', 0);
        $info = [];
        if($regionId) {
            $info['region_id'] = $regionId;
        }
        return array(
            'regionList' =>target('site/SiteAdvRegion')->loadList(),
            'info' => $info
        );
    }

    protected function _editAssign($info) {
        return array(
            'regionList' =>target('site/SiteAdvRegion')->loadList(),
        );
    }

    public function _indexParam() {
        return [
            'keyword' => 'name',
			'region_id' => 'region_id'
        ];
    }

    public function _indexOrder() {
        return 'sort asc,pos_id asc';
    }

}