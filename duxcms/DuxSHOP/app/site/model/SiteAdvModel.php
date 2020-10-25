<?php

/**
 * 广告管理
 */
namespace app\site\model;

use app\system\model\SystemModel;

class SiteAdvModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'adv_id',
        'validate' => [
            'title' => [
                'len' => ['1, 250', '广告名称输入不正确!', 'must', 'all'],
            ],
        ],
        'format' => [
            'title' => [
                'function' => ['html_clear', 'all'],
            ],
            'content' => [
                'function' => ['html_clear', 'all'],
            ],
            'start_time' => [
                'function' => ['strtotime', 'all'],
            ],
            'stop_time' => [
                'function' => ['strtotime', 'all'],
            ]
        ],
        'into' => '',
        'out' => '',
    ];

    public function loadList($where = [], $limit = 0, $order = '') {
        $list = parent::loadList($where, $limit, $order);
        foreach ($list as $key => $vo) {
            $urlExt = unserialize($vo['url_ext']);
            $list[$key]['url_ext'] = $urlExt;
            if(is_array($urlExt)) {
                foreach ($urlExt as $k => $v) {
                    $list[$key]['url_' . $k] = $v;
                }
            }
        }
        return $list;
    }

    public function getWhereInfo($where) {
        $info = parent::getWhereInfo($where);
        if($info) {
            $info['url_ext'] = unserialize($info['url_ext']);
        }
        return $info;
    }

    public function _saveBefore($data, $type) {
        $data['url_ext'] = serialize($_POST['url_ext']);
        return $data;
    }


}