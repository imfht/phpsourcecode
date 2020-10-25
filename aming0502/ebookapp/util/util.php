<?php

//global $base_dir="/ebook";

function makehtml($file,$content){
	$fp = fopen($file,'w');
	fwrite($fp,$content);
	fclose($fp);
}

function myflush(){
	flush();
	ob_flush();
}

function myfile_get_content($url) {
	if (function_exists('file_get_contents')) {
		$file_contents = @file_get_contents($url);
	}
	if ($file_contents == '') {
		echo "get url ["+$url+"] file contents empty";
		$ch = curl_init();
		$timeout = 30;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$file_contents = curl_exec($ch);
		curl_close($ch);
	}
	return $file_contents;
}

function pagesleep(){
	echo "<br>暂停1秒后继续采集...
		<script language=\"javascript\">setTimeout(\"gonextpage();\",1000);
		function gonextpage(){location.href=window.location;}</script><a href='javascript:gonextpage();'>点击进入下一页</a>";
}

function setlocation($location){
	echo "<script>window.location='".$location."'</script>";
}

function dhtmlspecialchars($string, $flags = null) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = dhtmlspecialchars($val, $flags);
		}
	} else {
		if($flags === null) {
			$string = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string);
			if(strpos($string, '&amp;#') !== false) {
				//过滤掉类似&#x5FD7的16进制的html字符
				$string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $string);
			}
		} else {
			if(PHP_VERSION < '5.4.0') {
				$string = htmlspecialchars($string, $flags);
			} else {
				if(strtolower(CHARSET) == 'utf-8') {
					$charset = 'UTF-8';
				} else {
					$charset = 'ISO-8859-1';
				}
				$string = htmlspecialchars($string, $flags, $charset);
			}
		}
	}
	return $string;
}


/*
 函数名称：inject_check()
 函数作用：检测提交的值是不是含有SQL注射的字符，防止注射，保护服务器安全
 参　　数：$sql_str: 提交的变量
 返 回 值：返回检测结果，ture or false
 */
function inject_check($sql_str) {
	//if(preg_match('/^test/i',$file))
	return preg_match('/select|insert|and|or|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile/i', $sql_str); // 进行过滤
}

/*
 函数名称：verify_id()
 函数作用：校验提交的ID类值是否合法
 参　　数：$id: 提交的ID值
 返 回 值：返回处理后的ID
 */
function verify_id($id=null) {
	if (!$id) { exit('没有提交参数！'); } // 是否为空判断
	elseif (inject_check($id)) { exit('提交的参数非法！'); } // 注射判断
	elseif (!is_numeric($id)) { exit('提交的参数非法！'); } // 数字判断
	$id = intval($id); // 整型化

	return $id;
}

/*
 discuz的php防止sql注入函数
 函数名称：str_check()
 函数作用：对提交的字符串进行过滤
 参　　数：$var: 要处理的字符串
 返 回 值：返回过滤后的字符串
 */
function str_check( $str ) {
	if (!get_magic_quotes_gpc()) { // 判断magic_quotes_gpc是否打开
		$str = addslashes($str); // 进行过滤
	}
	if (inject_check($str)) { exit('提交的参数非法！'); } // 注射判断
	$str = str_replace("_", "\_", $str); // 把 '_'过滤掉
	$str = str_replace("%", "\%", $str); // 把 '%'过滤掉
	$str = htmlspecialchars($str); // html标记转换

	return $str;
}

/*
 函数名称：post_check()
 函数作用：对提交的编辑内容进行处理
 参　　数：$post: 要提交的内容
 返 回 值：$post: 返回过滤后的内容
 */
function post_check($post) {
	if (!get_magic_quotes_gpc()) { // 判断magic_quotes_gpc是否为打开
		$post = addslashes($post); // 进行magic_quotes_gpc没有打开的情况对提交数据的过滤
	}
	$post = str_replace("_", "\_", $post); // 把 '_'过滤掉
	$post = str_replace("%", "\%", $post); // 把 '%'过滤掉
	$post = nl2br($post); // 回车转换
	$post = htmlspecialchars($post); // html标记转换

	return $post;
}


/**
 对于敏感字符进行转义
 **/
function convert($t_Val){
	$t_Val = str_replace("&", "&amp;",$t_Val);
	$t_Val = str_replace("<", "&lt;",$t_Val);
	$t_Val = str_replace(">", "&gt;",$t_Val);
	if ( get_magic_quotes_gpc() )
	{
		$t_Val = str_replace("\\\"", "&quot;",$t_Val);
		$t_Val = str_replace("\\''", "&#039;",$t_Val);
	}
	else
	{
		$t_Val = str_replace("\"", "&quot;",$t_Val);
		$t_Val = str_replace("'", "&#039;",$t_Val);
	}
	return $t_Val;
}

function deldir($dir) {
  //先删除目录下的文件：
  $dh=opendir($dir);
  while ($file=readdir($dh)) {
    if($file!="." && $file!="..") {
      $fullpath=$dir."/".$file;
      if(!is_dir($fullpath)) {
          unlink($fullpath);
      } else {
          deldir($fullpath);
      }
    }
  }
  closedir($dh);
  //删除当前文件夹：
  if(rmdir($dir)) {
    return true;
  } else {
    return false;
  }
}

function mkdir_force($dir){
	if(file_exists($dir)){
		deldir($dir);
	}
	mkdir($dir, 0777);
}



?>