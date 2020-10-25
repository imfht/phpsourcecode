<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\controller\admin;

use think\facade\Db;
use app\model\Form as FormM;
use app\model\FormAttr;

/**
 * @title 自定义表单
 * @description 自定义表单
 */
class Form extends Base {

	public $modelInfo = [];
	public $model = null;
	
	public function initialize() {
		parent::initialize();
		$this->modelInfo = FormM::where('id', $this->request->param('form_id'))->find();
		$this->model = Db::name('Form'.ucfirst($this->modelInfo['name']));
	}

	/**
	 * @title 表单列表
	 */
	public function index() {
		$map   = [];
		
		$order = "id desc";

		$list  = FormM::where($map)->order($order)->paginate($this->request->pageConfig);

		$this->data = [
			'list' => $list,
			'page' => $list->render(),
		];
		return $this->fetch();
	}

	/**
	 * @title 添加表单
	 */
	public function add() {
		if ($this->request->isPost()) {
			$result = FormM::create($this->request->post());
			if (false !== $result) {
				return $this->success('添加成功！', url('/admin/form/index'));
			} else {
				return $this->error($this->model->getError());
			}
		} else {
			$this->data = array(
				'keyList' => (new FormM())->addField,
			);
			return $this->fetch('admin/public/edit');
		}
	}

	/**
	 * @title 编辑表单
	 */
	public function edit() {
		if ($this->request->isPost()) {
			$result = FormM::update($this->request->post(), array('id' => $this->request->param('id')));
			if (false !== $result) {
				return $this->success('修改成功！', url('/admin/form/index'));
			} else {
				return $this->error($this->model->getError());
			}
		} else {
			$info = FormM::where('id', $this->request->param('id'))->find();
			$this->data = array(
				'info'    => $info,
				'keyList' => (new FormM())->editField,
			);
			return $this->fetch('admin/public/edit');
		}
	}

	/**
	 * @title 删除表单
	 */
	public function del() {
		$id = $this->request->param('id', 0);

		if (!$id) {
			return $this->error('非法操作！');
		}

		$result = FormM::find($id)->delete();
		if ($result) {
			return $this->success('删除模型成功！');
		} else {
			return $this->error('删除失败！');
		}
	}

	/**
	 * @title       表单数据
	 * @description 表单数据
	 * @Author      molong
	 * @DateTime    2017-06-30
	 * @return      html        页面
	 */
	public function lists($form_id = '') {
		if(!$form_id){
			return $this->error('无此表单！');
		}
		$form = $this->modelInfo;

		$list = $this->model->order('id desc')->paginate(25);

		$this->data = array(
			'grid' => $this->modelInfo['grid_list'],
			'meta_title' => $this->modelInfo['title'] . '列表',
			'form_id'  => $form_id,
			'require' => ['jsname' => 'form', 'actionname' => 'lists'],
			'list'   => $list,
			'page'   => $list->render(),
		);
		return $this->fetch();
	}

	/**
	 * @title 数据详情
	 */
	public function detail($form_id = '', $id = ''){
		$form = $this->model->where('id', $form_id)->find();

		$info = M($form['name'], 'form')->where('id', $id)->find();

		$data = array(
			'info'   => $info
		);
		$this->assign($data);
		$this->setMeta('数据详情');
		return $this->fetch('detail_'.$form['name']);
	}

	/**
	 * @title 数据导出
	 */
	public function outxls($form_id = '') {
		$form = $this->model->where('id', $form_id)->find();

		$attr = FormAttr::where('form_id', $form_id)->where('is_show', 1)->select();
		foreach ($attr as $key => $value) {
			$title[$value['name']] = $value['title'];
		}

		$data = $list = $this->model->order('id desc')->select();

		$this->data['data'] = $data;
		return $this->data;
	}

	/**
	 * @title 表单字段
	 */
	public function attr($form_id = '') {
		$map   = [];
		$order = "id desc";

		$map[] = ['form_id', '=', $form_id];

		$list  = FormAttr::where($map)->order($order)->paginate(25);

		$this->data = array(
			'list'    => $list,
			'form_id' => $form_id,
			'page'    => $list->render(),
		);
		return $this->fetch();
	}

