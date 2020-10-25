#icyblog 开源博客系统

一款基于laravel5.1的开源博客cms系统，特别适合技术博客，具有以下特点

- 后台富文本markdown解决方案，图片拖拽上传
- 文章支持草稿及标签分类，管理更快捷
- 针对前端加载速度进行优化
- 支持使用redis缓存页面，高并发
- 针对搜索引擎进行了seo优化

示例站点[我的个人博客 icybee.cn](http://icybee.cn)

#需要的支持
laravel需要composer来管理依赖
composer安装参考 [laravel官网](http://laravel.com)

#获取icyblog
```
git clone https://git.oschina.net/icybee/icyblog.git
cd icyblog
```
#修改nginx 指向public 目录
```
server {
    listen 80;
    server_name tool.lu;
    root /route/to/your/app/public;
    index index.html index.php;

    location / {
        try_files $uri $uri/ /index.php$is_args$query_string;
    }

    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```
#配置数据库和redis
##数据库配置
编辑.env文件，修改
```
DB_HOST=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```
依次是数据库主机，database名，数据库用户和密码

##redis配置
编辑config/redis.php
```
<?php

return [

  'host' => '127.0.0.1',

  'port' => 6379

];

```
将默认的host和port改为实际的redis主机和端口

#更改图片上传路径
编辑app/Http/Controller/UploadBase64Controller
修改
```
file_put_contents('/alidata/www/laravelupload/upimg/'.$savname, base64_decode($base));
```
中的路径为你希望的保存图片的路径，记住赋予www用户路径写权限

#联系方式
icybee@yeah.net 欢迎骚扰~~
