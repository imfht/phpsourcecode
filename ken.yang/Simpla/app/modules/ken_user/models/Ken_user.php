<?php

/*
 * 测试models
 */

class Ken_user extends Eloquent {

    public static function new_user_list() {
        //获取区块信息
        $block = Block::where('machine_name', '=', 'ken_new_user_list')->first();
        $users = User::where('status', 1)->orderBy('created_at', 'desc')->take(5)->get();
        $html = View::make('ken_user::new_user_list', array('block' => $block, 'users' => $users));
        return $html;
    }

}
