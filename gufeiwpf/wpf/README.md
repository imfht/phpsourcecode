基于phalcon的PHP框架，请自行配置phalcon扩展，当前框架使用的phalcon版本为2.0.7

[phalcon项目地址](https://github.com/phalcon/cphalcon)

QQ交流群：485475740


##环境

由于使用rewrite规则，默认使用apache，[Apache 安装说明](https://docs.phalconphp.com/zh/latest/reference/apache.html)
如果使用nginx，[Nginx 安装说明](https://docs.phalconphp.com/zh/latest/reference/nginx.html)

由于图片规则的关系，本框架在nginx上需要增加一条rewrite配置
```
rewrite ^(photo|user)/(.*)/index/(.*)$ $1/$2/$3;
```

后台模板采用bootstrap框架，为了解决一些跨域字体等问题需要发送Access-Control-Allow-Origin头

APACHE，需要开启headers_module，并且在配置中加入
```
<FilesMatch "\.*$">
    <IfModule headers_module>
        Header set Access-Control-Allow-Origin "*"
    </IfModule>
</FilesMatch>
```

NGINX,需要在http中加入
```
add_header Access-Control-Allow-Origin *;
add_header Access-Control-Allow-Headers X-Requested-With;
add_header Access-Control-Allow-Methods GET,POST,OPTIONS;
```


nginx方式，请注意默认会出现502错误
```
pstream sent too big header while reading response header from upstream
```
解决方案
修改nginx配置文件，在http中加入
```
proxy_buffer_size  128k;

proxy_buffers   32 32k;

proxy_busy_buffers_size 128k;

proxy_temp_file_write_size 128k;
```

在对应的server中的php中加入
```
fastcgi_buffer_size 128k;

fastcgi_buffers 32 32k;
```

###框架默认使用mysql数据库

###php需要的扩展

缓存
memcache
redis

加密
mcrypt

图片
gd


###框架目录权限

如果根目录有写权限的话，会自动建立cache目录和logs目录，以及public/photo目录
如果根目录没有写权限的话，请自行手工建立这三个目录，并分别赋于写权限即可



##域名式多模块

框加中默认有四个模块，在app目录下，分别是
* app
    * admin 后台管理，对应admin.youdomain.com
    * files 静态处理，对应files.youdomain.com
    * www 前台，对应www.youdomain.com
    * cron 命令行式任务处理

请把对应的域名同时指向public目录即可




