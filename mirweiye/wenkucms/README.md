# 七只熊文库CMS

###  **## 介绍** 

七只熊是类似百度文库,能够实现文档分享、售卖的文库CMS系统。用户上传源文档后，七只熊会自动将文档进行转码成HTML，成功后，将文档HTML返回文库CMS。实现免插件、在线浏览。
七只熊文库系统，由2个部分组成：
1. 七只熊文库CMS：开源，用于文档内容管理、用户及权限管理、积分系统等等。
2. 七只熊转换系统：用于配合文库CMS实现将office文档转换成HTML，以实现客户端在线浏览。

###  **## 联系我们** 

七只熊文库-3号QQ群： [633871890](http://shang.qq.com/wpa/qunwpa?idkey=699b850c0812329c3a6821c22bc0c1b13e21c84f230f9d40dd6a6dde998a9a1d)

联系熊二：QQ [996403 ](http://wpa.qq.com/msgrd?v=3&amp;uin=996403&amp;site=qq&amp;menu=yes)



###  **## 快速体验入口** 

文库前端演示： http://doc.qizhixiong.com

文库管理后台： http://doc.qizhixiong.com/admin.php （联系熊二获取密码：QQ [996403 ](http://wpa.qq.com/msgrd?v=3&amp;uin=996403&amp;site=qq&amp;menu=yes)）

七只熊官网：http://www.qizhixiong.com



###  **## 软件架构** 

七只熊文库系统，由2个部分组成：
1. 七只熊文库CMS： 用于文档内容管理、用户及权限管理、积分系统等。
2. 七只熊转换系统：本系统不开源。用于配合文库CMS实现将office文档转换成HTML，以实现客户端在线浏览。



###  **## CMS主要功能概述** 

1. 分类管理、文档管理、文档预览、收费文档悦读页数限制。
2. 文档积分系统。
3. 支付宝在线积分充值。
4. 用户积分策略自由设置。
5. 新闻系统。
6. 论坛 + 文档悬赏系统。
7. 企业名录系统。
8. 大批量文档客户端软件。
9. 全站广告系统。
10. 文档专辑系统。




###  **## 安装教程** 

第一步：
下载七只熊文库。访问 http://您的域名/  将会自动执行安装程序。

第二步：
进入后台 – 系统 – 站点设置，修改“网站链接”即您的站点域名，
域名后必须加上斜杠“/”，否则将导致图片显示失败，转换失败等问题。

第三步：
联系七只熊获取站点appid、appsecret ，并进入后台 – 系统 – 转换设置填写appid、appsecret。

第四步：
上传文档测试转换效果。


 **#### 伪静态** 

伪静态规则文件在程序根目录“伪静态实现”里，如遇到问题请自行百度或联系七只熊协助解决。
- Apache

```
<IfModule mod_rewrite.c>
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ index.php/$1 [QSA,PT,L]
</IfModule>
```

- Nginx

```
if (!-e $request_filename) {
   rewrite  ^(.*)$  /index.php?s=$1  last;
break;
```

