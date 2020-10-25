  **_最近有两天没有更新程序，不是没有完善，是由于，我在进行小程序的前端的实现，先发几张图片，让大家知道我在努力中_**

 


### EasyAdmin For Thinkphp 5.1.12

#### 所有源码以及小程序前端
1.完成所有的代码以及API
2.部分功能或多有BUG，作者有其它项目开发，暂时没法处理
3.配置过后，可以自己演示
4.后一步会抽空完善接口安全以及小程序的BUG修复
5.小程序过后会将开发微信公众号--》APP


#### 添加部分功能和修复相关功能
1. 添加阿里大鱼的支持
2. 修复商品添加页的界面
3. 新添wokerwamn的支持
4. 更新数据库文件

### 最近有很多伙伴下载安装的时候报以下错误
Parse error: syntax error, unexpected '.', expecting ')' in E:\WEB\easyAdmin\vendor\composer\autoload_static.php on line 10
thinkphp5.0过后，对PHP的版本要求 **5.6** 及 **5.6** 以上，各位伙伴注意自己的PHP版本  

### 小程序的配置
关于小程序的配置，找到小程序\utils\common.js，修改AjaxUrl的地址就可以了

#### 安装说明
1. 修改数据库配置文件
```

    'database'        => '你的数据库',

    'username'        => '数据库名称',

    'password'        => '数据库密码',

    'prefix'          => 'ky_',

    @数据库默认带有前缀，前缀为ky_

```
2.修改伪静态配置
```
[ Apache ]
 <IfModule mod_rewrite.c>
  Options +FollowSymlinks -Multiviews
  RewriteEngine On

  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteRule ^(.*)$ index.php/$1 [QSA,PT,L]
</IfModule>

[ IIS ]
<rewrite>
 <rules>
 <rule name="OrgPage" stopProcessing="true">
 <match url="^(.*)$" />
 <conditions logicalGrouping="MatchAll">
 <add input="{HTTP_HOST}" pattern="^(.*)$" />
 <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />
 <add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />
 </conditions>
 <action type="Rewrite" url="index.php/{R:1}" />
 </rule>
 </rules>
 </rewrite>

[ Nginx ]
location / { // …..省略部分代码
   if (!-e $request_filename) {
   		rewrite  ^(.*)$  /index.php?s=/$1  last;
    }
}

[ Phpstudy]
<IfModule mod_rewrite.c>
Options +FollowSymlinks -Multiviews
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ index.php [L,E=PATH_INFO:$1]
</IfModule>
```
3.其它配置请参考官方文档
官方文档：https://www.kancloud.cn/manual/thinkphp5_1/353955

#### 后台模块说明
1. 后台采用AUTH权限验证，不懂的同学可以查看相关文档
2. 后台界面采用moltran+bootstarp
3. 实现用户管理，权限管理，角色管理，部门管理
4. 后续将按照商城开发。

#### 数据库和功能相关

1.新添加小程序，微信的配置
2.新添加阿里大鱼的支持
3.新添加的数据表在DATA下，将以表的形式，自动导入就行


#### 其它说明
1.作者属于个人开发，或多或少有些BUG，如发现BUG，可以 @芒果人生 作者基本在线可以回答。
2. 可以加群一起研究讨论QQ群：781216188
#### 后台登录说明
后台地址：你的网址/admin 
用户名：admin 密码：admin

#### 界面展示
![输入图片说明](https://gitee.com/uploads/images/2018/0511/184420_e0014c5a_1091193.png "1.png")
![输入图片说明](https://gitee.com/uploads/images/2018/0511/184431_69c52ec8_1091193.png "2.png")

#### 小程序界面展示

![输入图片说明](https://gitee.com/uploads/images/2018/0515/224622_00d607b6_1091193.jpeg "TIM图片20180515223625.jpg")
![输入图片说明](https://gitee.com/uploads/images/2018/0515/224632_0a977896_1091193.jpeg "TIM图片20180515223631.jpg")
#### 小程序还在努力开发当中，暂时不共享源码
因为你们拿去了，也不知道如何配置，关于小程序部分，我开发完善了会第一时间开源共享出来。


####小程序最新界面展示


![输入图片说明](https://gitee.com/uploads/images/2018/0519/104548_51f15c7d_1091193.png "TIM图片20180519104227.png")
![输入图片说明](https://gitee.com/uploads/images/2018/0519/104602_5c62c607_1091193.png "TIM图片20180519104304.png")
![输入图片说明](https://gitee.com/uploads/images/2018/0519/104612_963abdd8_1091193.png "TIM图片20180519104336.png")
![输入图片说明](https://gitee.com/uploads/images/2018/0519/104620_1ad60d05_1091193.png "TIM图片20180519104357.png")

我是一个界面控，不好看的情况下会修改，然后小程序不支持高德地图，本人喜欢高德地图，这一步也在考虑切换到腾讯地图，但或者不会换到腾讯地图，因为本实例会扩展到APP，基于腾讯地图和高德地图的确准性，大家都应该懂。

