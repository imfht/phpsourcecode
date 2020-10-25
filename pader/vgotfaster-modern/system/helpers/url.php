<?php
/**
 * VgotFaster PHP Framework
 *
 * URL Helpers
 *
 * @package VgotFaster
 * @author pader
 * @copyright Copyright (c) 2009-2015, VGOT.NET
 * @link http://www.vgot.net/ http://vgotfaster.googlecode.com
 * @filesource
 */

if (!function_exists('baseUrl'))
{
	/**
	 * Return Base URL Fron Framework Configuration
	 *
	 * If base_url is empty in config, program will automatically identify and returns a relative base url address
	 *
	 * @param bool $absolute
	 * @return string
	 */
	function baseUrl($absolute=FALSE) {
		static $baseUrl = NULL, $absoluteUrl = NULL;
		if ($absolute) {
			if ($absoluteUrl === null) {
				$absoluteUrl = baseUrl();
				if (!preg_match('!^\w+://!i', $absoluteUrl)) {
					$protocol = strtolower(substr($_SERVER['SERVER_PROTOCOL'], 0, strpos($_SERVER['SERVER_PROTOCOL'], '/')));
					//$port = $_SERVER['SERVER_PORT'] == 80 ? '' : ':'.$_SERVER['SERVER_PORT'];
					$absoluteUrl = $protocol.'://'.$_SERVER['HTTP_HOST'].$absoluteUrl;
				}
			}
			return $absoluteUrl;
		} else {
			if($baseUrl === null) {
				$VF =& getInstance();
				$baseUrl = $VF->config->get('config','base_url');
				if($baseUrl == '') {
					$baseUrl = str_replace('\\','/',dirname($_SERVER['SCRIPT_NAME']));
					$baseUrl = trim($baseUrl,'/');
					$baseUrl = empty($baseUrl) ? '/' : "/$baseUrl/";
				}
			}
			return $baseUrl;
		}
	}
}

if (!function_exists('siteUrl'))
{
	/**
	 * Generate Site URL
	 *
	 * @param string $uri
	 * @param bool $suffix With suffix?
	 * @param bool $absolute
	 * @return string URL address
	 */
	function siteUrl($uri,$suffix=TRUE,$absolute=FALSE) {
		$config =& $GLOBALS['CONFIG']['config'];
		switch($config['router_method']) {
			case 'PATH_INFO':
			case 'QUERY_STRING':
				$config['uri_separator_replace'] && $uri = str_replace('/',$config['uri_separator'],$uri);
				if($config['router_method'] == 'QUERY_STRING') {
					$join = '?';
					$baseUrl = '';
				} else {
					$join = empty($config['index_file']) ? '' : '/';
					$baseUrl = baseUrl($absolute);
				}
				$begin = $baseUrl.$config['index_file'];
				return empty($uri) ? $begin : $begin.$join.$uri.($suffix ? $config['url_suffix'] : '');
			break;
			default: showError('Unspport URI method of function siteUrl()',FALSE);
		}
	}
}

if (!function_exists('anchor'))
{
	/**
	 * Generate A Link HTML Code
	 *
	 * @param string $uri
	 * @param string $title
	 * @param array|string $attributes
	 * @return string <a> HTML Code
	 */
	function anchor($uri='',$title='',$attributes='') {
		$title = (string)$title;

		if (!is_array($uri)) {
			$site_url = (!preg_match('!^\w+://! i', $uri)) ? siteUrl($uri) : $uri;
		} else {
			$site_url = siteUrl($uri);
		}

		if($title == '') {
			$title = $site_url;
		}

		if($attributes != '') {
			$attributes = _parseAttributes($attributes);
		}

		return '<a href="'.$site_url.'"'.$attributes.'>'.$title.'</a>';
	}
}

if (!function_exists('_parse_attributes'))
{
	/**
	 * Parse Out The Attributes
	 *
	 * Some of the functions use this
	 *
	 * @access private
	 * @param array
	 * @param bool
	 * @return string
	 */
	function _parseAttributes($attributes,$javascript=FALSE) {
		if(is_string($attributes)) {
			return ($attributes != '') ? ' '.$attributes : '';
		}

		$att = '';
		foreach($attributes as $key => $val) {
			if($javascript == TRUE) {
				$att .= $key.'='.$val.',';
			} else {
				$att .= ' '.$key.'="'.$val.'"';
			}
		}

		if($javascript == TRUE AND $att != '') {
			$att = substr($att,0,-1);
		}

		return $att;
	}
}

if (!function_exists('redirect'))
{
	/**
	 * Header Redirect
	 *
	 * Fast to goto an url
	 *
	 * @param string $uri
	 * @param string $method location or refresh
	 * @param int $httpResponseCode
	 * @return void
	 */
	function redirect($uri='',$method='location',$httpResponseCode=302) {
		if(!preg_match('#^https?://#i',$uri)) {
			$uri = siteUrl($uri);
		}
		switch($method)
		{
			case 'refresh': header('Refresh:0;url='.$uri); break;
			default: header('Location: '.$uri, TRUE, $httpResponseCode);
		}
		exit;
	}
}

if (!function_exists('absoluteBaseUrl'))
{
	function absoluteBaseUrl() {
		static $absolute = NULL;

		if (is_null($absolute)) {
			$absolute = baseUrl();
			if (!preg_match('!^\w+://!i', $absolute)) {
        $protocol = strtolower(substr($_SERVER['SERVER_PROTOCOL'], 0, strpos($_SERVER['SERVER_PROTOCOL'], '/')));
				$port = $_SERVER['SERVER_PORT'] == 80 ? '' : ':'.$_SERVER['SERVER_PORT'];
				$absolute = $protocol.'://'.$_SERVER['SERVER_NAME'].$port;
			}
		}

		return $absolute;
	}
}
