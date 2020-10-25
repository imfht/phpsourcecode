![4ucms](https://images.gitee.com/uploads/images/2019/0731/162852_ab5564b7_566936.png)

### 标题开发环境
* PHP 5.3+
* MYSQL

### 系统预览
[http://cms.foru.net.cn/](http://cms.foru.net.cn/view/)

### 协议
MIT Lisence

### 开发者邮箱
shadowwing@163.com

### 系统搭建
* [本机搭建视频](http://cms.foru.net.cn/videoplayer/index.html)
1. 打开install/index.php进行数据导入
2. 如果需要使用smtp功能，请修改config/smtp.php内的对应信息

### 目录结构
- /addon          插件文件夹
- /admin          管理后台,默认账户密码为admin
- /config         配置项
- /editor         编辑器
- /font           字体
- /install        数据安装
- /js             前后台共用js
- /language       语言文件
- /library        函数库
- /sql            数据存储(需要读写权限)
- /template       前台模板(默认为default)
- /uploadfile     上传文件(需要读写权限)
- /view           模板切换查看
- sitemap.xml     (需要读写权限)

### 功能介绍
* [内容]菜单：频道管理，详情管理，幻灯管理，碎片管理
* [交互]菜单：会员管理，留言管理，友情链接
* [系统]菜单：系统设置，权限管理，管理员，插件管理，模板管理，数据库管理，日志管理，sitemap

### 有关版本
* 4ub 后台bootstrap的开发测试版本
* dev 正式版本
* master 原始版本
<br>我会在开发测试版使用一段时间后对正式版本进行相应的更新. 如果您迫切的想知道有什么改变了, 您可以直接使用开发测试版本, 但是相应的可能会遇到更多的bug, 开发测试版会不定时的更新
* MCV版本 只是简单的实现了MCV的模式，如果有需要的朋友可以通过这里下载：https://gitee.com/sw1981/4u
<br>目前主要进度都在MCV版本更新