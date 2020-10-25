<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;
use think\facade\Cache;
use app\model\Model;
use app\model\Rewrite;


$model = Cache::get('model_list');
if (!$model) {
	$model = Model::where('status', '>', 0)->field(['id', 'name'])->select()->toArray();
	Cache::set('model_list', $model);
}

if (!empty($model)) {
	foreach ($model as $value) {
		Route::rule('/admin/' . $value['name'] . '/:function', 'admin.Content/:function')->append(['name'=>$value['name'], 'model_id' => $value['id']]);
		Route::rule($value['name'] . '/index', 'front.Content/index')->append(['name'=>$value['name'], 'model_id' => $value['id']]);
		Route::rule($value['name'] . '/list/:id', 'front.Content/lists')->append(['name'=>$value['name'], 'model_id' => $value['id']]);
		Route::rule($value['name'] . '/detail-:id', 'front.Content/detail')->append(['name'=>$value['name'], 'model_id' => $value['id']]);
		Route::rule('/user/' . $value['name'] . '/:function', 'user.Content/:function')->append(['name'=>$value['name'], 'model_id' => $value['id']]);
		Route::rule('/api/' . $value['name'] . '/:function', 'api.Content/:function')->append(['name'=>$value['name'], 'model_id' => $value['id']]);
	}
}

$rewrite = Cache::get('rewrite_list');
if (!$rewrite) {
	$rewrite = Rewrite::select()->toArray();
	Cache::set('rewrite_list', $rewrite);
}

if (!empty($rewrite)) {
	foreach ($rewrite as $key => $value) {
		$url = parse_url($value['url']);
		$param = [];
		parse_str($url['query'], $param);
		Route::rule($value['rule'], $url['path'])->append($param);
	}
}

Route::rule('/', 'front.Index/index');
Route::rule('search', 'front.Content/search');
Route::rule('category', 'front.Content/category');
Route::rule('topic-:id', 'front.Content/topic');
Route::rule('form/:id/[:name]', 'front.Form/index');
Route::rule('front/:controller/:function', 'front.:controller/:function');

Route::group('admin', function () {
	Route::rule('/', 'admin.Index/index');
	Route::rule('login', 'admin.Index/login');
	Route::rule('logout', 'admin.Index/logout');
	Route::rule('upload/:function', 'Upload/:function')->append(['from'=>'admin']);
	Route::rule(':controller/:function', 'admin.:controller/:function');
});

Route::group('user', function () {
	Route::rule('/', 'user.Index/index');
	Route::rule('login', 'user.Index/login');
	Route::rule('logout', 'user.Index/logout');
	Route::rule('register', 'user.Index/register');
	Route::rule('upload/:function', 'Upload/:function')->append(['from'=>'user']);
	Route::rule(':controller/:function', 'user.:controller/:function');
});

Route::group('api', function () {
	Route::rule('/', 'api.Index/index');
	Route::rule('login', 'api.Login/index');
	Route::rule('register', 'api.Login/register');
	Route::rule('logout', 'api.Login/logout');
	Route::rule('upload/:function', 'Upload/:function')->append(['from'=>'api']);
	Route::rule(':controller/:function', 'api.:controller/:function');
})->allowCrossDomain([
	'Access-Control-Allow-Origin'      => '*',
	'Access-Control-Allow-Credentials' => 'true',
	'Access-Control-Allow-Headers'     => 'authorization, token, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-Requested-With',
]);

Route::miss('front.Index/miss');