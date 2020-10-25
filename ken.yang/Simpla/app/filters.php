<?php

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

App::before(function($request) {
    //检查程序是否已经安装
    if (!strpos(Request::url(), 'install')) {
        if (!file_exists('../app/lock.txt')) {
            return Redirect::to('/install/step1');
        }
    }
});


App::after(function($request, $response) {
    //
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

Route::filter('auth', function() {
    if (Auth::guest()) {
        if (Request::ajax()) {
            return Response::make('Unauthorized', 401);
        } else {
            return Redirect::guest('login');
        }
    }
});


Route::filter('auth.basic', function() {
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

Route::filter('guest', function() {
    if (Auth::check()) {
        return Redirect::to('/');
    }
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

Route::filter('csrf', function() {
    if (Session::token() !== Input::get('_token')) {
        throw new Illuminate\Session\TokenMismatchException;
    }
});

/**
 * 后台权限访问过滤
 * 只有管理员权限才能访问
 */
Route::filter('admin', function() {
    //排除403和404页面
    if (Request::path() != 'admin/403' && Request::path() != 'admin/404') {
        //使用了记住我的功能
        //未登录的用户跳转到登录页面，登录了的用户验证是否具有权限
        if (Auth::check() || Auth::viaRemember()) {
            if (User::find(Auth::user()->id)->roles['rid'] == '3') {
                //echo '验证通过';
            } elseif (User::find(Auth::user()->id)->roles['rid'] == '1') {
                if (stristr($as_name, 'admin') != '0') {
                    return Redirect::to('admin/403');
                }
            } else {
                //根据数据库权限验证
                $rid = User::find(Auth::user()->id)->roles['rid'];
                $result = RolesPermission::check($rid);
                if (!$result) {
                    return Redirect::to('admin/403');
                }
            }
        } else {
            if (Request::path() == 'admin/login') {//$_SERVER['REQUEST_URI']
                //如果是登录页面，则停留此页面
            } else {
                return Redirect::to('admin/login');
            }
        }
    }
});

/**
 * 不存在的router路径，直接跳转到404页面
 * 例如：dev.example.com/abcd这个路径本身就不存在
 */
App::missing(function($exception) {
    //return Response::view('404', array(), 404);
    if (substr(Request::path(), 0, 5) == 'admin') {
        return Redirect::to('admin/404');
    }
    return Redirect::to('404');
});

/**
 * 有时您可能希望当记录没有被找到时抛出一个异常，允许您使用 App::error 处理器捕捉这些异常并显示404页面。
 * 例如：node/44,数据库中并没有44这个节点
 */
//App::error(function($exception) {
//    return Redirect::to('404');
//    //return Response::make('Not Found', 404);
//});
