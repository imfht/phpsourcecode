<?php
/**
 *+------------------
 * SFDP-超级表单开发平台V3.0
 *+------------------
 * Copyright (c) 2018~2020 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */

namespace sfdp;

use think\Db;
use think\Exception;

class BuildTable{
	/**
     * 创建数据表
     */
    public function Btable($table,$data)
    {
		$int_config = int_config();
		if (in_array($table, $int_config['black_table'])) {
			return ['info'=>'该数据表不允许创建','code'=>1];
        }
        $tableName = $int_config['int_db_prefix'] . $table;
        $tableExist = false;// 判断表是否存在
        $ret = Db::query("SHOW TABLES LIKE '{$tableName}'");
		$this->hasDbbak($table);
        if ($ret && isset($ret[0])) {
            Db::execute("RENAME TABLE {$tableName} to {$tableName}_bak");
            $tableExist = true;
        }
        $auto_create_field = ['id', 'status', 'create_time', 'update_time'];
        // 强制建表和不存在原表执行建表操作
        $fieldAttr = [];
        $key = [];
        if (in_array('id', $auto_create_field)) {
            $fieldAttr[] = tab(1) . "`id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键'";
        }
        foreach ($data as $field) {
            if (!in_array($field['tpfd_db'], $auto_create_field)) {
				if($field['tpfd_dblx']=='datetime'||$field['tpfd_dblx']=='longtext'){
					$fieldAttr[] = tab(1) . "`{$field['tpfd_db']}` {$field['tpfd_dblx']} COMMENT '{$field['tpfd_name']}'";
					}else{
					$fieldAttr[] = tab(1) . "`{$field['tpfd_db']}` {$field['tpfd_dblx']}({$field['tpfd_dbcd']}) DEFAULT NULL COMMENT '{$field['tpfd_name']}'";
				}
            }
        }
		$fieldAttr[] = tab(1) . "`uid` int(10) DEFAULT '0' COMMENT '用户id'";
		$fieldAttr[] = tab(1) . "`status` int(10)  DEFAULT '0' COMMENT '审核状态'";
		$fieldAttr[] = tab(1) . "`create_time` int(10)  DEFAULT '0' COMMENT '新增时间'";
		$fieldAttr[] = tab(1) . "`update_time` int(10)  DEFAULT '0' COMMENT '更新时间'";
		$fieldAttr[] = tab(1) . "PRIMARY KEY (`id`)";
        $sql_drop = "DROP TABLE IF EXISTS `{$tableName}`";//删除数据表
        $sql_create = "CREATE TABLE `{$tableName}` (\n"
            . implode(",\n", array_merge($fieldAttr, $key))
            . "\n)ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT '{$table}'";
        try {
            Db::execute($sql_drop);
            Db::execute($sql_create);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
		return ['info'=>'创建成功！','code'=>0];
    }
	public function hasDbbak($table){
		$int_config = int_config();
        $tableName = $int_config['int_db_prefix'] . $table;
		$ret_bak = Db::query("SHOW TABLES LIKE '{$tableName}_bak'");
		if ($ret_bak && isset($ret_bak[0])) { 
			return ['code'=>1,'msg'=>'备份数据表已经存在，请先删除！'];
		}
	}
	public function delDbbak($table){
		$int_config = int_config();
        $tableName = $int_config['int_db_prefix'] . $table;
		$ret_bak = Db::query("SHOW TABLES LIKE '{$tableName}_bak'");
		if ($ret_bak && isset($ret_bak[0])) { 
			try {
				$ret = Db::execute("DROP TABLE IF EXISTS `{$tableName}_bak`");
			} catch (\Exception $e) {
				return ['code'=>1,'msg'=>'系统异常。'.$e->getMessage()];
			}
			return ['code'=>0,'msg'=>'备份表已经删除！'];
		}else{
			return ['code'=>1,'msg'=>'备份表不存在！'];
		}
		
	}
}