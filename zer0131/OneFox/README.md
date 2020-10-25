# OneFox

## Introduction

![build](https://img.shields.io/badge/build-passing-green.svg) ![PHP](https://img.shields.io/badge/PHP-5.4%2B-brightgreen.svg) ![version](https://img.shields.io/badge/version-2.2.3-red.svg) ![license](https://img.shields.io/badge/license-Apache%202-blue.svg) ![download](https://img.shields.io/badge/download-120KB-yellow.svg) ![author](https://img.shields.io/badge/author-ryan-orange.svg)

[[Simplified Chinese]](https://github.com/zer0131/OneFox/blob/master/zh.md)

OneFox is a simple php framework(**require php5.4+**). It's very convenient to use. Simple reading manual can quickly develop their own web site. Advantages are as follows:
* The frame core is not bulky, and the loading speed is fast.
* More suitable for API and other interface services.
* Templates don't rely on template engines, reducing the cost of learning template languages.
* The core code is concise and can be adjusted according to business needs.

## Install and Configure

### Install
```
$ git clone https://github.com/zer0131/OneFox.git /home/project
```
Of course, you can also customize other clone directories.

### Nginx
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
    location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$ {
        #expires 30d;
    }
    location ~ .*\.(js|css)?$ {
        #expires 1h;
    }
    access_log  /usr/local/nginx/logs/OneFox.log;
}
```
301 redirect.
```
if ($host != 'www.appryan.com') {
    rewrite ^/(.*)$ http://www.appryan.com/$1 permanent;
}
```

### Apache
```
<VirtualHost 80>
    DocumentRoot "/home/project/app/public"
    ServerName www.appryan.com
    ServerAlias www.appryan.com
    ErrorLog "logs/OneFox.error.log"
    CustomLog "logs/OneFox.access.log" common
</VirtualHost>
```

.htaccess
```
<IfModule mod_rewrite.c>
    Options +FollowSymlinks
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(.*)$ index.php?/$1 [QSA,PT,L]
</IfModule>
```

>  Tip: The part of the annotation can be modified according to the actual situation

## Directory
```
project           Project dir 
├─LICENSE         LICENSE
├─README.md       README 
├─app             App Dir 
├─extend          Extended class library
└─onefox          Framework dir
```

App Directory
```
├─app 
│  ├─actions       
│  ├─cache          
│  ├─config      
│  ├─controller  
│  │  ├─index     
│  ├─lib          
│  ├─model        
│  ├─tpl          
│  │  ├─comm      
│  │  ├─index    
│  │  │  ├─index  
│  ├─logs         
│  ├─daemon       
│  └─public      
```

Framework Directory
```
├─onefox
│  ├─caches             Caches
│  ├─tpl                Sys Tpl
│  ├─C.php              Static function
│  ├─Cache.php          Abstract cache class
│  ├─Config.php         Config class
│  ├─Controller.php     Abstract controller class
│  ├─DB.php             Database class
│  ├─Dispatcher.php     Routing class
│  ├─Log.php            Log class
│  ├─Model.php          Basic Model
│  ├─Request.php        Request class
│  ├─Response.php       Response class
│  ├─View.php           View class
│  ├─functions.php      Common functions
│  └─OneFox.php         Entry class
```

## Development specification

1、The module directory needs to be lowercase, and the controller file name is uppercase

2、The class name in the extension library uses the hump naming method, and the file name should be the same as the class name, such as: MyClass

3、Using namespaces, and the prefix should be the same as the directory name, such as namespace lib\MySpace\MyClass, file name lib/MySpace/MyClass

4、The initial letter of the template controller should be lowercase

## Composer

The framework automatically identifies the vendor directory in composer. Please introduce the class library you want to use in composer.json.

Install

```
curl -sS https://getcomposer.org/installer | php
```

Or

```
php -r "readfile('https://getcomposer.org/installer');" | php
```

[Composer Usage](http://docs.phpcomposer.com/)

## Document

[http://www.zhangenrui.com/category/#onefox](http://www.zhangenrui.com/category/#onefox)
