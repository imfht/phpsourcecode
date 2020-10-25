# OneFox

## 简介

OneFox是一个简洁的PHP框架(**PHP版本要求5.4+**)，使用非常方便，简单阅读使用手册即可快速开发自己的网站了。而且OneFox具备以下优点：
* 框架核心不臃肿，加载速度快 
* 更适合api之类的接口业务 
* 模板不依赖模板引擎，减少学习模板语言的成本 
* 核心代码简洁，可根据业务需要调整 

## 安装和配置

### 安装
```
$ git clone https://github.com/zer0131/OneFox.git /home/project
```
当然，你也可以自定义其他克隆目录

### nginx配置示例
```
server {
    listen  80;
    server_name  www.appryan.com appryan.com;
    index index.php index.html index.html;
    root /home/project/app/public;
    location / {
        try_files $uri $uri/ /index.php?$args;
    }
    location ~ .*\.(php|php5)?$ {
        fastcgi_pass  127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi.conf;
    }
    #图片缓存时间设置
    location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$ {
        #expires 30d;
    }
    #JS和CSS缓存时间设置
    location ~ .*\.(js|css)?$ {
        #expires 1h;
    }
    access_log  /usr/local/nginx/logs/OneFox.log;
}
```
若要添加301重定向，则添加下面配置
```
if ($host != 'www.appryan.com') {
    rewrite ^/(.*)$ http://www.appryan.com/$1 permanent;
}
```

### apache配置示例
```
<VirtualHost 80>
    DocumentRoot "/home/project/app/public"
    ServerName www.appryan.com
    ServerAlias www.appryan.com
    ErrorLog "logs/OneFox.error.log"
    CustomLog "logs/OneFox.access.log" common
</VirtualHost>
```

.htaccess配置
```
<IfModule mod_rewrite.c>
    Options +FollowSymlinks
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(.*)$ index.php?/$1 [QSA,PT,L]
</IfModule>
```

>  注意：注释的部分可根据实际情况修改

## 目录结构
```
project  WEB部署目录（或者子目录） 
├─LICENSE         LICENSE文件
├─README.md       README文件 
├─app             应用目录 
├─extend          扩展类库目录
└─onefox          框架目录
```

应用目录结构
```
├─app 
│  ├─actions        action目录
│  ├─cache        缓存目录
│  ├─config       配置目录
│  ├─controller   controller目录
│  │  ├─index     模块目录(如果开启)
│  ├─lib          应用类库目录
│  ├─model        model目录
│  ├─tpl          模板目录
│  │  ├─comm      公共模板
│  │  ├─index     模块目录(如果开启)
│  │  │  ├─index  控制器目录
│  ├─logs         日志目录
│  ├─daemon       守护进程目录
│  └─public       入口目录，可存放资源文件等
```

框架目录结构
```
├─onefox
│  ├─caches             缓存类目录
│  ├─tpl                系统模板目录
│  ├─C.php              公共函数文件
│  ├─Cache.php          缓存抽象文件
│  ├─Config.php         配置类文件
│  ├─Controller.php     抽象控制器文件
│  ├─DB.php             数据库范文类文件
│  ├─Dispatcher.php     路由解析类文件
│  ├─Log.php            日志类文件
│  ├─Model.php          基础Model类文件
│  ├─Request.php        请求类文件
│  ├─Response.php       响应类文件
│  ├─View.php           视图解析类文件
│  ├─functions.php      常用函数文件
│  └─OneFox.php         框架入口文件
```

## 使用规范

1、模块目录需小写, 并且控制器文件名首字母大写

2、扩展库中类名使用驼峰命名法，并且文件名应与类名相同，如：MyClass

3、使用命名空间，并且前缀应与对应的目录名称相同，如：命名空间为lib\MySpace\MyClass，则文件名lib/MySpace/MyClass

4、模板目录中控制器目录的首字母需小写

## 关于composer

框架自动识别composer中的vendor目录, 请在composer.json中引入你要使用的类库。

安装请使用下面命令

```
curl -sS https://getcomposer.org/installer | php
```

或

```
php -r "readfile('https://getcomposer.org/installer');" | php
```

[composer具体用法](http://docs.phpcomposer.com/)

## 详细文档

[http://www.zhangenrui.com/category/#onefox](http://www.zhangenrui.com/category/#onefox)
