#来自Thinkphp的调试工具条，还是原来的配方，还是熟悉的味道

**效果图**
![输入图片说明](http://git.oschina.net/uploads/images/2016/0812/232537_ae9a048e_24703.jpeg "效果图1")
![输入图片说明](http://git.oschina.net/uploads/images/2016/0812/232622_ab876ffa_24703.jpeg "效果图2")




**使用前**
- 该包适用于Laravel5
- 适用于对热爱Thinkphp调试工具的朋友
- 经过测试本包适用于laravel5.1版本，5.2版本暂不支持


**安装**
```
composer require cheer/trace-from-thinkphp
```

**使用**
编辑app/config/app.php
在app.php的providers节点下增加以下代码
```
Cheer\TpTrace\TpTraceServiceProvider::class,
```

然后执行下发布将包中的配置文件复制到项目中
```
php artisan vendor:publish
```

将会生成该配置文件，打开编辑app/config/thinkphp_trace.php
默认show_page_trace为true表示开启trace工具条，false则为关闭工具条。


**说明**
支持trace函数，和Thinkphp一样，
```
trace('debug info!');
```
通过如上代码可以将信息添加到laravel的日志中去，并在工具条中显示出来。


然后就可以开心的使用这个工具条啦！

**使用注意**
在laravel版本5.1.43 (LTS)以上可能就不能开心的使用了
因为在vendor/symfony/http-foundation/Response.php 366-384行中
```
    /**
     * Sends HTTP headers and content.
     *
     * @return Response
     */
    public function send()
    {
        $this->sendHeaders();
        $this->sendContent();

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        } elseif ('cli' !== PHP_SAPI) {
            static::closeOutputBuffers(0, true);
        }

        return $this;
    }
```
受到fastcgi_finish_request()这个函数的影响，程序结束后执行的代码将不会显示到页面上，这在生产环境中是比较好的,
因此我们如果要使用这个小工具就得将fastcgi_finish_request()这一行代码注释下即可，记得不要发到生产环境上去咯。

