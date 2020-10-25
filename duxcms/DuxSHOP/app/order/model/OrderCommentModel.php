<?php

/**
 * 订单货品评价
 */
namespace app\order\model;

use app\system\model\SystemModel;

class OrderCommentModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'comment_id',
    ];

    protected function base($where) {
        $base = $this->table('order_comment(A)')
            ->join('member_user(B)', ['B.user_id', 'A.user_id'])
            ->join('order_goods(C)', ['C.id', 'A.order_goods_id'])
			->join('order(D)', ['D.order_id', 'C.order_id']);
        $field = ['A.*', 'B.email(user_email)', 'B.tel(user_tel)', 'B.nickname(user_nickname)', 'B.avatar(user_avatar)', 'C.goods_name', 'C.goods_name', 'C.goods_image', 'C.goods_url', 'C.goods_qty','C.goods_price', 'C.price_total', 'C.extend'];
        return $base->field($field)->where((array)$where);
    }

    public function loadList($where = array(), $limit = 0, $order = '') {
        $list = $this->base($where)
            ->limit($limit)
            ->order('A.comment_id desc')
            ->select();
        foreach($list as $key => $vo) {
            $list[$key]['show_name'] = target('member/MemberUser')->getNickname($vo['user_nickname'], $vo['user_tel'], $vo['user_email']);
            $list[$key]['user_avatar'] = target('member/MemberUser')->getAvatar($vo['user_id']);
            $list[$key]['images'] = unserialize($vo['images']);
            $list[$key]['extend'] = unserialize($vo['extend']);
        }
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
        $where['A.comment_id'] = $id;
        return $this->getWhereInfo($where);
    }

}