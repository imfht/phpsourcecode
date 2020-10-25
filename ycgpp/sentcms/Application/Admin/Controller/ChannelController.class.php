<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace Admin\Controller;

class ChannelController extends \Common\Controller\AdminController {
	/**
	 * 频道列表
	 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
	 */
	public function index() {
		$pid = I('get.pid', 0);
		/* 获取频道列表 */
		//$map  = array('status' => array('gt', -1), 'pid'=>$pid);
		$map = array('status' => array('gt', -1));
		$list = M('Channel')->where($map)->order('sort asc,id asc')->select();
		
		$list = D('Tree')->toFormatTree($list);
		
		C('_SYS_GET_CHANNEL_TREE_', true);
		
		$this->assign('tree', $list);
		$this->assign('pid', $pid);
		$this->setMeta('导航管理');
		$this->display();
	}
	/* 单字段编辑 */
	public function editable($name = null, $value = null, $pk = null) {
		if ($name && ($value != null || $value != '') && $pk) {
			D('Channel')->where(array('id' => $pk))->setField($name, $value);
		}
	}
	/**
	 * 添加频道
	 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
	 */
	public function add() {
		if (IS_POST) {
			$Channel = D('Channel');
			$data = $Channel->create();
			if ($data) {
				$id = $Channel->add();
				if ($id) {
					$this->success('新增成功', U('index'));
					//记录行为
					action_log('update_channel', 'channel', $id, UID);
				} 
				else {
					$this->error('新增失败');
				}
			} 
			else {
				$this->error($Channel->getError());
			}
		} 
		else {
			$pid = I('get.pid', 0);
			//获取父导航
			if (!empty($pid)) {
				$parent = M('Channel')->where(array('id' => $pid))->field('title')->find();
				$this->assign('parent', $parent);
			}
			
			$pnav = M('Channel')->where(array('pid' => '0'))->select();
			$this->assign('pnav', $pnav);
			$this->assign('pid', $pid);
			$this->assign('info', null);
			$this->setMeta('新增导航');
			$this->display('edit');
		}
	}
	/**
	 * 编辑频道
	 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
	 */
	public function edit($id = 0) {
		if (IS_POST) {
			$Channel = D('Channel');
			$data = $Channel->create();
			if ($data) {
				if ($Channel->save()) {
					//记录行为
					action_log('update_channel', 'channel', $data['id'], UID);
					$this->success('编辑成功', U('index'));
				} 
				else {
					$this->error('编辑失败');
				}
			} 
			else {
				$this->error($Channel->getError());
			}
		} 
		else {
			$info = array();
			/* 获取数据 */
			$info = M('Channel')->find($id);
			
			if (false === $info) {
				$this->error('获取配置信息错误');
			}
			
			$pid = I('get.pid', 0);
			//获取父导航
			if (!empty($pid)) {
				$parent = M('Channel')->where(array('id' => $pid))->field('title')->find();
				$this->assign('parent', $parent);
			}
			
			$pnav = M('Channel')->where(array('pid' => '0'))->select();
			$this->assign('pnav', $pnav);
			$this->assign('pid', $pid);
			$this->assign('info', $info);
			$this->setMeta('编辑导航');
			$this->display();
		}
	}
	/**
	 * 删除频道
	 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
	 */
	public function del() {
		$id = array_unique((array)I('id', 0));
		
		if (empty($id)) {
			$this->error('请选择要操作的数据!');
		}
		
		$map = array('id' => array('in', $id));
		if (M('Channel')->where($map)->delete()) {
			//记录行为
			action_log('update_channel', 'channel', $id, UID);
			$this->success('删除成功');
		} 
		else {
			$this->error('删除失败！');
		}
	}
	/**
	 * 导航排序
	 * @author huajie <banhuajie@163.com>
	 */
	public function sort() {
		if (IS_GET) {
			$ids = I('get.ids');
			$pid = I('get.pid');
			//获取排序的数据
			$map = array('status' => array('gt', -1));
			if (!empty($ids)) {
				$map['id'] = array('in', $ids);
			} 
			else {
				if ($pid !== '') {
					$map['pid'] = $pid;
				}
			}
			$list = M('Channel')->where($map)->field('id,title')->order('sort asc,id asc')->select();
			
			$this->assign('list', $list);
			$this->setMeta('导航排序');
			$this->display();
		} 
		elseif (IS_POST) {
			$ids = I('post.ids');
			$ids = explode(',', $ids);
			foreach ($ids as $key => $value) {
				$res = M('Channel')->where(array('id' => $value))->setField('sort', $key + 1);
			}
			if ($res !== false) {
				$this->success('排序成功！');
			} 
			else {
				$this->error('排序失败！');
			}
		} 
		else {
			$this->error('非法请求！');
		}
	}

	public function setStatus(){
		$id = array_unique((array)I('ids', 0));
		$status = I('status','0','trim');
		
		if (empty($id)) {
			$this->error('请选择要操作的数据!');
		}
		
		$map = array('id' => array('in', $id));
		$result = M('Channel')->where($map)->save(array('status'=>$status));
		if ($result) {
			$this->success("操作成功！");
		}else{
			$this->error("操作失败！");
		}
	}
}
