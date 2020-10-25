<?php

/**
 * 商品咨询
 */
namespace app\shop\model;

use app\system\model\SystemModel;

class ShopFaqModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'faq_id',
    ];

    protected function base($where) {
        $base = $this->table('shop_faq(A)')
            ->join('member_user(B)', ['B.user_id', 'A.user_id']);
        $field = ['A.*', 'B.email(user_email)', 'B.tel(user_tel)', 'B.nickname(user_nickname)', 'B.avatar(user_avatar)'];
        return $base->field($field)->where((array)$where);
    }

    public function loadList($where = array(), $limit = 0, $order = '') {
        $list = $this->base($where)
            ->limit($limit)
            ->order('A.faq_id desc')
            ->select();
        foreach($list as $key => $vo) {
            $list[$key]['show_name'] = target('member/MemberUser')->getNickname($vo['user_nickname'], $vo['user_tel'], $vo['user_email']);
            $list[$key]['user_avatar'] = target('member/MemberUser')->getAvatar($vo['user_id']);
            $list[$key]['images'] = unserialize($vo['images']);
            $list[$key]['extend'] = unserialize($vo['extend']);
        }
        return $list;
    }

    public function countList($where = array()) {
        return $this->base($where)->count();
    }

    public function getWhereInfo($where) {
        $info = $this->base($where)->find();
        return $info;
    }

    public function getInfo($id) {
        $where = [];
        $where['A.faq_id'] = $id;
        return $this->getWhereInfo($where);
    }


}