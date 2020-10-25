<?php
//版权所有(C) 2014 www.ilinei.com

namespace poll\control;

use admin\model\_log;
use poll\model\_poll;
use poll\model\_poll_option;
use poll\model\_poll_award;
use poll\model\_poll_vote;
use ilinei\upload;
use ilinei\image;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/poll/lang.php';
//引入二维码库
require_once ROOTPATH.'/source/lib/QRcode.php';

//答题
class index{
	//默认
	public function index(){
		global $_var;
		
		$_log = new _log();
		$_poll = new _poll();
		$_poll_option = new _poll_option();
		$_poll_vote = new _poll_vote();
		
		$poll_status = $_poll->get_status();
		
		$search['nextquery'] = "&nextquery=|page,{$_var[page]}|psize,{$_var[psize]}";
	
		if($_var['gp_do'] == 'delete'){
			$poll = $_poll->get_by_id($_var['gp_id']);
			if($poll){
				$_poll->delete($poll['POLLID']);
				
				$_log->insert($GLOBALS['lang']['poll.index.log.delete']."({$poll[TITLE]})", $GLOBALS['lang']['poll.index']);
			}
		}elseif($_var['gp_do'] == 'delete_list' && is_array($_var['gp_cbxItem'])){
			$poll_titles = '';
			
			foreach ($_var['gp_cbxItem'] as $key => $val){
				$poll = $_poll->get_by_id($val);
				if($poll){
					$_poll->delete($poll['POLLID']);
					
					$poll_titles .= $poll['TITLE'].'， ';
				}
				
				unset($poll);
			}
			
			if($poll_titles) $_log->insert($GLOBALS['lang']['poll.index.log.delete.list']."({$poll_titles})", $GLOBALS['lang']['poll.index']);
		}elseif($_var['gp_do'] == 'enable_list' && is_array($_var['gp_cbxItem'])){
			$poll_titles = '';
			
			foreach ($_var['gp_cbxItem'] as $key => $val){
				$poll = $_poll->get_by_id($val);
				if($poll){
					$_poll->update($poll['POLLID'], array('STATUS' => 1));
					
					$poll_titles .= $poll['TITLE'].'， ';
				}
				
				unset($poll);
			}
			
			if($poll_titles) $_log->insert($GLOBALS['lang']['poll.index.log.enable.list']."({$poll_titles})", $GLOBALS['lang']['poll.index']);
		}elseif($_var['gp_do'] == 'disable_list' && is_array($_var['gp_cbxItem'])){
			$poll_titles = '';
			
			foreach ($_var['gp_cbxItem'] as $key => $val){
				$poll = $_poll->get_by_id($val);
				if($poll){
					$_poll->update($poll['POLLID'], array('STATUS' => 0));
					
					$poll_titles .= $poll['TITLE'].'， ';
				}
				
				unset($poll);
			}
			
			if($poll_titles) $_log->insert($GLOBALS['lang']['poll.index.log.disable.list']."({$poll_titles})", $GLOBALS['lang']['poll.index']);
		}
		
		$polls = array();
		$count = $_poll->get_count($search['wheresql']);
		if($count){
			$perpage = $_var['psize'];
			$pages = @ceil($count / $perpage);
			$_var['page'] = $_var['page'] > $pages ? $pages : $_var['page'];
			$start = ($_var['page'] - 1) * $perpage;
			
			$poll_list = $_poll->get_list($start, $perpage, $search['wheresql']);
			foreach ($poll_list as $key => $poll){
				$poll['_STATUS'] = $poll['STATUS'];
				
				$poll['STATUS'] = $poll_status[$poll['STATUS']];
				$poll['OPTIONS'] = $_poll_option->get_count("AND a.POLLID = '{$poll[POLLID]}'");
				$poll['VOTES'] = $_poll_option->get_count("AND a.POLLID = '{$poll[POLLID]}'");
				
				$polls[] = $poll;
			}
			
			$pager = pager($count, $perpage, $_var['page'], "{ADMIN_SCRIPT}/poll", $perpage);
		}
		
		include_once view('/module/poll/view/index');
	}
	
