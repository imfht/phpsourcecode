# Laravel Bigfile Upload

## 说明
结合 `Webuploader` 切片上传文件功能实现，网站在处理大文件上传缓慢，或者不能上传的功能。

## Laravel版本
`>=5.5`

## 安装

```bash
composer require aurora/big-file-upload

php artisan vendor:publish --provider='AuroraLZDF\Bigfile\BigfileServiceProvider'
```

## 配置项

执行完 `php artisan vendor:publish --provider='AuroraLZDF\Bigfile\BigfileServiceProvider'`，会在 `config` 下面生成 `bigfile.php` 配置文件。配置项说明：

```php
<?php

return [
    /*
     |------------------------
     |     文件每次切片尺寸
     |------------------------
     */
    'chunk_size' => 1024 * 1024 * 2,


    /*
     |------------------------
     |     允许上传文件最大尺寸
     |------------------------
     */
    'max_size' => 1024 * 1024 * 1024,


    /*
     |------------------------
     |     文件保存路径
     |------------------------
     */
    'save_path' => 'upload/' . date('Y') . '/' . date('m') . '/',


    /*
     |------------------------
     |     文件切片缓存路径
     |------------------------
     */
    'tmp_path' => storage_path('app/public/tmp'),


    /*
     |------------------------
     |     允许上传文件类型
     |------------------------
     */
    'allow_type' => ['jpg', 'jpeg', 'gif', 'png', 'mp4', 'mp3', 'zip', 'apk', 'pdf', 'rar'],


    /*
     |------------------------
     |     切片文件是否随机命名
     |------------------------
     */
    'rand_name' => true,


    /*
     |------------------------
     |     是否删除临时文件
     |------------------------
     */
    'remove_tmp_file' => true,
];
```

## 访问路由
```php
Route::middleware('web')->get('/upload/bigfile', '\AuroraLZDF\Bigfile\Controllers\BigfileController@loadView')->name('bigfile_view');

// bindings:不限制API访问次数限制，不需要 csrf_token 验证
Route::middleware('bindings')->post('/upload/bigfile', '\AuroraLZDF\Bigfile\Controllers\BigfileController@upload')->name('bigfile_upload');
```

...



