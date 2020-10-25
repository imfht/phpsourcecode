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


Route::post('login', 'LoginController@index');
Route::post('helper/captcha', 'HelperController@captcha');//验证码

//验证token
Route::group(['middleware' => \App\Http\Middleware\SellerToken::class], function () {
    Route::post('loginout', 'LoginController@loginOut');
    Route::post('main', 'IndexController@main');
    Route::post('left_menu', 'IndexController@leftMenu');
    Route::prefix('helper')->group(function () {
        Route::post('aliyun_token', 'HelperController@aliyunToken');//阿里云oss信息
        Route::post('area', 'HelperController@area');//获取地区
    });

    /**
     ***************设置模块*******************
     */
    Route::group(['prefix' => 'system', 'namespace' => 'System'], function () {
        //品牌
        Route::prefix('brand')->group(function () {
            Route::post('select', 'BrandController@select');
        });

        //快递公司
        Route::prefix('express_company')->group(function () {
            Route::post('select', 'ExpressCompanyController@select');
        });
    });

    /**
     ***************会员模块*******************
     */
    Route::group(['prefix' => 'member', 'namespace' => 'Member'], function () {
        //会员组
        Route::prefix('group')->group(function () {
            Route::post('select', 'GroupController@select');
        });

    });

    /**
     ***************商家模块*******************
     */
    Route::group(['prefix' => 'seller', 'namespace' => 'Seller'], function () {
        //商家
        Route::prefix('seller')->group(function () {
            Route::post('info', 'SellerController@info');
            Route::post('info_update', 'SellerController@infoUpdate');
        });
        //商家地址
        Route::prefix('address')->group(function () {
            Route::post('/', 'AddressController@index');
            Route::post('detail', 'AddressController@detail');
            Route::post('save', 'AddressController@save');
            Route::post('delete', 'AddressController@delete');
            Route::post('select', 'AddressController@select');
        });

        //分类
        Route::prefix('category')->group(function () {
            Route::post('/', 'CategoryController@index');
            Route::post('detail', 'CategoryController@detail');
            Route::post('save', 'CategoryController@save');
            Route::post('delete', 'CategoryController@delete');
            Route::post('status', 'CategoryController@status');
            Route::post('select_all', 'CategoryController@selectAll');
            Route::post('select', 'CategoryController@select');
            Route::post('select_multi', 'CategoryController@selectMulti');
        });
    });

    /**
     ***************商品模块*******************
     */
    Route::group(['prefix' => 'goods', 'namespace' => 'Goods'], function () {
        //分类
        Route::prefix('category')->group(function () {
            Route::post('select_all', 'CategoryController@selectAll');
            Route::post('select', 'CategoryController@select');
        });

        //配送方式
        Route::prefix('delivery')->group(function () {
            Route::post('/', 'DeliveryController@index');
            Route::post('detail', 'DeliveryController@detail');
            Route::post('save', 'DeliveryController@save');
            Route::post('delete', 'DeliveryController@delete');
            Route::post('status', 'DeliveryController@status');
            Route::post('field_update', 'DeliveryController@fieldUpdate');
            Route::post('select', 'DeliveryController@select');
        });

        //商品
        Route::prefix('goods')->group(function () {
            Route::post('/', 'GoodsController@index');
            Route::post('detail', 'GoodsController@detail');
            Route::post('save', 'GoodsController@save');
            Route::post('delete', 'GoodsController@delete');
            Route::post('status', 'GoodsController@status');
            Route::post('get_attribute', 'GoodsController@getAttribute');
            Route::post('get_spec', 'GoodsController@getSpec');
        });

    });

    /**
     ***************订单模块*******************
     */
    Route::group(['prefix' => 'order', 'namespace' => 'Order'], function () {
        //订单
        Route::prefix('order')->group(function () {
            Route::post('/', 'OrderController@index');
            Route::post('detail', 'OrderController@detail');
            Route::post('get_status', 'OrderController@getStatus');
            Route::post('get_price', 'OrderController@getPrice');//获取价格
            Route::post('update_price', 'OrderController@updatePrice');//修改价格
            Route::post('get_address', 'OrderController@getAddress');
            Route::post('update_address', 'OrderController@updateAddress');//修改地址
            Route::post('cancel', 'OrderController@cancel');//后台取消
            Route::post('delivery', 'OrderController@delivery');//发货
            Route::post('get_delivery', 'OrderController@getDelivery');//获取发货信息
            Route::post('get_log', 'OrderController@getLog');//获取订单日志
            Route::post('batch_delivery_list', 'OrderController@batchDeliveryList');//批量发货
            Route::post('batch_delivery_submit', 'OrderController@batchDeliverySubmit');//批量发货提交
            Route::post('print_goods', 'OrderController@printGoods');//打印发货单
            Route::post('print_delivery', 'OrderController@PrintDelivery');//打印快递单
        });
        //售后
        Route::prefix('refund')->group(function () {
            Route::post('/', 'RefundController@index');
            Route::post('detail', 'RefundController@detail');
            Route::post('action_save', 'RefundController@actionSave');
        });
    });

    /**
     ***************财务模块*******************
     */
    Route::group(['prefix' => 'financial', 'namespace' => 'Financial'], function () {
        //资金明细
        Route::prefix('balance')->group(function () {
            Route::post('', 'BalanceController@index');
            Route::post('detail', 'BalanceController@detail');
            Route::post('save', 'BalanceController@save');
        });
    });

    /**
     ***************促销模块*******************
     */
    Route::group(['prefix' => 'market', 'namespace' => 'Market'], function () {
        //促销活动
        Route::prefix('promotion')->group(function () {
            Route::post('', 'PromotionController@index');
            Route::post('detail', 'PromotionController@detail');
            Route::post('save', 'PromotionController@save');
            Route::post('delete', 'PromotionController@delete');
            Route::post('status', 'PromotionController@status');
            Route::post('get_type', 'PromotionController@getType');
        });
        //优惠券活动
        Route::prefix('coupons')->group(function () {
            Route::post('', 'CouponsController@index');
            Route::post('detail', 'CouponsController@detail');
            Route::post('save', 'CouponsController@save');
            Route::post('delete', 'CouponsController@delete');
            Route::post('status', 'CouponsController@status');
            Route::post('select', 'CouponsController@select');
        });
        //优惠券明细
        Route::prefix('coupons_detail')->group(function () {
            Route::post('', 'CouponsDetailController@index');
            Route::post('generate', 'CouponsDetailController@generate');
            Route::post('bind', 'CouponsDetailController@bind');
        });
    });
});
