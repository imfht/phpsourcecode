<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;

class GroupController extends \Common\Controller\AdminController  {

	public function _initialize(){
		parent::_initialize();
		$this->model = D('AuthGroup');
		$this->rule = D('AuthRule');

		$this->nodeKey = array(
			array('name'=>'id','title'=>'标识','type'=>'hidden'),
			array('name'=>'module','title'=>'所属模块','type'=>'hidden'),
			array('name'=>'title','title'=>'节点名称','type'=>'text'),
			array('name'=>'name','title'=>'节点标识','type'=>'text'),
			array('name'=>'group','title'=>'功能组','type'=>'text','help'=>'功能分组'),
			array('name'=>'status','title'=>'状态','type'=>'select','opt'=>array('1'=>'启用','0'=>'禁用')),
			array('name'=>'condition','title'=>'条件','type'=>'text')
		);

		$this->groupKey = array(
			array('name'=>'id','title'=>'标识','type'=>'hidden'),
			array('name'=>'module','title'=>'所属模块','type'=>'hidden'),
			array('name'=>'title','title'=>'分组名称','type'=>'text'),
			array('name'=>'description','title'=>'分组描述','type'=>'textarea'),
			array('name'=>'status','title'=>'分组状态','type'=>'select','opt'=>array('1'=>'启用','0'=>'禁用')),
		);
	}


	//会员分组首页控制器
	public function index(){
		$type  = I('get.type','admin','trim');

		$map['module'] = $type;

		$count = $this->model->where($map)->count();
		$page = new \Think\Page($count,25);
		$list = $this->model->where($map)->limit()->order('id desc')->select();

		$data = array(
			'list'   => $list,
			'page'   => $page->show(),
			'type'   => $type
		);
		$this->assign($data);
		$this->setMeta('用户组管理');
		$this->display();
	}

	//会员分组添加控制器
	public function add(){
		$type  = I('get.type','admin','trim');
		if (IS_POST) {
			$result = $this->model->update();
			if ($result) {
				$this->success("添加成功！");
			}else{
				$this->error("添加失败！");
			}
		}else{
			$data = array(
				'info' => array('module' => $type,'status' => 1),
				'keyList' => $this->groupKey
			);
			$this->assign($data);
			$this->setMeta('添加用户组');
			$this->display('Public/edit');
		}
	}

	//会员分组编辑控制器
	public function edit(){
		if (IS_POST) {
			$result = $this->model->update();
			if ($result) {
				$this->success("编辑成功！");
			}else{
				$this->error("编辑失败！");
			}
		}else{
			$id = I('id','','trim,intval');
			if (!$id) {
				$this->error("非法操作！");
			}
			$info = $this->model->find($id);
			$data = array(
				'info' => $info,
				'keyList' => $this->groupKey
			);
			$this->assign($data);
			$this->setMeta('编辑用户组');
			$this->display('Public/edit');
		}
	}

	//会员分组编辑字段控制器
	public function editable(){
		$pk = I('pk','','trim,intval');
		$name = I('name','','trim');
		$value = I('value','','trim');
		$result = $this->model->where(array('id'=>$pk))->setField($name,$value);
		if ($result) {
			$this->success("删除成功！");
		}else{
			$this->error("删除失败！");
		}
	}

	//会员分组删除控制器
	public function del(){
		$id = I('id','','trim,intval');
		if (!$id) {
			$this->error("非法操作！");
		}
		$this->model->where(array('id'=>$id))->delete();
		if ($result) {
			$this->success("删除成功！");
		}else{
			$this->error("删除失败！");
		}
	}

	//权限节点控制器
	public function node(){
		$type  = I('get.type','admin','trim');

		$map['module'] = $type;

		$count = $this->rule->where($map)->count();
		$page = new \Think\Page($count,25);
		$list = $this->rule->where($map)->limit($page->firstRow,$page->listRows)->order('id desc')->select();

		$data = array(
			'list'   => $list,
			'page'   => $page->show(),
			'type'   => $type
		);
		$this->assign($data);
		$this->setMeta('权限节点');
		$this->display();
	}

	//根据菜单更新节点
	public function upnode(){
		$type  = I('get.type','admin','trim');
		if ($type == 'admin') {
			$rule = $this->returnNodes(false);
			foreach ($rule as $key => $value) {
				$data = array(
					'module'  => 'admin',
					'type'   => 2,
					'name' => $value['url'],
					'title' => $value['title'],
					'group' => $value['group'],
					'status' => 1
				);
				$data = $this->rule->create($data);
				$id = $this->rule->where(array('name'=>$data['name']))->getField('id');
				if ($id) {
					$data['id'] = $id;
					$result = $this->rule->save($data);
				}else{
					$result = $this->rule->add($data);
				}
			}
			if ($result) {
				$this->success("更新成功！");
			}else{
				$this->error("更新失败！");
			}
		}else{
			$this->success("更新成功！");
		}
	}

	public function auth(){
		if (IS_POST) {
			$id = I('id','','trim,intval');
			$rule = I('rule','','intval');
			if (!$id) {
				$this->error("非法操作！");
			}
			$rules = implode(',', $rule);
			$result = $this->model->where(array('id'=>$id))->setField('rules',$rules);
			if ($result !== false) {
				$this->success("授权成功！");
			}else{
				$this->error("授权失败！");
			}
		}else{
			$id  = I('get.id','','trim');
			if (!$id) {
				$this->error("未选择分组！");
			}
			$group = $this->model->where(array('id'=>$id))->find();

			$map['module'] = $group['module'];
			$row = $this->rule->where($map)->order('id desc')->select();

			foreach ($row as $key => $value) {
				$list[$value['group']][] = $value;
			}

			$data = array(
				'list'   => $list,
				'auth_list'  => explode(',', $group['rules']),
				'id'     => $id
			);
			$this->assign($data);
			$this->setMeta('授权');
			$this->display();
		}
	}

	public function addnode(){
		$type = I('get.type','admin','trim');

		if (IS_POST) {
			$result = $this->rule->update();
			if ($result) {
				$this->success("创建成功！");
			}else{
				$this->error($this->rule->getError());
			}
		}else{
			$data = array(
				'info' => array('module' => $type,'status' => 1),
				'keyList' => $this->nodeKey
			);
			$this->assign($data);
			$this->setMeta('添加节点');
			$this->display('Public/edit');
		}
	}

	public function editnode(){
		if (IS_POST) {
			$result = $this->rule->update();
			if ($result) {
				$this->success("更新成功！");
			}else{
				$this->error("更新失败！");
			}
		}else{
			$id = I('id','','trim,intval');
			if (!$id) {
				$this->error("非法操作！");
			}
			$info = $this->rule->find($id);
			$data = array(
				'info'  => $info,
				'keyList' => $this->nodeKey
			);
			$this->assign($data);
			$this->setMeta('编辑节点');
			$this->display('Public/edit');
		}
	}

	public function delnode(){
		$id = I('id','','trim,intval');
		if (!$id) {
			$this->error("非法操作！");
		}
		$this->rule->where(array('id'=>$id))->delete();
		if ($result) {
			$this->success("删除成功！");
		}else{
			$this->error("删除失败！");
		}
	}
}