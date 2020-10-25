<?php

/**
 * 代理等级
 */
namespace app\member\model;

use app\system\model\SystemModel;

class MemberGradeModel extends SystemModel {
 
    protected $infoModel = [
        'pri' => 'grade_id',
    ];
 

    public function loadList($where= [], $limit = 0, $order = 'grade_id desc') {
        return parent::loadList($where, $limit, $order);
    }

}