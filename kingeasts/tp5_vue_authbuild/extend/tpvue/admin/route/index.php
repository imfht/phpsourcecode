<?php
// +----------------------------------------------------------------------
// | TpAndVue.
// +----------------------------------------------------------------------
// | FileName: index.php
// +----------------------------------------------------------------------
// | Author: King east <1207877378@qq.com>
// +----------------------------------------------------------------------


use tpvue\admin\library\Route;

Route::add('/', 'index/index');

Route::add('login', 'login/index', true);

Route::add('logout', 'login/loginOut');

Route::add('login/verify', 'login/verify', true);

Route::add('home', 'index/home');

Route::add('member/list', 'member/userList');

Route::add('system', 'system/index');

Route::add('access/resetPassword', 'access/manager/resetPassword');
Route::add('admin/delCache', 'admin/delCache');

/* 文件上传 */
Route::add('file/upload', 'file/upload');

/* 网站配置 */
Route::add('system/setConfig', 'system/setConfig');

/* 保存网站配置 */
Route::add('system/saveAllConfig', 'system/saveAllConfig');

/* 配置列表 */
Route::add('system/configlist', 'system/configlist');

/* 新增配置 */
Route::add('system/addConfig', 'system/addConfig');

/* 修改配置 */
Route::add('system/editConfig', 'system/editConfig');

/* 用户管理 */
Route::add('member/group_auth', 'member/groupAuth');

/* 权限管理 */
Route::add('access', 'access/index');

/* 管理列表 */
Route::add('access/manager/list', 'access/manager/index');

/* 添加管理 */
Route::add('access/manager/add', 'access/manager/add');

/* 编辑管理 */
Route::add('access/manager/edit', 'access/manager/edit');

/* 删除管理 */
Route::add('access/manager/delete', 'access/manager/delete');


/* 角色管理 */
Route::add('access/rolelist', 'access/roleList');

/* 新增角色 */
Route::add('access/addRole', 'access/addRole');

/* 角色授权 */
Route::add('access/authRole', 'access/authRole');

/* 角色[禁用 / 启用] */
Route::add('access/disabledRole', 'access/disabledRole');

/* 角色删除 */
Route::add('access/delRole', 'access/delRole');

/* 节点列表 */
Route::add('access/nodeList', 'access/nodeList');

/* 新增节点 */
Route::add('access/add', 'access/add');

/* 编辑节点 */
Route::add('access/edit', 'access/edit');

/* 节点[启用 / 禁用] */
Route::add('access/setStatus', 'access/setStatus');
