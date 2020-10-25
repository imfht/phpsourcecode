# PhalApi 2.x 接口开发 - Hello World

## B站视频链接

[第三课 PhalApi 2.x 接口开发 - Hello World](https://www.bilibili.com/video/av83585951)

[![](http://cdn7.okayapi.com/yesyesapi_20200116091644_8da023f21a5aaea25ad6a8165e7c2738.png)](https://www.bilibili.com/video/av83585951)

## 安装后的运行效果

1、查看在线接口文档：http://api.demo.com/docs.php，查看默认的接口示例。 全部展开的接口。 
2、请求默认接口服务  
3、在线测试请求其他接口服务  
4、接下来，编写第一个Hello World接口  

 
## 目录介绍
```
./src/
└── app
    ├── Api # 放置接口源代码，相当于控制器层
    ├── Common # 公共代码目录，放置工具等
    ├── Domain # 领域业务层，负责业务逻辑和处理
    ├── functions.php
    └── Model # 数据源层，负责数据持久化存储及操作
```
本次重点讲解src目录，后面再补充介绍其他目录结构。

## 第一个接口，以及Api类编写的要求

 + 1、默认所在命名空间必须为```App\Api``` (第2行)
 + 2、具体实现的接口类必须是```PhalApi\Api```的子类 （第4行、第9行）
 + 3、定义接口方法，必须为public访问权限  （第23行）
 + 4、接口参数，放置在getRules()函数方法中  （第11行）
 + 5、返回业务的数据，对应data返回字段，推荐返回对象结构，方便扩展 （第24行）

```php
  1 <?php
  2 namespace App\Api;
  3 
  4 use PhalApi\Api;
  5 
  6 /**
  7  * 第一个接口
  8  */
  9 class Hello extends Api {
 10 
 11     public function getRules() {
 12         return array(
 13             'world' => array(
 14                 'username' => array('name' => 'username', 'desc' => 'B站账号名称'),
 15             ),
 16         );
 17     }
 18 
 19     /**
 20      * 接口名称-欢迎B站
 21      * @desc 欢迎B站的同学，bilibili!!
 22      */
 23     public function world() {
 24         return array('content' => 'Hello ' . $this->username);
 25     }
 26 }
```

## 通过HTTP/HTTPS协议请求Hello World接口
接口地址格式：域名 + ?s=接口服务名称

如：  
```
http://api.demo.com/?s=Hello.World&username=ABC
```

其中，接口服务名称组成格式：  
 + Namespace：命名空间
 + Class：类名
 + Action：方法名
 
## 接口返回格式讲解

```
{
    "ret": 200,
    "data": {
        "title": "Hello World!"
    },
    "msg": ""
}
```

 + ret，return_code，返回状态码，状态码，200表示成功
 + data，表示业务数据
 + msg，message，提示信息，通常是错误的提示信息
 
 


