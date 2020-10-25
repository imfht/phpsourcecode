<?php

/**
 * 银行卡管理
 */
namespace app\member\model;

use app\system\model\SystemModel;

class PayCardModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'card_id',
        'validate' => [
            'bank' => [
                'empty' => ['', '请输入银行名称!', 'must', 'all'],
            ],
            'label' => [
                'empty' => ['', '请输入银行标识!', 'must', 'all'],
            ],
            'account' => [
                'empty' => ['', '银行卡号码输入错误!', 'must', 'all'],
            ],
            'account_name' => [
                'empty' => ['', '请输入开户姓名!', 'must', 'all'],
            ],
        ],
        'format' => [
            'account_name' => [
                'function' => ['html_clear', 'all'],
            ],
            'bank' => [
                'function' => ['html_clear', 'all'],
            ],
        ],
    ];

    protected function base($where) {
        return $this->table('pay_card(A)')
            ->join('member_user(B)', ['A.user_id', 'B.user_id'])
            ->join('pay_bank(C)', ['A.label', 'C.label'])
            ->field(['A.*', 'B.email(user_email)', 'B.tel(user_tel)', 'B.nickname(user_nickname)', 'C.logo'])
            ->where((array)$where);
    }

    public function loadList($where = array(), $limit = 0, $order = '') {
        $list = $this->base($where)
            ->limit($limit)
            ->order($order)
            ->select();

        foreach ($list as $key => $vo) {
            $list[$key]['show_name'] = target('member/MemberUser')->getNickname($vo['user_nickname'], $vo['user_tel'], $vo['user_email']);
            $list[$key]['type_name'] = target('member/PayBank')->getType($vo['type']);
            $list[$key]['logo'] = $vo['logo'] ? $vo['logo'] : (ROOT_URL . '/public/member/images/blank/'.$vo['label'].'.png');
        }
        return $list;
    }

    public function showList($data) {
        foreach ($data as $key => $vo) {
            $data[$key]['account'] = substr($vo['account'], -4);
        }
        return $data;
    }

    public function countList($where = array()) {
        return $this->base($where)->count();
    }

    public function getWhereInfo($where) {
        $info = $this->base($where)->find();
        if ($info) {
            $info['show_name'] = target('member/MemberUser')->getNickname($info['user_nickname'], $info['user_tel'], $info['user_email']);
            $info['type_name'] =target('member/PayBank')->getType($info['type']);
        }
        return $info;
    }

    public function showInfo($info) {
        if($info) {
            $info['account'] = substr($info['account'], -4);
        }
        return $info;
    }

}