<?php
/**
 * 队列的简单操作
 *
 * 队列正好和堆栈相反，最先进入队列的元素会最先走出队列
 *
 * 操作：
 * enqueue：进入队列
 * dequeue：退出队列
 *
 * 注意：队列和堆栈的操作方法基本上是类似的，不过有一些方法是和堆栈相反的，比如说offsetSet里面的0,表示的是第一个节点，而不像堆栈表示的是最后一个节点。还有rewind、next、prev操作
 */

// 实例化
$queue = new SplQueue();

// 添加节点数据
$queue->enqueue('a');
$queue->enqueue('b');
$queue->enqueue('c');

echo "输出队列：" . PHP_EOL;
print_r($queue);
/*
    [0] => a
    [1] => b
    [2] => c
 */

echo PHP_EOL . "第一个节点的值为：" . $queue->bottom() . PHP_EOL;  // a
echo "最后一个节点的值为：" . $queue->top() . PHP_EOL;  // c

// 设置第一个节点。注意：和双向链表一致，这里的0代表的是第一个节点，但是和堆栈是相反的，堆栈的0表示的是最后一个节点
$queue->offsetSet(0, 'AA');
echo "输出队列：" . PHP_EOL;
print_r($queue);
/*
    [0] => AA
    [1] => b
    [2] => c
 */

// 注意：和双向链表一致，这里的rewind之后当前节点是第一个节点，和堆栈是相反的
$queue->rewind();
echo PHP_EOL . "设置了起始节点后，当前节点为：" . $queue->current() . PHP_EOL;  // AA

// 注意：next和prev也是和双向链表的一致，和堆栈的相反
$queue->next();
echo "下一个节点后，当前节点为：" . $queue->current() . PHP_EOL;  // b

// 遍历队列
echo "遍历的节点为：" . PHP_EOL;
$queue->rewind();
while ($queue->valid()) {
    echo $queue->key() . "=>" . $queue->current() . PHP_EOL;
    $queue->next();
}

// 删除并返回队列的第一个节点
$pop = $queue->dequeue();
echo "删除的第一个节点为：" . $pop . PHP_EOL;  // AA

echo "输出队列：" . PHP_EOL;
print_r($queue);
/*
    [0] => b
    [1] => c
 */
