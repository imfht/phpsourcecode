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

class spider_project {
    public static function get($id) {
    	$key = 'spider:project:'.$id;
    	$project = $GLOBALS[$key];
    	if(!isset($GLOBALS[$key])){
	        $project = iDB::row("SELECT * FROM `#iCMS@__spider_project` WHERE `id`='$id' LIMIT 1;", ARRAY_A);
	        if($project['config']){
				$project['config']  = (array)json_decode($project['config'],true);
				$project+= $project['config'];
	        }
    		$GLOBALS[$key] = $project;
    	}
        return $project;
    }
    public static function option($id = 0, $output = null) {
        $rs = iDB::all("SELECT * FROM `#iCMS@__spider_project` order by id desc");
        foreach ((array) $rs AS $proj) {
            $rArray[$proj['id']] = $proj['name'];
            $opt .= "<option value='{$proj['id']}'" . ($id == $proj['id'] ? " selected='selected'" : '') . ">{$proj['name']}[id='{$proj['id']}'] </option>";
        }
        if ($output == 'array') {
            return $rArray;
        }
        return $opt;
    }
}
