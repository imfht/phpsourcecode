<?php
//版权所有(C) 2014 www.ilinei.com

namespace admin\control;

use admin\model\_log;
use admin\model\_table;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/admin/lang.php';

//表与字段
class table{
	//默认
	public function index(){
		global $_var;
		
		$_log = new _log();
		$_table = new _table();
		
		if(is_array($_var['gp_cname'])){
			foreach ($_var['gp_cname'] as $key => $val){
				$_table->update($key, array('CNAME' => $val, 'DISPLAYORDER' => $_var['gp_displayorder'][$key]));
			}
		}
		
		if($_var['gp_do'] == 'delete' && $_var['gp_id'] > 0){
			$table = $_table->get_by_id($_var['gp_id']);
			if($table){
				$_table->delete($table['TABLEID']);
				$_table->drop($table['IDENTITY']);
				
				$_log->insert($GLOBALS['lang']['admin.table.log.delete']."({$table[CNAME]})", $GLOBALS['lang']['admin.table']);
			}
		}elseif($_var['gp_do'] == 'delete_list' && is_array($_var['gp_cbxItem'])){
			$tablenames = '';
			foreach ($_var['gp_cbxItem'] as $key => $val){
				$table = $_table->get_by_id($val);
				if(!$table) continue;
				
				$_table->delete($table['TABLEID']);
				$_table->drop($table['IDENTITY']);
				
				$tablenames .= $table['CNAME'].', ';
				
				unset($table);
			}
			
			$_log->insert($GLOBALS['lang']['admin.table.log.delete.list']."({$tablenames})", $GLOBALS['lang']['admin.table']);
		}
		
		$count = $_table->get_count();
		if($count){
			$perpage = $_var['psize'];
			$pages = @ceil($count / $perpage);
			$_var['page'] = $_var['page'] > $pages ? $pages : $_var['page'];
			$start = ($_var['page'] - 1) * $perpage;
			
			$tables = $_table->get_list($start, $perpage);
			
			$pager = pager($count, $perpage, $_var['page'], "{ADMIN_SCRIPT}/admin/table", $perpage);
		}
		
		include_once view('/module/admin/view/table');
	}
	
	//添加
	public function _add(){
		global $_var;
		
		$_log = new _log();
		$_table = new _table();
		
		$join_tables = $_table->get_joins(0);
	
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			if(empty($_var['gp_txtDisplayOrder'])) $_var['msg'] .= $GLOBALS['lang']['admin.table_edit.validate.no']."<br/>";
			if(empty($_var['gp_txtCName'])) $_var['msg'] .=  $GLOBALS['lang']['admin.table_edit.validate.cname']."<br/>";
			if(empty($_var['gp_txtIdentity'])) $_var['msg'] .=  $GLOBALS['lang']['admin.table_edit.validate.identity']."<br/>";
			
			if(empty($_var['msg'])){
				$table = $_table->get_by_identity($_var['gp_txtIdentity']);
				if($table) $_var['msg'] .=  $GLOBALS['lang']['admin.table_edit.validate.identity.exists']."<br/>";
				else{
					$columns = array();
					
					if(is_array($_var['gp_identity'])){
						foreach ($_var['gp_identity'] as $key => $val){
							$temp_identity = utf8substr($_var['gp_identity'][$key], 0, 25);
							if($temp_identity){
								$columns[strtoupper($temp_identity)] = array(
								'identity' => strtoupper($temp_identity),
								'name' => utf8substr($_var['gp_name'][$key], 0, 15),
								'type' => $_var['gp_type'][$key],
								'length' => $_var['gp_length'][$key] + 0,
								'displayorder' => $_var['gp_displayorder'][$key] + 0, 
								'exists' => 0, 
								'locked' => 0
								);
							}
							
							unset($temp_identity);
						}
					}
					
					if($columns) usort($columns, sort_column_array);
					
					$joins = array();
					foreach ($_var['gp_jointables'] as $key => $val){
						$joins[$key] = $_var['gp_jointables_prex'][$key] ? $_var['gp_jointables_prex'][$key] : '_';
					}
					
					$insertid = $_table->insert(array(
					'CNAME' => utf8substr($_var['gp_txtCName'], 0, 30),
					'IDENTITY' => strtolower(utf8substr($_var['gp_txtIdentity'], 0, 50)),
					'REMARK' => utf8substr($_var['gp_txtRemark'], 0, 100),
					'DISPLAYORDER' => $_var['gp_txtDisplayOrder'] + 0,
					'COLUMNS' => serialize($columns), 
					'JOINS' => serialize($joins), 
					'FILENUM' => $_var['gp_txtFileNum'] + 0,
					'USERID' => $_var['current']['USERID'],
					'USERNAME' => $_var['current']['USERNAME'],
					'EDITTIME' => date('Y-m-d H:i:s')
					));
					
					$table = $_table->get_by_id($insertid);
					$table['FILENUM_NEW'] = $_var['gp_txtFileNum'] + 0;
					
					$_table->flash_schema($table, $columns, $joins);
					
					$_log->insert($GLOBALS['lang']['admin.table.log.add']."({$_var[gp_txtCName]})", $GLOBALS['lang']['admin.table']);
					
					show_message($GLOBALS['lang']['admin.table.message.add'], "{ADMIN_SCRIPT}/admin/table");
				}
			}
		}
		
