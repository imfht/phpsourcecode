<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\controller\user;

use think\facade\Db;
use app\model\Model;
use app\model\Attribute;

/**
 * @title 内容模块
 */
class Content extends Base {

	public $modelInfo = [];
	public $model = null;
	
	public function initialize() {
		parent::initialize();
		$this->modelInfo = Model::where('name', $this->request->param('name'))->find()->append(['grid_list', 'attr_group'])->toArray();
		$this->model = Db::name($this->modelInfo['name']);
	}

	/**
	 * @title 内容首页
	 * @return [type] [description]
	 */
	public function index() {
		$param = $this->request->param();
		if ($this->modelInfo['list_grid'] == '') {
			return $this->error("列表定义不正确！", url('/user/model/edit', array('id' => $this->modelInfo['id'])));
		}
		$order = "id desc";
		$map = [];
		$map[] = ['uid', '=', session('userInfo.uid')];
		if (isset($param['keyword']) && $param['keyword'] != '') {
			$map[] = ['title', 'LIKE', '%'.$param['keyword'].'%'];
		}

		$list = $this->model->where($map)->order($order)->paginate($this->modelInfo['list_row'], false, array(
			'query' => $this->request->param(),
		));

		$this->data = array(
			'grid' => $this->modelInfo['grid_list'],
			'list' => $list,
			'page' => $list->render(),
			'model_name' => $this->modelInfo['name'],
			'model_id' => $this->modelInfo['id'],
			'meta_title' => $this->modelInfo['title'].'列表',
			'param' => $param
		);
		if ($this->modelInfo['template_list']) {
			$template = 'user@content/' . $this->modelInfo['template_list'];
		} else {
			$template = 'user@content/index';
		}
		return $this->fetch($template);
	}

	/**
	 * @title 添加内容
	 * @return [type] [description]
	 */
	public function add() {
		if ($this->request->isPost()) {
			$data = $this->request->post();
			$data['create_time'] = time();
			$data['update_time'] = time();
			$data['uid'] = session('userInfo.uid');

			$result = $this->model->save($data);
			if ($result) {
				return $this->success("添加成功！", url('/user/'.$this->modelInfo['name'].'/index'));
			} else {
				return $this->error('添加失败！');
			}
		}else{
			$info = [
				'model_name' => $this->modelInfo['name'],
				'model_id' => $this->modelInfo['id']
			];
			$this->data = [
				'info' => $info,
				'fieldGroup' => Attribute::getField($this->modelInfo),
				'meta_title' => $this->modelInfo['title'].'添加'
			];

			if ($this->modelInfo['template_add']) {
				$template = 'user/content/' . $this->modelInfo['template_add'];
			} else {
				$template = 'user@/edit';
			}
			return $this->fetch($template);
		}
	}

	/**
	 * @title 修改内容
	 * @return [type] [description]
	 */
	public function edit($id) {
		if ($this->request->isPost()) {
			$data = $this->request->post();
			$data['update_time'] = time();

			$result = $this->model->save($data);
			if ($result !== false) {
				return $this->success("更新成功！", url('/user/'.$this->modelInfo['name'].'/index'));
			} else {
				return $this->error('修改失败！');
			}
		}else{
			if (!$id) {
				return $this->error("非法操作！");
			}
			$info = $this->model->find($id);
			if (!$info) {
				return $this->error('无此数据！');
			}
			$info['model_id'] = $this->modelInfo['id'];
			$this->data = array(
				'info' => $info,
				'fieldGroup' => Attribute::getField($this->modelInfo, 'edit'),
				'meta_title' => $this->modelInfo['title'].'修改'
			);
			if ($this->modelInfo['template_edit']) {
				$template = 'user/content/' . $this->modelInfo['template_edit'];
			} else {
				$template = 'user@/edit';
			}
			return $this->fetch('user@/edit');
		}
	}

	/**
	 * @title 删除内容
	 * @return [type] [description]
	 */
	public function del() {
		return $this->error("无删除权限！");
	}
}