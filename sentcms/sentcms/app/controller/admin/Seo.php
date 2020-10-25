<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\controller\admin;

use app\model\Rewrite;
use app\model\SeoRule;
use think\facade\Cache;

/**
 * @title SEO管理
 */
class Seo extends Base {

	/**
	 * @title SEO列表
	 */
	public function index($page = 1, $r = 20) {
		$map = [];

		//读取规则列表
		$map[] = ['status', '>=', 0];

		$list = SeoRule::where($map)->order('sort asc')->paginate($this->request->pageConfig);

		$this->data = [
			'list' => $list,
			'page' => $list->render(),
		];
		return $this->fetch();
	}

	/**
	 * @title 添加SEO
	 */
	public function add() {
		if ($this->request->isPost()) {
			$data = $this->request->post();
			$result = SeoRule::create($data);
			if ($result) {
				return $this->success("添加成功！");
			} else {
				return $this->error("添加失败！");
			}
		} else {
			$this->data = array(
				'keyList' => SeoRule::$keyList,
			);
			return $this->fetch('admin/public/edit');
		}
	}

	/**
	 * @title 编辑SEO
	 */
	public function edit($id = null) {
		if ($this->request->isPost()) {
			$data = $this->request->post();

			$result = SeoRule::update($data, array('id' => $data['id']));
			if (false !== $result) {
				return $this->success("修改成功！");
			} else {
				return $this->error("修改失败！");
			}
		} else {
			$id = $this->request->param('id', 0);
			if (!$id) {
				return $this->error("非法操作！");
			}
			$info = SeoRule::find($id);
			$this->data = array(
				'info' => $info,
				'keyList' => SeoRule::$keyList,
			);
			return $this->fetch('admin/public/edit');
		}
	}

	/**
	 * @title 删除SEO
	 */
	public function del() {
		$id = $this->request->param('id');

		if (empty($id)) {
			return $this->error("非法操作！");
		}

		if (is_array($id)) {
			$map[] = ['id', 'IN', $id];
		} else {
			$map[] = ['id', '=', $id];
		}

		$result = SeoRule::where($map)->delete();
		if ($result) {
			return $this->success("删除成功！");
		} else {
			return $this->error("删除失败！");
		}
	}

	/**
	 * @title 伪静态列表
	 */
	public function rewrite() {
		$map = [];

		$list = Rewrite::where($map)->paginate($this->request->pageConfig);

		$this->data = [
			'list' => $list,
			'page' => $list->render(),
		];
		return $this->fetch();
	}

	/**
	 * @title 添加静态规则
	 */
	public function addrewrite() {
		if ($this->request->isPost()) {
			$data = $this->request->param();

			$result = Rewrite::create($data);
			if (false != $result) {
				Cache::pull('rewrite_list');
				return $this->success("添加成功！", url('/admin/seo/rewrite'));
			} else {
				return $this->error('添加失败！');
			}
		} else {
			$this->data = array(
				'keyList' => Rewrite::$keyList,
			);
			return $this->fetch('admin/public/edit');
		}
	}

	/**
	 * @title 编辑静态规则
	 */
	public function editrewrite() {
		if ($this->request->isPost()) {
			$data = $this->request->param();

			$result = Rewrite::update($data, ['id' => $data['id']]);
			if (false != $result) {
				Cache::pull('rewrite_list');
				return $this->success("更新成功！", url('/admin/seo/rewrite'));
			} else {
				return $this->error('更新失败！');
			}
		} else {
			$id = $this->request->param('id');
			$info = Rewrite::find($id);
			$this->data = array(
				'info' => $info,
				'keyList' => Rewrite::$keyList,
			);
			return $this->fetch('admin/public/edit');
		}
	}

	/**
	 * @title 删除静态规则
	 */
	public function delrewrite() {
		$id = $this->request->param('id');

		if (empty($id)) {
			return $this->error("非法操作！");
		}

		if (is_array($id)) {
			$map[] = ['id', 'IN', $id];
		} else {
			$map[] = ['id', '=', $id];
		}

		$result = Rewrite::where($map)->delete();
		if ($result) {
			Cache::pull('rewrite_list');
			return $this->success("删除成功！");
		} else {
			return $this->error("删除失败！");
		}
	}
}