	//添加
	public function _pub(){
		global $_var;
		
		$_log = new _log();
		$_poll = new _poll();
		$_poll_option = new _poll_option();
		$_poll_award = new _poll_award();
		
		$poll['SIMPLE'] = 1;
	
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			if(empty($_var['gp_txtIdentity'])) $_var['msg'] .= $GLOBALS['lang']['poll.index_edit.validate.identity']."<br/>";
			if(empty($_var['gp_txtTitle'])) $_var['msg'] .= $GLOBALS['lang']['poll.index_edit.validate.title']."<br/>";
			if(!empty($_var['gp_txtBeginDate']) && !is_datetime($_var['gp_txtBeginDate'])) $_var['msg'] .= $GLOBALS['lang']['poll.index_edit.validate.begindate']."<br/>";
			if(!empty($_var['gp_txtEndDate']) && !is_datetime($_var['gp_txtEndDate'])) $_var['msg'] .= $GLOBALS['lang']['poll.index_edit.validate.enddate']."<br/>";
			
			if(empty($_var['msg'])){
				$_var['gp_txtIdentity'] = utf8substr($_var['gp_txtIdentity'], 0, 20);
				$_var['gp_txtTitle'] = utf8substr($_var['gp_txtTitle'], 0, 50);
				$_var['gp_txtIdentity'] = utf8substr($_var['gp_txtIdentity'], 0, 30);
				
				$summary = utf8substr($_var['gp_txtContent'], 0, 200);
				$summary = $summary ? strip2words($summary) : '';
				
				$pollid = $_poll->insert(array(
				'TITLE' => $_var['gp_txtTitle'], 
				'IDENTITY' => $_var['gp_txtIdentity'], 
				'SUMMARY' => $summary, 
				'CONTENT' => $_var['gp_txtContent'], 
				'GUEST' => $_var['gp_rdoGuest'] + 0,
				'OPTIONTYPE' => $_var['gp_rdoOptionType'] + 0, 
				'SIMPLE' => $_var['gp_rdoSimple'] + 0, 
				'LIMITNUM' => $_var['gp_txtLimitNum'] + 0, 
				'ISAWARD' => $_var['gp_rdoIsAward'] + 0, 
				'BEGINTIME' => is_datetime($_var['gp_txtBeginDate']) ? $_var['gp_txtBeginDate'] : '', 
				'ENDTIME' => is_datetime($_var['gp_txtEndDate']) ? $_var['gp_txtEndDate'] : '', 
				'USERID' => $_var['current']['USERID'],
				'USERNAME' => $_var['current']['USERNAME'], 
				'EDITTIME' => date('Y-m-d H:i:s'), 
				'FILE01' => $_var['gp_hdnFile01'], 
				'FILE02' => $_var['gp_hdnFile02']
				));
				
				foreach ($_var['gp_newoptiontitle'] as $key => $val){
					if($_var['gp_newoptiontitle'][$key]){
						preg_match('/\{VOTES\:(\d*)\}/', $_var['gp_optionsummary'][$key], $matchs);
						
						$_poll_option->insert(array(
						'POLLID' => $pollid, 
						'TITLE' => utf8substr($_var['gp_newoptiontitle'][$key], 0, 50), 
						'SUMMARY' => utf8substr($_var['gp_newoptionsummary'][$key], 0, 50), 
						'DISPLAYORDER' => $_var['gp_newoptiondisplayorder'][$key] + 0, 
						'USERID' => $_var['current']['USERID'],
						'USERNAME' => $_var['current']['USERNAME'],
						'EDITTIME' => date('Y-m-d H:i:s'), 
						'STATUS' => 1, 
						'VOTES' => $matchs[1] + 0, 
						'FILE01' => utf8substr($_var['gp_newoptionfile'][$key], 0, 200)
						));
					}
					
					unset($matchs);
				}
				
				foreach ($_var['gp_newcname'] as $key => $val){
					if($_var['gp_newcname'][$key]){
						$_poll_award->insert(array(
						'POLLID' => $pollid, 
						'CNAME' => utf8substr($_var['gp_newcname'][$key], 0, 50),
						'DISPLAYORDER' => $_var['gp_newdisplayorder'][$key] + 0, 
						'NUM' => $_var['gp_newnum'][$key] + 0, 
						'FILE01' => utf8substr($_var['gp_newfile'][$key], 0, 200)
						));
					}
				}
				
				$_log->insert($GLOBALS['lang']['poll.index.log.add']."({$_var[gp_txtTitle]})", $GLOBALS['lang']['poll.index']);
				
				show_message($GLOBALS['lang']['poll.index.message.add'], "{ADMIN_SCRIPT}/poll");
			}
		}
		
