<?php
//版权所有(C) 2014 www.ilinei.com

namespace admin\model;

/**
 * 表与字段
 * @author sigmazel
 * @since v1.0.2
 */
class _table{
	//根据ID获取记录
	public function get_by_id($id){
		global $db;
		
		$table = $db->fetch_first("SELECT * FROM tbl_table WHERE TABLEID = '{$id}'");
		if($table){
			$table['COLUMNS'] = unserialize($table['COLUMNS']);
			$table['COLUMNS'] = is_array($table['COLUMNS']) ? $table['COLUMNS'] : array();
			
			$table['JOINS'] = unserialize($table['JOINS']);
			$table['JOINS'] = is_array($table['JOINS']) ? $table['JOINS'] : array();
		}
		
		return $table;
	}
	
	//根据标识号获取记录
	public function get_by_identity($identity){
		global $db;
		
		$table = $db->fetch_first("SELECT * FROM tbl_table WHERE `IDENTITY` = '{$identity}'");
		if($table){
			$table['COLUMNS'] = unserialize($table['COLUMNS']);
			$table['COLUMNS'] = is_array($table['COLUMNS']) ? $table['COLUMNS'] : array();
			
			$table['JOINS'] = unserialize($table['JOINS']);
			$table['JOINS'] = is_array($table['JOINS']) ? $table['JOINS'] : array();
		}
		
		return $table;
	}
	
	//获取数量 
	public function get_count($wheresql = ''){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_table WHERE 1 {$wheresql}") + 0;
	}
	
	//获取列表
	public function get_list($start, $perpage, $wheresql = ''){
		global $db;
		
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$rows = array();
	
		$temp_query = $db->query("SELECT * FROM tbl_table WHERE 1 {$wheresql} ORDER BY DISPLAYORDER ASC {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row['COLUMNS'] = unserialize($row['COLUMNS']);
			$row['COLUMNS'] = is_array($row['COLUMNS']) ? $row['COLUMNS'] : array();
			
			$rows[] = $row;
		}
	
		return $rows;
	}
	
	//获取关记录
	public function get_joins($exid = 0){
		global $db;
		
		$temparr = array();
		$temparr[] = array('IDENTITY' => 'user', 'CNAME' => '用户');
		$temparr[] = array('IDENTITY' => 'group', 'CNAME' => '用户等级');
		$temparr[] = array('IDENTITY' => 'category', 'CNAME' => '分类');
		$temparr[] = array('IDENTITY' => 'district', 'CNAME' => '地区');
		
		
		$temp_query = $db->query("SELECT IDENTITY, CNAME FROM tbl_table WHERE TABLEID <> '{$exid}' ORDER BY DISPLAYORDER ASC");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$temparr[] = $row;
		}
		
