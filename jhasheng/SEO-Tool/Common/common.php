<?php
header('Content-Type:text/html;charset=utf8');
function genTree($rows, $id='id', $pid='pid') {

	$items = array();
	foreach ($rows as $row)
	{
		$items[$row[$id]] = $row;	
	}

	foreach ($items as $item)
	{
		$items[$item[$pid]]['son'][$item[$id]] = &$items[$item[$id]];
	}
	return isset($items[0]['son']) ? $items[0]['son'] : array(); 
}

function getNode()
{
	$dir = LIB_PATH.'Action/';
	$new = array();
	if(is_dir($dir))
	{
		$files = glob($dir."*.class.php");
		foreach($files as $v)
		{
			if(is_dir($v))
			{
				continue;
			}
			else
			{
				$dis = explode(',',C('NOT_AUTH_MODULE'));
				$dis[] = 'Common';
				$module = basename($v,'Action.class.php');
				if(!in_array($module,$dis))
				{
					$new[$module] = getFunction($module);
				}
			}
				
		}
	}
	return $new;
}

function getFunction($module)
{
	if(empty($module)) return null;
	$action = A($module);
	$class = new ReflectionClass($module."Action");
	preg_match('/@name (.*)[\s]/',$class->getDocComment(),$matches);
	$comment_class = $matches[1] ? $matches[1] : '未知' ;
	$return['comment'] = $comment_class;
	$functions = $class->getMethods(ReflectionMethod::IS_PUBLIC);
	$_functions = array(
		'_initialize','__construct','getActionName','isAjax','display','show','fetch','buildHtml','assign','__set','get','__get','__isset','__call','error','success','ajaxReturn','redirect','__destruct','doRequest','theme','dispatchJump',''
	);
	foreach ($functions as $func)
	{
		if(!in_array($func->getName(), $_functions))
		{
			preg_match('/@name (.*)[\s]/',$func->getDocComment(),$matches);
			$comment = $matches[1] ? $matches[1] : '未知' ;
			$customer_functions[$func->getName()] = $comment;
			//echo $func->getDocComment();exit;
		}
	}
	$return['methods'] = $customer_functions;
	return $return;
}
