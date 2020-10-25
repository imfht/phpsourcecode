<?php

// 合并 abc/def/../ 为 abc/
function xn_realpath($path) {
	$path = str_replace('\\', '/', $path);
	$i = 0;
	while(strpos($path, '../') !== FALSE) {
		if($i++ > 10) break; // 最多 10 层，另外防止死循环，比如 ./abc/../
		$path = preg_replace('#\w+\/\.\./#', '', $path);
	}
	return $path;
}

function message($s) {
	global $conf;
	include './header.inc.php';	
	echo "<div class=\"bg1 border\" style=\"padding: 16px;\">$s</div>";
	include './footer.inc.php';
	exit;
}

function clear_tmp($pre = '', $tmppath = '') {
	$dh = opendir($tmppath);
	while(($file = readdir($dh)) !== false ) {
		if($file != "." && $file != ".." && $file != ".svn") {
			if(empty($pre) || substr($file, 0, strlen($pre)) == $pre) {
				is_file($tmppath."$file") && unlink($tmppath."$file");
			}
		}
	}
	closedir($dh);
}

function sql_mysql_to_sqlite($sql) {
	
}

function get_env(&$env, &$write) {
	$env['php_version']['name'] = 'PHP Version';
	$env['php_version']['must'] = TRUE;
	$env['php_version']['current'] = PHP_VERSION;
	$env['php_version']['need'] = '5.0';
	$env['php_version']['status'] = version_compare(PHP_VERSION , '5') > 0;

	$spl_autoload_register = function_exists('spl_autoload_register');
	$env['spl_autoload_register']['name'] = 'Auto Load(SPL)';
	$env['spl_autoload_register']['must'] = TRUE;
	$env['spl_autoload_register']['current'] = $spl_autoload_register ? '开启' : '<a href="http://www.php.net/spl">SPL未开启</a>';
	$env['spl_autoload_register']['need'] = '开启';
	$env['spl_autoload_register']['status'] = $spl_autoload_register;
	
	// 头像缩略需要，没有也可以。
	if(function_exists('gd_info')) {
		$gd_info = gd_info();
		preg_match('/\d(?:.\d)+/', $gd_info['GD Version'], $arr);
		$gd_version = $arr[0];
		$env['gd_version']['name'] = 'GD Version';
		$env['gd_version']['must'] = FALSE;
		$env['gd_version']['current'] = $gd_version;
		$env['gd_version']['need'] = '1.0';
		$env['gd_version']['status'] = version_compare($gd_version , '1') > 0 ? 1 : 2;
	} else {
		$env['gd_version']['name'] = 'GD Version';
		$env['gd_version']['must'] = FALSE;
		$env['gd_version']['current'] = 'None';
		$env['gd_version']['need'] = '1.0';
		$env['gd_version']['status'] = 2;
	}

	// 目录可写
	$upload_tmp_dir = ini_get('upload_tmp_dir');
	$upload_tmp_dir = $upload_tmp_dir ? $upload_tmp_dir : getenv('TEMP');
	$writedir = array(BBS_PATH.'conf/conf.php', BBS_PATH.'log', BBS_PATH.'tmp', BBS_PATH.'upload', BBS_PATH.'plugin');

	$write = array();
	foreach($writedir as &$dir) {
		//$dir = realpath($dir);
		$write[$dir] = misc::is_writable($dir);
	}
}

function str_line_replace($s, $startline, $endline, $replacearr) {
	// 从16行-33行，正则替换
	$sep = "\n";
	$s = str_replace("\r\n", $sep, $s);
	$arr = explode($sep, $s);
	$arr1 = array_slice($arr, 0, $startline - 1); // 此处: startline - 1 为长度
	$arr2 = array_slice($arr, $startline - 1, $endline - $startline + 1); // 此处: startline - 1 为偏移量
	$arr3 = array_slice($arr, $endline);
	
	foreach($arr2 as &$s) {
		foreach($replacearr as $k=>$v) { 
			$s = preg_replace('#\''.preg_quote($k).'\'\s*=\>\s*\'?.*?\'?,#ism', "'$k' => '$v',", $s);
		}
	}
	$s = implode($sep, $arr1).$sep.implode($sep, $arr2).$sep.implode($sep, $arr3);
	return $s;
}

function get_key_add($primarykey, $arr) {
	$s = '';
	foreach($primarykey as $col=>$v) {
		$s .= "-$col-".$arr[$col];
	}
	return $s;
}

