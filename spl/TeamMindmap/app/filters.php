<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/


App::before(function($request)
{
	//
});


App::after(function($request, $response)
{
	//
});


/**
 * 捕获查询失败的异常，并返回404信息
 */
App::error(function(ModelNotFoundException $error){

    $currentModel = $error->getModel();

    $errorMessages = [
        'User'=>'用户',
        'Project_Member'=>'项目成员',
        'ProjectRole'=>'项目角色',
        'ProjectTask'=>'任务',
		'NotifyInbox' => '私信',
		'ProjectTaskPriority'=>'任务优先级'
    ];

    if( isset($errorMessages[ $currentModel ] ) ){
        $respError = $errorMessages[ $currentModel ]. '不存在';
    } else {
        $respError = 'Not Found';
    }

    return Response::json( [
        'error'=> $respError
    ], 404);
});

App::error(function(Illuminate\Session\TokenMismatchException $error){
	return Response::make('Invalid CSRF TOKEN', 403);
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest())
	{
		/*
		 * 如果用户访问了被auth过滤器保护的页面（该页面需要登陆后操作）,
		 * 则设置标记Session `tryAccessAuth`，登陆后跳转到原访问页面.
		 */
		Session::set('tryAccessAuthUri', Request::getRequestUri());

		if (Request::ajax())
		{
			return Response::make('Unauthorized', 401);
		}
		else
		{
			return Redirect::guest('/authority/login');
		}
	}
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() !== Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});


/**
 * 此过滤器用于校验项目管理的数据交互中权限校验和数据验证问题，
 * 具体的实现在类： /app/libraries/AccessProjectFilter.php
 */
Route::filter('accessProject','Libraries\Filter\AccessProjectFilter');

/**
 * 针对ngApp所发起的请求而设置的CSRF TOKEN过滤
 */
Route::filter('csrf_header', function(Illuminate\Routing\Route $route, \Illuminate\Http\Request $request){

	if( Session::token() !== $request->header('X-CSRF-TOKEN') ){
		throw new Illuminate\Session\TokenMismatchException;
	}
});