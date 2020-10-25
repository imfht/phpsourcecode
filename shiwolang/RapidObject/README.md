# RapidObject PHP基类组件

* PHP 多继承 Mixin
* PHP getter setter

##使用方式

###通过composer安装
```
$ composer require shiwolang/base
```
###多继承
```php
class Nihao1 extends Object
{
    public $nihao = "asdf";
    public function nihao()
    {
        echo "nihao1";
    }
}
class Nihao2 extends Object
{
    public function nihao()
    {
        echo "nihao2";
    }
}
class Nihao3 extends Object
{
}
class Nihao4 extends Object
{
    public $nihao = "asdf";

    protected static function extend()
    {
        return parent::extend(Nihao1::className(), Nihao2::className());
    }

    public static function hello()
    {

        echo "asdf";
    }
}
class Nihao5 extends Object
{
    protected static function extend()
    {
        return parent::extend(Nihao3::className());
    }
}
class Nihao6 extends Object
{
    protected static function extend()
    {
        $aa = parent::extend(Nihao4::className(), Nihao5::className());

        return $aa;
    }
}
```
###查询执行顺序
```php
var_dump(Nihao6::__mro__());
/**结果**
array(5) {
  [0] =>
  string(23) "test\base\object\Nihao4"
  [1] =>
  string(23) "test\base\object\Nihao1"
  [2] =>
  string(23) "test\base\object\Nihao2"
  [3] =>
  string(23) "test\base\object\Nihao5"
  [4] =>
  string(23) "test\base\object\Nihao3"
}
**/
```
###组件使用思路
    组件利用PHP的特性完成的类多继承的Mixin，给PHP开发提供更多的开发思路。