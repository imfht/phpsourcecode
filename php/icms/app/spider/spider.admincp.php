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

class spiderAdmincp {

	public function __construct() {
		self::init($this);
	}
	public static function init(&$a) {
		spider::$cid = $a->cid = (int) $_GET['cid'];
		spider::$rid = $a->rid = (int) $_GET['rid'];
		spider::$pid = $a->pid = (int) $_GET['pid'];
		spider::$sid = $a->sid = (int) $_GET['sid'];
		spider::$title = $a->title = $_GET['title'];
		spider::$url = $a->url = $_GET['url'];
		spider::$work = $a->work = false;
		$a->poid = (int) $_GET['poid'];
	}
	/**
	 * [更新采集结果]
	 * @return [type] [description]
	 */
	public function do_update() {
		if ($this->sid) {
			$data = iSQL::update_args($_GET['_args']);
			$data && iDB::update("spider_url", $data, array('id' => $this->sid));
		}
		iUI::success('操作成功!', 'js:1');
	}
	public function do_batch() {
		list($idArray,$ids,$batch) = iUI::get_batch_args();
		switch ($batch) {
		case 'delurl':
			iDB::query("delete from `#iCMS@__spider_url` where `id` IN($ids);");
		break;
		default:
			if (strpos($batch, '#') !== false) {
				list($table, $_batch) = explode('#', $batch);
				if (in_array($table, array('url', 'post', 'project', 'rul'))) {
					if (strpos($_batch, ':') !== false) {
						$data = iSQL::update_args($_batch);
						foreach ($idArray AS $id) {
							$data && iDB::update("spider_" . $table, $data, array('id' => $id));
						}
						iUI::success('操作成功!', 'js:1');
					}
				}
			}
			iUI::alert('参数错误!', 'js:1');
		}
		iUI::success('全部删除成功!', 'js:1');
	}
	/**
	 * [删除采集结果]
	 * @return [type] [description]
	 */
	public function do_delspider($dialog=true) {
		$this->sid OR iUI::alert("请选择要删除的项目");
		iDB::query("delete from `#iCMS@__spider_url` where `id` = '$this->sid';");
		$dialog && iUI::success('删除完成', 'js:1');
	}

	public function do_delcontent() {
		$indexid = $_GET['indexid'];
		$indexid OR iUI::alert("请选择要删除的项目");

		$project = spider_project::get($this->pid);
		$spost   = spider_post::get($project['poid']);
		$app     = apps::get_app($spost->app);
		$obj     = $spost->app."Admincp";
		$acp     = new $obj;
		if(method_exists($acp, 'do_del')){
			$acp->do_del($indexid,false);
			$this->do_delspider(false);
			iUI::success('删除完成');
		}else{
			iUI::success($obj.' 中没找到 do_del 方法', 'js:1');
		}
	}
	public function do_iCMS($doType = null) {
		$this->do_manage();
	}

	/**
	 * [采集结果管理]
	 * @return [type] [description]
	 */
	public function do_manage($doType = null) {
		$sql = " WHERE 1=1";
		$field = $_GET['field']?:'title';
		$_GET['keywords'] && $sql .= "  AND `{$field}` REGEXP '{$_GET['keywords']}'";
		$doType == "inbox" && $sql .= " AND `publish` ='0'";
		$_GET['pid'] && $sql .= " AND `pid` ='" . (int) $_GET['pid'] . "'";
		$_GET['rid'] && $sql .= " AND `rid` ='" . (int) $_GET['rid'] . "'";
		$_GET['status'] && $sql .= " AND `status` ='" . (int) $_GET['status'] . "'";
		$_GET['starttime'] && $sql .= " AND `addtime`>=UNIX_TIMESTAMP('" . $_GET['starttime'] . " 00:00:00')";
		$_GET['endtime'] && $sql .= " AND `addtime`<=UNIX_TIMESTAMP('" . $_GET['endtime'] . " 23:59:59')";

		$sql .= category::search_sql($this->cid);

		$projArray = spider_project::option(0, 'array');
		$ruleArray = spider_rule::option(0, 'array');
		$postArray = spider_post::option(0, 'array');
		list($orderby,$orderby_option) = get_orderby();
		$maxperpage = $_GET['perpage'] > 0 ? (int) $_GET['perpage'] : 20;
		$total = iPagination::totalCache( "SELECT count(*) FROM `#iCMS@__spider_url` {$sql}", "G");
		iUI::pagenav($total, $maxperpage, "个网页");
		$rs = iDB::all("SELECT * FROM `#iCMS@__spider_url` {$sql} order by {$orderby} LIMIT " . iPagination::$offset . " , {$maxperpage}");
		$_count = count($rs);
		include admincp::view("spider.manage");
	}

	public function do_inbox() {
		$this->do_manage("inbox");
	}

