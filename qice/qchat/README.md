### Qchat
> 一款使用VUE打造的内网办公聊天工具，支持发送图片、文件，群聊，离线消息，消息提醒；类似于WEB版微信

> 后续将升级用户头像，加入用户身份信息，加入简单的OA办公功能：打卡、申请、周报... **敬请关注**


[服务端搭建](https://gitee.com/qice/QchatServer)


#### 项目安装
先修改 index.html 文件中，wsUrl 和 uploadConfig 这两个地址，分别为WS服务器地址 及文件上传的地址
```
// 三步曲
npm install

npm run dev

npm run build
```

#### 开始聊天
- 默认管理员帐号：admin/111111 登录后记得改密码哦
- 左侧人员管理添加人员
- 后面就开始聊天啦

#### 在线体验
- [点这里（请用Chrome浏览器，网络很慢，请耐心等待）](http://free.vipnps.vip:24500/chat/) 
- 测试帐号：小一/111111、小二/111111、小三/111111、小四/111111、小五/111111

#### 效果图如下

![新增手机适配](./doc/chat_phone01.png "新增手机适配")

![新增手机适配](./doc/chat_phone02.png "新增手机适配")

![人员管理](./doc/chat_admin.png "人员管理")

![聊天界面](./doc/chat_list.png "聊天界面")

![发起群聊](./doc/chat_group.png "发起群聊")