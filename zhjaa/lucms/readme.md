## lucms

## lucms

`lucms` 意为「撸一个cms」

更详细文档请参考网站： [Code好事](https://codehaoshi.com)

更强版本 lucmsee 请转至 https://gitee.com/zhjaa/lucmsee

### 版本与协议

版权遵循 [MIT](https://baike.baidu.com/item/MIT/10772952#viewPageContent) 开源协议，以下为基于 lucms 的解释协议。

1、您可以在完全遵循本协议的情况下，将 lucms 用于商业用途，而不必支付使用费用，但我们也不承诺会对非赞助用户提供任何形式的技术支持；

2、使用 lucms 您可以不用在明显页面保留程序版权信息，但程序最终版权仍归原作者所有，为了程序能持续发展建议您在网站底部注明：powered by lucms，另外我们可能不会对未保留版权信息的用户提供任何无偿的技术支持；

3、非授权用户后台版权与程序内版权信息不可以去除，这是我们唯一可以保护自己权益的地方；

4、您可以免费使用 lucms ，修改源代码或界面风格以适应您的实际要求，但是禁止对软件进行改名发布，禁止以任何形式对 lucms 形成竞争；

5、您可以对 lucms 进行二次开发，但禁止重新分发任何在 lucms 的整体或任何部分基础上发展的派生版本、修改版本或第三方版本，可以自己分配使用版权请参考第四条；

6、如果您在您的程序如引用 lucms 的功能或者设计，请在明显的地方如官网等地方加入本设计或功能采用 lucms ；

7、自您开始使用 lucms 时本协议已自动生效；

8、如果您未能遵守本协议的条款3、条款4、条款5、条款6，您的免费使用授权将被终止，并将依法承担相应法律责任。

### 愿景

能快速搭建出一个后台管理系统、接口系统

希望能达到如下预期

- 代码简浩、优雅、规范化
- 自己用着顺手
- 包括大部分后台管理系统需要的基本功能
- 用户体验好
- 安全
- 高效
- 加入一些前沿技术

### 加入

一个人的力量终究是有限的。欢迎大家加入贡献 ！


### 感谢~

感谢 laravel 框架开发团队。

感谢 iview 开发团队，`lucms` 前端采用了 iview 来开发。

在开发过程中，采用了大量的第三方库，感谢这些开发团队。

## 项目概述

- 产品名称：lucms
- demo: http://lucms.codehaoshi.com/dashboard  账号：dev@lucms.com  密码： 123456

lucms 是一个基于 `laravel5.5*` 与 `iviewjs` 开发的一套后台管理系统。


## 功能如下

- 用户认证 —— 登录、退出
- 用户认证 —— 多表登录
- 用户管理 —— 头像上传、资料修改
- 权限系统 —— 多角色、多权限管理
- 附件管理 —— 服务器文件管理
- 新闻系统 —— 基础新闻管理
- 系统安全 —— 日志记录、ip 过滤
- Excel 导入导出
- Markdown 编辑器支持
- Wangeditor 编辑器支持

## 开发环境部署/安装

本项目代码使用 PHP 框架 Laravel 5.5 开发，本地开发环境使用 Laravel Homestead。

下文将在假定读者已经安装好了 Homestead 的情况下进行说明。如果您还未安装 Homestead，可以参照 Homestead 安装与设置 进行安装配置。

### 基础安装

- 克隆源代码

克隆 lucms 源代码到本地：

> git@gitee.com:zhjaa/lucms.git

- php 配置修改

1). 配置本地环境，根目录指向 `public`

2). 安装 composer
```html
composer install
```

2). 生成配置文件
```html
cp .env.example .env
你可以根据情况修改 .env 文件里的内容，如数据库连接、缓存、邮件设置等：
```

2). 目录访问权限配置

```text
  $ chmod -R 777 storage
```


3). 配置 .env  ，修改数据库信息 . ....
```sh
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:IKlBaIonliiolP7yK0QWP8Ixwgc1Z5R2ylxEA6CD3nA=
APP_DEBUG=true
APP_LOG_LEVEL=debug
APP_URL=http://lucms.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lucms
DB_USERNAME=root
DB_PASSWORD=route
.

.

.
QUEUE_DRIVER=sync 「同步模式，不使用任何队列」 => redis

.

.

.
```

