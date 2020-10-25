# DBErp

#### 介绍
DBErp 系统，是北京珑大钜商科技有限公司 基于 Zendframework 3 + doctrine 2 开发的一套进销存系统。<br>
*本软件用于非商业用途(学习、交流、研究 等)，不必支付软件版权授权费用；未获商业授权，不得在任何商业环境中使用。*
#### 本系统运行环境要求：

- 服务器系统：Linux（推荐）、Unix、Windows
- Web服务软件：Apache（推荐）、Nginx
- PHP版本：7.1及以上版本
- MySQL版本：5.7及以上版本


Web服务软件要求开启重写（Rewrite），使用Apache默认已经开启重写功能


#### PHP需要开启的扩展：
1. Curl
1. fileinfo
1. intl
1. openssl
1. PDO

#### DBErp系统安装过程：
*从码云下载的程序在本地需要运行 [composer](https://getcomposer.org/) update，可以在 https://bbs.dbshop.net/forum.php?mod=viewthread&tid=2191 下载完整系统包*
1. 使用数据库管理软件，建立DBErp需要的数据库，然后将sql目录内的dberp.sql导入数据库。
1. 在config/autoload/local.php中设置数据库连接，主要设置 40、41、42、43 行。
1. 删除 data/cache 目录下的两个php文件。
1. 将data目录及其子目录，权限设置为可读写 即 766。
1. 通过ip或者域名访问即可，访问地址是 域名(ip)/public 如果你使用域名，请在设置域名时，直接将域名指向public目录，这样就可以直接通过域名访问，而不需要在后面加上public目录了。
1. 默认登录账户是 admin 密码是 111111

*特别说明：如果您在没有设置好数据库连接信息的情况下，通过浏览器访问了系统，那么在设置好连接信息后，请重复操作 3；<br>生产环境下，请将public/index.php中的error_reporting(0)、module/Admin/view/error/ 文件下的$this->layout()->setTemplate('error/index')、$this->layout()->setTemplate('error/404') 取消注释。*

#### DBErp更新说明：
如果系统中新增加了模块，在获取到代码后，需要在本地系统目录，运行 composer dumpautoload<br>
删除 /data/cache/ 目录下以 cache.php 为后缀的两个文件


#### DBErp系统支持网址：

官方网站：https://www.dberp.com.cn<br>
官方论坛：https://bbs.dbshop.net<br>
演示DEMO：http://demo.dberp.com.cn


#### DBErp系统联系方式：
电子邮箱：support@dbshop.net<br>
QQ交流群：737830419

![QQ交流群扫码加入](https://images.gitee.com/uploads/images/2019/0629/174642_f5bf58c1_1001162.jpeg "TIM图片20190629174609.jpg")