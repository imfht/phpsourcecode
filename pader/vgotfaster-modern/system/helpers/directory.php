<?php
/**
 * VgotFaster PHP Framework
 *
 * Directory Helpers
 *
 * @package VgotFaster
 * @author pader
 * @copyright Copyright (c) 2009-2010, VGOT.NET
 * @link http://www.vgot.net/ http://vgotfaster.googlecode.com
 * @filesource
 */

/**
 * Create a multi-directory [recursive]
 *
 * @param string $dir
 * @param int $mode
 * @return
 */
if(!function_exists('mkdirs'))
{
	function mkdirs($dir,$mode=0777) {
		//return is_dir($dir) or (mkdirs(dirname($dir)) and mkdir($dir, $mode));
		if(is_dir($dir)) return TRUE; else {
			if(mkdirs(dirname($dir)) and mkdir($dir,$mode)) {
				chmod($dir,0777);
				return TRUE;
			} else return FALSE;
		}
	}
}

/**
 * Delete non-empty directory
 *
 * @param string $dirName
 * @return
 */
if(!function_exists('removeDir'))
{
	function removeDir($dirName) {
		if(!is_dir($dirName)) {
			return false;
		}
		$handle = @opendir($dirName);
		while(($file = @readdir($handle)) !== false) {
			if($file != '.' && $file != '..') {
				$dir = $dirName . '/' . $file;
				is_dir($dir) ? removeDir($dir) : @unlink($dir);
			}
		}
		closedir($handle);
		return rmdir($dirName) ;
	}
}

/**
 * Count Directory Used Size
 *
 * @param string $dir Directory path
 * @return int
 */
if(!function_exists('dirTotalSpace'))
{
	function dirTotalSpace($dir) {
		$handle = @opendir($dir);
		$size = 0;
		while($file = @readdir($handle)) {
			if($file != '.' && $file != '..') {
				$path = $dir.'/'.$file;
				if(@is_dir($path)) {
					$size += dirTotalSpace($path);
				} else {
					$size += @filesize($path);
				}
			}
		}
		@closedir($handle);
		return $size;
	}
}

/**
 * Count Sub Directorys
 *
 * @param string $dir Parent directory path
 * @return int
 */
if(!function_exists('dirCount'))
{
	function dirCount($dir) {
		$handle = @opendir($dir);
		$count = 0;
		while($file = @readdir($handle)) {
			if($file != '.' && $file != '..') {
				$path = $dir.'/'.$file;
				if(@is_dir($path)) {
					$count++;
				}
			}
		}
		@closedir($handle);
		return $count;
	}
}

if (!function_exists('dirDeepScan'))
{
	function dirDeepScan($dir) {
		$result = array();
		$handle = opendir($dir);
		while ($filename = readdir($handle)) {
			if ($filename == '.' OR $filename == '..') continue;
			$path = $dir.'/'.$filename;
			if (is_dir($path)) {
				$deep= dirDeepScan($path);
				$result = array_merge($result,$deep);
			} else {
				$result[] = $path;
			}
		}
		closedir($handle);
		return $result;
	}
}
