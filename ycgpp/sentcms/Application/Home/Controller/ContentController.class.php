<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;

class ContentController extends \Common\Controller\FrontController {

	protected function _initialize(){
		parent::_initialize();
		$model_name = strtolower(CONTROLLER_NAME);
		$list = D('Model')->index('name')->select();

		if (empty($list[$model_name])) {
			$this->error("无此模型！");
		}else {
			$this->modelInfo = $list[$model_name];
			if ($this->modelInfo['extend'] > 1) {
				$this->model = D($this->modelInfo['name']);
			}else{
				$this->model = D('Document');
			}
		}

		$this->assign('model_id',$model_id);
		$this->assign('model_list',$list);
	}

	//模块频道首页
	public function index(){
		$id = I('id','','trim,intval');
		$name = I('name','','trim');
		if ($name) {
			$id = D('Category')->where(array('name'=>$name))->getField('id');
		}

		if (!$id) {
			$this->error("无此频道！");
		}

		$cate = $this->getCategory($id);

		//获得当前栏目的所有子栏目
		$ids = get_category_child($id);

		$data = array(
			'category'  => $cate,
			'child_cate' => $ids
		);
		if ($cate['template_index']) {
			$teamplate = 'Content/'.$this->modelInfo['name'].'/'.$cate['template_index'];
		}else{
			$teamplate = 'Content/'.$this->modelInfo['name'].'/index';
		}
		$this->assign($data);
		$this->display($teamplate);
	}

	//模块列表页
	public function lists(){
		$id = I('id','','trim,intval');
		$name = I('name','','trim');
		if ($name) {
			$id = D('Category')->where(array('name'=>$name))->getField('id');
		}

		if (!$id) {
			$this->error("无此栏目！");
		}
		
		$cate = $this->getCategory($id);

		//获得当前栏目的所有子栏目
		$ids = get_category_child($id);
		$map['category_id'] = array('IN',$ids);

		$count = $this->model->where($map)->count();
		$page = new \Think\Page($count,30);

		$list = $this->model->where($map)->limit($page->firstRow,$page->listRows)->order('id desc')->select();

		$data = array(
			'list'    => $list,
			'page'    => $page
		);

		if ($cate['template_lists']) {
			$teamplate = 'Content/'.$this->modelInfo['name'].'/'.$cate['template_lists'];
		}else{
			$teamplate = 'Content/'.$this->modelInfo['name'].'/list';
		}
		$this->assign($data);
		$this->display($teamplate);
	}

	//模块内容详情页
	public function detail(){
		$id = I('id','','trim,intval');
		$name = I('name','','trim');

		if ($this->modelInfo['extend'] > 1) {
			//当为独立模型时
			$info = $this->model->find($id);
		}else{
			//当为文章模型时
			$info = $this->model->detail($id);
		}
		
		$data = array(
			'info'    => $info
		);
		if ($name) {
			$teamplate = 'Content/'.$this->modelInfo['name'].'/'.$name;
		}else{
			$teamplate = 'Content/'.$this->modelInfo['name'].'/detail';
		}
		$this->assign($data);
		$this->display($teamplate);	
	}

	protected function getCategory($id){
		$data = D('Category')->find($id);
		return $data;
	}
}