<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: colin <colin@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace com;

use think\facade\Config;
use think\facade\Env;
use think\facade\Db;

/**
 * 数据库管理类
 * @author colin <colin@tensent.cn>
 */
class Datatable {

	protected $table; /*数据库操作的表*/
	protected $fields             = []; /*数据库操作字段*/
	protected $charset            = 'utf8'; /*数据库操作字符集*/
	public $prefix                = ''; /*数据库操作表前缀*/
	protected $model_table_prefix = ''; /*模型默认创建的表前缀*/
	protected $engine_type        = 'MyISAM'; /*数据库引擎*/
	protected $key                = 'id'; /*数据库主键*/
	public $sql                   = ''; /*最后生成的sql语句*/
	protected $typeAlist          = [
		"text"     => "VARCHAR",
		"string"   => "VARCHAR",
		"password" => "VARCHAR",
		"textarea" => "TEXT",
		"bool"     => "INT",
		"select"   => "INT",
		"num"      => "INT",
		"decimal"   => "DECIMAL",
		"tags"     => "VARCHAR",
		"datetime" => "INT",
		"date"     => "INT",
		"editor"   => "TEXT",
		"bind"     => "INT",
		"image"    => "INT",
		"images"   => "VARCHAR",
		"attach"   => "VARCHAR",
		"fieldlist" => "text"
	];

	/**
	 * 初始化数据库信息
	 * @author colin <colin@tensent.cn>
	 */
	public function __construct() {
		//创建DB对象
		$this->prefix             = Env::get('database.prefix');
		$this->model_table_prefix = Config::get('model_table_prefix') ? Config::get('model_table_prefix') : '';
	}

	/**
	 * @title       初始化表
	 * @description 初始化创建表
	 * @Author      molong
	 * @DateTime    2017-06-11
	 * @param       string        $table 表名
	 * @return      void               空
	 */
	public function initTable($table = '', $comment = '', $pk = 'id') {
		$this->table = $this->getTablename($table, true);

		$sql = $this->generateField($pk, 'int', 11, '', '主键', true);

		$primary = $pk ? "PRIMARY KEY (`" . $pk . "`)" : '';
		$generatesql = $sql . ',';

		$create = "CREATE TABLE IF NOT EXISTS `" . $this->table . "`("
		. $generatesql
		. $primary
		. ") ENGINE=" . $this->engine_type . " AUTO_INCREMENT=1 DEFAULT CHARSET=" . $this->charset . " ROW_FORMAT=DYNAMIC COMMENT='" . $comment . "';";
		$this->sql = $create;
		return $this;
	}

	/**
	 * 快速创建ID字段
	 * @var length 字段的长度
	 * @var comment 字段的描述
	 * @author colin <colin@tensent.cn>
	 */
	public function generateField($key = '', $type = '', $length = 11, $default = '', $comment = '主键', $is_auto_increment = false) {
		if ($key && $type) {
			$auto_increment = $is_auto_increment ? 'AUTO_INCREMENT' : '';
			$field_type     = $length ? $type . '(' . $length . ')' : $type;
			$signed         = in_array($type, array('int', 'float', 'double')) ? 'signed' : '';
			$comment        = $comment ? "COMMENT '" . $comment . "'" : "";
			$default        = $default ? "DEFAULT '" . $default . "'" : "";
			$sql            = "`{$key}` {$field_type} {$signed} NOT NULL {$default} $auto_increment {$comment}";
		}
		return $sql;
	}

	/**
	 * 追加字段
	 * @var $table 追加字段的表名
	 * @var $attr 属性列表
	 * @var $is_more 是否为多条同时插入
	 * @author colin <colin@tensent.cn>
	 */
	public function columField($table, $attr = array()) {
		$field_attr['table'] = $table ? $this->getTablename($table, true) : $this->table;
		$field_attr['name'] = $attr['name'];
		$field_attr['type']  = ($attr['type'] && isset($this->typeAlist[$attr['type']])) ? $this->typeAlist[$attr['type']] : 'varchar';
		if (intval($attr['length']) && $attr['length']) {
			$field_attr['length'] = "(" . $attr['length'] . ")";
		} else {
			$field_attr['length'] = "";
		}
		$field_attr['is_null'] = (isset($attr['is_must']) && $attr['is_must']) ? 'NOT NULL' : 'NULL';
		if(isset($attr['type']) && in_array($attr['type'], ['textarea', 'editor', 'fieldlist', 'text'])){
			$field_attr['default'] = "";
		}else{
			$field_attr['default'] = (isset($attr['is_must']) && $attr['is_must'] !== '') ? 'DEFAULT "' . $attr['value'] . '"' : '';
		}

		$field_attr['comment'] = (isset($attr['remark']) && $attr['remark']) ? $attr['remark'] : $attr['title'];
		$field_attr['after']   = (isset($attr['after']) && $attr['after']) ? ' AFTER `' . $attr['after'] . '`' : ' AFTER `id`';
		$field_attr['action']  = (isset($attr['action']) && $attr['action']) ? $attr['action'] : 'ADD';
		//确认表是否存在

		if ($field_attr['action'] == 'ADD') {
			$this->sql = "ALTER TABLE `{$field_attr['table']}` ADD `{$field_attr['name']}` {$field_attr['type']}{$field_attr['length']} {$field_attr['is_null']} {$field_attr['default']} COMMENT '{$field_attr['comment']}' {$field_attr['after']}";
		} elseif ($field_attr['action'] == 'CHANGE') {
			$field_attr['oldname'] = (isset($attr['oldname']) && $attr['oldname']) ? $attr['oldname'] : $attr['name'];

			$this->sql = "ALTER TABLE `{$field_attr['table']}` CHANGE `{$field_attr['oldname']}` `{$field_attr['name']}` {$field_attr['type']}{$field_attr['length']} {$field_attr['is_null']} {$field_attr['default']} COMMENT '{$field_attr['comment']}' {$field_attr['after']}";
		}
		return $this;
	}

