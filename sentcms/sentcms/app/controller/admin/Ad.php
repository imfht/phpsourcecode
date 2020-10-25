<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\controller\admin;

use app\model\AdPlace;
use app\model\Ad as AdModel;

/**
 * @title 广告管理
 * @description 广告管理
 */
class Ad extends Base {

	/**
	 * @title 广告位管理
	 */
	public function index() {
		$map = [];
		$order = "id desc";

		$list = AdPlace::where($map)->order($order)->paginate($this->request->pageConfig);

		$this->data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		return $this->fetch();
	}

	/**
	 * @title 广告位添加
	 */
	public function add() {
		if ($this->request->isPost()) {
			$data = $this->request->post();
			$result = AdPlace::create($data);
			if (false !== $result) {
				return $this->success("添加成功！");
			} else {
				return $this->error('添加失败！');
			}
		} else {
			$this->data = array(
				'keyList' => AdPlace::$keyList,
			);
			return $this->fetch('admin/public/edit');
		}
	}

	/**
	 * @title 广告位编辑
	 */
	public function edit($id = null) {
		if ($this->request->isPost()) {
			$data = $this->request->post();
			$result = AdPlace::update($data, ['id' => $data['id']]);
			if ($result) {
				return $this->success("修改成功！", url('/admin/ad/index'));
			} else {
				return $this->error('修改失败！');
			}
		} else {
			$info = AdPlace::find($id);
			if (!$info) {
				return $this->error("非法操作！");
			}
			$this->data = array(
				'info' => $info,
				'keyList' => AdPlace::$keyList,
			);
			return $this->fetch('admin/public/edit');
		}
	}

	/**
	 * @title 广告位删除
	 */
	public function del() {
		$id = $this->getArrayParam('id');

		if (empty($id)) {
			return $this->error("非法操作！");
		}
		$map['id'] = array('IN', $id);
		$result = $this->adplace->where($map)->delete();
		if ($result) {
			return $this->success("删除成功！");
		} else {
			return $this->error("删除失败！");
		}
	}

	/**
	 * @title 广告列表
	 */
	public function lists($id = null) {
		$map[] = ['place_id', '=', $id];
		$order = "id desc";

		$list = AdModel::where($map)->order($order)->paginate($this->request->pageConfig);
		$this->data = array(
			'id' => $id,
			'list' => $list,
			'page' => $list->render(),
		);
		return $this->fetch();
	}

	/**
	 * @title 添加广告
	 */
	public function addad($id) {
		if ($this->request->isPost()) {
			$data = $this->request->post();

			$result = AdModel::create($data);
			if ($result) {
				return $this->success("添加成功！", url('/admin/ad/lists', ['id' => $data['place_id']]));
			} else {
				return $this->error('添加失败！');
			}
		} else {
			$info['place_id'] = $id;
			$this->data = array(
				'info' => $info,
				'keyList' => AdModel::$keyList,
			);
			return $this->fetch('admin/public/edit');
		}
	}

	/**
	 * @title 编辑广告
	 */
	public function editad($id = null) {
		if ($this->request->isPost()) {
			$data = $this->request->post();

			$result = AdModel::update($data, ['id' => $data['id']]);
			if ($result) {
				return $this->success("修改成功！", url('/admin/ad/lists', ['id' => $data['place_id']]));
			} else {
				return $this->error('修改失败！');
			}
		} else {
			$info = AdModel::find($id);
			if (!$info) {
				return $this->error("非法操作！");
			}
			$this->data = array(
				'info' => $info,
				'keyList' => AdModel::$keyList,
			);
			return $this->fetch('admin/public/edit');
		}
	}

	/**
	 * @title 删除广告
	 */
	public function delad() {
		$id = $this->getArrayParam('id');

		if (empty($id)) {
			return $this->error("非法操作！");
		}
		$map['id'] = array('IN', $id);
		$result = db('ad')->where($map)->delete();
		if ($result) {
			return $this->success("删除成功！");
		} else {
			return $this->error("删除失败！");
		}
	}
}