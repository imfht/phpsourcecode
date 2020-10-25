<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\controller\admin;

use app\model\Category as CategoryM;
use app\model\Attribute;
use app\model\Model;
use app\model\Channel;

/**
 * @title 栏目管理
 * @description 分类管理
 */
class Category extends Base {

	public function initialize() {
		parent::initialize();
		$this->getContentMenu();
	}

	/**
	 * @title 栏目列表
	 */
	public function index(CategoryM $category, Attribute $attr, Model $model) {
		$param = $this->request->param();
		$map = [];

		$map[]  = ['status', '>', -1];
		if (isset($param['model_id']) && $param['model_id']) {
			$map[] = ['model_id', '=', $param['model_id']];
		}
		$list = $category->where($map)->order('sort asc,id asc')->select();

		if (!empty($list)) {
			$tree = new \sent\tree\Tree();
			$list = $tree->toFormatTree($list->toArray());
		}
		$model_list = Model::where('id', 'IN', function($query){
			$query->name('Attribute')->where('name', 'category_id')->field('model_id');
		})->select();

		$this->data = [
			'tree'  => $list,
			'model_id' => isset($param['model_id']) ? $param['model_id'] : 0,
			'model_list' => $model_list
		];
		return $this->fetch();
	}

	/**
	 * @title 编辑字段
	 */
	public function editable() {
		$name = $this->request->param('name', '');
		$value = $this->request->param('value', '');
		$pk = $this->request->param('pk', '');

		if ($name && $value && $pk) {
			$save[$name] = $value;
			CategoryM::update($save, ['id' => $pk]);
		}
	}

	/**
	 * @title 编辑分类
	 */
	public function edit($id = null, $pid = 0) {
		if ($this->request->isPost()) {
			$data = $this->request->post();

			$result = CategoryM::update($data, ['id' => $data['id']]);
			if (false !== $result) {
				return $this->success('修改成功！', url('/admin/category/index'));
			} else {
				return $this->error('修改失败！');
			}
		} else {
			$category = CategoryM::getCategoryTree();

			$model_list = Model::where('id', 'IN', function($query){
				$query->name('Attribute')->where('name', 'category_id')->field('model_id');
			})->select();

			/* 获取分类信息 */
			$info = $id ? CategoryM::find($id) : [];

			$this->data = [
				'info' => $info,
				'model_list' => $model_list,
				'category' => $category
			];
			return $this->fetch();
		}
	}

	/**
	 * @title 添加分类
	 */
	public function add() {
		if ($this->request->isPost()) {
			//提交表单
			$data = $this->request->post();

			$result = CategoryM::create($data);
			if (false !== $result) {
				return $this->success('新增成功！', url('/admin/category/index'));
			} else {
				return $this->error('添加失败！');
			}
		} else {
			$pid = $this->request->param('pid', 0);

			$category = CategoryM::getCategoryTree();

			$model_list = Model::where('id', 'IN', function($query){
				$query->name('Attribute')->where('name', 'category_id')->field('model_id');
			})->select();
			
			/* 获取分类信息 */
			$this->data = [
				'info'  => ['pid' => $pid],
				'model_list' => $model_list,
				'category' => $category
			];
			return $this->fetch('edit');
		}
	}
	/**
	 * @title 删除分类
	 * @author huajie <banhuajie@163.com>
	 */
	public function remove($id) {
		if (empty($id)) {
			return $this->error('参数错误!');
		}
		//判断该分类下有没有子分类，有则不允许删除
		$child = CategoryM::where('pid', $id)->field('id')->select();

		if (!$child->isEmpty()) {
			return $this->error('请先删除该分类下的子分类');
		}

		//删除该分类信息
		$result = CategoryM::where('id', $id)->delete();
		if ($result !== false) {
			return $this->success('删除分类成功！');
		} else {
			return $this->error('删除分类失败！');
		}
	}

