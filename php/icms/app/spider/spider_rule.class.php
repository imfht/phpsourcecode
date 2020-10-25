<?php

/**
 * iCMS - i Content Management System
 * Copyright (c) 2007-2017 iCMSdev.com. All rights reserved.
 *
 * @author icmsdev <master@icmsdev.com>
 * @site https://www.icmsdev.com
 * @licence https://www.icmsdev.com/LICENSE.html
 */
defined('iPHP') OR exit('What are you doing?');

class spider_rule {
    public static function get($id) {
    	$key = 'spider:rule:'.$id;
    	$rs = $GLOBALS[$key];
    	if(!isset($GLOBALS[$key])){
	        $rs = iDB::row("SELECT * FROM `#iCMS@__spider_rule` WHERE `id`='$id' LIMIT 1;", ARRAY_A);
	        $rs['rule'] && $rs['rule'] = (array)stripslashes_deep(json_decode($rs['rule'],true));
	        $rs['rule']['user_agent'] OR $rs['rule']['user_agent'] = "Mozilla/5.0 (compatible; Baiduspider/2.0; +http://www.baidu.com/search/spider.html)";
        	$GLOBALS[$key] = $rs;
        }
        spider::$useragent = $rs['rule']['user_agent'];
        spider::$encoding  = $rs['rule']['curl']['encoding'];
        spider::$referer   = $rs['rule']['curl']['referer'];
        spider::$cookie    = $rs['rule']['curl']['cookie'];
        spider::$charset   = $rs['rule']['charset'];
        return $rs;
    }
	public static function option($id = 0, $output = null) {
		$rs = iDB::all("SELECT * FROM `#iCMS@__spider_rule` order by id desc");
		foreach ((array) $rs AS $rule) {
			$rArray[$rule['id']] = $rule['name'];
			$opt .= "<option value='{$rule['id']}'" . ($id == $rule['id'] ? " selected='selected'" : '') . ">{$rule['name']}[id='{$rule['id']}'] </option>";
		}
		if ($output == 'array') {
			return $rArray;
		}
		return $opt;
	}

}
