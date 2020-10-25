###codeception自动化测试说明

###如何正常访问官网
在hosts中添加
```sh
192.30.252.154 codeception.com
```

###codeception安装
####一、Phar
```sh
//下载phar包
wget http://codeception.com/codecept.phar
//初始化
php codecept.phar bootstrap
或
php codecept.phar bootstrap /path/to/my/project
```
####二、Composer
已存在composer.json在require块加上`"codeception/codeception": "^2.2"`，然后使用`composer update`命令更新composer包

若不存在composer则使用`php composer.phar require "codeception/codeception:*"`

使用如下命令初始化
```sh
php vendor/bin/codecept bootstrap
//或者
php vendor/codeception/codeception/codecept bootstrap
```
####三、GIT
此方式类似与composer方式，clone下来作为一个独立的工具
```sh
git clone git@github.com:Codeception/Codeception.git
//使用Composer安装依赖项
cd Codeception
        curl -s http://getcomposer.org/installer | php
        php composer.phar install
   
//执行引导程序，指定目录的路径。
php codecept bootstrap /path/to/my/project
//使用-c选项指定路径运行测试
php codecept run -c /path/to/my/project
```

[官网](http://codeception.com)<br>

###中文文档地址
[快速入门指南](https://www.cloudxns.net/Support/detail/id/903.html)<br>
[Codeception 简介](https://www.cloudxns.net/Support/detail/id/924.html)<br>
[Codeception 入门](https://www.cloudxns.net/Support/detail/id/939.html)<br>
[Codeception 验收测试](https://www.cloudxns.net/Support/detail/id/957.html)<br>
[Codeception 功能测试](https://www.cloudxns.net/Support/detail/id/1005.html)<br>
[Unit Tests 单元测试](https://www.cloudxns.net/Support/detail/id/1125.html)<br>
[重用测试代码（1）](https://www.cloudxns.net/Support/detail/id/1152.html)<br>
[重用测试代码（2）](https://www.cloudxns.net/Support/detail/id/1176.html)<br>
[高级用法（1）](https://www.cloudxns.net/Support/detail/id/1329.html)<br>

[KK之家](http://www.kkh86.com/it/codeception/guide-README.html)<br>


###编写示例场景
我们可以通过运行以下命令来创建
```sh
php vendor/codeception/codeception/codecept generate:cept acceptance Signin
```
通过命令会创建`tests/acceptance/SigninCept.php`文件，在此文件中输入如下代码：
```php
$I = new AcceptanceTester($scenario);
$I->am('user');//用户角色
$I->wantTo('login to website');//目的
$I->lookForwardTo('access all website features');//期待达到什么效果
$I->amOnPage('/login');//在哪个页面
$I->fillField('Username','davert');//填充表单字段
$I->fillField('Password','qwerty');
$I->click('Login');//点击按钮
$I->see('Hello, davert');//会看到什么文字
```
打开`tests/acceptance.suite.yml`文件配置好`PhpBrowser-url`值

执行测试
```sh
php vendor/codeception/codeception/codecept run
```

使用第一个参数，您可以运行一个套件中的所有测试：
```sh
php vendor/codeception/codeception/codecept run acceptance
```
要限制测试运行到单个类，请添加第二个参数。提供测试类的本地路径，从套件目录：

```sh
php vendor/codeception/codeception/codecept run acceptance SigninCept.php
```

或者，您可以提供测试文件的完整路径：
```sh
php vendor/codeception/codeception/codecept run tests/acceptance/SigninCept.php
```

您可以通过向类附加一个方法名称来进一步过滤运行哪些测试，用冒号分隔（对于Cest或测试格式）：
```sh
php vendor/codeception/codeception/codecept run tests/acceptance/SignInCest.php:^anonymousLogin$
```

您也可以提供目录路径。这将执行从所有验收测试backend目录：
```sh
php vendor/codeception/codeception/codecept run tests/acceptance/backend
```

使用正则表达式，您甚至可以从同一目录或类运行许多不同的测试方法。例如，这将执行从所有验收测试backend目录以单词的登录开始：
```sh
php vendor/codeception/codeception/codecept run tests/acceptance/backend:^login
```
要执行一组不存储在同一目录下的测试，你可以组织这些组。

报告
```sh
php vendor/codeception/codeception/codecept run --steps --xml --html
```
此命令将运行所有套件的所有测试，显示步骤，并构建HTML和XML报告。报告将被储存在tests/_output/目录中。
要查看所有可用选项，请运行以下命令：
```sh
php vendor/codeception/codeception/codecept help run
```
###测试API接口方法
创建api测试套件
```sh
php vendor/codeception/codeception/codecept generate:suite api
```
配置`api.suite.yml`
```sh
class_name: ApiTester
modules:
    enabled:
        - \Helper\Api
        - REST:
            url: http://m2test.yaochufa.com/
            depends: PhpBrowser
            part: Json//可选，如果不设置则会同时处理xml和json响应
```

更改配置时，将自动重新构建actor类。如果未创建或更新的演员类如您所愿，请尝试使用手动生成它们build的命令：

```sh
php vendor/codeception/codeception/codecept build
```
一旦我们配置了新的测试套件，我们可以创建一个样本测试：
```sh
php vendor/codeception/codeception/codecept generate:cest api Index
```