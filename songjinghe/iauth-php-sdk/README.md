# 北航ihome社区开放API官方PHP-SDK

北航ihome是校内的SNS社区。使用ihome开放API可以通过编程方便的调用所有ihome功能。

API开放说明：http://i.buaa.edu.cn/space.php?uid=665&do=blog&id=33881

python版SDK使用说明：http://i.buaa.edu.cn/space.php?uid=665&do=blog&id=33936 

本PHP-SDK是iauth协议中 *website client* 的一个开源实现

iauth协议是ihome参考oauth协议设计的数据权限管理协议，详见：http://i.buaa.edu.cn/space.php?uid=665&do=blog&id=33989

ihome本身是iauth协议中 *Auth server* 与 *resource provider* 的一个实现

本SDK仅用于方便开发者学习研究ihome开放API，ihome为本SDK专门注册了一个应用用于测试，如果您希望在应用大厅中开发一个应用，请到：http://i.buaa.edu.cn/plugin.php?pluginid=apps&ac=apply 申请应用。请不要使用该应用恶意编写脚本刷ihome。一旦发现该情况，ihome保留随时停用该应用的权利。

# 使用前提

- 您在本地（或其他地方）有一台支持PHP（5.3或更高版本）的web服务器。
- 您有权限修改服务器的设置和服务器管理的文件。


# 使用方法

- 下载。
- 正确的设置文件路径，使得可以通过浏览器访问http://localhost/ihome/demo_session.php
- 访问上述链接

# 文件说明

- 以iauth_开头的文件是使用iAuth协议与ihome进行数据交互的部分。
- 以demo_开头的是演示文件，展示了如何使用iauth_*文件中的几个核心函数。您可以学习并根据您的需要进行修改

# SDK工作流程
> 本部分内容对于使用SDK不是必须的，您无需理解本部分内容仍然可以使用SDK

下图中

- 【会话初始化网址】【授权回调网址】【登录回调网址】是您开发网站类应用时需要向ihome提交的三个您服务器上的URL，用于实现特定功能，对应到本SDK中则分别为

   - 【会话初始化网址】对应`demo_session.php`
   - 【授权回调网址】对应`demo_auth.php`
   - 【登录回调网址】对应`demo_login.php`

- 【iauth入口】http://i.buaa.edu.cn/plugin/iauth/login.php
- 【登录校验入口】http://i.buaa.edu.cn/plugin/iauth/getuid.php
- 【授权校验入口】http://i.buaa.edu.cn/plugin/iauth/access.php

![流程图](http://git.oschina.net/uploads/images/2014/0831/165933_fd6bf17d_19536.png)

# 使用说明
> 本部分内容对于使用SDK不是必须的，您无需理解本部分内容仍然可以使用SDK

本部分是上面的流程图的详细说明。

* STEP A：用户通过浏览器访问http://localhost/ihome/demo_session.php ，即该应用的【会话初始化网址】

* STEP B：demo_session.php会在用户浏览器设置session并产生一个与之对应的`state`参数（假设为`fedcba9876543210`），然后将用户重定向到：<http://i.buaa.edu.cn/plugin/iauth/login.php>即【iauth入口】，并带上相关参数，如`?appid=[该应用的appid]&state=fedcba9876543210`

* STEP C：用户带着相关参数访问：<http://i.buaa.edu.cn/plugin/iauth/login.php> ，该页面会要求用户登录ihome，并检测相关参数的合法性，以及用户是否已经授权该应用。如果没有授权，转到STEP D。如果已经授权，转到STEP G。

* STEP D：将用户重定向到应用大厅的授权页并显示授权页面，ihome服务器会产生一个`verifier`（如`ffffffffffffffff`）将用户重定向到 <http://localhost/ihome/demo_auth.php> ，即该应用的【授权回调网址】。并带上传入的`state`和刚产生的`verifier`参数，如`?state=fedcba9876543210&verifier=ffffffffffffffff`

* STEP E：demo_auth.php从后台向ihome发起请求完成授权。并显示从ihome得到的3个参数（用户在ihome的`uid`（假设为5633），40个字符的是`access key`，32个字符的字符串是`access secret`。就好象用户名和密码一样。你的应用用这两个参数（`access key` + `access secret`）访问ihome时，ihome就知道你要以5633用户的身份操作ihome。因此你的应用应该在授权成功后保存这三个参数。

* STEP F：至此您就可以通过`access key` + `access secret`调用ihomeAPI获得相应数据了。

* STEP G：ihome服务器会产生一个`verifier`（假设为`eeeeeeeeeeeeeeee`）并将用户重定向到http://localhost/ihome/demo_login.php ，即该应用的【登录回调网址】，并带上传入的`state`和刚产生的`verifier`参数，如`?state=fedcba9876543210&verifier=eeeeeeeeeeeeeeee`

* STEP H：demo_login.php从后台发起请求获得之前授权时的`uid`和`access key`（注意没有`access secret`），然后你可以从本地检索出`uid`和`access key`所对应的`access secret`，然后重复SETP F获得用户数据。

# 有问题

- 查看本项目wiki
- 查看搜索[ihome应用接入讨论组](http://i.buaa.edu.cn/space.php?do=mtag&tagid=1822)的置顶帖和精品帖
- 在[ihome应用接入讨论组](http://i.buaa.edu.cn/space.php?do=mtag&tagid=1822)里提问
- 使用issue
- 在ihome里私信或留言@[宋景和](http://i.buaa.edu.cn/space.php?uid=5633)
- mail to songjignhe at 163 dot com

# FAQ
- Q：部署在服务器上后无法访问ihome（通过域名i.buaa.edu.cn）
  > A：是域名解析的问题，可以通过IP211.71.14.156访问，不过iauth暂不支持通过ip的请求。
  >   因此请修改hosts文件添加一行`211.71.14.156           i.buaa.edu.cn`
  >   如果是您无法修改hosts文件（如虚拟机）请参考以下链接修改本SDK的源代码。。。
  >   http://stackoverflow.com/questions/20574860

- Q：我从哪里可以看到授权结果和日志？调用出错了怎么办？
  > A：
  >  - 授权日志及已经授权的用户信息见：http://i.buaa.edu.cn/plugin/iauth/debug/applog.php?appid=cab4d4effedabf32
  >  - 错误日志（全局，所有应用的都有）见：http://i.buaa.edu.cn/plugin/iauth/debug/apperror.php
  >  - iauth错误消息汇总及解决方案：http://git.oschina.net/songjinghe/iauth-php-sdk/wikis/iauth错误消息汇总

- Q：在应用大厅里点击授权按钮后又跳回授权页面
  > A：请先取消授权后重新授权

- Q：为什么一开始可以正常使用，过了一段时间就用不了了？
  > A：请检查您服务器的时间和北京时间严格同步，误差不得超过20秒，ihome服务器的时间与cn.pool.ntp.cn同步