### IAdmin 后台管理系统基础框架

IAdmin 是一套基于 [Laravel 5.3](http://d.laravel-china.org/docs/5.3/) 的后台管理系统，目的是建立一套稳定、易用的后台基础框架，让开发者能够节省开发时间，快速实现应用

- 登录认证（Auth）
- 基于RBAC的权限控制系统 [开发者可自由控制节点约束]

###### Laravel 5.3 运行环境要求， 详细的[说明文档](http://d.laravel-china.org/docs/5.3/)
- PHP >= 5.6.4
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension


Laravel 使用 [Composer](https://getcomposer.org/) 来管理代码依赖。所以，在使用 Laravel 之前，请先确认你的电脑上安装了 Composer。

###### 下载地址 [ Downloads ]

    git clone https://git.oschina.net/yazikeji/iadmin.git

    composer install

成功安装后，需要配置 Database、Redis，为了提高运行速度，建议大家使用 [Redis](http://www.redis.cn/)

###### 配置 Database
    vim .env
        /**编辑一下内容**/
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=数据库名称
        DB_USERNAME=数据库用户名
        DB_PASSWORD=数据库密码
###### 运行安装命令
    php artisan iadmin:install
至此系统已经安装完毕， 可以通过 http://youdomain 来访问系统
