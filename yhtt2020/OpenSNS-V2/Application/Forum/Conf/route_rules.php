<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/26
 * Time: 14:40
 * @author :  xjw129xjt（駿濤） xjt@ourstu.com
 */

//todo 论坛模块太乱了。。。駿濤
return array(
    'route_rules' => array(

        '/^forum(\/(ctime|reply)(\/(\d+)(\/(\d+)(.*?))?)?)?$/' => is_mobile() ? 'mob/forum/index?order=:2&type_id=:4' : 'forum/index/forum?order=:2&id=:4&page=:6',
        '/^forum\/(all|hot|top)$/' => is_mobile() ? 'mob/forum/index?type=:1' : 'forum/index/forum',
        '/^forum(\/other)?\/(\d+)(\/(\d+)(.*?))?$/' => is_mobile() ? 'mob/forum/index?type=other&type_id=:2' : 'forum/index/forum?id=:2&page=:4',
        'forum/index/[:page\d]$' => is_mobile() ? 'mob/forum/index' : 'forum/index/index',
        'forum/lists' => is_mobile() ? 'mob/forum/index' : 'forum/index/lists',

        'forum/detail/:id\d' => is_mobile() ? 'mob/forum/postDetail' : 'forum/index/detail',

        'forum/editreply/:reply_id\d' => is_mobile() ? 'mob/forum/addComment?is_edit=1' : 'forum/index/editreply',
        '/^forum\/edit\/(\d+)$/' => is_mobile() ? 'mob/forum/addpost?isedit=1&postid=:1' : 'forum/index/edit?post_id=:1',
        'forum/look' => is_mobile() ? 'mob/forum/index' : 'forum/index/look',

        'forum/search' => is_mobile() ? 'mob/forum/index' : 'forum/index/search',
    ),
    'router' => array(
        /*论坛*/
        'forum/index/forum' => 'forum/forum/[order]/[id]/[page]',
        'forum/index/index' => 'forum/index/[page]',
        'forum/index/lists' => 'forum/lists',
        'forum/index/detail' => 'forum/detail/[id]',
        'forum/index/edit' => 'forum/edit/[post_id]',

        'forum/index/editreply' => 'forum/editreply/[reply_id]',
        'forum/index/look' => 'forum/look',
        'forum/index/search' => 'forum/search',


    )

);