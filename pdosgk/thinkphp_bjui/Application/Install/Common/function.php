<?php
/**
 * 及时显示提示信息
 * @param  string $msg 提示信息
 */
function show_msg($msg, $class = '')
{
	echo "<script type=\"text/javascript\">showmsg(\"{$msg}\", \"{$class}\")</script>";
	ob_flush();
	flush();


}
