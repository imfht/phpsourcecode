# 接口参数


## 参数规则格式

参数规则是针对各个接口服务而配置的多维规则数组，由接口类的```getRules()```方法返回。其中，  

 + 一维下标是接口类的方法名，对应接口服务的Action；
 + 二维下标是类属性名称，对应在服务端获取通过验证和转换化的最终客户端参数；
 + 三维下标```name```是接口参数名称，对应外部客户端请求时需要提供的参数名称。  

小结如下：  
```php
    public function getRules() {
        return array(
            '接口类方法名' => array(
                '接口类属性' => array('name' => '接口参数名称', ... ... ),
            ),
        );
    }
```  

在接口实现类里面```getRules()```成员方法配置参数规则后，便可以通过类属性的方式，根据配置指定的名称获取对应的接口参数，如上面的：```$this->username```和```$this->password```。

注意：在每个控制器里面都会存在一个getRules方法，该方法里面的用于对应的接口传递的参数行为约束。  

```
/**
 * 这个方法是做参数验证的
 * @return [type] [description]
 */

/* 使用示例
public function getRules() {
    return array(
        '接口类方法名' => array(
            '接口类属性' => array('name' => '接口参数名称', ... ... ),
        ),
    );
}
*/
public function getRules() {
    
    return array(
        // 一维下标是接口类的方法名，对应接口服务的Action；
        // '方法名' => array
        'index' => array(
            //  二维下标是类属性名称，对应在服务端获取通过验证和转换化的最终客户端参数；
            //  array()中是对该属性的一个修饰 ，属性的值从get传参过来
            //  'name' : 指定接口参数名称，对应外部客户端请求时需要提供的参数名称。$_GET['username'] 
            //  'desc'： 描述，生成接口文档的时候使用
            //
            //  $this->username = isset( $_GET['username'] ) ? $_GET['username'] : 'PhalApi'

            // '属性名称' => array()
            'username'  => array('name' => 'username', 'default' => 'PhalApi', 'desc' => '用户名'),
        ),
    );
}
```

