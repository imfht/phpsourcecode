<?php

/**
 * 广告模块
 */

namespace app\site\api;

use \app\base\api\BaseApi;

class AdvApi extends BaseApi {

    /**
     * 获取广告
     */
    public function index() {

        $posInfo = target('site/SiteAdvPosition')->getWhereInfo([
            'label' => $this->data['pos']
        ]);
        if(empty($posInfo)) {
            $this->error404();
        }
        if(!$posInfo['status']) {
            $this->error404();
        }
        $where['pos_id'] = $posInfo['pos_id'];
        $where['_sql'][] = '(start_time = 0 OR start_time >= ' . time() . ')';
        $where['_sql'][] = '(stop_time = 0 OR stop_time <= ' . time() . ')';
        $where['status'] = 1;
        $list = target('site/SiteAdv')->loadList($where, 0, 'sort asc, adv_id asc');
        $this->success('ok', $list);
    }
	
    /**
     * 获取广告位列表
     */
    public function posList() {
		$regionInfo = target('site/SiteAdvRegion')->getWhereInfo([
            'label' => $this->data['region']
        ]);
		if(empty($regionInfo)) {
            $this->error404();
        }
        $posList = target('site/SiteAdvPosition')->loadList(['region_id' => $regionInfo['region_id'], 'status' => 1], 0, 'sort asc, pos_id asc');
        if(empty($posList)) {
            $this->error404();
        }
        $this->success('ok', $posList);
    }	

}