<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\controller\admin;

use app\model\Channel as ChannelM;
use sent\tree\Tree;

/**
 * @title 频道管理
 * @description 频道管理
 */
class Channel extends Base {

	/**
	 * @title 频道列表
	 */
	public function index(ChannelM $channel, $type = 0) {
		/* 获取频道列表 */
		$map[] = ['status', '>', -1];
		if ($type) {
			$map[] = ['type', '=', $type];
		}
		$list = $channel->where($map)->order('sort asc,id asc')->select()->append(['status_text'])->toArray();

		if (!empty($list)) {
			$tree = new Tree();
			$list = $tree->toFormatTree($list);
		}

		$this->data = array(
			'tree' => $list,
			'type' => $type,
		);
		return $this->fetch();
	}

	/**
	 * @title 单字段编辑
	 */
	public function editable($name = null, $value = null, $pk = null) {
		if ($name && ($value != null || $value != '') && $pk) {
			ChannelM::where(array('id' => $pk))->update([$name => $value]);
		}
	}

	/**
	 * @title 添加频道
	 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
	 */
	public function add() {
		if ($this->request->isPost()) {
			$data = $this->request->post();
			if ($data) {
				$id = ChannelM::create($data);
				if ($id) {
					return $this->success('新增成功', url('/admin/channel/index'));
				} else {
					return $this->error('新增失败');
				}
			} else {
				$this->error('新增失败');
			}
		} else {
			$pid = $this->request->param('pid', 0);
			//获取父导航
			$parent = "";
			if (!empty($pid)) {
				$parent = ChannelM::where(array('id' => $pid))->value('title');
			}

			$pnav = ChannelM::where(array('pid' => '0'))->select();

			$this->data = [
				'parent'  => $parent,
				'pnav'  => $pnav,
				'pid'   => $pid,
				'info'  => ['pid' => $pid]
			];
			return $this->fetch('edit');
		}
	}
	/**
	 * @title 编辑频道
	 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
	 */
	public function edit($id = 0) {
		if ($this->request->isPost()) {
			$data = $this->request->post();
			if ($data) {
				$result = ChannelM::update($data, ['id' => $data['id']]);
				if (false !== $result) {
					return $this->success('编辑成功', url('/admin/channel/index'));
				} else {
					return $this->error('编辑失败');
				}
			} else {
				return $this->error('非法操作！');
			}
		} else {
			$pid = $this->request->param('pid', 0);
			/* 获取数据 */
			$info = ChannelM::find($id);

			if (false === $info) {
				return $this->error('获取配置信息错误');
			}

			//获取父导航
			$parent = "";
			if (!empty($pid)) {
				$parent = ChannelM::where(array('id' => $pid))->value('title');
			}

			$pnav = ChannelM::where(array('pid' => '0'))->select();
			$this->data = [
				'parent'  => $parent,
				'pnav'  => $pnav,
				'pid'   => $pid,
				'info'  => $info
			];
			return $this->fetch();
		}
	}
	/**
	 * @title 删除频道
	 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
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

		$result = ChannelM::where($map)->delete();
		if (false !== $result) {
			return $this->success('删除成功');
		} else {
			return $this->error('删除失败！');
		}
	}
	/**
	 * @title 导航排序
	 * @author huajie <banhuajie@163.com>
	 */
	public function sort() {
		if ($this->request->isPost()) {
			$ids = $this->request->param('ids', '');
			$ids = explode(',', $ids);
			$data = [];
			foreach ($ids as $key => $value) {
				$data[] = ['id' => $value, 'sort' => $key];
			}
			$result = (new ChannelM())->saveAll($data);
			if ($result !== false) {
				return $this->success('排序成功！', url('/admin/channel/index'));
			} else {
				return $this->error('排序失败！');
			}
		}else{
			$ids = $this->request->param('ids', '');
			$pid = $this->request->param('pid', '');
			$map = [];
			//获取排序的数据
			$map[] = ['status', '>', -1];
			if ($ids && strrpos($ids, ",")) {
				$map[] = ['id', 'IN', explode(",", $ids)];
			}else{
				if ($pid) {
					$map[] = ['pid', '=', $pid];
				}
			}
			$list = ChannelM::where($map)->field('id,title')->order('sort asc,id asc')->select();

			$this->data = [
				'list' => $list
			];
			return $this->fetch();
		}
	}

	/**
	 * @title 设置状态
	 */
	public function setStatus() {
		$id = $this->request->param('id', 0);
		$status = $this->request->param('status', 0);
		$map = [];
		if (is_array($id)) {
			$map[] = ['id', 'IN', $id];
		}else{
			$map[] = ['id', '=', $id];
		}

		$result = ChannelM::update(['status'=> $status], $map);
		if ($result !== false) {
			return $this->success('操作成功！');
		} else {
			return $this->error('操作失败！');
		}
	}
}