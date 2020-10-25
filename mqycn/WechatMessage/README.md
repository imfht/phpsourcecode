# WechatMessage

#### 项目介绍
微信消息处理类

#### 开发说明


参考 demo/message/WechatMessageApp.php，直接重写对应的方法就可以了。

收到 订阅(onSubscribeEvent)，返回 你好，非常感谢您的订阅。

收到 文本信息，增加 [自动回复] 和 内容原样输出。

收到 图片信息，为了演示图片功能，直接用户发啥给回啥。

```
class WechatMessageApp extends WechatMessageCommon {
	protected function onSubscribeEvent() {
		$msg = "你好，非常感谢您的订阅。\n\n";
		return $this->textMessage($msg);
	}
	protected function onTextMessage($content) {
		return $this->textMessage("[自动回复]${content}");
	}
	protected function onImageMessage($image, $media_id) {
		return $this->imageMessage($media_id);
	}
}
```

如果 没有 重写 消息事件，会回复 不支持的消息加接收到的参数，方便调试，当然也可以通过重写 onOtherMessage 的方法引导用户操作

```
class WechatMessageApp extends WechatMessageCommon {
	protected function onOtherMessage($event_type, $argument = array()) {
		return $this->textMessage("不支持的消息，请回复\n1:XXX\n2:XXX");
	}
}
```


#### 消息类型和对应的方法

| 消息事件 | 需要重写的方法 | 
| :-: | - |
| 用户订阅 | `onSubscribeEvent()` |
| 文字消息 | `onTextMessage($content)` |
| 图片消息 | `onImageMessage($image，$media_id)` |
| 语音消息 | `onVoiceMessage($media_id，$format，$to_text)` |
| 视频消息 | `onVideoMessage($media_id，$media_thumb_id)` |
| 分享消息 | `onLinkMessage($title，$desc，$url)` |
| 文件上传 | `onFileMessage($filename，$desc，$file_key，$file_md5，$file_size)` |
| 位置信息 | `onLocationMessage($address，$lat，$lng，$scale)` |
| 进入客服界面(小程序) | `onUserEnterTempsessionEvent()` |

| 回复类型 | 回复的方法 | 
| :-: | - |
| 文字消息 | `textMessage($content)` |
| 图片消息 | `imageMessage($media_id)` |
| 语音消息 | `voiceMessage($media_id)` |
| 视频消息 | `videoMessage($media_id，$title = ''，$desc = '')` |
| 分享消息 | `linkMessage($articles = array())` |


公众号没有认证，只能回复文本信息、分享消息，因为 图片、语音、视频 需要用到上传媒体资源的接口。（可能有的朋友会抬杠，图片信息能获得媒体ID、这样客户实现客户给你发啥图片，你就回复给他图片，但是这样没啥意义）

小程序 可以支持所有消息类型（测试中，暂时没有提交到gitee）

分享信息的 $articles 创建的方法：

```
$articles = array(
    $this->linkMessageArticleItem($title, $url, $image, $desc),
    $this->linkMessageArticleItem($title, $url, $image, $desc),
    $this->linkMessageArticleItem($title, $url, $image, $desc)
);
```



#### 使用说明（使用测试工具）

**1、下载本源码并部署到PHP的WEB环境 **

将下载的脚本安装到服务器后，比如：http://您的域名/安装路径/

测试工具的地址为： http://您的域名/安装路径/test/index.html

如果 仅 需要测试，可以直接访问在线测试地址：[http://wechatmessage.demo.miaoqiyuan.cn/test/](http://wechatmessage.demo.miaoqiyuan.cn/test/)

**2、选择消息类型，填写参数，点击立即测试，即可看到服务器执行的信息 **

这样 就可以不使用微信，直接开发了。

![演示](https://images.gitee.com/uploads/images/2019/0107/160334_d3000009_82383.gif "dev.gif")


#### 使用说明（真机测试）

**1、申请测试帐号**

打开测试接口页面，[https://mp.weixin.qq.com/debug/cgi-bin/sandbox?t=sandbox/login](https://mp.weixin.qq.com/debug/cgi-bin/sandbox?t=sandbox/login)

![输入图片说明](https://images.gitee.com/uploads/images/2019/0104/161704_fbff2508_82383.png "1.png")

点击 登录 按钮，使用微信扫码登陆。

**2、填写 测试脚本的URL **

将下载的脚本安装到服务器后，比如：http://您的域名/安装路径/

在 接口配置信息中，填写 测试地址为： http://您的域名/安装路径/demo/message.php 访问

如果不想自己配置，可以使用 在线测试地址：http://wechatmessage.demo.miaoqiyuan.cn/demo/message.php

![输入图片说明](https://images.gitee.com/uploads/images/2019/0104/162114_2c759830_82383.png "2.png")


**3、关注 测试号二维码 **

![输入图片说明](https://images.gitee.com/uploads/images/2019/0104/162348_24e78c53_82383.png "3.png")

关注 测试号二维码，进入 公众号 聊天窗口，输入内容即可测试。

![输入图片说明](https://images.gitee.com/uploads/images/2019/0104/162928_a77ffa95_82383.jpeg "4.jpg")

默认 不支持的消息模式，会如下图显示：

![输入图片说明](https://images.gitee.com/uploads/images/2019/0104/171228_26312f74_82383.jpeg "111.jpg")
