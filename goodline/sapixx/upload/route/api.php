<?php
/**
 * @copyright   Copyright (c) 2018 http://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 默认路由配置
 */
//快捷访问
use think\facade\Route;
Route::rule('openapi-[:app]/:version/:controller/:action','system/api.:version.:controller/:action')->append(['sapixx'=>1]);
Route::rule('openapi/[:app]/:version/:controller/:action','system/api.:version.:controller/:action')->append(['sapixx'=>1]);
Route::rule('openapi-[:app]/:version/:action','system/api.:version.miniapp/:action')->append(['sapixx'=>1]); 
Route::rule('openapi/[:app]/:version/:action','system/api.:version.miniapp/:action')->append(['sapixx'=>1]); 
//API访问
//路径1
Route::rule('app-[:app]/:module-:controller-:action/[:id]',':module/home.:controller/:action')->append(['sapixx'=>1]); //兼容处理
Route::rule('app-[:app]/:module/:controller/:action/[:id]',':module/home.:controller/:action')->append(['sapixx'=>1]);
Route::rule('app/:app/:module/:controller/:action/[:id]',':module/home.:controller/:action')->append(['sapixx'=>1]);
//API接口
Route::rule('api-[:app]/:version/:module-:controller-:action/[:id]',':module/api.:version.:controller/:action')->append(['sapixx'=>1]); //兼容处理
Route::rule('api-[:app]/:version/:module/:controller/:action/[:id]',':module/api.:version.:controller/:action')->append(['sapixx'=>1]);
Route::rule('api/[:app]/:version/:module/:controller/:action/[:id]',':module/api.:version.:controller/:action')->append(['sapixx'=>1]);
//路径2