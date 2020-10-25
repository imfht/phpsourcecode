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

class spider_projectAdmincp {
	public function __construct() {
		spiderAdmincp::init($this);
	}
	/**
	 * [测试采集方案]
	 * @return [type] [description]
	 */
	public function do_test() {
		spider::$dataTest = true;
		spider_data::crawl();
	}
	/**
	 * [复制采集方案]
	 * @return [type] [description]
	 */
	public function do_copy() {
		$fields    = apps_db::fields('#iCMS@__spider_project');
		$fields    = array_column($fields, 'field');
		$key       = array_search('id',$fields);unset($fields[$key]);
		$field_SQL = implode('`,`', $fields);
		iDB::query("
			INSERT INTO `#iCMS@__spider_project` (`".$field_SQL."`)
			SELECT `".$field_SQL."` FROM `#iCMS@__spider_project` WHERE id = '$this->pid'
		");
		$pid = iDB::$insert_id;
		iUI::success('复制完成,编辑此方案', 'url:' . APP_URI . '&do=add&pid=' . $pid . '&copy=1');
	}
	public function do_batch() {
		list($idArray,$ids,$batch) = iUI::get_batch_args();
		switch ($batch) {
	        case 'poid':
	            $poid = (int)$_POST['poid'];
	            iDB::query("update `#iCMS@__spider_project` set `poid`='$poid' where `id` IN($ids);");
	            iUI::success('操作成功!','js:1');
	        break;
	        case 'rid':
	            $rid = (int)$_POST['rid'];
	            iDB::query("update `#iCMS@__spider_project` set `rid`='$rid' where `id` IN($ids);");
	            iUI::success('操作成功!','js:1');
	        break;
	        case 'move':
	            $cid = (int)$_POST['cid'];
	            iDB::query("update `#iCMS@__spider_project` set `cid`='$cid' where `id` IN($ids);");
	            iUI::success('操作成功!','js:1');
	        break;
			case 'del':
				iDB::query("delete from `#iCMS@__spider_project` where `id` IN($ids);");
				break;
			default:
				if (strpos($batch, ':') !== false) {
					$data = iSQL::update_args($batch);
					foreach ($idArray AS $id) {
						$data && iDB::update("spider_project", $data, array('id' => $id));
					}
					iUI::success('操作成功!', 'js:1');
				}
				iUI::alert('参数错误!', 'js:1');
		}
		iUI::success('全部删除成功!', 'js:1');
	}
	/**
	 * [采集方案管理]
	 * @return [type] [description]
	 */
	public function do_manage($a=null) {

		$sql = "where 1=1";
		if ($_GET['keywords']) {
			$sql .= " and `name` REGEXP '{$_GET['keywords']}'";
		}
		$sql .= category::search_sql($this->cid);

		if ($_GET['rid']) {
			$sql .= " AND `rid` ='" . (int) $_GET['rid'] . "'";
		}
		if (isset($_GET['auto'])) {
			$sql .= " AND `auto` ='".$_GET['auto']."'";
		}
		if ($_GET['poid']) {
			$sql .= " AND `poid` ='" . (int) $_GET['poid'] . "'";
		}
        $_GET['starttime'] && $sql.=" AND `lastupdate`>='".str2time($_GET['starttime']." 00:00:00")."'";
        $_GET['endtime']   && $sql.=" AND `lastupdate`<='".str2time($_GET['endtime']." 23:59:59")."'";
		$ruleArray = spider_rule::option(0, 'array');
		$postArray = spider_post::option(0, 'array');
		list($orderby,$orderby_option) = get_orderby();
		$maxperpage = $_GET['perpage'] > 0 ? (int) $_GET['perpage'] : 20;
		$total = iPagination::totalCache( "SELECT count(*) FROM `#iCMS@__spider_project` {$sql}", "G");
		iUI::pagenav($total, $maxperpage, "个方案");
		$rs = iDB::all("SELECT * FROM `#iCMS@__spider_project` {$sql} order by {$orderby} LIMIT " . iPagination::$offset . " , {$maxperpage}");
		$_count = count($rs);
		include admincp::view("project.manage");
	}
	/**
	 * [删除采集方案]
	 * @return [type] [description]
	 */
	public function do_del() {
		$this->pid OR iUI::alert("请选择要删除的项目");
		iDB::query("delete from `#iCMS@__spider_project` where `id` = '$this->pid';");
		iUI::success('删除完成');
	}
	/**
	 * [添加采集方案]
	 * @return [type] [description]
	 */
	public function do_add() {
		$rs = array();
		$this->pid && $rs = spider_project::get($this->pid);
		$cid = empty($rs['cid']) ? $this->cid : $rs['cid'];

		$cata_option = category::select($cid);
		$rule_option = spider_rule::option($rs['rid']);
		$post_option = spider_post::option($rs['poid']);

		//$rs['sleep'] OR $rs['sleep'] = 30;
		include admincp::view("project.add");
	}
	/**
	 * [保存采集方案]
	 * @return [type] [description]
	 */
	public function do_save() {
		$id   = (int) $_POST['id'];
		$name = iSecurity::escapeStr($_POST['name']);
		$urls = iSecurity::escapeStr($_POST['urls']);
		$cid  = iSecurity::escapeStr($_POST['cid']);
		$rid  = iSecurity::escapeStr($_POST['rid']);
		$poid = iSecurity::escapeStr($_POST['poid']);
		$auto = iSecurity::escapeStr($_POST['auto']);
		$lastupdate = $_POST['lastupdate'] ? str2time($_POST['lastupdate']) : '';

		$config = iSecurity::escapeStr($_POST['config']);
		$config = addslashes(json_encode($config));

		empty($name) && iUI::alert('名称不能为空！');
		empty($cid) && iUI::alert('请选择绑定的栏目');
		empty($rid) && iUI::alert('请选择采集规则');
		//empty($poid)	&& iUI::alert('请选择发布规则');
		$fields = array('name', 'urls', 'cid', 'rid', 'poid', 'auto', 'lastupdate', 'config');
		$data   = compact($fields);
		if ($id) {
			iDB::update('spider_project', $data, array('id' => $id));
		} else {
			iDB::insert('spider_project', $data);
		}
		iUI::success('完成', 'url:' . APP_URI . '&do=manage');
	}
	/**
	 * [导入采集方案]
	 * @return [type] [description]
	 */
    public function do_import(){
        files::$check_data        = false;
        files::$cloud_enable      = false;
        iFS::$config['allow_ext'] = 'txt';
        $F    = iFS::upload('upfile','spider');
        $path = $F['RootPath'];
        if($path){
            $data = file_get_contents($path);
            @unlink($path);
            if($data){
                $data = base64_decode($data);
                $data = json_decode($data,true);
                if(is_array($data)){
	                foreach ($data as $key => $value) {
	                	$value = iSecurity::slashes($value);
	                    iDB::insert("spider_project",$value);
	                }
                }else{
                	iUI::alert('导入方案出现错误');
                }
            }
            iUI::success('方案导入完成,请重新设置规则','js:1');
        }
    }
	/**
	 * [导出采集方案]
	 * @return [type] [description]
	 */
    public function do_export(){
        $data = iDB::all("
        	SELECT `name`, `urls`,`cid`, `rid`, `poid`, `config`,`lastupdate`
        	FROM `#iCMS@__spider_project`
        	WHERE rid = '$this->rid'
        ");
        if(empty($data)){
        	iUI::alert('未找到属于规则:'.$this->rid.'的方案','js:1');
        }
        $data = base64_encode(json_encode($data));
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=spider.rule.".$this->rid.'.project.txt');
		echo $data;
	}
	public function do_update_lastupdate(){
		iDB::update('spider_project', array('lastupdate' => time()), array('id' => (int)$_GET['id']));
	}
}