4). 生成数据表及生成测试数据

```sh
$ art migrate
$ art db:seed
```

5). 安装 passport 客户端, vue api 请求 token 认证要用到
```sh
 $ art passport:install
 
 # 以下内容复制到 .env 中
 Client ID: 2
 Client Secret: qtbbnoYSKM1QkAfbcs614iCiWmMvBWNdRloJNbDi

```

6). 配置 .env  ，修改数据库信息 . ....
```sh
.

.

.
OAUTH_GRANT_TYPE=password
OAUTH_CLIENT_ID=2
OAUTH_CLIENT_SECRET=p7XaeU3D9RASxQ18eiF5CT1uL9xUJRYjT6O8BJFt
OAUTH_SCOPE=*

.

.

.
```


7). 生成密钥
```html
art key:generate
```

8). 设定图片上传软链接 `storage/app/public/*` 到 `public/storage/images`
```
$ cd public
$ mkdir storage
$ ln -s /srv/wwwroot/homestead-code/lucms/storage/app/public/* ./storage/
```


- 修改 js 配置

1). 全局修改基本域名 http://lucms.test => https://xxxxx

```html
cp  lu/example.vue.config.js lu/vue.config.js
cp  lu/config/example.env.js lu/config/env.js
cp  lu/config/example.url.js lu/config/url.js
```

`lucms/lu/vue.config.js`
```js
const BASE_URL = env === 'development'
  ? '/iview-admin/'
  : 'https://lucms.com/lu/dist/'
```

`lucms/lu/config/url.js`
```js
const DEV_URL = 'http://lucms.test/'
const PRO_URL = 'https://lucms.com/'
```


### vuejs 安装与运行

1). 开发环境
```
$ cd lu
$ cnpm install
$ npm run dev
```

2). 生产环境
```
$ cd lu 
$ npm run build
```

## 扩展包使用情况

| 扩展包	| 一句话描述	| 本项目应用场景|
| --- | --- | --- |
| [laravel/passport](https://github.com/laravel/passport)     | jwt 用户认证包          | api 登录认证|
| [Intervention/image](https://github.com/Intervention/image)     | 图片处理包     | 图片上传裁剪|
| [laravel-permission:~2.7](https://github.com/spatie/laravel-permission)     | 权限管理包     | 权限管理|
| [mews/purifier](https://github.com/mewebstudio/Purifier)     | xss过滤     | 富文本编辑器|
| [overtrue/pinyin](https://github.com/overtrue/pinyin)     | 基于 CC-CEDICT 词典的中文转拼音工具     | 文章 seo 友好的 url|
| [nrk/predis](https://github.com/nrk/predis)     | redis 队列驱动器     | 队列管理 |
| [laravel/horizon](https://laravel-china.org/docs/laravel/5.5/horizon/1345)     | 队列监控     | 队列监控 |
| [rap2hpoutre/laravel-log-viewer](https://github.com/rap2hpoutre/laravel-log-viewer)     | laravel 日志查看     | 查看日志 |
| [aliyuncs/oss-sdk-php](https://help.aliyun.com/document_detail/32099.html?spm=5176.87240.400427.47.CtLkv4)     | 啊里云 oss     | 对象存储 |
| [overtrue/easy-sms](https://github.com/overtrue/easy-sms)     | 短信发送     | 找回密码 |
| [barryvdh/laravel-cors](https://github.com/barryvdh/laravel-cors)     | 跨越解决     | 开发环境方便测试 |
| [league/html-to-markdown](https://github.com/thephpleague/html-to-markdown)     | markdown 转 html     | 富文本编辑器 markdown 支持 |
| [Maatwebsite/Laravel-Excel](https://github.com/Maatwebsite/Laravel-Excel)     | excel 处理     | 导入导出 excel |


## 队列

| Jobs | 一句话描述|
|--- | --- |
| TranslateSlug | 翻译文章 title |


