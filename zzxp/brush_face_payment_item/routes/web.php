<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::post('testoss','TestossController@index');

Route::group(['middleware' => 'auth'], function() {
    Route::get('/', 'IndexController@index');
    Route::get('/welcome', 'IndexController@welcome');
    Route::get('/statistics', 'IndexController@getStatictis');
    Route::get('/pay', 'IndexController@getPay');
    Route::get('/member', 'IndexController@member');
});


Route::get('error', array('uses' => 'ErrorController@index'));
Route::get('mail', array('uses' => 'LoginController@sendMail'));


//绕开权限验证：
Route::group(['prefix'=>'admin','namespace' => 'Auth'], function()
{
    //所有人都有权限的缓存更新
    Route::get('system_role/updateCache', array('as'=>'system_role.get', 'uses' => 'SystemRoleController@updateCache'));
});


Route::group(['prefix'=>'admin','middleware'=>'auth','namespace' => 'Auth'], function()
{
    //
    Route::get('question/index', array('as'=>'question.get', 'before' => '', 'uses' => 'QuestionController@index'));
    Route::post('question/add', array('as'=>'question.add', 'before' => '', 'uses' => 'QuestionController@postAdd'));
    Route::get('question/edit', array('as'=>'question.edit', 'before' => '', 'uses' => 'QuestionController@getEdit'));
    Route::post('question/update', array('as'=>'question.update', 'before' => '', 'uses' => 'QuestionController@postUpdate'));


    Route::get('article/index', array('as'=>'article.get', 'before' => '', 'uses' => 'ArticleController@index'));
    Route::post('article/add', array('as'=>'article.add', 'before' => '', 'uses' => 'ArticleController@postAdd'));
    Route::get('article/edit', array('as'=>'article.edit', 'before' => '', 'uses' => 'ArticleController@getEdit'));
    Route::post('article/update', array('as'=>'article.update', 'before' => '', 'uses' => 'ArticleController@postUpdate'));
    
    Route::post('article/upload', array('as'=>'article.upload', 'before' => '', 'uses' => 'ArticleController@upload'));

    Route::post('article/del', array('as'=>'article.del', 'before' => '', 'uses' => 'ArticleController@postDel'));
    //
    Route::get('attachment/index', array('as'=>'attachment.get', 'before' => '', 'uses' => 'AttachmentController@index'));
    Route::post('attachment/add', array('as'=>'attachment.add', 'before' => '', 'uses' => 'AttachmentController@postAdd'));
    Route::get('attachment/edit', array('as'=>'attachment.edit', 'before' => '', 'uses' => 'AttachmentController@getEdit'));
    Route::post('attachment/update', array('as'=>'attachment.update', 'before' => '', 'uses' => 'AttachmentController@postUpdate'));
    Route::post('attachment/del', array('as'=>'attachment.del', 'before' => '', 'uses' => 'AttachmentController@postDel'));

        //银行配置表
    Route::get('bank/index', array('as'=>'bank.get', 'before' => '', 'uses' => 'BankController@index'));
    Route::post('bank/add', array('as'=>'bank.add', 'before' => '', 'uses' => 'BankController@postAdd'));
    Route::get('bank/edit', array('as'=>'bank.edit', 'before' => '', 'uses' => 'BankController@getEdit'));
    Route::post('bank/update', array('as'=>'bank.update', 'before' => '', 'uses' => 'BankController@postUpdate'));
    Route::post('bank/del', array('as'=>'bank.del', 'before' => '', 'uses' => 'BankController@postDel'));


        //提现配置表
    Route::get('config/index', array('as'=>'config.get', 'before' => '', 'uses' => 'ConfigController@index'));
    Route::post('config/add', array('as'=>'config.add', 'before' => '', 'uses' => 'ConfigController@postAdd'));
    Route::get('config/edit', array('as'=>'config.edit', 'before' => '', 'uses' => 'ConfigController@getEdit'));
    Route::post('config/update', array('as'=>'config.update', 'before' => '', 'uses' => 'ConfigController@postUpdate'));
    Route::post('config/del', array('as'=>'config.del', 'before' => '', 'uses' => 'ConfigController@postDel'));
    //商户注册表
    Route::get('business/index', array('as'=>'business.get', 'before' => '', 'uses' => 'BusinessController@index'));
    Route::post('business/add', array('as'=>'business.add', 'before' => '', 'uses' => 'BusinessController@postAdd'));
    Route::get('business/edit', array('as'=>'business.edit', 'before' => '', 'uses' => 'BusinessController@getEdit'));
    Route::post('business/update', array('as'=>'business.update', 'before' => '', 'uses' => 'BusinessController@postUpdate'));
    Route::post('business/del', array('as'=>'business.del', 'before' => '', 'uses' => 'BusinessController@postDel'));
    Route::get('business/pay-qrcode', array('as'=>'business.pay-qrcode', 'before' => '', 'uses' => 'BusinessController@payQrcode'));
    Route::get('business/get-file', array('as'=>'business.get-file', 'before' => '', 'uses' => 'BusinessController@getFile'));

    //商户机器
    Route::get('business-machine/index', array('as'=>'business-machine.get', 'before' => '', 'uses' => 'BusinessMachineController@index'));
    Route::post('business-machine/add', array('as'=>'business-machine.add', 'before' => '', 'uses' => 'BusinessMachineController@postAdd'));
    Route::get('business-machine/edit', array('as'=>'business-machine.edit', 'before' => '', 'uses' => 'BusinessMachineController@getEdit'));
    Route::post('business-machine/update', array('as'=>'business-machine.update', 'before' => '', 'uses' => 'BusinessMachineController@postUpdate'));
    Route::post('business-machine/del', array('as'=>'business-machine.del', 'before' => '', 'uses' => 'BusinessMachineController@postDel'));


    //行业配置
    Route::get('business-type/index', array('as'=>'business-type.get', 'before' => '', 'uses' => 'BusinessTypeController@index'));
    Route::post('business-type/add', array('as'=>'business-type.add', 'before' => '', 'uses' => 'BusinessTypeController@postAdd'));
    Route::get('business-type/edit', array('as'=>'business-type.edit', 'before' => '', 'uses' => 'BusinessTypeController@getEdit'));
    Route::post('business-type/update', array('as'=>'business-type.update', 'before' => '', 'uses' => 'BusinessTypeController@postUpdate'));
    Route::post('business-type/del', array('as'=>'business-type.del', 'before' => '', 'uses' => 'BusinessTypeController@postDel'));
    //行政区
    Route::get('district/index', array('as'=>'district.get', 'before' => '', 'uses' => 'DistrictController@index'));
    Route::post('district/add', array('as'=>'district.add', 'before' => '', 'uses' => 'DistrictController@postAdd'));
    Route::get('district/edit', array('as'=>'district.edit', 'before' => '', 'uses' => 'DistrictController@getEdit'));
    Route::post('district/update', array('as'=>'district.update', 'before' => '', 'uses' => 'DistrictController@postUpdate'));
    Route::post('district/del', array('as'=>'district.del', 'before' => '', 'uses' => 'DistrictController@postDel'));
    //物料表
    Route::get('goods/index', array('as'=>'goods.get', 'before' => '', 'uses' => 'GoodsController@index'));
    Route::post('goods/add', array('as'=>'goods.add', 'before' => '', 'uses' => 'GoodsController@postAdd'));
    Route::get('goods/edit', array('as'=>'goods.edit', 'before' => '', 'uses' => 'GoodsController@getEdit'));
    Route::post('goods/update', array('as'=>'goods.update', 'before' => '', 'uses' => 'GoodsController@postUpdate'));
    Route::post('goods/del', array('as'=>'goods.del', 'before' => '', 'uses' => 'GoodsController@postDel'));
    //购物车
    Route::get('goods-car/index', array('as'=>'goods-car.get', 'before' => '', 'uses' => 'GoodsCarController@index'));
    Route::post('goods-car/add', array('as'=>'goods-car.add', 'before' => '', 'uses' => 'GoodsCarController@postAdd'));
    Route::get('goods-car/edit', array('as'=>'goods-car.edit', 'before' => '', 'uses' => 'GoodsCarController@getEdit'));
    Route::post('goods-car/update', array('as'=>'goods-car.update', 'before' => '', 'uses' => 'GoodsCarController@postUpdate'));
    Route::post('goods-car/del', array('as'=>'goods-car.del', 'before' => '', 'uses' => 'GoodsCarController@postDel'));
    //推荐奖励明细
    Route::get('introduce-money/index', array('as'=>'introduce-money.get', 'before' => '', 'uses' => 'IntroduceMoneyController@index'));
    Route::post('introduce-money/add', array('as'=>'introduce-money.add', 'before' => '', 'uses' => 'IntroduceMoneyController@postAdd'));
    Route::get('introduce-money/edit', array('as'=>'introduce-money.edit', 'before' => '', 'uses' => 'IntroduceMoneyController@getEdit'));
    Route::post('introduce-money/update', array('as'=>'introduce-money.update', 'before' => '', 'uses' => 'IntroduceMoneyController@postUpdate'));
    Route::post('introduce-money/del', array('as'=>'introduce-money.del', 'before' => '', 'uses' => 'IntroduceMoneyController@postDel'));
    //级别
    Route::get('level-name/index', array('as'=>'level-name.get', 'before' => '', 'uses' => 'LevelNameController@index'));
    Route::post('level-name/add', array('as'=>'level-name.add', 'before' => '', 'uses' => 'LevelNameController@postAdd'));
    Route::get('level-name/edit', array('as'=>'level-name.edit', 'before' => '', 'uses' => 'LevelNameController@getEdit'));
    Route::post('level-name/update', array('as'=>'level-name.update', 'before' => '', 'uses' => 'LevelNameController@postUpdate'));
    Route::post('level-name/del', array('as'=>'level-name.del', 'before' => '', 'uses' => 'LevelNameController@postDel'));
    //会员表
    Route::get('member/index', array('as'=>'member.get', 'before' => '', 'uses' => 'MemberController@index'));
    Route::post('member/add', array('as'=>'member.add', 'before' => '', 'uses' => 'MemberController@postAdd'));
    Route::get('member/edit', array('as'=>'member.edit', 'before' => '', 'uses' => 'MemberController@getEdit'));
    Route::post('member/update', array('as'=>'member.update', 'before' => '', 'uses' => 'MemberController@postUpdate'));
    Route::post('member/del', array('as'=>'member.del', 'before' => '', 'uses' => 'MemberController@postDel'));
    //订单表
    Route::get('member-orders/index', array('as'=>'member-orders.get', 'before' => '', 'uses' => 'MemberOrdersController@index'));
    Route::post('member-orders/add', array('as'=>'member-orders.add', 'before' => '', 'uses' => 'MemberOrdersController@postAdd'));
    Route::get('member-orders/edit', array('as'=>'member-orders.edit', 'before' => '', 'uses' => 'MemberOrdersController@getEdit'));
    Route::post('member-orders/update', array('as'=>'member-orders.update', 'before' => '', 'uses' => 'MemberOrdersController@postUpdate'));
    Route::post('member-orders/del', array('as'=>'member-orders.del', 'before' => '', 'uses' => 'MemberOrdersController@postDel'));
    //消息列表
    Route::get('message/index', array('as'=>'message.get', 'before' => '', 'uses' => 'MessageController@index'));
    Route::post('message/add', array('as'=>'message.add', 'before' => '', 'uses' => 'MessageController@postAdd'));
    Route::get('message/edit', array('as'=>'message.edit', 'before' => '', 'uses' => 'MessageController@getEdit'));
    Route::post('message/update', array('as'=>'message.update', 'before' => '', 'uses' => 'MessageController@postUpdate'));
    Route::post('message/del', array('as'=>'message.del', 'before' => '', 'uses' => 'MessageController@postDel'));
    //资金流水
    Route::get('money-log/index', array('as'=>'money-log.get', 'before' => '', 'uses' => 'MoneyLogController@index'));
    Route::post('money-log/add', array('as'=>'money-log.add', 'before' => '', 'uses' => 'MoneyLogController@postAdd'));
    Route::get('money-log/edit', array('as'=>'money-log.edit', 'before' => '', 'uses' => 'MoneyLogController@getEdit'));
    Route::post('money-log/update', array('as'=>'money-log.update', 'before' => '', 'uses' => 'MoneyLogController@postUpdate'));
    Route::post('money-log/del', array('as'=>'money-log.del', 'before' => '', 'uses' => 'MoneyLogController@postDel'));
    //订单明细
    Route::get('order-info/index', array('as'=>'order-info.get', 'before' => '', 'uses' => 'OrderInfoController@index'));
    Route::post('order-info/add', array('as'=>'order-info.add', 'before' => '', 'uses' => 'OrderInfoController@postAdd'));
    Route::get('order-info/edit', array('as'=>'order-info.edit', 'before' => '', 'uses' => 'OrderInfoController@getEdit'));
    Route::post('order-info/update', array('as'=>'order-info.update', 'before' => '', 'uses' => 'OrderInfoController@postUpdate'));
    Route::post('order-info/del', array('as'=>'order-info.del', 'before' => '', 'uses' => 'OrderInfoController@postDel'));
    //订单表
    Route::get('orders/index', array('as'=>'orders.get', 'before' => '', 'uses' => 'OrdersController@index'));
    Route::get('orders/unpay', array('as'=>'orders.get', 'before' => '', 'uses' => 'OrdersController@unpay'));
    Route::get('orders/pay', array('as'=>'orders.get', 'before' => '', 'uses' => 'OrdersController@pay'));

    Route::get('orders/export', array('as'=>'orders.get', 'before' => '', 'uses' => 'OrdersController@export'));
    Route::post('orders/import', array('as'=>'orders.get', 'before' => '', 'uses' => 'OrdersController@import'));

    Route::get('orders/send', array('as'=>'orders.get', 'before' => '', 'uses' => 'OrdersController@send'));
    Route::get('orders/unsend', array('as'=>'orders.get', 'before' => '', 'uses' => 'OrdersController@unsend'));
    Route::post('orders/add', array('as'=>'orders.add', 'before' => '', 'uses' => 'OrdersController@postAdd'));
    Route::get('orders/edit', array('as'=>'orders.edit', 'before' => '', 'uses' => 'OrdersController@getEdit'));
    Route::post('orders/update', array('as'=>'orders.update', 'before' => '', 'uses' => 'OrdersController@postUpdate'));
    Route::post('orders/del', array('as'=>'orders.del', 'before' => '', 'uses' => 'OrdersController@postDel'));
    Route::get('orders/export', array('as'=>'orders.export', 'before' => '', 'uses' => 'OrdersController@export'));
    Route::post('orders/import', array('as'=>'orders.import', 'before' => '', 'uses' => 'OrdersController@import'));


    //销售订单
    Route::get('sale-order/index', array('as'=>'sale-order.get', 'before' => '', 'uses' => 'SaleOrderController@index'));
    Route::post('sale-order/add', array('as'=>'sale-order.add', 'before' => '', 'uses' => 'SaleOrderController@postAdd'));
    Route::get('sale-order/edit', array('as'=>'sale-order.edit', 'before' => '', 'uses' => 'SaleOrderController@getEdit'));
    Route::post('sale-order/update', array('as'=>'sale-order.update', 'before' => '', 'uses' => 'SaleOrderController@postUpdate'));
    Route::post('sale-order/del', array('as'=>'sale-order.del', 'before' => '', 'uses' => 'SaleOrderController@postDel'));
    //销售收入
    Route::get('sale-money/index', array('as'=>'sale-money.get', 'before' => '', 'uses' => 'SaleMoneyController@index'));
    Route::post('sale-money/add', array('as'=>'sale-money.add', 'before' => '', 'uses' => 'SaleMoneyController@postAdd'));
    Route::get('sale-money/edit', array('as'=>'sale-money.edit', 'before' => '', 'uses' => 'SaleMoneyController@getEdit'));
    Route::post('sale-money/update', array('as'=>'sale-money.update', 'before' => '', 'uses' => 'SaleMoneyController@postUpdate'));
    Route::post('sale-money/del', array('as'=>'sale-money.del', 'before' => '', 'uses' => 'SaleMoneyController@postDel'));
    //周排行
    Route::get('weekend/index', array('as'=>'weekend.get', 'before' => '', 'uses' => 'WeekendController@index'));
    Route::post('weekend/add', array('as'=>'weekend.add', 'before' => '', 'uses' => 'WeekendController@postAdd'));
    Route::get('weekend/edit', array('as'=>'weekend.edit', 'before' => '', 'uses' => 'WeekendController@getEdit'));
    Route::post('weekend/update', array('as'=>'weekend.update', 'before' => '', 'uses' => 'WeekendController@postUpdate'));
    Route::post('weekend/del', array('as'=>'weekend.del', 'before' => '', 'uses' => 'WeekendController@postDel'));
    //提现记录
    Route::get('withdraw/index', array('as'=>'withdraw.get', 'before' => '', 'uses' => 'WithdrawController@index'));
    Route::post('withdraw/add', array('as'=>'withdraw.add', 'before' => '', 'uses' => 'WithdrawController@postAdd'));
    Route::get('withdraw/edit', array('as'=>'withdraw.edit', 'before' => '', 'uses' => 'WithdrawController@getEdit'));
    Route::post('withdraw/update', array('as'=>'withdraw.update', 'before' => '', 'uses' => 'WithdrawController@postUpdate'));
    Route::post('withdraw/del', array('as'=>'withdraw.del', 'before' => '', 'uses' => 'WithdrawController@postDel'));
    Route::get('withdraw/export', array('as'=>'withdraw.export', 'before' => '', 'uses' => 'WithdrawController@export'));
    Route::post('withdraw/import', array('as'=>'withdraw.import', 'before' => '', 'uses' => 'WithdrawController@import'));

      //
    Route::get('sort/index', array('as'=>'sort.get', 'before' => '', 'uses' => 'SortController@index'));
    Route::post('sort/add', array('as'=>'sort.add', 'before' => '', 'uses' => 'SortController@postAdd'));
    Route::get('sort/edit', array('as'=>'sort.edit', 'before' => '', 'uses' => 'SortController@getEdit'));
    Route::post('sort/update', array('as'=>'sort.update', 'before' => '', 'uses' => 'SortController@postUpdate'));
    Route::post('sort/del', array('as'=>'sort.del', 'before' => '', 'uses' => 'SortController@postDel'));



});
Route::group(['prefix'=>'admin','middleware'=>'auth','namespace' => 'Auth'], function()
{
  
    //权限表
    Route::get('system_menu/index', array('as'=>'system_menu.get', 'uses' => 'SystemMenuController@index'));
    Route::post('system_menu/add', array('as'=>'system_menu.add', 'uses' => 'SystemMenuController@postAdd'));
    Route::get('system_menu/edit', array('as'=>'system_menu.edit', 'uses' => 'SystemMenuController@getEdit'));
    Route::post('system_menu/update', array('as'=>'system_menu.update', 'uses' => 'SystemMenuController@postUpdate'));
    Route::post('system_menu/del', array('as'=>'system_menu.del', 'uses' => 'SystemMenuController@postDel'));
    //角色表
    Route::get('system_role/index', array('as'=>'system_role.get', 'uses' => 'SystemRoleController@index'));
    Route::post('system_role/add', array('as'=>'system_role.add', 'uses' => 'SystemRoleController@postAdd'));
    Route::get('system_role/edit', array('as'=>'system_role.edit', 'uses' => 'SystemRoleController@getEdit'));
    Route::post('system_role/update', array('as'=>'system_role.update', 'uses' => 'SystemRoleController@postUpdate'));
    Route::post('system_role/del', array('as'=>'system_role.del', 'uses' => 'SystemRoleController@postDel'));
    //系统管理员表
    Route::get('system_user/index', array('as'=>'system_user.get', 'uses' => 'SystemUserController@index'));
    Route::post('system_user/add', array('as'=>'system_user.add', 'uses' => 'SystemUserController@postAdd'));
    Route::get('system_user/edit', array('as'=>'system_user.edit', 'uses' => 'SystemUserController@getEdit'));
    Route::post('system_user/update', array('as'=>'system_user.update', 'uses' => 'SystemUserController@postUpdate'));
    Route::post('system_user/del', array('as'=>'system_user.del', 'uses' => 'SystemUserController@postDel'));
    //管理员修改自己的密码
    Route::post('system_user/updatemyselfpassword', array('as'=>'system_user.updatemyselfpassword', 'uses' => 'SystemUserController@updateMyselfPassword'));
    Route::get('system_user/editmyselfpassword', array('as'=>'system_user.editmyselfpassword', 'uses' => 'SystemUserController@editMyselfPassword'));

    //后台操作日志表
    Route::get('system_log/index', array('as'=>'system_log.index', 'uses' => 'SystemLogController@index'));
    Route::get('system_log/moneylog', array('as'=>'system_log.moneylog', 'uses' => 'SystemLogController@MoneyLog'));//充值记录 就是进账
    Route::get('system_log/consumemoneylog', array('as'=>'system_log.consumemoneylog', 'uses' => 'SystemLogController@ConsumeMoneyLog'));//消费记录 就是出账
    Route::get('system_log/integrallog', array('as'=>'system_log.integrallog', 'uses' => 'SystemLogController@integralLog'));//积分记录
    Route::get('system_log/withdrawallog', array('as'=>'system_log.withdrawallog', 'uses' => 'SystemLogController@withdrawalLog'));//提现记录
    Route::get('system_log/commissionlog', array('as'=>'system_log.commissionlog', 'uses' => 'SystemLogController@CommissionLog'));//佣金记录
    Route::get('system_log/aftersalelog', array('as'=>'system_log.aftersalelog', 'uses' => 'SystemLogController@AftersaleLog'));//售后记录
    Route::get('system_log/moneyorder', array('as'=>'system_log.moneyorder', 'uses' => 'SystemLogController@MoneyOrder'));//支付记录
    Route::get('system_log/faultlog', array('as'=>'system_log.faultlog', 'uses' => 'SystemLogController@faultLog'));//故障记录


});

Route::get('/login', 'LoginController@getIndex');
Route::post('/login', 'LoginController@postIndex');
Route::get('/login/out', 'LoginController@getOut');


Route::get('/vcode', ['as' => 'vcode', function() {
    $vcode = new App\Lib\Huasuan\Vcode;
    $vcode->doimage();
    Session::put('vcode', $vcode->get_code());
    return;
}]);

