<?php

/**
 * 会员充值
 */
namespace app\member\model;

use app\system\model\SystemModel;

class PayRechargeModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'recharge_id',
    ];

    protected function base($where) {
        return $this->table('pay_recharge(A)')
            ->join('member_user(B)', ['B.user_id', 'A.user_id'])
            ->field(['A.*', 'B.email(user_email)', 'B.tel(user_tel)', 'B.nickname(user_nickname)'])
            ->where((array)$where);
    }

    public function loadList($where = array(), $limit = 0, $order = 'recharge_id desc') {
        $list = $this->base($where)
            ->limit($limit)
            ->order($order)
            ->select();
        foreach ($list as $key => $vo) {
            $list[$key]['show_name'] = target('member/MemberUser')->getNickname($vo['user_nickname'], $vo['user_tel'], $vo['user_email']);
            $list[$key]['show_time'] = date('Y-m-d', $vo['create_time']);
            $list[$key]['status_name'] = $vo['status'] ? '已完成' : '未完成';
        }
        return $list;
    }

    public function countList($where = array()) {
        return $this->base($where)->count();
    }

    public function showList($data) {
        foreach ($data as $key => $vo) {
            $data[$key]['url'] = url('member/Recharge/info', ['no' => $vo['recharge_no']]);
        }
        return $data;
    }

    public function getWhereInfo($where) {
        $info = $this->base($where)->find();
        if($info) {
            $info['show_name'] = target('member/MemberUser')->getNickname($info['user_nickname'], $info['user_tel'], $info['user_email']);
            $info['show_time'] = date('Y-m-d', $info['create_time']);
        }
        return $info;
    }

    public function showInfo($info) {
        if($info) {
            $info['url'] = url('member/Recharge/info', ['no' => $info['recharge_no']]);
        }
        return $info;
    }
}