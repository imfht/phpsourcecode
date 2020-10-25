<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
Route::get('/', function()
{
	$response = View::make('home');

	if( Auth::check() ){

		$currUserId = Auth::user()['id'];

		$data['unread'] = [
			'notification'=>NotifyInbox::getUnreadStatistics($currUserId),
			'message'=>MessageInbox::getUnreadStatistics($currUserId)
		];

		$response->with($data);
	}

    return $response;
});


Route::get('/guide', function(){
	return Response::make('暂未开通');
});

/*
 * 实现用户的登陆、注册、退出
 */
Route::controller('authority', 'AuthorityController');

Route::group(['before'=>'auth'], function(){

	/*
	 * Angular App的入口文件
	 */
	Route::controller('/ng', 'NgController');

	Route::group(['prefix'=>'api', 'before'=>'csrf_header'], function(){

		/*
		 * 项目模块的后台访问API
		 */

		Route::resource('/project', 'ProjectController',
			    ['only'=>['index', 'show', 'store', 'update', 'destroy']]);
		//项目-成员 后台访问API
		Route::resource('/project/{project_id}/member', 'MemberController');
		//项目-任务 后台访问API
        Route::resource('/project/{project_id}/task', 'TaskController');
		//项目-分享中心 后台访问API
		Route::resource('/project/{project_id}/sharing', 'ProjectSharingController');
        //项目-标签 后台访问API
        Route::resource('/project/{project_id}/tag', 'TagController', ['only' => ['index', 'show', 'store', 'destroy']]);
		//项目-讨论 后台访问API
		Route::resource('/project/{project_id}/discussion', 'ProjectDiscussionController');
		//项目-讨论-回复 后台访问API
		Route::controller('/project/{project_id}/discussion/{discussion_id}/comment', 'ProjectDiscussionCommentController');


		/*
		 * 个人模块的后台访问API
		 */

		//个人信息（密码修改） 后台访问API
		Route::controller('/personal', 'PersonalController');
		// 通知 后台访问API
		Route::resource('/notify', 'NotifyController');
		//私信 后台访问API
		Route::resource('messages', 'MessageController');


        /*
         * 一般资源获取入口
         */
        Route::resource('/role', 'RoleController',
                ['only'=>['index', 'show']]);           //获取角色的有关信息

        Route::controller('/user', 'UserController');   //获取用户的有关信息

        Route::resource('/task-status', 'TaskStatusController');  //获取项目的状态的具体信息

		Route::resource('/task-priority', 'TaskPriorityController');	//获取项目的优先级的具体信息

		Route::controller('/backend-filter/{query}', 'FilterMethodController'); //获取某项资源的过滤筛选条件

    	Route::resource('/notify-type', 'NotifyTypeController'); //获取通知类型的具体信息

		Route::resource('messages', 'MessageController');	//私信

	});

	Route::group(['prefix'=>'api'], function(){
		Route::controller('/file', 'FileController');	//用于实现文件上传
	});

});

Route::group(['prefix'=>'api'], function(){
	Route::get('repeat/{mixed}', 'UserController@checkRepeat');
});
