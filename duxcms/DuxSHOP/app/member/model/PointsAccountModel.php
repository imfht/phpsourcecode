<?php

/**
 * 积分账户管理
 */
namespace app\member\model;

use app\system\model\SystemModel;

class PointsAccountModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'account_id',
    ];

    protected function base($where) {
        return $this->table('points_account(A)')
            ->join('member_user(B)', ['B.user_id', 'A.user_id'])
            ->field(['A.*', 'B.email(user_email)', 'B.tel(user_tel)', 'B.nickname(user_nickname)'])
            ->where((array)$where);
    }

    public function loadList($where = array(), $limit = 0, $order = '') {
        $list = $this->base($where)
            ->limit($limit)
            ->order($order)
            ->select();
        foreach ($list as $key => $vo) {
            $list[$key]['show_name'] = target('member/MemberUser')->getNickname($vo['user_nickname'], $vo['user_tel'], $vo['user_email']);
            $list[$key]['money'] = target('member/PointsCharge')->where([
                'user_id' => $vo['user_id'],
                'status' => 1,
                '_sql' => 'start_time <= ' . time() . ' AND stop_time >= ' . time()
            ])->sum('money');
        }
        return $list;
    }

    public function countList($where = array()) {
        return $this->base($where)->count();
    }

    public function getWhereInfo($where) {
        $info = $this->base($where)->find();
        if($info) {
            $info['show_name'] = target('member/MemberUser')->getNickname($info['user_nickname'], $info['user_tel'], $info['user_email']);
            $info['money'] = target('member/PointsCharge')->where([
                'user_id' => $info['user_id'],
                'status' => 1,
                '_sql' => 'start_time <= ' . time() . ' AND stop_time >= ' . time()
            ])->sum('money');
        }
        return $info;
    }


}