<?php
/**
 * MultipleIterator迭代器用于把多个Iterator里面的数据组合成为一个整体来访问
 * MultipleIterator迭代器可以按数字或者是字符串作为数组的key
 * 这里是按数字作为key
 */

// 实例化ArrayIterator对象
$idIter = new ArrayIterator(['01', '02', '03']);
$nameIter = new ArrayIterator(['张三', '李四', '王五']);
$ageIter = new ArrayIterator(['22', '34', '35']);

// 设置容器（以key为关联）
$mit = new MultipleIterator(MultipleIterator::MIT_KEYS_ASSOC);

// 将迭代器加入容器中
$mit->attachIterator($idIter, "0");
$mit->attachIterator($nameIter, "1");
$mit->attachIterator($ageIter, "2");

// 遍历容器
foreach ($mit as $value) {
    print_r($value);
}
/*
Array
(
    [0] => 01
    [1] => 张三
    [2] => 22
)
Array
(
    [0] => 02
    [1] => 李四
    [2] => 34
)
Array
(
    [0] => 03
    [1] => 王五
    [2] => 35
)
 */