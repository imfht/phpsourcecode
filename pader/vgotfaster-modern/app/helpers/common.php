<?php

/**
 * 简单的变量调试
 *
 * @param mixed $var,$var2,$var3....
 * @return void
 */
function printr() {
	$args = func_get_args();

	if(is_array($args) and !empty($args)) {
		foreach($args as $var) {
			echo '<pre>';
			if(is_bool($var)) {
				echo $var ? '<i>TRUE</i>' : '<i>FALSE</i>';
			} else {
				(is_array($var) or is_object($var)) ? print_r($var) : print($var);
			}
			echo "</pre>\r\n";
		}
	}
}

/*
	获取可以直接访问的控制器
	此函数必须在控制器已经执行过 parent::Controller(); 之后调用
*/
function getControllerActions() {
	$VF =& getInstance();
	$methods = get_class_methods($VF);
	return array_diff($methods,array('__construct','Controller','_getInstance'));
}

//得到当前控制器的动作访问链接
function actionsNavLinks($controllerName) {
		$myMethods = getControllerActions();

		$links = array();
		foreach($myMethods as $row) {
			$links[] = anchor($controllerName.'/'.$row,$row);
		}

		$style = <<<EOD
<style type="text/css">
#actionsNavLinks {background-color:#C8CBFF; border:1px solid #B7ACFF; border-top-color:#8A8CF7; padding:0 0 5px 0; color:#7A7EDB; font-size:12px; font-family:Verdana;}
#actionsNavLinks .l1 {border-top:1px solid #9EA2FF; border-bottom:1px solid #ABB3FF;}
#actionsNavLinks .l2 {border-top:1px solid #B4BBFF; border-bottom:1px solid #BEC2FF;}
#actionsNavLinks span {padding:0 6px 0 6px;}
#actionsNavLinks .l3 {border-top:1px solid #C4C8FF;}
#actionsNavLinks a {color:#3A5877; text-decoration:none; padding:2px;}
#actionsNavLinks a:hover {background-color:#E8E9FE;}
</style>
EOD;
		return $style.'<div id="actionsNavLinks"><div class="l1"></div><div class="l2"></div><div class="l3"></div><span>'.join(' | ',$links).'</span></div>';
}
