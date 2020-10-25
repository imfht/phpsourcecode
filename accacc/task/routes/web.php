<?php

/*
 * |--------------------------------------------------------------------------
 * | Web Routes
 * |--------------------------------------------------------------------------
 * |
 * | Here is where you can register web routes for your application. These
 * | routes are loaded by the RouteServiceProvider within a group which
 * | contains the "web" middleware group. Now create something great!
 * |
 */
Route::group ( [ 
		'middleware' => [ 
				'web' 
		] 
], function () {
	
	Route::get ( '/', function () {
		return view ( 'welcome' );
	} )->middleware ( 'guest' );
	
	Route::get ( '/home', 'IndexController@index' );
	Route::get ( '/index', 'IndexController@index' );
	Route::get ( '/index/test', 'IndexController@test' );
	
	Route::get ( '/help/feedback', 'HelpController@feedback' );
	Route::post ( '/help/feedbackStore', 'HelpController@feedbackStore' );
	
	Route::get ( '/notes', 'NoteController@index' );
	Route::post ( '/notes/upload', 'NoteController@upload' );
	Route::get ( '/notes/add_content/{add_content}', 'NoteController@index' );
	Route::post ( '/note', 'NoteController@store' );
	Route::delete ( '/note/{note}', 'NoteController@destroy' );
	Route::get ( '/note/getRecord/{note}', 'NoteController@getRecord' );
	
	Route::get ( '/minds', 'MindController@index' );
	Route::post ( '/mind', 'MindController@store' );
	Route::delete ( '/mind/{mind}', 'MindController@destroy' );
	Route::get ( '/mind/{mind}', 'MindController@view' );
	Route::get ( '/mindajaxget/{mind}', 'MindController@ajaxget' );
	Route::post ( '/mind/{mind}', 'MindController@update' );
	
	Route::get ( '/tasks', 'TaskController@index' );
	Route::get ( '/tasksall', 'TaskController@getAllList' );
	Route::post ( '/task', 'TaskController@store' );
	Route::delete ( '/task/{task}', 'TaskController@destroy' );
	Route::post ( '/task/{task}', 'TaskController@update' );
	Route::get ( '/task/{task}', 'TaskController@update' );
	Route::get ( '/taskpriority', 'TaskController@priority' );
	
	Route::get ( '/cals', 'CalController@index' );
	Route::get ( '/calics/{theme}', 'CalController@ics' );
	Route::get ( '/taskics/{cal_token}', 'CalController@taskics' );
	
	Route::get ( '/categorys', 'CategoryController@index' );
	Route::post ( '/category', 'CategoryController@store' );
	Route::post ( '/category/{category}', 'CategoryController@update' );
	Route::get ( '/category/{category}', 'CategoryController@update' );
	Route::delete ( '/category/{category}', 'CategoryController@destroy' );
	Route::post ( '/categorys/sort', 'CategoryController@sort' );
	
	Route::get ( '/feeds', 'FeedController@index' );
	Route::get ( '/feeds/setting', 'FeedController@setting' );
	Route::post ( '/feed', 'FeedController@store' );
	Route::get ( '/feed/checkNewFeed', 'FeedController@checkNewFeed' );
	Route::get ( '/feed/checkFeedUrl', 'FeedController@checkFeedUrl' );
	Route::delete ( '/feed/{feedSub}', 'FeedController@destroy' );
	Route::post ( '/feed/{feedSub}', 'FeedController@update' );
	Route::get ( '/feed/{feedSub}', 'FeedController@update' );
	Route::post ( '/feeds/sort', 'FeedController@sort' );
	Route::get ( '/feeds/explorer', 'FeedController@explorer' );
	Route::get ( '/feeds/quickstore', 'FeedController@quickstore' );
	Route::get ( '/feeds/search', 'FeedController@search' );
	Route::get ( '/feeds/weixinrss', 'FeedController@weixinrss' );
	Route::get ( '/feeds/weiborss', 'FeedController@weiborss' );
	Route::get ( '/feeds/opml', 'FeedController@opml' );
	Route::post ( '/feeds/importOpml', 'FeedController@importOpml' );
	
	Route::get ( '/articles', 'ArticleController@index' );
	Route::post ( '/article', 'ArticleController@store' );
	Route::get ( '/article/list', 'ArticleController@list' );
	Route::post ( '/article/mark', 'ArticleController@mark' );
	Route::get ( '/article/view/{article}', 'ArticleController@view' );
	Route::get ( '/articles/status/{articleSub}', 'ArticleController@status' );
	Route::get ( '/articles/allstatus', 'ArticleController@status' );
	Route::delete ( '/article/{article}', 'ArticleController@destroy' );
	Route::get ( '/article/record/{articleSub}', 'ArticleController@getArticleRecord' );
	Route::get ( '/article/navinfo', 'ArticleController@navinfo' );
	Route::get ( '/article/navcountinfo', 'ArticleController@navcountinfo' );
	
	Route::get ( '/pomos', 'PomoController@index' );
	Route::get ( '/pomos/start', 'PomoController@start' );
	Route::get ( '/pomos/discard/{pomo}', 'PomoController@discard' );
	Route::get ( '/pomos/discard/', 'PomoController@discard' );
	
	Route::get ( '/third/index', 'ThirdController@index' );
	Route::get ( '/third/testFave', 'ThirdController@testFave' );
	Route::get ( '/third/fanfouIndex', 'ThirdController@fanfouIndex' );
	Route::get ( '/third/fanfouCallback', 'ThirdController@fanfouCallback' );
	Route::get ( '/third/twitterIndex', 'ThirdController@twitterIndex' );
	Route::get ( '/third/twitterCallback', 'ThirdController@twitterCallback' );
	
	Route::post ( '/pomo/{pomo}', 'PomoController@store' );
	Route::delete ( '/pomo/{pomo}', 'PomoController@destroy' );
	
	Route::get ( '/statistics', 'StatisticsController@index' );
	
	Route::get ( '/goals', 'GoalController@index' );
	Route::post ( '/goal', 'GoalController@store' );
	Route::delete ( '/goal/{goal}', 'GoalController@destroy' );
	Route::post ( '/goal/{goal}', 'GoalController@update' );
	Route::get ( '/goal/{goal}', 'GoalController@update' );
	
	// welcome
	Route::get ( '/pomo/welcome', 'PomoController@welcome' );
	Route::get ( '/note/welcome', 'NoteController@welcome' );
	Route::get ( '/read/welcome', 'ArticleController@welcome' );
	Route::get ( '/minds/welcome', 'MindController@welcome' );
	
	Route::get ( '/accounts', 'AccountController@index' );
	
	Route::get ( '/settings', 'SettingController@index' );
	Route::post ( '/setting/{setting}', 'SettingController@update' );
	Route::post ( '/setting', 'SettingController@update' );
	
	Route::get ( '/kindles', 'KindleController@index' );
	Route::get ( '/kindle/test', 'KindleController@test' );
	
	Route::get ( '/things', 'ThingController@index' );
	Route::post ( '/thing', 'ThingController@store' );
	Route::delete ( '/thing/{thing}', 'ThingController@destroy' );
	Route::post ( '/thing/{thing}', 'ThingController@update' );
	Route::get ( '/thing/{thing}', 'ThingController@update' );
	
	Auth::routes ();
	
	Route::get ( 'login/third/{driver}', 'Auth\LoginController@thirdRedirect' );
	Route::get ( 'login/third/{driver}/callback', 'Auth\LoginController@thirdCallback' );
	
	Route::get ( '/logout', 'Auth\LoginController@logout' );
} );

