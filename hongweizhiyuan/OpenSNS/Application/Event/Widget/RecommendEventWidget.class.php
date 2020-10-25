<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Event\Widget;

use Think\Action;

/**
 * 推荐活动widget
 * 用于动态调用分类信息
 */
class RecommendEventWidget extends Action
{

    /* 显示指定分类的同级分类或子分类列表 */
    public function recommendEvent()
    {

        $rec_event = D('Event')->where(array('is_recommend' => 1))->limit(2)->order('rand()')->select();
        foreach ($rec_event as &$v) {
            $v['user'] = query_user(array('id', 'username', 'nickname', 'space_url', 'space_link', 'avatar128', 'rank_html'), $v['uid']);
            $v['type'] = $this->getType($v['type_id']);
            $v['check_isSign'] = D('event_attend')->where(array('uid' => is_login(), 'event_id' => $v['id']))->select();
        }
        unset($v);
        $this->assign('rec_event', $rec_event);
        $this->display(T('Event@Widget/recommend'));
    }
    /**
     * 获取活动类型
     * @param $type_id
     * @return mixed
     * autor:xjw129xjt
     */
    private function getType($type_id)
    {

        $type = D('EventType')->where('id=' . $type_id)->find();
        return $type;
    }
}
