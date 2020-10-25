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

class spider_ruleAdmincp {
	public function __construct() {
		spiderAdmincp::init($this);
	}
	public function do_batch() {
		list($idArray,$ids,$batch) = iUI::get_batch_args();
		switch ($batch) {
		case 'del':
			iDB::query("delete from `#iCMS@__spider_rule` where `id` IN($ids);");
		break;
		default:
			iUI::alert('参数错误!', 'js:1');
		}
		iUI::success('全部删除成功!', 'js:1');
	}
	/**
	 * [测试采集规则]
	 * @return [type] [description]
	 */
	public function do_test() {
		spider::$ruleTest = true;
		spider_urls::crawl('WEB@AUTO');
	}
	/**
	 * [采集规则管理]
	 * @return [type] [description]
	 */
	public function do_manage() {
		if ($_GET['keywords']) {
			$sql = " WHERE CONCAT(name,rule) REGEXP '{$_GET['keywords']}'";
		}
		list($orderby,$orderby_option) = get_orderby();
		$maxperpage = $_GET['perpage'] > 0 ? (int) $_GET['perpage'] : 20;
		$total = iPagination::totalCache( "SELECT count(*) FROM `#iCMS@__spider_rule` {$sql}", "G");
		iUI::pagenav($total, $maxperpage, "个规则");
		$rs = iDB::all("SELECT * FROM `#iCMS@__spider_rule` {$sql} order by {$orderby} LIMIT " . iPagination::$offset . " , {$maxperpage}");
		$_count = count($rs);
		include admincp::view("rule.manage");
	}

	/**
	 * [复制采集规则]
	 * @return [type] [description]
	 */
	public function do_copy() {
		iDB::query("
			INSERT INTO `#iCMS@__spider_rule` (`name`, `rule`)
			SELECT `name`, `rule` FROM `#iCMS@__spider_rule` WHERE id = '$this->rid'
		");
		$rid = iDB::$insert_id;
		iUI::success('复制完成,编辑此规则', 'url:' . APP_URI . '&do=add&rid=' . $rid);
	}
	/**
	 * [删除采集规则]
	 * @return [type] [description]
	 */
	public function do_del() {
		$this->rid OR iUI::alert("请选择要删除的项目");
		iDB::query("delete from `#iCMS@__spider_rule` where `id` = '$this->rid';");
		iUI::success('删除完成', 'js:1');
	}
	/**
	 * [添加采集规则]
	 * @return [type] [description]
	 */
	public function do_add() {
		$rs = array();
		$this->rid && $rs = spider_rule::get($this->rid);
		$rs['rule'] && $rule = $rs['rule'];
		if (empty($rule['data'])) {
			$rule['data'] = array(
				array('name' => 'title','empty' => true,
					'process'=>array(
						array('helper'=>'trim'),
						array('helper'=>'cleanhtml')
					)
				),
				array('name' => 'body','empty' => true,'page' => true, 'multi' => true,
					'process'=>array(
						array('helper'=>'format'),
						array('helper'=>'trim')
					)
				),
			);
		}else{
			// //兼容旧版
			// if(is_array($rule['data']))foreach ($rule['data'] as $key => $value) {
			// 	if(isset($value['process'])){
			// 		continue;
			// 	}
			// 	$rule['data'][$key]['process'] = self::old7014($value);
			// 	unset($rule['data'][$key]['cleanbefor'],$rule['data'][$key]['helper'],$rule['data'][$key]['cleanafter']);
			// }
			$rule['fs']['encoding']&& $rule['http']['ENCODING'] = $rule['fs']['encoding'];
			$rule['fs']['referer'] && $rule['http']['REFERER'] = $rule['fs']['referer'];
		}

		$rule['sort'] OR $rule['sort'] = 1;
		$rule['mode'] OR $rule['mode'] = 1;
		$rule['page_no_start'] OR $rule['page_no_start'] = 1;
		$rule['page_no_end'] OR $rule['page_no_end'] = 5;
		$rule['page_no_step'] OR $rule['page_no_step'] = 1;

		include admincp::view("rule.add");
	}
	/**
	 * [保存采集规则]
	 * @return [type] [description]
	 */
	public function do_save() {
		$id = (int) $_POST['id'];
		$name = iSecurity::escapeStr($_POST['name']);
		$rule = $_POST['rule'];

		empty($name) && iUI::alert('规则名称不能为空！');
		//empty($rule['list_area_rule']) 	&& iUI::alert('列表区域规则不能为空！');
		if ($rule['mode'] != '2') {
			empty($rule['list_url_rule']) && iUI::alert('列表链接规则不能为空！');
		}

		$rule = addslashes(json_encode($rule));
		$fields = array('name', 'rule');
		$data = compact($fields);
		if ($id) {
			iDB::update('spider_rule', $data, array('id' => $id));
			iUI::success('保存成功');
		} else {
            $id = iDB::insert('spider_rule',$data);
			iUI::success('保存成功!', 'url:' . APP_URI . "&do=add&rid=" . $id);
		}
	}
	/**
	 * [导出采集规则]
	 * @return [type] [description]
	 */
	public function do_export() {
		$row = iDB::row("
			SELECT `name`, `rule`
			FROM `#iCMS@__spider_rule`
			WHERE id = '$this->rid'"
		, ARRAY_A);
		$data = base64_encode(json_encode($row));
		Header("Content-type: application/octet-stream");
		Header("Content-Disposition: attachment; filename=spider.rule." . $row['name'] . '.txt');
        echo $data;
    }

	/**
	 * [导入采集规则]
	 * @return [type] [description]
	 */
	public function do_import() {
        files::$check_data        = false;
        files::$cloud_enable      = false;
        iFS::$config['allow_ext'] = 'txt';
		$F = iFS::upload('upfile','spider');
		$path = $F['RootPath'];
		if ($path) {
			$data = file_get_contents($path);
			@unlink($path);
			if ($data) {
				$data = base64_decode($data);
				$data = json_decode($data,true);
				$rule = json_decode($data['rule'],true);
				if($rule){
					$data['rule'] = json_encode($rule);
					$data = iSecurity::slashes($data);
					iDB::insert("spider_rule", $data);
				}else{
					iUI::alert('导入规则出现错误');
				}
			}
			iUI::success('规则导入完成', 'js:1');
		}
	}
}
