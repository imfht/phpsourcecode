<?php
/**
 * Filesystemlterator迭代器
 */

if (!ini_get('date.timezone')) {
    date_default_timezone_set('PRC');
}

$it = new FilesystemIterator(".");

foreach ($it as $value) {
    echo $value->isDir() ? "目录名：" : "文件名：";
    echo $value->getFileName();
    echo "   大小为：" . number_format($value->getSize() / 1000, 2) . " KB";
    echo "   创建时间为：" . date("Y-m-d H:i:s", $value->getMTime()) . PHP_EOL;
}
/*
目录名：.git   大小为：4.10 KB   创建时间为：2018-01-11 14:49:39
目录名：.idea   大小为：4.10 KB   创建时间为：2018-01-11 14:49:39
文件名：2_4_SplDoublyLinkedList.php   大小为：5.25 KB   创建时间为：2018-01-10 20:14:43
文件名：2_7_SplStack.php   大小为：1.75 KB   创建时间为：2018-01-10 21:43:06
文件名：2_8_SplQueue.php   大小为：2.00 KB   创建时间为：2018-01-11 11:52:45
文件名：3_2_Array.php   大小为：0.93 KB   创建时间为：2018-01-11 14:07:53
文件名：3_2_ArrayIterator.php   大小为：2.10 KB   创建时间为：2018-01-11 13:56:42
文件名：3_3_Appendlterator.php   大小为：0.67 KB   创建时间为：2018-01-11 14:23:54
文件名：3_4_MultipleIterator_1.php   大小为：0.95 KB   创建时间为：2018-01-11 14:39:39
文件名：3_4_MultipleIterator_2.php   大小为：0.98 KB   创建时间为：2018-01-11 14:40:45
文件名：3_5_Filesystemlterator.php   大小为：0.46 KB   创建时间为：2018-01-11 14:49:38
目录名：img   大小为：0.00 KB   创建时间为：2018-01-11 14:27:50
文件名：README.md   大小为：7.86 KB   创建时间为：2018-01-11 14:48:27
 */