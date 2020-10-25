<?php
/**
 * User: 时光日志<root@sgfoot.com>
 * Date: 2018/7/5
 * Time: 11:45
 */

require('../lib/SgLogs.php');
define('SGLOGS_PATH', __DIR__ . '/../logs/');//结尾一定要加/

//写一个简单的实例,请到logs文件夹查看
SgLogs::write(range('a', 'z'), 'a-z');
dump('简单实例');

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
//默认帐号: sglogs
//默认密码: sglogs
//写一个简单的实例,请到logs文件夹查看
SgLogs::write(range('a', 'z'), 'a-z');
</pre>
<a href="../logs/">查看日志列表</a>
</div>
<script>
alert('请记住页面提供的帐号和密码,用于访问日志');
</script>
html;
    echo $html;

}