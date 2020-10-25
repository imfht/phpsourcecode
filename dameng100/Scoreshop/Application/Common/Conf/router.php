<?php
return array(

    /**
     * 路由的key必须写全称,且必须全小写. 比如: 使用'app/index/index', 而非'App'.
     */
    'router' => array(

        /*系统首页*/
        'home/index/index'                   =>  'home',
        /*用户中心*/
        'ucenter/index/index'                => 'ucenter/[uid]',
        'ucenter/index/following'            => 'ucenter/following_[uid]',
        'ucenter/index/applist'              => 'ucenter/applist_[type]/[uid]',
        'ucenter/index/information'          => 'ucenter/information_[uid]',
        'ucenter/index/fans'                 => 'ucenter/fans_[uid]',
        'ucenter/index/rank'                 => 'ucenter/rank_[uid]',
        'ucenter/index/rankverifywait'       => 'ucenter/rankwait_[uid]',
        'ucenter/index/rankverifyfailure'    => 'ucenter/rankfailure_[uid]',
        'ucenter/index/rankverify'           => 'ucenter/rankverify_[uid]',
        'ucenter/config/index'               => 'ucenter/conf',
        'ucenter/config/tag'                 => 'ucenter/tag',
        'ucenter/config/avatar'              => 'ucenter/avatar',
        'ucenter/config/password'            => 'ucenter/password',
        'ucenter/config/score'               => 'ucenter/score',
        'ucenter/config/role'                => 'ucenter/role',
        'ucenter/config/other'               => 'ucenter/other',
        'ucenter/message/session'            => 'ucenter/session',
        'ucenter/message/message'            => 'ucenter/msg_[tab]',
        'ucenter/collection/index'           => 'ucenter/collection_[type]',
        'ucenter/invite/invite'              => 'ucenter/invite',
        'ucenter/invite/index'               => 'ucenter/invite_create',
        /*会员*/
        'people/index/index'                    => 'people',

        /*注册登录*/
        'ucenter/member/login'                  => 'login',
        'ucenter/member/step'                   => 'register/step_[step]',
        'ucenter/member/register'               => 'register/[type]/c_[code]',

        /*文章*/
        'articles/index/index'                     => 'articles',
        'articles/index/category'                  => 'articles/category_[id]',
        'articles/index/detail'                    => 'articles/detail_[id]',
        /*about*/
        'about/index/index'                     => 'about_[id]',
    ),

);