<?php
use Illuminate\Routing\Router;

Admin::registerAuthRoutes ();

Route::group ( [ 
		'prefix' => config ( 'admin.route.prefix' ),
		'namespace' => config ( 'admin.route.namespace' ),
		'middleware' => config ( 'admin.route.middleware' ) 
], function (Router $router) {
	
	$router->get ( '/', 'HomeController@index' );
	$router->get ( 'statistic', 'UserController@statistic' );
	
	$router->resource ( 'users', UserController::class );
	
	$router->resource ( 'feeds', FeedController::class );
	$router->resource ( 'feedsubs', FeedSubController::class );
	
	$router->resource ( 'users', UserController::class );
	
	$router->resource ( 'articles', ArticleController::class );
	$router->resource ( 'articlemarks', ArticleMarkController::class );
	$router->resource ( 'feedbacks', FeedbackController::class );
	$router->resource ( 'goals', GoalController::class );
	$router->resource ( 'categorys', CategoryController::class );
	$router->resource ( 'kindlelogs', KindleLogController::class );
	$router->resource ( 'minds', MindController::class );
	$router->resource ( 'notes', NoteController::class );
	$router->resource ( 'pomos', PomoController::class );
	$router->resource ( 'settings', SettingController::class );
	$router->resource ( 'tasks', TaskController::class );
	$router->resource ( 'things', ThingController::class );
} );
