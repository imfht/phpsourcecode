<?php


/**       ==========================          基本APi           ====================   */
Route::namespace('Api')->group(function () {

    Route::post('login', 'LoginController@login');
    Route::post('logout', 'LoginController@logout');
    Route::post('refreshtoken', 'LoginController@refreshToken');

    // 多表登录测试
    /*
    Route::post('admin_user/login', 'LoginController@adminUserLogin');
    Route::get('admin_user', 'AdminUsersController@index');
    Route::post('admin_user/logout', 'LoginController@adminUserLogout');
    */


    Route::post('common_switch_enable', 'CommonController@switchEnable');
    Route::post('common_switch_talbe_status', 'CommonController@switchTableStatus')->name('common.switch_table_status');
    Route::get('common_get_table_status/{table_name}/{column_name?}', 'CommonController@getTableStatus');
    Route::get('common_get_system_config/{search_data}', 'CommonController@getSystemConfig')->name('common.get_system_config');

    Route::get('excels/export/advertisement_positions', 'ExcelController@exportAdvertisementPosition')->name('excel.export_advertisement_positions');
    Route::post('excels/import/advertisement_positions', 'ExcelController@importExcelAdvertisementPosition')->name('excel.import_advertisement_positions');

});



Route::post('send_sms', 'Api\ThirdController@sendSms')->name('third.send_sms');
Route::post('check_sms_code', 'Api\ThirdController@checkSmsCode')->name('third.check_sms_code');

