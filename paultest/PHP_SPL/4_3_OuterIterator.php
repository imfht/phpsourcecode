<?php
/**
 * OuterIterator接口：可以对迭代器进行一定的处理后返回
 * IteratorIterator类是OuterIterator接口的实现，扩展的时候可以直接继承IteratorIterator类。
 * 
 * 利用OutIterator接口对ArrayIterator迭代器的current和key方法进行内部处理（重写）
 */

$array = ["Value1", "Value2", "Value3", "Value4"];
$ArrayIt = new ArrayIterator($array);

// 正常的遍历
foreach ($ArrayIt as $key => $value) {
    echo $key . "：" . $value . PHP_EOL;
}
/*
0：Value1
1：Value2
2：Value3
3：Value4
 */
echo PHP_EOL;

class OutIterator extends IteratorIterator
{
    public function current()
    {
        return parent::current() . "_tail";
    }

    public function key()
    {
        return "pre_" . parent::key();
    }
}

// 对ArrayIterator迭代器的current和key方法进行内部处理（重写）
$outerObj = new OutIterator($ArrayIt);
foreach ($outerObj as $key => $value) {
    echo $key . "：" . $value . PHP_EOL;
}
/*
pre_0：Value1_tail
pre_1：Value2_tail
pre_2：Value3_tail
pre_3：Value4_tail
 */