### Qchat 服务端
> 基于 GatewayWorker 搭建的web聊天服务端

> 所须环境：nginx + PHP（需要5.3.3以上版本，和pcntl、posix扩展） + redis

#### [前端代码请点这里](https://gitee.com/qice/qchat)

### 安装步骤
1、将前端文件放入WEB目录

2、将本项目文件uploads.php也放入WEB目录，主要用来上传图片或文件的，如不需要可以不用

3、将项目文件放到任意地方，修改 Applications/chat/config/redis.php 配置文件，改为自己的redis地址，如果是本机可以不用改

4、在该目录下运行如下命令启动服务器

```
php start.php start -d
```

#### 在线体验
- [点这里（请用Chrome浏览器，网络很慢，请耐心等待）](http://420ac3fa.nat123.cc:29615/chat/) 
- 测试帐号：小一/111111、小二/111111、小三/111111、小四/111111、小五/111111

#### 聊天效果图

![聊天界面](./doc/chat_list.png "聊天界面")