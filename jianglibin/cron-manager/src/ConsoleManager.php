<?php

namespace SuperCronManager;

/**
 * 控制台输出
 */
class ConsoleManager
{

	/**
	 * 设置任务状态表格头
	 * @var array
	 */
	static private $_taskHeader = array('id', 'name', 'tag', 'status', 'count', 'last_time', 'next_time');

	/**
	 * 设置扩展检测表格头
	 * @var array
	 */
	static private $_checkHeader = array('name', 'status', 'desc', 'help');

	/**
	 * 向控制台输出任务状态信息
	 * @param  $tasks
	 * @param  $expand 扩展头部标题
	 * @return void
	 */
	public static function taskStatusTable($tasks, $expand = [])
	{
		$expandTable = new \Console_Table();

		$last = end($expand);
		foreach ($expand as $key => $value) {
			$expandTable->addRow(array($key, $value));
			if ($value != $last) {
				$expandTable->addSeparator();
			}
		}
		$table  = '';
		$table .= $expandTable->getTable();

		$taskTable = new \Console_Table();
		$taskTable->setHeaders(static::$_taskHeader);
		$status = [
			'0' => '正常',
			'1' => '关闭',
			'2' => '过期关闭',
		];
		foreach ($tasks as $id => $task) {
			$attr = $task->getAttributes();
			$taskTable->addRow(array(
				$attr['id'], 
				$attr['name'], 
				$attr['intvalTag'], 
				$status[$attr['status']], 
				$attr['count'], 
				$attr['lastTime'] ? date('Y-m-d H:i:s',$attr['lastTime']) : '-', 
				$attr['nextTime'] ? date('Y-m-d H:i:s',$attr['nextTime']) : '-'
			));
		}
		return $table . $taskTable->getTable();
	}

	/**
	 * 检查扩展是否开启
	 */
	public static function checkExtensions() {
		$table = new \Console_Table();
		$table->setHeaders(static::$_checkHeader);
		$exts = get_loaded_extensions();

		if (version_compare(PHP_VERSION, '5.4', ">=")) {
			$row = array('php>=5.4', '[OK]');
		} else {
			$row = array('php>=5.4', '[ERR]', '请升级PHP版本');
		}
		$table->addRow($row);
		
		$checks = array(
			array('name' => 'pcntl', 'remark' => '缺少扩展', 'help'=>'http://php.net/manual/zh/pcntl.installation.php'),
			array('name' => 'posix', 'remark' => '缺少扩展', 'help'=>'http://php.net/manual/zh/posix.installation.php'),
			array('name' => 'sysvmsg', 'remark' => '缺少扩展', 'help'=>'自行搜索安装方法,也可以推荐好的文章'),
			array('name' => 'sysvsem', 'remark' => '缺少扩展', 'help'=>'自行搜索安装方法,也可以推荐好的文章'),
			array('name' => 'sysvshm', 'remark' => '缺少扩展', 'help'=>'自行搜索安装方法,也可以推荐好的文章'),
		);

		foreach ($checks as $check) {
			$row = array();
			if(in_array($check['name'], $exts)) {
				$row = array($check['name'], '[OK]');
			} else {
				$row = array($check['name'], '[ERR]', $check['remark'], $check['help']);
			}
			$table->addRow($row);
		}
		return $table->getTable();
	}
} 