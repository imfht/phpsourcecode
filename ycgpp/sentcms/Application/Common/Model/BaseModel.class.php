<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace Common\Model;
use Think\Model;

class BaseModel extends Model{

	/**
	* 执行SQL文件
	* @access public
	* @param string  $file 要执行的sql文件路径
	* @param boolean $stop 遇错是否停止  默认为true
	* @param string  $db_charset 数据库编码 默认为utf-8
	* @return array
	*/
	public function executeSqlFile($file, $stop = true, $db_charset = 'utf-8'){
		$error = true;
		if (!is_readable($file)) {
			$error = array(
				'error_code' => 'SQL文件不可读',
				'error_sql' => '',
			);
			return $error;
		}

		$fp = fopen($file, 'rb');
		$sql = fread($fp, filesize($file));
		fclose($fp);

		$sql = str_replace("\r", "\n", str_replace('`' . 'sent_', '`' . $this->tablePrefix, $sql));

		foreach (explode(";\n", trim($sql)) as $query) {
			$query = trim($query);
			if ($query) {
				$res = $this->execute($query);
				if ($res === false) {
					$error[] = array(
						'error_code' => $this->getDbError(),
						'error_sql' => $query,
					);

					if ($stop){
						return $error;
					}
				}
			}
		}
		return $error;
	}
}