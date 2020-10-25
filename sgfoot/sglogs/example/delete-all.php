<?php
/**
 * User: 时光日志<root@sgfoot.com>
 * Date: 2018/7/5
 * Time: 11:45
 */

require('../lib/SgLogs.php');
define('SGLOGS_PATH', __DIR__ . '/../logs/');//结尾一定要加/

$data = '删除所有日志文件';
SgLogs::write($data, $data, 'all');//第三个参数传all表示删除所有的谁的,包括产生的子文件
dump($data);

function dump($title)
{
    $html = <<<html
<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<link href="http://www.sgfoot.com/favicon.png" rel="shortcut icon"/>
<title>{$title}</title>
<div style="text-align:center">
<h2>执行完成,请到logs文件夹查看</h2>
<h3>源码展示</h3>
<pre>
\$data = '删除所有日志文件';
SgLogs::write(\$data, \$data, 'all');//第三个参数传true表示删除当前文件,其它文件不会删除
dump(\$data);
</pre>
<a href="../logs/">查看日志列表</a>
</div>
html;
    echo $html;

}