	/**
	 * 删除字段
	 * @var $table 追加字段的表名
	 * @var $field 字段名
	 * @author colin <colin@tensent.cn>
	 */
	public function delField($table, $field) {
		$table     = $table ? $this->getTablename($table, true) : $this->table;
		$this->sql = "ALTER TABLE `$table` DROP `$field`";
		return $this;
	}

	/**
	 * 删除数据表
	 * @var $table 追加字段的表名
	 * @author colin <colin@tensent.cn>
	 */
	public function delTable($table) {
		$table     = $table ? $this->getTablename($table, true) : $this->table;
		$this->sql = "DROP TABLE `$table`";
		return $this;
	}

	/**
	 * 结束表
	 * @var $engine_type 数据库引擎
	 * @var $comment 表注释
	 * @var $charset 数据库编码
	 * @author colin <colin@tensent.cn>
	 */
	public function endTable($comment, $engine_type = null, $charset = null) {
		if (null != $charset) {
			$this->charset = $charset;
		}
		if (null != $engine_type) {
			$this->engine_type = $engine_type;
		}
		$end = "ENGINE=" . $this->engine_type . " AUTO_INCREMENT=1 DEFAULT CHARSET=" . $this->charset . " ROW_FORMAT=DYNAMIC COMMENT='" . $comment . "';";
		$this->sql .= ")" . $end;
		return $this;
	}

	/**
	 * 创建动作
	 * @return int 0
	 * @author colin <colin@tensent.cn>
	 */
	public function create() {
		$res = Db::execute($this->sql);
		return $res !== false;
	}

	/**
	 * create的别名
	 * @return int 0
	 * @author colin <colin@tensent.cn>
	 */
	public function query() {
		return $this->create();
	}

	/**
	 * 获取最后生成的sql语句
	 * @author colin <colin@tensent.cn>
	 */
	public function getLastSql() {
		return $this->sql;
	}

	/**
	 * 获取指定的表名
	 * @var $table 要获取名字的表名
	 * @var $prefix 获取表前缀？ 默认为不获取 false
	 * @author colin <colin@tensent.cn>
	 */
	public function getTablename($table, $prefix = false) {
		if (false == $prefix) {
			$this->table = $this->model_table_prefix . $table;
		} else {
			$this->table = $this->prefix . $this->model_table_prefix . $table;
		}
		return $this->table;
	}

	/**
	 * 获取指定表名的所有字段及详细信息
	 * @var $table 要获取名字的表名 可以为sent_tengsu_photo、tengsu_photo、photo
	 * @author colin <colin@tensent.cn>
	 */
	public function getFields($table) {
		if (false == $table) {
			$table = $this->table; //为空调用当前table
		} else {
			$table = $table;
		}
		$patten = "/\./";
		if (!preg_match_all($patten, $table)) {
			//匹配_
			$patten = "/_+/";
			if (!preg_match_all($patten, $table)) {
				$table = $this->prefix . $this->model_table_prefix . $table;
			} else {
				//匹配是否包含表前缀，如果是 那么就是手动输入
				$patten = "/$this->prefix/";
				if (!preg_match_all($patten, $table)) {
					$table = $this->prefix . $table;
				}
			}
		}
		$sql = "SHOW FULL FIELDS FROM $table";
		return Db::query($sql);
	}

	/**
	 * 确认表是否存在
	 * @var $table 表名 可以为sent_tengsu_photo、tengsu_photo、photo
	 * @author colin <colin@tensent.cn>
	 * @return boolen
	 */
	public function CheckTable($table) {
		//获取表名
		$this->table = $this->getTablename($table, true);
		$result      = Db::execute("SHOW TABLES LIKE '%$this->table%'");
		return $result;
	}

	/**
	 * 确认字段是否存在
	 * @var $table 表名 可以为sent_tengsu_photo、tengsu_photo、photo
	 * @var $field 字段名 要检查的字段名
	 * @author colin <colin@tensent.cn>
	 * @return boolen
	 */
	public function CheckField($table, $field) {
		//检查字段是否存在
		$table = $this->getTablename($table, true);
		if (!Db::query("Describe $table $field")) {
			return false;
		} else {
			return true;
		}
	}
}