/**       ==========================          后台APi           ====================   */
Route::namespace('Admin')->group(function () {

    /**       ==========================          自带 Api           ====================   */

    Route::get('admin/users', 'UserController@usersList')->name('users.list');
    Route::get('admin/users/current_user', 'UserController@currentUser')->name('users.current_user');
    Route::get('admin/users/{user}', 'UserController@show')->name('users.show');
    Route::post('admin/users', 'UserController@store')->name('users.store');
    Route::patch('admin/users/{user}', 'UserController@update')->name('users.update');
    Route::get('admin/users/{user}/roles', 'UserController@getUserRoles')->name('users.get_user_roles');
    Route::post('admin/give/{user}/roles', 'UserController@giveUserRoles')->name('users.give_user_roles');
    Route::delete('admin/users/{user}', 'UserController@destroy')->name('users.destroy');

    Route::get('admin/permissions', 'PermissionsController@permissionList')->name('permissions.list');
    Route::post('admin/permissions', 'PermissionsController@addEdit')->name('permissions.add_edit');
    Route::get('admin/all_permissions', 'PermissionsController@allPermissions')->name('permissions.all');
    Route::get('admin/permissions/{permission}', 'PermissionsController@show')->name('permissions.show');
    Route::delete('admin/permissions/{permission}', 'PermissionsController@destroy')->name('permissions.destroy');

    Route::get('admin/roles', 'RolesController@roleList')->name('roles.list');
    Route::get('admin/all_roles', 'RolesController@allRoles')->name('roles.all');
    Route::post('admin/roles', 'RolesController@addEdit')->name('roles.add_edit');
    Route::get('admin/roles/{role}', 'RolesController@show')->name('roles.show');
    Route::get('admin/roles/{role}/permissions', 'RolesController@getRolePermissions')->name('roles.get_role_permissions');
    Route::post('admin/give/{role}/permissions', 'RolesController@giveRolePermissions')->name('roles.give_role_permissions');
    Route::delete('admin/roles/{role}', 'RolesController@destroy')->name('roles.destroy');


    Route::get('admin/attachments', 'AttachmentsController@attachmentList')->name('attachments.list');
    Route::delete('admin/attachments/{attachment}/force_destroy', 'AttachmentsController@forceDestroy')->name('attachments.force_destroy');
    Route::delete('admin/attachments/{attachment}', 'AttachmentsController@destroy')->name('attachments.destroy');

    Route::get('admin/advertisement_positions', 'AdvertisementPositionsController@advertisementPositionList')->name('advertisement_positions.list');
    Route::get('admin/advertisement_positions/all', 'AdvertisementPositionsController@allAdvertisementPositions')->name('advertisement_positions.all');
    Route::get('admin/advertisement_positions/{advertisement_position}', 'AdvertisementPositionsController@show')->name('advertisement_positions.show');
    Route::post('admin/advertisement_positions', 'AdvertisementPositionsController@addEdit')->name('advertisement_positions.add_edit');
    Route::delete('admin/advertisement_positions/{advertisement_position}', 'AdvertisementPositionsController@destroy')->name('advertisement_positions.destroy');


    Route::get('admin/advertisements', 'AdvertisementsController@advertisementList')->name('advertisements.list');
    Route::get('admin/advertisements/{advertisement}', 'AdvertisementsController@show')->name('advertisements.show');
    Route::post('admin/advertisements', 'AdvertisementsController@store')->name('advertisements.store');
    Route::patch('admin/advertisements/{advertisement}', 'AdvertisementsController@update')->name('advertisements.update');
    Route::delete('admin/advertisements/{advertisement}', 'AdvertisementsController@destroy')->name('advertisements.destroy');


    Route::get('admin/categories', 'CategoriesController@categoryList')->name('categories.list');
    Route::post('admin/categories', 'CategoriesController@addEditCategory')->name('categories.add_edit');
    Route::get('admin/categories/all', 'CategoriesController@allCategories')->name('categories.all');
    Route::get('admin/categories/{category}', 'CategoriesController@show')->name('categories.show');
    Route::delete('admin/categories/{category}', 'CategoriesController@destroy')->name('categories.destroy');


    Route::get('admin/tags', 'TagsController@tagList')->name('tags.list');
    Route::get('admin/tags/{tag}', 'TagsController@show')->name('tags.show');
    Route::post('admin/tags', 'TagsController@addEditTag')->name('tags.add_edit');
    Route::delete('admin/tags/{tag}', 'TagsController@destroy')->name('tags.destroy');


    Route::get('admin/articles', 'ArticlesController@articleList')->name('articles.list');
    Route::get('admin/articles/{article}', 'ArticlesController@show')->name('articles.show');
    Route::post('admin/articles', 'ArticlesController@store')->name('articles.store');
    Route::patch('admin/articles/{article}', 'ArticlesController@update')->name('articles.update');
    Route::delete('admin/articles/{article}', 'ArticlesController@destroy')->name('articles.destroy');


    Route::get('admin/logs', 'LogsController@logList')->name('logs.list');


    Route::get('admin/ip_filters', 'IpFiltersController@ipFilterList')->name('ip_filters.list');
    Route::get('admin/ip_filters/{ip_filter}', 'IpFiltersController@show')->name('ip_filters.show');
    Route::post('admin/ip_filters', 'IpFiltersController@addEditIpFilter')->name('ip_filters.add_edit');
    Route::delete('admin/ip_filters/{ip_filter}', 'IpFiltersController@destroy')->name('ip_filters.destroy');


    Route::get('admin/versions', 'VersionsController@list');
    Route::post('admin/versions', 'VersionsController@store');

    Route::get('admin/system_configs', 'SystemConfigsController@list')->name('system_configs.list');
    Route::get('admin/system_configs/get_group', 'SystemConfigsController@getGroup')->name('system_configs.get_group');
    Route::get('admin/system_configs/{system_config}', 'SystemConfigsController@show')->name('system_configs.show');
    Route::post('admin/system_configs', 'SystemConfigsController@store')->name('system_configs.store');
    Route::patch('admin/system_configs/{system_config}', 'SystemConfigsController@update')->name('system_configs.update');
    Route::delete('admin/system_configs/{system_config}', 'SystemConfigsController@destroy')->name('system_configs.destroy');


    Route::get('admin/news/carousels', 'NewsController@carousels')->name('news.carousels');
    Route::get('admin/news/carousels/{id}', 'NewsController@showCarousels')->name('news.show_carousels');
    Route::post('admin/news/carousels', 'NewsController@storeCarousel')->name('news.store_carousel');
    Route::patch('admin/news/carousels/{id}', 'NewsController@updateCarousel')->name('news.update_carousel');
    Route::delete('admin/news/carousels/{id}', 'NewsController@destroyCarousel')->name('news.destroy_carousel');

    Route::get('admin/admin_messages', 'AdminMessagesController@list')->name('admin_messages.list');
    Route::post('admin/admin_messages/read_messages', 'AdminMessagesController@readMessages')->name('admin_messages.reade_messages');
    Route::delete('admin/admin_messages/{admin_message}', 'AdminMessagesController@destroy')->name('admin_messages.destroy');
    Route::delete('admin/admin_messages/{admin_message_ids}/many', 'AdminMessagesController@destroyMany')->name('admin_messages.destroy_many');


    Route::get('admin/api_messages', 'ApiMessagesController@list')->name('api_messages.list');
    Route::get('admin/api_messages/user_search/{phone}', 'ApiMessagesController@userSearch')->name('api_messages.user_search');
    Route::post('admin/api_messages', 'ApiMessagesController@store')->name('api_messages.store');


    Route::get('admin/app_versions', 'AppVersionsController@list')->name('app_versions.list');
    Route::get('admin/app_versions/{app_version}', 'AppVersionsController@show')->name('app_versions.show');
    Route::post('admin/app_versions', 'AppVersionsController@store')->name('app_versions.store');
    Route::patch('admin/app_versions/{app_version}', 'AppVersionsController@update')->name('app_versions.update');
    Route::delete('admin/app_versions/{app_version}', 'AppVersionsController@destroy')->name('app_versions.destroy');


    Route::get('admin/statistics', 'StatisticsController@base')->name('statistics.base');

});


Route::namespace('Admin')->group(function () {
    /**       ==========================          自定义 Api           ====================   */

});



