<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\model;

/**
 * 用户模型
 */
class SeoRule extends \think\Model {

	public static $keyList = [
		['name' => 'id', 'title' => '标识', 'type' => 'hidden'],
		['name' => 'title', 'title' => '规则名称', 'type' => 'text', 'is_must'=>true, 'option' => '', 'help' => '规则名称，方便记忆'],
		['name' => 'app', 'title' => '模块名', 'type' => 'select', 'option' => [['key'=>'*', 'label' => '-所有模块-'], ['key'=>'front', 'label' => '前台模块'], ['key'=>'user', 'label' => '用户中心']], 'help' => '不选表示所有模块'],
		['name' => 'controller', 'title' => '控制器', 'type' => 'text', 'option' => '', 'help' => '不填表示所有控制器'],
		['name' => 'action', 'title' => '方法', 'type' => 'text', 'option' => '', 'help' => '不填表示所有方法'],
		['name' => 'seo_title', 'title' => 'SEO标题', 'type' => 'text', 'is_must'=>true, 'option' => '', 'help' => '不填表示使用默认'],
		['name' => 'seo_keywords', 'title' => 'SEO关键字', 'type' => 'text', 'is_must'=>true, 'option' => '', 'help' => '不填表示使用默认'],
		['name' => 'seo_description', 'title' => 'SEO描述', 'type' => 'text', 'is_must'=>true, 'option' => '', 'help' => '不填表示使用默认'],
		['name' => 'status', 'title' => '状态', 'type' => 'select', 'option' => [['key'=>'0', 'label' => '禁用'], ['key'=>'1', 'label' => '启用']], 'help' => ''],
		['name' => 'sort', 'title' => '排序', 'type' => 'text', 'option' => '', 'help' => ''],
	];

	protected function setAppAttr($value) {
		return $value ? $value : '*';
	}

	protected function setControllerAttr($value) {
		return $value ? $value : '*';
	}

	protected function setActionAttr($value) {
		return (isset($value) && $value) ? $value : '*';
	}

	protected function getAppAttr($value) {
		return $value ? $value : '*';
	}

	protected function getControllerAttr($value) {
		return $value ? $value : '*';
	}

	protected function getActionAttr($value) {
		return (isset($value) && $value) ? $value : '*';
	}

	protected function getRuleNameAttr($value, $data) {
		return $data['app'] . '/' . $data['controller'] . '/' . $data['action'];
	}

	public static function getMetaOfCurrentPage($request, $seo) {
		foreach ($seo as $key => $value) {
			if (is_array($value)) {
				$seo_to_str[$key] = implode(',', $value);
			} else {
				$seo_to_str[$key] = $value;
			}
		}
		if (false !== strrpos($request->controller(), ".")) {
			list($module, $controller) = explode(".", $request->controller());
		}else{
			$module = "front";
			$controller = $request->controller();
		}
		
		$result = self::getMeta($module, $controller , $request->action(), $seo_to_str);
		return $result;
	}

	private static function getMeta($module, $controller, $action, $seo) {
		//获取相关的规则
		$rules = self::getRelatedRules($module, $controller, $action);

		//按照排序计算最终结果
		$title = '';
		$keywords = '';
		$description = '';

		$need_seo = 1;
		foreach ($rules as $e) {
			//如果存在完全匹配的seo配置，则不用程序设置的seo资料
			if ($e['app'] && $e['controller'] && $e['action']) {
				$need_seo = 0;
			}
			if (!$title && $e['seo_title']) {
				$title = $e['seo_title'];
			}
			if (!$keywords && $e['seo_keywords']) {
				$keywords = $e['seo_keywords'];
			}
			if (!$description && $e['seo_description']) {
				$description = $e['seo_description'];
			}
		}
		if ($need_seo) {
			//默认让全站的seo规则优先级小于$this->setTitle等方式设置的规则。
			if ($seo['title']) {
				$title = $seo['title'];
			}
			if ($seo['keywords']) {
				$keywords = $seo['keywords'];
			}
			if ($seo['description']) {
				$description = $seo['description'];
			}
		}
		//生成结果
		$result = array('title' => $title, 'keywords' => $keywords, 'description' => $description);

		//返回结果
		return $result;
	}

	private static function getRelatedRules($module, $controller, $action) {
		//查询与当前页面相关的SEO规则
		$rules = self::where('app', 'like', ['*', $module], 'or')
			->where('controller', 'like', ['*', $controller], 'or')
			->where('action', 'like', ['*', $action], 'or')
			->where('status', 1)
			->order('sort asc')
			->select();

		//返回规则列表
		return $rules;
	}
}