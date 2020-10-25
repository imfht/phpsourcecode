#简介
protox是一款php数据类型格式化工具，主要用于数据类型转换和数据初始化，省去很多手工初始化和类型转换操作。   
转换后的数据很方便使用json和amf进行编码，终于不用担心一个int转来转去变成string了。   
只需要一次数据定义和一次数据转换即可。  

#类型定义
在指定目录下创建一个php文件，定义类名为:{文件名}_protocol, 同时继承protox.    
定义成员变量fields，如下:
```php
//person.php
class person_protocol extends protox
{
	protected $fields = array(
		'name' => 'string',//string类型
		'age' => 'int',//int类型
		'phone' => 'string',//string类型
		'address' => 'string',//string类型
		'qq' => 'string|optional',//string类型，并且是可选项
		'email' => 'string|optional',//string类型，并且是可选项
	);
}
```

#类型转换
转换时只需要调用protox::make({文件名}，{参数})即可。    
```php
//test.php
define('ROOT', dirname(__FILE__));
require('protox.php');
protox::init(array(
	'path' => ROOT . '/protocol/',//设置类型目录
));

$input = array(
	'name' => 'test',
	'age' => '123a',
	'qq' => 123456,
	'phone' => 111
);
$output = protox::make('person', $input);
var_dump($output);
/*
可以看到所有key都是按定义好的类型进行转换的，并且对于必选属性，都会默认给定初始值。
array(5) {
  ["name"]=>
  string(4) "test"
  ["age"]=>
  int(123)
  ["phone"]=>
  string(3) "111"
  ["address"]=>
  string(0) ""
  ["qq"]=>
  string(6) "123456"
}
*/
```

#其它类型说明
```php
//有些时候可能我们如果不想转换类型，想直接使用参数，那么可以将fields值为*即可：
//mytype.php
class mytype_protocol extends protox
{
	protected $fields = '*';
}


//如果我们不想依赖于某个key,只想格式化一个数组，比如从mysql读出的数据，想把数据格式化一遍，然后json输出，可以这样：
//product.php
class product_proto extends protox
{
	protected $fields = array(
		'id' => 'int',
		'title' => 'string',
		'price' => 'int',
		'details' => 'string',
		'picture' => 'string|optional'
	);
}

//mylist.php
class mylist_protocol extends protox
{
	//如果product不需要共用，只在这个类里有效，那么你可以把product_proto复制到这个文件即可，省去了为了一个复杂的类型创建很多文件
	protected $fields = 'array.product';
}
```
目前可以支持的类型:int,double,string,array,object    
同时array和object可以有子类型，当然也可以没有,如果子类型为空，那么数据将原样添加到数组里.    
比如：array.int表示是一个数组，并且每个元素是int, 输出的数据key从0开始(如果你输入的数据是带key，这个key将会被忽略).   
那么object.product,表示一个对像，每个成员类型是product,那么key就是输入参数对应的key.   