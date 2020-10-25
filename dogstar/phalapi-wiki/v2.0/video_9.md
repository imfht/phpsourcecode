# 第九课 PhalApi 2.x 接口开发 - 测试驱动开发与PHPUnit

 第九课 PhalApi 2.x 接口开发 - 测试驱动开发与PHPUnit

[第九课 PhalApi 2.x 接口开发 - 测试驱动开发与PHPUnit](https://www.bilibili.com/video/av89209952/)

[![](http://cdn7.okayapi.com/yesyesapi_20200217122006_5369b650f9f453f33be4a3f42aa8cf27.png)](https://www.bilibili.com/video/av89209952/)


## 经典的PHPUnit（XUnit）
PHPUnit官网：https://phpunit.de
 - junit
 - javaunit

## PhalApi默认的单元测试
```
.
├── app # 单元测试目录，和产品源代码目录结构保持一致
│   └── Api
│       ├── Site_Test.php # 测试用例的文档，后续统一为：_Test.php
│       └── User_Test.php
├── bootstrap.php # 单元测试的启动文件
└── phpunit.xml # 单元测试的配置文件
```

## TDD 测试驱动开发
Test Driven Development，要求首先编写单元测试的代码，执行失败的单元测试，最后实现功能，让单元测试通过，并重构，再测试。  
重点颜色：红-绿-重构。

## 第1步：定义接口服务的函数签名
根据需求定义接口签名，并查看接口文档。

http://api.demo.com/docs.php?service=App.Comment.Get&detail=1&type=fold

## 第2步：phalapi-buildtest自动生成测试代码
生成测试骨架代码。

```bash
~/projects/tmp/phalapi/tests
$ ../bin/phalapi-buildtest ../src/app/Api/Comment.php App\\Api\\Comment

$ ../bin/phalapi-buildtest ../src/app/Api/Comment.php 'App\Api\Comment'

## 错误的类名写法（应用使用双反斜杠）
$ ../bin/phalapi-buildtest ../src/app/Api/Comment.php App\Api\Comment 
Error: cannot find class(AppApiComment). 
```

执行此单元测试，发现是失败的（失败是正常的）。
```bash
$ phpunit ./app/Api/Comment_Test.php
PHPUnit 5.7.25 by Sebastian Bergmann and contributors.

.F                                                                  2 / 2 (100%)

Time: 130 ms, Memory: 10.00MB

There was 1 failure:

1) tests\App\Api\PhpUnderControl_AppApiComment_Test::testGet
Failed asserting that false is true.

/Users/dogstar/projects/tmp/phalapi/tests/app/Api/Comment_Test.php:49

FAILURES!
Tests: 2, Assertions: 1, Failures: 1.
```

## 第3步：完善单元测试用例
根据构造-操作-检验（BUILD-OPERATE-CHECK）模式编写测试用例。

```php
    /**
     * @group testGet
     */
    public function testGet()
    {
        // Step 1. 构造
        $url = 's=Comment.Get';
        $params = array('id' => 1);
    
        // Step 2. 操作
        $rs = \PhalApi\Helper\TestRunner::go($url, $params);

        // Step 3. 检验
        $this->assertEquals(1, $rs['id']);
        $this->assertArrayHasKey('content', $rs);
        
    }  
```

## 第4步：执行单元测试（依然失败）
```bash
$ phpunit ./app/Api/Comment_Test.php
PHPUnit 5.7.25 by Sebastian Bergmann and contributors.

.F                                                                  2 / 2 (100%)

Time: 112 ms, Memory: 10.00MB

There was 1 failure:

1) tests\App\Api\PhpUnderControl_AppApiComment_Test::testGet
Failed asserting that null matches expected 1.

/Users/dogstar/projects/tmp/phalapi/tests/app/Api/Comment_Test.php:57

FAILURES!
Tests: 2, Assertions: 2, Failures: 1.

```

## 第5步：实现接口服务
```php
    public function get() {
        return array('id' => 1, 'content' => '这是一条模拟的评论');
    }
```

同时，再去看下在线接口文档测试的效果。

最后，客户端最终请求接口的效果：
http://api.demo.com/?s=App.Comment.Get&id=1  

返回：  
```html
{
  "ret": 200,
  "data": {
    "id": 1,
    "content": "这是一条模拟的评论"
  },
  "msg": ""
}
```

## 延伸：PhalApi核心框架的单元测试

