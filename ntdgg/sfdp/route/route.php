<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------


Route::get('index/sfdp/add/sid/:sid','\sfdp\Api@add');
Route::get('index/sfdp/desc','\sfdp\Api@sfdp');
Route::get('index/sfdp/sfdp_desc/sid/:sid','\sfdp\Api@sfdp_desc');
Route::get('index/sfdp/sfdp_fix/sid/:sid','\sfdp\Api@sfdp_fix');
Route::get('index/sfdp/create','\sfdp\Api@sfdp_create');
Route::get('index/sfdp/sfdp_script/sid/:sid','\sfdp\Api@sfdp_script');
Route::get('index/sfdp/sfdp_ui/sid/:sid','\sfdp\Api@sfdp_ui');
Route::get('index/sfdp/sfdp_deldb/sid/:sid','\sfdp\Api@sfdp_deldb');
Route::get('index/sfdp/sfdp_fun','\sfdp\Api@sfdp_fun');
Route::post('index/sfdp/sfdp_desc_save','\sfdp\Api@sfdp_save');
Route::post('index/sfdp/sfdp_script_save','\sfdp\Api@sfdp_script_save');
Route::post('index/sfdp/add/sid/:sid','\sfdp\Api@saveadd');
Route::post('index/sfdp/sfdp_fun_save','\sfdp\Api@sfdp_fun_save');
Route::post('index/sfdp/get_function_val','\sfdp\Api@get_function_val');
/*列表路由*/
Route::get('index/sfdp/list/sid/:sid','\sfdp\Api@lists');
Route::post('index/sfdp/list/sid/:sid','\sfdp\Api@lists');
/*查看路由*/
Route::get('index/sfdp/sfdp_view/sid/:sid/bid/:bid','\sfdp\Api@sfdp_view');



