<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\controller\admin;

use app\model\Attribute as AttributeModel;

/**
 * @title 字段管理
 * @description 字段管理
 */
class Attribute extends Base {

	//保存的Model句柄
	// protected $model;
	// protected $attr;

	//初始化
	public function initialize() {
		parent::initialize();
		$this->getContentMenu();
	}

	/**
	 * @title 字段列表
	 * @author colin <colin@tensent.cn>
	 */
	public function index($model_id = null) {
		if (!$model_id) {
			return $this->error('非法操作！');
		}
		$list = AttributeModel::where('model_id', $model_id)->order('id desc')->paginate($this->request->pageConfig);

		$this->data = array(
			'list' => $list,
			'model_id' => $model_id,
			'page' => $list->render(),
		);
		return $this->fetch();
	}

	/**
	 * @title 创建字段
	 * @author colin <colin@tensent.cn>
	 */
	public function add(AttributeModel $attribute) {
		if ($this->request->isPost()) {
			$data = $this->request->post();
			$result = $attribute->save($data);
			if (false !== $result) {
				return $this->success("创建成功！", url('/admin/attribute/index', ['model_id' => $data['model_id']]));
			} else {
				return $this->error('创建失败！');
			}
		} else {
			$model_id = $this->request->param('model_id', 0);
			if (!$model_id) {
				return $this->error('非法操作！');
			}
			$this->data = array(
				'info' => array('model_id' => $model_id),
				'fieldGroup' => AttributeModel::getfieldList(),
			);
			return $this->fetch('admin/public/edit');
		}
	}

	/**
	 * @title 编辑字段
	 * @author colin <colin@tensent.cn>
	 */
	public function edit(AttributeModel $attribute, $id = '', $model_id = '') {
		if ($this->request->isPost()) {
			$data = $this->request->post();
			$result = $attribute->exists(true)->save($data);
			if ($result) {
				return $this->success("修改成功！", url('/admin/attribute/index', ['model_id' => $model_id]));
			} else {
				return $this->error('修改失败！');
			}
		} else {
			$info = AttributeModel::find($id);
			$this->data = array(
				'info' => $info,
				'fieldGroup' => AttributeModel::getfieldList(),
			);
			return $this->fetch('admin/public/edit');
		}
	}

	/**
	 * @title 删除字段
	 * @var delattr 是否删除字段表里的字段
	 * @author colin <colin@tensent.cn>
	 */
	public function del() {
		$id = $this->request->param('id');
		$model_id = $this->request->param('model_id');

		if (!$id) {
			return $this->error("非法操作！");
		}

		$result = AttributeModel::find($id)->delete();
		if ($result) {
			return $this->success("删除成功！");
		} else {
			return $this->error($this->model->getError());
		}
	}
}