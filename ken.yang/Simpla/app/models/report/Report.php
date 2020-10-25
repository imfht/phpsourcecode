<?php

/*
 * 内容
 */

class Report extends Eloquent {

    /**
     * 获取最近注册的用户数量信息
     * @return array
     */
    public static function get_register_count() {
        $register_count = array();
        $register_count['today'] = User::where('created_at', '>=', date('Y-m-d H:i', Base::get_today_time()))->count();
        $register_count['week'] = User::where('created_at', '>=', date('Y-m-d H:i', Base::get_week_time()))->count();
        $register_count['month'] = User::where('created_at', '>=', date('Y-m-d H:i', Base::get_month_time()))->count();
        $register_count['all'] = User::count();
        return $register_count;
    }

    /**
     * 获取最近发布的文章数量信息
     * @return array
     */
    public static function get_node_count() {
        $node_count = array();
        $node_count['today'] = Node::where('created_at', '>=', date('Y-m-d H:i', Base::get_today_time()))->count();
        $node_count['week'] = Node::where('created_at', '>=', date('Y-m-d H:i', Base::get_week_time()))->count();
        $node_count['month'] = Node::where('created_at', '>=', date('Y-m-d H:i', Base::get_month_time()))->count();
        $node_count['all'] = Node::count();
        return $node_count;
    }

}
