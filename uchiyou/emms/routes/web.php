<?php

/**
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('index');
});
Route::get('/checkcode/{phone}','Auth\RegisterController@getCheckCode');
Route::post('/register/post','Auth\RegisterController@postRegister');
Auth::routes();
// 测试
Route::get('/test/email','Test\EmailTest@testEmail');
Route::get('/test/sms','Test\SmsTest@testSms');
Route::get('/test/strMethod','Test\PhpTest@testStringMethod');

Route::get('/home', 'HomeController@index');
Route::post('/picture/upload','PictureController@uploadPicture');
Route::get('/picture/download/{pictureName}','PictureController@downloadPicture');
Route::get('/picture/upload',function(){
	return view('elements/uploadPicture');
});
	// excel 导出
Route::get('excel/export','ExcelController@export');
Route::get('excel/export/{where}/{type}','ExcelController@exportHistory');
Route::group(['middleware' => 'auth', 'namespace' => 'Admin', 'prefix' => '/admin'], function () {
	//树形结构的操作
	Route::get('/tree',function(){
		return view('treeTrunk/entireTree');
	});
	Route::get('/tree/data/get/{nodeId}','TreeController@getTreeData');
	Route::get('/tree/material/anode/{nodeId}/data/get','TreeController@getANodeData');
	Route::get('/tree/material/anode/{nodeId}/data/get/table','TreeController@getANodeDataTable');
	Route::post('/tree/sort','TreeController@sort');
	Route::get('/tree/delete/{nodeId}','TreeController@deleteNode');
	//树叶节点的操作
	Route::resource('material', 'MaterialController');
	//树干节点的操作	
	Route::resource('treeTrunk','TreeTrunkController');
	Route::post('treeTrunk','TreeTrunkController@store');
	// 组织机构管理
	Route::get('/organization',function(){
		return view('organization/organization');
	});
	Route::get('/organization/tree/data/get/{parentId}','Organization\OrganizationController@getChildren');
	Route::get('/tree/organization/anode/{nodeId}/data/get','Organization\OrganizationController@getANode');
	Route::get('/tree/organization/anode/{nodeId}/data/get/table','Organization\OrganizationController@getANodeDataTable');
	Route::post('/tree/organization/sort','Organization\OrganizationController@sort');
	Route::get('/tree/organization/delete/{nodeId}','Organization\OrganizationController@deleteNode');
	Route::get('/organization/node/{nodeId}/delete/self','Organization\OrganizationController@isSelfNode');
	// 员工管理
	Route::post('/employee','EmployeeController@store');
	// 用户的通知消息
	Route::get('/message/number','Messages\MessageController@getMyMessageNumber');
	Route::get('/messages/show','Messages\MessageController@show');
	Route::get('/messages/showAll','Messages\MessageController@showAll');
	Route::get('/messages/{messageId}/delete',
			'Messages\MessageController@delete');
	Route::get('/messages/{messageId}/event/material/apply/{applyRecordId}/{operate}',
			'Messages\EventHandlerController@handleMaterialPurchaseApply');

	// 物资的生命周期
	Route::post('/material', 'MaterialController@store');
	Route::get('/material/{materialId}/show', 'MaterialController@showDetailInfo');
	// 租借流程
	Route::get('/material/rent/history/search/{where}/{type}','RentController@showHistorySearch');
	Route::get('/material/rent/history/{where}/{type}','RentController@showRentHistory');
	Route::get('/material/rent/{materialId}','RentController@getRent');// 普通成员自己租借商品
	Route::post('/material/rent/{materialId}','RentController@postRent');// 普通成员自己租借商品
	Route::get('/material/return/{rentRecordId}','RentController@returnMaterial');// 归还物资动作
	
	// 购买流程
	Route::get('/material/purchase/history/search/{where}/{type}','PurchaseController@showHistorySearch');
	Route::get('/material/purchase/history/{where}/{type}','PurchaseController@showPurchaseHistory');
	Route::post('/material/purchase/apply','PurchaseController@purchaseApply');
	Route::get('/material/purchase/{purchaseApplyId}/approve/{operate}','PurchaseController@doApprovePurchase');
	Route::get('/material/purchase/apply/counts','PurchaseController@getWaitApproveCount');
	Route::get('/material/purchase/excel/export/{where}/{type}','PurchaseController@exportExcel');
	// 预约流程
	Route::get('/material/appointment/{materialId}','AppointmentController@getAppointment');// 预约物资
	Route::get('/material/disappointment/{recordId}','AppointmentController@getDisappointment');// 取消预约
	Route::get('/material/appointment/history/search/{where}/{type}','AppointmentController@showHistorySearch');// 显示自己已经预约物资的信息
	Route::get('/material/appointment/history/{where}/{type}','AppointmentController@showAppointmentHistory');// 显示自己已经预约物资的信息
	//Route::get('/material/appointment/{appointmentId}/delete/{where}','AppointmentController@showAppointmentHistory');// 显示自己已经预约物资的信息
	//  维修流程
	Route::get('/material/repaire/history/search/{type}','RepaireController@showHistorySearch');// 
	Route::get('/material/repaire/history/{type}','RepaireController@showRepaireHistory');// 
	Route::post('/material/{materialId}/repaire/apply','RepaireController@applyRepaire');// 
	Route::get('/material/repaire/{recordId}/result/{result}','RepaireController@repaireResult');// 
	Route::get('/material/repaire/wait/counts','RepaireController@getWaitRepaireCount');// 
	// 物资配送信息
	Route::get('/material/deliver/history/search/{type}','DeliverController@showHistorySearch');
	Route::get('/material/deliver/history/{type}','DeliverController@showDeliverHistory');
	Route::get('/material/deliver/step/{deliverId}/start','DeliverController@startDeliver');
	Route::get('/material/deliver/step/{usingRecordId}/accepted','DeliverController@acceptDeliver');
	Route::get('/material/deliver/wait/counts','DeliverController@getWaitDeliverCount');//
	// 搜索操作
	Route::get('/search','SearchController@search');
	Route::get('/search/get',function(){
		return view('elements/search');
	});
	// 统计信息
	Route::get('/statistics/basic/{type}','StatisticsController@getBasics');
	Route::get('/statistics/material/department','StatisticsController@getMaterialDepartment');
	Route::get('/statistics/node','StatisticsController@getNodeTotal');
	
	// 用户个人中心操作
	Route::get('/user','UserController@index');
	Route::post('/user/baseInfo/update','UserController@updateBaseInfo');
	Route::post('/user/password/update','UserController@updatePassword');
	// 统一的删除操作
	Route::get('/delete/{recordType}/{recordId}/{where}','UserController@delete');
	// 企业信息的操作
	Route::get('/company','CompanyController@getCompanyInfo');
	Route::post('/company/baseinfo/update','CompanyController@postBaseInfo');
	Route::post('/company/service/update','CompanyController@postServiceInfo');
	// 系统管理员操作
	Route::get('/housekeep/home','HouseKeepController@index');
	Route::post('/housekeep/search','HouseKeepController@search');
	Route::get('/housekeep/company/{companyId}/shutdown/{operate}','HouseKeepController@operateCompany');
	
});