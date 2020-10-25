# 华为云OBS Wordpress插件
![Version](https://img.shields.io/badge/Version-1.0.1-blueviolet.svg)
![Wordpress](https://img.shields.io/badge/wordpress-5.3.2-brightgreen.svg)
![PHP](https://img.shields.io/badge/PHP-7.3-orange.svg)
![License](https://img.shields.io/badge/license-GPL%203-blue.svg)
#### 插件介绍

wp-obs是一款基于华为云OBS服务的wordpress插件，主要用于将文章中使用到的本地媒体文件和网络媒体文件，自动上传至华为云OBS。

#### 功能特性

1. 支持本地文件上传远端OBS
2. 非本地文件在发布时修改文件链接，并上传到远端OBS（当前版本仅支持图片）


#### 安装教程

wp-obs插件还未在wordpress上架，目前安装方法通过下载插件，后台上传的方式安装
1. 下载插件：
    - 使用git Clone，请将clone后的文件夹重命名为wp-obs后，使用工具上传至wp-content/plugins目录下
    - 通过https下载，请将下载后的文件解压并重命名为wp-obs后，使用工具上传至wp-content/plugins目录下
2. 安装插件：
    - 登录wordpress后台，启用华为云对象存储服务OBS插件

#### 插件设置

1. Bucket：填写在华为云OBS控制台上创建的Bucket名称，注意：桶策略需要设置为**公共读**
2. accessKey：访问密钥，请参考[创建访问密钥]: https://support.huaweicloud.com/usermanual-ca/zh-cn_topic_0046606340.html
3. secretKey：访问密钥，请参考[创建访问密钥]: https://support.huaweicloud.com/usermanual-ca/zh-cn_topic_0046606340.html
4. Endpoint：OBS所在区域
5. Bucket域名设置: 如果绑定了独立域名，请在此处填写域名地址，请注意以**http://**或**https://**开头
6. 是否上传传缩略图：发布文章时，系统会自动生成缩略图，选中说明要上传缩略图到OBS，否则，不上传
7. 是否本地保留备份：选中，发布文章后悔删除本地存储的原始媒体文件，否则，本地保留
   
**说明**

1. 本插件未提供**本地文件**和**远端文件夹**的设置，发布文章时插件会自动按照归档设置，在远端OBS上创建和本地目录一致的文件夹存储媒体文件
2. 建议在OBS上单独创建一个桶用于存储媒体文件，不和其他文件混用
3. 删除本地文件时，会同步删除远端OBS文件
4. <font color=red>**OBS AK 和 SK 在设置页面将会以明文的形式显示，请注意站点安全**</font>
5. 发布文章后，编辑器中外部链接不是最新链接，如果要更新文章（第一次更新），请通过菜单“编辑文章”的方式进入重新编辑

#### 参与贡献

此插件的开发，大量参考了**yangtoude**的**wp-bos**插件及**Luffy**的**wordpress-qcloud-cos**插件，在此表示特别感谢！



