#iWshop<微信开源商城>

iWshop是一个开源的微信商城。为了保证轻量级，使用了作者自主开发的mvc框架。 基于腾讯Weui开发店铺页面。

<a href="https://www.youdians.com/?from=osc"><img src="https://cdn.youdianx.com/images/youdian/1010-100.jpg"/></a>

<a href="https://www.youdians.com/?from=osc" target="_blank">点击体验云上有店</a>

>iWshop 交流群：`569523271`

>iWshop 交流群：`470442221`

>iWshop 交流社区：<a href="https://asw.iwshop.org/" target="_blank">https://asw.iwshop.org/</a> 

>iWshop 安装教程：<a href="http://git.oschina.net/koodo/iWshop/blob/dev/html/docs/install.md">http://git.oschina.net/koodo/iWshop/blob/dev/html/docs/install.md</a>

>`历史版本发布` <a target="_blank" href="http://git.oschina.net/koodo/iWshop/releases">http://git.oschina.net/koodo/iWshop/releases</a>

##配置说明 （请注意，iWshop当前稳定版正在开发中，请谨慎安装）

####一、根目录修改说明


如果是服务器根目录，必须修改为` /`


如果是服务器子目录，必须修改为`/subroot/` 等有左右斜杠的格式


修改文件：config/config.php 中的 $config->docroot选项

####二、手动部署说明

- 服务器配置过程略。

- 创建`/install/install.lock`

- 导入`/install/iWshop.sql`

- 后台地址（以域名www.iwshop.cn为例）为：http://www.iwshop.cn/?/Wdmin/login/

- 微信消息接口地址：http://www.iwshop.cn/wechat/ (切莫忘了最后的 / )

####三、目录权限说明

执行一下install里的shell文件

    ./install/dirmod.sh

请确保您的php配置中`magic_quotes_gpc为Off`，否则一些功能将失效

####四、配置文件config.php说明

初始配置文件为`config_sample.php`, 在/config/目录下

请编辑`config_sample.php`文件并且重命名为`config.php`

####五、运行环境要求
 
    * MySQL 5.5.3+ (utf8mb4编码用于保存带有emoji表情的微信用户昵称)**

    * PHP5.4+

    * PHP扩展：php_mysql php_curl php_pdo_mysql php_gd2


####六、演示项目

<img src="https://cdn.iwshop.org/release/qrcode8.jpg" height="120" width="120" style="float:left;border:1px solid #eee;margin-right:10px;" />
<img src="https://cdn.iwshop.org/release/qrcode4.jpg" height="120" width="120" style="float:left;border:1px solid #eee;margin-right:10px;" />
<img src="https://cdn.iwshop.org/release/qrcode5.jpg" height="120" width="120" style="float:left;border:1px solid #eee;margin-right:10px;" />
<img src="https://cdn.iwshop.org/release/qrcode7.jpg" height="120" width="120" style="float:left;border:1px solid #eee;margin-right:10px;" />

####六、后台预览

<img src="https://cdn.iwshop.org/release/previews1.png" width="100%" />
<img src="https://cdn.iwshop.org/release/previews2.png" width="100%" />
<img src="https://cdn.iwshop.org/release/previews3.png" width="100%" />

## 相关下载

><a href="https://cdn.iwshop.org/release/iwshop-beta-0.9.0.zip" target="_blank">iwshop-beta-0.9.0.zip</a> 

><a href="https://cdn.iwshop.org/release/iwshop-beta-0.9.1.zip" target="_blank">iwshop-beta-0.9.1.zip</a> 

><a href="https://cdn.iwshop.org/release/iwshop-beta-0.9.2.zip" target="_blank">iwshop-beta-0.9.2.zip</a> 

><a href="https://cdn.iwshop.org/release/iwshop-beta-0.9.3.zip" target="_blank">iwshop-beta-0.9.3.zip</a> 

><a href="https://cdn.iwshop.org/release/iwshop-beta-0.9.4.zip" target="_blank">iwshop-beta-0.9.4.zip</a>

><a href="https://cdn.iwshop.org/release/iwshop-beta-0.9.5.zip" target="_blank">iwshop-beta-0.9.5.zip</a>
