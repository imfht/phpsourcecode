# EpiiServer是什么
EpiiServer是一个简单易用的基于Php的部署工具。它能帮助开发人员迅速的构建php+nginx多应用部署环境，帮助测试人员构建测试环境。
### 特性：
1. 多应用快速部署,域名自动生成。
2. 每个应用可设置自己php的版本。
3. 每个应用可设置自己的环境变量。
4. 域名与目录自动绑定，支持5级域名自动绑定目录。
5. 与wamp等集成环境相比，更加侧重定制化配置，而非环境的安装。


### EpiiServer的基本工作原理大致如下：

首先，根据自己的需求安装nginx和php（可下载多个版本）
然后，在配置文件配置自己的应用， 每一个设置自己的php版本及环境变量
最后，通过`php install/install.php` 去修改nginx的config 文件。并生成服务。


## 安装
下载或clone到本地后，

1、请复制`config.ini.example` 为 `config.ini`，按照配置文件的提示配置自己的项目。

2、请运行项目下`install/install.php` 文件进行安装
```php
path/to/php ./install/install.php
```

安装只需一次，安装后，会自动生成启动，停止运行文件。

window自动生成的文件为：

- start.bat 启动服务
- stop.bat  停止服务

linux unix 自动生成的文件为：

- start.sh 启动服务
- stop.sh  停止服务

> 配置文件修改后记得 先关闭服务，再启动。

3、全局命令

全局命令可实现服务的快速启动，及应用的快速配置
> linux unix 系统默认已经在全局PATH目录下
> window 需要把 /path/to/epii-server/bin 目录增加到系统全局path下

| 命令                                      | 作用                                    |
| :---------------------------------------- | :-------------------------------------- |
| epii-server help                          | 支持的命令列表                          |
| epii-server config                        | 配置详情                                |
| epii-server start                         | 启动服务                                |
| epii-server stop                          | 暂停服务                                |
| epii-server restart                       | 重启启动服务                            |
| epii-server app list/ls                   | 显示所有应用                            |
| epii-server app add {appname}             | 为当前目录为新应用                      |
| epii-server app remove                    | 删除当前目录对应的应用                  |
| epii-server app remove {appname}          | 删除应用                                |
| epii-server app info                      | 显示当前目录对应的应用信息              |
| epii-server app info {appname}            | 应用详情                                |
| epii-server domain list\ls                | 外网域名列表                            |
| epii-server domain add {domain} {appname} | 新增外网域名绑定                        |
| epii-server domain remove {domain}        | 解除外网域名绑定                        |
| epii-server app open                      | 打开当前目录对应的应用网址              |
| epii-server app open {appname}            | 打开指定应用网址                        |
| epii-server app opendir                   | 打开当前目录对应的目录                  |
| epii-server app opendir {appname}         | 打开指定应用目录                        |
| epii-server app dir {appname}             | 仅仅显示应用目录                        |
| epii-server hosts list\ls                 | 本地域名列表                            |
| epii-server hosts addall                  | 本地域名全部添加 （需要管理员权限）     |
| epii-server hosts add {appname}           | 本地域名添加        （需要管理员权限）  |
| epii-server hosts clear                   | 清除相关本地域名添加 （需要管理员权限） |

### 配置文件（详细说明在下面）

