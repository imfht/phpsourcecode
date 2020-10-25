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

$app->get('/', function()
{
	$_SESSION = Session::all();
	if(!isset($_SESSION["id"]) || !isset($_SESSION["name"]))
		return view('login');
	return view('admin');
});

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
	$api->get 	('/favormenu',function(){
		$menuData = DB::table("menu")->orderBy("father_id","ASC")->get();
		$menuReturn = [];
		foreach ($menuData as $key => $value) {
			if($value->url != "") continue;
			if($value->object == "category"){
				$module = DB::table("category")->where("id",$value->object_id)->pluck("module");
			}
			$menu = [
					"name"	=>$value->title,
					"path"	=>"object/".$value->object."/".$value->object_id.(isset($module)?"/".$module:"")
			];

			if($value->father_id == 0){
				$menuReturn[$value->id] = $menu;
			}else{
				$menuReturn[$value->father_id]["son"][] = $menu;
			}
		}
		return ["error"=>false,"result"=>array_values($menuReturn)];
	});



	$api->get 	('/menu',				'App\Http\Controllers\MenuController@index');
	$api->get 	('/menu/{id:[0-9]+}',	'App\Http\Controllers\MenuController@show');
	$api->post 	('/menu',     			'App\Http\Controllers\MenuController@store');
	$api->put 	('/menu/{id:[0-9]+}',	'App\Http\Controllers\MenuController@update');

	$api->get 	('/category',     		'App\Http\Controllers\CategoryController@index');
	$api->get 	('/category/{id:[0-9]+}','App\Http\Controllers\CategoryController@show');
	$api->post 	('/category',     		'App\Http\Controllers\CategoryController@store');
	$api->put 	('/category/{id:[0-9]+}','App\Http\Controllers\CategoryController@update');

	//通用
	$api->get 	('/{table:[A-Z_a-z]+}/config', 				'App\Http\Controllers\ApiController@config'		);

	$api->post 	('/file', 					                'App\Http\Controllers\ApiController@upload'		);
	$api->get 	('/{table:[A-Z_a-z]+}', 					'App\Http\Controllers\ApiController@index'		);
	$api->get 	('/{table:[A-Z_a-z]+}/{id:[0-9]+}', 		'App\Http\Controllers\ApiController@show'		);
	$api->post 	('/{table:[A-Z_a-z]+}', 					'App\Http\Controllers\ApiController@store'		);
	$api->put 	('/{table:[A-Z_a-z]+}/{id:[0-9]+}', 		'App\Http\Controllers\ApiController@update'		);
	$api->delete('/{table:[A-Z_a-z]+}/{id:[0-9]+}', 		'App\Http\Controllers\ApiController@destroy'	);
	$api->get 	('/config/init', 							'App\Http\Controllers\ConfigController@init'	);

	$api->get  ('/user/session',               				 'App\Http\Controllers\UserController@check');
	$api->post ('/user/login',                				 'App\Http\Controllers\UserController@login');
	$api->get  ('/user/logout',                				 'App\Http\Controllers\UserController@logout');
	$api->post ('/user/{id}/password',        				 'App\Http\Controllers\UserController@password');	

	
});

