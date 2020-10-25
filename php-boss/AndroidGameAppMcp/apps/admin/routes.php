<?php
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\App;
Route::get('/',function(){
    return Redirect::to('/login');
});

Route::controller('login','LoginController');

$modules = Config::get('app.installed_modules',array());
foreach($modules as $module=>$path){
	if(file_exists($path . '/routes.php')){
		include_once $path . '/routes.php';
	}
}