// 生成 tmp 缓存, IN_SAE
function make_tmp($conf) {
	
	//$tmppath = IN_SAE ? FRAMEWORK_TMP_TMP_PATH.'tmp/' : FRAMEWORK_TMP_PATH;	// 这样比较保险，但是目前看来没有必要。
	$tmppath = FRAMEWORK_TMP_TMP_PATH;
	
	$runtimefile = $tmppath.'_runtime.php';
	if (!is_file($runtimefile)) {
		$content = '';
		$content .= php_strip_whitespace(FRAMEWORK_PATH.'core/core.class.php');
		$content .= php_strip_whitespace(FRAMEWORK_PATH.'core/misc.class.php');
		$content .= php_strip_whitespace(FRAMEWORK_PATH.'core/base_control.class.php');
		$content .= php_strip_whitespace(FRAMEWORK_PATH.'core/base_model.class.php');
		$content .= php_strip_whitespace(FRAMEWORK_PATH.'lib/log.class.php');
		$content .= php_strip_whitespace(FRAMEWORK_PATH.'lib/xn_exception.class.php');
		$content .= php_strip_whitespace(FRAMEWORK_PATH.'lib/encrypt.func.php');
		$content .= php_strip_whitespace(FRAMEWORK_PATH.'lib/template.class.php');
		$content .= php_strip_whitespace(FRAMEWORK_PATH.'db/db.interface.php');
		$content .= php_strip_whitespace(FRAMEWORK_PATH.'db/db_mysql.class.php');
		$content .= php_strip_whitespace(FRAMEWORK_PATH.'cache/cache.interface.php');
		$content .= php_strip_whitespace(FRAMEWORK_PATH.'cache/cache_memcache.class.php');
		file_put_contents($runtimefile, $content);
		unset($content);
	}
	
	// 获取插件目录
	$pluginpaths = $conf['plugin_disable'] ? array() : core::get_paths($conf['plugin_path'], TRUE);
	
	// 遍历 control
	foreach(array_merge($pluginpaths, $conf['control_path']) as $path) {
		
		// 如果有相关的 app path, 这只读取该目录
		if(is_dir($path.$conf['app_id'])) {
			$path = $path.$conf['app_id'].'/';
		}
		foreach((array)glob($path."*_control.class.php") as $file) {
			if(!is_file($file)) continue;
			$filename = substr(strrchr($file, '/'), 1);
			$objfile = $tmppath.$conf['app_id']."_control_$filename";
			
			$s = file_get_contents($file);
			core::process_include($conf, $s);
			
			$_ENV['preg_replace_callback_arg'] = $conf;
			$s = preg_replace_callback('#\t*\/\/\s*hook\s+([^\s]+)#is', 'core::process_hook_callback', $s);
			
			core::process_urlrewrite($conf, $s);
			file_put_contents($objfile, $s);
			unset($s);
		}
	}
	
	// 遍历 view，插入点的 .htm 编译是多余的，不过不碍事。
	$view = new template($conf);
	foreach(array_merge($pluginpaths, $conf['view_path']) as $path) {
		// 如果有相关的 app path, 这只读取该目录
		if(is_dir($path.$conf['app_id'])) {
			$path = $path.$conf['app_id'].'/';
		}
		foreach((array)glob($path."*.htm") as $file) {
			if(!is_file($file)) continue;
			$filename = substr(strrchr($file, '/'), 1);
			$objfile = $tmppath.$conf['app_id']."_view_$filename.php";
			$s = $view->complie($file);
			file_put_contents($objfile, $s);
		}
	}
	unset($view);
	
	// 遍历 model，公共
	foreach(array_merge($pluginpaths, $conf['model_path']) as $path) {
		foreach((array)glob($path."*.class.php") as $file) {
			if(!is_file($file)) continue;
			$filename = substr(strrchr($file, '/'), 1);
			$objfile = $tmppath."model_$filename";
			$s = file_get_contents($file);
			
			$_ENV['preg_replace_callback_arg'] = $conf;
			$s = preg_replace_callback('#\t*\/\/\s*hook\s+([^\s]+)#is', 'core::process_hook_callback', $s);

			core::process_urlrewrite($conf, $s);
			file_put_contents($objfile, $s);
			unset($s);
		}
	}
	
	// --------> bbsadmin start
	
	$bbsconf = $conf;
	$adminconf = include BBS_PATH.'admin/conf/conf.php';
	$adminconf += $conf;
	$conf = $adminconf;
	
	// 遍历 bbsadmin control
	foreach(array_merge($pluginpaths, $conf['control_path']) as $path) {
		
		// 如果有相关的 app path, 这只读取该目录
		if(is_dir($path.$conf['app_id'])) {
			$path = $path.$conf['app_id'].'/';
		}
		foreach((array)glob($path."*_control.class.php") as $file) {
			if(!is_file($file)) continue;
			$filename = substr(strrchr($file, '/'), 1);
			$objfile = $tmppath.$conf['app_id']."_control_$filename";
			
			$s = file_get_contents($file);
			core::process_include($conf, $s);
			
			$_ENV['preg_replace_callback_arg'] = $conf;
			$s = preg_replace_callback('#\t*\/\/\s*hook\s+([^\s]+)#is', 'core::process_hook_callback', $s);

			core::process_urlrewrite($conf, $s);
			file_put_contents($objfile, $s);
			unset($s);
		}
	}
	
	// 遍历 bbsadmin view
	$view = new template($conf);
	foreach(array_merge($conf['view_path'], $pluginpaths) as $path) {
		// 如果有相关的 app path, 这只读取该目录
		if(is_dir($path.$conf['app_id'])) {
			$path = $path.$conf['app_id'].'/';;
		}
		foreach((array)glob($path."*.htm") as $file) {
			if(!is_file($file)) continue;
			$filename = substr(strrchr($file, '/'), 1);
			$objfile = $tmppath.$conf['app_id']."_view_$filename.php";
			$s = $view->complie($file);
			file_put_contents($objfile, $s);
		}
	}
	unset($view);
	
	$conf = $bbsconf;
	
	// --------> bbsadmin end
	
	// 打包
	if(IN_SAE) {
		xn_zip::zip($tmppath.'tmp.zip', $tmppath);
		copy($tmppath.'tmp.zip', 'saestor://upload/tmp.zip');
	}
}
?>