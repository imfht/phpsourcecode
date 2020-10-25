<?php
/**
* @package phpBB-WAP
* @copyright (c) phpBB Group
* @Оптимизация под WAP: Гутник Игорь ( чел ).
* @简体中文：中文phpBB-WAP团队
* @license http://opensource.org/licenses/gpl-license.php
**/

/**
* 这是一款自由软件, 您可以在 Free Software Foundation 发布的
* GNU General Public License 的条款下重新发布或修改; 您可以
* 选择目前 version 2 这个版本（亦可以选择任何更新的版本，由
* 你喜欢）作为新的牌照.
**/

/**
* 	phpBB-WAP Session
*	作者: Crazy
*/

/*
* 	开始计算运行时间
*	@返回 浮点数 开始运行时间
*/
function start_runtime()
{
	list($usec, $sec) = explode(' ', microtime());
	return ((float)$usec + (float)$sec);

}

/*
*	取出运行时间
*	@参数 浮点数 $starttime 开始运行时间
*	@参数 整数 $round 保留的范围，默认为保留三位，最大不能超过三位
*	@返回 浮点数
*/
function spent_runtime($starttime, $round = 3)
{
	list($usec, $sec) = explode(' ', microtime());
	$stoptime = ((float)$usec + (float)$sec);
	return round(($stoptime - $starttime), $round);
}
?>