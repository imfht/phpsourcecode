# Yii2 Application Kit

这是基于Yii2 Starter Kit 和 Yii2 Advanced Template 的`开箱即用`的Web 应用程序。


##安装

#### 开始之前 

##### 克隆代码仓库
```
git clone https://git.oschina.net/kzeng/yii2-app-kit.git
```
##### 安装依赖软件包 <`已集成，此步可略过`>
```
composer install
```

#### 最小需求
The minimum requirement by this application template that your Web server supports PHP 5.5.0.
Required PHP extensions:
- intl
- gd
- mcrypt
- exif 文件信息模块必须，富文本编辑器及媒体管理（pendalf89），不安装上传缩略图会出错。

#### 安装PHP扩展办法之一
以lnmp 1.2 PHP服务器套件为例:

```
cd /home/freeman/tools/lnmp1.2-full/src/php-5.6.9/ext/intl/
$phpize && ./configure --with-php-config=/usr/local/php/bin/php-config && make && make install

在这个configure的过程中会出现错误，安装相应的lib就行。
$yum install libicu-devel -y
$yum install libxslt-devel -y

cd /home/freeman/tools/lnmp1.2-full/src/php-5.6.9/ext/xsl/
$phpize && ./configure --with-php-config=/usr/local/php/bin/php-config && make && make install

修改php.ini
$vi /usr/local/php/etc/php.ini

增加：
extension=xsl.so
extension=intl.so
然后重启php服务/etc/init.d/php-fpm restart
```

#### 安装应用
1. Copy `.env.dist` to `.env` in the project root (``.env.docker.dist`` if you are using docker)
2. Adjust settings in `.env` file
	- Set debug mode and your current environment  
```
        YII_DEBUG   = true             
        YII_ENV     = dev  
```
   
	- Set DB configuration   
```
    DB_DSN           = mysql:host=127.0.0.1;port=3306;dbname=yii2-app-kit     
    DB_USERNAME      = user     
    DB_PASSWORD      = password     
```
    
	- Set application canonical urls   
```
    FRONTEND_URL    = http://yii2-app-kit.dev 
    BACKEND_URL     = http://backend.yii2-app-kit.dev 
    STORAGE_URL     = http://storage.yii2-app-kit.dev 
```

3. Run in command line `此步必须, 重要！`  
```
php console/yii app/setup
```

4. 富文本编辑器及媒体管理（pendalf89）器插件的表初始化
```
php console/yii migrate --migrationPath=vendor/pendalf89/yii2-filemanager/migrations
```

#### Configure your web server
Copy `vhost.conf.dist` to `vhost.conf`, change it with your local settings and copy (symlink) it to nginx ``sites-enabled`` directory.
Or configure your web server with three different web roots:
- yii2-app-kit.dev => /path/to/yii2-app-kit/frontend/web
- backend.yii2-app-kit.dev => /path/to/yii2-app-kit/backend/web
- storage.yii2-app-kit.dev => /path/to/yii2-app-kit/storage/web


#### Single domain installation
##### Setup application
Adjust settings in `.env` file

```
FRONTEND_URL    = /   
BACKEND_URL     = /admin   
STORAGE_URL     = /storage/web   
```

Adjust settings in `backend/config/web.php` file
```
    ...   
    'components'=>[  
        ...    
        'request' => [   
            'baseUrl' => '/admin',   
        ...   
```

Adjust settings in `frontend/config/web.php` file
```
    ...   
    'components'=>[    
        ...   
        'request' => [     
            'baseUrl' => '',   
        ...    
```

