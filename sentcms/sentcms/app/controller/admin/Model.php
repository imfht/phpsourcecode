<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\controller\admin;

use app\model\Attribute;
use app\model\Model as ModelM;
use think\facade\Cache;

/**
 * @title 模型管理
 */
class Model extends Base {

	public function initialize() {
		parent::initialize();
		$this->getContentMenu();
	}

	/**
	 * @title 模型列表
	 * @author huajie <banhuajie@163.com>
	 */
	public function index() {
		$map = [];

		$map[] = ['status', '>', '-1'];

		$order = "id desc";
		$list = ModelM::where($map)->order($order)->paginate($this->request->pageConfig);

		$this->data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		return $this->fetch();
	}

	/**
	 * @title 新增模型
	 * @author huajie <banhuajie@163.com>
	 */
	public function add(ModelM $model) {
		if ($this->request->isPost()) {
			$data = $this->request->post();
			$result = $model->save($data);
			if (false !== $result) {
				Cache::pull('model_list');
				$this->success('创建成功！', url('/admin/model/index'));
			} else {
				return $this->error('创建失败！');
			}
		} else {
			return $this->fetch();
		}
	}

	/**
	 * @title 编辑模型
	 * @author molong <molong@tensent.cn>
	 */
	public function edit(ModelM $model) {
		if ($this->request->isPost()) {
			$data = $this->request->post();

			$result = $model->exists(true)->save($data);
			if (false !== $result) {
				Cache::pull('model_list');
				$this->success('更新成功！', url('/admin/model/index'));
			} else {
				return $this->error('修改失败');
			}
		} else {
			$info = ModelM::find($this->request->param('id'));

			$field_group = parse_config_attr($info['attribute_group']);
			//获取字段列表
			$rows = Attribute::where('model_id', $this->request->param('id'))->where('is_show', 1)->order('group_id asc, sort asc')->select();
			if ($rows) {
				// 梳理属性的可见性
				foreach ($rows as $key => $field) {
					$list[$field['group_id']][] = $field;
				}
				foreach ($field_group as $key => $value) {
					$fields[$key] = isset($list[$key]) ? $list[$key] : array();
				}
			} else {
				$fields = array();
			}
			$this->data = array(
				'info' => $info,
				'field_group' => $field_group,
				'fields' => $fields,
			);
			return $this->fetch();
		}
	}

	/**
	 * @title 删除模型
	 * @author huajie <banhuajie@163.com>
	 */
	public function del() {
		$id = $this->request->param('id', 0);

		if (!$id) {
			return $this->error('非法操作！');
		}

		$result = ModelM::find($id)->delete();
		if ($result) {
			Cache::pull('model_list');
			return $this->success('删除模型成功！');
		} else {
			return $this->error('删除失败！');
		}
	}

	public function update() {
		$res = \think\Loader::model('Model')->change();
		if ($res['status']) {
			return $this->success($res['info'], url('index'));
		} else {
			return $this->error($res['info']);
		}
	}

	/**
	 * @title 更新数据
	 * @author colin <colin@tensent.cn>
	 */
	public function status() {
		$id = $this->request->param('id', 0);
		$status = $this->request->param('status', 0);

		if (!$id) {
			return $this->error('非法操作！');
		}
		$model = ModelM::where('id', $id)->find();

		if ($model['list_grid'] == '' && $status == 1) {
			return $this->error('模型列表未定义');
		}
		$result = ModelM::update(['status' => $status], ['id' => $id]);
		if (false !== $result) {
			Cache::pull('model_list');
			return $this->success('状态设置成功！');
		} else {
			return $this->error('操作失败！');
		}
	}
}