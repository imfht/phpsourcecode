<?php

/**
 * 资金统计管理
 */

namespace app\member\model;

use app\system\model\SystemModel;

class PayStatsModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'stats_id',
    ];

    protected function base($where) {
        return $this->table('pay_stats(A)')
            ->join('member_user(B)', ['B.user_id', 'A.user_id'])
            ->field(['A.*', 'B.email(user_email)', 'B.tel(user_tel)', 'B.nickname(user_nickname)'])
            ->where((array)$where);
    }

    public function loadList($where = [], $limit = 0, $order = 'stats_id desc') {
        $list = $this->base($where)
            ->limit($limit)
            ->order($order)
            ->select();
        foreach ($list as $key => $vo) {
            $list[$key]['show_name'] = target('member/MemberUser')->getNickname($vo['user_nickname'], $vo['user_tel'], $vo['user_email']);
        }

        return $list;
    }

    public function countList($where = []) {
        return $this->base($where)->count();
    }

    public function getWhereInfo($where) {
        $info = $this->base($where)->find();
        if ($info) {
            $info['show_name'] = target('member/MemberUser')->getNickname($info['user_nickname'], $info['user_tel'], $info['user_email']);
        }

        return $info;
    }

    public function stats($userId, $money, $inc = true, $type = 'pay') {
        $time = strtotime(date('Y-m-d'));
        $info = $this->getWhereInfo([
            'A.time' => $time,
            'type' => $type
        ]);
        if ($inc) {
            $key = 'charge';
        } else {
            $key = 'spend';
        }
        if (empty($info)) {
            $status = $this->add([
                'user_id' => $userId,
                'time' => $time,
                $key => $money,
                'type' => $type,
                $key . '_num' => 1
            ]);
        } else {
            $status = $this->where([
                'stats_id' => $info['stats_id']
            ])->setInc($key, $money);
            if(!$status) {
                return false;
            }
            $status = $this->where([
                'stats_id' => $info['stats_id']
            ])->setInc($key . '_num', 1);
        }
        if(!$status) {
            return false;
        }
        return true;
    }


}