	/**
	 * @title 添加表单字段
	 */
	public function addattr(){
		$form_id = $this->request->param('form_id', '');
		if (!$form_id) {
			return $this->error('非法操作！');
		}
		if ($this->request->isPost()) {
			$data = $this->request->post();
			$result = FormAttr::create($data);
			if (false !== $result) {
				return $this->success('添加成功！', url('/admin/form/attr', ['form_id'=>$form_id]));
			}else{
				return $this->error($this->Fattr->getError());
			}
		}else{
			$this->data = array(
				'info'   => ['form_id' => $form_id],
				'keyList'   => $this->getField()
			);
			return $this->fetch('admin/public/edit');
		}
	}

	/**
	 * @title 编辑表单字段
	 */
	public function editattr(\think\Request $request){
		$param = $this->request->param();

		$form_id = isset($param['form_id']) ? $param['form_id'] : '';
		$id = isset($param['id']) ? $param['id'] : '';
		if (!$form_id || !$id) {
			return $this->error('非法操作！');
		}
		if ($this->request->isPost()) {
			$data = $request->post();
			$result = FormAttr::update($data, array('id'=>$data['id']));
			if (false !== $result) {
				return $this->success('修改成功！', url('/admin/form/attr', ['form_id'=>$form_id]));
			}else{
				return $this->error($this->Fattr->getError());
			}
		}else{
			$info = FormAttr::find($id);
			$this->data = array(
				'info'      => $info,
				'keyList'   => $this->getField()
			);
			return $this->fetch('admin/public/edit');
		}
	}

	/**
	 * @title 删除表单字段
	 */
	public function delattr(\think\Request $request){
		$id = $request->param('id', 0);
		if (!$id) {
			return $this->error('非法操作！');
		}
		$result = $this->Fattr->where('id', $id)->delete();
		if (false !== $result) {
			return $this->success('添加成功！');
		}else{
			return $this->error($this->Fattr->getError());
		}
	}

	protected function getField(){
		$config = \think\facade\Cache::get('system_config_data');
		return  array(
			array('name' => 'id', 'title' => 'id', 'help' => '', 'type' => 'hidden'),
			array('name' => 'form_id', 'title' => 'form_id', 'help' => '', 'is_must'=> true, 'type' => 'hidden'),
			array('name' => 'name', 'title' => '字段名', 'help' => '英文字母开头，长度不超过30', 'is_must'=> true, 'type' => 'text'),
			array('name' => 'title', 'title' => '字段标题', 'help' => '请输入字段标题，用于表单显示', 'is_must'=> true, 'type' => 'text'),
			array('name' => 'type', 'title' => '字段类型', 'help' => '用于表单中的展示方式', 'type' => 'select', 'option' => $config['config_type_list'], 'help' => ''),
			array('name' => 'length', 'title' => '字段长度', 'help' => '字段的长度值', 'type' => 'text'),
			array('name' => 'extra', 'title' => '参数', 'help' => '布尔、枚举、多选字段类型的定义数据', 'type' => 'textarea'),
			array('name' => 'value', 'title' => '默认值', 'help' => '字段的默认值', 'type' => 'text'),
			array('name' => 'remark', 'title' => '字段备注', 'help' => '用于表单中的提示', 'type' => 'text'),
			array('name' => 'is_show', 'title' => '是否显示', 'help' => '是否显示在表单中', 'type' => 'select', 'option' => [['key'=>'1', 'label' => '始终显示'], ['key' => '2', 'label' => '新增显示'], ['key' => '3', 'label' => '编辑显示'], ['key' => '0', 'label' => '不显示']], 'value' => 1),
			array('name' => 'is_must', 'title' => '是否必填', 'help' => '用于自动验证', 'type' => 'select', 'option' => array(['key'=>'0', 'label' => '否'], ['key'=>'1', 'label' => '是'])),
		);
	}
	/**
	 * @title 修改状态
	 * @author K先森 <77413254@qq.com>
	 */
	public function status() {
		$id = $this->request->param('form_id', 0);
		$status = input('status', '0', 'trim,intval');

		if (!$id) {
			return $this->error("非法操作！");
		}

		$map['id'] = array('IN', $id);
		$result    = FormM::where($map)->update(['status'=>$status]);
		if ($result) {
			return $this->success("设置成功！");
		} else {
			return $this->error("设置失败！");
		}
	} 	
}