Route::group ( [ 
		'middleware' => [ 
				'web' 
		] 
], function () {
	Route::get ( '/api/wechat/login', 'Api\WechatController@wechatlogin' );
	Route::get ( '/api/wechat/articles', 'Api\WechatController@articles' );
	Route::get ( '/api/wechat/articleview', 'Api\WechatController@articleview' );
	Route::get ( '/api/wechat/explorer', 'Api\WechatController@explorer' );
	Route::get ( '/api/wechat/notes', 'Api\WechatController@notes' );
	Route::get ( '/api/wechat/addNote', 'Api\WechatController@addNote' );
	Route::get ( '/api/wechat/articleSubStatus', 'Api\WechatController@articleSubStatus' );
	Route::get ( '/api/wechat/articleSubStatus/{articleSub}', 'Api\WechatController@articleSubStatus' );
	
	Route::get ( '/api/pomos', 'Api\TestController@index' );
	Route::get ( '/api/pomo/info', 'Api\TestController@info' );
	Route::get ( '/api/pomo/start', 'Api\TestController@start' );
	Route::get ( '/api/pomo/discard/{pomo}', 'Api\TestController@discard' );
	Route::get ( '/api/pomo/discard/', 'Api\TestController@discard' );
	Route::post ( '/api/pomos/{pomo}', 'Api\TestController@store' );
	Route::delete ( '/api/pomos/{pomo}', 'PomoController@destroy' );
} );
		