	/**
	 * [手动采集页]
	 * @return [type] [description]
	 */
	public function do_listpub() {
		$responses = spider_urls::crawl('WEB@MANUAL');
		extract($responses);
		include admincp::view("spider.lists");
	}
	/**
	 * [采集结果移除标记]
	 * @return [type] [description]
	 */
	public function do_markurl() {
		$hash = md5($this->url);
		$title = iSecurity::escapeStr($_GET['title']);
		iDB::insert('spider_url', array(
			'cid' => $this->cid,
			'rid' => $this->rid,
			'pid' => $this->pid,
			'title' => addslashes($title),
			'url' => $this->url,
			'hash' => $hash,
			'status' => '2',
			'addtime' => time(),
			'publish' => '2',
			'indexid' => '0',
			'pubdate' => '0',
		));
		iUI::success("移除成功!", 'js:parent.$("#' . $hash . '").remove();');
	}
	/**
	 * [删除采集数据]
	 * @return [type] [description]
	 */
    public function do_dropdata() {
        $this->pid OR iUI::alert("请选择要删除的项目");
		$rs      = iDB::all("SELECT `indexid`,`appid`,`pid` FROM `#iCMS@__spider_url` where `pid` = '$this->pid'");
		$project = spider_project::get($this->pid);
		$post    = spider_post::get($project['poid']);
        $_count  = count($rs);
        for ($i=0; $i <$_count ; $i++) {
			$class = $post->app.'Admincp';
			$delete = 'do_del';
        	if(@class_exists($class) && @method_exists ($class,'do_del')){
        		if($post->app=='content'){
        			$obj = new $class($rs[$i]['appid']);
        		}elseif($post->app=='forms'){
        			$obj = new $class();
        			$delete = 'do_delete';
        		}else{
        			$obj = new $class;
        		}
        		iPHP::callback(array($obj,$delete),array($rs[$i]['indexid'],false));
        	}else{
        		$msg = "未找到内容删除方法,请手动删除内容";
        	}
        }
        $msg && iUI::alert($msg);
        iDB::query("DELETE FROM `#iCMS@__spider_url` where `pid` = '$this->pid';");
        iUI::success('所有采集数据删除完成');
    }
	/**
	 * [删除采集结果数据]
	 * @return [type] [description]
	 */
	public function do_dropurl() {
		$this->pid OR iUI::alert("请选择要删除的项目");

		$type = $_GET['type'];
		if ($type == "0") {
			$sql = " AND `publish`='0'";
		}
		iDB::query("delete from `#iCMS@__spider_url` where `pid` = '$this->pid'{$sql};");
		iUI::success('数据清除完成');
	}
	/**
	 * [自动采集页]
	 * @return [type] [description]
	 */
	public function do_start() {
		$a = spider_urls::crawl('WEB@AUTO');
		$this->do_mpublish($a);
	}
	public function do_crawl($a=null,$dialog=true){
		// sleep(1);
		// echo json_encode($_POST);
		$_POST && $a = $_POST;

		spider::$sid = $a['sid'];
		spider::$cid = $a['cid'];
		spider::$pid = $a['pid'];
		spider::$rid = $a['rid'];
		spider::$url = $a['url'];
		spider::$title = $a['title'];

		isset($a['dialog']) && $dialog = $a['dialog'];

		$code = spider::publish('WEB@AUTO');

		if (is_array($code)) {
			$label = '内容ID['.$code['indexid'].'] <span class="label label-success">发布成功!</span>';
		} else {
			$code == "-1" && $label = '<span class="label label-warning">该URL的文章已经发布过!请检查是否重复</span>';
		}
		$msg = '标题:' . spider::$title . '<br />网址:' . spider::$url . '<br />信息:' . $label . '<hr />';
		$dialog && iUI::success($msg, 'js:1');
		echo $msg;
	}
	/**
	 * [批量发布]
	 * @return [type] [description]
	 */
	public function do_mpublish($pubArray = array()) {
		if ($_POST['pub']) {
			foreach ((array) $_POST['pub'] as $i => $a) {
				list($cid, $pid, $rid, $url, $title) = explode('|', $a);
				$pubArray[] = array('sid' => 0, 'url' => $url, 'title' => $title, 'cid' => $cid, 'rid' => $rid, 'pid' => $pid);
			}
		}

		empty($pubArray) && iUI::alert('暂无最新内容', 0, 30);

		$_count    = count($pubArray);
		$jsonArray = array();

		foreach ((array) $pubArray as $i => $a) {
			$a['index']    = $i;
			$a['dialog']   = 0;
			$a['md5']      = md5($a['url']);
			$jsonArray[$i] = $a;
		}

		iUI::$break = false;
		iUI::flush_start();
		iUI::dialog('开始采集', '', false, 0, false);
		include admincp::view("spider.mpublish");
// echo '<script type="text/javascript">';
// 		foreach ((array) $pubArray as $i => $a) {
// 			$a['index']    = $i;
// 			$a['dialog']   = 0;
// 			$a['md5']      = md5($a['url']);
// 			// $jsonArray[$i] = $a;
// 			echo 'var a = '.json_encode($a).';crawl_run(a);'.PHP_EOL;
// 			iUI::flush();
// 		}
// echo '</script>';
	}

	/**
	 * [发布]
	 * @return [type] [description]
	 */
	public function do_publish($work = null) {
		return spider::publish($work);
	}

	public function spider_url($work = NULL, $pid = NULL, $_rid = NULL, $_urls = NULL, $callback = NULL) {
		return spider_urls::crawl($work, $pid, $_rid, $_urls, $callback);
	}

	public function spider_content() {
		return spider_data::crawl();
	}

	/**
	 * [测试代理 [NOPRIV]]
	 * @return [type] [description]
	 */
	public function do_proxy_test() {
		$a = spider_tools::proxy_test();
		var_dump($a);
	}

}
