<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
Route::rules([
    // app下载页
    'download'=>'home/index/download',        //商品分类
    //PC版优化
    'index'=>'home/index/index',        //商品分类
    'category-:cat'=>'home/goods/lists',        //商品分类
    'search'=>'home/goods/search',               //商品搜索
    'goods-:goodsId'=>'home/goods/detail',           //商品
    'brands'=>'home/brands/index',               //品牌
    'street'=>'home/shops/shopstreet',           //品牌
    'service-:id'=>'home/helpcenter/view',      //帮助中心
    'service'=>'home/helpcenter/view',           //帮助中心
    'news-:id'=>'home/news/view',               //新闻
    'news'=>'home/news/view',                    //新闻
    'cats-news-:catId'=>'home/news/nlist',      //新闻
    'cats-news'=>'home/news/nlist',               //新闻
    'login'=>'home/users/login',                 //用户登录页
    'register'=>'home/users/regist',             //用户登录页
    'forget'=>'home/users/forgetpass',           //找回密码
    'find-forget'=>'home/users/forgetpasst',    //找回密码
    'success-forget'=>'home/users/forgetpassf',  //找回密码
    'reset-forget'=>'home/users/resetpass',       //找回密码
    'shop-:shopId'=>'home/shops/index',           //店铺主页
    'shopgoods-:shopId'=>'home/shops/goods',           //店铺商品页
    'joinstep-:id'=>'home/shops/joinstepnext',
    'join'=>'home/shops/join'
]);

