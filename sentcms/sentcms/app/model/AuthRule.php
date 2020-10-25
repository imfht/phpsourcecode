<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\model;

use think\Model;

/**
* 设置模型
*/
class AuthRule extends Model{

	const rule_url = 1;
	const rule_mian = 2;

	protected $type = array(
		'id'    => 'integer',
	);

	public $keyList = [
		['name'=>'module','title'=>'所属模块','type'=>'hidden'],
		['name'=>'title','title'=>'节点名称','type'=>'text', 'is_must'=>true,'help'=>''],
		['name'=>'name','title'=>'节点标识','type'=>'text', 'is_must'=>true,'help'=>''],
		['name'=>'group','title'=>'功能组','type'=>'text','help'=>'功能分组'],
		['name'=>'status','title'=>'状态','type'=>'select','option'=>[['key' => '0', 'label'=>'禁用'],['key' => '1', 'label'=>'启用']],'help'=>''],
		['name'=>'condition','title'=>'条件','type'=>'text','help'=>'']
	];

	public static function uprule($type){
		$path = app()->getAppPath() . 'controller' . DIRECTORY_SEPARATOR . $type;
		$list = [];

		$classname = self::scanFile($path);
		foreach ($classname as $value) {
			if($value == 'Base'){
				continue;
			}
			$class = "app\\controller\\" . $type . "\\" . $value;
			if (class_exists($class)) {
				$reflection = new \ReflectionClass($class);
				$group_doc  = self::Parser($reflection->getDocComment());
				$method     = $reflection->getMethods(\ReflectionMethod::IS_FINAL | \ReflectionMethod::IS_PUBLIC);
				$group_doc['name'] = $value;
				foreach ($method as $key => $v) {
					if (!in_array($v->name, ['__construct'])) {
						$title_doc = self::Parser($v->getDocComment());
						if (isset($title_doc['title']) && $title_doc['title']) {
							$list[] = array(
								'module'    => $type,
								'type'    => 2,
								'name'   => $type . '/' . strtolower($value) . '/' . strtolower($v->name),
								'title'  => trim($title_doc['title']),
								'group'  => $group_doc['title'],
								'status' => 1,
							);
						}
					}
				}
			}
		}
		foreach ($list as $key => $value) {
			$id = self::where('name', $value['name'])->value('id');
			if ($id) {
				$value['id'] = $id;
			}
			$list[$key] = $value;
		}
		return (new self())->saveAll($list);
	}

	protected static function scanFile($path) {
		$result = array();
		$files  = scandir($path);
		foreach ($files as $file) {
			if ($file != '.' && $file != '..') {
				if (is_dir($path . '/' . $file)) {
					self::scanFile($path . '/' . $file);
				} else {
					$result[] = substr(basename($file), 0, -4);
				}
			}
		}
		return $result;
	}

	protected static function Parser($text) {
		$doc = new \doc\Doc();
		return $doc->parse($text);
	}
}