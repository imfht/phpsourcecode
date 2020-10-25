# 什么是CIPlus

CIPlus是对CI框架的无损补充，在CI框架的基础上增加了更多本地化的类库和全局工具库。
可以在CI修复性升级后直接将原版框架中的system部分进行替换。

## Restful_Controller ：**RESTFul API 控制器**

帮助快速构造安全可靠的RESTFul API。

1、标准API包含code、message、data三个基本要素（详见开发规范）

通过setCode(int),setMessage(string),setData(array)三个方法为接口的三个要素进行赋值。

其中SetCode方法赋值后会在多语言文件message_lang中查询是否存在对应的message，若存在则自动赋值。

respond(int,string,array)方法将输出标准API数据。其中包含任意个可选参数，可以在调用respond方法的同时为API三要素进行赋值，不同的参数类型对应setCode(int),setMessage(string),setData(array)方法。

**示例1-1：**
```
#任意API控制器实现方法内

#方式一
$this->setCode(20000);
$this->setMessage('操作成功');
$this->setData(array('name'=>'lichao'));
$this->respond();

#方式二
$this->respond(20000,'操作成功',array('name'=>'jdzchao'));

#输出
{
    "code": 20000,
    "message": "操作成功",
    "data": {
            "name": "jdzchao"
        }
}
```

2、API的输出格式由全局HTTP参数_format控制(可配置)

随API参数传递_format数据可以修改API输出的数据格式，默认为JSON，同时可以修改为XML，CSV等等。

**示例1-2：**
```
#假设 示例1-1 的访问路径为 http://demo.ciplus.com/api/demo
#以GET方法为例，添加访问参数：
#http://demo.ciplus.com/api/demo?_format=xml

#输出
<xml>
    <code>20000</code>
    <message>操作成功</message>
    <data>
        <name>lichao</name>
    </data>
</xml>
```

3、增加配置参数strict

strict为true时，所有除respond输出的方法外的echo，print_f等都将被过滤，严格输出API数据。

4、接口参数接收方法request(array,array,string)

根据API的特性进行封装，可扩展验证API参数。

该方法第一个参数表示接口中的必填参数，可以对传入的参数空值进行报错。

该方法第二个参数表示接口中的选填参数，参数可以为空。所有选填参数若值为空则不生成键值。

该方法第三个参数表示数据提交方法，默认为兼容POST/GET方法，若设置POST或GET则强制使用对应方法获取参数。

request方法获取的参数都会对应查找对应的参数验证方法，若在子类中实现该方法则进行对应的参数验证操作。

参数验证方法默认前缀为verify_，后面接参数key。

**示例1-3：**
```
// API方法
public function Demo(){
    $this->request(array('id'),array('page'),'get');
    ...数据操作
    $this->respond();
}

// 验证当前接口类中接收到的id参数

public verify_id($value){
    ...验证value
    // 非法则：
    $this->respond(40000,'error');
}

// 验证分页参数，当前页：page
// 参数value使用取地址符“&”是为了设置默认值
public verify_page(&$value){
    if ($value <= 0)
        $value = 1;
    return $value;
}
```

5、返回API参数结果集方法requestData($array)

返回所有必填、选填参数的结果集数组，包含键和值
设置$array数组可以只取接口参数的交集

**示例1-4：**
```
#访问接口
/api/demo?a=1&b=2&name=jdzchao&age=18

// 接口实现方法
public function Index() {
    $this->request(array('a', 'b'), array('name', 'age'));
    $arr = $this->requestData();
    print_r($arr);
}

#输出：
Array ( [a] => 1 [b] => 2 [name] => jdzchao [age] => 18 )

```

6、过滤数据方法filterData(array,bool)

返回过滤后的数据结果集，方便构造数据库操作。

第一个参数为过滤参数名单数组，填写过滤的参数key

第二个参数为过滤方法，true为滤取交集，false为滤取补集

**示例1-5：**
```
#访问接口
/api/demo?a=1&b=2&name=jdzchao&age=18

// 接口实现方法 1
public function Index() {
    $this->request(array('a', 'b'), array('name', 'age'));
    $arr = $this->requestData();
    $arr1 = $this->filterData(array('a', 'name'), true);
    print_r($arr1);
}

#输出：
Array ( [a] => 1 [name] => jdzchao )


// 接口实现方法 2
public function Index() {
    $this->request(array('a', 'b'), array('name', 'age'));
    $arr = $this->requestData();
    $arr1 = $this->filterData(array('a', 'name'), false);
    print_r($arr1);
}
#输出：
Array ( [b] => 2 [age] => 10 )
```


