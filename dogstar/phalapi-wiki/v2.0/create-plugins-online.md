# 在线生成插件

## 进入在线生成插件界面

直接访问：http://www.yesx2.com/customer/account/createplugin

或者，通过PhalApi应用市场的入口点击进入。  

![](http://cdn7.okayapi.com/yesyesapi_20200315161148_c28aebed1430a2542cd5794ee4412f9d.png)  

或右上角入口：  
![](http://cdn7.okayapi.com/yesyesapi_20200315161235_979df3a18d98085437657f46791665e4.png)

## 填写你的插件信息

根据表单，填写你需要创建的插件信息。  
![](http://cdn7.okayapi.com/yesyesapi_20200315161339_85f5e13eb1b4cf6c824fcbcaed61fcd3.png)  

最后，点击【生成插件】。  

## 下载插件

例如，创建一个插件编号为：```test```的插件，提交后，会看到下载的链接。

![](http://cdn7.okayapi.com/yesyesapi_20200315161509_812ddb8b4c767faf5c237685363ea09c.png) 

下载后，将会得到zip压缩包。  
![](http://cdn7.okayapi.com/yesyesapi_20200315161609_33e2e39a03f54321e80d52635dd2becc.jpg)  

解压后将得到：  
![](http://cdn7.okayapi.com/yesyesapi_20200315161648_34a227f3f628285070def97da5ef5a5d.png) 

## 安装插件

将下载的插件压缩包，复制到你项目的下的plugins目录。  

![](http://cdn7.okayapi.com/yesyesapi_20200315161816_bfa33eed5f7347caae4b59b6437772e4.png) 

最后，在命令终端使用以下命令安装：  

```
$ php ./bin/phalapi-plugin-install.php test
正在安装 test
开始检测插件安装包 test
检测插件是否已安装
插件已安装：plugins/test.json
开始安装插件……
检测插件安装情况……
插件已安装：plugins/test.json
插件：test（test插件），开发者：作者名称，版本号：1.0，安装完成！
开始检测环境依赖、composer依赖和PHP扩展依赖
PHP版本需要：5.6，当前为：7.1.33
MySQL版本需要：5.3
PhalApi版本需要：2.12.2，当前为：2.12.2
开始数据库变更……
delete from `phalapi_portal_menu` where id = 434707799
insert into `phalapi_portal_menu` ( `target`, `id`, `title`, `href`, `sort_num`, `parent_id`, `icon`) values ( '_self', '434707799', 'test插件', 'page/test/index.html', '9999', '1', 'fa fa-list-alt')
插件安装完毕！
```

你也可以进入你的Portal运营平台进行界面安装。  

## 插件开发

安装好插件好，你就可以进行本地插件开发了。  