```php
[server]
;本机ip地址和端口
this_ip = 127.0.0.1
this_port = 80
;本机域名前缀
domain_this = this.jt
;web项目路径，此路径下每一个文件夹会当做一个应用，如果某一个项目不想放在此目录下，可以再app_dir中单独设置

;www_dir 为网站根目录，默认为web目录，如果设置请设置绝对路径
;www_dir = /Users/mrren/Documents/phpworkspace/EpiiWeb/web

;default_app = web1
;本程序以php为脚本安装和启动服务，指定php命令地址，一般为php.exe的文件路径
php_cmd=php

[nginx]
;nginx 文件地址; linux or unix 请指定nginx文件地址即可
cmd = /usr/local/Cellar/nginx/1.15.0/bin/nginx
nginx_config_file = /usr/local/etc/nginx/nginx.conf
[php]
;window下 php-cgi.exe 的路径，linux 下 php-fpm 路径
php_cgi[0] = /usr/local/Cellar/php\@7.1/7.1.19/sbin/php-fpm
;如果使用php-cgi，设置的端口将被启用。如果是php-fpm 请确保此端口和php-fpm.conf中一致（php-fpm.conf 需手动修改，多个php版本一定要设置不同的端口）
port[0] = 9000
php_cgi[1] = php-fpm
socket[1] = "unix:/var/run/php-fpm.sock"
[app_dir]
;如果你的应用不在www_dir下，请指定项目路径（必须为绝对路径）
;app1 = /Users/mrren/Documents/phpworkspace/jianguan
;epiiadmin=/Users/mrren/Documents/phpworkspace/EpiiWeb/web/epiiadmin/public


[app_php_select]
;默认所有的php版本自动为php_cgi[0] 的版本，如果有特殊需求请在这里设置
epiiadmin = 1

[php_env]
epiiadmin[db_hostname] = zhengxin
[domain_app]
;www.mydomain.com=app10/dir
```

## 解决了什么问题？
先不说怎么安装，先看看你是否需要本应用。

## 1、本地多个网站，域名自动生成。

----

也许你会说直接用 `http://localhost/app1`  和 `http://localhost/app2` 来访问两个网站不就行了。
确实可以。但如果各自设定自己的域名呢？比如 `http://app1.loc.com` 和 `http://app2.loc.com`  ，如果你有这个想法，本软件很轻松可帮你实现。

如下目录
```php
web -- App集合目录
    app3
        index.php --入口文件
    app4
        application
        public
            index.php --入口文件
    app5
        dir1
        dir2
            dir3
                index.php --入口文件

```
域名自动为

```php
http://app3.loc.com
http://public.app4.loc.com
http://dir3.dir2.app5.loc.com
```
> 你需要做的事情（唯一要做的）仅仅是在host文件中让`app3.loc.com` 和 `public.app4.loc.com`，`dir3.dir2.app5.loc.com` 指向你的ip

#### EpiiServer根目录下的`web`目录为app的项目集合目录，只要你把你的app放进这个目录，自动会生成上述的域名。


#### 疑问1

上面中域名`loc.com` 是什么？
他是你所有`app`的根域名。任何一个app将子对转化为域名 `{appname}.loc.com`,目录访问自动为 `dir3.dir2.dir1.{appname}.loc.com`。

如果想设置自己的根域名只需在`config.ini`(下载后请复制`config.ini.example` 为 `config.ini`)在`[server]`下设置

```php
[server]
domain_this=you.domain.com
```

- 如果你用dns服务器来实现域名的泛解析那么您将不需要在`hosts`文件中设置域名指向。
- 任何`app`均以`根域名`为基础产生的多级域名。其它格式的域名不支持。
- 如果某个app有另一个域名如`www.web.com` 那么您可以在dns服务商使用cname的方式解析到本app的本地域名。
#### 疑问2

也许你会问，我的项目都在另个目录下面，是不是必须复制到EpiiServer根目录下的`web`目录下才行呢？当然不是。

只需在`config.ini`的`[server]`下设置

```php
[server]
www_dir=/path/to/your/www
```
> www_dir为绝对路径，一定不要包含中文

#### 疑问3

按照上述方法设置了我的app集合目录，但仍有个别app(或很多)分布在其它目录,是不是需要复制到app集合目录下？当然不是。

比如我的 `app6` 放在了另一个目录 `"c:\workplace\app6"`,只需在`config.ini`的`[app_dir]`下设置

