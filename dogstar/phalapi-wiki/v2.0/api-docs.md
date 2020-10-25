# 接口文档


## 在线接口文档

PhalApi提供一些非常实用而又贴心的功能特性，其中最具特色的就是自动生成的在线可视化文档。在线接口文档主要分为两大类，分别是：  

 + 在线接口列表文档
 + 在线接口详情文档

当客户端不知道有哪些接口服务，或者需要查看某个接口服务的说明时，可借助此在线接口文档。访问在线接口列表文档的URL是：  
```
http://dev.phalapi.net/docs.php
```

打开后，便可看到类似下面这样的在线接口文档。  
![](http://cdn7.phalapi.net/20170701174008_d80a8df4f918dc063163a9d730ceaf32)  

此在线文档是实时生成的，可根据接口源代码以及注释自动生成。当有新增接口服务时，刷新后便可立即看到效果。通过在接口列表文档，可点击进入相应的接口详情文档页面。  

> 温馨提示：如果打开在线文档，未显示任何接口服务，请确保服务环境是否已关闭PHP的opcache缓存。  


## 代码、注释与接口文档

PhalApi提供了自动生成的在线接口文档，对于每一个接口服务，都有对应的在线接口详情文档。如默认接口服务```Site.Index```的在线接口详情文档为：  

![](http://cdn7.phalapi.net/20170716165631_4936f1cf60b99f5f830b4967769cf35b)  

此在线接口详情文档，从上到下，依次说明如下。  

### 接口项目名称
首先，通过根目录下的composer.json文件里面的autoload内psr-4配置，找到项目对应的命名空间名称，例如：
```
    "autoload": {
        "psr-4": {
            "App\\": "src/app"
        }
    }
```
然后，修改./language/zh_cn/common.ph翻译文件，把命名空间的类名改成中文项目名称。如：  
```php
return array(
    "App" => '我的项目',
);
```
刷新接口文档列表页，效果如下：
![](http://cdn7.okayapi.com/yesyesapi_20200218153205_85a7edf32fa4db8d719845e3aca334e4.png)


### 接口服务名称

接口服务名称是指用于请求时的名称，对应s参数（或service参数）。接口服务的中文名称，为不带任何注解的注释，通常为接口类成员函数的第一行注释。  
```php
class Site extends Api {

    /**
     * 默认接口服务
     */
    public function index() {
    }
}
```

### 接口说明

接口说明对应接口类成员函数的```@desc```注释，支持HTML格式。  

```php
class Site extends Api {

    /**
     * 默认接口服务
     * @desc 默认接口服务，当未指定接口服务时执行此接口服务
     */
    public function index() {
    }
}
```

> 温馨提示：@desc 注释支持HTML格式。

### 接口参数

接口参数是根据接口类配置的参数规则自动生成，即对应当前接口类```getRules()```方法中的返回。其中最后的“说明” 字段对应参数规则中的desc选项，支持HTML格式。可以配置多个参数规则。此外，配置文件./config/app.php中的公共参数规则也会显示在此接口参数里。  

```php
class Site extends Api {

    public function getRules() {
        return array(
            'index' => array(
                'username'  => array('name' => 'username', 'default' => 'PHPer', ),
            ),
        );
    }
}
```

> 温馨提示：desc 配置项支持HTML格式。

### 返回结果

返回结果对应接口类成员函数的```@return```注释，可以有多组，格式为：```@return 返回类型 返回字段 说明```。其中，  
 + **返回类型**：支持string/array/object/int/float/boolean/date/enum
 + **返回字段**：返回结果的键名称，多个层级时使用英文点号连接。例如：```xxx.yyy```，特别地，数组类型使用方括号作为后缀，如```xxx[].yyy```
 + **说明**：返回结果字段的说明，支持HTML格式

```php
class Site extends Api {

    /**
     * 默认接口服务
     * @desc 默认接口服务，当未指定接口服务时执行此接口服务
     * @return string title 标题
     * @return string content 内容
     * @return string version 版本，格式：X.X.X
     * @return int time 当前时间戳
     */
    public function index() {
    }
}
```

> 温馨提示：返回字段说明支持HTML格式。

返回结果的组合顺序，依次由以下三部分构成：  
 + 第一部分：接口类基类的公共返回结果注释（多个基类时依次顺序叠加）
 + 第二部分：当前接口类的类注释里的公共返回结果注释
 + 第三部分：接口类成员函数的返回结果注释

## 接口返回示例

为了方便客户端在未调用接口前也能了解接口的返回格式和示例，可以添加为每个接口服务添加相应的返回示例、同时，考虑到服务端维护的便易性，我们会对每个接口服务单独使用一个文件来存放。默认情况下，返回示例文件存放在：

```
./src/view/docs/demos
```

文件名是：
```
接口服务名称 + .json
```

例如：

```
./src/view/docs/demos/App.Site.Index.json
```

示例文件里可以放置返回给客户端的示例。如：
```
{
    "ret": 200,
    "data": {
        "title": "Hello PhalApi",
        "version": "2.7.0",
        "time": 1558489902
    },
    "msg": ""
}
```

最后，在线文档的展示效果是：

![](http://cdn7.okayapi.com/yesyesapi_20190522100934_d74f29dbb6af0de572206d7330475f2e.jpeg)

> 注意！接口返回示例，需要PhalApi 2.7.0 及以上版本方可支持。

### 异常情况

异常情况对应```@exception```注释，可以有多组，格式为：```@exception 错误码 错误描述信息```。例如：    

```php
    /**
     * @exception 400 非法请求，参数传递错误
     */
    public function index() {
```
刷新后，可以看到新增的异常情况说明。  

![](http://cdn7.okayapi.com/yesyesapi_20190522124948_ea764bc8b983cf404a2fbc62cc7027af.jpeg)

异常情况列表依次由以下四部分构成：
 + 第一部分：PhalApi框架的错误返回
 + 第二部分：接口类基类的公共异常注释（多个基类时依次叠加）
 + 第三部分：当前接口类的公共异常注释
 + 第四部分：接口类成员函数的异常注释

### 公共注释

对于当前类的全部函数成员的公共```@exception```异常情况注释和```@return```返回结果注释，可在类注释中统一放置。而对于多个类公共的@exception```和```@return```注释，则可以在父类的类注释中统一放置。

也就是说，通过把```@exception```注解和```@return```注解移到类注释，可以添加全部函数成员都适用的注解。例如，Api\User类的全部接口都返回code字段，且都返回400和500异常，则可以：  
```php
<?php
namespace App\Api;

use PhalApi\Api;

/**
 * @return int code 操作码，0表示成功
 * @exception 400 参数传递错误
 * @exception 500 服务器内部错误
 */

class User extends Api {
```
这样就不需要在每个函数成员的注释中重复添加注解。此外，也可以在父类的注释中进行添加。对于相同异常码的```@exception```注解，子类的注释会覆盖父类的注释，方法的注释会覆盖类的注释；而对于相同的返回结果```@return```注释，也一样。  

需要注意的是，注释必须是紧挨在类的前面，而不能是在namespace前面，否则会导致注释解析失败。  

## 通过在线接口文档进行测试

在线接口文档，不仅可以用来查看接口文档，包括接口参数、返回字段和功能说明外，还可以在上面进行接口测试。这将会直接请求当前的接口。效果如下：

![](http://cdn7.okayapi.com/yesyesapi_20190420153522_8629e92c261ad6a1f58f3e990994dce2.png)

## 如何隐藏接口？

如果需要在在线接口文档列表页隐藏接口模块，或者特定的接口，可以使用```@ignore```注释。例如：  
隐藏整个接口模块，可以在类注释添加```@ignore```。
```php
<?php
namespace App\Api;
use PhalApi\Api;

/**
 * @ignore
 */
class User extends Api {

}
```

如果只是需要隐藏特定的接口，可以在类成员函数的注释中添加```@ignore```。
```php
<?php
namespace App\Api;
use PhalApi\Api;

class User extends Api {
    /**
     * 忘记密码接口
     * @ignore
     */
    public function forgetPassword() {
    }

}
```

添加后刷新接口列表页将不会显示，但在接口详情页通过原来的链接仍能正常访问和显示。  

## 如何生成离线接口文档？  

上面在线的接口文档，也可以一键生成离线版的HTML文档，方便传阅，离线查看。  

当需要生成离线文档时，可以在终端，执行以下命令：  

```bash
phalapi$ php ./public/docs.php 

Usage:

生成展开版：  php ./public/docs.php expand
生成折叠版：  php ./public/docs.php fold

脚本执行完毕！离线文档保存路径为：/path/to/phalapi/public/docs
```

执行后，可以看到类似上面的提示和结果输出。再查看生成的离线文档，可以看到类似有：  
```bash
phalapi$ tree ./public/docs
./public/docs
├── App.Examples_CURD.Delete.html
├── App.Examples_CURD.Get.html
├── App.Examples_CURD.GetList.html
├── App.Examples_CURD.Insert.html
├── App.Examples_CURD.Update.html
├── App.Examples_Upload.Go.html
├── App.Site.Index.html
└── index.html
```

最后，可以在页面访问此离线版文档，如访问链接：  
```
http://dev.phalapi.net/docs/index.html
```

#### 也可以将此docs目录打包，在本地打开访问查看。
