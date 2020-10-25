<?php
class SqlToolsClass
{
	//取得加上前缀表名
	static function getTableName($tableName,$tablePrifx=_TABLE_FIRST_NAME)
	{
		global $db;
		return $tablePrifx . $tableName;
	}
	
	//清空表
	static function Truncate($tableName)
	{
		$sql = "truncate table " . self::getTableName($tableName);
		return $sql;
	}
	
	static function SelectItem($tableName,$where = "",$column = "*" ,$join = null, $order = null , $group = null , $limit = null,$having = null)
	{
		//select * from wa_menu left join 表名 on 条件 where    order by   group by   limit 
		$tableName = self::getTableName($tableName);
		$sql = "select " . $column . " from " . $tableName;
		if ($join)
			$sql .= " " . $join;
		if ($where)
			$sql .= " where " . $where;	
		if ($group)
			$sql .= " group by " . $group;	
		if ($having)
			$sql .= " having " . $having;
		if ($order)
			$sql .= " order by " . $order;				
		if ($limit)
			$sql .= " limit " . $limit;		
		
		return $sql;
	}
	static function InsertData($tableName,$data)
	{
		$tableName = self::getTableName($tableName);

		foreach ($data as $key => $v)
		{
			$key=self::checkKey($key);
			if ($v != "" && !is_array($v))
			{
				$fields[] = $key;
				if(is_array($v)){
					$values[] = $v[0];
				}else{
					$values[] = "'" . $v . "'";
				}
				
			}elseif (is_null($v)){
				$fields[] = $key;
				$values[] = "null";
			}
		}
		$sql = "insert into " . $tableName . " (" . implode(",",$fields) . ")value";
		$sql .= "(" . implode(",",$values) . ")";
		return $sql;
	}

	
	static function InsertDatas($tableName,$datas)
	{
		$tableName = self::getTableName($tableName);
		foreach ($datas as $data)
		{
			foreach ($data as $key => $v)
			{
				$key=self::checkKey($key);
				if (is_null($v))
				{
					$fields[] = $key;
					$values[] = "null";
				}else{
					$fields[] = $key;
					$values[] = "'" . $v . "'";
				}
			}
			if (empty($fieldList))
				$fieldList = implode(",",$fields);
			$valueLists[] = "(" . implode(",",$values) . ")";
			unset($values);
		}
		$sql = "insert into " . $tableName . " (" . $fieldList . ")values";
		$sql .= implode(",",$valueLists);
		return $sql;
	}
	static function EditData($tableName,$data,$where)
	{
		$tableName = self::getTableName($tableName);
		foreach ($data as $key => $v)
		{
			$key=self::checkKey($key);
			if (is_null($v))
				$dataList[] = $key . "= NULL";
			else
				$dataList[] = $key . "= '" . $v . "'";
		}
		$sql = "update " . $tableName . " SET " . implode(",",$dataList) . " where " . $where;
		return $sql;
	}
	static function DeleteData($tableName,$where)
	{
		$tableName = self::getTableName($tableName);
		$sql = "delete from " . $tableName . " where " . $where;
		return $sql;
	}
	//Sql 关键词转义
	static function checkKey($key)
	{
		$datas=array("type","key","where","status","add","password","select","delete","execute","insert","call","do");
		if (in_array($key, $datas)) $key="`".$key."`";
		return $key;
	}
}
?>