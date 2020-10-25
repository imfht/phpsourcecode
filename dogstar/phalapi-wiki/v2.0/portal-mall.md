# PhalApi应用市场

PhalApi应用市场是基于PhalApi生态开发的应用市场。

## 挑选插件/应用

进入你的运营平台，进入应用市场，挑选插件/应用。

![](http://cdn7.okayapi.com/yesyesapi_20200312123139_653dc13fa6c6809ccbb80551d756f671.png)

## 下载/购买插件/应用

进入到PhalApi官方应用市场：http://www.yesx2.com/  

挑选喜欢的应用或插件，进入商品详情页。  

> 这是一个测试购买链接，可体验购买流程：http://www.yesx2.com/phalapi-dev-help  

![](http://cdn7.okayapi.com/yesyesapi_20200315205126_f060ced58f3fb5fb8566d3096c565b04.jpg)  

> 如果还没注册，请先注册并登录。  

查看插件详情，确认后，点击【立即购买】，进入购物车页面。  

![](http://cdn7.okayapi.com/yesyesapi_20200315205302_b5060b368cb6860167e8ef5d2d1c718b.png)

点击【创建订单】，进入创建订单页面。  

![](http://cdn7.okayapi.com/yesyesapi_20200315205502_ba1c5bd2220a64d8e7a446922c2e628a.png)  

填写接收插件源代码的邮箱地址，以及授权域名，以及支付方式和优惠券等，点击【支付订单】。  

![](http://cdn7.okayapi.com/yesyesapi_20200315205502_ba1c5bd2220a64d8e7a446922c2e628a.png)  

进行在线支付。  

![](http://cdn7.okayapi.com/yesyesapi_20200315210008_e8ec59c664ff335e53820d0d080fb55e.jpg)

支付成功后：  

![](http://cdn7.okayapi.com/yesyesapi_20200315210458_47d11cff8ddce7484944d62d0e257be8.png)

稍候会收到邮件，邮件里面会有插件源代码的附件。    

![](http://cdn7.okayapi.com/yesyesapi_20200315205738_330fee099f763b50249da42c3a7a512d.png)

> 温馨提示：插件源代码会以邮件附件方式发送给您。  

收到插件源代码后，下载并复制或上传到你的项目下的plugins目录。  


## 插件安装

有三种安装插件的方式：  
 + 通过界面安装
 + 通过命令安装
 + 纯手工安装

### 通过界面安装

进入你的运营平台，进入应用市场-我的应用-安装。

安装完成后，会提示安装的信息：
![](http://cdn7.okayapi.com/yesyesapi_20200312122828_01b3e0ed1ee29e80c95a7b635a9c18e7.png)

> 温馨提示：如果安装失败，请检测是否有文件和目录的写入权限。此时，可以改用脚本命令安装插件。安装插件前，需要先安装PHP的zip扩展才能正常进行解压。

### 通过命令安装

你也可以通过脚本命令来安装插件。 

```
$ php ./bin/phalapi-plugin-install.php phalapi_dev_help
正在安装 phalapi_dev_help
开始检测插件安装包 phalapi_dev_help
检测插件是否已安装
开始安装插件……
检测插件安装情况……
插件已安装：plugins/phalapi_dev_help.json
插件：phalapi_dev_help（phalapi_dev_help插件），开发者：作者名称，版本号：1.0，安装完成！
开始检测环境依赖、composer依赖和PHP扩展依赖
PHP版本需要：5.6，当前为：7.1.33
MySQL版本需要：5.3
PhalApi版本需要：2.12.2，当前为：2.12.2
开始数据库变更……
delete from `phalapi_portal_menu` where id = 294705278
insert into `phalapi_portal_menu` ( `target`, `id`, `title`, `href`, `sort_num`, `parent_id`, `icon`) values ( '_self', '294705278', 'phalapi_dev_help插件', 'page/phalapi_dev_help/index.html', '9999', '1', 'fa fa-list-alt')
插件安装完毕！
```

> 温馨提示：第一个参数是插件编号，不需要带```.zip```。安装插件前，需要先安装PHP的zip扩展才能正常进行解压。


### 纯手工安装

如果界面安装或者命令安装失败，可以直接纯手工安装。  

纯手工安装，通常只需要两步：  
 + 第1步：把插件压缩包解压到根目录。注意是解压到项目的根目录，不是plugins子目录。
 + 第2步：复制```./data/{插件编号}.sql```文件里的sql语句，到你的数据库中执行。  

这样就可以完成插件的安装。  

## 使用插件

根据不同的应用提供的功能，你就可以在你的运营平台和接口上使用应用所提供的功能和接口了。