		include_once view('/module/poll/view/index_edit');
	}
	
	//修改
	public function _update(){
		global $_var;
		
		$_log = new _log();
		$_poll = new _poll();
		$_poll_option = new _poll_option();
		$_poll_award = new _poll_award();
		
		$id = $_var['gp_id'] + 0;
		if($id == 0) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/poll");
		
		$poll = $_poll->get_by_id($id);
		if($poll == null) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/poll"); 
		
		$poll['BEGINDATE'] = $poll['BEGINTIME'] + 0 > 0 ? date('Y-m-d H:i', strtotime($poll['BEGINTIME'])) : '';
		$poll['ENDDATE'] = $poll['ENDTIME'] + 0 > 0 ? date('Y-m-d H:i', strtotime($poll['ENDTIME'])) : '';
		
		!$poll['CONTENT'] && $poll['CONTENT'] = $poll['SUMMARY'];
		
		$poll = format_row_files($poll);
		
		$poll_options = $_poll_option->get_list(0, 0, "AND a.POLLID = '{$id}'");
		foreach($poll_options as $key => $poll_option){
			$poll_options[$key]['SUMMARY'] = $poll_option['_SUMMARY'];
		}
		
		$poll_awards = $_poll_award->get_list(0, 0, "AND a.POLLID = '{$poll[POLLID]}'");
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			if(empty($_var['gp_txtIdentity'])) $_var['msg'] .= $GLOBALS['lang']['poll.index_edit.validate.identity']."<br/>";
			if(empty($_var['gp_txtTitle'])) $_var['msg'] .= $GLOBALS['lang']['poll.index_edit.validate.title']."<br/>";
			if(!empty($_var['gp_txtBeginDate']) && !is_datetime($_var['gp_txtBeginDate'])) $_var['msg'] .= $GLOBALS['lang']['poll_edit.validate.begindate']."<br/>";
			if(!empty($_var['gp_txtEndDate']) && !is_datetime($_var['gp_txtEndDate'])) $_var['msg'] .= $GLOBALS['lang']['poll_edit.validate.enddate']."<br/>";
			
			if(empty($_var['msg'])){
				$_var['gp_txtIdentity'] = utf8substr($_var['gp_txtIdentity'], 0, 20);
				$_var['gp_txtTitle'] = utf8substr($_var['gp_txtTitle'], 0, 50);
				$_var['gp_txtIdentity'] = utf8substr($_var['gp_txtIdentity'], 0, 30);
				
				$summary = utf8substr($_var['gp_txtContent'], 0, 200);
				$summary = $summary ? strip2words($summary) : '';
				
				$_poll->update($poll['POLLID'], array(
				'TITLE' => $_var['gp_txtTitle'], 
				'IDENTITY' => $_var['gp_txtIdentity'], 
				'SUMMARY' => $summary, 
				'CONTENT' => $_var['gp_txtContent'], 
				'GUEST' => $_var['gp_rdoGuest'] + 0,
				'OPTIONTYPE' => $_var['gp_rdoOptionType'] + 0, 
				'SIMPLE' => $_var['gp_rdoSimple'] + 0, 
				'LIMITNUM' => $_var['gp_txtLimitNum'] + 0, 
				'ISAWARD' => $_var['gp_rdoIsAward'] + 0, 
				'BEGINTIME' => is_datetime($_var['gp_txtBeginDate']) ? $_var['gp_txtBeginDate'] : '', 
				'ENDTIME' => is_datetime($_var['gp_txtEndDate']) ? $_var['gp_txtEndDate'] : '', 
				'USERID' => $_var['current']['USERID'],
				'USERNAME' => $_var['current']['USERNAME'], 
				'EDITTIME' => date('Y-m-d H:i:s'), 
				'FILE01' => $_var['gp_hdnFile01'], 
				'FILE02' => $_var['gp_hdnFile02']
				));
				
				$_poll_option->update_batch("POLLID = '{$poll[POLLID]}'", array('STATUS' => 2));
				
				foreach ($_var['gp_newoptiontitle'] as $key => $val){
					if($_var['gp_newoptiontitle'][$key]){
						preg_match('/\{VOTES\:(\d*)\}/', $_var['gp_optionsummary'][$key], $matchs);
						
						$_poll_option->insert(array(
						'POLLID' => $poll[POLLID], 
						'TITLE' => utf8substr($_var['gp_newoptiontitle'][$key], 0, 50), 
						'SUMMARY' => utf8substr($_var['gp_newoptionsummary'][$key], 0, 30), 
						'DISPLAYORDER' => $_var['gp_newoptiondisplayorder'][$key] + 0, 
						'USERID' => $_var['current']['USERID'],
						'USERNAME' => $_var['current']['USERNAME'],
						'EDITTIME' => date('Y-m-d H:i:s'), 
						'STATUS' => 1, 
						'VOTES' => $matchs[1] + 0, 
						'FILE01' => utf8substr($_var['gp_newoptionfile'][$key], 0, 200)
						));
					}
					
					unset($matchs);
				}
				
				foreach ($_var['gp_optiontitle'] as $key => $val){
					if($_var['gp_optiontitle'][$key]){
						preg_match('/\{VOTES\:(\d*)\}/', $_var['gp_optionsummary'][$key], $matchs);
						
						$_poll_option->update($key, array(
						'POLLID' => $poll[POLLID], 
						'TITLE' => utf8substr($_var['gp_optiontitle'][$key], 0, 50), 
						'SUMMARY' => utf8substr($_var['gp_optionsummary'][$key], 0, 50), 
						'DISPLAYORDER' => $_var['gp_optiondisplayorder'][$key] + 0, 
						'USERID' => $_var['current']['USERID'],
						'USERNAME' => $_var['current']['USERNAME'],
						'EDITTIME' => date('Y-m-d H:i:s'), 
						'STATUS' => 1, 
						'VOTES' => $matchs[1] + 0, 
						'FILE01' => utf8substr($_var['gp_optionfile'][$key], 0, 200)
						));
					}
					
					unset($matchs);
				}
				
				$_poll_option->delete_batch("POLLID = '{$poll[POLLID]}' AND `STATUS` = 2");
				
				$_poll_award->update_batch("POLLID = '{$poll[POLLID]}'", array('LOCK' => 1));
				
				foreach ($_var['gp_newcname'] as $key => $val){
					if($_var['gp_newcname'][$key]){
						$_poll_award->insert(array(
						'POLLID' => $poll['POLLID'], 
						'CNAME' => utf8substr($_var['gp_newcname'][$key], 0, 50),
						'DISPLAYORDER' => $_var['gp_newdisplayorder'][$key] + 0, 
						'NUM' => $_var['gp_newnum'][$key] + 0, 
						'FILE01' => utf8substr($_var['gp_newfile'][$key], 0, 200)
						));
					}
				}
				
				foreach ($_var['gp_cname'] as $key => $val){
					$_poll_award->update($key, array(
					'LOCK' => 0, 
					'CNAME' => utf8substr($_var['gp_cname'][$key], 0, 50),
					'DISPLAYORDER' => $_var['gp_displayorder'][$key] + 0, 
					'NUM' => $_var['gp_num'][$key] + 0, 
					'FILE01' => utf8substr($_var['gp_file'][$key], 0, 200)
					));
				}
				
				$_poll_award->delete_batch("POLLID = '{$poll[POLLID]}' AND `LOCK` = 1");
				
				$_log->insert($GLOBALS['lang']['poll.index.log.update']."({$_var[gp_txtTitle]})", $GLOBALS['lang']['poll.index']);
				
				show_message($GLOBALS['lang']['poll.index.message.update'], "{ADMIN_SCRIPT}/poll&page={$_var[page]}&psize={$_var[psize]}");
			}
		}
		
		include_once view('/module/poll/view/index_edit');
	}
	
	//二维码
	public function _qrcode(){
		global $_var, $setting;
		
		$_poll = new _poll();
		
		if(!$_var['gp_id']) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/poll");
		
		$poll = $_poll->get_by_identity($_var['gp_id']);
		if($poll == null) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/poll"); 
		
		\QRcode::png("{$setting[SiteHost]}mobile.do?ac=poll&id={$poll[IDENTITY]}", '', QR_ECLEVEL_H);
	}
	
	//结果
	public function _result(){
		global $_var;
		
		$_poll = new _poll();
		$_poll_option = new _poll_option();
		$_poll_vote = new _poll_vote();
		
		$id = $_var['gp_id'] + 0;
		if($id == 0) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/poll");
		
		$poll = $_poll->get_by_id($id);
		if($poll == null) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/poll"); 
		
		$search['prevquery'] = str_replace('|', '&', str_replace(',', '=', $_var['gp_nextquery']));
		
		$poll_options = $_poll_option->get_list(0, 0, "AND a.POLLID = '{$id}'");
		$poll_options = $_poll_option->get_result($poll_options, true);
		
		usort($poll_options, "poll_result_displayorder");
		
		include_once view('/module/poll/view/index_result');
	}
	
	//投票记录
	public function _vote(){
		global $_var;
		
		$_poll = new _poll();
		$_poll_option = new _poll_option();
		$_poll_vote = new _poll_vote();
		
		$id = $_var['gp_id'] + 0;
		if($id == 0) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/poll");
		
		$poll = $_poll->get_by_id($id);
		if($poll == null) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/poll"); 
		
		$poll_options = $_poll_option->get_list(0, 0, "AND a.POLLID = '{$id}'");
		
		$search = $_poll_vote->search();
		
		$search['wheresql'] .= " AND a.POLLID = '{$id}'";
		$search['prevquery'] = str_replace('|', '&', str_replace(',', '=', $_var['gp_nextquery']));
		
		if($_var['gp_do'] == 'delete'){
			$voteid = $_var['gp_voteid'] + 0;
			if($voteid == 0) exit_json_message($GLOBALS['lang']['error']);
			
			$poll_vote = $_poll_vote->get_by_id($voteid);
			if($poll_vote == null) exit_json_message($GLOBALS['lang']['error']);
			
			$_poll_vote->update($voteid, array('POLL_AWARDID' => 0));
			
			exit_json_message(',', true);
		}elseif($_var['gp_do'] == 'clear'){
			$_poll_vote->delete_batch("POLLID = '{$id}'");
		}
		
		$votes = array();
		
		$count = $_poll_vote->get_count($search['wheresql']);
		if($count) {
			$perpage = $_var['psize'];
			$pages = @ceil($count / $perpage);
			$_var['page'] = $_var['page'] > $pages ? $pages : $_var['page'];
			$start = ($_var['page'] - 1) * $perpage;
			
			$vote_list = $_poll_vote->get_list($start, $perpage, $search['wheresql']);
			foreach ($vote_list as $key => $vote){
				foreach($poll_options as $key => $poll_option){
					$vote['VAL'] = str_replace("|{$poll_option[POLL_OPTIONID]}:", "{$poll_option[TITLE]}；", $vote['VAL']);
				}
				
				$votes[] = $vote;
			}
			
			$pager = pager($count, $perpage, $_var['page'], "{ADMIN_SCRIPT}/poll/_vote&id={$_var[gp_id]}&nextquery={$_var[gp_nextquery]}{$search[querystring]}", $perpage);
		}
		
		include_once view('/module/poll/view/index_vote');
	}
	
	//导出excel
	public function _excel(){
		global $_var;
		
		$_poll = new _poll();
		$_poll_option = new _poll_option();
		$_poll_vote = new _poll_vote();
		
		$id = $_var['gp_id'] + 0;
		if($id == 0) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/poll");
		
		$poll = $_poll->get_by_id($id);
		if($poll == null) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/poll");
		
		$poll_options = $_poll_option->get_list(0, 0, "AND a.POLLID = '{$id}'");
		
		$search = $_poll_vote->search();
		
		$search['wheresql'] .= " AND a.POLLID = '{$id}'";
		
		$votes = array();
		$vote_list = $_poll_vote->get_list(0, 0, $search['wheresql']);
		
		foreach ($vote_list as $key => $vote){
			foreach($poll_options as $key => $poll_option){
				$vote['VAL'] = str_replace("|{$poll_option[POLL_OPTIONID]}:", "{$poll_option[TITLE]}；", $vote['VAL']);
			}
			
			$votes[] = $vote;
		}
		
		header("Content-type:application/vnd.ms-excel");
		header("Content-Disposition:attachment;filename=".$GLOBALS['lang']['poll.index_vote.excel.file'].".xls");
		
		include_once view('/module/poll/view/index_excel');
	}
	
	//抽奖
	public function _award(){
		global $_var;
		
		$_poll = new _poll();
		$_poll_award = new _poll_award();
		$_poll_vote = new _poll_vote();
		
		$id = $_var['gp_id'] + 0;
		if($id == 0) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/poll");
		
		$poll = $_poll->get_by_id($id);
		if($poll == null) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/poll");
		
		$vote_count = $_poll_vote->get_count("AND a.POLLID = '{$poll[POLLID]}'");
		$award_votes = $_poll_vote->get_list(0, 0, "AND a.POLLID = '{$poll[POLLID]}' AND a.POLL_AWARDID > 0");
		
		foreach($award_votes as $key => $award_vote){
			is_mobile($award_vote['MOBILE']) && $award_vote['MOBILE'] = format_mobile_privacy($award_vote['MOBILE']);
			
			!$award_vote['MOBILE'] && $award_vote['MOBILE'] = $award_vote['REALNAME'];
			!$award_vote['MOBILE'] && $award_vote['MOBILE'] = $award_vote['USERNAME'];
			
			$award_votes[$key]['MOBILE'] = $award_vote['MOBILE'];
		}
		
		if($_var['gp_do'] == 'rand'){
			if($vote_count + 0 <= 0) exit_json_message($GLOBALS['lang']['poll.index_award.message.empty']);
			
			$poll_awards = $_poll_award->get_all($poll['POLLID']);
			$poll_award = null;
			
			foreach($poll_awards as $key => $award){
				if($award['NUM'] > $award['COUNT']){
					$poll_award = $award;
					break;
				}
			}
			
			if(!$poll_award) exit_json_message($GLOBALS['lang']['poll.index_award.message.over']);
			
			$poll_votes = $_poll_vote->get_list(0, 1, "AND a.POLLID = '{$poll[POLLID]}' AND a.POLL_AWARDID = 0 AND LENGTH(a.MOBILE) + LENGTH(a.REALNAME) + LENGTH(a.USERNAME) > 0", 'ORDER BY RAND()');
			if(count($poll_votes) == 0) exit_json_message($GLOBALS['lang']['poll.index_award.message.over']);
			
			$poll_vote = $poll_votes[0];
			$_poll_vote->update($poll_vote['POLL_VOTEID'], array('EDITTIME' => date('Y-m-d H:i:s'), 'POLL_AWARDID' => $poll_award['POLL_AWARDID']));
			
			is_mobile($poll_vote['MOBILE']) && $poll_vote['MOBILE'] = format_mobile_privacy($poll_vote['MOBILE']);
			
			!$poll_vote['MOBILE'] && $poll_vote['MOBILE'] = $poll_vote['REALNAME'];
			!$poll_vote['MOBILE'] && $poll_vote['MOBILE'] = $poll_vote['USERNAME'];
			
			$num = 1;
			$html = '';
			$html .= "<tr>";
			$html .= "<td>".(count($award_votes) + 1)."</td>";
			$html .= "<td>{$poll_vote[MOBILE]}</td>";
			$html .= "<td>{$poll_award[CNAME]}</td>";
			$html .= "</tr>";
			
			exit_json(array('num' => $num, 'html' => $html, 'success' => true));
		}
		
		$page_title = "{$poll[TITLE]}".$GLOBALS['lang']['poll.index_award.view.title'];
		
		include_once view('/module/poll/view/index_award');
	}
	
	//上传图片
	public function _upload(){
		global $_var;
		
		if(!$_var['current']) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.login']);
	
		$file_limit = $_var['gp_limit'] + 0;
		$file_uploaded = $_var['gp_uploaded'] + 0;
		
		if($file_limit > 0 && $file_limit < $file_uploaded + 1) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.limit']."{$file_limit}".$GLOBALS['lang']['admin.validate.swfupload.echo.limit.pic']);
		
		if($_FILES['Filedata']['name']){
			$upload = new upload();
			$cimage = new image();
			
			$upload->init($_FILES['Filedata'], 'mutual');

			if($upload->error()) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.error']);
			if(!$upload->attach['isimage']) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.image']);
			
			$upload->save();
			if($upload->error()) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.move']);
			
			if($upload->attach){
				$temp_imgs_ize = getimagesize('attachment/'.$upload->attach['target']);
				$thumb = thumb_image($cimage, $upload->attach['target'], array('ImageWidth' => 320, 'ImageHeight' => 200));
				
				exit_echo('FILEID:'.$upload->attach['target'].'|'.$upload->attach['name'].'|'.$thumb.'|'.$temp_imgs_ize[0].'|'.$temp_imgs_ize[1].'|'.$_var['gp_file']);
			}
		}
		
		exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.fail']);
	}
}

function poll_result_displayorder($a, $b){
   if ($a['VOTECOUNT'] == $b['VOTECOUNT']) return ($a['DISPLAYORDER'] > $b['DISPLAYORDER']) ? 1 : 0;
    
    return ($a['VOTECOUNT'] < $b['VOTECOUNT']) ? 1 : 0;
}
?>