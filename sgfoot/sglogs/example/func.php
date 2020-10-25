<?php
/**
 * User: 时光日志<root@sgfoot.com>
 * Date: 2018/7/5
 * Time: 11:45
 */

$data = '友好封装方法,全局使用';
sglogs($data, $data);
dump($data);
/**
 * 封装函数,全局调用
 * @param $data
 * @param $title
 * @param bool $flush
 */
function sglogs($data, $title, $flush = false)
{
    require_once('../lib/SgLogs.php');
    define('SGLOGS_PATH', __DIR__ . '/../logs/');//结尾一定要加/
    SgLogs::write($data, $title, $flush);
}

function dump($title)
{
    $html = <<<html
<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<link href="http://www.sgfoot.com/favicon.png" rel="shortcut icon"/>
<title>{$title}</title>
<div align="left">
<h2>执行完成,请到logs文件夹查看</h2>
<h3>源码展示</h3>
<pre>
\$data = '友好封装方法,全局使用';
sglogs(\$data, \$data);
dump(\$data);
/**
 * 封装函数,全局调用
 * @param \$data
 * @param \$title
 * @param bool \$flush
 */
function sglogs(\$data, \$title, \$flush = false)
{
    require_once('../lib/SgLogs.php');
    define('SGLOGS_PATH', __DIR__ . '/../logs/');//结尾一定要加/
    SgLogs::write(\$data, \$title, \$flush);
}
</pre>
<a href="../logs/">查看日志列表</a>
</div>
html;
    echo $html;

}