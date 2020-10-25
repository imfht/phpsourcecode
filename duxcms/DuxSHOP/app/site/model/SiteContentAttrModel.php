<?php

/**
 * 筛选内容属性
 */
namespace app\site\model;

use app\system\model\SystemModel;

class SiteContentAttrModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'id'
    ];

    protected function base($where) {
        $base = $this->table('site_content_attr(A)')
            ->where($where)
            ->join('site_filter_attr(B)', ['B.attr_id', 'A.attr_id']);
        return $base->field(['A.*', 'B.type', 'B.name']);
    }

    public function loadList($where = [], $limit = 0, $order = '') {
        $list = $this->base($where)
            ->limit($limit)
            ->order('A.id desc')
            ->select();
        return $list;
    }

    public function countList($where = []) {
        return $this->base($where)->count();
    }

    public function getWhereInfo($where) {
        $info = $this->base($where)->find();
        return $info;
    }

    public function getInfo($id) {
        $where = [];
        $where['A.id'] = $id;

        return $this->getWhereInfo($where);
    }


}