# Yii2 CMS
一款基于YII2框架的内容管理系统

安装
------------

### 安装Yii2 CMS 

  1. 通过Git安装

    ```bash
    cd /var/www/
    git clone https://git.oschina.net/kzeng/yii2-cms.git mysite.com
    ```

  2. 初始化

     执行 `init` 命令，然后选择 `dev` or `prod` 环境.

      ```bash
      cd /var/www/mysite.com/
      php init
      ```
  
  3. 配置Web服务器:

     Apache 服务器参考配置如下:
     
     ```apacheconf
     <VirtualHost *:80>
       ServerName mysite.com
       ServerAlias www.mysite.com
       DocumentRoot "/var/www/mysite.com/"
       <Directory "/var/www/mysite.com/">
         AllowOverride All
         Order deny,allow
         Allow from all
         Require all granted
       </Directory>
     </VirtualHost>
     ```
     Nginx 服务器参考配置如下:
     
     ```nginx
     server {
         charset      utf-8;
         client_max_body_size  200M;
         listen       80;
     
         server_name  mysite.com;
         root         /var/www/mysite.com;
     
         location / {
             root  /var/www/mysite.com/frontend/web;
             try_files  $uri /frontend/web/index.php?$args;
     
             # avoiding processing of calls to non-existing static files by Yii
             location ~ \.(js|css|png|jpg|gif|swf|ico|pdf|mov|fla|zip|rar)$ {
                 access_log  off;
                 expires  360d;
                 try_files  $uri =404;
             }
         }
     
         location /admin {
             alias  /var/www/mysite.com/backend/web;
             rewrite  ^(/admin)/$ $1 permanent;
             try_files  $uri /backend/web/index.php?$args;
         }
     
         # avoiding processing of calls to non-existing static files by Yii
         location ~ ^/admin/(.+\.(js|css|png|jpg|gif|swf|ico|pdf|mov|fla|zip|rar))$ {
             access_log  off;
             expires  360d;
     
             rewrite  ^/admin/(.+)$ /backend/web/$1 break;
             rewrite  ^/admin/(.+)/(.+)$ /backend/web/$1/$2 break;
             try_files  $uri =404;
         }
     
         location ~ \.php$ {
             include  fastcgi_params;
             # check your /etc/php5/fpm/pool.d/www.conf to see if PHP-FPM is listening on a socket or port
             fastcgi_pass  unix:/var/run/php5-fpm.sock; ## listen for socket
             #fastcgi_pass  127.0.0.1:9000; ## listen for port
             fastcgi_param  SCRIPT_FILENAME $document_root$fastcgi_script_name;
             try_files  $uri =404;
         }
         #error_page  404 /404.html;
     
         location = /requirements.php {
             deny all;
         }
     
         location ~ \.(ht|svn|git) {
             deny all;
         }
     }
     ```
    
       
  4. 创建一个新数据库，调整`components['db']`配置在`common/config/main-local.php`.


  5. 在控制台执行迁徙命令
 ```
  php yii migrate --migrationLookup=@yeesoft/yii2-yee-core/migrations/,@yeesoft/yii2-yee-auth/migrations/,@yeesoft/yii2-yee-settings/migrations/,@yeesoft/yii2-yee-menu/migrations/,@yeesoft/yii2-yee-user/migrations/,@yeesoft/yii2-yee-translation/migrations/,@yeesoft/yii2-yee-media/migrations/,@yeesoft/yii2-yee-post/migrations/,@yeesoft/yii2-yee-page/migrations/,@yeesoft/yii2-comments/migrations/,@yeesoft/yii2-yee-comment/migrations/,@yeesoft/yii2-yee-seo/migrations/
  ```
  增强contact 联系我们模块功能，把数据记到`contact_form`表中，迁徙命令：
  ```
  php yii migrate --migrationPath=@frontend/migrations/
  ```

  6. 在控制台创建一个root user: `php yii init-admin`.

  7. 配置 `['components']['mailer']` 在 `common/config/main-local.php` 中.
  ```
            'mailer' => [
                'class' => 'yii\swiftmailer\Mailer',    
                'useFileTransport' => false,
                'transport' => [
                    'class' => 'Swift_SmtpTransport',
                    'host' => 'smtp.qq.com',
                    'username' => 'zengkai001@qq.com',
                    'password' => 'jfpvkrfwcopibgdb',
                    'port' => '465',
                    'encryption' => 'ssl',
                ],
                'messageConfig' => [
                    'charset' => 'UTF-8',
                    'from' => ['zengkai001@qq.com'=>'admin'],
                ],
    
                'htmlLayout' => '@vendor/yeesoft/yii2-yee-auth/views/mail/layouts/html',
                'textLayout' => '@vendor/yeesoft/yii2-yee-auth/views/mail/layouts/text',
            ],
  ```
  其中`username`为邮箱账号。邮箱账号必须开启SMTP。`password`是SMTP授权码，不是邮箱账号密码！！！

  8. 访问前台：`mysite.com` 访问后台：`mysite.com/admin`



已解决的问题
------------
~~1. 后台Carousel(轮播图)管理模块，上传图片功能需完善。~~

~~2. tinymce中文化及定制开发与使用。~~

~~3. 轮播图中文件上传组件需改进，可参考post中缩略图上传组件的使用，应考虑图片素材一个网站内共用和统一管理~~

~~4. contact(联系我们)功能需完善， 建表需用migrate~~

~~5. 本地化，待完善~~


待解决的问题
------------
1. 后台自定义字段模块，怎么用？
2. 后台HTML Block模块，怎么用？
3. yii2-oauth,怎么用？怎么把社交帐号登录集成进来？
4. 已完成功能模块需规范，composer打包机制？
5. yee-gii的用法
6. 打造一套或多套门户网站风格前端layout+view

