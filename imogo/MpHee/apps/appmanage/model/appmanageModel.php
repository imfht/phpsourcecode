<?php
class appmanageModel extends baseModel{
  
	//安装数据库
	public function installSql($sqlFile){
		$sqls = Install::mysql($sqlFile, config('DB_OLD_PREFIX'), config('DB_PREFIX') );
		if( empty($sqls) ) return false;
		foreach($sqls as $sql){
			$this->query($sql);
		}
	}
	
	//卸载数据库
	public function uninstallSql( $tables ){
		foreach($tables as $table){
			$table = '`' . config('DB_PREFIX') . trim($table) . '`';
			$this->query("DROP TABLE $table ");
		}
	}
	
	//导出sql语句
	public function exportSql( $tables ){
		$sql = '';
		foreach($tables as $table){
			$table = '`' . config('DB_PREFIX') . trim($table) . '`';
			$query = $this->db->query("SHOW CREATE TABLE $table");
			$row = $this->db->fetchArray($query);
			$row['Create Table'] = str_ireplace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $row['Create Table']);
			$sql .= str_replace(array("\n","\t"), "" , $row['Create Table']) . ";\n";
	
			$query = $this->db->query("SELECT * FROM $table");
			while ($row = $this->db->fetchArray($query)){
				$data['fields'] = array_keys($row);
				$data['values'] = $this->escape( array_values($row) );
				$sql .= "INSERT INTO {$table}(`" . implode("`,`", $data['fields']) . "`) VALUES (" . implode(",", $data['values']) . ");\n";
			}
		}
		return $sql;
	}
}