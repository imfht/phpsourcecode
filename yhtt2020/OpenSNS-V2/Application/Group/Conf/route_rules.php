<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/25
 * Time: 14:40
 * @author :  DDG佳炜 fjw@ourstu.com
 */

return array(
    'route_rules' => array(
        'group/discover' => is_mobile() ? 'mob/group/index?mark=discover' : 'group/index/discover',
        'group/mygroup/[:page\d]' => is_mobile() ? 'mob/group/index?mark=myGroup' : 'group/index/my',
        'group/select' => is_mobile() ? 'mob/group/index?mark=select' : 'group/index/select',
        //'group/[:id\d]' => is_mobile() ? 'mob/group/group' : 'group/index/group',
        //'/^group\/(\d+)(\/p_(\d+)(\D*?))?$/' => is_mobile() ? 'mob/group/index' : 'group/index/group?id=:1&page=:3',
        //'/^group\/(\d+)\/(post|new|member)(\/p_(\d+)(\D*?))?$/' => is_mobile() ? 'mob/group/index' : 'group/index/group?id=:1&type=:2&page=:4',
        //'/^group\/(\d+)\/(ctime|reply)$/' => is_mobile() ? 'mob/group/index' : 'group/index/group?id=:1&order=:2',
        //'/^group\/(\d+)\/(\d+)(\/p_(\d+))?(.*?)$/' => is_mobile() ? 'mob/group/index' : 'group/index/group?id=:1&cate=:2&page=:4',
        'group/create' => is_mobile() ? 'mob/group/index' : 'group/index/create',
        'group/detail/[:id\d]' => is_mobile() ? 'mob/group/detail' : 'group/index/detail',
        '/^group\/edit(\/g_(\d+))?(\/(\d+))?$/' => is_mobile() ? 'mob/group/index' : 'group/index/edit?group_id=:2&post_id=:4',
        'group/editreply/[:reply_id\d]' => is_mobile() ? 'mob/group/index' : 'group/index/editreply',
        'group/search' => is_mobile() ? 'mob/group/index' : 'group/index/search',


        '/^group\/manage\/member\/(\d+)(\/(\d+))?(\/p_(\d+))?(.*?)$/' => is_mobile() ? 'mob/group/index' : 'group/manage/member?group_id=:1&status=:3&page=:5',


        'group/manage/notice/[:group_id\d]' => is_mobile() ? 'mob/group/index' : 'group/manage/notice',
        'group/manage/category/[:group_id\d]' => is_mobile() ? 'mob/group/index' : 'group/manage/category',
        'group/manage/[:group_id\d]' => is_mobile() ? 'mob/group/index' : 'group/manage/index',
        'groups' => is_mobile() ? 'mob/group/index' : 'group/index/groups',
    ),
    'router' => array(
        'group/index/index' => 'group',
        'group/index/my' => 'group/mygroup/[page]',
        'group/index/groups' => 'groups',
        'group/index/discover' => 'group/discover',
        'group/index/select' => 'group/select',
        //'group/index/group' => 'group/[id]/[order]/[type]/[cate]/p_[page]',
        'group/index/edit' => 'group/edit/g_[group_id]/[post_id]',
        'group/index/create' => 'group/create',
        'group/index/detail' => 'group/detail/[id]',
        'group/index/editreply' => 'group/editreply/[reply_id]',
        'group/index/search' => 'group/search',
        'group/manage/index' => 'group/manage/[group_id]',
        'group/manage/member' => 'group/manage/member/[group_id]/[status]/p_[page]',
        'group/manage/notice' => 'group/manage/notice/[group_id]',
        'group/manage/category' => 'group/manage/category/[group_id]',

    )
);