##### Configure your web server
###### Apache
This is an example single domain config for apache
```
<VirtualHost *:80>
    ServerName yii2-app-kit.dev

    RewriteEngine on
    # the main rewrite rule for the frontend application
    RewriteCond %{HTTP_HOST} ^yii2-app-kit.dev$ [NC] 
    RewriteCond %{REQUEST_URI} !^/(backend/web|admin|storage/web)
    RewriteRule !^/frontend/web /frontend/web%{REQUEST_URI} [L]
    # redirect to the page without a trailing slash (uncomment if necessary)
    #RewriteCond %{REQUEST_URI} ^/admin/$
    #RewriteRule ^(/admin)/ $1 [L,R=301]
    # disable the trailing slash redirect
    RewriteCond %{REQUEST_URI} ^/admin$
    RewriteRule ^/admin /backend/web/index.php [L]
    # the main rewrite rule for the backend application
    RewriteCond %{REQUEST_URI} ^/admin
    RewriteRule ^/admin(.*) /backend/web$1 [L]

    DocumentRoot /your/path/to/yii2-app-kit
    <Directory />
        Options FollowSymLinks
        AllowOverride None
        AddDefaultCharset utf-8
    </Directory>
    <Directory "/your/path/to/yii2-app-kit/frontend/web">
        RewriteEngine on
        # if a directory or a file exists, use the request directly
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        # otherwise forward the request to index.php
        RewriteRule . index.php

        Require all granted
    </Directory>
    <Directory "/your/path/to/yii2-app-kit/backend/web">
        RewriteEngine on
        # if a directory or a file exists, use the request directly
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        # otherwise forward the request to index.php
        RewriteRule . index.php

        Require all granted
    </Directory>
    <Directory "/your/path/to/yii2-app-kit/storage/web">
        RewriteEngine on
        # if a directory or a file exists, use the request directly
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        # otherwise forward the request to index.php
        RewriteRule . index.php

        Require all granted
    </Directory>
    <FilesMatch \.(htaccess|htpasswd|svn|git)>
        Require all denied
    </FilesMatch>
</VirtualHost>
```

###### Nginx
This is an example single domain config for nginx

```
server {
	listen 80;

	root /app;
	index index.php index.html;

	server_name yii2-app-kit.dev;

	charset utf-8;

	# location ~* ^.+\.(jpg|jpeg|gif|png|ico|css|pdf|ppt|txt|bmp|rtf|js)$ {
	#	access_log off;
	#	expires max;
	# }

	location / {
		try_files $uri /frontend/web/index.php?$args;
	}

	location /backend {
		try_files  $uri /backend/web/index.php?$args;
	}

	# storage access
	location /storage {
		try_files  $uri /storage/web/index.php?$args;
	}

    client_max_body_size 32m;

	location ~ \.php$ {
		fastcgi_split_path_info ^(.+\.php)(/.+)$;
		fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
		fastcgi_pass php-fpm;
		fastcgi_index index.php;
		include fastcgi_params;

		## Cache
		# fastcgi_pass_header Cookie; # fill cookie valiables, $cookie_phpsessid for exmaple
		# fastcgi_ignore_headers Cache-Control Expires Set-Cookie; # Use it with caution because it is cause SEO problems
		# fastcgi_cache_key "$request_method|$server_addr:$server_port$request_uri|$cookie_phpsessid"; # generating unique key
		# fastcgi_cache fastcgi_cache; # use fastcgi_cache keys_zone
		# fastcgi_cache_path /tmp/nginx/ levels=1:2 keys_zone=fastcgi_cache:16m max_size=256m inactive=1d;
		# fastcgi_temp_path  /tmp/nginx/temp 1 2; # temp files folder
		# fastcgi_cache_use_stale updating error timeout invalid_header http_500; # show cached page if error (even if it is outdated)
		# fastcgi_cache_valid 200 404 10s; # cache lifetime for 200 404;
		# or fastcgi_cache_valid any 10s; # use it if you want to cache any responses
	}
}
```
## PHP-FPM Servers ##
```
upstream php-fpm {
    server fpm:9000;
}
```


### Demo data
#### Demo Users

前端:
http://host/frontend/web

![Alt text](/docs/frontend.jpg "前端")

后端:
http://host/backend/web

![Alt text](/docs/backend.jpg "后端")

`administrator` role account
```
Login: webmaster
Password: webmaster
```

`manager` role account
```
Login: manager
Password: manager
```

`user` role account
```
Login: user
Password: user
```

### Important notes
- There is a VirtualBox bug related to sendfile that can lead to
corrupted files, if not turned-off
Uncomment this in your nginx config:
```sendfile off;```


