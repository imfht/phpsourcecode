<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/26
 * Time: 14:40
 * @author :  xjw129xjt（駿濤） xjt@ourstu.com
 */

return array(
    'route_rules' => array(
        'event/detail/[:id\d]' => is_mobile() ? 'mob/event/detail' : 'event/index/detail',
        'event/edit/[:id\d]' => is_mobile() ? 'mob/event/edit' : 'event/index/edit',
        'event/member/[:id\d]/[:tip]' => is_mobile() ? 'mob/event/member' : 'event/index/member',



//        'event/[:norh]/[:type_id\d]/[:page\d]' => is_mobile() ? 'mob/event/index' : 'event/index/index',
        'event/[:page\d]$' => is_mobile() ? 'mob/event/index' : 'event/index/index',
        'event/[:type_id\d]/[:page\d]$' => is_mobile() ? 'mob/event/index' : 'event/index/index',


        'myevent/[:lora]/[:type_id\d]/[:page\d]' => is_mobile() ? 'mob/event/myevent' : 'event/index/myevent',
        'myevent/[:page\d]' => is_mobile() ? 'mob/event/myevent' : 'event/index/myevent',
        'myevent/[:type_id\d]/[:page\d]' => is_mobile() ? 'mob/event/myevent' : 'event/index/myevent',



    ),
    'router' => array(
        /*活动*/
        'event/index/add' => 'event/add',
//        'event/index/index' => 'event/[norh]/[type_id]/[page]',


        'event/index/myevent' => 'myevent/[lora]/[type_id]/[page]',
        'event/index/detail' => 'event/detail/[id]',
        'event/index/member' => 'event/member/[id]/[tip]',
        'event/index/edit' => 'event/edit/[id]',

        //    'event/index/member' => 'event/member_[id]',
        //  'event/index/edit' => 'event/edit_[id]',




    )
);