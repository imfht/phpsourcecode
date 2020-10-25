# EasyChart


服务器端一般分为入口文件和执行文件

入口文件为客户端js库中设置的opt.uri地址，所有通讯都将发送到此文件

入口文件接收请求，解析api，根据api导航加载对应的执行文件

执行文件创建服务器端EasyChart对象，处理数据，将结果返回浏览器

EasyChart类包含了入口文件和执行文件常用的功能

## option和data

EasyChart对象内置option和data两个对象，用于管理客户端echarts组件和数据

### option

管理echarts配置信息，可将配置选项直接透传给客户端的echarts图表，可通过此对象修改echarts配置或附加配置信息

option支持php数组(array)或字符串类型的js脚本

option提供`set($name,$value)`和`clean($name="")`方法，用来管理配置信息

| 名称  |     参数     |                         说明                          |
| ----- | ------------ | ----------------------------------------------------- |
| set   | $name,$value | 设置一项配置信息                                      |
| clean | $name=""     | 清空所有名称为$name的配置信息，若$name=""清空所有配置 |

option的set是可叠加的，多次set相同$name配置，不会进行替换，而是采用合并的方式进行累加

```
//创建对象
$chart=new EasyChart("bar");

//设置标题，采用数组方式
$chart->option->set("title",array(
  'text'=>'I am title1'
  ));
//这时，标题为I am title1

//设置标题和对齐，采用js代码写法，与数组写法效果相同
$chart->option->set("title","
{
        text: 'I am title2',
        right: '10'
}
 ");
//这时，标题为I am title2，右对齐10px

//设置对齐，因为是合并方式，所以之前设置的标题会保留
$chart->option->set("title",array(
  'right'=>'auto',
  'left'=>'10'
  ));
//这时，标题为I am title2，左对齐10px

//清空标题设置
$chart->option->clean("title");

```

同时，option提供`setJS($js)`方法来为组件附加js代码，可用于实现数据初始化、事件绑定等操作

附加的js代码会在一个闭包环境执行，不用担心会造成变量污染，js中可使用`EasyChart`对象，此对象为当前EasyChart实例

```
$chart->option->setJS("

EasyChart.on('click',function(data){
  console.log(data);
});

");
```

> `$chart->option->set()`和`$chart->option->setJS()`已经映射到根对象，也就是说可以直接使用`$chart->set()`和`$chart->setJS()`

### data

管理数据，根据图表生成不同格式的数据，一般无需单独调用

## 方法

|    名称    |                     参数                      |                               说明                                |
| ---------- | --------------------------------------------- | ----------------------------------------------------------------- |
| title      | $title='',$subtitle='',$x="left"              | 设置标题，子标题，位置                                            |
| zoom       | $enable=true                                  | 启用echarts的dataZoom                                             |
| padding    | $left="60",$right="60",$top="60",$bottom="60" | 设置图表边界                                                      |
| toolbox    | $conf=""                                      | 启用默认toolbox，可以通过$conf设置其他echarts配置选项             |
| add        | 可变                                          | 添加展示数据，参数根据图表类型确定                                |
| clean      |                                               | 清空已添加的数据                                                  |
| right2left |                                               | 反转数据显示                                                      |
| out        | $to_str=false                                 | 输出结果，默认输出到浏览器，若要作为string返回，设置 $to_str=true |

## 静态方法

|  名称  |        参数         |                                                       说明                                                       |
| ------ | ------------------- | ---------------------------------------------------------------------------------------------------------------- |
| getAPI | $key="api"          | 解析api并返回，默认寻找客户端提交的EC_api变量，未找到则寻找$key指定的变量                                        |
| server | $dir="",$stop=false | 启动一个简易导航，$dir设置导航根目录，若$stop=true，api解析失败将返回error，反之，失败将不做任何操作继续向下执行 |
| getVar | $name,$default=""   | 获取客户端提交的变量                                                                                             |
| error  | $msg                | 输出错误信息并停止执行                                                                                           |


## 数据处理

在执行文件中，需要获取数据，经过EasyChart处理后返回客户端，对数据的处理的一般流程

1. 创建对象，指定图表类型
2. 添加数据
3. 输出

不同图表类型的主要区别，add()传入的数据格式不同

### bar类型图表实例

```
//创建对象，指定图表类型为 bar
$chart=new EasyChart("bar");

//添加数据
$chart->add("apple",365);
$chart->add("banana",200);
$chart->add("orange",180);

//输出
$chart->out();
```

### bar类型3D图表实例

```
//创建对象，指定图表类型为 bar3D
$chart=new EasyChart("bar3D");

//添加数据
$chart->add("apple","one",365);
$chart->add("apple","two",390);
$chart->add("banana","one",200);
$chart->add("banana","two",260);
$chart->add("orange","one",180);
$chart->add("orange","two",130);

//输出
$chart->out();

```


## 接口导航

EasyChart提供简单的接口导航功能，用于快速创建入口文件

> 入口文件对应js库中opt.uri设置，是所有请求的入口，入口文件根据js库的api进行导航，require对应的执行文件

### 入口文件

```
<?php
require "EasyChart/dist/Server/loader.php";
EasyChart::server();
?>
```

> 这是一个最简入口
> EasyChart::server()会按照目录结构自动完成api导航

也可自行处理导航

```
require 'EasyChart/src/Server/loader.php';

//获取请求的api
$_api =EasyChart::getAPI();

//可以获取其他数据
$count=EasyChart::getVar("count");

//可以进行权限验证或其他操作
if (!do_some_auth()) EasyChart::error('Access denied');

//自行处理api
$file=do_someting($_api);

//加载对应的执行文件
if (file_exists($file)) {
		require $file;
}else{
		EasyChart::error('API Not Found');
}

```
