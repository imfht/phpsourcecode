## Laravel 5 Page Cache Use Middleware

Add page cache with route.

The cache is best not to write in the program logic inside, to find a cache is very tired, I suggest that the cache in the routing

缓存放在路由里面, 使用方法如下：

````
    Route::group(['middleware' => 'cache:10'], function(){
        Route::get('/', 'HomeController@index');
    });
````

###Installation

Add to composer.json

```php
"rose1988c/laravel-routecache-middleware":"dev-master"

or

composer require rose1988c/laravel-routecache-middleware:dev-master

```

Register the service provider by adding in the provider section in config/app.php

````
    'providers' => [
        ...
        Rose1988c\RouteCache\RouteCacheServiceProvider::class
        ...
````

Just in case

````
    composer dump-autoload
````

Publish the migration and the config file

````
    php artisan vendor:publish
````

Add to app\Http\Kernel.php

````
    'cache' => \Rose1988c\RouteCache\CacheMiddleWare::class,
    'flush' => \Rose1988c\RouteCache\FlushMiddleWare::class,
````

Setting Route.php

````
    // set cache lifetime 10
    Route::group(['middleware' => 'cache:10'], function(){
        Route::get('/', 'DemoController@index');
    });
````

Flush Cache

* flush        -> Flush Current Request Url
* flush:ref    -> Flush Referer Url, Often used in AJAX
* flush:url    -> Flush Appoint Url, Often used in Manage And Clean Appoint Url, Add arg `?flushurl=http://xxxxx`

````
    Route::group(['middleware' => 'flush'], function(){
        Route::any('switchP', 'HomeController@switchP');
    });

    Route::group(['middleware' => 'flush:ref'], function(){
        Route::any('switchP', 'HomeController@switchP');
    });

    // test url: http://192.168.141.129:8084/cleanCache?flushurl=http://192.168.141.129:8084/wealthbalance
    // result  : ok
    Route::group(['middleware' => 'flush:url'], function(){
        Route::any('cleanCache', function(){
            echo 'hello, world!';
        });
    });

````