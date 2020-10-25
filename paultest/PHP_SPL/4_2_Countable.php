<?php
/**
 * Countable接口
 * count()方法是对象继承Countable后必须实现的方法，即某个类继承Countable，类中必须定义count方法。这样的话，直接使用count方法时会调用对象自身的count方法
 */

$array = [
    ['name' => 'paul1', 'id' => 5],
    ['name' => 'paul2', 'id' => 6],
    ['name' => 'paul3', 'id' => 7],
];

echo count($array) . PHP_EOL;  // 3
echo count($array[1]) . PHP_EOL;  // 2

class CountMe1
{
    protected $_myCount = 3;

    public function count()
    {
        return $this->_myCount;
    }
}

// 类（对象）没有继承Countable接口的话，不会调用对象的count方法
$obj1 = new CountMe1();
echo count($obj1) . PHP_EOL;  // 1

class CountMe2 implements Countable
{
    protected $_myCount = 6;

    public function count()
    {
        return $this->_myCount;
    }
}

// 类（对象）继承Countable接口的话，调用count方法自动会调用对象的count方法，相当于$obj2->count()
$obj2 = new CountMe2();
echo count($obj2) . PHP_EOL;    // 等同于echo $obj2->count();  6