<?php
/**
 * 堆栈的简单操作
 * push：压入堆栈（存入）
 * pop：退出堆栈（取出）
 *
 * 注意：堆栈里面的方法有一些和双向链表的相反，比如说next和prev，rewind了之后的第一个节点是最后一个节点。还有堆栈中的第一个节点是最后一个节点
 */

// 实例化
$stack = new SplStack();

// 添加节点数据
$stack->push('a');
$stack->push('b');
$stack->push('c');

echo "输出堆栈：" . PHP_EOL;
print_r($stack);
/*
    [0] => a
    [1] => b
    [2] => c
 */
echo PHP_EOL;

echo "第一个节点的值为：" . $stack->bottom() . PHP_EOL;
echo "最后一个节点的值为：" . $stack->top() . PHP_EOL;

// 设置最后一个节点。注意：和双向链表相反，这里的0代表的是最后一个节点而不是第一个节点
$stack->offsetSet(0, 'AA');
echo PHP_EOL . "输出堆栈：" . PHP_EOL;
print_r($stack);
/*
    [0] => a
    [1] => b
    [2] => AA
 */
// 注意：和双向链表相反，这里的rewind之后当前节点是最后一个节点
$stack->rewind();
echo "设置了起始节点后，当前节点为：" . $stack->current() . PHP_EOL;

// 注意：next和prev也是和双向链表的相反
$stack->next();
echo "下一个节点后，当前节点为：" . $stack->current() . PHP_EOL;

// 遍历堆栈
echo "遍历的节点为：" . PHP_EOL;
$stack->rewind();
while ($stack->valid()) {
    echo $stack->key() . "=>" . $stack->current() . PHP_EOL;
    $stack->next();
}

// 删除并返回堆栈最后一个节点
$pop = $stack->pop();
echo "删除的最后一个节点为：" . $pop . PHP_EOL;

echo PHP_EOL . "输出堆栈：" . PHP_EOL;
print_r($stack);
/*
    [0] => a
    [1] => b
 */