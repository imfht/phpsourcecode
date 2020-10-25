<?php
//版权所有(C) 2014 www.ilinei.com

namespace exam\control;

use admin\model\_log;
use admin\model\_table;
use exam\model\_exam;
use exam\model\_exam_user;
use exam\model\_exam_category;
use exam\model\_exam_award;
use exam\model\_exam_record;
use exam\model\_exam_option;
use ilinei\upload;
use ilinei\image;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/exam/lang.php';
//引入二维码库
require_once ROOTPATH.'/source/lib/QRcode.php';

//答题
class index{
	//默认
	public function index(){
		global $_var;
		
		$_log = new _log();
		$_exam = new _exam();
		$_exam_user = new _exam_user();
		$_exam_category = new _exam_category();
		
		$exam_status = $_exam->get_status();
		
		$search['nextquery'] = "&nextquery=|page,{$_var[page]}|psize,{$_var[psize]}";
		
		if($_var['gp_do'] == 'delete'){
			$exam = $_exam->get_by_id($_var['gp_id']);
			if($exam){
				$_exam->delete($exam['EXAMID']);
				
				$_log->insert($GLOBALS['lang']['exam.index.log.delete']."({$exam[TITLE]})", $GLOBALS['lang']['exam.index']);
			}
		}elseif($_var['gp_do'] == 'delete_list' && is_array($_var['gp_cbxItem'])){
			$exam_titles = '';
			
			foreach($_var['gp_cbxItem'] as $key => $val){
				$exam = $_exam->get_by_id($val);
				if($exam){
					$_exam->delete($exam['EXAMID']);
					
					$exam_titles .= $exam['TITLE'].'， ';
				}
				
				unset($exam);
			}
			
			if($exam_titles) $_log->insert($GLOBALS['lang']['exam.index.log.delete.list']."({$exam_titles})", $GLOBALS['lang']['exam.index']);
		}elseif($_var['gp_do'] == 'enable_list' && is_array($_var['gp_cbxItem'])){
			$exam_titles = '';
			
			foreach($_var['gp_cbxItem'] as $key => $val){
				$exam = $_exam->get_by_id($val);
				if($exam){
					$_exam->update($exam['EXAMID'], array('STATUS' => 1));
					
					$exam_titles .= $exam['TITLE'].'， ';
				}
				
				unset($exam);
			}
			
			if($exam_titles) $_log->insert($GLOBALS['lang']['exam.index.log.enable.list']."({$exam_titles})", $GLOBALS['lang']['exam.index']);
		}elseif($_var['gp_do'] == 'disable_list' && is_array($_var['gp_cbxItem'])){
			$exam_titles = '';
			
			foreach($_var['gp_cbxItem'] as $key => $val){
				$exam = $_exam->get_by_id($val);
				if($exam){
					$_exam->update($exam['EXAMID'], array('STATUS' => 0));
					
					$exam_titles .= $exam['TITLE'].'， ';
				}
				
				unset($exam);
			}
			
			if($exam_titles) $_log->insert($GLOBALS['lang']['exam.index.log.disable.list']."({$exam_titles})", $GLOBALS['lang']['exam.index']);
		}
		
		$exams = array();
		$examids = array();
		
		$count = $_exam->get_count($search['wheresql']);
		if($count){
			$perpage = $_var['psize'];
			$pages = @ceil($count / $perpage);
			$_var['page'] = $_var['page'] > $pages ? $pages : $_var['page'];
			$start = ($_var['page'] - 1) * $perpage;
			
			$exam_list = $_exam->get_list($start, $perpage, $search['wheresql']);
			foreach ($exam_list as $key => $exam){
				$exam['USER_COUNT'] = $_exam_user->get_count("AND a.EXAMID = '{$exam[EXAMID]}'");
				
				$exam['_STATUS'] = $exam['STATUS'];
				$exam['STATUS'] = $exam_status[$exam['STATUS']];
				
				$exam['CATEGORY'] = array();
				
				$exams[] = $exam;
				$examids[] = $exam['EXAMID'];
				
				unset($tempstat);
			}
			
			$pager = pager($count, $perpage, $_var['page'], "{ADMIN_SCRIPT}/exam", $perpage);
			
			$exam_categories = $_exam_category->get_list(0, 0, "AND a.EXAMID IN(".eimplode($examids).")");
			
			foreach($exam_categories as $key => $category){
				foreach($exams as $key => $exam){
					if($exam['EXAMID'] == $category['EXAMID']){
						$exams[$key]['CATEGORY'][] = $category;
					}
				}
			}
		}
		
		include_once view('/module/exam/view/index');
	}
	