		return $temparr;
	}
	
	//根据所有记录
	public function get_all(){
		global $db;
		
		$temparr = array();
		
		$temp_query = $db->query("SELECT TABLEID, IDENTITY, CNAME FROM tbl_table ORDER BY DISPLAYORDER ASC");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$temparr[] = $row;
		}
		
		return $temparr;
	}
	
	//根据字段描述
	public function get_column_schema($table){
		$rtn_array = array('COLUMNS' => array(), 'JOINS' => array());
		
		if(!$table) return $rtn_array;
		
		$val_array = array(
		'COLUMNVAL' => '', 
		'DISPLAYORDERVAL' => '', 
		'WIDTHVAL' => '', 
		'DEFAULTVAL' => '', 
		'OPTIONSVAL' => '', 
		'SINGLEVAL' => '', 
		'AUDITVAL' => '', 
		'INLINEVAL' => '',
		'ENABLEVAL' => '',
		'MUSTVAL' => '',
		'CHECKED' => 0
		);
	
		foreach ($table['COLUMNS'] as $key => $val){
			$rtn_array['COLUMNS'][] = array_merge(array('IDENTITY' => $val['identity'], 'COLUMN' => $val['identity'], 'CNAME' => $val['name'], 'TYPE' => $val['type'], 'LENGTH' => $val['length']), $val_array);
		}
		
		foreach ($table['JOINS'] as $key => $val){
			$temp_table = array();
			
			if($key == 'user'){
				$rtn_array['JOINS']['user']  = array('IDENTITY' => 'user', 
				'CNAME' => '管理员', 
				'COLUMNS' => array(
					array_merge(array('IDENTITY' => 'USERNAME', 'COLUMN' => 'USERNAME', 'CNAME' => '管理员', 'TYPE' => 'varchar', 'LENGTH' => 30), $val_array),
					array_merge(array('IDENTITY' => 'EDITTIME', 'COLUMN' => 'EDITTIME', 'CNAME' => '编辑时间', 'TYPE' => 'datetime', 'LENGTH' => 0), $val_array)
				)
				);
			}elseif ($key == 'category') {
				$rtn_array['JOINS']['category']  = array('IDENTITY' => 'category', 
				'CNAME' => '分类', 
				'COLUMNS' => array(
					array_merge(array('IDENTITY' => 'CATEGORYID', 'COLUMN' => 'CATEGORYNAME', 'CNAME' => '分类名称', 'TYPE' => 'int', 'LENGTH' => 20), $val_array)
				)
				);
			}elseif ($key == 'district'){
				$rtn_array['JOINS']['district']  = array('IDENTITY' => 'district', 
				'CNAME' => '地区', 
				'COLUMNS' => array(
					array_merge(array('IDENTITY' => 'DISTRICTID', 'COLUMN' => 'DISTRICTNAME', 'CNAME' => '地区名称', 'TYPE' => 'int', 'LENGTH' => 20), $val_array)
				)
				);
			}else{
				$temp_table = $this->get_by_identity($key);
				if($temp_table){
					$rtn_array['JOINS'][$temp_table['IDENTITY']]  = array('IDENTITY' => $temp_table['IDENTITY'], 
					'CNAME' => $temp_table['CNAME'], 
					'COLUMNS' => array()
					);
					
					foreach ($temp_table['COLUMNS'] as $tkey => $tval){
						$rtn_array['JOINS'][$temp_table['IDENTITY']]['COLUMNS'][] = array_merge(array('IDENTITY' => $tval['identity'], 'COLUMN' => $tval['identity'], 'CNAME' => $tval['name'], 'TYPE' => $tval['type'], 'LENGTH' => $tval['length']), $val_array);
					}
				}
			}
			
			unset($temp_table);
		}
		
		return $rtn_array;
	}
	
	//获取字段提交值
	public function get_column_postvalue($column, $val){
		if($column['JOIN'] || $column['TYPE'] == 'tinyint' || $column['TYPE'] == 'int') return $val + 0;
		else{
			if($column['TYPE'] == 'datetime') return is_shortdate($val) ? $val : null;
			elseif ($column['TYPE'] == 'text') return $val;
			else return utf8substr($val, 0, $column['LENGTH']);
		}
	}
	
	//格式化字段描述
	public function format_column_description($column){
		$column_name = strtoupper($column['identity']);
		$column_type = strtoupper($column['type']);
		$column_length = $column['length'] + 0;
		$column_length = $column_length > 250 ? 250 : $column_length;
		
		$column_description = '';
		
		if($column_type == 'TINYINT') $column_description = "`{$column_name}` TINYINT(1) NOT NULL DEFAULT '0'";
		else if($column_type == 'DECIMAL') $column_description = "`{$column_name}` DECIMAL({$column_length},2) NOT NULL DEFAULT '0'";
		elseif ($column_type == 'INT') {
			$column_length = $column_length > 20 ? 20 : $column_length;
			$column_length = $column_length < 1 ? 1 : $column_length;
			if($column_length > 8) $column_description = "`{$column_name}` BIGINT({$column_length}) NOT NULL DEFAULT '0'";
			else  $column_description = "`{$column_name}` INT({$column_length}) NOT NULL DEFAULT '0'";
		}elseif ($column_type == 'VARCHAR') $column_description = "`{$column_name}` VARCHAR({$column_length}) NOT NULL DEFAULT ''";
		elseif ($column_type == 'DATETIME') $column_description = "`{$column_name}` DATETIME DEFAULT NULL";
		elseif ($column_type == 'TEXT') $column_description = "`{$column_name}` TEXT DEFAULT NULL";
		
		return $column_description;
	}
	
	//更新描述
	public function flash_schema($table, $columns, $joins){
		global $db, $config;
		
		$temp_config = $db->get_config();
		
		$config['host'] + 0 <= 0 && $config['host'] = 1;
		
		$dbname = $temp_config[$config['host']][dbname];
		
		$schema = $db->fetch_first("SELECT * FROM information_schema.tables WHERE TABLE_SCHEMA = '{$dbname}' AND TABLE_NAME = 'tbl_{$table[IDENTITY]}'");
		
		if($schema){
			foreach ($table['COLUMNS'] as $key => $val){
				$isfined = false;
				
				foreach ($columns as $ikey => $ival){
					if($ival['identity']  == $val['identity']){
						$isfined = true;
						$columns[$ikey]['exists'] = 1;
						
						if(!$ival['locked']){
							$db->query("ALTER TABLE `tbl_{$table[IDENTITY]}` CHANGE `".strtoupper($ival['identity'])."` ".$this->format_column_description($ival).";");
						}
						
						break;
					}
				}
				
				if($isfined == false) $db->query("ALTER TABLE `tbl_{$table[IDENTITY]}` DROP COLUMN `{$val[identity]}`;");
				
				unset($isfined);
			}
			
			foreach ($columns as $key => $val){
				if($val['exists'] + 0 < 1) $db->query("ALTER TABLE `tbl_{$table[IDENTITY]}` ADD COLUMN ".$this->format_column_description($val).";");
			}
			
			foreach ($table['JOINS'] as $key => $val){
				if($joins[$key]) continue;
				
				if($key == 'user'){
					$db->query("ALTER TABLE `tbl_{$table[IDENTITY]}` DROP COLUMN `USERID`;");
					$db->query("ALTER TABLE `tbl_{$table[IDENTITY]}` DROP COLUMN `USERNAME`;");
					$db->query("ALTER TABLE `tbl_{$table[IDENTITY]}` DROP COLUMN `EDITTIME`;");
				}elseif ($key == 'category') $db->query("ALTER TABLE `tbl_{$table[IDENTITY]}` DROP COLUMN `CATEGORYID`;");
				elseif ($key == 'district')$db->query("ALTER TABLE `tbl_{$table[IDENTITY]}` DROP COLUMN `DISTRICTID`;");
				else  $db->query("ALTER TABLE `tbl_{$table[IDENTITY]}` DROP COLUMN `".strtoupper($key)."ID`;"); 
			}
			
			foreach ($joins as $key => $val){
				if($table['JOINS'][$key]) continue;
				
				if($key == 'user'){
					$db->query("ALTER TABLE `tbl_{$table[IDENTITY]}` ADD COLUMN `USERID` BIGINT(20) NOT NULL DEFAULT '0';");
					$db->query("ALTER TABLE `tbl_{$table[IDENTITY]}` ADD COLUMN `USERNAME` VARCHAR(30) NOT NULL DEFAULT '';");
					$db->query("ALTER TABLE `tbl_{$table[IDENTITY]}` ADD COLUMN `EDITTIME` DATETIME DEFAULT NULL;");
				}elseif ($key == 'category') $db->query("ALTER TABLE `tbl_{$table[IDENTITY]}` ADD COLUMN `CATEGORYID` BIGINT(20) NOT NULL DEFAULT '0';");
				elseif ($key == 'district')$db->query("ALTER TABLE `tbl_{$table[IDENTITY]}` ADD COLUMN `DISTRICTID` BIGINT(20) NOT NULL DEFAULT '0';");
				else  $db->query("ALTER TABLE `tbl_{$table[IDENTITY]}` ADD COLUMN `".strtoupper($key)."ID` BIGINT(20) NOT NULL DEFAULT '0';"); 
			}
			
			if($table['FILENUM'] + 0 > $table['FILENUM_NEW'] + 0){
				for($i = $table['FILENUM_NEW'] + 1; $i <= $table['FILENUM']; $i++){
					$db->query("ALTER TABLE `tbl_{$table[IDENTITY]}` DROP COLUMN `FILE".sprintf('%02d', $i)."`;");
				}
			}elseif($table['FILENUM'] + 0 < $table['FILENUM_NEW'] + 0){
				for($i = $table['FILENUM'] + 1; $i <= $table['FILENUM_NEW'];$i++){
					$db->query("ALTER TABLE `tbl_{$table[IDENTITY]}` ADD COLUMN `FILE".sprintf('%02d', $i)."` VARCHAR(200) NOT NULL DEFAULT '';");
				}
			}
			
		}else{
			$create_sql_pre = "
	CREATE TABLE `tbl_".strtolower($table['IDENTITY'])."` (
	  `".strtoupper($table['IDENTITY'])."ID` bigint(20) NOT NULL auto_increment,";
			$create_sql_next ="
	  PRIMARY KEY  (`".strtoupper($table['IDENTITY'])."ID`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			
			$create_sql_ctx = "";
			
			foreach ($columns as $key => $val){
				$create_sql_ctx .= $this->format_column_description($val).",
	  ";
			}
			
			for($i = 1; $i <= $table['FILENUM']; $i++){
				$create_sql_ctx .= "`FILE".sprintf('%02d', $i)."` VARCHAR(200) NOT NULL DEFAULT '',";
			}
			
			foreach ($joins as $key => $val){
				if($key == 'user'){
					$create_sql_ctx .= "`USERID` BIGINT(20) NOT NULL DEFAULT '0',";
					$create_sql_ctx .= "`USERNAME` VARCHAR(30) NOT NULL DEFAULT '',";
					$create_sql_ctx .= "`EDITTIME` DATETIME DEFAULT NULL,";
				}elseif ($key == 'category') $create_sql_ctx .= "`CATEGORYID` BIGINT(20) NOT NULL DEFAULT '0',";
				elseif ($key == 'district') $create_sql_ctx .= "`DISTRICTID` BIGINT(20) NOT NULL DEFAULT '0',";
				else  $create_sql_ctx .= "`".strtoupper($key)."ID` BIGINT(20) NOT NULL DEFAULT '0',";
			}
			
			$db->query($create_sql_pre.$create_sql_ctx.$create_sql_next);
		}
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_table', $data);
		
		return $db->insert_id();
	}
	
	//修改
	public function update($id, $data){
		global $db;
		
		$db->update('tbl_table', $data, "TABLEID = '{$id}'");
	}
	
	//删除
	public function delete($id){
		global $db;
		
		$db->delete('tbl_table', "TABLEID = '{$id}'");
	}
	
	//删除物理表
	public function drop($identity){
		global $db;
		
		$db->query("DROP TABLE `tbl_{$identity}`;");
	}
	
}


function sort_column_array($a, $b){
  if ($a['displayorder'] == $b['displayorder']) return 0;
  return ($a['displayorder'] > $b['displayorder']) ? 1 : -1;
}
?>