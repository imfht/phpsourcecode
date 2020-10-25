<?php

/**
 * 会员信息
 */
namespace app\member\model;

use app\system\model\SystemModel;

class MemberFeedbackModel extends SystemModel {


    protected $infoModel = [
        'pri' => 'feedback_id',
    ];

    protected function base($where) {
        return $this->table('member_feedback(A)')
            ->join('member_user(B)', ['B.user_id', 'A.user_id'])
            ->field(['A.*', 'B.email(user_email)', 'B.tel(user_tel)', 'B.nickname(user_nickname)'])
            ->where((array)$where);
    }

    public function loadList($where = array(), $limit = 0, $order = '') {
        $list = $this->base($where)
            ->limit($limit)
            ->order($order)
            ->select();
        return $list;
    }

    public function countList($where = array()) {
        return $this->base($where)->count();
    }

    public function getWhereInfo($where) {
        return $this->base($where)->find();
    }


}