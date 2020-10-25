# wxscanlogin
微信扫码登录，如果有服务号，可以在配置后搭建一个微信扫码登录Web站的演示  
[效果](http://wxinfo.datatiny.com/scanwxlogin/wxlogin.html)

步骤：
1.需要准备一个认证的微信服务号，如果没有并且想使用，可以加QQ群：17924450  
或者用QQ扫描下面的二维码，后续可能做个给没有服务号并且想使用扫码登陆的朋友   
![数据微群](qqqun.png)

2.进入微信公众后台，“设置”->“公众号设置”->“网页授权域名”，填写自己的域名

3.将config.ex.php改名为config.php,编辑,填入自己的appid和AppSecret  

计划：  
1.目前只完成了基于session，后续将完成基于memcached的微信扫码登录  
2.一个中继服务，给没有微信服务号的朋友提供一个扫码登录系统.  

反馈：
1.issue  
2.QQ群：17924450 
