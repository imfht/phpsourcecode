# 脚本命令的使用

自动化是提升开发效率的一个有效途径。PhalApi致力于简单的接口服务开发，同时也致力于通过自动化提升项目的开发速度。为此，生成单元测试骨架代码、生成数据库建表SQL这些脚本命令。应用这些脚本命令，能快速完成重复但消耗时间的工作。下面将分别进行说明。  

## phalapi-buildtest命令

当需要对某个类进行单元测试时，可使用phalapi-buildtest命令生成对应的单元测试骨架代码，其使用说明如下：  

![](http://cdn7.phalapi.net/20170725232117_3fb828887ae30e22c8d4f02aa5d9aa26)  
 
  
其中，

 + **第一个参数file_path**  是待测试的源文件相对/绝对路径 。 
 + **第二个参数class_name**  是待测试的类名。  
 + **第三个参数bootstrap**  是测试启动文件，通常是/path/to/phalapi/tests/bootstrap.php文件。  
 + **第四个参数author** 你的名字，默认是dogstar。  
   
通常，可以先写好类名以及相应的接口，然后再使用此脚本生成单元测试骨架代码。以默认接口服务```Site.Index```接口服务为例，当需要为其生成单元测试骨架代码时，可以执行以下命令。  
```bash
$ ./bin/phalapi-buildtest ./src/app/Api/Site.php App\\Api\\Site > ./tests/app/Api/Site_Test.php
```
  
最后，需要将生成好的骨架代码，重定向保存到你要保存的位置。通常与产品代码对齐，并以“{类名} + _Test.php”方式命名，如这里的app/Api/Site_Test.php。  

生成的骨架代码类似如下：  
```php
<?php

//require_once dirname(__FILE__) . '/bootstrap.php';

if (!class_exists('App\\Api\\Site')) {
    require dirname(__FILE__) . '/./src/app/Api/Site.php';
}

/**
 * PhpUnderControl_App\Api\Site_Test
 *
 * 针对 ./src/app/Api/Site.php App\Api\Site 类的PHPUnit单元测试
 *
 * @author: dogstar 20170725
 */

class PhpUnderControl_AppApiSite_Test extends \PHPUnit_Framework_TestCase
{
    public $appApiSite;

    protected function setUp()
    {
        parent::setUp();

        $this->appApiSite = new App\Api\Site();
    }

    ... ...
```

简单修改后，便可运行。 


## phalapi-buildsqls命令

当需要创建数据库表时，可以使用phalapi-buildsqls脚本命令，再结合数据库配置文件./config/dbs.php即可生成建表SQL语句。此命令在创建分表时尤其有用，其使用如下：  

![](http://cdn7.phalapi.net/20170725232919_e6d034485ed2c5f208d6e5b6c34ae555)  

  
其中，

 + **第一个参数dbs_config** 是指向数据库配置文件的路径，如./Config/dbs.php，可以使用相对路径。  
 + **第二个参数table**  是需要创建sql的表名，每次生成只支持一个。  
 + **第三个参数engine**  可选参数，是指数据库表的引擎，MySQL可以是：Innodb或者MyISAM。  
 + **第四个参数sqls_folder** 可选参数，SQL文件的目录路径。
  
在执行此命令先，需要提前先将建表的SQL语句，排除除主键id和ext_data字段，放置到./data目录下，文件名为：{表名}.sql。  
  
例如，我们需要生成10张user_session用户会话分表的建表语句，那么需要先添加数据文件./data/user_session.sql，并将除主键id和ext_data字段外的其他建表语句保存到该文件。   
```sql
      `user_id` bigint(20) DEFAULT '0' COMMENT '用户id',
      `token` varchar(64) DEFAULT '' COMMENT '登录token',
      `client` varchar(32) DEFAULT '' COMMENT '客户端来源',
      `times` int(6) DEFAULT '0' COMMENT '登录次数',
      `login_time` int(11) DEFAULT '0' COMMENT '登录时间',
      `expires_time` int(11) DEFAULT '0' COMMENT '过期时间',
```
  
然后，进入到项目根目录，执行命令：  
```bash
$ php ./bin/phalapi-buildsqls ./config/dbs.php user_session
```
  
正常情况下，会看到生成好的SQL语句，类似下面这样的输出。    
```sql
CREATE TABLE `phalapi_user_session_0` (
      `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      `user_id` bigint(20) DEFAULT '0' COMMENT '用户id',
      `token` varchar(64) DEFAULT '' COMMENT '登录token',
      `client` varchar(32) DEFAULT '' COMMENT '客户端来源',
      `times` int(6) DEFAULT '0' COMMENT '登录次数',
      `login_time` int(11) DEFAULT '0' COMMENT '登录时间',
      `expires_time` int(11) DEFAULT '0' COMMENT '过期时间',
      `ext_data` text COMMENT 'json data here',
      PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `phalapi_user_session_1` (
      `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      ... ...
      `ext_data` text COMMENT 'json data here',
      PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `phalapi_user_session_2` ... ...
CREATE TABLE `phalapi_user_session_3` ... ...
CREATE TABLE `phalapi_user_session_4` ... ...
CREATE TABLE `phalapi_user_session_5` ... ...
CREATE TABLE `phalapi_user_session_6` ... ...
CREATE TABLE `phalapi_user_session_7` ... ...
CREATE TABLE `phalapi_user_session_8` ... ...
CREATE TABLE `phalapi_user_session_9` ... ...
```
  
最后，便可把生成好的SQL语句，导入到数据库，完成建表的操作。  

值得注意的是，生成的SQL建表语句默认会带有自增ID主键id和扩展字段ext_data这两个字段。所以保存在./data目录下的建表语句可省略主键字段，以免重复。    
```sql
      `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      ... ...
      `ext_data` text COMMENT 'json data here',
```

## phalapi-cli命令

此脚本可用于在命令行终端，直接运行接口服务，也可用于作为命令行终端应用的执行入口。

需要注意的是，要先确保在composer.json文件内有以下配置：

```
{
    "require": {
        "phalapi/cli": "dev-master"
    }
}
```
并确保已经成功安装phalapi/cli。  

> phalapi/cli扩展地址：https://github.com/phalapi/cli

以默认接口服务App.Site.Index为例，执行方式如下：  

```
$  ./bin/phalapi-cli -s App.Site.Index --username dogstar
{"ret":200,"data":{"title":"Hello dogstar","version":"2.2.3","time":1535207991},"msg":""}
```

如果想查看帮助提示信息，可以在指定了接口服务后，使用```--help```参数。例如：  

```
$ ./bin/phalapi-cli -s App.Site.Index -h
Usage: ./bin/phalapi-cli [options] [operands]
Options:
  -s, --service <arg>     接口服务
  -h, --help              查看帮助
```

## 注意事项

在使用这些脚本命令前，需要注意以下几点。  

### 执行权限

第一点是执行权限，当未设置执行权限时，脚本命令会提示无执行权限，类似这样。  
```bash
$ ./phalapi/bin/phalapi-buildtest 
-bash: ./phalapi/bin/phalapi-buildtest: Permission denied
```
那么需要这样设置脚本命令的执行权限。  
```bash
$ chmod +x ./phalapi/bin/phalapi-build*
```
  
### 编码问题

其次，对于Linux平台，可能会存在编码问题，例如提示：  
```bash
$ ./phalapi/bin/phalapi-buildtest 
bash: ./phalapi/bin/phalapi-buildtest: /bin/bash^M: bad interpreter: No such file or directory
```
这时，可使用dos2unix命令转换一下编码。  
```bash
$ dos2unix ./phalapi/bin/phalapi-buildtest*
dos2unix: converting file ./phalapi/bin/phalapi-buildsqls to Unix format ...
dos2unix: converting file ./phalapi/bin/phalapi-buildtest to Unix format ...
```

### 软链

最后一点是，在任意目录位置都是可以使用这些命令的，但会与所在的项目目录绑定。通常，为了更方便使用这些命令，可以将这些命令软链到系统命令下。例如：  
```bash
$ sudo ln -s /path/to/phalapi/bin/phalapi-buildsqls /usr/bin/phalapi-buildsqls
$ sudo ln -s /path/to/phalapi/bin/phalapi-buildtest /usr/bin/phalapi-buildtest
```
