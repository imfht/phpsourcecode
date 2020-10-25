<?php
/* Copyright 2015  JefferyWang  (email: admin@wangjunfeng.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function jw_wlu_user_level($user_email, $admin_email, $level_name, $level_count) {
    global $wpdb;

    // 如果为管理员账号，则直接返回
    if ($user_email == $admin_email) {
        return '<span class="ua user-admin-gw user-level-gw"><i>站长</i></span>';
    }

    $querystr = "SELECT `comment_author_email` FROM `wp_comments` where `comment_author_email`='" . $user_email . "' AND `comment_author_email` <> '' AND `comment_author_email` <> '$admin_email'";
    $results = $wpdb->get_results($querystr);
    $mun = (count($results));

    if ($mun <= $level_count[0]) {
        $level = 1;
        $level_type = "1-4";
    } elseif ($mun <= $level_count[1]) {
        $level = 2;
        $level_type = "1-4";
    } elseif ($mun <= $level_count[2]) {
        $level = 3;
        $level_type = "1-4";
    } elseif ($mun <= $level_count[3]) {
        $level = 4;
        $level_type = "1-4";
    } elseif ($mun <= $level_count[4]) {
        $level = 5;
        $level_type = "5-7";
    } elseif ($mun <= $level_count[5]) {
        $level = 6;
        $level_type = "5-7";
    } elseif ($mun <= $level_count[6]) {
        $level = 7;
        $level_type = "5-7";
    } elseif ($mun <= $level_count[7]) {
        $level = 8;
        $level_type = "8-10";
    } elseif ($mun <= $level_count[8]) {
        $level = 9;
        $level_type = "8-10";
    } else {
        $level = 10;
        $level_type = "8-10";
    }
    $level_array = $level_name;
    return '<span class="ua user-level-gw user-' . $level_type . '-gw user-' . $level . '-gw" title="等级'
    . $level . '：' . $level_array[$level-1] . '"><i>' . $level_array[$level-1] . '</i></span>';
}