		include_once view('/module/admin/view/table_edit');
	}
	
	//修改
	public function _update(){
		global $_var;
		
		$_log = new _log();
		$_table = new _table();
		
		$id = $_var['gp_id'] + 0;
		if($id == 0) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/admin/table");
		
		$table = $_table->get_by_id($id);
		if($table == null) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/admin/table"); 
		
		$join_tables = $_table->get_joins(0);
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			if(empty($_var['gp_txtDisplayOrder'])) $_var['msg'] .= $GLOBALS['lang']['admin.table_edit.validate.no']."<br/>";
			if(empty($_var['gp_txtCName'])) $_var['msg'] .=  $GLOBALS['lang']['admin.table_edit.validate.cname']."<br/>";
			if(empty($_var['gp_txtIdentity'])) $_var['msg'] .=  $GLOBALS['lang']['admin.table_edit.validate.identity']."<br/>";
			
			if(empty($_var['msg'])){
				$exists_table = $_table->get_by_identity($_var['gp_txtIdentity']);
				if($exists_table && $exists_table['TABLEID'] != $id) $_var['msg'] .= $GLOBALS['lang']['admin.table_edit.validate.identity.exists']."<br/>";
				else{
					$table_identity = strtolower(utf8substr($_var['gp_txtIdentity'], 0, 50));
					
					if($table['IDENTITY'] != $table_identity) $_table->drop($table['IDENTITY']);
					
					$columns = array();
					
					if(is_array($_var['gp_identity'])){
						foreach ($_var['gp_identity'] as $key => $val){
							$temp_identity = utf8substr($_var['gp_identity'][$key], 0, 25);
							if($temp_identity){
								$columns[strtoupper($temp_identity)] = array(
								'identity' => strtoupper($temp_identity),
								'name' => utf8substr($_var['gp_name'][$key], 0, 15),
								'type' => $_var['gp_type'][$key],
								'length' => $_var['gp_length'][$key] + 0,
								'displayorder' => $_var['gp_displayorder'][$key] + 0, 
								'exists' => 0, 
								'locked' => $_var['gp_locked'][$key] + 0
								);
							}
							
							unset($temp_identity);
						}
					}
					
					if($columns) usort($columns, sort_column_array);
					
					$joins = array();
					foreach ($_var['gp_jointables'] as $key => $val){
						$joins[$key] = $_var['gp_jointables_prex'][$key] ? $_var['gp_jointables_prex'][$key] : '_';
					}
					
					$_table->update($id, array(
					'CNAME' => utf8substr($_var['gp_txtCName'], 0, 30),
					'IDENTITY' => $table_identity,
					'REMARK' => utf8substr($_var['gp_txtRemark'], 0, 100),
					'DISPLAYORDER' => $_var['gp_txtDisplayOrder'] + 0,
					'COLUMNS' => serialize($columns), 
					'JOINS' => serialize($joins), 
					'FILENUM' => $_var['gp_txtFileNum'] + 0,
					'USERID' => $_var['current']['USERID'],
					'USERNAME' => $_var['current']['USERNAME'],
					'EDITTIME' => date('Y-m-d H:i:s')
					));
					
					
					$table['IDENTITY'] = $table_identity;
					$table['FILENUM_NEW'] = $_var['gp_txtFileNum'] + 0;
					
					$_table->flash_schema($table, $columns, $joins);
					
					$_log->insert($GLOBALS['lang']['admin.table.log.update']."({$_var[gp_txtCName]})", $GLOBALS['lang']['admin.table']);
					
					show_message($GLOBALS['lang']['admin.table.message.update'], "{ADMIN_SCRIPT}/admin/table&page={$_var[page]}&psize={$_var[psize]}");
				}
			}
		}
		
		include_once view('/module/admin/view/table_edit');
	}
	
	//导出结构doc
	public function _export(){
		$_table = new _table();
		
		header("Content-type:application/vnd.ms-word;");
		header("Content-Disposition:attachment;filename=table_scheme.doc"); 
		
		$table_scheme = array();
		$table_list = $_table->get_list(0, 0);
		
		foreach ($table_list as $key => $table){
			$table['JOINS'] = unserialize($table['JOINS']);
			$table['JOINS'] = is_array($table['JOINS']) ? $table['JOINS'] : array();
			
			if($table['FILENUM']) {
				$temp_files = range(1, $table['FILENUM']);
				$files = array();
				
				foreach($temp_files as $val) $files[] = 'FILE'.sprintf('%02d', $val);
				
				$table['FILES'] = $files;
			}
			
			$join_tables = $_table->get_joins($table['TABLEID']);
			
			$table_scheme[] = array('table' => $table, 'join' => $join_tables);
			
			unset($files);
			unset($temp_files);
			unset($join_tables);
		}
		
		include_once view('/module/admin/view/table_scheme');
	}
	
	//生成代码
	public function _code(){
	    global $_var, $dispatches, $setting;

        $_table = new _table();

		if($_var['gp_formsubmit']){
			$tableids = array();
			
			$_var['gp_cbxItem'] = explode(',', $_var['gp_cbxItem']);
			foreach ($_var['gp_cbxItem'] as $key => $val){
				if(!$val) continue;
				$tableids[] = $val;
			}
			
			$table_list = array();
			$table_array = $_table->get_list(0, 0, "AND TABLEID IN(".eimplode($tableids).")");
			
			foreach ($table_array as $key => $table){
				$table['JOINS'] = unserialize($table['JOINS']);
				$table['JOINS'] = is_array($table['JOINS']) ? $table['JOINS'] : array();
				
				if($table['FILENUM']) {
					$temp_files = range(1, $table['FILENUM']);
					$files = array();
					
					foreach($temp_files as $val) $files[] = 'FILE'.sprintf('%02d', $val);
					
					$table['FILES'] = $files;
				}
				
				$table_list[] = $table;
				
				unset($files);
				unset($temp_files);
			}
			
			if(empty($dispatches['operations']['export'])){
				$path = ROOTPATH."/{$setting[SiteTheme]}/module/";
				if(!is_dir($path)){
					$res = @mkdir($path, 0755, true);
					@chown($path, 'apache');
				}

                $themes = explode('/', $setting['SiteTheme']);
				$_namespace = $themes[0].'\\'.$themes[1];

				if($_var['gp_rdoCodeFilter']){
					$code = '';
					include ROOTPATH.'/module/admin/view/table_code_filter.htm';
					file_put_contents($path.'/filter.php', "<?php\r\n{$code}\r\n?>");
				}

				foreach ($table_list as $key => $table){
					$files = array(
					'module' => 'ext',
					'control' => '',
					'model' => '',
					'view' => array(
						'index' => '',
						'index_edit' => '',
						'index_detail' => ''
						)
					);

                    $table['_NAMESPACE'] = $_namespace.'\\module\\'.$table['IDENTITY'];
                    $table['_PATH'] = "/{$table[IDENTITY]}";

                    $files_path = $path."/{$table[IDENTITY]}";
                    if(!is_dir($files_path)){
                        $res = @mkdir($files_path, 0755, true);
                        @chown($files_path, 'apache');
                    }

                    if(!is_dir($files_path.'/control')){
                        $res = @mkdir($files_path.'/control', 0755, true);
                        @chown($files_path.'/control', 'apache');
                    }

                    if(!is_dir($files_path.'/model')){
                        $res = @mkdir($files_path.'/model', 0755, true);
                        @chown($files_path.'/model', 'apache');
                    }

                    if(!is_dir($files_path.'/view')){
                        $res = @mkdir($files_path.'/view', 0755, true);
                        @chown($files_path.'/view', 'apache');
                    }

                    $files['model'] = $files_path."/model/_{$table[IDENTITY]}.php";
                    $files['control'] = $files_path."/control/index.php";
                    $files['view']['index'] = $files_path."/view/index.htm";
                    $files['view']['index_edit'] = $files_path."/view/index_edit.htm";
                    $files['view']['index_detail'] = $files_path."/view/index_detail.htm";

					include ROOTPATH.'/module/admin/view/table_code_init.htm';
					
					$code = '';
					include ROOTPATH.'/module/admin/view/table_code_model.htm';
					file_put_contents($files['model'], "<?php\r\n{$code}\r\n?>");
					
					$code = '';
					include ROOTPATH.'/module/admin/view/table_code_index.htm';
					file_put_contents($files['control'], "<?php\r\n{$code}\r\n?>");
					
					$code = '';
					include ROOTPATH.'/module/admin/view/table_code_view_index.htm';
					file_put_contents($files['view']['index'], $code);
					
					$code = '';
					include ROOTPATH.'/module/admin/view/table_code_view_index_edit.htm';
					file_put_contents($files['view']['index_edit'], $code);
					
					$code = '';
					include ROOTPATH.'/module/admin/view/table_code_view_index_detail.htm';
					file_put_contents($files['view']['index_detail'], $code);
					
					unset($files);
					unset($files_path);
					unset($code);
				}

				show_message($GLOBALS['lang']['admin.table.message.code'], "{ADMIN_SCRIPT}/admin/table&page={$_var[page]}&psize={$_var[psize]}");
			}
		}
		
		include_once view('/module/admin/view/table_code');
	}
	
}
?>