<?php
// +----------------------------------------------------------------------
// | YFCMF [ WE CAN DO IT MORE SIMPLE]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2018 http://yfcmf.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: rainfer <rainfer520@qq.com>
// +----------------------------------------------------------------------
use think\facade\Route;

// 定义插件路由
Route::any('addons/execute/:route', "\\app\\common\\controller\\Base@execute");

Route::rule(config('yfcmf.adminpath') . '/:c/:a', 'admin/:c/:a');
Route::rule(config('yfcmf.adminpath') . '/:c', 'admin/:c/index');
Route::rule(config('yfcmf.adminpath'), 'admin/index/index');
//阻止admin
/*Route::rule('admin',function(){
	return '404 Not Found';
});*/
return [
];
