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
Route::group(['middleware' => \App\Http\Middleware\AdminToken::class], function () {
    Route::post('loginout', 'LoginController@loginOut');
    Route::post('main', 'IndexController@main');
    Route::post('left_menu', 'IndexController@leftMenu');
    Route::prefix('helper')->group(function () {
        Route::post('aliyun_token', 'HelperController@aliyunToken');//阿里云oss信息
        Route::post('admin_routes', 'HelperController@getAdminRoutes');//获取路由
        Route::post('area', 'HelperController@area');//获取地区
    });

    /**
     ***************管理员模块*******************
     */
    Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
        Route::prefix('admin')->group(function () {
            Route::post('/', 'AdminController@index');
            Route::post('detail', 'AdminController@detail');
            Route::post('save', 'AdminController@save');
            Route::post('status', 'AdminController@status');
            Route::post('delete', 'AdminController@delete');
            Route::post('info', 'AdminController@info');
            Route::post('info_update', 'AdminController@infoUpdate');
        });
        //角色
        Route::prefix('role')->group(function () {
            Route::post('/', 'RoleController@index');
            Route::post('detail', 'RoleController@detail');
            Route::post('save', 'RoleController@save');
            Route::post('status', 'RoleController@status');
            Route::post('delete', 'RoleController@delete');
            Route::post('select', 'RoleController@select');
        });
        //权限码
        Route::prefix('right')->group(function () {
            Route::post('/', 'RightController@index');
            Route::post('detail', 'RightController@detail');
            Route::post('save', 'RightController@save');
            Route::post('status', 'RightController@status');
            Route::post('delete', 'RightController@delete');
            Route::post('rights', 'RightController@rights');
        });
    });

    /**
     ***************设置模块*******************
     */
    Route::group(['prefix' => 'system', 'namespace' => 'System'], function () {
        //基本设置
        Route::prefix('config')->group(function () {
            Route::post('', 'ConfigController@index');
            Route::post('update', 'ConfigController@update');
            Route::post('detail', 'ConfigController@detail');
            Route::post('save', 'ConfigController@save');
        });

        //菜单
        Route::prefix('menu')->group(function () {
            Route::post('/', 'MenuController@index');
            Route::post('detail', 'MenuController@detail');
            Route::post('save', 'MenuController@save');
            Route::post('delete', 'MenuController@delete');
            Route::post('status', 'MenuController@status');
            Route::post('select_all', 'MenuController@selectAll');
            Route::post('select', 'MenuController@select');
        });

        //快递公司
        Route::prefix('express_company')->group(function () {
            Route::post('', 'ExpressCompanyController@index');
            Route::post('detail', 'ExpressCompanyController@detail');
            Route::post('save', 'ExpressCompanyController@save');
            Route::post('delete', 'ExpressCompanyController@delete');
            Route::post('status', 'ExpressCompanyController@status');
            Route::post('field_update', 'ExpressCompanyController@fieldUpdate');
            Route::post('select', 'ExpressCompanyController@select');
            Route::post('pay_type', 'ExpressCompanyController@payType');
        });

        //支付方式
        Route::prefix('payment')->group(function () {
            Route::post('', 'PaymentController@index');
            Route::post('detail', 'PaymentController@detail');
            Route::post('save', 'PaymentController@save');
            Route::post('delete', 'PaymentController@delete');
            Route::post('status', 'PaymentController@status');
            Route::post('field_update', 'PaymentController@fieldUpdate');
            Route::post('client_type', 'PaymentController@getClientType');
        });

        //品牌
        Route::prefix('brand')->group(function () {
            Route::post('', 'BrandController@index');
            Route::post('detail', 'BrandController@detail');
            Route::post('save', 'BrandController@save');
            Route::post('delete', 'BrandController@delete');
            Route::post('status', 'BrandController@status');
            Route::post('field_update', 'BrandController@fieldUpdate');
            Route::post('select', 'BrandController@select');
        });

        //商家后台菜单
        Route::prefix('menu_seller')->group(function () {
            Route::post('/', 'MenuSellerController@index');
            Route::post('detail', 'MenuSellerController@detail');
            Route::post('save', 'MenuSellerController@save');
            Route::post('delete', 'MenuSellerController@delete');
            Route::post('status', 'MenuSellerController@status');
        });
    });

    /**
     ***************工具模块*******************
     */
    Route::group(['prefix' => 'tool', 'namespace' => 'Tool'], function () {
        //文章分类
        Route::prefix('category')->group(function () {
            Route::post('/', 'CategoryController@index');
            Route::post('detail', 'CategoryController@detail');
            Route::post('save', 'CategoryController@save');
            Route::post('delete', 'CategoryController@delete');
            Route::post('status', 'CategoryController@status');
            Route::post('field_update', 'CategoryController@fieldUpdate');
            Route::post('select_all', 'CategoryController@selectAll');
        });

        //文章
        Route::prefix('article')->group(function () {
            Route::post('/', 'ArticleController@index');
            Route::post('detail', 'ArticleController@detail');
            Route::post('save', 'ArticleController@save');
            Route::post('delete', 'ArticleController@delete');
            Route::post('status', 'ArticleController@status');
            Route::post('field_update', 'ArticleController@fieldUpdate');
        });

        //广告位
        Route::prefix('adv_group')->group(function () {
            Route::post('/', 'AdvGroupController@index');
            Route::post('detail', 'AdvGroupController@detail');
            Route::post('save', 'AdvGroupController@save');
            Route::post('delete', 'AdvGroupController@delete');
            Route::post('status', 'AdvGroupController@status');
            Route::post('select', 'AdvGroupController@select');
        });

        //广告
        Route::prefix('adv')->group(function () {
            Route::post('/', 'AdvController@index');
            Route::post('detail', 'AdvController@detail');
            Route::post('save', 'AdvController@save');
            Route::post('delete', 'AdvController@delete');
            Route::post('status', 'AdvController@status');
            Route::post('field_update', 'AdvController@fieldUpdate');
            Route::post('target_type', 'AdvController@targetType');
        });
    });

    /**
     ***************会员模块*******************
     */
    Route::group(['prefix' => 'member', 'namespace' => 'Member'], function () {
        //会员组
        Route::prefix('group')->group(function () {
            Route::post('/', 'GroupController@index');
            Route::post('detail', 'GroupController@detail');
            Route::post('save', 'GroupController@save');
            Route::post('delete', 'GroupController@delete');
            Route::post('status', 'GroupController@status');
            Route::post('select', 'GroupController@select');
        });

        //会员
        Route::prefix('member')->group(function () {
            Route::post('/', 'MemberController@index');
            Route::post('detail', 'MemberController@detail');
            Route::post('save', 'MemberController@save');
            Route::post('delete', 'MemberController@delete');
            Route::post('status', 'MemberController@status');
        });

    });

    /**
     ***************商家模块*******************
     */
    Route::group(['prefix' => 'seller', 'namespace' => 'Seller'], function () {
        //商家
        Route::prefix('seller')->group(function () {
            Route::post('/', 'SellerController@index');
            Route::post('detail', 'SellerController@detail');
            Route::post('save', 'SellerController@save');
            Route::post('delete', 'SellerController@delete');
            Route::post('status', 'SellerController@status');
            Route::post('select', 'SellerController@select');
        });
        //商家地址
        Route::prefix('address')->group(function () {
            Route::post('/', 'AddressController@index');
            Route::post('detail', 'AddressController@detail');
            Route::post('save', 'AddressController@save');
            Route::post('delete', 'AddressController@delete');
            Route::post('select', 'AddressController@select');
        });
    });

    /**
     ***************商品模块*******************
     */
    Route::group(['prefix' => 'goods', 'namespace' => 'Goods'], function () {
        //分类
        Route::prefix('category')->group(function () {
            Route::post('/', 'CategoryController@index');
            Route::post('detail', 'CategoryController@detail');
            Route::post('save', 'CategoryController@save');
            Route::post('delete', 'CategoryController@delete');
            Route::post('status', 'CategoryController@status');
            Route::post('select_all', 'CategoryController@selectAll');
            Route::post('select', 'CategoryController@select');
        });

        //规格
        Route::prefix('spec')->group(function () {
            Route::post('/', 'SpecController@index');
            Route::post('detail', 'SpecController@detail');
            Route::post('save', 'SpecController@save');
            Route::post('delete', 'SpecController@delete');
            Route::post('status', 'SpecController@status');
            Route::post('field_update', 'SpecController@fieldUpdate');
            Route::post('select', 'SpecController@select');
        });

        //规格值
        Route::prefix('spec_value')->group(function () {
            Route::post('/', 'SpecValueController@index');
            Route::post('detail', 'SpecValueController@detail');
            Route::post('save', 'SpecValueController@save');
            Route::post('delete', 'SpecValueController@delete');
            Route::post('status', 'SpecValueController@status');
            Route::post('field_update', 'SpecValueController@fieldUpdate');
        });

        //属性
        Route::prefix('attribute')->group(function () {
            Route::post('/', 'AttributeController@index');
            Route::post('detail', 'AttributeController@detail');
            Route::post('save', 'AttributeController@save');
            Route::post('delete', 'AttributeController@delete');
            Route::post('status', 'AttributeController@status');
            Route::post('field_update', 'AttributeController@fieldUpdate');
            Route::post('select', 'AttributeController@select');
        });

        //属性值
        Route::prefix('attribute_value')->group(function () {
            Route::post('/', 'AttributeValueController@index');
            Route::post('detail', 'AttributeValueController@detail');
            Route::post('save', 'AttributeValueController@save');
            Route::post('delete', 'AttributeValueController@delete');
            Route::post('status', 'AttributeValueController@status');
            Route::post('field_update', 'AttributeValueController@fieldUpdate');
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
            Route::post('shelves_status', 'GoodsController@shelvesStatus');
            Route::post('rem', 'GoodsController@rem');
            Route::post('field_update', 'GoodsController@fieldUpdate');
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
            Route::post('pay', 'OrderController@pay');//后台支付
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
        //资金
        Route::prefix('balance')->group(function () {
            Route::post('', 'BalanceController@index');
            Route::post('batch_recharge', 'BalanceController@batchRecharge');
            Route::post('recharge', 'BalanceController@recharge');
            Route::post('deduct', 'BalanceController@deduct');
        });
        //资金明细
        Route::prefix('balance_detail')->group(function () {
            Route::post('', 'BalanceDetailController@index');
        });
        //用户提现
        Route::prefix('withdraw')->group(function () {
            Route::post('', 'WithdrawController@index');
            Route::post('get_status', 'WithdrawController@getStatus');
            Route::post('agreed', 'WithdrawController@agreed');
            Route::post('refused_money', 'WithdrawController@refusedMoney');
            Route::post('refused_no_money', 'WithdrawController@refusedNoMoney');
        });
        //积分
        Route::prefix('point')->group(function () {
            Route::post('', 'PointController@index');
            Route::post('batch_recharge', 'PointController@batchRecharge');
            Route::post('recharge', 'PointController@recharge');
            Route::post('deduct', 'PointController@deduct');
        });
        //积分明细
        Route::prefix('point_detail')->group(function () {
            Route::post('', 'PointDetailController@index');
        });
        //交易单
        Route::prefix('trade')->group(function () {
            Route::post('', 'TradeController@index');
            Route::post('get_type', 'TradeController@getType');
        });
        //商家提现
        Route::prefix('seller_withdraw')->group(function () {
            Route::post('', 'SellerWithdrawController@index');
            Route::post('get_status', 'SellerWithdrawController@getStatus');
            Route::post('agreed', 'SellerWithdrawController@agreed');
            Route::post('refused_money', 'SellerWithdrawController@refusedMoney');
            Route::post('refused_no_money', 'SellerWithdrawController@refusedNoMoney');
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
            Route::post('delete', 'CouponsDetailController@delete');
            Route::post('status', 'CouponsDetailController@status');
            Route::post('bind', 'CouponsDetailController@bind');
        });
    });
});
