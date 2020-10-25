<?php
class ConfigClass{
	static function getConfig(){
		global $db;
		
		$sql = SqlToolsClass::SelectItem("config");
		$rs = $db->GetRow($sql);
		return $rs;
	}
}
?>