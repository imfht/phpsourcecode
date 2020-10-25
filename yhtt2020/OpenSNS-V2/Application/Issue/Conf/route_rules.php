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
        'issue/detail/:id\d' => is_mobile() ? 'mob/issue/issuedetail' : 'issue/index/issuecontentdetail',
        'issue/edit' => is_mobile() ? 'mob/issue/addissue' : 'issue/index/index',
        'issue/[:page\d]' => is_mobile() ? 'mob/issue/index' : 'issue/index/index',
        'issue/masonry/[:page\d]' => is_mobile() ? 'mob/issue/index' : 'issue/index/index?display_type=masonry',
        'issue/list/[:page\d]' => is_mobile() ? 'mob/issue/index' : 'issue/index/index?display_type=list',
        'issue/add'=>is_mobile() ? 'mob/issue/addissue' : 'issue/add',
    ),
    'router' => array(
        'issue/index/index' => 'issue/[display_type]/[page]',
        'issue/index/issuecontentdetail' => 'issue/detail/[id]',
    )

);