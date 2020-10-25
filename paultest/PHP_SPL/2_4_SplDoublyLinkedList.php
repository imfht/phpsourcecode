<?php
/**
 * 双向链表的简单操作(SplDoublyLinkedList)

    当前节点操作：
        rewind（使当前节点指向头部）
        current（获得当前节点）
        next（使当前节点到下一个节点）
        prev（使当前节点到上一个节点）
    增加节点操作：
        push（尾部添加一个节点）
        unshift（头部添加一个节点）
    删除节点操作
        pop（删除尾部的第一个节点）
        shift（删除头部的第一个节点）
    定位操作：
        bottom（获取头部的元素）
        top（获取底部的元素）
    特定节点操作：
        offsetExists（判断特定节点是否存在）
        offsetGet（获得某个节点的数据）
        offsetSet（设置某个节点的数据）
        offsetUnset（释放某个节点的数据）
 *
 * 注意：
 * 如果没有设置起始节点的话，当前节点是无效的，即输出current和valid都是null
 * bottom和top只是获取首尾的数据而已，并不会把当前指针定位到首尾去
 * 通过next和prev来移动当前指针，如果移动到的节点不存在的话，当前节点是无效的，此时再通过prev或者是next返回上一个位置的话也同样是输出当前节点是无效的
 * 如果通过pop或者是shift来删除节点，刚好当前节点也是在被删除的节点的时候，那么当前节点是无效的
输出当前节点的时候，最好是先判断一下当前节点是否有效
 */

// 实例化
$SplDoubly_obj = new SplDoublyLinkedList();

// 链表的顶部/尾部（Top）添加节点数据
$SplDoubly_obj->push(1);
$SplDoubly_obj->push(2);
$SplDoubly_obj->push(3);

// 底部/头部（Bottom）添加节点数据
$SplDoubly_obj->unshift(10);

echo "输出双向链表：" . PHP_EOL;
var_dump($SplDoubly_obj);
/*
    [0] => 10
    [1] => 1
    [2] => 2
    [3] => 3
 */
echo PHP_EOL;

// 判断当前节点是否有效（由于未设置起始节点，所以输出无效）
if ($SplDoubly_obj->valid()) {
    echo "当前节点有效" . PHP_EOL;
} else {
    echo "当前节点无效" . PHP_EOL;
}

// 获取节点指针指向的节点（当前节点），由于还没有设置起始节点，所以这里输出NULL
echo "当前节点为：" . PHP_EOL;
var_dump($SplDoubly_obj->current());

// rewind操作用于把节点指针指向头部Bottom所在的节点
$SplDoubly_obj->rewind();
echo "设置了起始节点后，当前节点为：" . $SplDoubly_obj->current() . PHP_EOL;

echo "第一个节点的值为：" . $SplDoubly_obj->bottom() . PHP_EOL;  // 10
echo "最后一个节点的值为：" . $SplDoubly_obj->top() . PHP_EOL;  // 3

// bottom和top只是获取首尾的值而已，并不会定位到该节点
echo "当前节点为：" . $SplDoubly_obj->current() . PHP_EOL;  // 10

// 使当前节点指向下一个节点
$SplDoubly_obj->next();
$SplDoubly_obj->next();
echo "指向下一个下一个节点后，当前节点为：" . $SplDoubly_obj->current() . PHP_EOL;  // 2

// 判断当前节点是否有效
if ($SplDoubly_obj->valid()) {
    echo "当前节点有效" . PHP_EOL;
} else {
    echo "当前节点无效" . PHP_EOL;
}

// 使当前节点指向下一个节点
$SplDoubly_obj->next();
$SplDoubly_obj->next();
echo "指向下一个下一个节点后，当前节点为：" . PHP_EOL;
// 由于下一个节点没有数据，所以这里输出NULL
var_dump($SplDoubly_obj->current());

// 判断当前节点是否有效（建议用valid来判断，如果当前节点的数据为0或者是false或者是null的话会被判断为无效）
if ($SplDoubly_obj->current()) {
    echo "当前节点有效" . PHP_EOL;
} else {
    echo "当前节点无效" . PHP_EOL;
}

// 使当前节点指向上一个节点
$SplDoubly_obj->prev();
$SplDoubly_obj->prev();
echo "指向上一个上一个节点后，当前节点为：" . PHP_EOL;
// 由于当前节点已经是NULL，再指向上一个节点，即使上一个节点是有数据的，也同样显示NULL
var_dump($SplDoubly_obj->current());
echo PHP_EOL;

// 删除并返回最后的节点
$SplDoubly_obj->rewind();
echo "重新指向第一个节点后，当前节点为：" . $SplDoubly_obj->current() . PHP_EOL;  // 10

$SplDoubly_obj->next();
$SplDoubly_obj->next();
$SplDoubly_obj->next();
echo "指向下一个下一个下一个节点后，当前节点为：" . $SplDoubly_obj->current() . PHP_EOL;  // 3
$pop = $SplDoubly_obj->pop();
echo "删除的最后一个节点为：" . $pop . PHP_EOL;  // 3

echo "输出双向链表：" . PHP_EOL;
var_dump($SplDoubly_obj);
/*
    [0] => 1
    [1] => 2
 */

echo "当前节点为：" . PHP_EOL;
// 如果最后一个节点被删除了然后恰好当前节点正好指在最后一个节点，current会输出null
var_dump($SplDoubly_obj->current());

// 删除并返回第一个节点
$first = $SplDoubly_obj->shift();
echo "删除的第一个节点为：" . $first . PHP_EOL;  // 10
var_dump($SplDoubly_obj);

// 设置第一个节点的值
$SplDoubly_obj->offsetSet(0, 'AA');
echo "输出双向链表：" . PHP_EOL;
var_dump($SplDoubly_obj);
/*
    [0] => AA
    [1] => 2
 */