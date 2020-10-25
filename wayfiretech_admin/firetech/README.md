# 欢迎使用店滴AI
- 完美的swagger api稳定，可以在线测试的接口稳定。
- 使用稳定的YII后台框架，自研系统，方便二次开发，欢迎有开发能力的朋友加入进来一起开发模块进行售卖，官方提供技术支持，推广支持，品牌支持
# 关于我们
店滴AI：基于AI技术的应用开源管理系统，对接AI有关的软件、硬件，提供基于AI技术的整体解决方案。
我们提供开源的AI管理系统源码，欢迎各界朋友使用。
## 官方地址
### 官方网址：[http://www.wayfirer.com/](http://www.wayfirer.com/ "http://www.wayfirer.com/")
### 接口地址：[http://www.wayfirer.com//index.php?r=doc](http://www.wayfirer.com//index.php?r=doc "http://www.wayfirer.com//index.php?r=doc")
### 后台代码GIT：[https://gitee.com/wayfiretech_admin/firetech](https://gitee.com/wayfiretech_admin/firetech "https://gitee.com/wayfiretech_admin/firetech")
#### 加群连接
1. qq群：麻烦下载的朋友加下qq交流群：[823429313]( https://jq.qq.com/?_wv=1027&k=5cutnyx "823429313")，可以获取后台管理，接口管理系统，代码开源，
欢迎使用。
# 微信公众号：
![店滴AI](http://www.wayfirer.com/attachment/202003/15/4a0aec77-66cf-3354-9b4a-daf2bab58fb5.png "店滴AI")



# 环境准备：

	php>=7.2
	redis
	git 工具下载：https://git-scm.com/downloads
	composer https://www.phpcomposer.com/ 
	composer建议使用阿里镜像 https://developer.aliyun.com/composer


# 第一步：git下载代码

```
git clone https://gitee.com/wayfiretech_admin/firetech.git

```
# 第二步：更新composer 扩展
```
cd 你的文件路径
composer update

```
# 第三步：建立数据库并完成配置
```
cd common\config

vim common\config\main-local.php

```

```
<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-12 20:12:31
 * @Last Modified by:   Wang Chunsheng 2192138785@qq.com
 * @Last Modified time: 2020-03-22 19:35:10
 */

return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=netos',
            'username' => 'root',
            'password' => 'root',
            'charset' => 'utf8',
            'attributes'  => [
                PDO::ATTR_STRINGIFY_FETCHES => false,
                PDO::ATTR_EMULATE_PREPARES  => false,
            ],
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
    ],
    'language' => 'zh-CN',

];


```



# Nginx 部署配置

首先解析网站到 frontend\web，然后配置nginx如下：

```
server {
        listen        80;
        server_name  www.ai.com;
        root   "D:/www/firetech/frontend/web";
			add_header Access-Control-Allow-Origin *;
			add_header Access-Control-Allow-Methods GET,POST,OPTIONS;
			add_header Access-Control-Allow-Headers X-Requested-With;
        location / {
            index index.php index.html error/index.html;
            error_page 400 /error/400.html;
            error_page 403 /error/403.html;
            error_page 404 /error/404.html;
            error_page 500 /error/500.html;
            error_page 501 /error/501.html;
            error_page 502 /error/502.html;
            error_page 503 /error/503.html;
            error_page 504 /error/504.html;
            error_page 505 /error/505.html;
            error_page 506 /error/506.html;
            error_page 507 /error/507.html;
            error_page 509 /error/509.html;
            error_page 510 /error/510.html;
            include D:/www/firetech/frontend/web/nginx.htaccess;
            autoindex  off;
        }
        location ~ \.php(.*)$ {
            fastcgi_pass   127.0.0.1:9001;
            fastcgi_index  index.php;
            fastcgi_split_path_info  ^((?U).+\.php)(/?.+)$;
            fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
            fastcgi_param  PATH_INFO  $fastcgi_path_info;
            fastcgi_param  PATH_TRANSLATED  $document_root$fastcgi_path_info;
            include        fastcgi_params;
        }
		       location /backend {
            index index.php index.html;
            if (!-e $request_filename)
            {
                rewrite ^/backend/(.*)$ /backend/index.php last;
            }
        }
        location /api {
            index index.php index.html;
            if (!-e $request_filename)
            {
                rewrite ^/api/(.*)$ /api/index.php last;
            }
            if (!-f $request_filename){
                set $rule_0 1$rule_0;
            }
        }
}


```

