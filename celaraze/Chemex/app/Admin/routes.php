<?php

use Dcat\Admin\Admin;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Admin::routes();

Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {
    $router->get('/', 'HomeController@index');
    $router->get('/update', 'UpdateController@index');
    $router->get('/test', 'HomeController@test');
    $router->resource('/device/tracks', 'DeviceTrackController');
    $router->resource('/device/records', 'DeviceRecordController', ['names' => [
        'index' => 'device.records.index'
    ]]);
    $router->resource('/device/categories', 'DeviceCategoryController');
    $router->resource('/software/tracks', 'SoftwareTrackController', ['names' => [
        'index' => 'software.tracks.index'
    ]]);
    $router->resource('/software/records', 'SoftwareRecordController', ['names' => [
        'index' => 'software.records.index'
    ]]);
    $router->resource('/software/categories', 'SoftwareCategoryController');
    $router->resource('/hardware/tracks', 'HardwareTrackController');
    $router->resource('/hardware/records', 'HardwareRecordController', ['names' => [
        'index' => 'hardware.records.index'
    ]]);
    $router->resource('/hardware/categories', 'HardwareCategoryController');
    $router->resource('/vendor/records', 'VendorRecordController');
    $router->resource('/staff/records', 'StaffRecordController', ['names' => [
        'index' => 'staff.records.index'
    ]]);
    $router->resource('/staff/departments', 'StaffDepartmentController');
    $router->resource('/check/records', 'CheckRecordController');
    $router->resource('/check/tracks', 'CheckTrackController');
    $router->resource('/service/records', 'ServiceRecordController', ['names' => [
        'index' => 'service.records.index'
    ]]);
    $router->resource('/service/tracks', 'ServiceTrackController');
    $router->resource('/service/issues', 'ServiceIssueController', ['names' => [
        'index' => 'service.issues.index'
    ]]);
    $router->resource('/maintenance/records', 'MaintenanceRecordController');
});
