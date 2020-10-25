# 如何请求接口服务

## HTTP协议下的请求方式

对于PhalApi，默认是通过HTTP协议进行通信的。根据接口服务的具体实现，可以使用GET或POST方式请求。  

## 访问入口

如前面所言，PhalApi推荐将系统对外可访问的根目录设置为/path/to/phalapi/public。PhalApi的统一访问入口文件是/path/to/phalapi/public/index.php文件。  

当配置的域名为：dev.phalapi.net，并且已将根目录设置到public，此时访问的URL是：  
```
http://dev.phalapi.net
```

当未配置域名，亦未配置根目录时，此时访问的URL是（显然更长更不优雅）：  
```
http://localhost/phalapi/public/index.php
```

如果尚未安装，请先阅读[下载与安装](http://docs.phalapi.net/#/v2.0/download-and-setup)。  

## 如何指定待请求的接口服务？

默认情况下，可以通过s参数指定待请求的接口服务，当s未传时，缺省使用默认接口服务，即：App.Site.Index。以下三种方式是等效的，都是请求默认接口服务。  

 + 未传s参数
 + ?s=Site.Index，省略命名空间，默认使用App
 + ?s=App.Site.Index，带有命名空间前缀  

 也就是说，当请求除默认接口服务以外的接口服务时，其格式可以二选一：  

 + ?s=Class.Action
 + 或者：?s=Namespace.Class.Action

其中，Namespace表示命名空间前缀，Class为接口服务类名，Action为接口服务方法名，这三者通常首字母大写，并使用英文点号分割。最终执行的类方法是：Namespace/Api/Class::Action()。  

> 温馨提示：s参数为service参数的缩写，即使用```?s=Class.Action```等效于```?service=Class.Action```，两者都存在时优先使用service参数。

需要注意的是：如果Api内有多级目录，则Class类名及目录之间使用下划线连接，并且类名中不能出现下划线。例如对于接口文件Namespace/Api/Folder/Class::Action()对应的接口服务名称是：?s=Namespace.Folder_Class.Action。  

> 温馨提示：接口有多级目录时，使用下划线连接目录和类名。  

### 关于Namespace命名空间

Namespace是指命名空间中```/Api/```的前半部分。并且需要在根目录下的composer.json文件中进行autoload的注册，以便能正常自动加载类文件。如默认已经注册的App命名空间：  
```
{
    "autoload": {
        "psr-4": {
            "App\\": "src/app"
        }
    }
}
```
当命名空间存在子命名空间时，在请求时使用下划线分割。反过来，当不存在多级命名空间时，命名空间不应该含有下划线。 

### 关于Class接口服务类名

Class接口服务类名是指命名空间中```/Api/```的后半部分，并且必须是[PhalApi/Api](https://github.com/phalapi/kernal/blob/master/src/Api.php)的子类。当命名空间存在子命名空间时，在请求时同样改用下划线分割。类似的，当不存在多级命名空间时，命名空间不应该含有下划线。  

### 关于Action接口服务方法名

待请求的Action，应该是public访问级别的类方法，并且不能是[PhalApi/Api](https://github.com/phalapi/kernal/blob/master/src/Api.php)已经存在的方法。

### 一些示例

以下是一些综合的示例。  

PhalApi 2.x 请求的s参数|对应的文件|执行的类方法
---|---|---
无|./src/app/Api/Site.php|App\Api\Site::Index()
?s=Site.Index|./src/app/Api/Site.php|App\Api\Site::index()
?s=Weibo.Login|./src/app/Api/Weibo.php|App\Api\Weibo::login()
?s=User.Weibo.Login|./src/user/Api/Weibo.php|User\Api\Weibo::login()
?s=Company_User.Third_Weibo.Login|./src/company_user/Api/Third/Weibo.php|Company\User\Api\Third\Weibo::login()

上面示例中假设，已经在composer.json中配置有：  
```
{
    "autoload": {
        "psr-4": {
            "App\\": "src/app",
            "User\\": "src/user",
            "Company\\User\\": "src/company_user"
        }
    }
}
```

## 开启URI路由匹配

> 注意！本功能需要PhalApi 2.7.0 及以上版本方可支持。

任何情况下，PhalApi都会优先通过service参数，其次是s参数（也就是service的短参数）来定位当前客户端请求的是哪一个接口服务。  

当客户端未提供service参数，亦未提供s参数时，可以通过开启```sys.enable_uri_match```尝试进行URI路由匹配。

先通过几个例子来了解开启URI路由匹配后的访问效果，以下效果是等效的。

```
# 通过service指定
http://dev.phalapi.net/?service=App.Usre.Login

# 开启URI路由匹配后
http://dev.phalapi.net/App/User/Login

# 省略App默认命名空间
http://dev.phalapi.net?s=App.Usre.Login

# 开启URI路由匹配后
http://dev.phalapi.net/User/Login

```

原理很简单，当未提供service参数和s参数时，并且是开启```sys.enable_uri_match```后，客户端可以通过```/Namespace/Class/Action```这样的URI访问接口服务。

除了要在./config/sys.php修改enable_uri_match配置为true外，还需要同步进行Rewrite规则配置，以便让你的服务在未找到文件时把请求转发给index.php处理。参考以下Nginx配置：

```
server {
    listen 80;
    server_name dev.phalapi.net;
    root /path/to/phalapi/public;
    charset utf-8;

    # 开启URI路由匹配
    location / {
        try_files $uri $uri/ $uri/index.php;
    }
    if (!-e $request_filename) {
        rewrite ^/(.*)$ /index.php last;
    }

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    access_log logs/dev.phalapi.net.access.log;
    error_log logs/dev.phalapi.net.error.log;
}

```

### 路由如何匹配？

开启路由匹配，并且正确配置Nginx或Apache的Rewrite规则后，客户端可以通过以下方式访问接口服务：

 + 常见的路径：```/Namespace/Class/Action```
 + 常见的路径，且带有GET参数：```/Namespace/Class/Action?xx=123```
 + 常见的路径，且前面包含index.php文件：```/index.php/Namespace/Class/Action```
 + 常见的路径，且同时包含index.php文件和GET参数：```/public/index.php/Namespace/Class/Action?xx=123```

类似地，如果Namespace是App，那么可以忽略不写，即有：

 + 默认App，常见的路径：```/Class/Action```
 + 默认App，常见的路径，且带有GET参数：```/Class/Action?xx=123```
 + 默认App，常见的路径，且前面包含index.php文件：```/index.php/Class/Action```
 + 默认App，常见的路径，且同时包含index.php文件和GET参数：```/public/index.php/Class/Action?xx=123```

下面是针对登录接口的例子：

```
// 常见的路径
http://dev.phalapi.net/App/User/Login

// 常见的路径，且带有GET参数
http://dev.phalapi.net/App/User/Login?username=dogstar&password=123456

// 常见的路径，且前面包含index.php文件
http://dev.phalapi.net/index.php/App/User/Login

// 常见的路径，且同时包含index.php文件和GET参数（入口文件必须是index.php，前面目录路径可自定义）
http://dev.phalapi.net/public/index.php/App/User/Login?username=dogstar&password=123456
```

## 扩展：如何定制接口服务的传递方式？

虽然我们约定统一使用```?s=Namespace.Class.Action```的格式来传递接口服务名称，但如果项目有需要，也可以采用其他方式来传递。例如类似于Yii框架的请求格式：```?r=Namespace/Class/Action```。  

如果需要定制传递接口服务名称的方式，可以重写[PhalApi\Request::getService()](https://github.com/phalapi/kernal/blob/master/src/Request.php)方法。以下是针对改用斜杠分割，并换用r参数名字的实现代码片段。  
```php
// 文件 ./src/app/Common/Request.php

<?php
namespace App\Common;

class Request extends \PhalApi\Request {

    public function getService() {
        // 优先返回自定义格式的接口服务名称
        $service = $this->get('r');
        if (!empty($service)) {
            $namespace = count(explode('/', $service)) == 2 ? 'App.' : '';
            return $namespace . str_replace('/', '.', $service);
        }

        return parent::getService();
    }
}
```

实现好自定义的请求类后，需要在项目的DI配置文件[./config/di.php](https://github.com/phalapi/phalapi/blob/master/config/di.php)进行注册。在最后的加上一行：  
```php
$di->request = new App\Common\Request();
```

这时，便可以通过新的方式来进行接口服务的请求的了。即：  

原来的方式|现在的方式
---|---
?s=Site.Index|?r=Site/Index   
?s=App.Site.Index|?r=App/Site/Index   
?s=Hello.World|?r=Hello/World  
?s=App.Hello.World|?r=App/Hello/World 


这里有几个注意事项： 

 + 1、重写后的方法需要转换为原始的接口服务格式，即：Namespace.Class.Action，注意别遗漏命名空间。   
 + 2、为保持兼容性，在取不到自定义的接口服务名称参数时，应该返回```parent::getService()```。  


如果想再进行URL路由的美化，可以结合重定向配置来使用。  

例如Nginx参考配置：  

```
if (!-e $request_filename) {
    rewrite ^/(.*)$ /index.php?r=$1 last;
}
```

例如Apache参考配置：  
```
<IfModule mod_rewrite.c>
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ index.php/?r=$1 [QSA,PT,L]
SetEnvIf Authorization "(.*)" HTTP_AUTHORIZATION=$1
</IfModule>
```

又如IIS参考配置：  
```
<?xml version="1.0" encoding="utf-8"?>
<configuration>
    <system.webServer>
        <rewrite>
            <rules>
                <rule name="Imported Rule 1" stopProcessing="true">
                    <match url="^(.*)$" ignoreCase="false" />
                    <conditions>
                        <add input="{REQUEST_FILENAME}" matchType="IsDirectory" ignoreCase="false" negate="true" />
                        <add input="{REQUEST_FILENAME}" matchType="IsFile" ignoreCase="false" negate="true" />
                    </conditions>
                    <action type="Rewrite" url="index.php/?r={R:1}" appendQueryString="true" />
                </rule>
            </rules>
        </rewrite>
    </system.webServer>
</configuration>
```

最终效果会类似，当访问：http://api.phalapi.net/user/login，就会变成：http://api.phalapi.net/?r=user/login，然后触发上在的扩展规则，最终等效于：http://api.phalapi.net/?s=user.login  
  
是不是觉得很好玩？可以立马亲自尝试一下哦。定制你最喜欢的请求方式。
