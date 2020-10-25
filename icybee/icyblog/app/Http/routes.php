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

//Route::get('/', 'WelcomeController@index');
Route::get('/','BlogIndexController@frontIndex');

Route::get('shit', 'HomeController@index');

Route::get('label/{label}/page/{page}','BlogIndexController@index');

Route::get('page/{page}','BlogIndexController@pageIndex');

Route::get('label/{label}','BlogIndexController@labelIndex');

Route::get('publish','BlogPublishController@index');

Route::get('publish/{id}','BlogPublishController@editArticle');

Route::get('commit','BlogCommitController@index');

Route::post('commit','BlogCommitController@index');

Route::get('article/{id}.html','BlogIndexController@article');

Route::get('articleJson','BlogIndexController@articleJson');

Route::get('articleSummary','BlogIndexController@articleSummary');

Route::post('upload/base64','UploadBase64Controller@upload');

//Route::get('pics/{filename}','UploadBase64Controller@download');

Route::get('api/getAllLabel','BlogLabelController@getAllLabel');

Route::get('test',function(){
	return "test";
});

/*Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
*/
Route::get('/blogadmin','BlogAdminController@frontpage');
Route::get('/blogadmin/delete','BlogAdminController@delete');
Route::get('/blogadmin/addArticleLabel','BlogAdminController@addArticleLabel');
Route::get('/blogadmin/{page}','BlogAdminController@index');
Route::get('/blogadmin/addLabel/{name}','BlogAdminController@addLabel');
Route::get('/blogadmin/deleteLabel/{name}','BlogAdminController@deleteLabel');


Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');
