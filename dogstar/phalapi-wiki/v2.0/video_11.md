# 第十一课 PhalApi 2.x 接口开发 - 接口文档

[第十一课 PhalApi 2.x 接口开发 - 接口文档](https://www.bilibili.com/video/av89878191/)

[](https://www.bilibili.com/video/av89878191/)

## 在线接口文档
 + 在线接口列表文档，访问网站：http://dev.phalapi.net/docs.php
 + 在线接口详情文档
 
> 温馨提示：如果打开在线文档，未显示任何接口服务，请确保服务环境是否已关闭PHP的opcache缓存。
 
## 接口文档配置教程 
 + 项目名称
 + 接口模块名称
 + 接口服务名称
 + 接口说明（支持HTML格式）
 + 接口参数（从PHP的配置代码获取，接口说明desc支持HTML格式）
 + 返回结果
 	- 返回类型有：string/array/object/int/float/boolean/date/enum
 	- 返回结构，对象用 xxx.yyy；数组用 xxx[].yyy
 + 接口返回示例
 + 异常情况
 
## 补充（PHP注释与反射）
PHP的注释规范
 - 文件的注释
 - 类的注释
 - 类方法的注释
 - 类成员属性的注释
 - 函数的注释

```php
// 这是单行注释（推荐写法）

# 这也是单行的注释（但个人觉得是老式的写法，不推荐）

/**
 * 多行，区域的注释（规范的注释，特别是phpdocument建议的，也是PHP的Reflection反射机制能解析和获取）
 * @关键字 表示特定的注释
 */
 
/**
  * 这种也是多行，但不推荐，并且并不规范
  */
``` 

```php
<?php
/**
 * 这里是文件的注释
 */
namespace Foo\Api;

use PhalApi\Api;

/**
 * Foo下的示例（类注释，可通过ReflectionClass::getDocComment方法获取）
 */
class Hello extends Api {

    /**
     * @var string $name 名字
     */
    protected $name;

    /**
     * 第二个项目的示例接口（类方法的注释，可通过ReflectionMethod::getDocComment方法获取）
     */
    public function world() {
        return array('title' => 'Hello World in Foo!');
    }
}
```

## 开发技巧
 + 公共注释
 + 在线测试
 
## 如何生成离线接口文档？


## 附本次视频教程源代码
```php
<?php
/**
 * 这里是文件的注释
 */
namespace Foo\Api;

use PhalApi\Api;

/**
 * Foo下的示例（类注释，可通过ReflectionClass::getDocComment方法获取）
 * @return string version 统一返回的版本号字段
 * @exception 409 统一返回的异常码
 */
class Hello extends Api {

    public function getRules() {
        return array(
            'world' => array(
                'project' => array('name' => 'project', 'type' => 'string', 'require' => true, 'default' => '', 'desc' => '<strong>项目名称</strong><hr/>'),
            ),
        );
    }

    /**
     * @var string $name 名字
     */
    protected $name;

    /**
     * 第二个项目的示例接口
     * @desc 第二个项目的示例接口（类方法的注释，可通过<a href="https://www.php.net/manual/zh/reflectionmethod.getdoccomment.php" target="_blank">ReflectionMethod::getDocComment</a>方法获取）
     * @return string title 标题
     * @exception 401 视频演示的参数传递错误
     */
    public function world() {
        return array('title' => 'Hello World in Foo!');
    }

    /**
     * 视频教程测试接口
     * @desc <font color="red">接口功能说明可以放置在这里。</font>
     */
    public function test() {
    }
}
```

