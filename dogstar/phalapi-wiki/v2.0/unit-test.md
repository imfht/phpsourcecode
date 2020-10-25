# PhalApi 2.x 单元测试

## 测试驱动开发与PHPUnit

PhalApi推荐使用测试驱动开发最佳实践，并主要使用的是PHPUnit进行单元测试。 

> PHPUnit官网：[https://phpunit.de](https://phpunit.de)，如需进行单元测试，请先安装PHPUnit。  

以下是在PhalApi下简化后TDD步骤。

## 定义接口服务的函数签名

当新增一个接口服务时，可先定义好接口服务的函数签名，通俗来说，即确定类名和方法名，以及输入、输出参数、接口服务的名称与描述等。   

例如，对于获取评论的接口服务，可以这样定义。  

```php
<?php
namespace App\Api;

use PhalApi\Api;

/**
 * 评论服务
 */
class Comment extends Api {

    public function getRules() {
        return array(
            'get' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'require' => true, 'min' => 1, 'desc' => '评论ID'),
            ),
        );
    }

    /**
     * 获取评论
     * @desc 根据评论ID获取对应的评论信息
     * @return int      id      评论ID，不存在时不返回
     * @return string   content 评论内容，不存在时不返回
     */
    public function get() {

    }
}
```

通过在线接口详情文档，可以看到对应生成的接口文档内容。 

![](http://cdn7.phalapi.net/20170716191153_34619105bf07324d05e0c1b69b3526f9)

这样就完成了我们伟大的第一步，是不是很简单，很有趣？

## phalapi-buildtest自动生成测试代码

接下来是为新增的接口类编写对应的单元测试。单元测试的代码，可以手动编写，也可以使用phalapi-buildtest脚本命令自动生成。  

生成的命令是：  
```bash
phalapi$ ./bin/phalapi-buildtest ./src/app/Api/Comment.php App\\Api\\Comment > ./tests/app/Api/Comment_Test.php
```

保存的测试文件，统一放在tests目录下，保持与产品代码结构平行，并以“_Test.php”为后缀。  

查看生成的单元测试代码文件./tests/app/Api/Comment_Test.php，可以看到类似以下代码：  
```php
class PhpUnderControl_AppApiComment_Test extends \PHPUnit_Framework_TestCase
{
    public $appApiComment;

    protected function setUp()
    {
        parent::setUp();

        $this->appApiComment = new App\Api\Comment();
    }

    protected function tearDown()
    {
        // 输出本次单元测试所执行的SQL语句
        // var_dump(DI()->tracer->getSqls());

        // 输出本次单元测试所涉及的追踪埋点
        // var_dump(DI()->tracer->getSqls());
    }

    /**
     * @group testGet
     */
    public function testGet()
    {
        $rs = $this->appApiComment->get();

        $this->assertTrue(is_int($rs));

    }
}
```

生成的骨架只是初步的代码，还需要手动调整一下才能最终正常运行。例如需要调整bootstrap.php的文件引入路径。    

```php
require_once dirname(__FILE__) . '/../../bootstrap.php';
```

## 完善单元测试用例

最为重要的是，应该根据**构造-操作-检验（BUILD-OPERATE-CHECK）模式**编写测试用例。对于Api接口层，还需要依赖[]()进行模拟请求。例如这里的：  
```php
class PhpUnderControl_AppApiComment_Test extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        // Step 1. 构造
        $url = 's=Comment.Get';
        $params = array('id' => 1);

        // Step 2. 操作
        $rs = PhalApi\Helper\TestRunner::go($url, $params);
        
        // Step 3. 检验
        $this->assertEquals(1, $rs['id']);
        $this->assertArrayHasKey('content', $rs);
    }
}
```

## 执行单元测试

使用phpunit，可以执行刚生成的测试文件。执行：  

```bash
phalapi$ cd ./tests
tests$ phpunit ./app/Api/Comment_Test.php 
```

会看到类似这样的输出：  
```bash

PHPUnit 4.3.4 by Sebastian Bergmann.

.F

Time: 39 ms, Memory: 8.00Mb

There was 1 failure:

1) PhpUnderControl_AppApiComment_Test::testGet
Failed asserting that false is true.

/path/to/phalapi/tests/app/Api/Comment_Test.php:53

FAILURES!
Tests: 2, Assertions: 1, Failures: 1.
```

## 实现接口服务

在单元测试驱动的引导下，完成接口服务的具体功能，例如这里简单地返回：  
```php
<?php
namespace App\Api;

use PhalApi\Api;

class Comment extends Api {

    public function get() {
        return array('id' => 1, 'content' => '这是一条模拟的评论');
    }
}
```

再次执行单元测试，便可通过了。  

#### 温馨提示：以上示例代码可从[这里](https://github.com/phalapi/phalapi/commit/4eb124792cf6616035dcf937fe56e8e0fc5ebe77)查看。
