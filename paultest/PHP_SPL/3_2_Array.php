<?php
/**
 * PHP的数组简单操作
 */

$array = ['step one', 'step two', 'step three', 'step four'];

// 返回当前指针的值
echo current($array) . PHP_EOL; // "step one"

// 指向下一个指针并且返回指针的值
echo next($array) . PHP_EOL;  // "step two" 

// 指向上一个指针并且返回指针的值
echo prev($array) . PHP_EOL;  // "step one"

// 指向下一个指针
next($array);    // "step two" 

// 指向下一个指针
next($array);     // "step three"

// 返回当前指针的值
echo current($array) . PHP_EOL; // "step three"

// 将指针倒回到第一个并返回第一个的值
reset($array);

// 返回当前指针的值
echo current($array) . PHP_EOL; // "step one"

// 将指针倒回到最后一个并返回最后一个的值返回当前指针的值
echo end($array) . PHP_EOL;

// 返回当前指针的值
echo current($array) . PHP_EOL; // "step one"