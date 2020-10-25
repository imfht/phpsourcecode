<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace User\Controller;

class ContentController extends \Common\Controller\UserController {

	public function _initialize(){
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
		$this->assign('model' , $this->modelInfo);
		$this->assign('model_id',$model_id);
		$this->assign('model_list',$list);
	}
	
	public function index(array $map){
		$map['uid'] = session('user_auth.uid');
		$map['status'] = 1;
		$info = $this->modelInfo;
		if($info['extend'] == 1){
			//如果继承了文档模型
			$list = D('Document')->where($map)->order('id desc')->select();
			foreach ($list as $key => $value) {
				$info = D('document_'.$this->modelInfo['name'])->where(array('id' => $value['id']))->find();
				$data[] = array_merge($value,$info);
			}
			$data = array_filter($data);
			$this->assign('list' , $data);
		}
		$this->display('Content/index');
	}

	public function add(int $model_id){
		$data = array(
			'meta' => '新增',
			'fieldGroup' => $this->getField($this->modelInfo),
			'savePostUrl' => U('update')
		);
		$this->assign($data);
		$this->display('Content/base_edit');
	}

	public function edit(){
		$map['id'] = I('get.id');
		$map['uid'] = session('user_auth.uid');
		//文档模型
		if($this->model['extend'] == 1){
			$list = D('Document')->where($map)->find();
			$info = D('document_'.$this->modelInfo['name'])->where($map)->find();
			$find = array_merge($list , $info);
			$data = array(
				'info' => $find,
				'meta' => '编辑',
				'fieldGroup' => $this->getField($this->modelInfo),
				'savePostUrl' => U('update')
			);
			$this->assign($data);
		}
		$this->display('Content/base_edit');
	}

	public function del(){
		$id = I('get.id');
		if(empty($id)){
			$id = array('IN' , implode(',', I('post.id')));
		}
		$map['uid'] = session('user_auth.uid');
		$document_extend = D('document_'.$this->model['name']);
		$document = D('Document');
		$find = $document_extend->field('id')->where(array('id' => $id))->select();
		if(empty($find[0])){
			$this->error('找不到此数据信息');
		}
		$find = $document->field('id')->where(array('id' => $id , 'uid' => session('user_auth.uid')))->select();
		if(empty($find[0])){
			$this->error('找不到此数据信息');
		}
		$document_extend->where(array('id' => $id))->delete();
		$document->where(array('id' => $id , 'uid' => session('user_auth.uid')))->delete();
		$this->success('删除成功！' , U('index'));
	}

	public function update(){
		if(IS_POST){
			$document = D('Document');
			$document_extend = D('document_'.$this->model['name']);
			$document_data = $document->create();
			if(!$document_data){
				$this->error($document->getError());
			}
			$document_extend_data = $document_extend->create();
			if(!$document_extend_data){
				$this->error($document_extend->getError());
			}
			$id = $document->add($document_data);
			if(empty($id)){
				$this->error('新增失败！');
			}
			$document_extend_data['id'] = $id;
			$id = $document_extend->add($document_extend_data);
			if(empty($id)){
				$this->error('新增失败！');
			}
			$this->success('新增成功！' , U('index'));
		}
	}
}