	//添加
	public function _pub(){
		global $_var;
		
		$_log = new _log();
		
		$_exam = new _exam();
		$_exam_category = new _exam_category();
		$_exam_award = new _exam_award();
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			if(empty($_var['gp_txtIdentity'])) $_var['msg'] .= $GLOBALS['lang']['exam.index_edit.validate.identity']."<br/>";
			if(empty($_var['gp_txtTitle'])) $_var['msg'] .= $GLOBALS['lang']['exam.index_edit.validate.title']."<br/>";
			if(!empty($_var['gp_txtBeginDate']) && !is_datetime($_var['gp_txtBeginDate'])) $_var['msg'] .= $GLOBALS['lang']['exam.index_edit.validate.begindate']."<br/>";
			if(!empty($_var['gp_txtEndDate']) && !is_datetime($_var['gp_txtEndDate'])) $_var['msg'] .= $GLOBALS['lang']['exam.index_edit.validate.enddate']."<br/>";
			
			if(empty($_var['msg'])){
				$_var['gp_txtIdentity'] = utf8substr($_var['gp_txtIdentity'], 0, 20);
				$_var['gp_txtTitle'] = utf8substr($_var['gp_txtTitle'], 0, 50);
				$_var['gp_txtIdentity'] = utf8substr($_var['gp_txtIdentity'], 0, 30);
				$_var['gp_txtSummary'] = utf8substr($_var['gp_txtSummary'], 0, 200);
				
				$examid = $_exam->insert(array(
				'TITLE' => $_var['gp_txtTitle'], 
				'SUMMARY' => $_var['gp_txtSummary'], 
				'IDENTITY' => $_var['gp_txtIdentity'], 
				'RAND' => $_var['gp_rdoRand'] + 0,
				'GUEST' => $_var['gp_rdoGuest'] + 0, 
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
				
				foreach($_var['gp_newcname'] as $key => $val){
					if($_var['gp_newcname'][$key]){
						$_exam_category->insert(array(
						'EXAMID' => $examid, 
						'CNAME' => utf8substr($_var['gp_newcname'][$key], 0, 30),
						'DISPLAYORDER' => $_var['gp_newdisplayorder'][$key] + 0, 
						'NUM' => $_var['gp_newnum'][$key] + 0
						));
					}
				}
				
				foreach($_var['gp_newacname'] as $key => $val){
					if($_var['gp_newacname'][$key]){
						$_exam_award->insert(array(
						'EXAMID' => $examid, 
						'CNAME' => utf8substr($_var['gp_newacname'][$key], 0, 50),
						'DISPLAYORDER' => $_var['gp_newadisplayorder'][$key] + 0, 
						'NUM' => $_var['gp_newanum'][$key] + 0, 
						'FILE01' => utf8substr($_var['gp_newafile'][$key], 0, 200)
						));
					}
				}
				
				$_log->insert($GLOBALS['lang']['exam.index.log.add']."({$_var[gp_txtTitle]})", $GLOBALS['lang']['exam.index']);
				
				show_message($GLOBALS['lang']['exam.index.log.add'], "{ADMIN_SCRIPT}/exam");
			}
		}
		
		include_once view('/module/exam/view/index_edit');
	}
	
	//修改
	public function _update(){
		global $_var;
		
		$_log = new _log();
		
		$_exam = new _exam();
		$_exam_category = new _exam_category();
		$_exam_award = new _exam_award();
		
		$id = $_var['gp_id'] + 0;
		if($id == 0) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/exam");
		
		$exam = $_exam->get_by_id($id);
		if($exam == null) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/exam"); 
		
		$exam['BEGINTIME'] = $exam['BEGINTIME'] + 0 > 0 ? date('Y-m-d H:i', strtotime($exam['BEGINTIME'])) : '';
		$exam['ENDTIME'] = $exam['ENDTIME'] + 0 > 0 ? date('Y-m-d H:i', strtotime($exam['ENDTIME'])) : '';
		
		$exam = format_row_files($exam);
		
		$exam_awards = $_exam_award->get_all($exam['EXAMID']);
		$exam_categories = $_exam_category->get_list(0, 0, "AND a.EXAMID = '{$exam[EXAMID]}'");
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			if(empty($_var['gp_txtIdentity'])) $_var['msg'] .= $GLOBALS['lang']['exam.index_edit.validate.identity']."<br/>";
			if(empty($_var['gp_txtTitle'])) $_var['msg'] .= $GLOBALS['lang']['exam.index_edit.validate.title']."<br/>";
			if(!empty($_var['gp_txtBeginDate']) && !is_datetime($_var['gp_txtBeginDate'])) $_var['msg'] .= $GLOBALS['lang']['exam.index_edit.validate.begindate']."<br/>";
			if(!empty($_var['gp_txtEndDate']) && !is_datetime($_var['gp_txtEndDate'])) $_var['msg'] .= $GLOBALS['lang']['exam.index_edit.validate.enddate']."<br/>";
			
			if(empty($_var['msg'])){
				$_var['gp_txtIdentity'] = utf8substr($_var['gp_txtIdentity'], 0, 20);
				$_var['gp_txtTitle'] = utf8substr($_var['gp_txtTitle'], 0, 50);
				$_var['gp_txtIdentity'] = utf8substr($_var['gp_txtIdentity'], 0, 30);
				$_var['gp_txtSummary'] = utf8substr($_var['gp_txtSummary'], 0, 200);
				
				$_exam->update($exam['EXAMID'], array(
				'TITLE' => $_var['gp_txtTitle'], 
				'SUMMARY' => $_var['gp_txtSummary'], 
				'IDENTITY' => $_var['gp_txtIdentity'], 
				'RAND' => $_var['gp_rdoRand'] + 0, 
				'GUEST' => $_var['gp_rdoGuest'] + 0, 
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
				
				$_exam_category->update_batch("EXAMID = '{$exam[EXAMID]}'", array('LOCK' => 1));
				
				foreach($_var['gp_newcname'] as $key => $val){
					if($_var['gp_newcname'][$key]){
						$_exam_category->insert(array(
						'EXAMID' => $exam['EXAMID'], 
						'CNAME' => utf8substr($_var['gp_newcname'][$key], 0, 30),
						'DISPLAYORDER' => $_var['gp_newdisplayorder'][$key] + 0, 
						'NUM' => $_var['gp_newnum'][$key] + 0
						));
					}
				}
				
				foreach($_var['gp_cname'] as $key => $val){
					$_exam_category->update($key, array(
					'LOCK' => 0, 
					'CNAME' => utf8substr($_var['gp_cname'][$key], 0, 30),
					'DISPLAYORDER' => $_var['gp_displayorder'][$key] + 0, 
					'NUM' => $_var['gp_num'][$key] + 0
					));
				}
				
				$_exam_category->delete_batch("EXAMID = '{$exam[EXAMID]}' AND `LOCK` = 1");
				
				$_exam_award->update_batch("EXAMID = '{$exam[EXAMID]}'", array('LOCK' => 1));
				
				foreach($_var['gp_newacname'] as $key => $val){
					if($_var['gp_newacname'][$key]){
						$_exam_award->insert(array(
						'EXAMID' => $exam['EXAMID'], 
						'CNAME' => utf8substr($_var['gp_newacname'][$key], 0, 50), 
						'DISPLAYORDER' => $_var['gp_newadisplayorder'][$key] + 0, 
						'NUM' => $_var['gp_newanum'][$key] + 0, 
						'FILE01' => utf8substr($_var['gp_newafile'][$key], 0, 200)
						));
					}
				}
				
				foreach($_var['gp_acname'] as $key => $val){
					$_exam_award->update($key, array(
					'LOCK' => 0, 
					'CNAME' => utf8substr($_var['gp_acname'][$key], 0, 50),
					'DISPLAYORDER' => $_var['gp_adisplayorder'][$key] + 0, 
					'NUM' => $_var['gp_anum'][$key] + 0, 
					'FILE01' => utf8substr($_var['gp_afile'][$key], 0, 200)
					));
				}
				
				$_exam_award->delete_batch("EXAMID = '{$exam[EXAMID]}' AND `LOCK` = 1");
				
				$_log->insert($GLOBALS['lang']['exam.index.log.update']."({$_var[gp_txtTitle]})", $GLOBALS['lang']['exam.index']);
				
				show_message($GLOBALS['lang']['exam.index.log.update'], "{ADMIN_SCRIPT}/exam&page={$_var[page]}&psize={$_var[psize]}");
			}
		}
		
		include_once view('/module/exam/view/index_edit');
	}
	
	//二维码
	public function _qrcode(){
		global $_var, $setting;
		
		$_exam = new _exam();
		
		if(!$_var['gp_id']) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/exam");
		
		$exam = $_exam->get_by_identity($_var['gp_id']);
		if($exam == null) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/exam"); 
		
		\QRcode::png("{$setting[SiteHost]}mobile.do?ac=exam&id={$_var[gp_id]}", '', QR_ECLEVEL_H);
	}
	
	//题目
	public function _option(){
		global $_var;
		
		$_log = new _log();
		$_table = new _table();
		
		$_exam = new _exam();
		$_exam_category = new _exam_category();
		$_exam_option = new _exam_option();
		
		$id = $_var['gp_id'] + 0;
		if($id == 0) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/exam");
		
		$exam = $_exam->get_by_id($id);
		if($exam == null) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/exam"); 
		
		$table = $_table->get_by_identity('exam_option');
		$exam_categories = $_exam_category->get_list(0, 0, "AND a.EXAMID = '{$exam[EXAMID]}'");
		
		$search = $_exam_option->search();
		$search['prevquery'] = str_replace('|', '&', str_replace(',', '=', $_var['gp_nextquery']));
		
		if($_var['gp_do'] == 'pub'){
			if($_var['gp_formsubmit']){
				$_var['msg'] = '';
				
				if(empty($_var['gp_txtDisplayOrder'])) $_var['msg'] .= $GLOBALS['lang']['exam.index_option_edit.validate.displayorder']."<br/>";
				if(empty($_var['gp_txtTitle'])) $_var['msg'] .= $GLOBALS['lang']['exam.index_option_edit.validate.title']."<br/>";
				
				if(empty($_var['msg'])){
					$_var['gp_rdoReType'] = $_var['gp_rdoReType'] + 0;
					
					$remark = '';
					
					if($_var['gp_rdoReType'] == 1) $remark = $_var['gp_hdnFile01'];
					elseif($_var['gp_rdoReType'] == 2) $remark = $_var['gp_txtReVideo'];
					elseif($_var['gp_rdoReType'] == 3) $remark = $_var['gp_txtReAudio'];
					else $remark = $_var['gp_txtReText'];
					
					$option_items = array();
					foreach($_var['gp_newtitle'] as $key => $val){
						if($_var['gp_newdisplayorder'][$key]){
							$item = array();
							$item['ANSWER'] = $_var['gp_newanswer'][$key] + 0;
							$item['DISPLAYORDER'] = $_var['gp_newdisplayorder'][$key];
							$item['TITLE'] = utf8substr($_var['gp_newtitle'][$key], 0, 50);
							$item['FILE01'] = utf8substr($_var['gp_newfile'][$key], 0, 100);
							
							$option_items[] = $item;
						}
					}
					
					$_exam_option->insert(array(
					'EXAM_CATEGORYID' => $_var['gp_sltCategoryID'] + 0, 
					'DISPLAYORDER' => $_var['gp_txtDisplayOrder'] + 0, 
					'TITLE' => utf8substr($_var['gp_txtTitle'], 0, 100), 
					'RETYPE' => $_var['gp_rdoReType'] + 0, 
					'REMARK' => $remark,
					'FILE01' => count($option_items) > 0 ? $option_items[0]['ANSWER'].'|'.$option_items[0]['DISPLAYORDER'].'|'.$option_items[0]['TITLE'].'|'.$option_items[0]['FILE01'] : '', 
					'FILE02' => count($option_items) > 1 ? $option_items[1]['ANSWER'].'|'.$option_items[1]['DISPLAYORDER'].'|'.$option_items[1]['TITLE'].'|'.$option_items[1]['FILE01'] : '', 
					'FILE03' => count($option_items) > 2 ? $option_items[2]['ANSWER'].'|'.$option_items[2]['DISPLAYORDER'].'|'.$option_items[2]['TITLE'].'|'.$option_items[2]['FILE01'] : '', 
					'FILE04' => count($option_items) > 3 ? $option_items[3]['ANSWER'].'|'.$option_items[3]['DISPLAYORDER'].'|'.$option_items[3]['TITLE'].'|'.$option_items[3]['FILE01'] : '', 
					'FILE05' => count($option_items) > 4 ? $option_items[4]['ANSWER'].'|'.$option_items[4]['DISPLAYORDER'].'|'.$option_items[4]['TITLE'].'|'.$option_items[4]['FILE01'] : '', 
					'FILE06' => count($option_items) > 5 ? $option_items[5]['ANSWER'].'|'.$option_items[5]['DISPLAYORDER'].'|'.$option_items[5]['TITLE'].'|'.$option_items[5]['FILE01'] : '', 
					'EXAMID' => $exam['EXAMID'], 
					'USERID' => $_var['current']['USERID'],
					'USERNAME' => $_var['current']['USERNAME'], 
					'EDITTIME' => date('Y-m-d H:i:s')
					));
					
					$_log->insert($GLOBALS['lang']['exam.index_option.log.add']."({$_var[gp_txtTitle]})", $GLOBALS['lang']['exam.index']);
					
					show_message($GLOBALS['lang']['exam.index_option.log.add'], "{ADMIN_SCRIPT}/exam/_option&id={$_var[gp_id]}{$search[querystring]}");
				}
			}
			
			include_once view('/module/exam/view/index_option_edit');
			exit(0);
		}elseif($_var['gp_do'] == 'update'){
			$optionid = $_var['gp_optionid'] + 0;
			if($optionid == 0) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/exam/_option&id={$_var[gp_id]}&page={$_var[page]}&psize={$_var[psize]}{$search[querystring]}");
			
			$exam_option = $_exam_option->get_by_id($optionid);
			if($exam_option == null) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/exam/_option&id={$_var[gp_id]}&page={$_var[page]}&psize={$_var[psize]}{$search[querystring]}"); 
			
			if($_var['gp_formsubmit']){
				$_var['msg'] = '';
				
				if(empty($_var['gp_txtDisplayOrder'])) $_var['msg'] .= $GLOBALS['lang']['exam.index_option_edit.validate.displayorder']."<br/>";
				if(empty($_var['gp_txtTitle'])) $_var['msg'] .= $GLOBALS['lang']['exam.index_option_edit.validate.title']."<br/>";
				
				if(empty($_var['msg'])){
					$_var['gp_rdoReType'] = $_var['gp_rdoReType'] + 0;
					
					$remark = '';
					
					if($_var['gp_rdoReType'] == 1) $remark = $_var['gp_hdnFile01'];
					elseif($_var['gp_rdoReType'] == 2) $remark = $_var['gp_txtReVideo'];
					elseif($_var['gp_rdoReType'] == 3) $remark = $_var['gp_txtReAudio'];
					else $remark = $_var['gp_txtReText'];
					
					$option_items = array();
					foreach($_var['gp_title'] as $key => $val){
						if($_var['gp_displayorder'][$key]){
							$item = array();
							$item['ANSWER'] = $_var['gp_answer'][$key] + 0;
							$item['DISPLAYORDER'] = $_var['gp_displayorder'][$key];
							$item['TITLE'] = utf8substr($_var['gp_title'][$key], 0, 50);
							$item['FILE01'] = strpos($_var['gp_file'][$key], '|') === false ? $_var['gp_file'][$key] : substr($_var['gp_file'][$key], 0, strpos($_var['gp_file'][$key], '|'));
							
							$option_items[] = $item;
						}
					}
					
					foreach($_var['gp_newtitle'] as $key => $val){
						if($_var['gp_newdisplayorder'][$key]){
							$item = array();
							$item['ANSWER'] = $_var['gp_newanswer'][$key] + 0;
							$item['DISPLAYORDER'] = $_var['gp_newdisplayorder'][$key];
							$item['TITLE'] = utf8substr($_var['gp_newtitle'][$key], 0, 50);
							$item['FILE01'] = strpos($_var['gp_newfile'][$key], '|') === false ? $_var['gp_file'][$key] : substr($_var['gp_newfile'][$key], 0, strpos($_var['gp_newfile'][$key], '|'));
							
							$option_items[] = $item;
						}
					}
					
					usort($option_items, "exam_option_displayorder");
					
					$_exam_option->update($exam_option['EXAM_OPTIONID'], array(
					'EXAM_CATEGORYID' => $_var['gp_sltCategoryID'] + 0, 
					'DISPLAYORDER' => $_var['gp_txtDisplayOrder'] + 0, 
					'TITLE' => utf8substr($_var['gp_txtTitle'], 0, 100), 
					'RETYPE' => $_var['gp_rdoReType'] + 0, 
					'REMARK' => $remark, 
					'FILE01' => count($option_items) > 0 ? $option_items[0]['ANSWER'].'|'.$option_items[0]['DISPLAYORDER'].'|'.$option_items[0]['TITLE'].'|'.$option_items[0]['FILE01'] : '', 
					'FILE02' => count($option_items) > 1 ? $option_items[1]['ANSWER'].'|'.$option_items[1]['DISPLAYORDER'].'|'.$option_items[1]['TITLE'].'|'.$option_items[1]['FILE01'] : '', 
					'FILE03' => count($option_items) > 2 ? $option_items[2]['ANSWER'].'|'.$option_items[2]['DISPLAYORDER'].'|'.$option_items[2]['TITLE'].'|'.$option_items[2]['FILE01'] : '', 
					'FILE04' => count($option_items) > 3 ? $option_items[3]['ANSWER'].'|'.$option_items[3]['DISPLAYORDER'].'|'.$option_items[3]['TITLE'].'|'.$option_items[3]['FILE01'] : '', 
					'FILE05' => count($option_items) > 4 ? $option_items[4]['ANSWER'].'|'.$option_items[4]['DISPLAYORDER'].'|'.$option_items[4]['TITLE'].'|'.$option_items[4]['FILE01'] : '', 
					'FILE06' => count($option_items) > 5 ? $option_items[5]['ANSWER'].'|'.$option_items[5]['DISPLAYORDER'].'|'.$option_items[5]['TITLE'].'|'.$option_items[5]['FILE01'] : '', 
					'USERID' => $_var['current']['USERID'],
					'USERNAME' => $_var['current']['USERNAME'], 
					'EDITTIME' => date('Y-m-d H:i:s')
					));
					
					$_log->insert($GLOBALS['lang']['exam.index_option.log.update']."{$_var[gp_txtTitle]})", $GLOBALS['lang']['exam.index']);
					
					show_message($GLOBALS['lang']['exam.index_option.log.update'], "{ADMIN_SCRIPT}/exam/_option&id={$_var[gp_id]}&page={$_var[page]}&psize={$_var[psize]}{$search[querystring]}");
				}
			}
			
			include_once view('/module/exam/view/index_option_edit');
			exit(0);
		}elseif($_var['gp_do'] == 'delete'){
			$exam_option = $_exam_option->get_by_id($_var['gp_optionid']);
			if($exam_option){
				$_exam_option->delete($exam_option['EXAM_OPTIONID']);
				
				$_log->insert($GLOBALS['lang']['exam.index_option.log.delete']."({$exam_option[TITLE]})", $GLOBALS['lang']['exam.index']);
			}
		}elseif($_var['gp_do'] == 'delete_list' && is_array($_var['gp_cbxItem'])){
			$option_titles = '';
			
			foreach($_var['gp_cbxItem'] as $key => $val){
				$exam_option = $_exam_option->get_by_id($val);
				if($exam_option){
					$_exam_option->delete($exam_option['EXAM_OPTIONID']);
					
					$option_titles .= $exam_option['TITLE'].'， ';
				}
				
				unset($exam_option);
			}
			
			$_log->insert($GLOBALS['lang']['exam.index_option.log.delete.list']."({$option_titles})", $GLOBALS['lang']['exam.index']);
		}
		
		foreach($_var['gp_cbxItem'] as $key => $val){
			$exam_option = $_exam_option->get_by_id($val);
			if($exam_option){
				$_exam_option->update($exam_option['EXAM_OPTIONID'], array(
				'DISPLAYORDER' => $_var['gp_displayorder'][$exam_option['EXAM_OPTIONID']]
				));
			}
		}
		
		$count = $_exam_option->get_count("AND a.EXAMID = '{$exam[EXAMID]}' {$search[wheresql]}");
		if($count){
			$perpage = $_var['psize'];
			$pages = @ceil($count / $perpage);
			$_var['page'] = $_var['page'] > $pages ? $pages : $_var['page'];
			$start = ($_var['page'] - 1) * $perpage;
			
			$exam_options = $_exam_option->get_list($start, $perpage, "AND a.EXAMID = '{$exam[EXAMID]}' {$search[wheresql]}");
			
			$pager = pager($count, $perpage, $_var['page'], "{ADMIN_SCRIPT}/exam/_option&id={$_var[gp_id]}&nextquery={$_var[gp_nextquery]}{$search[querystring]}", $perpage);
		}
		
		include_once view('/module/exam/view/index_option');
	}
	
	//用户
	public function _user(){
		global $_var;
		
		$_exam = new _exam();
		$_exam_user = new _exam_user();
		$_exam_record = new _exam_record();
		
		$id = $_var['gp_id'] + 0;
		if($id == 0) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/exam");
		
		$exam = $_exam->get_by_id($id);
		if($exam == null) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/exam"); 
		
		$search = $_exam_user->search();
		
		$search['wheresql'] .= " AND a.EXAMID = '{$id}'";
		$search['prevquery'] = str_replace('|', '&', str_replace(',', '=', $_var['gp_nextquery']));
		
		if($_var['gp_do'] == 'delete'){
			$exam_userid = $_var['gp_exam_userid'] + 0;
			if($exam_userid == 0) exit_json_message($GLOBALS['lang']['error']);
			
			$exam_user = $_exam_user->get_by_id($exam_userid);
			if($exam_user == null) exit_json_message($GLOBALS['lang']['error']);
			
			$_exam_user->update($exam_userid, array('EXAM_AWARDID' => 0));
			
			exit_json_message(',', true);
		}elseif($_var['gp_do'] == 'clear'){
			$_exam_user->delete_batch("EXAMID = '{$exam[EXAMID]}'");
			$_exam_record->delete_batch("EXAMID = '{$exam[EXAMID]}'");
		}if($_var['gp_do'] == 'excel'){
			$exam_users = array();
			$exam_user_list = $_exam_user->get_list(0, 0, $search['wheresql']);
			
			foreach ($exam_user_list as $key => $exam_user){
				$exam_user['RECORD_COUNT'] = $_exam_record->get_count("AND a.EXAM_USERID = '{$exam_user[EXAM_USERID]}'");
				$exam_user['RECORD_RIGHT'] = $_exam_record->get_count("AND a.EXAM_USERID = '{$exam_user[EXAM_USERID]}' AND a.ISRIGHT = 1");
				
				$exam_users[] = $exam_user;
			}
			
			header("Content-type:application/vnd.ms-excel");
			header("Content-Disposition:attachment;filename=".$GLOBALS['lang']['exam.index_user.excel.title'].".xls");
			
			include_once view('/module/exam/view/index_excel');
			exit(0);
		}
		
		$exam_users = array();
		
		$count = $_exam_user->get_count($search['wheresql']);
		if($count){
			$perpage = $_var['psize'];
			$pages = @ceil($count / $perpage);
			$_var['page'] = $_var['page'] > $pages ? $pages : $_var['page'];
			$start = ($_var['page'] - 1) * $perpage;
			
			$exam_user_list = $_exam_user->get_list($start, $perpage, $search['wheresql']);
			foreach ($exam_user_list as $key => $exam_user){
				$exam_user['RECORD_COUNT'] = $_exam_record->get_count("AND a.EXAM_USERID = '{$exam_user[EXAM_USERID]}'");
				$exam_user['RECORD_RIGHT'] = $_exam_record->get_count("AND a.EXAM_USERID = '{$exam_user[EXAM_USERID]}' AND a.ISRIGHT = 1");
				
				$exam_users[] = $exam_user;
			}
			
			$pager = pager($count, $perpage, $_var['page'], "{ADMIN_SCRIPT}/exam/_user&id={$_var[gp_id]}&nextquery={$_var[gp_nextquery]}{$search[querystring]}", $perpage);
		}
		
		include_once view('/module/exam/view/index_user');
	}
	
	//抽奖
	public function _award(){
		global $_var;
		
		$_exam = new _exam();
		
		$_exam_user = new _exam_user();
		$_exam_award = new _exam_award();
		
		$id = $_var['gp_id'] + 0;
		if($id == 0) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/exam");
		
		$exam = $_exam->get_by_id($id);
		if($exam == null) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/exam"); 
		
		$over_count = $_exam_user->get_count("AND a.EXAMID = '{$exam[EXAMID]}' AND a.OVER = 1");
		$award_users = $_exam_user->get_list(0, 0, "AND a.EXAMID = '{$exam[EXAMID]}' AND a.EXAM_AWARDID > 0");
		
		foreach($award_users as $key => $award_user){
			is_mobile($award_user['MOBILE']) && $award_user['MOBILE'] = format_mobile_privacy($award_user['MOBILE']);
			
			!$award_user['MOBILE'] && $award_user['MOBILE'] = $award_user['REALNAME'];
			!$award_user['MOBILE'] && $award_user['MOBILE'] = $award_user['USERNAME'];
			
			$award_users[$key]['MOBILE'] = $award_user['MOBILE'];
		}
		
		if($_var['gp_do'] == 'rand'){
			if($over_count + 0 <= 0) exit_json_message($GLOBALS['lang']['exam.index_award.message.empty']);
			
			$exam_awards = $_exam_award->get_all($exam['EXAMID']);
			$exam_award = null;
			
			foreach($exam_awards as $key => $award){
				if($award['NUM'] > $award['COUNT']){
					$exam_award = $award;
					break;
				}
			}
			
			if(!$exam_award) exit_json_message($GLOBALS['lang']['exam.index_award.message.over']);
			
			$exam_users = $_exam_user->get_list(0, 1, "AND a.EXAMID = '{$exam[EXAMID]}' AND a.EXAM_AWARDID = 0 AND LENGTH(a.MOBILE) + LENGTH(a.REALNAME) + LENGTH(a.USERNAME) > 0", 'ORDER BY RAND()');
			if(count($exam_users) == 0) exit_json_message($GLOBALS['lang']['exam.index_award.message.over']);
			
			$exam_user = $exam_users[0];
			$_exam_user->update($exam_user['EXAM_USERID'], array('EDITTIME' => date('Y-m-d H:i:s'), 'EXAM_AWARDID' => $exam_award['EXAM_AWARDID']));
			
			is_mobile($_exam_user['MOBILE']) && $_exam_user['MOBILE'] = format_mobile_privacy($_exam_user['MOBILE']);
			
			!$_exam_user['MOBILE'] && $_exam_user['MOBILE'] = $_exam_user['REALNAME'];
			!$_exam_user['MOBILE'] && $_exam_user['MOBILE'] = $_exam_user['USERNAME'];
			
			$num = 1;
			$html = '';
			$html .= "<tr>";
			$html .= "<td>".(count($award_users) + 1)."</td>";
			$html .= "<td>{$exam_user[MOBILE]}</td>";
			$html .= "<td>{$exam_award[CNAME]}</td>";
			$html .= "</tr>";
			
			exit_json(array('num' => $num, 'html' => $html, 'success' => true));
		}
		
		$page_title = "{$exam[TITLE]}".$GLOBALS['lang']['exam.index_award.view.title'];
		
		include_once view('/module/exam/view/index_award');
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
			$upload->init($_FILES['Filedata'], 'mutual');

			if($upload->error()) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.error']);
			if(!$upload->attach['isimage']) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.image']);
			
			$upload->save();
			if($upload->error()) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.move']);
			
			if($upload->attach){
				$temp_img_size = getimagesize('attachment/'.$upload->attach['target']);
				
				exit_echo('FILEID:'.$upload->attach['target'].'|'.$upload->attach['name'].'|0|'.$temp_img_size[0].'|'.$temp_img_size[1].'|'.$_var['gp_file']);
			}
		}
		
		exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.fail']);
	}
	
}

function exam_option_displayorder($a, $b){
    if ($a['DISPLAYORDER'] == $b['DISPLAYORDER']) return ($a['ITEMID'] > $b['ITEMID']) ? 1 : 0;
    
    return ($a['DISPLAYORDER'] < $b['DISPLAYORDER']) ? -1 : 1;
}
?>