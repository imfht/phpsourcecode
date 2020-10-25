<h1 align="center">
Gryen-GTD
</h1>

<p align="center">
gryen-gtd 是一个界面简洁的 web 端个人 gtd app。基于 <a target="_blank" href="https://laravel.com/" rel="noopener noreferrer">Laravel</a> 开发，提供博客发布、任务列表等功能。
</p>

<p align="center">
<a href="https://travis-ci.org/itargaryen/gryen-gtd">
  <img src="https://travis-ci.org/itargaryen/gryen-gtd.svg?branch=master" alt="Build Status" />
</a>
<a href="https://github.styleci.io/repos/164370918">
  <img src="https://github.styleci.io/repos/164370918/shield?branch=master" alt="StyleCI">
</a>
<a href="https://packagist.org/packages/itargaryen/gryen-gtd">
  <img alt="Packagist" src="https://img.shields.io/packagist/l/itargaryen/gryen-gtd.svg?color=%231380C3">
</a>
<a href="https://github.com/itargaryen/gryen-gtd">
  <img src="https://img.shields.io/badge/Awesome-Laravel-brightgreen.svg?style=flat-square" alt="Awesome Laravel">
</a>
</p>

## 目录

-   [页面一览](readme.md#页面一览)
-   [运行环境](readme.md#运行环境)
-   [安装指南](readme.md#安装指南)
-   [日常维护](readme.md#日常维护)
-   [更新日志](CHANGELOG.md)

### 页面一览

<p align="center">
<img src="http://markdown.gryen.com/index.jpg" alt="封面" width="800">
</p>
<p align="center">封面</p>

<p align="center">
<img src="http://markdown.gryen.com/articles.jpg" alt="笔记列表" width="800">
</p>
<p align="center">笔记列表</p>

<p align="center">
<img src="http://markdown.gryen.com/article_detail.jpg" alt="笔记详情" width="800">
</p>
<p align="center">笔记详情</p>

<p align="center">
<img src="http://markdown.gryen.com/2018-04-25-15087530267780-1.jpg" alt="笔记编辑" width="800">
</p>
<p align="center">笔记编辑</p>

### 运行环境

-   正确搭建 PHP 站点运行环境（PHP 7.2.5 及以上版本），推荐 OpenResty（Nginx） + PHP + MariaDB；
-   安装 NodeJS 以支持前端构建；
-   推荐安装启用 Redis 以提高性能（非必须）。

### 安装指南

#### 基础配置

1. 克隆或下载代码；
2. 执行 `composer install` 安装 PHP 依赖；
3. 执行 `npm install` 安装 JS 依赖；
4. 复制 `.env.example` 到 `.env`，修改数据库连接参数：

    ```
    DB_DATABASE=
    DB_USERNAME=
    DB_PASSWORD=
    ```

    _亦可进一步修改其他参数，使用 Redis 或者使用[七牛云](https://portal.qiniu.com/signup?code=3loirka20zp76)加速网站。_

5. 执行 `php artisan key:generate` 生成应用密钥；
6. 执行 `php artisan migrate`，生成数据表；

#### 评估试用

1. 首先，执行 `composer dumpautoload` 转储自动加载器；
2. 借助 Laravel 提供的快速填充数据能力，配置成功后可以马上看到 Gryen-GTD 铺满笔记的效果：执行 `php artisan db:seed` 填充数据，然后访问 `http(s)://[yourdomain]/` 查看；
3. 访问 `http(s)://[yourdomain]/login` 登录用户（邮箱：`[user@gryen.com]`，密码：`password`）；
4. 访问 `http(s)://[yourdomain]/articles/create` 尝试创作。

**评估结束后可以方便地移除测试数据，执行 `php artisan migrate:fresh` 重建数据表即可。**

#### 正式使用

1. 确保已经执行 `php artisan migrate:fresh` 重建数据表，访问 `http(s)://[yourdomain]/register` 注册用户获取权限；
2. 访问 `http(s)://[yourdomain]/login` 登录用户；
3. 访问 `http(s)://[yourdomain]/articles/create` 开始创作。
