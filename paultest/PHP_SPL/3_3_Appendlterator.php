<?php
/**
 * AppendIterator迭代器：陆续遍历几个迭代器
 *
 * AppendIterator迭代器可以通过append方法把多个ArrayIterator迭代器对象放到一起来遍历
 */

$aa = ['a', 'b', 'c'];
$bb = ['d', 'e', 'f'];

// 实例化ArrayIterator对象
$array_a = new ArrayIterator($aa);
$array_b = new ArrayIterator($bb);

// 实例化AppendIterator对象
$it = new AppendIterator();

// 通过append方法把多个迭代器对象添加到AppendIterator对象中
$it->append($array_a);
$it->append($array_b);

foreach ($it as $key => $value) {
    echo $key . " : " . $value . PHP_EOL;
}
/*
0 : a
1 : b
2 : c
0 : d
1 : e
2 : f
 */