> 以上内容摘自[PhalApi框架 | Kuma's Blog](http://blog.kuma8866.top/posts/745665408/)。顺便一提，这篇文章整理得非常棒，建议阅读，关于如何使用PhalApi开发微信小程序。 

## 三级参数规则配置

参数规则主要有三种，分别是：系统参数规则、应用参数规则、接口参数规则。  

### 系统参数

系统参数是指被框架保留使用的参数。目前已被PhalApi占用的系统参数只有一个，即：service参数（缩写为s参数），前面已有介绍。  

### 应用参数

应用参数是指在一个接口系统中，全部项目的全部接口都需要的参数，或者通用的参数。假如我们的商城接口系统中全部的接口服务都需要必须的签名sign参数，以及非必须的版本号，则可以在```./config/app.php```中的```apiCommonRules```进行应用参数规则的配置：  
```php
<?php
return array(
    /**
     * 应用接口层的统一参数
     */
    'apiCommonRules' => array(
        //签名
        'sign' => array(
            'name' => 'sign', 'require' => true,
        ),
        //客户端App版本号，默认为：1.4.0
        'version' => array(
            'name' => 'version', 'default' => '1.4.0', 
        ),
    ),
)
```

如果需要对全局的应用参数进行定制或扩展，可重载```\PhalApi\Api::getApiCommonRules()```方法。例如：
```php
<?php
namespace App\Api;
use PhalApi\Api;

class Demo extends Api {
    protected function getApiCommonRules() {
        // TODO：以下为默认实现代码，可根据需要进行动态调整
        return DI()->config->get('app.apiCommonRules', array());
    }

}
```
通过上面方式，可以解决特定接口下不需要全局应用参数的问题。需要PhalApi 2.10.0及上以版本支持。

### 接口参数

接口参数是指各个具体的接口服务所需要的参数，为特定的接口服务所持有，独立配置。并且进一步在内部又细分为两种：  

 + **通用接口参数规则**：使用```*```作为下标，对当前接口类全部的方法有效。  
 + **指定接口参数规则**：使用方法名作为下标，只对接口类的特定某个方法有效。  


例如为了加强安全性，需要为全部的用户接口服务都加上长度为4位的验证码参数：  
```php
    public function getRules() {
        return array(
            '*' => array(
                'code' => array('name' => 'code', 'require' => true, 'min' => 4, 'max' => 4),
            ),
            'login' => array(
                'username' => array('name' => 'username', 'require' => true),
                'password' => array('name' => 'password', 'require' => true, 'min' => 6),
            ),
        );
    }
```
现在，当再次请求用户登录接口，除了要提供用户名和密码外，我们还要提供验证码code参数。并且，对于Api\User类的其他方法也一样。  

### 多个参数规则时的优先级

当同一个参数规则分别在应用参数、通用接口参数及指定接口参数出现时，后面的规则会覆盖前面的，即具体化的规则会替换通用的规则，以保证接口参数满足特定场合的定制要求。  

简而言之，多个参数规则的优先级从高到下，分别是（正如你想到的那样）： 
  
 + 1、指定接口参数规则
 + 2、通用接口参数规则
 + 3、应用参数规则
 + 4、系统参数规则

> 温馨提示：如果对过滤器配置了白名单，必选参数最终会自动切换为可选参数，即```require = false```，详细请参考[白名单配置](http://docs.phalapi.net/#/v2.0/filter?id=%E7%99%BD%E5%90%8D%E5%8D%95%E9%85%8D%E7%BD%AE)。   

对于重叠的接口参数，若指定接口不需要某个接口参数，可以通过将参数规则配置置为NULL或FALSE，从而取消此参数。例如取消sign全局参数；
```php
    public function getRules() {
        return array(
            'login' => array(
                'username' => array('name' => 'username', 'require' => true),
                'password' => array('name' => 'password', 'require' => true, 'min' => 6),
                'sign' => NULL, // 取消sign参数，取消后接口文档不会再显示此参数。需要PhalApi 2.10.0及以上版本支持
            ),
        );
    }
```

## 参数规则配置详细说明

具体的参数规则，根据不同的类型有不同的配置选项，以及一些公共的配置选项。目前，主要的类型有：字符串、整数、浮点数、布尔值、时间戳/日期、数组、枚举类型、文件上传和回调函数。    
 

类型 type|参数名称 name|是否必须 require|默认值 default|最小值 min，最大值 max|更多配置选项（无特殊说明，均为可选）
---|---|---|---|---|---
字符串|string|TRUE/FALSE，默认FALSE|应为字符串|可选|regex选项用于配置正则匹配的规则；format选项用于定义字符编码的类型，如utf8、gbk、gb2312等
整数|int|TRUE/FALSE，默认FALSE|应为整数|可选|---
浮点数|float|TRUE/FALSE，默认FALSE|应为浮点数|可选|---
布尔值|boolean|TRUE/FALSE，默认FALSE|true/false|---|以下值会转换为TRUE：ok，true，success，on，yes，1，以及其他PHP作为TRUE的值
时间戳/日期|date|TRUE/FALSE，默认FALSE|日期字符串|可选，仅当为format配置为timestamp时才判断，且最值应为时间戳|format选项用于配置格式，为timestamp时会将字符串的日期转换为时间戳
数组|array|TRUE/FALSE，默认FALSE|字符串或者数组，为非数组会自动转换/解析成数组|可选，判断数组元素个数|format选项用于配置数组和格式，为explode时根据separator选项将字符串分割成数组, 为json时进行JSON解析，未指定format时将直接转成为一个元素的数据，即：array(参数)
枚举|enum|TRUE/FALSE，默认FALSE|应为range选项中的某个元素|---|必须的range选项，为一数组，用于指定枚举的集合
文件|file|TRUE/FALSE，默认FALSE|数组类型|可选，用于表示文件大小范围，单位为B|range选项用于指定可允许上传的文件类型；ext选项用于表示需要过滤的文件扩展名
回调|callable/callback|TRUE/FALSE，默认FALSE|---|---|callable/callback选项用于设置回调函数，params选项为回调函数的第三个参数（另外第一个为参数值，第二个为所配置的规则）  

## 公共参数配置选项

公共的配置选项，除了上面的类型、参数名称、是否必须、默认值，还有说明描述、数据来源。下面分别简单说明。  
 
 + **类型 type**  
 用于指定参数的类型，可以是string、int、float、boolean、date、array、enum、file、callable，或者自定义的类型。未指定时，默认为字符串。  
 > 注意：参数类型是约定客户端提供参数的类型，但不强制。最终PhalApi会根据配置的参数类型和客户端提供的实际参数值根据配置的参数规则进显示类型行转换、判断和检测。即：接口参数类型不强制匹配客户端实际参数类型，但会保证服务端PHP代码最终获取的参数类型。
  
 + **参数名称 name**  
 接口参数名称，即客户端需要传递的参数名称。与PHP变量规则一样，以下划线或字母开头。此选项必须提供，否则会提示错误。   
  
 + **是否必须require**  
 为TRUE时，表示此参数为必须值；为FALSE时，表示此参数为可选。未指定时，默认为FALSE。  
  
 + **默认值 default**  
 未提供接口参数时的默认值。未指定时，默认为NULL。  
  
 + **最小值 min，最大值 max**  
 部分类型适用。用于指定接口参数的范围，比较时采用的是闭区间，即范围应该为：[min, max]。也可以只使用min或max，若只配置了min，则表示：[min, +∞)；若只配置了max，则表示：(-∞, max]。   

 + **说明描述 desc**  
 用于自动生成在线接口详情文档，对参数的含义和要求进行扼要说明。未指定时，默认为空字符串。  
  
 + **数据来源 source**  
 指定当前单个参数的数据来源，可以是post、get、cookie、server、request、header、或其他自定义来源。未指定时，默认为统一数据源。目前支持的source与对应的数据源映射关系如下：   
  
source|对应的数据源  
---|---
post     | $_POST              
get      | $_GET               
cookie   | $_COOKIE            
server   | $_SERVER            
request  | $_REQUEST           
header   | $_SERVER['HTTP_X']

通过source参数可以轻松、更自由获取不同来源的参数。以下是一些常用的配置示例。  
```php
// 获取HTTP请求方法，判断是POST还是GET
'method' => array('name' => 'REQUEST_METHOD', 'source' => 'server'),

// 获取COOKIE中的标识
'is_new_user' => array('name' => 'is_new_user', 'source' => 'cookie'),

// 获取HTTP头部中的编码，判断是否为utf-8
'charset' => array('name' => 'Accept-Charset', 'source' => 'header'),
```

若配置的source为无效或非法时，则会抛出异常。如配置了```'source' => 'NOT_FOUND'```，会得到：     
```
"msg": "服务器运行错误: 参数规则中未知的数据源：NOT_FOUND"
```

 + **错误提示 message**  
 如果配置此项，当接口参数错误时优先显示此错误提示信息，让开发人员可以自定义友好的错误提示信息，并支持i18n国际翻译。

> 温馨提示：message配置，需要PhalApi V2.5.0 及以上版本才支持。 

 + **解析后回调函数 on_after_parse**  

 用于在接口参数正确解析后且在返回前的钩子回调函数，回调函数签名是：```<T> func(<T>)```，其中T类型是当前对应的接口参数的类型。支持两种配置方式：  
  + 多个函数名的管通配置：例如为字符串参数配置```trim|strtolower```，则表示先进行trim()操作，再进行strtolower()转大写操作；  
  + 匿名函数的配置：例如为字符串参数配置```function ($value) { return strtolower(trim($value)); })```，则效果和前文一样。  

下面结合示例进行介绍。
假设配置的接口参数如下：
```
// 对于字符串的回调配置
'username' => array('name' => 'username', 'type' => 'string', 'on_after_parse' => 'trim|strtolower'),

// 对于数组的回调配置
'options' => array('name' => 'options', 'type' => 'array', 'format' => 'explode', 'on_after_parse' => 'array_unique'),
```

且假设客户端传递的参数是：```&username=PhalApi &options=A,A,A,B,B,C```

最后在Api层得到的结果是：
```
var_dump($this->username); // PHALAPI(且最后没有空格) 

var_dump($this->options); // array('A', 'B', 'C')，已去重
```

也可以通过匿名函数的方式来配置，下面配置效果一样：
```
// 对于字符串的回调配置
'username' => array('name' => 'username', 'type' => 'string', 
        'on_after_parse' => function ($value) { return strtolower(trim($value)); }
        ),

// 对于数组的回调配置
'options' => array('name' => 'options', 'type' => 'array', 'format' => 'explode', 
        'on_after_parse' => function ($value) { return array_unique($value); }
        ),
```

  关于on_after_parse回调配置，两点重要信息小结：  

  + 小结1：回调时机是在接口参数的全部规则通过后，在返回最终结果前；
  + 小结2：回调函数签名应符合```<T> func(<T>)```，即接收一个参数且返回一个参数。例如字符串类型可以用：ltrim、rtrim、trim、strtolower、strtoupper、addslashes、strrev等函数，数组类型可以用：array_unique、array_reverse、array_values等。若回调函数不可调用则跳过，避免造成接口服务不可用。

> 温馨提示：on_after_parse配置，需要PhalApi V2.9.0 及以上版本才支持。 

 + **是否在接口文档隐藏 is_doc_hide**  
 默认为false，即全部接口参数会显示在接口文档上。如果不希望显示在接口文档但仍需要在Api层的PHP代码中使用，可设置此配置项为true。 

>  温馨提示：is_doc_hide配置，需要PhalApi V2.10.0 及以上版本才支持。

## 9种基本接口参数类型

对于各种参数类型，结合示例说明如下。  

 + **字符串 string**  

当一个参数规则未指定类型时，默认为string。如最简单的：  
```php
array('name' => 'username')
```
> **温馨提示：**这一小节的参数规则配置示例，都省略了类属性，以关注配置本身的内容。  

这样就配置了一个参数规则，接口参数名字叫username，类型为字符串。  

一个完整的写法可以为：
```php
array('name' => 'username', 'type' => 'string', 'require' => true, 'default' => 'nobody', 'min' => 1, 'max' => 10)
```
这里指定了为必选参数，默认值为nobody，且最小长度为1个字符，最大长度为10个字符，若传递的参数长度过长，如```&username=alonglonglonglongname```，则会异常失败返回：
```
"msg": "非法请求：username.len应该小于等于10, 但现在username.len = 21"
```
  

当需要验证的是中文的话，由于一个中文字符会占用3个字节。所以在min和max验证的时候会出现一些问题。为此，PhalApi提供了format配置选项，用于指定字符集。如：  

```php
array('name' => 'username', 'type' => 'string', 'format' => 'utf8', 'min' => 1, 'max' => 10)
```
  
我们还可以使用```regex```下标来进行正则表达式的验证，一个邮箱的例子是：  
```php
array('name' => 'email', 'regex' => "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i")
```

 + **整型 int**  

整型即自然数，包括正数、0和负数。如通常数据库中的id，即可配置成：  
```php
array('name' => 'id', 'type' => 'int', 'require' => true, 'min' => 1)
```

当传递的参数，不在其配置的范围内时，如```&id=0```，则会异常失败返回：
```
"msg": "非法请求：id应该大于或等于1, 但现在id = 0"
```

另外，对于常见的分页参数，可以这样配置：  
```php
array('name' => 'page_num', 'type' => 'int', 'min' => 1, 'max' => 20, 'default' => 20)
```
即每页数量最小1个，最大20个，默认20个。  


 + **浮点 float**  

浮点型，类似整型的配置，此处略。 

 + **布尔值 boolean**  

布尔值，主要是可以对一些字符串转换成布尔值，如ok，true，success，on，yes，以及会被PHP解析成true的字符串，都会转换成TRUE。如通常的“是否记住我”参数，可配置成：
```php
array('name' => 'is_remember_me', 'type' => 'boolean', 'default' => TRUE)
```
  
则以下参数，最终服务端会作为TRUE接收。  
```
?is_remember_me=ok
?is_remember_me=true
?is_remember_me=success
?is_remember_me=on
?is_remember_me=yes
?is_remember_me=1
```

 + **日期 date**  

日期可以按自己约定的格式传递，默认是作为字符串，此时不支持范围检测。例如配置注册时间：
```php
array('name' => 'register_date', 'type' => 'date')
```
对应地，```register_date=2015-01-31 10:00:00```则会被获取到为："2015-01-31 10:00:00"。
  
当需要将字符串的日期转换成时间戳时，可追加配置选项```'format' => 'timestamp'```，则配置成：
```php
array('name' => 'register_date', 'type' => 'date', 'format' => 'timestamp')
```
则上面的参数再请求时，则会被转换成：1422669600。  

此时作为时间戳，还可以添加范围检测，如限制时间范围在31号当天：  
```php
array('name' => 'register_date', 'type' => 'date', 'format' => 'timestamp', 'min' =>  1422633600, 'max' => 1422719999)
```

当配置的最小值或最大值为字符串的日期时，会自动先转换成时间戳再进行检测比较。如可以配置成：  
```php
array('name' => 'register_date', ... ... 'min' => '2015-01-31 00:00:00', 'max' => '2015-01-31 23:59:59')
```

 + **数组 array**  

很多时候在接口进行批量获取时，都需要提供一组参数，如多个ID，多个选项。这时可以使用数组来进行配置。如：  
```php
array('name' => 'uids', 'type' => 'array', 'format' => 'explode', 'separator' => ',')
```

这时接口参数```&uids=1,2,3```则会被转换成：  
```php
array ( 0 => '1', 1 => '2', 2 => '3', )
```

如果设置了默认值，那么默认值会从字符串，根据相应的format格式进行自动转换。如：  
```php
array( ... ... 'default' => '4,5,6')
```
那么在未传参数的情况下，自动会得到：  
```php
array ( 0 => '4', 1 => '5', 2 => '6', )
```

又如接口需要使用JSON来传递整块参数时，可以这样配置：
```php
array('name' => 'params', 'type' => 'array', 'format' => 'json')
```
对应地，接口参数```&params={"username":"test","password":"123456"}```则会被转换成：
```php
array ( 'username' => 'test', 'password' => '123456', )
```
> **温馨提示：**使用JSON传递参数时，建议使用POST方式传递。若使用GET方式，须注意参数长度不应超过浏览器最大限制长度，以及URL编码问。  

若使用JSON格式时，设置了默认值为：  
```php
array( ... ... 'default' => '{"username":"dogstar","password":"xxxxxx"}')
```
那么在未传参数的情况下，会得到转换后的：  
```php
array ( 'username' => 'dogstar', 'password' => 'xxxxxx', )
```

特别地，当配置成了数组却未指定格式format时，接口参数会转换成只有一个元素的数组，如接口参数：```&name=test```，会转换成：
```php
array ( 0 => 'test' )
```

 + **枚举 enum**  

在需要对接口参数进行范围限制时，可以使用此枚举型。如对于性别的参数，可以这样配置：
```php
array('name' => 'sex', 'type' => 'enum', 'range' => array('female', 'male'))
```
当传递的参数不合法时，如```&sex=unknow```，则会被拦截，返回失败：
```
"msg": "非法请求：参数sex应该为：female/male，但现在sex = unknow"
```
  
关于枚举类型的配置，这里需要特别注意配置时，应尽量使用字符串的值。 因为通常而言，接口通过GET/POST方式获取到的参数都是字符串的，而如果配置规则时指定范围用了整型，会导致底层规则验证时误判。例如接口参数为```&type=N```，而接口参数规则为：  
```php
array('name' => 'type', 'type' => 'enum', 'range' => array(0, 1, 2))
```
则会出现以下这样的误判：  
```php  
var_dump(in_array('N', array(0, 1, 2))); // 结果为true，因为 'N' == 0
```  
  
为了避免这类情况发生，应该使用使用字符串配置范围值，即可这样配置：  
```php
array('name' => 'type', 'type' => 'enum', 'range' => array('0', '1', '2'))
```
  
 + **文件 file**  

在需要对上传的文件进行过滤、接收和处理时，可以使用文件类型，如：
```php
array(
    'name' => 'upfile', 
    'type' => 'file', 
    'min' => 0, 
    'max' => 1024 * 1024, 
    'range' => array('image/jpeg', 'image/png') , 
    'ext' => array('jpeg', 'png')
)
```
其中，min和max分别对应文件大小的范围，单位为字节；range为允许的文件类型，使用数组配置，且不区分大小写。 
  
如果成功，返回的值对应的是```$_FILES["upfile"]```，即会返回：
```php
array(
     'name' => ..., // 被上传文件的名称
     'type' => ..., // 被上传文件的类型
     'size' => ..., // 被上传文件的大小，以字节计
     'tmp_name' => ..., // 存储在服务器的文件的临时副本的名称
)
```
对应的是：  

 + $_FILES["upfile"]["name"] - 被上传文件的名称
 + $_FILES["upfile"]["type"] - 被上传文件的类型
 + $_FILES["upfile"]["size"] - 被上传文件的大小，以字节计
 + $_FILES["upfile"]["tmp_name"] - 存储在服务器的文件的临时副本的名称
 + $_FILES["upfile"]["error"] - 由文件上传导致的错误代码
  
> 参考：以上内容来自W3School，文件上传时请使用表单上传，并enctype 属性使用"multipart/form-data"。更多请参考[PHP 文件上传](http://www.w3school.com.cn/php/php_file_upload.asp)。  

若需要配置默认值default选项，则也应为一数组，且其格式应类似如上。

其中，ext是对文件后缀名进行验证，当如果上传文件后缀名不匹配时将抛出异常。文件扩展名的过滤可以类似这样进行配置：

 + 单个后缀名 - 数组形式  
```php
'ext' => array('jpg')
```

 + 单个后缀名 - 字符串形式  
```php
'ext' => 'jpg'
```

 + 多个后缀名 - 数组形式  
```php
'ext' => array('jpg', 'jpeg', 'png', 'bmp')
```

 + 多个后缀名 - 字符串形式（以英文逗号分割）  
```php
'ext' => 'jpg,jpeg,png,bmp' 
```

最后，如果需要保存图片，可以使用以下参考代码：
```php
move_uploaded_file($this->file->['tmp_name'], API_ROOT . '/public/upload/' . $this->file['name']);
```
其中，file是接口参数配置中的key值，视情况替换。保存的路径和名称，可根据需要进行调整。  
通常不建议把图片存放在接口本地服务器上，而是推荐尽量放置在CDN云服务上。
  
 + **回调 callable/callback**  

当需要利用已有函数进行自定义验证时，可采用回调参数规则，如配置规则：  

```php
array('name' => 'version', 'type' => 'callable', 'callback' => 'App\\Common\\Request\\Version::formatVersion')
```
然后，回调时将调用下面这个新增的类函数：
```php
<?php
namespace App\Common\Request;

use PhalApi\Exception\BadRequestException;

class Version {

    public static function formatVersion($value, $rule) {
        if (count(explode('.', $value)) < 3) {
            throw new BadRequestException('版本号格式错误');
        }
        return $value;
    }
}
```

回调函数的签名为：```function format($value, $rule, $params)```，第一个为参数原始值，第二个为所配置的规则，第三个可选参数为配置规则中的params选项。最后应返回转换后的参数值。  

## request请求和原始参数

PhalApi对接口请求封装了\PhalApi\Request类，注册在\PhalApi\DI()->request，更多请求相关的信息如下。

### 获取头部信息

获取特定头部信息：
```php
$userAgent = \PhalApi\DI()->request->getHeader('User-Agent');

// 或
$userAgent = \PhalApi\DI()->request->getHeader('USER_AGE');
```

### 获取参数

获取全部参数：
```php
$params = \PhalApi\DI()->request->getAll();
```

获取指定参数，没有时返回NULL，数据来源于$_GET、$_POST、$_REQUEST或getData()函数指定的数据源。
```php
$username = \PhalApi\DI()->request->get('username');
```

获取指定参数，没有时返回默认值：
```php
$username = \PhalApi\DI()->request->get('username', 'PhalApi');
```

### 获取请求的接口信息

获取当前的接口服务名称：
```php
$service = \PhalApi\DI()->request->getService();

```
获取当前的接口服务命名空间：
```php
$namespace = \PhalApi\DI()->request->getNamespace();
```

获取当前接口服务名称中的接口类名
```php
$className = \PhalApi\DI()->request->getServiceApi();
```

获取接口服务名称中的接口方法名：
```php
$actionName = \PhalApi\DI()->request->getServiceAction();
```


## 扩展：定制接口参数来源、解密和预处理

把我们的API接口服务想象成一个函数，那么请求的参数就是我们的参数列表；而接口响应的数据则对应函数返回的结果。  
  
对于请求，正如前面所看到的，我们可以使用$_GET，也可以使用$_POST，也可以两者都使用，还可以在测试时自己指定模拟的请求数据包。  
  
或者，在实际项目开发中，我们还需要根据自身的需求，跟我们自己的客户端作一些约定。如通常地，我们会要求客户端 **service参数使用GET方式** ，以便服务器返回500时定位接口服务位置。对此，简单的我们把$_POST['service']去掉即可，如在入口文件前面添加：

```php
unset($_POST['service']);   //只接收GET方式的service参数
```

更高级的功能将介绍如下。
  
### (1) 在di.php中指定数据来源

很多时候，不同的项目对数据来源和接收方式有不同的需求。如简单地，当需要强制统一使用$_POST参数，我们可以把在./config/di.php文件中对request请求服务进行调整。  
```php
// 注册新的请求服务，限制只能POST传递全部参数
$di->request = new \PhalApi\Request($_POST); // 只允许POST参数
```

特别注意，若限制全部参数使用POST方式后，在使用postman或者其他工具进行接口测试和请求时，s参数也需要通过POST方式传递。

![](http://cdn7.okayapi.com/yesyesapi_20200109234940_1572b237d8c049c809dd187554e97a7d.jpeg)

又如，对于curl请求，正确和错误的请求示例如下：  
```bash
# 正确例子，全部参数使用POST
$ curl "http://demo.phalapi.net/" -X POST -d 's=App.Examples_Rule.JsonArray&datas={"name":"PhalApi 2.x"}'
{"ret":200,"data":{"datas":{"name":"PhalApi 2.x"}},"msg":""}

# 错误例子，s参数使用GET，接口框架无法识别，最终执行默认接口服务App.Site.Index
$ curl "http://demo.phalapi.net/?s=App.Examples_Rule.JsonArray" -X POST  -d 'datas={"name":"PhalApi 2.x"}'
{"ret":200,"data":{"title":"Hello PhalApi","version":"2.5.0","time":1578585816},"msg":""}
```

> 温馨提示：当全部参数限制只允许POST方式后，连同s参数（即service接口服务参数）也需要一并通过POST方式传递，否则接口框架会因为未指定接口服务而执行默认的接口服务：```App.Site.Index```。  

  
对于复杂的情况，如需要使用post_raw数据，则可以继承[PhalApi\Request](https://github.com/phalapi/kernal/blob/master/src/Request.php)实现相应的数据源解析。如创建./src/app/Common/MyRequest.php文件。

```php
<?php
namesapce App\Common;
use PhalApi\Request;

class MyRequest extends Request {
    public function __construct($data = NULL) {
        parent::__construct($data);

        // json处理
        $this->data = json_decode(file_get_contents('php://input'), TRUE);    

        // 普通xml处理
        $this->data = simplexml_load_string (
            file_get_contents('php://input'),
            'SimpleXMLElement',
            LIBXML_NOCDATA
        );
        $this->data = json_decode(json_encode($this->data), TRUE);
    }  
}
```
然后在子类实现对各类参数的数据源的准备。可以说，```PhalApi\Request::__construct()```构造函数用于初始化各类辅助侯选的数据源，而```PhalApi\Request::getData()```则用于生成主要默认的数据源。  
  
### (2) 在单元测试时指定数据来源
在进行单元测试时，我们需要模拟接口的请求动作，也需要提供接口参数。这时的参数的指定更为灵活。可通过以下代码来实现，即：  
```php
//数据源
$data = array(...);

\PhalApi\DI()->request = new \PhalApi\Request($data);
```
  
或者使用PhalApi封装的测试类来快速模拟调用接口：  
```
    public function testIndex()
    {
        //Step 1. 构建请求URL
        $url = 'service=App.Site.Index&username=dogstar';

        //Step 2. 执行请求  
        $rs = TestRunner::go($url);

        //Step 3. 验证
        $this->assertNotEmpty($rs);
        $this->assertArrayHasKey('title', $rs);
    }
```
  
### (3) 接口数据的加密传送
有时，出于安全性的考虑，项目需要对请求的接口参数进行对称加密传送。这时可以通过重载PhalApi\Request::::genData()来轻松实现。 

假设，我们现在需要把全部的参数base64编码序列化后通过$_POST['data']来传递，则相应的解析代码如下。  
  
第一步，先定义自己的扩展请求类，在里面完成对称解析的动作：  
```php
<?php

namesapce App\Common;
use PhalApi\Request;

class MyRequest extends Request {
    public function genData($data) {
        if (!isset($data) || !is_array($data)) {
            $data = $_POST; //改成只接收POST
        }

        return isset($data['data']) ? base64_decode($data['data']) : array();
    }
}
```
  
第二步，在./config/di.php入口文件中重新注册请求类（即添加以下代码）：  
```php
// 注册新的请求服务
$di->request = new \App\Common\MyRequest(); // 内含参数解密的实现

// JSON中文输出
// $di->response = new \PhalApi\Response\JsonResponse(JSON_UNESCAPED_UNICODE);
```

然后，就可以轻松实现了接口参数的对称加密传送。  
至此，你也许已经发现：指定数据源和对称加密是可以结合来使用的。  
  
### (4) 接口参数级别的数据来源
除了可以指定全局的接口数据源外，还可以进行更细致的配置，即为某个接口参数指定使用$GET、$_POST、$_COOKIE、$_SERVER、$_REQUEST或头部等其他数据源。  
  
其使用方式是在配置接口参数规则时，使用source配置来指定当前参数的数据源，如指定用户在登录时，用户名使用$_GET、密码使用$_POST。  
```php
public function getRules() {
    return array(
        'login' => array(
            'username' => array('name' => 'username', 'source' => 'get'),
            'password' => array('name' => 'password', 'source' => 'post'),
        ),  
    );
}
```

此部分前面已有说明，不再赘述。


## 扩展：定制你的参数规则

当PhalApi提供的参数规则不能满足接口参数的规则验证时，除了使用callable类型进行扩展外，还可以扩展[PhalApi\Request\Formatter](https://github.com/phalapi/kernal/blob/master/src/Request/Formatter.php)接口来定制项目需要的类型。  
  
一如既往，分两步：  
 + 1、扩展实现PhalApi\Request\Formatter接口
 + 2、在DI注册你的参数规则新类型
  
下面以大家所熟悉的邮件类型为例，说明扩展的步骤。  
  
首先，我们需要一个实现了邮件类型验证的功能类，创建./src/app/Common/EmailFormatter，放置代码：  
```php
<?php
namespace App\Common;

use PhalApi\Request\Formatter;
use PhalApi\Exception\BadRequestException;

class EmailFormatter implements Formatter {

    public function parse($value, $rule) {
        if (!preg_match('/^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/', $value)) {
            throw new BadRequestException('邮箱地址格式错误');
        }

        return $value;
    }
}  
```
  
然后，在./config/di.php文件中追加注册：  
```php
$di->_formatterEmail = new App\Common\EmailFormatter();
```
  
> 温馨提示：在DI中手动注册服务时，名称的格式为： _formatter + 参数类型（首字母大写）。
```
   
系统已自动注册的格式化服务有：  

 + _formatterArray 数组格式化服务
 + _formatterBoolean 布尔值格式化服务
 + _formatterCallable 回调格式化服务
 + _formatterDate 日期格式化服务
 + _formatterEnum 枚举格式化服务
 + _formatterFile 上传文件格式化服务
 + _formatterFloat 浮点数格式化服务
 + _formatterInt 整数格式化服务
 + _formatterString 字符串格式化服务
 

至此，便可使用自己定制的类型规则了，  
```php
array('name' => 'user_email', 'type' => 'email')
```
