# 山雨内容管理系统
## 简介
山雨内容管理系统基于THINKPHP 3.2版本制作,实现权限管理,接入短信接口,上传驱动切换(本地/七牛),全局动态参数,后台模版页自动生成(FormBuilder),封装富文本UEDITOR/上传WEBUPLOADER插件/弹出层ARTDIALOG等.

## Widget封装调用
``` php
内容: {:W('Ueditor/editor',array('content',$info['content']))}
图集: {:W('Uploader/images',array('images',$info['images']))}
```


## 后台功能截图

### 首页
![后台首页](http://git.oschina.net/uploads/images/2016/0908/164935_54804bb8_10167.png "后台首页")

### 内容编辑
![内容编辑](http://git.oschina.net/uploads/images/2016/0908/165007_f917d34a_10167.png "内容编辑")

### 全局配置
![全局配置](http://git.oschina.net/uploads/images/2016/0908/165030_8de0dded_10167.png "全局配置")

### 栏目管理
![栏目管理](http://git.oschina.net/uploads/images/2016/0908/165054_e03abc9f_10167.png "栏目管理")


## 前台截图

### 首页
![首页](http://git.oschina.net/uploads/images/2016/0908/165417_d86de572_10167.jpeg "首页")

### 列表
![列表](http://git.oschina.net/uploads/images/2016/0908/165449_e149b175_10167.png "列表")

### 内页
![内页](http://git.oschina.net/uploads/images/2016/0908/165506_eafa154a_10167.png "内页")