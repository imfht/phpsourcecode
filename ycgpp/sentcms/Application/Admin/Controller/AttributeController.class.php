<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: colin <colin@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;

/**
 * 字段配置控制器
 * @author colin <colin@tensent.cn>
 */
class AttributeController extends \Common\Controller\AdminController{

	//保存的Model句柄
	protected $model;
	protected $attr;

	//初始化
	public function _initialize(){
		parent::_initialize();
		$this->model = D('Attribute');
		//遍历属性列表
		foreach (get_attribute_type() as $key => $value) {
			$this->attr[$key] = $value[0];
		}
		$this->validate_rule = array(
			0=>'请选择',
			'regex'=>'正则验证',
			'function'=>'函数验证',
			'unique'=>'唯一验证',
			'length'=>'长度验证',
			'in'=>'验证在范围内',
			'notin'=>'验证不在范围内',
			'between'=>'区间验证',
			'notbetween'=>'不在区间验证'
		);
		$this->auto_type = array(0=>'请选择','function'=>'函数','field'=>'字段','string'=>'字符串');
		$this->the_time = array(0=>'请选择','3'=>'始 终','1'=>'新 增','2'=>'编 辑');
		$this->field = $this->getField();
	}


	/**
	 * index方法
	 * @author colin <colin@tensent.cn>
	 */
	public function index($model = null){
		$model_id = I('get.model_id','','trim,intval');
		$map['model_id'] = $model_id;
		if (!$model_id) {
			$this->error("非法操作！");
		}

		$count = $this->model->where($map)->count();
		$page = new \Think\Page($count,25);
		$list = $this->model->where($map)->limit($page->firstRow,$page->listRows)->order('id desc')->select();

		$data = array(
			'list'   => $list,
			'model_id'=> $model_id,
			'page'   => $page->show()
		);
		$this->assign($data);
		$this->setMeta('字段管理');
		$this->display();
	}

	/**
	 * 创建字段
	 * @author colin <colin@tensent.cn>
	 */
	public function add(){
		$model_id = I('get.model_id','','trim,intval');
		if(IS_POST){
			$result = $this->model->update();
			if ($result) {
				$this->success("创建成功！",U('Attribute/index',array('model_id'=>$model_id)));
			}else{
				$this->error($this->model->getError());
			}
		}else{
			if (!$model_id) {
				$this->error('非法操作！');
			}
			$data = array(
				'info'        => array('model_id'=>$model_id),
				'fieldGroup'  => $this->field
			);
			$this->assign($data);
			$this->setMeta('添加字段');
			$this->display('Public/edit');
		}
	}

	/**
	 * 编辑字段方法
	 * @author colin <colin@tensent.cn>
	 */
	public function edit(){
		if(IS_POST){
			$result = $this->model->update();
			if ($result) {
				$this->success("修改成功！",U('Attribute/index',array('model_id'=>$_POST['model_id'])));
			}else{
				$this->error($this->model->getError());
			}
		}else{
			$id = I('get.id','','trim,intval');
			$info = $this->model->find($id);
			$data = array(
				'info'        => $info,
				'fieldGroup'  => $this->field
			);
			$this->assign($data);
			$this->setMeta('编辑字段');
			$this->display('Public/edit');
		}
	}

	/**
	 * 删除字段信息
	 * @var delattr 是否删除字段表里的字段
	 * @author colin <colin@tensent.cn>
	 */
	public function del(){
		$id = I('id','','trim,intval');
		if (!$id) {
			$this->error("非法操作！");
		}

		$result = $this->model->del($id);
		if ($result) {
			$this->success("删除成功！");
		}else{
			$this->error($this->model->getError());
		}
	}

	//字段编辑所需字段
	protected function getField(){
		return array(
			'基础' => array(
				array('name'=>'id','title'=>'id','subtitle'=>'','type'=>'hidden'),
				array('name'=>'model_id','title'=>'model_id','subtitle'=>'','type'=>'hidden'),
				array('name'=>'name','title'=>'字段名','subtitle'=>'英文字母开头，长度不超过30','type'=>'text'),
				array('name'=>'title','title'=>'字段标题','subtitle'=>'请输入字段标题，用于表单显示','type'=>'text'),
				array('name'=>'type','title'=>'字段类型','subtitle'=>'用于表单中的展示方式','type'=>'select','opt'=>$this->attr),
				array('name'=>'length','title'=>'字段长度','subtitle'=>'字段的长度值','type'=>'text'),
				array('name'=>'extra','title'=>'参数','subtitle'=>'布尔、枚举、多选字段类型的定义数据','type'=>'textarea'),
				array('name'=>'value','title'=>'默认值','subtitle'=>'字段的默认值','type'=>'text'),
				array('name'=>'remark','title'=>'字段备注','subtitle'=>'用于表单中的提示','type'=>'text'),
				array('name'=>'is_show','title'=>'是否显示','subtitle'=>'是否显示在表单中','type'=>'select','opt'=>array('1'=>'始终显示','2'=>'新增显示','3'=>'编辑显示','0'=>'不显示'),'value'=>1),
				array('name'=>'is_must','title'=>'是否必填','subtitle'=>'用于自动验证','type'=>'select','opt'=>array('0'=>'否','1'=>'是')),
			),
			'高级' => array(
				array('name'=>'validate_type','title'=>'验证方式','type'=>'select','opt'=>$this->validate_rule),
				array('name'=>'validate_rule','title'=>'验证规则','subtitle'=>'根据验证方式定义相关验证规则','type'=>'text'),
				array('name'=>'error_info','title'=>'出错提示','type'=>'text'),
				array('name'=>'validate_time','title'=>'验证时间','subtitle'=>'英文字母开头，长度不超过30','type'=>'select','opt'=>$this->the_time),
				array('name'=>'auto_type','title'=>'自动完成方式','subtitle'=>'英文字母开头，长度不超过30','type'=>'select','opt'=>$this->auto_type),
				array('name'=>'auto_rule','title'=>'自动完成规则','subtitle'=>'根据完成方式订阅相关规则','type'=>'text'),
				array('name'=>'auto_time','title'=>'自动完成时间','subtitle'=>'英文字母开头，长度不超过30','type'=>'select','opt'=>$this->the_time),
			),
		);
	}
}