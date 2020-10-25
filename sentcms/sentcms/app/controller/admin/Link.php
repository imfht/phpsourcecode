<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\controller\admin;

use app\model\Link as LinkM;

/**
 * @title 友情链接
 * @description 友情链接
 */
class Link extends Base {

	/**
	 * @title 链接列表
	 */
	public function index(LinkM $link) {
		$map = array();

		$order = "id desc";
		$list = $link->where($map)->order($order)->paginate($this->request->pageConfig);

		$this->data = array(
			'list' => $list,
			'page' => $list->render(),
		);
		return $this->fetch();
	}

	/**
	 * @title 添加链接
	 */
	public function add() {
		if ($this->request->isPost()) {
			$data = $this->request->post();
			if ($data) {
				$result = LinkM::create($data);
				if ($result) {
					return $this->success("添加成功！", url('/admin/link/index'));
				} else {
					return $this->error('添加失败！');
				}
			} else {
				return $this->error('未提交数据');
			}
		} else {
			$this->data = array(
				'keyList' => LinkM::$keyList,
			);
			return $this->fetch('admin/public/edit');
		}
	}

	/**
	 * @title 修改链接
	 */
	public function edit() {
		$id = $this->request->param('id');
		if ($this->request->isPost()) {
			$data = $this->request->post();
			if ($data) {
				$result = LinkM::update($data, array('id' => $data['id']));
				if (false !== $result) {
					return $this->success("修改成功！", url('/admin/link/index'));
				} else {
					return $this->error("修改失败！");
				}
			} else {
				return $this->error('未提交数据');
			}
		} else {
			$info = LinkM::find($id);

			$this->data = array(
				'keyList' => LinkM::$keyList,
				'info' => $info,
			);
			return $this->fetch('admin/public/edit');
		}
	}

	/**
	 * @title 删除链接
	 */
	public function delete() {
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

		$result = LinkM::where($map)->delete();
		if (false !== $result) {
			return $this->success('删除成功');
		} else {
			return $this->error('删除失败！');
		}
	}
}