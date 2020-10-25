<?php
/**
 * VgotFaster PHP Framework
 *
 * File Helpers
 *
 * @package VgotFaster
 * @author pader
 * @copyright Copyright (c) 2009-2010, VGOT.NET
 * @link http://www.vgot.net/ http://vgotfaster.googlecode.com
 * @filesource
 */

/**
 * File Write Helper
 *
 * @param string $file
 * @param string $content
 * @param string $mod
 * @param bool $exit
 * @return bool
 */
if(!function_exists('fileWrite'))
{
	function fileWrite($file,$content,$mod='w',$exit=FALSE) {
		$state = FALSE;
		$do = 'open';
		if($fp = @fopen($file, $mod)) {
			$do = 'lock';
			if(@flock($fp, LOCK_EX)) {
				$do = 'write';
				$state = @fwrite($fp, $content) !== FALSE ? TRUE : FALSE;
				@flock($fp, LOCK_UN);
				@fclose($fp);
			}
		}
		if(!$state and $exit) exit("No access to $do file: $file");
		return $state;
	}
}

/**
 * 格式化文件大小
 *
 * 将原始文件大小转换为单位文件大小
 *
 * @param int $filesize
 * @return string
 */
if(!function_exists('formatFilesize'))
{
	function formatFilesize($filesize) {
		$filesizename = array('Bytes','KB','MB','GB','TB','PB','EB','ZB','YB');
		return $filesize ? number_format($filesize/pow(1024, ($i = floor(log($filesize, 1024)))), 2, '.', '') . $filesizename[$i] : '0 Bytes';
	}
}

if(!function_exists('symbolicPermissions'))
{
	function symbolicPermissions($perms) {
		if(($perms & 0xC000) == 0xC000) {
			$symbolic = 's'; // Socket
		} elseif(($perms & 0xA000) == 0xA000) {
			$symbolic = 'l'; // Symbolic Link
		} elseif(($perms & 0x8000) == 0x8000) {
			$symbolic = '-'; // Regular
		} elseif(($perms & 0x6000) == 0x6000) {
			$symbolic = 'b'; // Block special
		} elseif(($perms & 0x4000) == 0x4000) {
			$symbolic = 'd'; // Directory
		} elseif(($perms & 0x2000) == 0x2000) {
			$symbolic = 'c'; // Character special
		} elseif(($perms & 0x1000) == 0x1000) {
			$symbolic = 'p'; // FIFO pipe
		} else {
			$symbolic = 'u'; // Unknown
		}

		// Owner
		$symbolic .= (($perms & 0x0100) ? 'r' : '-');
		$symbolic .= (($perms & 0x0080) ? 'w' : '-');
		$symbolic .= (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x' ) : (($perms & 0x0800) ? 'S' : '-'));

		// Group
		$symbolic .= (($perms & 0x0020) ? 'r' : '-');
		$symbolic .= (($perms & 0x0010) ? 'w' : '-');
		$symbolic .= (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x' ) : (($perms & 0x0400) ? 'S' : '-'));

		// World
		$symbolic .= (($perms & 0x0004) ? 'r' : '-');
		$symbolic .= (($perms & 0x0002) ? 'w' : '-');
		$symbolic .= (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x' ) : (($perms & 0x0200) ? 'T' : '-'));

		return $symbolic;
	}
}

/**
 * 此函数用于取代 php 自带的 pathinfo() 函数
 *
 * 自带的 pathinfo() 函数在非 windows 操作系统中表现很不正常
 *
 * @param string $path 文件路径
 * @param string $get 是否只获取返回值中的一个元素[填写索引]
 * @return array|string
 */
if (!function_exists('pathinfoCompat'))
{
	function pathinfoCompat($path,$index='')
	{
		$path = str_replace('\\','/',$path); //只使用 / 斜杠
		$info = array();

		$pathExport = explode('/',$path);
		$count = count($pathExport);
		$baseName = end($pathExport);

		$lastPoint = strrpos($baseName,'.');
		unset($pathExport[$count - 1]);

		$info['dirname'] = join('/',$pathExport);
		$info['basename'] = $baseName;  //baseName
		$info['extension'] = $lastPoint !== FALSE ? substr($baseName,$lastPoint + 1) : '';  //extension
		$info['filename'] = $lastPoint !== FALSE ? substr($baseName,0,$lastPoint) : $info['basename'];  //fileName
		return $index == '' ? $info : $info[$index];
	}
}
