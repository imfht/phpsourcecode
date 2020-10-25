<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;

class ContentController extends \Common\Controller\AdminController {
	
	protected function _initialize(){
		parent::_initialize();
		$model_id = I('model_id','1','trim,intval');
		$list = D('Model')->index('id')->select();

		if (empty($list[$model_id])) {
			$this->error("无此模型！");
		}else {
			$this->modelInfo = $list[$model_id];
			if ($this->modelInfo['extend'] > 1) {
				$this->model = D(ucwords($this->modelInfo['name']));
			}else{
				$this->model = D('Document');
			}
		}

		$this->assign('model_id',$model_id);
		$this->assign('model_list',$list);
	}

	public function index() {
		$grid_list = get_grid_list($this->modelInfo['list_grid']);
		$order = "id desc";

		if ($this->modelInfo['id'] > 1) {
			$map['model_id'] = $this->modelInfo['id'];
		}

		$count = $this->model->where($map)->count();
		$page = new \Think\Page($count,25);
		$list = $this->model->where($map)->field(array_filter($grid_list['fields']))->limit($page->firstRow,$page->listRows)->order($order)->select();
		
		$data = array(
			'grid'  => $grid_list,
			'list'  => $list,
			'page'  => $page->show()
		);
		$this->setMeta("数据列表");
		$this->assign($data);
		$this->display();
	}

	public function add(){
		if (IS_POST) {
			$result = $this->model->update();
			if ($result) {
				$this->success("添加成功！",U('Content/index',array('model_id'=>$this->modelInfo['id'])));
			}else{
				$this->error($this->model->getError());
			}
		}else{
			$info = array(
				'model_id'   => $this->modelInfo['id']
			);
			$data = array(
				'info'   => $info,
				'fieldGroup' => $this->getField($this->modelInfo)
			);
			$this->assign($data);
			$this->setMeta("添加".$this->modelInfo['title']);
			$this->display('Public/edit');
		}
	}

	public function edit(){
		if (IS_POST) {
			$result = $this->model->update();
			if ($result) {
				$this->success("更新成功！",U('Content/index',array('model_id'=>$this->modelInfo['id'])));
			}else{
				$this->error($this->model->getError());
			}
		}else{
			$id = I('get.id','','trim,intval');
			if (!$id) {
				$this->error("非法操作！");
			}
			$info = $this->model->detail($id);
			if (!$info) {
				$this->error($this->model->getError());
			}
			$info['model_id'] = $this->modelInfo['id'];
			$data = array(
				'info'   => $info,
				'fieldGroup' => $this->getField($this->modelInfo)
			);
			$this->assign($data);
			$this->setMeta("编辑".$this->modelInfo['title']);
			$this->display('Public/edit');
		}
	}

	public function del(){
		$id = I('get.id','','trim');

		$map['id'] = $id;
		$result = $this->model->where($map)->delete();
		if ($result) {
			$this->success("删除成功！");
		}else{
			$this->error("删除失败！！");
		}
	}

	public function setstatus(){
		$id = I('get.id','','trim,intval');
		$status = I('get.status','','trim,intval');

		$map['id'] = $id;
		$result = $this->model->where($map)->setField('status',$status);
		if ($result) {
			$this->success("操作成功！");
		}else{
			$this->error("操作失败！！");
		}
	}

	protected function getField(){
		$attr = D('Attribute');
		$field_group = parse_config_attr($this->modelInfo['field_group']);
		$field_sort = json_decode($this->modelInfo['field_sort'],true);

		if ($this->modelInfo['extend'] > 1) {
			$map['model_id'] = $this->modelInfo['id'];
		}else{
			$model_id[] = $this->modelInfo['id'];
			$model_id[] = 1;
			$map['model_id'] = array('IN',$model_id);
		}
		if (ACTION_NAME == 'add') {
			$map['is_show'] = array('in',array('1','2'));
		}elseif(ACTION_NAME == 'edit'){
			$map['is_show'] = array('in',array('1','3'));
		}

		//获得数组的第一条数组
		$first_key = array_keys($field_group);
		$fields = $attr->getFields($map);
		if (!empty($field_sort)) {
			foreach ($field_sort as $key => $value) {
				foreach ($value as $index) {
					$groupfield[$key][] = $fields[$index];
					unset($fields[$index]);
				}
			}
		}
		//未进行排序的放入第一组中
		$fields[] = array('name'=>'model_id','type'=>'hidden');    //加入模型ID值
		$fields[] = array('name'=>'id','type'=>'hidden');    //加入模型ID值
		foreach ($fields as $key => $value) {
			$groupfield[$first_key[0]][] = $value;
		}

		foreach ($field_group as $key => $value) {
			if ($groupfield[$key]) {
				$data[$value] = $groupfield[$key];
			}
		}
		return $data;
	}
}
