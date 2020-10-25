<div align="center">
    <img src="http://chemex.it/assets/images/logo.png"/>
    <h2>Chemex</h2>
</div>

<p align="center">
<a href="http://chemex.it" target="_blank">Chemex 官方网站</a> |
<a href="https://chemex.famio.cn" target="_blank">Demo 演示站点</a>
</p>

<p align="center">
    <img src="https://img.shields.io/badge/Latest Release-1.4.3-orange" />
    <img src="https://img.shields.io/badge/PHP-7.3+-green" />
    <img src="https://img.shields.io/badge/MariaDB-10.5+-blueviolet" />
    <img src="https://img.shields.io/badge/License-GPL3.0-blue" />
</p>

## 关于Chemex

Chemex是一个轻量的、现代设计风格的ICT设备资产管理系统。得益于 [Laravel](https://laravel.com/) 框架以及 [Dcat Admin](https://dcatadmin.com) 开发平台，使其具备了优雅、简洁的优秀体验。
Chemex是完全免费且开源的，任何人都可以无限制的修改代码以及部署服务，这对于很多想要对ICT资产做信息化管理的中小型企业来说，是一个很好的选择：低廉的成本换回的是高效的管理方案，同时又有健康的生态提供支持。

系统拥有以下模块：

- 设备台账管理

    - 其中包含了设备的名称、所有软硬件、制造商、购入日期、保护日期、IP地址、MAC、使用者等维护内容，同时拥有设备相关历史记录。
    
    - 支持在线 SSH 远程访问管理设备。

- 硬件台账管理

    - 其中包含了硬件的名称、规格、序列号、归属设备管理等维护内容，同时拥有硬件相关历史记录。

- 软件台账管理
    
    - 其中包含了软件的名称、版本、分发方式、授权方式、购入金额、序列号、授权数量管理等维护内容，也有软件相关历史记录。

- 雇员管理

- 服务管理
    
    - 其中包含了服务所在的宿主服务器、服务状态、异常报告等。
    
    - 服务异常的修复。
    
    - 首页特别的看板。

- 盘点管理
    
    - 设备、硬件、软件盘点任务的创建、完成和取消。
    
    - 盘盈盘亏。
    
    - 指定盘点负责人员。

- 数据图表

    - 各模块的基础数据。
    
    - 各服务状态实时展示，包括异常内容，发生时间和恢复时间。

- 多国语言

    - 目前暂时最优支持中文简体，后续会发布英文语言，同时会支持语言切换。

- 私有化部署

    - 是的，只需要一个 `LNMP` 环境，就可以无限制的私有化部署。
    
## 开发计划

|序号|项目|状态|优先级|
|----|----|----|----|
|1|制造商管理基础|✔|紧急|
|2|雇员管理基础|✔|紧急|
|3|硬件管理基础|✔|紧急|
|4|软件管理基础|✔|紧急|
|5|设备管理基础|✔|紧急|
|6|盘点管理基础|✔|紧急|
|7|自动生成二维码|✔|一般|
|8|扫描二维码查看信息|❌|一般|
|9|硬件归属|✔|紧急|
|10|软件归属|✔|紧急|
|11|软件授权数量管理|✔|一般|
|12|软件解除归属|✔|一般|
|13|历史履历|✔|紧急|
|14|操作人员全记录|✔|紧急|
|15|简易部署|➖|紧急|
|16|图表基础|✔|一般|
|17|图表更多的优化|✔|一般|
|18|移动端盘点|❌|一般|
|19|数据库导出（备份）|❌|紧急|
|20|人性化的站点配置|❌|一般|
|21|服务管理基础|✔|一般|
|22|服务状态看板|✔|一般|
|23|设备在线SSH管理|✔|一般|
|24|维修管理基础|✔|紧急|

## 环境要求

`PHP 7.3 +`

`MariaDB 10.5 +`

源码开发依赖于`composer`包管理器。

## 部署

### 生产环境部署

生产环境下为遵守安全策略，我们非常建议在服务器本地进行部署，暂时不提供相关线上初始化安装的功能。因此，虽然前期部署的步骤较多，但已经为大家自动化处理了很大部分的流程，只需要跟着下面的命令一步步执行，一般是不会有部署问题的。

1：为你的计算机安装 `PHP` 环境，参考：[PHP官方](https://www.php.net/downloads) 。

2：为你的计算机安装 `mariaDB` ，并且有可以使用的 `mariadb-client` 客户端工具，一般安装完 `MariaDB` 会自动安装，如果在 Ubuntu 上可能需要另外执行 `sudo apt install mariadb-client` 进行安装。

3：创建一个数据库，命名任意，但记得之后填写配置时需要对应正确，并且数据库字符集为 `utf8-general-ci`。

4：下载 [发行版](https://gitee.com/celaraze/Chemex/releases) ，解压得到程序目录，放置到你想要放置的地方。

5：在项目根目录中，复制 `.env.example` 文件为一份新的，并重命名为 `.env`。

6：在 `.env` 中配置数据库信息。

7：执行 `php artisan chemex:install` 进行安装。

8：你可能使用的web服务器为 `nginx` 以及 `apache`，无论怎样，应用的起始路径在 `/public` 目录，请确保指向正确。

9：修改web服务器的伪静态规则为：`try_files $uri $uri/ /index.php?$args;`。

### 开发环境部署

欢迎对此感兴趣的开发者进行协同开发，使 Chemex 更趋于完美。开发过程相对于简单，没有过多的环境配置和改动。

1：为你的计算机安装 `PHP` 环境，参考：[PHP官方](https://www.php.net/downloads) 。

2：安装 `composer` 包管理工具，参考：[composer官方](https://getcomposer.org/download/) 。

3：进入项目根目录，执行 `composer install`以安装相关依赖。

4：在项目根目录中，复制 `.env.example` 为 `.env`。

5：编辑 `.env` 文件中的数据库连接配置相关字段。

6：仍然在项目根目录中，执行 `php artisan migrate` 进行数据库迁移。

7：参考 [Laravel](https://laravel.com/) 以及 [Dcat Admin](https://dcatadmin.com) 相关文档进行开发。

### 更新

下载最新的 Release 包，覆盖文件到根目录即可，其它可能的配置修改参考 Release 说明。

## 截图

![](https://oss.celaraze.com/cache/qnGfHILR.png)

![](https://oss.celaraze.com/cache/MpWykr4L.png)

![](https://oss.celaraze.com/cache/7RLZFaul.png)

![](https://oss.celaraze.com/cache/N67KSqQk.png)

![](https://oss.celaraze.com/cache/6UR87L3n.png)

## 参与贡献

1：`Fork` 本仓库。

2：修改代码。

3：新建 `Pull Request`。

## 开源协议

Chemex 遵循 [GPL3.0](https://www.gnu.org/licenses/gpl-3.0.html) 开源协议。
