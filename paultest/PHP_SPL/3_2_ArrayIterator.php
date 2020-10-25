<?php
/**
 * 相比起foreach遍历数组，ArrayIterator在排序和取部分数组上面更有优势
 */

$fruits = [
    'apple'   => 'apple value',
    'orange'  => 'orange value',
    'grape'   => 'grape value',
    'plum'    => 'plum value'
];

// 使用传统的foreach遍历数组
echo "使用传统的foreach遍历数组:" . PHP_EOL;
foreach ($fruits as $key => $value) {
    echo $key . " : " . $value . PHP_EOL;
}

// 实例化ArrayIterator
$obj = new ArrayObject($fruits);
$it = $obj->getIterator();

// 使用ArrayIterator来遍历数组（foreach）
echo PHP_EOL . "使用ArrayIterator来遍历数组（foreach）:" . PHP_EOL;
foreach ($it as $key => $value) {
    echo $key . " : " . $value  . PHP_EOL;
}

// 使用ArrayIterator来遍历数组（while）
echo PHP_EOL . "使用ArrayIterator来遍历数组（while）:" . PHP_EOL;
$it->rewind();
while ($it->valid()) {
    echo $it->key() . " : " . $it->current() . PHP_EOL;
    $it->next();
}

// 跳过某些元素进行打印
echo PHP_EOL . "使用ArrayIterator来遍历数组（跳过某些元素）:" . PHP_EOL;
$it->rewind();
if ($it->valid()) {
    $it->seek(2);    // 设置指针的位置，为n时即跳过前面n-1的元素
    while ($it->valid()) {
        echo $it->key() . " : " . $it->current() . PHP_EOL;
        $it->next();
    }
}
/*
grape : grape value
plum : plum value
 */

// 对key进行字典序排序
echo PHP_EOL . "使用ArrayIterator来遍历数组（对key排序）:" . PHP_EOL;
$it->ksort();
$it->rewind();
while ($it->valid()) {
    echo $it->key() . " : " . $it->current() . PHP_EOL;
    $it->next();
}
/*
apple : apple value
grape : grape value
orange : orange value
plum : plum value
 */

// 对value进行字典序排序
echo PHP_EOL . "使用ArrayIterator来遍历数组（对value排序）:" . PHP_EOL;
$it->asort();
$it->rewind();
while ($it->valid()) {
    echo $it->key() . " : " . $it->current() . PHP_EOL;
    $it->next();
}
/*
apple : apple value
grape : grape value
orange : orange value
plum : plum value
 */