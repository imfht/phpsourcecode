<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\controller\admin;

use think\facade\Db;
use app\model\Model;
use app\model\Attribute;

/**
 * @title 内容管理
 */
class Content extends Base {

	public $modelInfo = [];
	public $model = null;
	
	public function initialize() {
		parent::initialize();
		$this->getContentMenu();
		$this->modelInfo = Model::where('name', $this->request->param('name'))->find()->append(['grid_list', 'attr_group'])->toArray();
		$this->model = Db::name($this->modelInfo['name']);
	}

	/**
	 * @title 内容列表
	 * @return [html] [页面内容]
	 * @author molong <ycgpp@126.com>
	 */
	public function index() {
		$param = $this->request->param();
		if ($this->modelInfo['list_grid'] == '') {
			return $this->error("列表定义不正确！", url('/admin/model/edit', array('id' => $this->modelInfo['id'])));
		}
		$order = "id desc";
		$map = [];

		if (isset($param['keyword']) && $param['keyword'] != '') {
			$map[] = ['title', 'LIKE', '%'.$param['keyword'].'%'];
		}

		$list = $this->model->where($map)->order($order)->paginate($this->request->pageConfig);

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
			$template = 'admin/content/' . $this->modelInfo['template_list'];
		} else {
			$template = 'admin/content/index';
		}
		return $this->fetch($template);
	}

	/**
	 * @title 内容添加
	 * @author molong <ycgpp@126.com>
	 */
	public function add() {
		if ($this->request->isPost()) {
			$data = $this->request->post();
			$data['create_time'] = time();
			$data['update_time'] = time();
			$data['uid'] = session('adminInfo.uid');

			$result = $this->model->save($data);
			if ($result) {
				return $this->success("添加成功！", url('/admin/'.$this->modelInfo['name'].'/index'));
			} else {
				return $this->error('添加失败！');
			}
		} else {
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
				$template = 'admin/content/' . $this->modelInfo['template_add'];
			} else {
				$template = 'admin/public/edit';
			}
			return $this->fetch($template);
		}
	}

	/**
	 * @title 内容修改
	 * @author molong <ycgpp@126.com>
	 */
	public function edit($id) {
		if ($this->request->isPost()) {
			$data = $this->request->post();
			$data['update_time'] = time();

			$result = $this->model->save($data);
			if ($result !== false) {
				return $this->success("更新成功！", url('/admin/'.$this->modelInfo['name'].'/index'));
			} else {
				return $this->error('修改失败！');
			}
		} else {
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
				$template = 'admin/content/' . $this->modelInfo['template_edit'];
			} else {
				$template = 'admin/public/edit';
			}
			return $this->fetch($template);
		}
	}

	/**
	 * @title 内容删除
	 * @author molong <ycgpp@126.com>
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

		$result = $this->model->where($map)->delete();

		if (false !== $result) {
			return $this->success("删除成功！");
		} else {
			return $this->error("删除失败！");
		}
	}

	/**
	 * @title 设置状态
	 * @author molong <ycgpp@126.com>
	 */
	public function status() {
		$id = $this->request->param('id', '');
		$status = $this->request->param('status', 1);
		if (!$id) {
			return $this->error('请选择要操作的数据!');
		}

		$result = $this->model->where('id', $id)->update(['status' => $status]);
		if (false !== $result) {
			return $this->success("操作成功！");
		} else {
			return $this->error("操作失败！！");
		}
	}

	/**
	 * @title 设置置顶
	 * @author molong <ycgpp@126.com>
	 */
	public function settop() {
		$id = $this->request->param('id', '');
		$is_top = $this->request->param('is_top', 1);
		if (!$id) {
			return $this->error('请选择要操作的数据!');
		}

		$result = $this->model->where('id', $id)->update(['is_top' => $is_top]);
		if (false !== $result) {
			return $this->success("操作成功！");
		} else {
			return $this->error("操作失败！！");
		}
	}

	/**
	 * 检测需要动态判断的文档类目有关的权限
	 *
	 * @return boolean|null
	 *      返回true则表示当前访问有权限
	 *      返回false则表示当前访问无权限
	 *      返回null，则会进入checkRule根据节点授权判断权限
	 *
	 * @author 朱亚杰  <xcoolcc@gmail.com>
	 */
	protected function checkDynamic() {
		$model_id = $this->request->param('model_id');
		if (IS_ROOT) {
			return true; //管理员允许访问任何页面
		}
		$models = model('AuthGroup')->getAuthModels(session('user_auth.uid'));
		if (!$model_id) {
			return false;
		} elseif (in_array($model_id, $models)) {
			//返回null继续判断操作权限
			return null;
		} else {
			return false; //无权限
		}
		return false;
	}
}