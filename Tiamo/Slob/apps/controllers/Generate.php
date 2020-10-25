<?php

namespace Swoole\Generate;

/**
 * Description of Generator
 *
 * @author Xiang dongdong<xiangdong198719@gmail.com>
 */
class Generator
{

	protected $db;

	/**
	 * 将要自动生成的数据库配置传入类获取db
	 *
	 * @param $db_name
	 * @throws \Exception
	 * @internal param type $config
	 */
	function __construct($db_name)
	{
		if (isset($db_name)) {
			$this->db = \Swoole::$php->db($db_name);
		} else {
			throw new \Exception(__CLASS__ . ": require db name");
		}
	}

	/**
	 * 获取所有表 表名
	 *
	 * @return type
	 */
	function getAllTables()
	{
		$data = $this->db->query("show tables")->fetchall();
		return $data;
	}

	/**
	 * 文件写入
	 *
	 * @param type $file
	 * @param type $content
	 * @return boolean
	 * @throws \Exception
	 */
	static function createFile($file, $content)
	{
		if (!file_exists($file)) { //文件不存在
			$info_path = pathinfo($file);
			if (!is_dir($info_path["dirname"])) {
				self::createDir($info_path["dirname"]);
			}
			$handle = fopen($file, "w");
			if ($handle) {
				$cont = fwrite($handle, $content);
				//修改权限
				chmod($file, 0777);
				if ($cont === false) {
					throw new \Exception(__CLASS__ . ": $file 不能写入到文件 ");
				} else {
					return true;
				}
			} else {
				throw new \Exception(__CLASS__ . ": 创建文件失败");
			}
		} else { //文件已经存在
			if (is_writable($file)) {
				$handle = fopen($file, "w");
				$cont = fwrite($handle, $content);
				if ($cont === false) {
					throw new \Exception(__CLASS__ . ": $file 不能写入到文件 ");
				} else {
					return true;
				}
			} else {
				throw new \Exception(__CLASS__ . ": $file 文件不可写");
			}
		}
	}

	/**
	 * 创建目录
	 *
	 * @param type $path
	 */
	static function createDir($path)
	{
		if (!file_exists($path)) {
			self::createDir(dirname($path));
			mkdir($path, 0777);
		}
	}

	/**
	 * 生成model
	 *
	 * @param $modelName
	 * @param $name
	 * @return mixed
	 * @throws \Exception
	 */
	function generateModel($modelName, $name)
	{
		$table = $this->analysisTable($name);
		$primary = $table["primary"];
		if (!$primary) {
			throw new \Exception(__CLASS__ . ": $name 没有主键 ");
		}
		$content = include dirname(__FILE__) . "/templete/model.php";
		$file = WEBPATH . "/apps/models/" . $modelName . ".php";
		self::createFile($file, $content);
		return $content;
	}

	/**
	 * 生成controller
	 *
	 * @param $controllerName
	 * @param $name
	 * @return mixed
	 * @throws \Exception
	 */
	function generateController($controllerName, $name)
	{
		//分析表
		$table = $this->analysisTable($name);
		$primary = $table["primary"];
		//生成主页
		$templete_index = include dirname(__FILE__) . "/templete/view_index.php";
		$file = WEBPATH . "/apps/templates/$name/index.php";
		self::createFile($file, $templete_index);
		//生成添加
		$templete_add = include dirname(__FILE__) . "/templete/view_add.php";
		$file = WEBPATH . "/apps/templates/$name/add_$name.php";
		self::createFile($file, $templete_add);
		//生成修改
		$templete_update = include dirname(__FILE__) . "/templete/view_update.php";
		$file = WEBPATH . "/apps/templates/$name/update_$name.php";
		self::createFile($file, $templete_update);
		//生成控制器
		$content = include dirname(__FILE__) . "/templete/controller.php";
		$file = WEBPATH . "/apps/controllers/" . $controllerName . ".php";
		self::createFile($file, $content);
		return $content;
	}

	/**
	 * 分析表
	 *
	 * @param type $name
	 * @return type
	 */
	function analysisTable($name)
	{
		$data = $this->db->query("SHOW FULL COLUMNS FROM " . $name)->fetchall();
		foreach ($data as $key => $value) {
			if ($value["Key"] == "PRI") {
				$table["primary"] = $value["Field"];
			}
			$table["fields"][$value["Field"]] = $this->analysisType($value["Type"]);
			$table["fields"][$value["Field"]]["comment"] = $value["Comment"];
			$table["fields"][$value["Field"]]["isNull"] = $value["Null"] == "NO" ? 0 : 1;
			$table["fields"][$value["Field"]]["extra"] = $value["Extra"];
		}
		return $table;
	}

	/**
	 * 分析类型
	 *
	 * @param type $type
	 * @return string
	 */
	function analysisType($type)
	{
		preg_match_all('/(\w+)\((\d+)\)/', $type, $matchs);
		if ($matchs[0]) {
			$r_type = $matchs[1][0];
			$r_length = $matchs[2][0];
		} else {
			$r_type = $type;
			$r_length = "";
		}
		$data["type"] = $r_type;
		$data["length"] = $r_length;
		return $data;
	}

}