## 功能
- Beautiful and open source dashboard theme for backend [AdminLTE 2](http://almsaeedstudio.com/AdminLTE)
- Translations: English, Spanish, Russian, Ukrainian, Chinese
- Translations Editor
- Language change action + behavior to choose locale based on browser preferred language 
- Sign in, Sign up, profile(avatar, locale, personal data), email activation etc
- OAuth authorization
- User management
- RBAC with predefined `guest`, `user`, `manager` and `administrator` roles
- RBAC migrations support
- Content management components: articles, categories, static pages, editable menu, editable carousels, text blocks
- Key-value storage component
- Application settings form (based on KeyStorage component)
- Ready-to-go RESTful API module
- [File storage component + file upload widget](https://github.com/trntv/yii2-file-kit)
- On-demand thumbnail creation [trntv/yii2-glide](https://github.com/trntv/yii2-glide)
- Command Bus with queued and async tasks support [trntv/yii2-command-bus](https://github.com/trntv/yii2-command-bus)
- Useful behaviors (GlobalAccessBehavior, CacheInvalidateBehavior, MaintenanceBehavior)
- Yii2 log web interface
- Application timeline component
- Cache web controller
- Maintenance mode component ([more](#maintenance-mode))
- System information web interface
- dotenv support
- `ExtendedMessageController` with ability to replace source code language and migrate messages between message sources
- [Aceeditor widget](https://github.com/trntv/yii2-aceeditor)
- [Datetimepicker widget](https://github.com/trntv/yii2-bootstrap-datetimepicker), 
- [Imperavi Reactor Widget](https://github.com/asofter/yii2-imperavi-redactor), 
- [Elfinder Extension](https://github.com/MihailDev/yii2-elfinder)
- [Xhprof Debug panel](https://github.com/trntv/yii2-debug-xhprof)
- Extended IDE autocompletion
- Nginx config example
- Test-ready
- Docker support and Vagrant support
- Built-in [mailcatcher](http://mailcatcher.me/)
- Assets compression and concatenation
- many other features ...


# 应用组件

### 国际化 I18N
If you want to store application messages in DB and to have ability to edit them from backend, run:
```
php console/yii message/migrate @common/config/messages/php.php @common/config/messages/db.php
```
it will copy all existing messages to database

Then uncomment config for `DbMessageSource` in
```php
common/config/base.php
```

### KeyStorage
Key storage is a key-value storage to store different information. Application settings for example.
Values can be stored both via api or by backend CRUD component.
```
Yii::$app->keyStorage->set('articles-per-page', 20);
Yii::$app->keyStorage->get('articles-per-page'); // 20
```

### Maintenance mode
Application kit has built-in component to provide a maintenance functionality. All you have to do is to configure ``maintenance``
component in your config
```php
'bootstrap' => ['maintenance'],
...
'components' => [
    ...
    'maintenance' => [
        'class' => 'common\components\maintenance\Maintenance',
        'enabled' => Astronomy::isAFullMoonToday()
    ]
    ...
]
```
This component will catch all incoming requests, set proper response HTTP headers (503, "Retry After") and show a maintenance message.
Additional configuration options can be found in a corresponding class.

Application kit configured to turn on maintenance mode if ``frontend.maintenance`` key in KeyStorage is set to ``true``

### Command Bus
- [What is command bus?](http://shawnmc.cool/command-bus)

In Application Kit Command Bus pattern is implemented with [tactician](https://github.com/thephpleague/tactician) package and 
it's yii2 connector - [yii2-tactician](https://github.com/trntv/yii2-tactician)

Command are stored in ``common/commands/command`` directory, handlers in ``common/commands/handler``

To execute command run
```php
$sendEmailCommand = new SendEmailCommand(['to' => 'user@example.org', 'body' => 'Hello User!']);
Yii::$app->commandBus->handle($sendEmailCommand);
```

### Timeline (Activity)
```php
$addToTimelineCommand = new AddToTimelineCommand([
    'category' => 'user', 
    'event' => 'signup', 
    'data' => ['foo' => 'bar']
]);
Yii::$app->commandBus->handle($addToTimelineCommand);
```

### Behaviors
#### CacheInvalidateBehavior
```php
 public function behaviors()
 {
     return [
         [
             'class' => `common\behaviors\CacheInvalidateBehavior`,
             'tags' => [
                  'awesomeTag',
                   function($model){
                       return "tag-{$model->id}"
                  }
              ],
             'keys' => [
                  'awesomeKey',
                  function($model){
                      return "key-{$model->id}"
                  }
              ]
         ],
     ];
 }
```
#### GlobalAccessBehavior
Add in your application config:
```php
'as globalAccess'=>[
        'class'=>'\common\behaviors\GlobalAccessBehavior',
        'rules'=>[
            [
                'controllers'=>['sign-in'],
                'allow' => true,
                'roles' => ['?'],
                'actions'=>['login']
            ],
            [
                'controllers'=>['sign-in'],
                'allow' => true,
                'roles' => ['@'],
                'actions'=>['logout']
            ],
            [
                'controllers'=>['site'],
                'allow' => true,
                'roles' => ['?', '@'],
                'actions'=>['error']
            ],
            [
				'allow' => true,
				'roles' => ['@']
			]
        ]
    ]
```
It will allow access to you application only for authentificated users. 

### Command Bus
Read more about command bus on in [official repository](https://github.com/trntv/yii2-command-bus#yii2-command-bus)

### Widgets configurable from backend
#### Carousel
1. Create carousel in backend
2. Use it:
```php
<?php echo DbCarousel::widget(['key' => 'key-from-backend']) ?>
```

#### DbText
1. Create text block in backend
2. Use it:
```php
<?php echo DbText::widget(['key' => 'key-from-backend']) ?>
```

#### DbMenu
1. Create text block in backend
2. Use it:
```php
<?php echo DbMenu::widget(['key' => 'key-from-backend']) ?>
```

### Widgets
- [WYSIWYG Redactor widget](https://github.com/asofter/yii2-imperavi-redactor)  
- [DateTime picker](https://github.com/trntv/yii2-bootstrap-datetimepicker)
- [Ace Editor](https://github.com/trntv/yii2-aceeditor)
- [File upload](https://github.com/trntv/yii2-file-kit)
- [ElFinder](https://github.com/MihailDev/yii2-elfinder)


### Grid
#### EnumColumn
```php
 [
      'class' => '\common\grid\EnumColumn',
      'attribute' => 'status',
      'enum' => User::getStatuses() // [0=>'Deleted', 1=>'Active']
 ]
```
### API
Application Kit has fully configured and ready-to-go REST API module. You can access it on http://yii2-app-kit.dev/api/v1

### MultiModel
``common\base\MultiModel`` - class for handling multiple models in one
In controller:
```php
$model = new MultiModel([
    'models' => [
        'user' => $userModel,
        'profile' => $userProfileModel
    ]
]);

if ($model->load(Yii::$app->request->post()) && $model->save()) {
    ...
}
```
In view:
```php
<?php echo $form->field($model->getModel('account'), 'username') ?>

<?php echo $form->field($model->getModel('profile'), 'middlename')->textInput(['maxlength' => 255]) ?>    
```
### Other
- ``common\behaviors\GlobalAccessBehavior`` - allows to set access rules for your application in application config

- ``common\behaviors\LocaleBehavior`` - discover user locale from browser or account settings and set it

- ``common\behaviors\LoginTimestampBehavior`` - logs user login time

- ``common\validators\JsonValidator`` - validates a value to be a valid json

- ``common\rbac\rule\OwnModelRule`` - simple rule for RBAC to check if the current user is model owner
```php
Yii::$app->user->can('editOwnModel', ['model' => $model]);
```

- ``common\filters\OwnModelAccessFilter`` - action filter to check if user is allowed to manage this model
```php
public function behaviors()
    {
        return [
            'modelAccess' => [
                'class' => OwnModelAccessFilter::className(),
                'only' => ['view', 'update', 'delete'],
                'modelClass' => Article::className()
            ],
        ];
    }
```


##CONSOLE


### RbacMigrateController
Provides migrate functionality for RBAC.

``php console/yii rbac-migrate/create init_roles``

``php console/yii rbac-migrate/up``

``php console/yii rbac-migrate/down all``

#### Compress assets
You need to have yuicompressor and uglifyjs installed.

```php console/yii asset/compress frontend/config/assets/compress.php frontend/config/assets/_bundles.php```

then uncomment these lines in the ``frontend/config/web.php``
```
// Compressed assets
//$config['components']['assetManager'] = [
//   'bundles' => require(__DIR__ . '/assets/_bundles.php')
//];
```



##测试


To run tests:
1. Rename ``tests/.env.dist`` to ``tests/.env`` and edit it to set your local settings
2. Create ``yii2-app-kit-test`` database
3. Setup application
```
php tests/codeception/bin/yii app/setup
```
4. Start web server
```
php -S localhost:8080
```
5. Run tests:
```
codecept run
```


##常见问题


### 1. Where is Gii?
Gii is available on:
- http://127.0.0.1/yii2-app-kit/backend/web/gii
- http://127.0.0.1/yii2-app-kit/frontend/web/gii

### 2. How do i enable email activation?
Edit ``frontend/config/web.php`` and set [[frontend\modules\user\Module::shouldBeActivated]] property to ``true``






