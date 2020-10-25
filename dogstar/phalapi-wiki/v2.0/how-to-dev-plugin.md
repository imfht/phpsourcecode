# 第三方应用插件开发教程

## 前方高能

开发第三方应用插件有哪些好处？  
 + 首先，可以更大化满足自己项目的开发需求，实现自己的产品。
 + 其次，你可以将开发好的应用插件放到PhalApi官方的应用市场进行销售，通过自己努力编写的代码和插件应用赚取收入。
 + 最后，在开始之前，需要使用PhalApi 2.12.2 及以上版本，且参考以下教程。

## 应用插件开发整体流程

开发一款第三方应用插件，主要开发流程如下：  

![](http://cdn7.okayapi.com/yesyesapi_20200312120956_eb710650243d8f36ee73453fe537e997.png)

主要步骤有：  
 + 第1步：创建一个你自己的插件
 + 第2步：本地编程，开发你的插件
 + 第3步：打包你的插件
 + 第4步：发布你的插件

下面通过demo插件示例来演示和进行讲解。

##  第1步：创建一个新的插件

使用脚本命令```bin/phalapi-plugin-create.php```，可以快速创建一个PhalApi插件骨架。  

在根目录，执行脚本命令：  
```
$ php ./bin/phalapi-plugin-create.php
```
需要提供参数：
 + 第一个参数：插件编码，需要全网唯一，由母数字和下划线组合，例如：```demo```、```dogstar_demo```、```my_erp```

会看到使用帮助：  
```
$ php ./bin/phalapi-plugin-create.php
Usage: ./bin/phalapi-plugin-create.php <plugin_key>
请输入你的插件编号，字母数字和下划线组合。
例如：./bin/phalapi-plugin-create.php plugin_demo
```

我们以开发一个新的插件，插件编号为：```demo```，执行创建命令：  
```
[phalapi]$ php ./bin/phalapi-plugin-create.php demo
开始生成插件json配置文件……
/home/apps/projects/phalapi/plugins/demo.json json配置文件生成 ok 

开始创建插件文件和目录……
/path/to/phalapi/public/../config/demo.php... 
/path/to/phalapi/public/../plugins/demo.php... 
/path/to/phalapi/public/../data/demo.sql... 
/path/to/phalapi/public/../public/portal/demo... 
/path/to/phalapi/public/../public/portal/page/demo/index.html... 
/path/to/phalapi/public/../src/app/Api/Demo/Main.php... 
/path/to/phalapi/public/../src/app/Domain/Demo/Main.php... 
/path/to/phalapi/public/../src/app/Model/Demo/Main.php... 
/path/to/phalapi/public/../src/portal/Api/Demo/Main.php... 
ok 

开始添加运营平台菜单……
demo插件菜单添加 ok 

恭喜，插件创建成功，可以开始开发啦！
```

这时候，就已经生成了一个基本的插件。下面来看下插件应用包含哪些部分。

首先，在在线接口文档列表页，会自动有以下新的API接口，具体功能需要自己开发实现。  
![](http://cdn7.okayapi.com/yesyesapi_20200312113825_b85a58f3f4857099b35d579ccc4c4e72.png)

同时，在运营平台的接口里，已经生成相应的数据接口，只需要简单替换成自己的数据库表，即可使用。
![](http://cdn7.okayapi.com/yesyesapi_20200312113942_474fe47187bfa9b20a7bded372b72cef.png)

进入运营平台后，已经自动生成了一个新的菜单，并且有一个默认的模板页面，具体功能需要自行完善。
![](http://cdn7.okayapi.com/yesyesapi_20200312114036_a9acdd694358985bce676f66309b968b.png)

同时，在运营平台的应用市场，进入【我的应用】，可以看到刚才的插件已经是安装成功。  
![](http://cdn7.okayapi.com/yesyesapi_20200312114225_d807261e4f599a1eb3963e20a60652e6.png)

新增的代码，通过git比较，发现新的文件和目录有：  

```
# Untracked files:
#   (use "git add <file>..." to include in what will be committed)
#
#       config/demo.php
#       data/demo.sql
#       plugins/demo.json
#       plugins/demo.php
#       public/portal/page/demo/
#       src/app/Api/Demo/
#       src/app/Domain/Demo/
#       src/app/Model/Demo/
#       src/portal/Api/Demo/
```

接下来，你就可以开发自己的插件啦！

## 插件应用包含哪些模块？

通过上面的脚本命令```bin/phalapi-plugin-create.php```，我们已经生成了demo插件的代码。并且，通过这个demo插件，总结一下插件模块需要提供哪些模块、代码和内容。

一个插件应用，通常需要包含以下文件。

 + 插件json配置文件，放置在plugins目录下，文件名为：```插件编号.json```，如：plugins/demo.json
 + 插件启动文件，放置在plugins目录下，文件名：```插件编号.php```，如：plugins/demo.php
 + 插件php配置文件，放置在config目录下，文件名：```插件编号.php``，如：config/demo.php
 + 插件数据库文件，放置在data目录下，文件名：```插件编号.sql```，如：data/demo.sql
 + 插件PHP源代码，放置在src目录下，前台API代码放在src/app内，运营平台API代码则放在src/portal内
 + 插件前端代码，放置在public目录下，如果是运营平台的界面代码，则放在public/portal内 

这样就构成了一个完整的插件应用，除此之外，你还可以根据插件应用的情况进行添加、扩展和调整。

下面分别详细介绍。

### 插件json配置文件

当前，插件json配置文件的内容如下：  
```
{
    "plugin_key": "demo",
    "plugin_name": "demo插件",
    "plugin_author": "作者名称",
    "plugin_desc": "插件描述",
    "plugin_version": "1.0",
    "plugin_encrypt": 0,
    "plugin_depends": {
        "PHP": "5.6",
        "MySQL": "5.3",
        "PhalApi": "2.11.0",
        "composer": [],
        "extension": []
    },
    "plugin_files": {
        "config": "config\/demo.php",
        "plugins": "plugins\/demo.php",
        "data": "data\/demo.sql",
        "public": [
            "public\/portal\/page\/demo",
            "public\/portal\/page\/demo"
        ],
        "src": [
            "src\/app\/Api\/Demo",
            "src\/app\/Domain\/Demo",
            "src\/app\/Model\/Demo",
            "src\/app\/Common\/Demo",
            "src\/portal\/Api\/Demo"
        ]
    }
}
```

配置中的各项配置说明如下，可根据自己的情况进行修改。  
 + **plugin_key 插件编号**
 由字母和数字和下划线组成，插件的唯一标识，为避免重复及方便区分，建议使用：作者+插件功能，进行命名。例如个人的：```dogstar_demo```，企业的：```gouchuang_erp```。

 + **plugin_name 插件名称**
 插件的中文名称，简短的标题，以突出插件应用的功能。

 + **plugin_author 插件作者**
 你的开发者名称。

 + **plugin_desc 插件描述**
 插件的功能描述，可使用HTML富文本，注意不要破坏json的格式。

 + **plugin_version 插件版本**
 插件当前的版本号，方便后续升级和维护。

 + **plugin_encrypt 源码是否加密**
 插件源码加密模式，0无加密，1有加密，2部分加密。

 + **plugin_depends 插件依赖**
 插件的依赖，比如对PHP的版本要求、对数据库MySQL的版本要求，对PhalApi框架的版本要求，对composer的包依赖，以及对PHP扩展extension的依赖。如果检测失败，当前依然能安装插件，只是展示给用户提示。

 + **plugin_files 插件源代码文件及目录**
 可以根据插件的情况，进行添加或修改或删除。

### 插件启动文件
插件启动文件是把在接口请求时，在完成PhalApi框架的初始化之后，在项目初始化之前的插件启动。

被加载的时机，代码在```./config/di.php```：  
```php
/** ---------------- 第三应用 服务注册 ---------------- **/

// 加载plugins目录下的第三方应用初始化文件
foreach (glob(API_ROOT . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . '*.php') as $pluginFile) {
        include_once $pluginFile;
}
```

你可以在这里添加更多需要初始化的事情。例如，建立数据库连接，注册DI服务等。

### 插件php配置文件

插件自身可能需要一些属于自己的配置，你可以放置在插件php配置文件，类似./config/app.php那样。在你的配置文件中返回一个PHP数组，例如在./config/demo.php中：
```php
return array(
    'name' => 'demo',
);
```

然后就可以通过以下方式读取。  

```php
$name = \PhalApi\DI()->config->get('demo.name');
var_dump($name);
```

### 插件数据库文件
在插件数据库文件，你可以编写需要添加的菜单的sql语句，可以准备需要创建的数据库表，以及需要初始化的数据库表数据。  

但需要注意的是，所执行的SQL语句，需要考虑到用户在日后进行重新安装、或者升级安装时，是否需要清除原来的表结构、是否需要进行数据库变更、是否需要删除原来的插件数据等。

** SQL数据库文件注意事项 **

另外，需要特别注意几点：
 + SQL文件中，不能包含任何注释。安装程序会以```;\n```或```;\r\n```进行分割，依次处理
 + 创建数据库表时，无特殊不需要指定数据库排序字符集，但需要统一包含自增主键```id```
 + 关于表前缀，安装程序会自动进行表前缀的替换，替换规则是把```{插件编号}```换成```{当前表前缀}{插件编号}```
 + 根据插件自身情况判断，是否需要删除原来的表再重新建表，尽量避免使用```DROP TABLE```等危险操作
 + 如果需要，添加初始化数据，例如insert一些数据
 + 根据需要补充表本身和表字段的注释
 + 导入运营平台菜单

例如参考以下sql文件：  
```sql
delete from `phalapi_portal_menu` where id = 457602782;
insert into `phalapi_portal_menu` ( `target`, `id`, `title`, `href`, `sort_num`, `parent_id`, `icon`) values ( '_self', '457602782', 'phalapi_mini_tea插件', 'page/phalapi_mini_tea/index.html', '9999', '1', 'fa fa-list-alt');

CREATE TABLE `phalapi_mini_tea_user` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `user_identify` varchar(255) DEFAULT NULL COMMENT '用户的唯一标识(openid)',
        `user_wechat` varchar(255) DEFAULT NULL COMMENT '用户微信账号',
        `user_mobile` varchar(20) DEFAULT NULL COMMENT '用户手机',
        `user_nickname` varchar(255) NOT NULL COMMENT '用户名',
        PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 ;

delete from phalapi_mini_tea_user where id = 1;
INSERT INTO `phalapi_mini_tea_user` VALUES ('1', '4', null, null, '2432');
```

> 温馨提示：数据库表应该以你的插件编号为开头，并且在编写PHP代码时使用封装的NotORM进行数据库操作，以便可以适配不同环境下的表前缀，避免数据库表找不到。

当数据库文件执行失败时，不会影响安装，但会提示给用户。  

如果你的插件不需要创建运营平台菜单，也不需要添加任何的数据库表，可以忽略并删除相应的sql文件。

### 插件PHP源代码

放置在src目录下，前台API代码放在src/app内，运营平台API代码则放在src/portal内，具体开发，根据自己的业务需求，参考PhalApi的开发文档，进行编写。

### 插件前端代码

放置在public目录下，如果是运营平台的界面代码，则放在public/portal内。具体开发，根据自己的业务需求，参考PhalApi的开发文档，进行编写。

## 第2步：本地编程，开发插件

参考前面插件各模板的介绍，进行插件开发。整体上，插件开发和使用PhalApi进行日常开发是类似的。

## 第3步：打包插件

当你的插件应用开发完成后，并测试通过后，就可以进行插件的打包。把全部插件相关的文件和目录，根据./plugins/插件编号.json配置文件，打包到./plugins/插件编号.zip压缩包。  

你可以使用脚本命令```phalapi-plugin-build.php```来帮你完成打包的任务。

执行：```php ./bin/phalapi-plugin-build.php```，可以看到使用帮助。  

```
$ php ./bin/phalapi-plugin-build.php 
Usage: ./bin/phalapi-plugin-build.php <plugin_key>
请输入你的插件编号，字母数字和下划线组合。
```

需要输入第一个参数：插件编号，就是刚才你创建的插件编号。便可进行插件自动打包。

```
$ php ./bin/phalapi-plugin-build.php demo
插件已打包发布完毕！
/home/apps/projects/phalapi/plugins/demo.zip
```

打包后，你会看到插件打包后的压缩包。  
```
$ ll plugins/demo.zip
-rw-rw-r-- 1 apps apps 2674 Mar 12 11:58 plugins/demo.zip
```

完成打包后，可以在本地测试安装。进入运营平台-应用市场-我的应用-安装-确认重新安装。

![](http://cdn7.okayapi.com/yesyesapi_20200312130626_37229ec88ff374e36c70a5501b66fa4b.png)

安装完成后，会提示安装的信息：  
![](http://cdn7.okayapi.com/yesyesapi_20200312122828_01b3e0ed1ee29e80c95a7b635a9c18e7.png)

> 如果安装失败，请检测是否有文件和目录的写入权限。此时，可以改用脚本命令安装插件。

你也可以通过脚本命令来安装插件。  

```
[phalapi]$ php ./bin/phalapi-plugin-install.php demo
正在安装 demo
开始检测插件安装包 demo
检测插件是否已安装
插件已安装：plugins/demo.json
开始安装插件……
检测插件安装情况……
插件已安装：plugins/demo.json
插件：demo（demo插件），开发者：作者名称，版本号：1.0，安装完成！
开始检测环境依赖、composer依赖和PHP扩展依赖
PHP版本需要：5.6，当前为：7.1.33
MySQL版本需要：5.3
PhalApi版本需要：2.11.0，当前为：2.11.0
开始数据库变更……
插件安装完毕！
```

## 第4步：发布插件

当发布插件时，开发者需要做的事情主要有

 + 开发者进入，并进行实名认证
 + 将打包好的zip压缩包提交审核
 + 坐等收益（服务费用请参考应用市场的服务规则）

目前PhalApi应用市场正在抓紧研发中，当你的插件已经开发完成，可以联系我们提前录入，方便应用市场上线后可以第一时间展示给全部的用户进行查看、购买和安装。

联系方式：dogstar QQ号 376741929



