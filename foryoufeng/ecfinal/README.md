# ecfinal

##  [我想先吐槽一下，你可以先看看这个](https://github.com/foryoufeng/ecfinal/wiki)

##  [项目简要说明](https://github.com/foryoufeng/ecfinal/wiki/first)

## INSTALL

### 项目要求

* apache开启url重写

* PHP版本 >=5.4 && PHP版本 <7 （原因:使用到了php的新特性 trait，所以要5.4以上，7以下是因为ecshop的mysql连接在7中被移除了）

* 使用的是ecshop的2.7版本，没用3.0，两个看起来差不多，我也懒得看3.0的代码，[源代码下载地址](https://github.com/foryoufeng/ecshop-source)

### 安装

* 请先下载 ecshop2.7版本进行安装(为了用他来安装数据库),安装完成之后拷贝它的data/config.php文件到我这个项目，如果正常的话应该就没问题了

* 本版本修改了includes/init.php和includes/cls_template.php文件

* 如有问题看具体原因吧

* 前台访问例子，现只给出了 /user.html  后台访问地址 /manage 现只给出了 /c=article这个例子，只要你们看懂了代码，我相信一切都会变的简单
![article](https://github.com/foryoufeng/ecshop-source/blob/master/%E5%B0%8FQ%E6%88%AA%E5%9B%BE-20161126212618.png)

### 升级成果

* 引入MVC模式来进行开发

* 使用pdo和提交参数的简单过滤数据更加安全

* 使用了bootstrap,界面更加丰富

### 待开源项目

* 手机端

* app接口

* erp接口


# 后记

* 由于是公司项目，而已我们对数据库和程序都有了很大的修改，所以并不会整体开源，现在只是慢慢整理一部分来进行开源，当然我也希望大家也能参与其中，来对其中的代码进行改造，
比如用mysqli来重写ecshop的mysql连接，毕竟ecshop都能改成多店铺版的，我相信用我的这个项目来做改造要轻松容易的多，而且这里面很大一部分是借鉴的thinkphp,你可以理解成一个阉割了的thinkphp，
因为它里面的很多功能都没有实现，但对于这个项目应该是够用了，希望大家 `happy coding` 了


