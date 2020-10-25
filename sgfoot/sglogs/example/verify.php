<?php
/**
 * User: 时光日志<root@sgfoot.com>
 * Date: 2018/7/5
 * Time: 11:45
 */
require('../lib/SgLogs.php');
define('SGLOGS_PATH', __DIR__ . '/../logs/');//结尾一定要加/

define('SGLOGS_USER', 'admin');//定义帐号
define('SGLOGS_PASS', 'admin');//定义密码

SgLogs::write(1,1, 'all');//清理掉之前的文件

$data = '自定义登陆帐号和密码';
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
define('SGLOGS_USER', 'admin');//定义帐号
define('SGLOGS_PASS', 'admin');//定义密码

SgLogs::write(1,1, 'all');//清理掉之前的文件

\$data = '自定义登陆帐号和密码';
SgLogs::write(\$data, \$data);
</pre>
<a href="../logs/">查看日志列表</a>
</div>
html;
    echo $html;

}