<?php

/*
 * 系统接口
 */

class Blocksystem extends Eloquent {

    /**
     * 接口列表
     * baid--区块区域，默认值：0
     * machine_name--机器名字（唯一），你可以通过该名字进行block读取
     * title--区块
     * description--描述（必填）
     * weight--位置，默认值：0
     * type--类型为system，固定值:system
     * status--状态，1为开启，0为结束,默认值:1开启
     * pages--在哪些页面进行展示,默认值：为空
     * cache--是否缓存,默认值:1开启
     * callback--方法名字（必填），需要知道通过那个方法来执行,例如：User::get();
     * @return string
     */
    public static function block() {
        $list = array();

        //最新文章
        $list[] = array(
            'machine_name' => 'system_new_posts',
            'title' => '最新文章',
            'description' => '最新文章',
            'callback' => 'Blocksystem::new_posts()',
        );

        //最新用户
        $list[] = array(
            'machine_name' => 'system_new_users',
            'title' => '新进用户',
            'description' => '新进用户',
            'callback' => 'Blocksystem::new_users()',
        );

        //友情连接
        $list[] = array(
            'machine_name' => 'system_friend_link',
            'title' => '友情连接',
            'description' => '友情连接',
            'callback' => 'Blocksystem::friend_link()',
        );

        return $list;
    }

    /**
     * 最新发表的文章
     */
    public static function new_posts() {
        //获取区块信息
        $block = Block::where('machine_name', '=', 'system_new_posts')->firstOrFail();
        $nodes = Node::orderBy('created_at', 'desc')->take(5)->get();
        $html = View::make('BackTheme::templates/block/template/node/new_posts', array('block' => $block, 'nodes' => $nodes));
        return $html;
    }

    /**
     * 最新注册的用户
     */
    public static function new_users() {
        //获取区块信息
        $block = Block::where('machine_name', '=', 'system_new_users')->first();
        $users = User::where('status', 1)->orderBy('created_at', 'desc')->take(5)->get();
        $html = View::make('BackTheme::templates/block/template/user/new_users', array('block' => $block, 'users' => $users));
        return $html;
    }

    /**
     * 友情连接
     */
    public static function friend_link() {
        //获取区块信息
        $block = Block::where('machine_name', '=', 'system_friend_link')->first();
        $links = Link::orderBy('weight', 'desc')->get()->toarray();
        $html = View::make('BackTheme::templates/block/template/link/friend_link', array('block' => $block, 'links' => $links));
        return $html;
    }

}
