<?php

//登录
Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController'
]);

//前台
Route::get('/', 'WelcomeController@index');
Route::get('search', 'SearchController@index');
Route::resource('category', 'CategoryController',['only'=>['index','show']]);
Route::resource('article', 'ArticleController',['only'=>['index','show']]);
Route::resource('person', 'PersonController',['only'=>['index','show']]);
Route::resource('comment', 'CommentController',['only'=>['store']]);
Route::get('project/type/{id}', 'ProjectController@getType');
Route::resource('project', 'ProjectController',['only'=>['index','show']]);
Route::get('page/{id}', ['as' => 'page.show' ,'uses' => 'PageController@show']);
Route::get('friendLink/{id}', 'FriendLinkController@show');

//个人中心
Route::group(['prefix' => 'user', 'namespace' => 'User', 'middleware'=>['auth']], function () {
    Route::get('', 'IndexController@index');
    Route::resource('favorite', 'FavoriteController');
    Route::resource('password', 'PasswordController',['only'=>['index','store']]);
});

//管理系统
Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware'=>['auth','isadmin']], function () {
    Route::get('', 'DashController@index');
    Route::resource('attachment', 'AttachmentController',['only'=>['store']]);
    Route::resource('category', 'CategoryController');
    Route::resource('articles', 'ArticlesController');
    Route::resource('system', 'SystemController',['only'=>['index','store']]);
    Route::resource('pages', 'PagesController');
    Route::resource('users', 'UsersController',['only'=>['index','update','destroy']]);
    Route::resource('persons', 'PersonsController');
    Route::resource('projects', 'ProjectsController');
    Route::resource('friendLinks', 'FriendLinksController');
    Route::resource('menus', 'MenusController');
    Route::resource('adspaces', 'AdspacesController');
    Route::resource('adimages', 'AdimagesController');
    Route::resource('comments', 'CommentsController');
    Route::get('ueditor', 'UeditorController@index');
    Route::post('ueditor', 'UeditorController@index');
    Route::get('logs', 'LogsController@index');
});