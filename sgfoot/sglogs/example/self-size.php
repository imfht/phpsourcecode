<?php
/**
 * User: 时光日志<root@sgfoot.com>
 * Date: 2018/7/5
 * Time: 11:45
 */
require('../lib/SgLogs.php');
define('SGLOGS_PATH', __DIR__ . '/../logs/');//结尾一定要加/

//定义日志文件大小,一个文件超过定义的最大值会自动递增子文件进行存储,以m为单位
define('SGLOGS_MAX', 3);

$data = '自定义文件大小';
SgLogs::write($data, $data);

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
//定义日志文件大小,一个文件超过定义的最大值会自动递增子文件进行存储,以m为单位
define('SGLOGS_MAX', 3);
\$data = '自定义文件大小';
SgLogs::write(\$data, \$data);
</pre>
<a href="../logs/">查看日志列表</a>
</div>
html;
    echo $html;

}