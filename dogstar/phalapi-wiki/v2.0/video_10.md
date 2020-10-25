# 第十课 PhalApi 2.x 接口开发 - 自动加载和PSR-4

[第十课 PhalApi 2.x 接口开发 - 自动加载和PSR-4](https://www.bilibili.com/video/av89570518)

[![](http://cdn7.okayapi.com/yesyesapi_20200217121635_b695ae6aede7742b332467aadebb8085.png)](https://www.bilibili.com/video/av89570518)


## PHP世界中的类加载方式
### 1、最原始的方式：手动引入
 - include、require、include_once、require_once区别
 - 为什么现在仍然有很多大公司的项目保留使用手动加载的方式？
  - 1）历史原因，改动升级成本和风险过高
  - 2）性能更优

### 2、自行实现的自动加载方式（PSR-0）
 - ```__autoload()函数```
 - ```spl_autoload_register()```
 - 简单回顾当初PhalApi 1.x如何实现自动加载
 
### 3、composer自动加载方式，PSR-4
PhalApi 2.x使用了composer的命名规范。
规范一瞥，类的全称格式如下：   
```
\<NamespaceName>(\<SubNamespaceNames>)*\<ClassName>
```

不同语言的包管理工具：
 + Scala，sbt（simple building tool）
 + Java，Maven、Ant、Gradle
 + Ruby，rvm（Ruby Version Manager）
 + Python，pip
 + PHP，composer
 + R，

## PhalApi 2.x 的命名规范
PhalApi 2.x 的项目的顶级命名空间，默认是app。 
 - Api层命名规范（类名、文件名和接口名称，关联起来）
 - Domain层和Model层的命名规范
 
## 如何实例化
 - 先use，再实例
   - 1）use时，不需要在前面再加反斜杠，例如：```use PhalApi\Api;```，不需要```use \PhalApi\Api;```
   - 2）你可以use一个类，也可以use一个目录。后面使用时，就要用：目录名+类名
   - 3）可以通过AS起一个别名，例如：```use App\Domain\User as DomainUser; ```
   - 4）对于相同命名空间的类，不需要use（引伸一个新问题？？？推理：一旦未use而使用的类，则被视为在当前命名空间）
   - 5）也可以use一个函数
   
 - 使用完整类名实例化（注意使用绝对路径）
   - 特别注意：一定要使用绝对路径，否则会被当作成为当前的命名空间

#### 这是一个错误的示例
```php
<?php
namespace App\Api\Weixin;

use PhalApi\Api;

/**
 * 微信用户
 */
class User extends Api {

    /**
     * 微信登录接口
     */
    public function login() {
    	// 注意这一行！！！
        $mcrypt = new \PhalApi\Crypt\McryptCrypt('12345678');

        $data = 'The Best Day of My Life';
        $key = 'phalapi';

        $encryptData = $mcrypt->encrypt($data, $key);
        var_dump($encryptData);
        // string(24) "ÎdÑTÖ=&y»ÚÁr=-"

        $decryptData = $mcrypt->decrypt($encryptData, $key);
        var_dump($decryptData);
        // string(23) "The Best Day of My Life"
    }
}
``` 

报错信息：  
```bash
Fatal error:  Uncaught Error: Class 'App\\Api\\Weixin\\PhalApi\\Crypt\\McryptCrypt' not found
```

### 特别强调，要区分绝对路径！！
两个容易忽略的问题：
 - 1）抛出异常：```throw new Exception('baba~~');```，被当作是当前命名空间的Exception（提示：Fatal error:  Uncaught Error: Class 'App\\Api\\Weixin\\Exception' not found）
 - 2）使用App命名空间下的全局函数（提示：Uncaught Error: Call to undefined function App\\Api\\Weixin\\hello()）
 
> 小结：在PHP命名空间下，PHP官方类需要在前面加反斜杠作为绝对路径引入；但是PHP官方的**函数**则不需要在前面加反斜杠作为绝对路径而引入。

** 延伸：绝对路径和相对路径之间微妙的区别？** 
 - 命名空间
 - 网址
 	- http://www.phalapi.net/index.html
 	- /index.html
 	- ./test/index.html
 	- ./test
 - Linux系统的文件路径 
 	- /etc/hosts
 	- ./public/index.php
 	- ../

## 如何增加一个顶级命名空间？
换个说法：如何添加一个新接口项目？

 - 第一步：修改composer.json，新增项目配置
 - 第二步：执行composer更新，推荐使用：```composer dumpautoload```


## 注意事项
使用函数时，要区分：
 - 如果是PHP的函数，或者是没有命名空间的函数，可以直接使用
 - 如果是PhalApi框架下的函数，例如DI()，需要这样使用：```\PhalApi\DI()```
 - 如果是当前项目的函数（对应./src/app/functions.php），应该这样使用：```\App\hello();```

