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
    'mbrands'=>'mobile/brands/index',            //自营店铺
    'mstreet'=>'mobile/shops/shopstreet',        //自营店铺
    'mlogin'=>'mobile/users/login',              //用户登录
    'mregister'=>'mobile/users/toregister',      //用户注册  
    'mforget'=>'mobile/users/forgetpass',        //找回密码     
    'mgoods-:goodsId'=>'mobile/goods/detail',
    'mshop-:shopId'=>'mobile/shops/index',
    'mshopview-:shopId'=>'mobile/shops/view',
    'mlist'=>'mobile/goods/lists',
    'msearch'=>'mobile/goods/search',
    'mnews'=>'mobile/news/view',
    'mcategoty'=>'mobile/goodscats/index',
    'mshopgoods-:shopId'=>'mobile/shops/goods',
	'mnewsdetail-:id'=>'mobile/news/getnews'
]);