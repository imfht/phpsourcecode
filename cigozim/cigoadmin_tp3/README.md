# CigoAdmin

#### 项目介绍
CigoAdmin为西谷开源（CigoOS）项目组开发的通用Web后台项目，项目基于ThinkPHP3.2框架，免费开源。

项目地址
https://gitee.com/cigoos/CigoAdmin

文档地址
http://doc.cigoos.com/cigoadmin

演示地址
http://cigoadmin.cigoos.com

用户名：manager
密码：123456

#### 目录结构
```
./CigoAdminDemo                     --功能使用演示Demo
./CigoAdminLib                      --后端核心功能库
    ./Assets                        --后端资源目录
        ./bak.sql                   --项目数据库备份文件(创建项目基于此数据库进行修改)
./CigoAdminPublic                   --前端功能库及公共资源库
    ./cigoos                        --前端核心插件库
        ./cigoIScroll.js            --手机滚屏加载更多、刷新库
        ./cigoos.js                 --前端公共JS库
        ./edit.js                   --前端编辑插件库
        ./list.js                   --前端列表插件库
        ./plUpload.js               --前端文件上传插件库
        ./left-menu.js              --后台左侧功能菜单插件库
    ...                             --引用相关第三方插件库
```

#### 项目中使用说明
1. 迁出CigoAdminDemo
```
svn checkout svn://gitee.com/cigoos/CigoAdmin/CigoAdminDemo
```
2. 进入CigoAdminDemo/Application目录
3. 在步骤2目录中迁出CigoAdminLib, 并通过svn忽略此目录
```
svn checkout svn://gitee.com/cigoos/CigoAdmin/CigoAdminLib
```
4. 进入CigoAdminDemo/webroot/Public目录
5. 在步骤4目录中迁出CigoAdminPublic, 并通过svn忽略此目录
```
svn checkout svn://gitee.com/cigoos/CigoAdmin/CigoAdminPublic
```

最终目录结构如下
```
/CigoAdminDemo
    ./Application
        ./CigoAdminLib
        ...
    ./webroot
        ./Public
            ./CigoAdminPublic
            ...
        ...
        ./index.php
```


#### 开源协议

CigoAdmin遵循Apache2开源协议发布。Apache Licence是著名的非盈利开源组织Apache采用的协议，该协议和BSD类似，鼓励代码共享和尊重原作者的著作权，同样允许代码修改，再作为开源或商业软件发布。