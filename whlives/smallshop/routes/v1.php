<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('index', 'IndexController@index');

//基础公共模块
Route::prefix('helper')->group(function () {
    Route::get('adv/{code}', 'HelperController@adv')->where('code', '[0-9]+');//广告位
    Route::get('express_company', 'HelperController@expressCompany');//快递公司
    Route::get('area/{parent_id?}', 'HelperController@area')->where('parent_id', '[0-9]+');//地区
    Route::get('wx_jssdk', 'HelperController@wxJssdk');//获取微信jssdk信息
});

//文章
Route::prefix('article')->group(function () {
    Route::get('/{category_id}/{page?}/{limit?}', 'ArticleController@index')->where('category_id', '[0-9]+');
    Route::get('detail/{id}', 'ArticleController@detail');
    //分类
    Route::get('category/{parent_id?}', 'ArticleCategoryController@index')->where('parent_id', '[0-9]+');
});

//商品
Route::prefix('goods')->group(function () {
    Route::get('search', 'GoodsController@search');
    Route::get('detail/{id}/{type?}', 'GoodsController@detail');
    Route::get('category/{parent_id?}', 'CategoryController@index')->where('parent_id', '[0-9]+');
    Route::get('evaluation/{id}/{page?}/{limit?}', 'GoodsController@evaluation');
});

//品牌
Route::prefix('brand')->group(function () {
    Route::get('/{page?}/{limit?}', 'BrandController@index');
    Route::get('detail/{id}', 'BrandController@detail');
});

//支付相关
Route::prefix('pay')->group(function () {
    Route::post('notify/{payment_id}/{platform?}', 'PayController@notify')->where('payment_id', '[0-9]+');//支付第三方回调
});

//站外推送
Route::prefix('outpush')->group(function () {
    Route::post('kdniao', 'OutPushController@kdniao');//快递鸟推送
});

//需要验证签名的
Route::group(['middleware' => \App\Http\Middleware\SignCheck::class], function () {
    Route::prefix('helper')->group(function () {
        Route::post('captcha', 'HelperController@captcha');//发送验证码
        Route::post('aliyun_token', 'HelperController@aliyunToken');//获取阿里云web上传参数
        Route::post('aliyun_sts', 'HelperController@aliyunSts');//获取阿里云客户端上传参数
    });

    //登陆注册
    Route::prefix('login')->group(function () {
        Route::post('/', 'LoginController@index');//密码登陆
        Route::post('auth', 'LoginController@auth');//第三方登陆
        Route::post('mini_program', 'LoginController@miniProgram');//小程序登陆
        Route::post('mini_program_bind_mobile', 'LoginController@miniProgramBindMobile');//小程序绑定手机
        Route::post('wechat', 'LoginController@wechat');//微信公众号、开放平台登陆
        Route::post('out', 'LoginController@loginOut');//退出登录
        //需要验证码的
        Route::group(['middleware' => \App\Http\Middleware\CaptchaCheck::class], function () {
            Route::post('speed', 'LoginController@speed');//验证码登陆
            Route::post('bind_mobile', 'LoginController@bindMobile');//第三方登陆绑定手机
            Route::post('find_password', 'LoginController@findPassword');//找回密码
        });
    });

    //需要验证token的
    Route::group(['middleware' => \App\Http\Middleware\TokenCheck::class], function () {
        //购物车
        Route::prefix('cart')->group(function () {
            Route::post('/', 'CartController@index');
            Route::post('add', 'CartController@add');
            Route::post('edit', 'CartController@edit');
            Route::post('delete', 'CartController@delete');
            Route::post('clear', 'CartController@clear');
        });

        //订单
        Route::prefix('order')->group(function () {
            Route::post('get_price', 'OrderController@getPrice');
            Route::post('confirm', 'OrderController@confirm');
            Route::post('submit', 'OrderController@submit');
        });

        //支付信息
        Route::prefix('pay')->group(function () {
            Route::post('payment', 'PayController@payment');
            Route::post('pay_data', 'PayController@payData');
            Route::post('trade_status', 'PayController@tradeStatus');
        });

        //会员中心
        Route::group(['prefix' => 'member', 'namespace' => 'Member'], function () {
            //我的
            Route::post('index', 'IndexController@index');
            Route::post('info', 'IndexController@info');
            Route::post('save_info', 'IndexController@saveInfo');
            Route::post('up_password', 'IndexController@upPassword');
            Route::post('set_pay_password', 'IndexController@setPayPassword');
            Route::post('up_pay_password', 'IndexController@upPayPassword');
            Route::post('remove_auth_bind', 'IndexController@removeAuthBind'); //第三方登录解除绑定

            //订单
            Route::prefix('order')->group(function () {
                Route::post('/', 'OrderController@index');
                Route::post('detail', 'OrderController@detail');
                Route::post('cancel', 'OrderController@cancel');
                Route::post('confirm', 'OrderController@confirm');
                Route::post('delivery', 'OrderController@delivery');
                Route::post('evaluation', 'OrderController@evaluation');
                Route::post('evaluation_put', 'OrderController@evaluationPut');
            });
            //售后
            Route::prefix('refund')->group(function () {
                Route::post('/', 'RefundController@index');
                Route::post('detail', 'RefundController@detail');
                Route::post('apply', 'RefundController@apply');
                Route::post('apply_put', 'RefundController@applyPut');
                Route::post('delivery', 'RefundController@delivery');
                Route::post('log', 'RefundController@log');
                Route::post('cancel', 'RefundController@cancel');
            });
            //地址
            Route::prefix('address')->group(function () {
                Route::post('/', 'AddressController@index');
                Route::post('add', 'AddressController@add');
                Route::post('detail', 'AddressController@detail');
                Route::post('edit', 'AddressController@edit');
                Route::post('delete', 'AddressController@delete');
                Route::post('default', 'AddressController@default');
            });
            //收藏
            Route::prefix('favorite')->group(function () {
                Route::post('goods', 'FavoriteController@goods');
                Route::post('seller', 'FavoriteController@seller');
                Route::post('article', 'FavoriteController@article');
                Route::post('set', 'FavoriteController@set');
            });
            //余额
            Route::prefix('balance')->group(function () {
                Route::post('detail_list', 'BalanceController@detailList');
                Route::post('detail', 'BalanceController@detail');
                Route::post('recharge', 'BalanceController@recharge');
                Route::post('withdraw', 'BalanceController@withdraw');
                Route::post('withdraw_list', 'BalanceController@withdrawList');
            });
            //积分
            Route::prefix('point')->group(function () {
                Route::post('detail_list', 'PointController@detailList');
                Route::post('detail', 'PointController@detail');
            });
            //优惠券
            Route::prefix('coupons')->group(function () {
                Route::post('obtain', 'CouponsController@obtain');
                Route::post('is_use', 'CouponsController@isUse');
                Route::post('normal', 'CouponsController@normal');
                Route::post('overdue', 'CouponsController@overdue');
            });
            //评价
            Route::prefix('evaluation')->group(function () {
                Route::post('/', 'EvaluationController@index');
            });
        });
    });
});