```php
[app_dir]
app6=c:\workplace\app6
app7=/path/to/app7
```
> 目录为绝对路径，不包含中文。

这种情况下域名 `app6.loc.com` ,`app7.loc.com` 将指向你设置的路径。同样支持子目录转化为域名 ，
如 `app6` 的入口文件为 `c:\workplace\app6\public\index.php`,则访问
```php
http://public.app6.loc.com
```
其实上述设置是不科学的，直接把app6的目录指向`public`更好
```php
[app_dir]
app6=c:\workplace\app6\public

```
这样你的域名将简化为`app6.loc.com`

#### 技巧
```php
在上面web下app5中，为了访问入口文件 我们需要 访问 
`http://dir3.dir2.app5.loc.com` 这个域名才可以，如果想简化为 `http://app5.loc.com`，
只需把 dir3的绝对路径设置为`app5`的路径即可。app5=/path/to/app5/dir2/dir3
```

#### 疑问4

直接访问ip会怎么样？

直接访问ip和其它继承环境一样了。

```php
http://127.0.0.1/app1
http://127.0.0.1/app2/dir1/dir2/index.php
```

能不能当我访问ip的时候，默认指定一个`app`呢。比如访问ip直接访问 `app1`，
只需在`config.ini`的`[server]`下设置

```php
[server]
default_app=app1
```
设置完后，当访问 `http://127.0.0.1/` 时候将直接指向了`app1`。
> ip 访问的作用在于别人对你电脑的访问。当然别人也可以在他`hosts`文件中绑定 app1.loc.com 到你的ip，直接访问域名也可以





##  2、多个php版本共存。

---

多年的php工作者，对着php技术更新，及php版本的更新。你的项目分别设置了不同的php的最低版本。

例如

- `app8`支持版本为`php5.6` 
- `app9`支持版本为`php7.1` 
- `app10`支持版本为`php7.2` 

如果这些应用共存，您有什么解决方法。

解决方法很简单。首先下载多个版本的php。然后在`config.ini`的`[php]`模块设置。

`window` 使用的是`php-cgi.exe`,所以只需要指定每一个php版本的`php-cgi.exe`路径,及端口即可。
```php
[php]
php_cgi[0] = c:\path\to\php5.6\php-cgi.exe
port[0] = 9000

php_cgi[1] = c:\path\to\php7.1\php-cgi.exe
port[1] = 9001

php_cgi[2] = c:\path\to\php7.3\php-cgi.exe
port[2] = 9002
```

`linux`,`unix`下使用的是`php-fpm`(php-cgi，fastcgi，php-fpm的区别，大家自己查)

```php
[php]
php_cgi[0] = /path/to/php5.6/sbin/php-fpm
port[0] = 9000

php_cgi[1] =/path/to/php7.1/sbin/php-fpm
port[1] = 9001

php_cgi[2] = /path/to/php7.2/sbin/php-fpm
port[2] = 9002
```

> 注意：php-fpm的配置文件 `php-fpm.conf` 里设置了端口。上面设置的端口一定要和各个版本的 `php-fpm.conf`中的端口一致。而`php-cgi` 只需设置端口即可。

通过上述设置`php`多版本后,默认的所有应用都设置为第一个php版本，即`php_cgi[0]`的设置。

为了实现
- `app8`支持版本为`php5.6` 
- `app9`支持版本为`php7.1` 
- `app10`支持版本为`php7.2` 

需在在`config.ini`的`[app_php_select]`模块设置各自的php版本id

```php
[app_php_select]
app9=1
app0=2
```
> app8无需设置，因为所有的应用默认都使用第一个php版本

##  3、环境变量设置。

大家肯定会遇到这些问题

- app的开发和部署使用的数据库参数不一样。如何有效分离。
- 很多人在使用`git`作为团队合作方式。如何让重要的账号和密码不受版本控制。

上述问题，有很多解决方案，但更方便更科学的方式为通过`环境变量`设置账号和密码，使得`程序和重要账号完全分离`。

