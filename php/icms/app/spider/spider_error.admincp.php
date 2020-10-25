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
define('iPHP_WAF_CSRF', true);

class spider_errorAdmincp {
	public function __construct() {
		spiderAdmincp::init($this);
	}
	/**
	 * [采集错误结果管理]
	 * @return [type] [description]
	 */
	public function do_manage() {
		$sql = " WHERE 1=1";
		$_GET['pid'] && $sql .= " AND `pid` ='" . (int) $_GET['pid'] . "'";
		$_GET['rid'] && $sql .= " AND `rid` ='" . (int) $_GET['rid'] . "'";
		$days = $_GET['days'] ? $_GET['days'] : "7";
		$days && $sql.=" AND `addtime`>".strtotime('-'.$days.' day');
		$ruleArray = spider_rule::option(0, 'array');
		// $postArray = $this->post_opt(0, 'array');
		// list($orderby,$orderby_option) = get_orderby();
		$maxperpage = $_GET['perpage'] > 0 ? (int) $_GET['perpage'] : 100;
		// $total = iPagination::totalCache( "SELECT count(*) FROM `#iCMS@__spider_error` {$sql}", "G");
		// iUI::pagenav($total, $maxperpage, "个网页");
		// $rs = iDB::all("SELECT * FROM `#iCMS@__spider_error` {$sql} order by {$orderby} LIMIT " . iPagination::$offset . " , {$maxperpage}");
		$rs = iDB::all("
		    SELECT
		      `pid`,`rid`,COUNT(id) AS ct,`date`
		    FROM
		      `#iCMS@__spider_error`
		    {$sql}
		    GROUP BY pid DESC
		    ORDER BY ct DESC, `date` DESC
		    LIMIT {$maxperpage}
		");
		$_count = count($rs);
		include admincp::view("error.manage");
	}
    public function do_view(){
        $date = $_GET['date'];
        $date && $sql.=" AND `date`='$date'";

		$days = $_GET['days'] ? $_GET['days'] : "7";
		$days && $sql.=" AND `addtime`>".strtotime('-'.$days.' day');

		$rs = iDB::all("
		    SELECT *,
		    	COUNT(id) AS ct,
		    	group_concat(`msg`) as `msg`,
		    	group_concat(`type`) as `type`
		    FROM
		      `#iCMS@__spider_error`
			where pid='$this->pid' {$sql}
			GROUP by url
			ORDER BY id DESC
		");

        include admincp::view("error.view");
    }
	/**
	 * [删除错误信息]
	 * @return [type] [description]
	 */
	public function do_del() {
		$this->pid OR iUI::alert("请选择要删除的项目");
		iDB::query("delete from `#iCMS@__spider_error` where `pid` = '$this->pid';");
		iUI::success('删除完成', 'js:1');
	}
}
