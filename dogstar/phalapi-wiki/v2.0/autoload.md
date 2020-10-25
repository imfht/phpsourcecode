# 自动加载和PSR-4

PhalApi 2.x 的自动加载很简单，完全遵循于[PSR-4规范](http://www.php-fig.org/psr/psr-4/)，并且兼容 PhalApi 1.x 版本的加载方式。  

在PhalApi 2.x这里，我们主要介绍PSR-4的使用，如果你已经熟悉此约定成俗的命名规范，可跳过这一节。  

# PSR-4规范一瞥

简单来说，类的全称格式如下：  

```
 \<NamespaceName>(\<SubNamespaceNames>)*\<ClassName>
```

其中，```<NamespaceName>```为顶级命名空间；```<SubNamespaceNames>```为子命名空间，可以有多层；```<ClassName>```为类名。 

# PhalApi 2.x 的命名规范

PhalApi 2.x 的项目的顶级命名空间，默认是```app```。  

## Api层命名规范

默认情况下，假设有一个为```?s=User.Login```的用户登录接口服务，则它对应的Api层接口类文件为：  
```
/path/to/phalapi/src/app/Api/User.php
```

类名则为```App\Api\User```，即顶级命名为```app```，子命名空间为```Api```，类名为```User```。由于存在命名空间，所以其代码实现片段如下：  

```php
<?php
// 对应文件：./src/app/Api/Weixin/User.php 
namespace App\Api;

use PhalApi\Api;

class User extends Api {

    public function Login() {
        // TODO
    }
}
```

### 多层子命名空间

当存在多层子命名空间时，则需要多层子目录；反之亦然，即如果存在多个子目录，则需要多层子命名空间。例如，对于接口服务```?s=Weixin_User.Login```，其文件路径为：  
```
/path/to/phalapi/src/app/Api/Weixin/User.php
```

实现代码片段为：  
```php
<?php
namespace App\Api\Weixin;

use PhalApi\Api;

class User extends Api {

    public function Login() {
        // TODO
    }
}
```

需要注意的是，此时当前的命名空间为```App\Api\Weixin```，而不再是```App\Api```。 


## Domain层和Model层的命名规范

Domain层，和Model层的命名规范，和Api层的一样，或者说其他层级或者目录的规范也是如此，可依次类推。  

例如，对于类```app\Domain\User```，其文件路径为：  
```
/path/to/phalapi/src/app/Domain/User.php
```

实现代码片段为：  
```php
<?php
namespace App\Domain;

class User { }
```

而对于类```app\Domain\Weixin\User```，其文件路径为：  
```
/path/to/phalapi/src/app/Domain/Weixin/User.php
```

实现代码片段为：  
```php
<?php
namespace App\Domain\Weixin;

class User { }
```

## 如何实例化

实例化的方式有两种，对应命名空间两种不同的使用方式。 

### 先use，再实例

通常情况下，都是先use，然后再实例化。例如，在Api层需要用到Domain层的类时，可以这样：  

```php
<?php
namespace App\Api;

use PhalApi\Api;
use App\Domain\User as DomainUser;  // 在这里先use

class User extends Api {

    public function Login() {
        $domainUser = new DomainUser();
    }
}
```

因为存在两个User类，所以在use领域类时需要改用别名```DomainUser```。如果当前命名空间和待使用的类是同一命名空间，则可以省略use。例如在```App\Domain\User```类中使用```App\Domain\Friends```。  

```php
<?php
namespace App\Domain;

class User {

    public function Login() {
        $friend = new Friend(); // 可直接使用Friend类
    }
}
```

### 使用完整类名实例化

另一种情况下，可不用先use，直接使用带命名空间前缀的完整类名来实例化。例如，上面的可改成：  

```php
<?php
namespace App\Api;

use PhalApi\Api;

class User extends Api {

    public function Login() {
        $domainUser = new \App\Domain\User();
    }
}
```

值得注意的是，在当前命名空间下，如果需要引用其他类，应该在最前面加上反斜杠，表示绝对路径，否则会导致类名错误，从而加载失败。即：  

当前命名空间|类名|最终解析类名|区别
---|---|---|----
App\Api|\App\Domain\User|\App\Domain\User|最前面有反斜杠，正确
App\Api|App\Domain\User|App\Api\App\Domain\User|最前面缺少反斜杠，错误
App\Api|\Exception|Exception|最前面有反斜杠，使用PHP官方的异常类，正确
App\Api|Exception|App\ApiException|最前面缺少反斜杠，使用当前命名空间的异常类

如果当前没有命名空间，则最前面可不用加上反斜杠。  

# 如何增加一个顶级命名空间？  

在composer下，增加一个顶级命名空间很简单。首先，需要在根目录下的```composer.json```文件中追加psr-4配置，如在原有基础上添加一个```Foo```命名空间，则：  

```
{
    "autoload": {
        "psr-4": {
            "App\\": "src/app",
            "Foo\\": "src/foo"
        }
    }
}
```

配置好后，执行composer更新操作：  
```bash
$ composer update
```

或者进行快捷更新，只更新命名空间的映射关系：  
```bash
$ composer dumpautoload 
```

此时，对于顶级命名空间```Foo```，其源代码保存在```/path/to/phalapi/src/foo```下。其他类似，这里不再赘述。 

需要注意的是，源代码目录需要自己手动添加，即分别添加以下几个常见目录：Api、Domain、Model、Common。以这里的```Foo```命名空间为例，需要创建以下目录：  

 + src/foo/Api  
 + src/foo/Domain  
 + src/foo/Model  
 + src/foo/Common  

接下来就可以正常开始开发了。在src/foo/Api目录下新增的接口服务，会同步实时显示在在线接口文档上。如这里添加src/foo/Api/Hello.php文件，并放置以下代码：  

```
// 文件 ./src/foo/Api/Hello.php
<?php
namespace Foo\Api;

use PhalApi\Api;

/**
 * Foo下的示例
 */
class Hello extends Api {

    public function world() {
        return array('title' => 'Hello World in Foo!');
    }
}
```  

就可以看到：  
![](http://cdn7.phalapi.net/20180322205119_9d2a2886f6e9517382d7fa0743fd0fff)  

# 添加全局函数

如果需要添加全局函数，可以放置到./src/app/functions.php文件内，例如：

```php
<?php
namespace App;

function hello() {
        return 'Hey, man~';
}
```

当需要使用时，可直接在前面加命名空间进行使用，例如：
```php
echo \App\hello();
```

这是因为此文件已经在./composer.json中注册，框架启动时会自动加载此函数文件。

类似地，如果你需要添加新的函数文件，可以继续追加到在./composer.json文件中，例如：
```
"autoload": {
    "files": [
        "src/app/functions.php",
        "src/app/common.php"
    ],
}
```

这样就可以新增src/app/common.php函数文件。使用方式和上面类似，注意，如果不添加命名空间，则可以不加命名空间前缀。例如在src/app/common.php中添加函数：

```
<?php
function hi() {
        return 'Hi, guys!';
}
```

然后可这样调用：
```php
echo \hi();
```

# 注意事项  

对于初次使用composer和初次接触PSR-4的同学，以下事项需要特别注意，否则容易导致误解、误用、误导。  

 + 1、在当前命名空间使用其他命名空间的类时，应先use再使用，或者使用完整的、最前面带反斜杠的类名。  
 + 2、在定义类时，当前命名空间应置于第一行，且当存在多级命名空间时，应填写完整。  
 + 3、命名空间和类，应该与文件路径保持一致，并区别大小写。  
  

例如：  

```php
<?php
namespace App\Api;
use PhalApi\Api;

class Site extends Api {

    public function test() {
        // 错误！会提示 App\Api\DI()函数不存在！
        DI()->logger->debug('测试函数调用');  

        // 正确！调用PhalApi官方函数要用绝对命名空间路径
        \PhalApi\DI()->logger->debug('测试函数调用');  
    }


    public function testMyFun() {
        // 错误！会提示 App\Api\my_fun()函数不存在！
        //（假设在./src/app/functions.php有此函数）
        my_fun();  

        // 正确！调用前要加上用绝对命名空间路径
        \App\my_fun();  
    }
}
```

#### 
