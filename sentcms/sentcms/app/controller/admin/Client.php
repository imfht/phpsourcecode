<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\controller\admin;

use app\model\Client as ClientM;

/**
 * @title 客户端管理
 */
class Client extends Base {

	/**
	 * @title 客户端列表
	 */
	public function index() {
		$map = [];

		$list = ClientM::where($map)->paginate($this->request->pageConfig);

		$this->data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		return $this->fetch();
	}

	/**
	 * @title 添加客户端
	 */
	public function add() {
		if ($this->request->isPost()) {
			$data = $this->request->param();

			$result = ClientM::create($data);
			if (false !== $result) {
				return $this->success('成功添加', url('/admin/client/index'));
			} else {
				return $this->error('添加失败！');
			}
		} else {
			$info['appid'] = time(); //八位数字appid
			$info['appsecret'] = \xin\helper\Str::random(32); //32位数字加字母秘钥

			$this->data = array(
				'info' => $info,
			);
			return $this->fetch();
		}
	}

	/**
	 * @title 编辑客户端
	 */
	public function edit() {
		if ($this->request->isPost()) {
			$data = $this->request->param();

			$result = ClientM::update($data, ['id' => $this->request->param('id')]);
			if (false !== $result) {
				return $this->success('修改添加', url('/admin/client/index'));
			} else {
				return $this->error($this->model->getError());
			}
		} else {
			$info = ClientM::where('id', $this->request->param('id'))->find();

			$this->data = array(
				'info' => $info,
			);
			return $this->fetch('admin/client/add');
		}
	}

	/**
	 * @title 删除客户端
	 */
	public function del() {
		$id = $this->request->param('id', '');

		$map = [];
		if (!$id) {
			return $this->error('请选择要操作的数据!');
		}
		if (is_array($id)) {
			$map[] = ['id', 'IN', $id];
		}else{
			$map[] = ['id', '=', $id];
		}

		$result = ClientM::where($map)->delete();
		if (false !== $result) {
			return $this->success('删除成功');
		} else {
			return $this->error('删除失败！');
		}
	}

	public function api(){
		$list = [];
		$path = app()->getAppPath() . 'controller/api';

		$classname = $this->scanFile($path);
		foreach ($classname as $value) {
			if($value == 'Base'){
				continue;
			}
			$class = "app\\controller\\api\\" . $value;
			if (class_exists($class)) {
				$reflection = new \ReflectionClass($class);
				$group_doc  = $this->Parser($reflection->getDocComment());
				$method     = $reflection->getMethods(\ReflectionMethod::IS_FINAL | \ReflectionMethod::IS_PUBLIC);
				$group_doc['name'] = $value;
				foreach ($method as $key => $v) {
					if (!in_array($v->name, ['__construct'])) {
						$title_doc = $this->Parser($v->getDocComment());
						if (isset($title_doc['title']) && $title_doc['title']) {
							$list[] = array(
								'url'    => 'api/' . strtolower($value) . '/' . strtolower($v->name),
								'name'   => 'api/' . strtolower($value) . '/' . strtolower($v->name),
								'method' => isset($title_doc['method']) ? strtoupper($title_doc['method']) : 'GET',
								'title'  => trim($title_doc['title']),
								'group'  => $group_doc['title'],
								'status' => 1,
							);
						}
					}
				}
			}
		}

		$this->data = [
			'list' => $list
		];
		return $this->fetch();
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