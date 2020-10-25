### 演示地址

[http://lendoo.leanapp.cn/manager/login](http://lendoo.leanapp.cn/manager/login)

### 安装指南

方式一 git部署
[https://my.oschina.net/huangxiujie/blog/817038](https://my.oschina.net/huangxiujie/blog/817038)

方式二 lean deploy上传
[https://leancloud.cn/docs/leanengine_quickstart.html#部署到云端](https://leancloud.cn/docs/leanengine_quickstart.html#部署到云端)

### 说明

- 在项目的/application/config/hooks.php文件中，换成自己的appid与appsecret

- 在项目的/application/third_party/wxpay/WxPay.Config.php文件中，换成自己的微信key信息

- 如果报密码错误，在_User表中，找到lendoo这个用户，将password明文录入，即可改成自己想要的密码

- 支付回调，改成自己申请到的二级域名，Line27处，`$input->SetNotify_url("https://laeser.leanapp.cn/WXPay/notify");`


### 技术栈

Codeigniter

LeanCloud

Bootstrap

Admin-LTE

jQuery

fex-webuploader

element-ui/vue

**截图**

![1-login.png](http://upload-images.jianshu.io/upload_images/2599324-55a404dac3c01799.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)
![2-dashboard.png](http://upload-images.jianshu.io/upload_images/2599324-95eaf7c4ad526974.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)
![3-goods-list.png](http://upload-images.jianshu.io/upload_images/2599324-8cc65415305ff312.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)
![4-goods-add.png](http://upload-images.jianshu.io/upload_images/2599324-629b145f9364a2bc.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)
![5-category-list.png](http://upload-images.jianshu.io/upload_images/2599324-d1d443f9a629f379.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)
![6-category-add.png](http://upload-images.jianshu.io/upload_images/2599324-4de4d7726d4d8f38.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)
![7-order.png](http://upload-images.jianshu.io/upload_images/2599324-d19908ef40000325.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)
![8-profile.png](http://upload-images.jianshu.io/upload_images/2599324-e70bd2dbdec0c616.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)

相应小程序端源码地址：[http://git.oschina.net/dotton/lendoo-wx](http://git.oschina.net/dotton/lendoo-wx)

对移动开发有兴趣的朋友可以关注我的公众号【huangxiujie85】与我交流讨论。

![公众号](https://static.oschina.net/uploads/img/201610/07111145_qD6d.jpg "二维码")