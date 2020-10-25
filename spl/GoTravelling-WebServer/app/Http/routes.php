<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function(){
   return view('home');
});

Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);

Route::get('download/{platform}', function($platform){
    echo $platform . ' 版本正在建设中';
})->where(['platform' => '[\w_]+']);

Route::group(['middleware' => 'auth'], function(){
    Route::controller('personal', 'PersonalController');

    Route::resource('collection', 'CollectionController');
    Route::resource('collection-route', 'CollectionRouteController');
    Route::resource('route', 'RouteController');
    Route::resource('route.daily', 'RouteDailyController');
    Route::resource('route.transport', 'RouteTransportController');
    Route::resource('route.photo', 'RoutePhotoController');
    Route::resource('route.note', 'RouteNoteController');
});

// 针对App所设定的路由规则
Route::group(['prefix'=>'api'], function(){

    Route::controller('user', 'UserController');

    Route::controller('auth', 'Auth\AuthController');

    Route::controller('route-tag', 'RouteTagController');

    Route::group(['middleware' => 'auth'], function(){
        Route::controller('personal', 'PersonalController');

        Route::resource('collection', 'CollectionController');
        Route::resource('collection-route', 'CollectionRouteController');
        Route::resource('route', 'RouteController');
        Route::resource('route.daily', 'RouteDailyController');
        Route::resource('route.transport', 'RouteTransportController');
        Route::resource('route.photo', 'RoutePhotoController');
        Route::resource('route.note', 'RouteNoteController');
        Route::resource('sight', 'SightController');
    });

});

