# 下载与安装

PhalApi 2.x 与PhalApi 1.x 系列一样，要求PHP >= 5.3.3。

## 快速安装

PhalApi 2.x 版本的安装很简单，有两种方式。

### composer一键安装

#### 安装Composer

如果还没有安装 Composer，你可以按 getcomposer.org 中的方法安装。 在 Linux 和 Mac OS X 中可以运行如下命令：

```bash
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
```

> 温馨提示：关于composer的使用，请参考[Composer 中文网 / Packagist 中国全量镜像](http://www.phpcomposer.com/)。

#### 安装PhalApi 2.x

使用composer创建项目的命令，可实现一键安装。

```bash
$ composer create-project phalapi/phalapi
```

### 手动下载安装

或者，也可以进行手动安装。首先下载[phalapi](https://github.com/phalapi/phalapi/tree/master-2x)项目**master-2x分支**源代码。下载解压后，进行可选的composer更新，即：  
```bash
$ composer update
```

> 温馨提示：为提高友好度，phalapi中已带有缺省vendor安装包，从而减轻未曾接触过composer开发同学的学习成本。即便composer安装失败，也可正常运行PhalApi 2.x。  

## 配置

### Nginx配置

如果使用的是Nginx，可参考以下配置。  
```
server {
    listen 80;
    server_name dev.phalapi.net;
    root /path/to/phalapi/public;
    charset utf-8;

    location / {
        index index.php;
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


重启Nginx并配置本地HOSTS后，可通过以下链接，访问默认接口服务。  
```
http://dev.phalapi.net
```

> 温馨提示：推荐将访问根路径指向/path/to/phalapi/public。后续开发文档中，如无特殊说明，均约定采用此配置方式。

### Apache配置

如果使用的是Apache，可参考以下配置。目录结构：  
```
htdocs
├── phalapi
└── .htaccess
```

.htaccess内容：  
```
<IfModule mod_rewrite.c>
    RewriteEngine on
    RewriteBase /

    RewriteCond %{HTTP_HOST} ^dev.phalapi.net$

    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d

    RewriteCond %{REQUEST_URI} !^/phalapi/public/
    RewriteRule ^(.*)$ /phalapi/public/$1
    RewriteRule ^(/)?$ index.php [L]
</IfModule>
```

### XAMPP配置

如果使用的是XAMPP集成环境，只需要将项目源代码phalapi整个目录复制到xampp的htdocs目录下即可。打开XAMPP控制面板并启动Apache后，便可通过以下链接，访问默认接口服务。  
```
http://localhost/phalapi/public/
```


以上不管何种配置，正常情况下，访问默认接口服务可以看到类似这样的输出：  
```
{
    "ret": 200,
    "data": {
        "title": "Hello PhalApi",
        "version": "2.0.1",
        "time": 1501079142
    },
    "msg": ""
}
```

运行效果，截图如下：  

![](http://cdn7.phalapi.net/20170726223129_eecf3d78826c5841020364c852c35156)


至此，安装完毕！


## 如何升级PhalApi 2.x框架？

在composer的管理下，升级PhalApi 2.x 版本系列非常简单。只需要修改composer.json文件，指定相应的版本即可。PhalApi的框架内核项目在[phalapi/kernal](https://github.com/phalapi/kernal)，你可以指定版本，也可以跟随最新版本。

例如，当需要指定PhalApi 2.0.1版本时，可以这样配置：
```
{
    "require": {
        "phalapi/kernal": "2.0.1"
    }
}
```

当需要保持最新版本时，则可以改成： 
```
{
    "require": {
        "phalapi/kernal": "2.*.*"
    }
}
```

这样，当PhalApi 2.x 有版本更新时，只需执行composer更新操作即可。对应命令操作为：  
```bash
$ composer update
```

至此，升级完毕！

#### 温馨提示：关于composer版本的说明，可参考[Composer中文文档 - 包版本](http://docs.phpcomposer.com/01-basic-usage.html#Package-Versions)。

  
