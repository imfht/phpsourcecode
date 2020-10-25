# validator

yeah, another params validate component for PHP applications.

嗯，又一个PHP应用参数验证组件

## 安装
* 在项目的composer.json文件中的require项中添加：
```
"furthestworld/validator": "~1.0"
```
并更新composer依赖：`composer update`

* 在需要使用Validator服务的地方添加：

```
require_once __ROOT__ . '/vendor/autoload.php';
use FurthestWorld\Validator\Src\Validator;
```

## 食用方法
```
//扩展验证规则实例
Validator::extend('extend_test', new TestExtendRules());

Validator::formatParams(
    $params,
    [
        'domain'    => ['format_rule' => 'strtoupper', 'default_value' => ''],
        'member_id' => ['format_rule' => 'formatExtendMemberId:domain']
    ]
);
Validator::validateParams(
    $params,
    [
        'domain'    => ['check_rule' => 'number#numberGt0|string#string:10,500'],
        'member_id' => ['check_rule' => 'extendEq:20#number'],
    ]
);

if (!Validator::pass()) {
    //验证未通过
    var_dump(Validator::getErrors());
} else {
    //验证通过
}
```

## 语法说明

### 参数格式化

* format_rule
规则： 格式化方法（PHP函数或自定义函数）：格式化参数（若为空则默认为当前字段的值）

* default_value
参数默认值设置

* force_value 
强制重置参数

### 参数验证

* check_rule

分隔符 `|` ：`或验证`（满足其中的至少一项验证）

分隔符 `#` ：`与验证`（满足其中所有的验证项）

分隔符 `:` ：方法和参数分隔符

分隔符  `,` ：多个参数分隔符

如上面的规则：`'number#numberGt0|string#string:10,500'` 解析成PHP代码逻辑相当于：
```
   if((number && numberGt0) || (string && string:10,500)){
       ...
   }
```

> 为了避免方法名和PHP关键字冲突，对应规则中的方法在解析成方法名时会自动加上前缀 `check`
> 比如上面的`number`、`numberGt0`、`string` 验证方法对应的方法分别为： `checkNumber`、`checkNumberGt0`、`checkString`

### 规则实例扩展

`format_rule` 和 `check_rule` 除了使用组件自带的格式化和验证方法，也支持自定义方法。
只需要把自定义的规则实例注册到组件的扩展规则中就可以了，如：

```
Validator::extend('extend_test', new TestExtendRules());
```

## enjoy~ :)

