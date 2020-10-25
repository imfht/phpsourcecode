<?php
/** 数据库更新程序 */
$config = config();
$database = database();
$dbconf = $config['mod']['database'];
$sqlite = $dbconf['type'] == 'sqlite'; //是否为 SQLite 数据库
if(!$dbconf['prefix'] && !$sqlite) return error(lang('mod.noDatabasePrefix'));
if(!$config['mod']['installed']){
	database::open(0) //连接数据库
			->set('type', $dbconf['type'])
			->set('host', $dbconf['host'])
			->set('dbname', $dbconf['name'])
			->set('port', $dbconf['port'])
			->set('prefix', $dbconf['prefix'])
			->login($dbconf['username'], $dbconf['password']);
	if($err = database::$error) return error($err);
}

//获取数据表
$tables = array();
$sql = $sqlite ? "select name from sqlite_master where type = 'table'" : 'SHOW TABLES';
$key = 'Tables_in_'.$dbconf['name'];
$result = database::query($sql);
while($result && $table = $result->fetchObject()){
	$name = $sqlite ? $table->name : $table->$key;
	if($sqlite && $name == 'sqlite_sequence') continue; //跳过 SQLite 信息表
	if(strpos($name, $dbconf['prefix']) === 0){
		$tables[] = $name;
	}
}

/** 删除多余数据表 */
foreach($tables as $table){
	$table = substr($table, strlen($dbconf['prefix']));
	if(!array_key_exists($table, $database)){ //删除多余数据表
		database::query("DROP TABLE IF EXISTS `{$dbconf['prefix']}{$table}`");
		if(isset($config[$table])) unset($config[$table]); //删除模块配置
		if(file_exists($file = __ROOT__.'user/classes/'.$table.'.class.php'))
			unlink($file); //删除模块类文件
	}
}

// 创建数据表
$createTable = function($table, $fields) use($sqlite){
	$sql = "CREATE TABLE `{$table}` (\n";
	foreach ($fields as $field => $attr) {
		if($sqlite) $attr = str_ireplace('AUTO_INCREMENT', 'AUTOINCREMENT', $attr);
		$sql .= "`{$field}` {$attr},\n";
	}
	$sql .= ")";
	if(!$sqlite) $sql .= ' ENGINE=InnoDB DEFAULT CHARSET=utf8';
	database::query(str_replace(",\n)", "\n)", $sql));
};

/** 新建或修改表 */
foreach($database as $table => $fields){
	if(!isset($config[$table])) $config[$table] = array();
	$table = $dbconf['prefix'].$table;
	if(in_array($table, $tables)){ //当数据表存在时更改数据表
		$cols = array();
		$sql = $sqlite ? "pragma table_info(`{$table}`)" : "SHOW COLUMNS FROM `{$table}`";
		$result = database::query($sql);
		while($result && $col = $result->fetchObject()){
			$cols[] = $sqlite ? $col->name : $col->Field; //获取表字段
		}
		database::query("ALTER TABLE `{$table}` RENAME TO `{$table}_old`"); //重命名旧表
		$createTable($table, $fields); //创建新表
		$sql = "INSERT INTO `{$table}` (";
		$_fields = '';
		foreach ($cols as $name) {
			if(array_key_exists($name, $fields)){
				$_fields .= "`{$name}`, ";
			}
		}
		$_fields = rtrim($_fields, ', ');
		$sql .= $_fields.') SELECT '.$_fields.' FROM '.$table.'_old';
		database::query($sql); //导入数据
		database::query("DROP TABLE `{$table}_old`"); //删除旧表
	}else{ //当数据表不存在时创建数据表
		$createTable($table, $fields);
	}
}
$config['mod']['installed'] = true;
config($config);
export(config(), __ROOT__.'user/config/config.php'); //更新配置文件