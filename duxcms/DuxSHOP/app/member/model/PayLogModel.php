<?php

/**
 * 资金记录管理
 */
namespace app\member\model;

use app\system\model\SystemModel;

class PayLogModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'log_id',
    ];

    protected function base($where) {
        return $this->table('pay_log(A)')
            ->join('member_user(B)', ['B.user_id', 'A.user_id'])
            ->field(['A.*', 'B.email(user_email)', 'B.tel(user_tel)', 'B.nickname(user_nickname)', 'B.user_id'])
            ->where((array)$where);
    }

    public function loadList($where = array(), $limit = 0, $order = 'log_id desc') {
        $list = $this->base($where)
            ->limit($limit)
            ->order($order)
            ->select();
        foreach ($list as $key => $vo) {
            $list[$key]['show_name'] = target('member/MemberUser')->getNickname($vo['user_nickname'], $vo['user_tel'], $vo['user_email']);
            $list[$key]['title'] = $vo['title'] ? $vo['title'] : $vo['pay_name'];
            $list[$key]['show_time'] = date('Y-m-d', $vo['time']);
        }
        return $list;
    }

    public function countList($where = array()) {
        return $this->base($where)->count();
    }

    public function showList($data) {
        foreach ($data as $key => $vo) {
            $data[$key]['url'] = url('member/Finance/info', ['no' => $vo['log_no']]);
        }
        return $data;
    }

    public function getWhereInfo($where) {
        $info = $this->base($where)->find();
        if($info) {
            $info['show_name'] = target('member/MemberUser')->getNickname($info['user_nickname'], $info['user_tel'], $info['user_email']);
            $info['title'] = $info['title'] ? $info['title'] : $info['pay_name'];
        }
        return $info;
    }

    public function showInfo($info) {
        if($info) {
            $info['url'] = url('member/Finance/info', ['no' => $info['log_no']]);
        }
        return $info;
    }


}