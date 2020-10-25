<div align="center">
    <img src="https://i.loli.net/2020/10/06/rNvE5BdZ4eJjKQP.png"/>
    <h2>Lara Watcher</h2>
</div>

<p align="center">
    <img src="https://img.shields.io/badge/Latest Release-1.0.0-orange" />
    <img src="https://img.shields.io/badge/PHP-7.3+-green" />
    <img src="https://img.shields.io/badge/MySQL-5.6+-blueviolet" />
    <img src="https://img.shields.io/badge/License-MIT-blue" />
</p>

## 关于Lara Watcher

Lara Watcher是一个轻量的服务（器）状态维护平台。灵感源于模仿 Apple Services 来实时展示相关服务的运行情况。得益于 [Laravel](https://laravel.com/) 框架以及 [Dcat Admin](https://dcatadmin.com) 开发平台，使其具备了优雅、简洁的优秀体验。
Lara Watcher是完全免费且开源的，任何人都可以无限制的修改代码以及部署服务，这对于很多想要对ICT资产做信息化管理的中小型企业来说，是一个很好的选择：低廉的成本换回的是高效的管理方案，同时又有健康的生态提供支持。

系统拥有以下模块：

- 服务器定义

    - 定义属于你组织的服务器清单。

- 服务定义

    - 在你组织中所使用的所有服务，都可以列举在此，并且和服务器息息相关。

- 异常报告
    
    - 可以对服务器、服务做异常报告。

- 看板

    - 简洁的服务状态看板。

- 多国语言

    - 目前暂时最优支持中文简体，后续会发布英文语言，同时会支持语言切换。

- 私有化部署

    - 是的，只需要一个 `LNMP` 环境，就可以无限制的私有化部署。

## 最新版本

[1.0.0](https://gitee.com/famio/LaraWatcher/raw/master/releases/LaraWatcher-1.0.0.zip)

## 环境要求

`PHP 7.3 +`

`Mysql 5.6 +`

源码开发依赖于`composer`包管理器。

## 部署

### 生产环境部署

1：为你的计算机安装 `PHP` 环境，参考：[PHP官方](https://www.php.net/downloads) 。

2：为你的计算机安装 `MySQL` 或者 `mariaDB` 。

3：下载 [发行版](https://github.com/Celaraze/LaraWatcher/releases) ，解压得到程序目录，放置到你想要放置的地方。

4：在项目根目录中，执行 `php artisan watcher:install` 根据提示进行安装。

5：你可能使用的web服务器为 `nginx` 以及 `apache`，无论怎样，应用的起始路径在 `/public` 目录，请确保指向正确。

6：修改web服务器的伪静态规则为：`try_files $uri $uri/ /index.php?$args;`。

### 开发环境部署

欢迎对此感兴趣的开发者进行协同开发，使 Lara Watcher 更趋于完美。开发过程相对于简单，没有过多的环境配置和改动。

1：为你的计算机安装 `PHP` 环境，参考：[PHP官方](https://www.php.net/downloads) 。

2：安装 `composer` 包管理工具，参考：[composer官方](https://getcomposer.org/download/) 。

3：进入项目根目录，执行 `composer install`以安装相关依赖。

4：在项目根目录中，复制 `.env.example` 为 `.env`。

5：编辑 `.env` 文件中的数据库连接配置相关字段。

6：仍然在项目根目录中，执行 `php artisan migrate` 进行数据库迁移。

7：参考 [Laravel](https://laravel.com/) 以及 [Dcat Admin](https://dcatadmin.com) 相关文档进行开发。

## 截图

![](https://i.loli.net/2020/10/06/F127aoZDyfqL9lu.png)

![](https://i.loli.net/2020/10/06/rIz419blSFQvGWc.png)

![](https://i.loli.net/2020/10/06/MS3L6WgIRwtnB7O.png)

![](https://i.loli.net/2020/10/06/1pRkhiOHtPxmM62.png)

## 参与贡献

1：`Fork` 本仓库。

2：修改代码。

3：新建 `Pull Request`。

## 开源协议

Lara Watcher 遵循 MIT 开源协议。