	/**
	 * 移动/合并分类
	 * @param string $type
	 * @author huajie <banhuajie@163.com>
	 */
	public function operate($type = 'move', $from = '') {
		$map = [];
		$map[] = ['status', '=', 1];
		//检查操作参数
		if ($type == 'move') {
			$operate = '移动';
		} elseif ($type == 'merge') {
			$operate = '合并';
		} else {
			return $this->error('参数错误！');
		}

		if (empty($from)) {
			return $this->error('参数错误！');
		}else{
			$map[] = ['id', '<>', $from];
		}

		//获取分类
		$list = CategoryM::where($map)->field('id,pid,title')->select();

		$this->data = [
			'type' => $type,
			'operate' => $operate,
			'from' => $from,
			'list' => $list
		];
		return $this->fetch();
	}
	
	/**
	 * @title 移动分类
	 * @author huajie <banhuajie@163.com>
	 */
	public function move() {
		$to   = input('post.to');
		$from = input('post.from');
		$res  = db('Category')->where(array('id' => $from))->setField('pid', $to);
		if ($res !== false) {
			return $this->success('分类移动成功！', url('index'));
		} else {
			return $this->error('分类移动失败！');
		}
	}

	/**
	 * @title 合并分类
	 * @author huajie <banhuajie@163.com>
	 */
	public function merge() {
		$to    = input('post.to');
		$from  = input('post.from');
		$Model = model('Category');
		//检查分类绑定的模型
		$from_models = explode(',', $Model->getFieldById($from, 'model'));
		$to_models   = explode(',', $Model->getFieldById($to, 'model'));
		foreach ($from_models as $value) {
			if (!in_array($value, $to_models)) {
				return $this->error('请给目标分类绑定' . get_document_model($value, 'title') . '模型');
			}
		}
		//检查分类选择的文档类型
		$from_types = explode(',', $Model->getFieldById($from, 'type'));
		$to_types   = explode(',', $Model->getFieldById($to, 'type'));
		foreach ($from_types as $value) {
			if (!in_array($value, $to_types)) {
				$types = config('document_model_type');
				return $this->error('请给目标分类绑定文档类型：' . $types[$value]);
			}
		}
		//合并文档
		$res = db('Document')->where(array('category_id' => $from))->setField('category_id', $to);

		if ($res !== false) {
			//删除被合并的分类
			$Model->delete($from);
			return $this->success('合并分类成功！', url('index'));
		} else {
			return $this->error('合并分类失败！');
		}
	}

	/**
	 * @title 修改状态
	 * @author huajie <banhuajie@163.com>
	 */
	public function status() {
		$id = $this->request->param('id', 0);
		$status = $this->request->param('status', 0);
		$map = [];
		if (is_array($id)) {
			$map[] = ['id', 'IN', $id];
		}else{
			$map[] = ['id', '=', $id];
		}

		$result = CategoryM::update(['status'=> $status], $map);
		if ($result !== false) {
			return $this->success('操作成功！');
		} else {
			return $this->error('操作失败！');
		}
	}
	
	/**
	 * @title 生成频道
	 * @author huajie <banhuajie@163.com>
	 */
	public function add_channel() {
		if ($this->request->isPost()) {
			$data    = $this->request->param();
			if ($data) {
				$mid = $data['id'];
				$data['status'] = 1;
				unset($data['id']);
				$channel = Channel::create($data);
				if ($channel->id) {
					$result  = CategoryM::update(['ismenu'=>$channel->id], ['id' => $mid]);                                      
					return $this->success('生成成功',url('/admin/category/index'));
				} else {
					return $this->error('生成失败');
				}
			} else {
				$this->error($Channel->getError());
			}
		} else {
			$data    = $this->request->param();
			$info = CategoryM::where('id', $data['id'])->field('id, title,model_id,pid')->find()->toArray();
			$modelname = Model::where('id', $data['model_id'])->value('name');
			$info['url'] = '/' . $modelname.'/list/'.$data['id'];
			$data['pid'] = CategoryM::where('id', $info['pid'])->value('ismenu');
			$data['pid'] = isset($data['pid']) ? $data['pid'] : 0;
			
			$this->data = [
				'info'  => $info
			];
			return $this->fetch('admin/channel/edit');
		}
	}
}