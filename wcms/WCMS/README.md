#WCMS V11
我做了基础部分，什么是基础，一个网站应该有的那些东西。
就是
登录、注册。
文章，
分类，
用户，
评论，

其他扩展的东西
多图,
日志,

因为是基础，代码很简单，所以看的明白，也方便自己DIY扩展功能，这个也是本CMS存在的意义。  


对于前端，思考了很久，还是全静态的html，直接用接口请求后台数据，这个耦合性最少。
后台做自己的事情就可以了。


他已经包含了为你写好了各种接口，你可以像java一样直接调用，譬如获取一个用户信息，可以像下面一样调用。  
$memberSer=new MemberService();  
$user=$memberSer->getMemberByUid($uid);  `

#注意事项
在chrome下默认是不开启flash的，因为用到了uploadify 所以请打开网站的flash
参考文章
https://my.oschina.net/u/554046/blog/1933837

#本地安装请修改config下的database.local.php为databse.php
登录地址http://localhost/index.php?anonymous/login
帐号15800000000 密码123456
QQ交流群425590782

#安装视频教程
http://v.qq.com/page/o/4/u/o03005g574u.html

#文件夹权限
/a  777
/cache  777
/index.html  777
/static/attached 777

#二次开放请看doc.md


API
文章分类接口
http://localhost/index.php?article/getarticlebycid/?cid=37&p=1


文章内容接口
http://localhost/index.php?article/getarticlebyid/?id=30



