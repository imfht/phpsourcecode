<?php

// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;
use Common\Api\UserApi;

class UserExtendsController extends \Common\Controller\AdminController{
	public function index(){
		$list = $this->lists('MemberExtendGroup');
		$this->assign('_list' , $list);
		$this->setMeta('扩展列表');
		$this->display();
	}

	/**
	 * 字段管理
	 */
	public function fields($id = null){
		if(!empty($id)){
			$map['extend_id'] = $id;
		}
		$list = $this->lists('MemberExtendSetting' , $map);
		$this->setMeta('扩展字段');
		$this->assign('_list' , $list);
		$this->display();
	}

	/**
	 * 新增字段
	 */
	public function createfields($id = null){
		$find = D('MemberExtendGroup')->selectone($id);
		if(empty($find)){
			$this->error('没有此扩展！');
		}
		$data = array(
			'info' => array('extend_id' => $id),
			'keyList' => $this->getFields(),
			'savePostUrl' => U('updatefields')
		);
		$this->setMeta('创建字段');
		$this->assign($data);
		$this->display('Public/edit');
	}
	
	/**
	 * 修改字段
	 */
	public function editfields($id = null){
		$find = D('MemberExtendSetting')->where(array('id' => $id))->find();
		$find['oldname'] = $find['name'];
		if(empty($find)){
			$this->error('不存在此字段！');
		}
		$data = array(
			'info' => $find,
			'keyList' => $this->getFields(),
			'savePostUrl' => U('updatefields')
		);
		$this->setMeta('更新字段');
		$this->assign($data);
		$this->display('Public/edit');
	}
	
	/**
	 * 删除字段
	 */
	public function delfields($id = null){
		$find = D('MemberExtendSetting')->field('name')->where(array('id' => $id))->find();
		if(empty($find)){
			$this->error('不存在此字段！');
		}
		$status = D('MemberExtendSetting')->del_field($find['name']);
		if(!$status){
			$this->error('删除字段失败！');
		}
		$this->success('删除字段成功！' , U('fields' , array('id' => $find['extend_id'])));
	}

	/**
	 * 更新字段
	 */
	public function updatefields(){
		$model = D('MemberExtendSetting');
		$data = $model->create();
		if(!$data) $this->error($model->getError());
		//处理类型
		if(empty($data['extend_id'])){
			$this->error('找不到扩展分组！');
		}
		$attribute = get_attribute_type();
		$data['type_string'] = $attribute[$data['type']][1];
		$oldname = I('post.oldname');
		if(!empty($oldname)){
			$data['oldname'] = $oldname;
		}
		$status = $model->update($data);
		$msg = empty($data['id']) ? array('error' => '创建字段失败' , 'success' => '创建字段成功') : array('error' => '更新字段失败' , 'success' => '更新字段成功');
		if(!$status){
			$this->error($msg['success']);
		}
		$this->success($msg['success'] , U('fields' , array('id' => $data['extend_id'])));
	}

	/**
	 * 创建分组
	 */
	public function create(){
		$this->setMeta('创建扩展');
		$this->display();
	}

	/**
	 * 删除分组
	 */
	public function del($id){
		$id = I('id');
		$status = D('MemberExtendGroup')->remove($id);
		if(!$status){
			$this->error('删除失败');
		}
		$this->success('删除成功' , U('index'));
	}

	/**
	 * 更新分组
	 */
	public function edit($id){
		$find = D('MemberExtendGroup')->where(array('id' => $id))->find();
		$this->assign('find' , $find);
		$this->setMeta('创建扩展');
		$this->display('create');
	}

	/**
	 * 更新分组
	 */
	public function update(){
		if(IS_POST){
			$model = D('MemberExtendGroup');
			$data = $model->create();
			$status = $model->update($data);
			$msg = empty($data['id']) ? array('error' => '创建失败' , 'success' => '创建成功') : array('error' => '更新失败' , 'success' => '更新成功');
			if(!$status){
				$this->error($msg['success']);
			}
			$this->success($msg['success'] , U('index'));
		}
	}

	/**
	 * 字段信息
	 */
	public function getFields(){
		foreach (get_attribute_type() as $key => $value) {
			$attribute_type[$key] = $value[0];
		}
		return array(
			array('name'=>'extend_id','title'=>'id','subtitle'=>'','type'=>'hidden'),
			array('name'=>'id','title'=>'id','subtitle'=>'','type'=>'hidden'),
			array('name'=>'oldname','title'=>'id','subtitle'=>'','type'=>'hidden'),
			array('name'=>'name','title'=>'字段名','subtitle'=>'英文字母开头，长度不超过30','type'=>'text'),
			array('name'=>'title','title'=>'字段标题','subtitle'=>'请输入字段标题，用于表单显示','type'=>'text'),
			array('name'=>'type','title'=>'字段类型','subtitle'=>'用于表单中的展示方式','type'=>'select' , 'opt' => $attribute_type),
			array('name'=>'length','title'=>'字段长度','subtitle'=>'字段的长度值','type'=>'text'),
			array('name'=>'extra','title'=>'参数','subtitle'=>'布尔、枚举、多选字段类型的定义数据','type'=>'textarea'),
			array('name'=>'value','title'=>'默认值','subtitle'=>'字段的默认值','type'=>'text'),
			array('name'=>'remark','title'=>'字段备注','subtitle'=>'用于表单中的提示','type'=>'text'),
			array('name'=>'is_show','title'=>'是否显示','subtitle'=>'是否显示在表单中','type'=>'select','opt'=>array('隐藏' , '显示'),'value'=>1),
				array('name'=>'is_must','title'=>'是否必填','subtitle'=>'用于自动验证','type'=>'select','opt'=>array('0'=>'否','1'=>'是')),
		);
	}
}