在不同的环境下（window，linux,iis,apache,nginx）设置`php环境变量`的方式不一样，

但

#### 在php获取环境变量的方式是一样的

这样使得我们的应用程序代码无需任何修改，只需在环境中设置了环境变量即可。
> php中通过 $_EVN,或者 getenv() 来获取指定的环境变量值。

现有的方式设置的环境变量，往往是针对所有app都生效的。这意味着如果我有多个项目，每一个项目都是共享这些环境变量，这样的结果为：

- 不方便。`app11` ，`app12` 的数据库名称，我们必须设置两个环境变量，如 `DBNAMA_APP11`, `DBNAMA_APP12`。然后分别获取。
- 不安全。在`app11`中仍然可以获取到`app12` 的环境变量。

#### 使用 `EpiiServer` 这些问题将变得很容易解决。

我们的需求是：

1、`app11` 需要把数据库信息设置为环境变量 分别为
 
 ``` 
 DB_HOST=192.168.1.100
 DB_NAME=ceshi
 DB_USER=username
 DB_PWD=password
 ```
2、`app12` 需要把数据库信息设置为环境变量 分别为
 
 ``` 
 DB_HOST=192.168.1.102
 DB_NAME=ceshi2
 DB_USER=username2
 DB_PWD=password2
 ``` 
我们只需在`config.ini`的`[php_env]`模块设置各自的环境变量

```php
[php_env]
app11[DB_HOST] = 192.168.1.100
app11[DB_NAME] = ceshi
app11[DB_USER] = username
app11[DB_PWD] = password

app12[DB_HOST] = 192.168.1.102
app12[DB_NAME] = ceshi2
app12[DB_USER] = username2
app12[DB_PWD] = password2
```

> 在程序中使用 $_ENV['DB_HOST'] 即可获取到相应的 DB_HOST

> 阿里云和微软云等云平台都有设置环境变量的方法。



##  4、域名与App绑定。

如果有另一个域名，www.mydomain.com 。想绑定到一个app。

如果按上述配置的入口域名为：

```
dir.app.loc.com
```

我们只需在`config.ini`的`[domain_app]`模块绑定就可以了

```json
[domain_app]
www.mydomain.com=app/dir
```


## 如何安装

`EpiiServer` 侧重的是`nginx` `php` 安装后的灵活配置，而非 `nginx` `php`本身的安装。

所以在安装`EpiiServer`之前你需要（必须）

1、根据自己的系统下载`nginx`，并且明白安装路径及配置文件路径。

2、根据自己的需要下载安装`php`，可下载多个版本。

3、window 用户须知道安装的各个php版本路径及`php-cgi`的位置，并且每个版本可以成功运行 
```php
/path/to/php-cgi.exe -b 127.0.0.1:9000
```
> window下有些电脑高版本php提示你的系统缺少dll，只需下载运行库即可。 下载地址： https://www.microsoft.com/zh-cn/download/details.aspx?id=48145

4、linux，unix 用户须知道安装的各个php版本路径及`php-fpm`的位置和`php-fpm.conf`的路径，分别修改`php-fpm.conf`文件 ，并成功运行
```php
/path/to/php-fpm
```

`EpiiServer` 仓库首页

#### gitee仓库
```php
https://gitee.com/epii/epii-server
```
#### github仓库首页
```php
https://github.com/epaii/epii-server
```



大部分参数在上述教程已经涉及到。重点介绍

`[server]` 下的 `php_cmd`
```php
[server]
php_cmd=php
```
`EpiiServer`本身是基于php的（并非你的网站），如果你的php在环境变量PATH下，则直接为默认配置即可。 如果不是。linux，unix 用户 为`path/to/php`,window用户为`paht/to/php.exe`



> 最后希望`EpiiServer`能给您带来帮助。让您更多的时间去研发产品，而非环境搭建。



