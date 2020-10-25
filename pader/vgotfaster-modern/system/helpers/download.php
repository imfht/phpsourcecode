<?php
/**
 * VgotFaster PHP Framework
 *
 * Download Helpers
 *
 * @package VgotFaster
 * @author pader
 * @copyright Copyright (c) 2009-2010, VGOT.NET
 * @link http://www.vgot.net/ http://vgotfaster.googlecode.com
 * @filesource
 */

if(!function_exists('headerDownload'))
{
	function headerDownload($filename,$data=NULL,$from='data') {
		forceDownload($filename,$data,$from);
	}
}

/**
 * Send A Header Information To Browse
 *
 * @param string $filename Filename or filepath
 * @param mixed $data When is NULL, $fileName was download file path, else $from was data,$data as download file's contents.
 * @param string $from When is 'file', $fileName as download filename and $data was source file path.
 * @param string $contentType Force mimetype
 * @return void
 */
if (!function_exists('forceDownload'))
{
	function forceDownload($filename,$data=NULL,$from='data',$contentType=NULL) {
		if (is_null($data)) {
			if (!file_exists($filename)) showError('Download file does not exists or argument 2 is NULL.');
			$x = explode('/',str_replace('\\','/',$filename));
			$fileRename = end($x);
			$filesize = filesize($filename);
		} elseif (strtolower($from) == 'file') {
			if (!file_exists($data)) showError('Download file does not exists.');
			$fileRename = $filename;  $filename = $data;  $data = NULL;
			$filesize = filesize($filename);
		} else {
			$fileRename = $filename;
			$filesize = strlen($data);
		}

		!$contentType AND $contentType = 'application/octet-stream';

		header('HTTP/1.1 200 OK');
		header('Accept-Ranges: bytes');
		header('Content-Type: '.$contentType);
		header('Content-Disposition: attachment;filename="'.$fileRename.'";');
		header('Expires: 0');
		header('Content-Transfer-Encoding: binary;');
		header('Content-Length: '.$filesize);

		if (is_null($data)) {
			readfile($filename);
		} else {
			exit($data);
		}
	}
}
