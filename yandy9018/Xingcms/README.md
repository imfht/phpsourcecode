#Xingcms说明文档

### 服务器要求



- PHP版本: >5.2.X 建议5.3
- Mysql版本（client）:>5.x.x
- 附件上传 : >2M    
- 磁盘空间：>50M


### 源码获取

- Xingcms 开源版源码全部托管于git@osc
- 开源版的更新请及时关注git@osc
- 程序源地址：https://git.oschina.net/yandy9018/Xingcms.git

###代码结构
- |——admin/ 后台管理目录
- |——cache/ 缓存控制
- |——include/ 核心文件
- |——index/ 模块控制器
- |——install/ 安装程序
- |——m/ 移动站点
- |——static/ 静态文件
- |——templates/ 模板文件
- |——templates_c/ 模板缓存文件
- |——upload/ 上传文件
- ——adm.php 后台入口文件
- ——common.inc.php 程序配置文件
- ——config.php 数据库连接文件
- ——index.php 前台入口文件
- ——logo.png 默认logo图片



### 安装

1. 将upload中的文件全部上传到网站根目录
1. 访问http://你的域名/install 按照提示进行安装
1. 进入到网站后台生成html，并且清除缓存即可
