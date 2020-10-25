### 项目停止维护，有问题还请各位自己解决
注：项目中部分公众号接口已过时，导致项目不可用，好像有三四个的样子。若需使用本项目，请自行调试更新相关接口的对接

---

### Welcome to the We_Questionnaire wiki!

**下面先预览一下项目图片：**

![移动端界面1](http://git.oschina.net/uploads/images/2015/0727/011700_aecb2af3_467065.jpeg "在这里输入图片标题")

![移动端界面2](http://git.oschina.net/uploads/images/2015/0727/011716_48f6b89a_467065.jpeg "在这里输入图片标题")

![后台界面1](http://git.oschina.net/uploads/images/2015/0727/011725_6a30f5a5_467065.jpeg "在这里输入图片标题")

![后台界面2](http://git.oschina.net/uploads/images/2015/0727/011734_4ba5e60e_467065.jpeg "在这里输入图片标题")

![后台界面3](http://git.oschina.net/uploads/images/2015/0727/011743_74d4341a_467065.jpeg "在这里输入图片标题")



### 项目部署流程
**前提准备:**

1. 一台在线可访问主机

2. 一个认证过的公众账号(包括服务号和订阅号,企业号由于Api略有区别,暂不考虑,具体会有什么问题未知)

3. 没有公众号的 或者 希望绕过公众号频率限制多次尝试应用功能的 可以申请公众平台测试号(申请过程很方便,测试号没有频率限制,有效期一年),申请地址 http://mp.weixin.qq.com/debug/cgi-bin/sandbox?t=sandbox/login



**配置:**

1 运行环境配置

确保php环境安装cURL拓展
由于微信公众平台的特殊机制，要求部署该应用的web服务开放在80端口
请确保web服务器支持pathinfo模式，并配置好重写规则隐藏index.php文件名(即部署为TP URL模式为rewrite 2模式下的环境)

2 数据库配置

导入应用源码根目录下的questionnaire.sql文件到你新建的数据库中

3 公众号配置

进入微信公众平台的开发者中心进行设定(https://mp.weixin.qq.com/cgi-bin/loginpage), 测试号设定请进入
http://mp.weixin.qq.com/debug/cgi-bin/sandbox?t=sandbox/login
服务器配置中的URL应设定为: "http://你的域名/Weixin/Service/listen.html", 其余选项自定义
针对认证公众号, 请先进入公众平台的高级功能菜单下开启 “开发者模式”
针对测试账号, 需要额外配置上 "网页授权获取用户基本信息" 为你的域名(不带http://)

4 应用配置

进入应用源码\Application\Common\Conf\config.php, 配置超级管理员账号和数据库
通过你的超级管理员账号进入后台 "http://你的域名/", 进入系统配置菜单, 根据你在公众平台或者测试号上的设定完成应用配置
当你确保公众平台设定和该应用中的设定一致后，请回到微信公众平台上保存一次配置以发起公众平台对应用服务器的接入验证请求


### 系统使用
1. 用户关注该公众号(作为未来可接收问卷用户群)
2. 进入应用后台的群发问卷菜单, 选择发送目标 挑选问卷 配置图文消息, 确认发送
3. 目前, 应用默认一个用户只能对一套卷子答题一次, 以后对同一套卷子的访问只能查看结果
