# dataComposer
# 简介
datacomposer ：数据聚合器

可以把 mysql，mongo，http api，xml， excel 等不同来源的数据按照数据列对应关系（类似于主键外键）聚合成树状结构的数据集合的实用小工具。

它是php第三方类库，通过composer工具安装或升级

目前支持的php框架：laveral， thinkphp。如需其他框架支持可联系作者。

**本工具不是数据查询器，不能跨数据源连接查询（join），在下面有详细说明**

# 痛点和效果
在程序员的日常开发工作中，总是需要同时从多个数据库多个表里读取数据合并在一起，甚至还需要从http api取得数据合并到数据集合里。这些工作模式大同小异，繁琐并且重复。设计这个小工具的目的是为了比较简单的解决关联数据的聚合问题。

通过简单的配置就可以从多种数据源里获取数据，并按照定义的数据对应关系组装数据，省去了大量sql和重复代码。

# 小例子
如下有两张表，想在获取订单数据时，同时获取对应的商户数据。

![image](http://webjxt.cn/dc/dc_20180301143601.png)

1. 添加一个配置文件：order.php
2. 文件内容如下：
```
return [
	"property" => [
		"tableName" => "order",
	],
	"dataSource" => [
		"customer" => [
			"property" => [
				"tableName" => "customer",
				"relationKey" => ["customer_id" => "id"]
			]
		]
	]
]
```
3. 获取数据
```
$dc=new Engine("order");
$data=$dc->GetData();
```
数据如下
```

[
	[
		"id" => 1,
		"customer_id" => 333,
		"customer"=>[
			"id"=>333,
			"name"=>"小米",
			"address"=>"北京"
		],
		"address" => "北京海淀西办街12号",
		"name" => "张三",
		"mobile" => "13455555555",
		"number" => 3
	],
	[
		"id" => 2,
		"customer_id" => 354,
		"customer"=>[
			"id"=>354,
			"name"=>"华为",
			"address"=>"深圳"
		],
		"address" => "上海天津街45号301",
		"name" => "李四",
		"mobile" => "17634343434",
		"number" => 1
	]
];

```


# 支持的数据源
- 几乎所有关系型数据库：mysql，sqlserver，oracle，sqlite 等
- mongo
- redis
- excel
- 文件型数据源：json，xml，phpArray
- http api
- 其他数据源（通过自定义读取器支持）



# 安装
通过 composer  安装

```
composer require peiyu/data-composer
```


# 环境要求
1. php >= 5.4
2. laveral 或者 thinkphp


# 组成
1. 通用配置
2. 数据源定义
3. 调用代码


# 起步
1. 确保php项目完好可用，例如：数据库可用正常访问。
2. 安装好datacomposer
```
composer require peiyu/data-composer
```
3. 在项目配置目录的app.php文件中添加如下属性（重要）

```
'frameworkType'=>"thinkphp", // laveral 或 thinkphp，默认为 laveral 。如果框架是 laveral 可注释本行
```
app.php文件位置请参阅各自框架的技术文档

4. 在项目配置目录下创建目录 DataComposer , 注意大小写一致
5. 在DataComposer目录中创建配置文件：conf.php

内容如下

```
return [
	'connectType'=>'db',  //默认数据源类型,可选项： 'db','mongo','redis','api','file','excel'
	'maxLimit'=>1000,  //每个数据源最大行数
	'cacheEnable'=>false,  //是否启用缓存
	'cacheExpire'=>10,  //缓存时间（分钟）
];
```


6. 在DataComposer目录中创建数据源文件，文件名就是“根数据节点名称”
7. 在数据源文件中编辑好配置并保持，例如“小例子”中的，并且确保表和数据都正确
8. 获取数据，例如:在任意一个 controller 的任意方法中添加如下代码

```
//use DataComposer\Engine;  //必要引用

$dc=new Engine("order");  // order 就是 数据源文件文件名
$data=$dc->GetData();
return $data;
```
9. 重复6-8可创建多个数据源文件并获取数据 

# 技术手册

请先查看“[DataComposer脑图](http://naotu.baidu.com/file/99b507338380407ce6cb3b39bdcf9364?token=142a34916d220f04)”

DataComposer主要由以下三部分组成

### 1. 通用配置

##### frameworkType

- 位置：项目配置目录的app.php文件里
- 选项：laveral ，thinkphp。 默认值：laveral
- 示例: ![image](http://www.webjxt.cn/dc/dc_20180313104645.png)

##### conf.php

- 位置：项目配置目录的DataCompose目录下

属性 | 类型 | 选项 | 默认值 | 备注 
---|---|---|---|---
connectType | string | 'db','mongo','redis','api','file','excel'|'db' |默认数据源类型
maxLimit | int|| 1000 |每个数据源最大行数
cacheEnable | bool  | true,false | false |是否启用缓存
cacheExpire | int||10 |缓存时间（分钟）

- 示例: ![image](http://www.webjxt.cn/dc/dc_2018231120306668.png)

### 2. 数据源定义

#### 数据源文件

- 位置：项目配置目录的DataCompose目录下
- 命名：使用**根数据节点名**作为文件名，区分大小写。
- 文件示例：如上图，根数据节点名worker，文件名worker.php
- 组成：数据源定义完全由一个或多个**数据节点**组成，第一个数据节点为根数据节点，其他为子数据节点，一个数据源文件只能定义一个根数据节点
- 结构：树状（tree）

#### 数据节点

每个数据节点都支持独立定义，互不影响。
- 结构组成
1. 名称：根数据节点名就是文件名，子数据节点名为节点的健名
2. 属性：每个数据节点由两个属性组成：property 和 dataSource
- property 

    配置集合,因数据节点类型不同，配置项各不相同，下面详细说明
    
    配置的所有值中都可以嵌入【参数】
    
    参数格式：“{$” + 参数名 + “}” ，例如： {$v}
    
    对参数赋值查看下面“调用代码”部分

```
"property" => [
		"tableName" => "worker",
		"where" => [["{$k}", '>', '{$v}']],
	],
```


- dataSource

    子数据节点集合,数量不限
    
- 示例  ![image](http://www.webjxt.cn/dc/dc_17150131109122113.png)
    

##### db （关系型数据库）类型的property配置

- connectType

    数据源类型，关系型数据库，值：db。对关系型数据库的访问借助laveral或thinkphp自带组件实现。所以可以支持大多数关系型数据库：mysql，sqlserver，oracle，sqlite，等。如果值和默认配置connectType一致，可省略

- connectName

    连接字符串名称，确保项目连接配置中存在此名称，如果和系统默认连接名一致，可省略
    
- tableName

    表名，必填项

- relationKey（重要属性）

    和父级数据的关联健，根数据节点无需此属性，子数据源必填
    
    格式：父级数据节点列名 => 本数据节点列名
    
    示例：
```
"relationKey" => ["customer_id" => "id"]
```

    
    
- where（可选）

    过滤条件，可支持多个条件，关系为 and ，如果需要实现 or，请使用 callback 。
    
    格式：参照laveral或thinkphp官方文档
    
    示例：
```
"where"=>[["id",'>',23],["status",1]]
```

    
- whereIn（可选）

    多值过滤条件，可支持多个条件，关系为 and ，如果需要实现 or，请使用 callback 。
    
    格式："whereIn" => [列名=>多值,列名=>多值] ,多值为数组格式
    
    示例：
```
"whereIn" => ["id"=>[24,25],"status"=>[1,2]]
```

- orderBy（可选）

    排序条件，可支持多个条件。
    
    格式："orderBy" => [[列名,asc或desc],[列名,asc或desc]] ,asc为默认，可省略
    
    示例：
```
"orderBy"=>[["status",'desc'],["id"]]
```

- fields（可选）

    输出列名，数组格式，默认输出全部列。
    
    格式："fields" => [列名,列名, ...]]
    
    示例：
```
"fields"=>["id","status"]
```

- maxLimit（可选）

    最大行数，如果和系统默认值一致，可省略
    
- cacheEnable（可选）

    是否使用缓存，布尔新 ，true 或 false 。如果和系统默认值一致，可省略
    
- cacheExpire（可选）

    缓存时间，分钟数，int型。如果和系统默认值一致，可省略
    
- callback（可选）

    回调方法，用法后面有详细说明
    
  

##### mongo 类型的property配置

- connectType

    数据源类型，mongodb数据库，值：mongo。对mongodb型数据库的访问借助laveral或thinkphp自带组件实现。如果值和默认配置connectType一致，可省略

- connectName

    连接字符串名称，确保项目连接配置中存在此名称，如果和系统默认连接名一致，可省略
    
- collection

    collection名，必填项

- relationKey（重要属性）

    和父级数据的关联健，根数据节点无需此属性，子数据源必填
    
    格式：父级数据节点列名 => 本数据节点列名
    
    示例：
```
"relationKey" => ["customer_id" => "id"]
```

    
    
- where（可选）

    过滤条件，可支持多个条件，关系为 and ，如果需要实现 or，请使用 callback 。
    
    格式：参照laveral或thinkphp官方文档
    
    示例：
```
"where"=>[["id",'>',23],["status",1]]
```

    
- whereIn（可选）

    多值过滤条件，可支持多个条件，关系为 and ，如果需要实现 or，请使用 callback 。
    
    格式："whereIn" => [列名=>多值,列名=>多值] ,多值为数组格式
    
    示例：
```
"whereIn" => ["id"=>[24,25],"status"=>[1,2]]
```

- orderBy（可选）

    排序条件，可支持多个条件。
    
    格式："orderBy" => [[列名,asc或desc],[列名,asc或desc]] ,asc为默认，可省略
    
    示例：
```
"orderBy"=>[["status",'desc'],["id"]]
```

- fields（可选）

    输出列名，数组格式，默认输出全部列。
    
    格式："fields" => [列名,列名, ...]]
    
    示例：
```
"fields"=>["id","status"]
```

- maxLimit（可选）

    最大行数，如果和系统默认值一致，可省略
    
- cacheEnable（可选）

    是否使用缓存，布尔新 ，true 或 false 。如果和系统默认值一致，可省略
    
- cacheExpire（可选）

    缓存时间，分钟数，int型。如果和系统默认值一致，可省略
    
- callback（可选）

    回调方法，用法后面有详细说明
  

##### redis 类型的property配置


- connectType

    数据源类型，redis，值：redis。对redis型数据库的访问借助laveral或thinkphp自带组件实现。如果值和默认配置connectType一致，可省略

- connectName

    连接字符串名称，确保项目连接配置中存在此名称，如果和系统默认连接名一致，可省略

- relationKey（重要属性）

    和父级数据的关联健，根数据节点无需此属性，子数据源必填
    
    格式：父级数据节点列名,(无需定义本数据节点列名，列名为 value)
    
    示例：
```
"relationKey" =>"customer_id"
```

- cacheEnable（可选）

    是否使用缓存，布尔新 ，true 或 false 。如果和系统默认值一致，可省略
    
- cacheExpire（可选）

    缓存时间，分钟数，int型。如果和系统默认值一致，可省略
    


#####  http api 类型的property配置

- connectType

    数据源类型，http api 类型，值：api。利用第三方组件 [GuzzleHttp](http://guzzle-cn.readthedocs.io/zh_CN/latest/)  实现。如果值和默认配置connectType一致，可省略


- relationKey（必填,重要属性）

    和父级数据的关联健，根数据节点无需此属性，子数据源必填
    
    格式：父级数据节点列名 => 本数据节点列名
    
    示例：
```
"relationKey" => ["customer_id" => "id"]
```

- url（必填）

    http 请求地址，必须是完全地址，例如 'http://www.jxt.cn/check/this' 。

- method（可选）

    http请求方式 ，默认为 get，可支持 ‘get’，‘post’ 等方式。

- options（可选）

    请求参数控制请求的各个方面，包括头信息、查询字符串参数、超时、请求主体等。请参考[文档](http://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
    
    示例：
    
```
"options"=>[
    "query"=>["id"=>44,"_d_"=>"ekss88"],
    "form_params"=>["name"=>"nam","s"=>1],
    "headers"=>[],
    "timeout"=>3
]
```

- callback（可选）

    回调方法，用法后面有详细说明

##### 文件类型的property配置

- connectType

    数据源类型，文件类型，值：file。如果值和默认配置connectType一致，可省略


- fileType（必填）

    文件类型，选项：json，xml，phparray（ Array 类型 ）。字符串类型。

- fullFileName（必填）

    文件全名，包含文件路径和文件名。字符串类型。
    
- relationKey（必填,重要属性）

    和父级数据的关联健，根数据节点无需此属性，子数据源必填
    
    格式：父级数据节点列名 => 本数据节点列名
    
    示例：
```
"relationKey" => ["customer_id" => "id"]
```
- callback（可选）

    回调方法，用法后面有详细说明


##### excel类型的property配置
- connectType

    数据源类型，excel类型，值：excel。如果值和默认配置connectType一致，可省略

- fullFileName（必填）

    文件全名，包含文件路径和文件名。字符串类型。
    
- relationKey（必填,重要属性）

    和父级数据的关联健，根数据节点无需此属性，子数据源必填
    
    格式：父级数据节点列名 => 本数据节点列名
    
    示例：
```
"relationKey" => ["customer_id" => "id"]
```
- callback（可选）

    回调方法，用法后面有详细说明

### 3. 调用代码

##### 引用    

```
use DataComposer\Engine;  //必要引用
```
##### 实例化

-  第一种（推荐）
```
$dc=new Engine("order");  // order 就是 数据源文件文件名
```
-  第二种，完全不使用配置文件，实例化时输入配置 

属性 | 类型  | 备注 
---|---|---
name（必须） | string | 数据源文件文件名
frameworktype（可选） | string | php框架类型：laveral， thinkphp
config（可选） | array | 默认配置信息，和 conf.php 内容一致
dataComposerConfig（可选） | array | 数据源配置信息

##### 给【property】属性中的变量赋值，如果【property】属性中的无变量，可忽略次项

- 方法：SetParameterValue
- 参数：

参数 | 类型  | 备注 
---|---|---
name（必须） | string | 数据节点名
parameterValue（必须） | array | 变量名和值的键值对


```
//如果 property 如下
"property" => [
		"tableName" => "worker",
		"where" => [["id", '>', '{$_id}'],['name','{$_name}']],
	],
```

```
// 则 赋值代码如下
$dc->SetParameterValue('worker',['_id'=>23,'_name'=>'li']);
```


##### 获取数据

```
$data=$dc->GetData();
```

GetData 方法还有一个可选参数

属性 | 类型  | 备注 
---|---|---
nameList（可选） | array | 子节点白名单    


如果本次获取数据只需要部分节点数据，就可以把需要的节点名组成数组输入。
注意：根节点名无效。如果父级节点名不在数组中，子节点名会被忽略。


```
$data=$dc->GetData(['customer']);
```


### 4. 高级用法
>     编写中...