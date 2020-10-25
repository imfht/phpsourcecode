<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\controller\api;

/**
 * @title 接口功能
 */
class Api extends Base {

	public $filter_method = ['__construct'];

	/**
	 * @title 功能列表
	 * @return [type] [description]
	 */
	public function index() {
		$list = [];
		$path = app()->getAppPath() . 'controller/api';

		$classname = $this->scanFile($path);
		foreach ($classname as $value) {
			$class = "app\\controller\\api\\" . $value;
			if (class_exists($class)) {
				$reflection = new \ReflectionClass($class);
				$group_doc  = $this->Parser($reflection->getDocComment());
				$method     = $reflection->getMethods(\ReflectionMethod::IS_FINAL | \ReflectionMethod::IS_PUBLIC);
				$group_doc['name'] = $value;
				$item = [];
				foreach ($method as $key => $v) {
					if (!in_array($v->name, $this->filter_method)) {
						$title_doc = $this->Parser($v->getDocComment());
						if (isset($title_doc['title']) && $title_doc['title']) {
							$item[] = array(
								'url'    => 'api/' . strtolower($value) . '/' . strtolower($v->name),
								'name'   => 'api/' . strtolower($value) . '/' . strtolower($v->name),
								'method' => isset($title_doc['method']) ? strtoupper($title_doc['method']) : 'GET',
								'title'  => trim($title_doc['title']),
								'group'  => strtolower($value),
								'status' => 1,
							);
						}
					}
				}
				$group_doc['children'] = $item;
				$list[] = $group_doc;
			}
		}

		$this->data['data'] = $list;
		return $this->data;
	}

	protected function scanFile($path) {
		$result = array();
		$files  = scandir($path);
		foreach ($files as $file) {
			if ($file != '.' && $file != '..') {
				if (is_dir($path . '/' . $file)) {
					$this->scanFile($path . '/' . $file);
				} else {
					$result[] = substr(basename($file), 0, -4);
				}
			}
		}
		return $result;
	}

	protected function Parser($text) {
		$doc = new \doc\Doc();
		return $doc->parse($text);
	}
}