<?php
// ModPHP 压缩包名称，如果设置，ModPHP 将从 ZIP 中加载内核
defined('MOD_ZIP') or define('MOD_ZIP', '');

require_once (MOD_ZIP ? 'zip://'.__DIR__.'/'.MOD_ZIP.'#' : '').'mod/common/init.php'; //引入初始化程序

/*
 * URL 请求使说明：
 * 可以通过 URL 携带参数访问 mod.php 文件直接访问模块类方法，通常在 AJAX 中使用。
 * 需要至少提供两个参数，obj 和 act，用来调用相应的对象(类)和操作(方法)，其他的参数将作为类方法的参数。
 * ModPHP 会自动收集向后台提交的数据，执行请求的操作并将结果返回给客户端。
 * 默认有三种 URL 格式可以提交请求，以获取 user_id = 1 的用户为例：
 * 	 1. mod.php?obj=user&act=get&user_id=1[&更多参数];
 * 	 2. mod.php?user::get|user_id:1[|更多参数]
 * 	 3. mod.php/user/get/user_id/1[/更多参数]
 */

/** 交互式控制台，兼容 shell 命令 */
if(__SCRIPT__ != 'mod.php' && !is_console()) goto end;
$ENCODING = get_cmd_encoding(); //命令行编码，仅 Windows
$TITLE = 'ModPHP Console'; //窗口标题
$PROMPT = '>>> '; //提示符
$STDOUT = $STDIN = null;
if(is_console()){
	fwrite(STDOUT, 'ModPHP '.MOD_VERSION.' started at '.date('D M d H:i:s Y').PHP_EOL);
	do_hooks('console.open'); //在控制台打开前执行挂钩函数
	if(error()){
		$ERROR = error();
		exit($ERROR['data'].PHP_EOL); //可以在挂钩函数中返回错误以终止程序
	}
	fwrite(STDOUT, $PROMPT);
}
while(true){
	if(PHP_OS == 'WINNT') exec("title ".__DIR__." - ".$TITLE); //设置 CMD 窗口标题
	error(null);
	if(!is_console() || $STDIN = fgets(STDIN)){
		if($STDIN){ //交互式控制台
			$STDIN = trim($STDIN);
			if($ENCODING && strcasecmp($ENCODING, 'UTF-8')) //转换编码
				$STDIN = iconv($ENCODING, 'UTF-8', $STDIN) ?: $STDIN;
			$argv = parse_cli_str('"'.$_SERVER['argv'][0].'" '.$STDIN);
			$argv = parse_cli_param($argv);
		}else{ //命令行直接调用
			$argv = parse_cli_param($_SERVER['argv']);
		}
		if(!$argv['param']){
			fwrite(STDOUT, $PROMPT);
			continue;
		}
		foreach($argv['param'] as $PARAM){
			ob_start();
			if(!strpos($PARAM['cmd'], '(') && (is_callable($PARAM['cmd']) || strpos($PARAM['cmd'], '::'))) {
				//将输入按 shell 命令来运行
				${'SHELL'.INIT_TIME} = true;
				foreach($PARAM['args'] as &$v){ //转换参数
					if($v === 'true') $v = true;
					elseif($v === 'false') $v = false;
					elseif($v === 'undefined' || $v === 'null') $v = null;
					elseif(is_numeric($v) && (int)$v < 2147483647) $v = (int)$v;
				};
				if(is_assoc($PARAM['args'])){ //关联数组参数
					print_r(call_user_func($PARAM['cmd'], $PARAM['args']));
				}elseif(is_array($PARAM['args'])){ //索引数组参数
					print_r(call_user_func_array($PARAM['cmd'], $PARAM['args']));
				}
			}elseif($STDIN !== null){ //变量或者其他
				eval($STDIN ? rtrim($STDIN, ';').';' : '');
			}else{ //命令行直接调用
				print_r(eval('return '.rtrim($PARAM['cmd'], ';').';'));
			}
			$STDOUT = trim(ob_get_clean(), PHP_EOL); //获取输出缓存
			if($STDOUT && $ENCODING && strcasecmp($ENCODING, 'UTF-8')) //转换编码
				$STDOUT = iconv('UTF-8', $ENCODING, $STDOUT) ?: $STDOUT;
			if($STDIN === null){ //命令行直接调用
				echo $STDOUT.PHP_EOL; //输出代命令行
			}else{ //交互式控制台
				if($STDIN && $STDOUT) fwrite(STDOUT, $STDOUT.PHP_EOL); //输出到交互式控制台
				if(!isset(${'SHELL'.INIT_TIME})) break;
				unset(${'SHELL'.INIT_TIME});
			}
		}
		if($STDIN === null) break;
		else fwrite(STDOUT, $PROMPT);
	}
}
end: