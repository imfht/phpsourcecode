<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-8-27
 * Time: 下午4:54
 * @author 想天小郑<zzl@ourstu.com>
 */
return array(

    /**
     * 路由的key必须写全称,且必须全小写. 比如: 使用'wap/index/index', 而非'wap'.
     */
    'router' => array(

        /*商城配置*/
        'shop/index/index'          =>  'shop',
        'shop/index/goods'			=>  'goods/[category_id]',
        'shop/index/goodsdetail'    =>  'goods/detail_[id]',
        'shop/index/mygoods'        =>  'mygoods/[status]',

        /*活动*/
        'event/index/index'         => 'event/[type_id]/p_[page]',
        'event/index/myevent'       => 'myevent/[type_id]',
        'event/index/detail'        => 'event/detail_[id]',
        'event/index/member'        => 'event/member_[id]',
        'event/index/edit'          => 'event/edit_[id]',
        'event/index/add'           => 'event/add',

        /*专辑*/
        'issue/index/index'                     => 'issue/[issue_id]/p_[page]',
        'issue/index/issuecontentdetail'        => 'issue/detail_[id]',
        'issue/index/edit'                      => 'issue/edit_[id]',

        /*讨论区*/
        'forum/index/index'                     => 'forum',
        'forum/index/forum'                     => 'forum/forum_[id]/p_[page]',
        'forum/index/edit'                      => 'forum/edit_[forum_id]/p_[post_id]',
        'forum/index/detail'                    => 'forum/detail_[id]',
        'forum/index/search'                    => 'forum/search',

        /*资讯*/
        'blog/index/index'                      => 'blog/p_[page]',
        'blog/article/lists'                    => 'blog/list_[category]',
        'blog/article/detail'                   => 'blog/detail_[id]',

        /*微博*/
        'weibo/index/index'                     => 'weibo/p_[page]',
        'weibo/index/weibodetail'               => 'weibo/detail_[id]',
        'weibo/index/myconcerned'               => 'weibo/concerned',
        'weibo/index/search'                    => 'weibo/search',
        'weibo/topic/index'                     => 'weibo/topic',

        /*用户中心*/
        'usercenter/index/index'                => 'ucenter/[uid]',
        'usercenter/index/following'            => 'ucenter/following_[uid]',
        'usercenter/index/applist'              => 'ucenter/applist_[type]/[uid]',
        'usercenter/index/information'          => 'ucenter/information_[uid]',
        'usercenter/index/fans'                 => 'ucenter/fans_[uid]',
        'usercenter/index/rank'                 => 'ucenter/rank_[uid]',
        'usercenter/index/rankverifywait'       => 'ucenter/rankwait_[uid]',
        'usercenter/index/rankverifyfailure'    => 'ucenter/rankfailure_[uid]',
        'usercenter/index/rankverify'           => 'ucenter/rankverify_[uid]',
        'usercenter/config/index'               => 'ucenter/conf',
        'usercenter/message/session'            => 'ucenter/session',
        'usercenter/message/message'            => 'ucenter/msg',
        'usercenter/message/collection'         => 'ucenter/collection',
        'usercenter/recharge/recharge'          => 'ucenter/recharge',
        'usercenter/recharge/lists'             => 'ucenter/rechargelist',
        'usercenter/recharge/withdraw'          => 'ucenter/withdraw',
        'usercenter/recharge/wlists'            => 'ucenter/wlists',

        /*会员*/
        'people/index/find'                     => 'people/find',
        'people/index/index'                    => 'people',

        /*注册登录*/
        'home/user/register'                    => 'register',
        'home/user/step2'                       => 'register/step2',
        'home/user/step3'                       => 'register/step3',
        'home/user/login'                       => 'login',
        'home/index/index'                       => '',

        /*群组*/
        'group/index/index'                     => 'group/p_[page]',
        'group/index/groups'                    => 'groups/[cate]/p_[page]',
        'group/index/mygroup'                   => 'mygroup/p_[page]',
        'group/index/group'                     => 'onegroup/[id]/[type]/[cate]',
        'group/index/detail'                    => 'group/detail_[id]',
        'group/index/edit'                      => 'group/edit_[group_id]/[post_id]',
        'group/index/create'                    => 'group/create',
        'group/manage/index'                    => 'group/manage_[group_id]',
        'group/manage/member'                   => 'group/member_[group_id]/[status]',
        'group/manage/notice'                   => 'group/notice_[group_id]',
        'group/manage/category'                 => 'group/category_[group_id]',

        /*微店*/
        'store/index/index'                     => 'store',
        'store/index/li'                        => 'store/li_[type]_[name]',
        'store/index/search'                    => 'store/search',
        'store/index/info'                      => 'store/info_[info_id]',
        'store/shop/lists'                      => 'stores/[page]',
        'store/shop/detail'                     => 'onestore/[id]',
        'store/shop/goods'                      => 'onestore/goods_[id]',
        'store/center/detail'                   => 'userstore/detail',
        'store/center/buy'                      => 'userstore/buy',
        'store/center/pay'                      => 'userstore/pay',
        'store/center/orders'                   => 'userstore/orders',
        'store/center/payorder'                 => 'userstore/payorder_[id]',
        'store/center/response'                 => 'userstore/response_[s]',
        'store/center/fav'                      => 'userstore/fav_[id]',
        'store/center/createshop'               => 'userstore/create',
        'store/center/post'                     => 'userstore/post_[entity_id]',
        'store/center/selling'                  => 'userstore/selling_[page]',
        'store/center/sold'                     => 'userstore/sold',

        /*分类信息*/
        'cat/index/index'                       => 'cat',
        'cat/index/li'                          => 'cat/li_[name]',
        'cat/index/info'                        => 'cat/info_[info_id]',
        'cat/index/post'                        => 'cat/post_[name]',
        'cat/center/my'                         => 'cat/my_[id]',
        'cat/center/fav'                        => 'cat/fav_[id]',
        'cat/center/rec'                        => 'cat/rec',
        'cat/center/send'                       => 'cat/send',
        'cat/center/post'                       => 'usercat/post',
    ),

);