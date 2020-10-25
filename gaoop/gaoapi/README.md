# gaoapi

#### 介绍
用于接口管理，移除swagger对代码的侵入式开发，集成swagger-ui,将繁琐的swagger-edit编写转为通用的后台管理，支持多项目管理，支持接口变更报告，支持接口文档生成查看
亦可用于symfony4学习交流使用

#### demo
前台：
- url: http://gaoapi.gaoop.com

#### 运行环境
- php7.3
- nginx1.8
- mysql5.7
- redis5.0.7
- symfony4.4
- swagger3

#### 安装使用

1.php 运行环境准备 最好掌握一定的symfony4基础

2.下载组件 切换到项目根目录，命令行执行composer update，这一步会慢一点，最好换成国内的镜像源

3.导入数据库 /database/gaoapi.sql

4.添加配置文件 将根目录下的 .env.gaoapi 更名为 .env，并将其中的相关账户或IP地址更换成自己的配置即可，目前是设置的开发模式便于调试，可将APP_ENV=dev 改成APP_ENV=prod即为生产模式

5.因为使用了sf4的messenger功能，所以需要添加supervisor来实现进程守护功能，具体参考sf官网 https://symfony.com/doc/current/messenger.html


#### 后台截图预览
![avatar](/public/images/demo/bb11.png)

![avatar](/public/images/demo/bb22.png)

![avatar](/public/images/demo/bb33.png)

![avatar](/public/images/demo/bb44.png)

![avatar](/public/images/demo/bb55.png)

![avatar](/public/images/demo/bb66.png)

![avatar](/public/images/demo/bb77.png)

#### 前台截图预览
![avatar](/public/images/demo/f11.png)

![avatar](/public/images/demo/f22.png)

![avatar](/public/images/demo/f33.png)

