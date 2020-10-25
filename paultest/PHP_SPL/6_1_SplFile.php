<?php
/**
 * 文件处理类库
 *
 * SplFileInfo：用于获得文件的基本信息，比如修改时间、大小、目录等信息
 * SplFileObject：用于操作文件的内容，比如读取、写入
 */

date_default_timezone_set('PRC');

$url = "5_1_Class/Test.php";
$file = new SplFileInfo($url);

echo "文件创建时间：" . date("Y-m-d H:i:s", $file->getCTime()) . PHP_EOL;
echo "文件修改时间：" . date("Y-m-d H:i:s", $file->getMTime()) . PHP_EOL;
echo "文件大小：" . $file->getSize() / 1000 . " KB" . PHP_EOL;

// 读取文件的内容
$fileObj = $file->openFile("r");
while ($fileObj->valid()) {
    // fgets函数用于获取文件里面的一行数据
    echo $fileObj->fgets();
}

// 销毁变量
$fileObj = null;
$file = null;

/*
运行：
文件创建时间：2018-01-11 16:11:05
文件修改时间：2018-01-11 16:11:17
文件大小：0.131 KB
<?php

class Test
{
    public function __construct()
    {
        echo "加载Test.php的Test，这是初始化";
    }
}
*/