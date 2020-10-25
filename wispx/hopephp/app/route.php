<?php

// +----------------------------------------------------------------------
// | HopePHP
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.wispx.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: WispX <i@wispx.cn>
// +----------------------------------------------------------------------

// [ 路由配置 Link: https://github.com/NoahBuscher/Macaw ]

use hope\Route;

//Route::any('(:all)', 'app\index\controller\index@index');
Route::get('/', 'app\controller\index@index');