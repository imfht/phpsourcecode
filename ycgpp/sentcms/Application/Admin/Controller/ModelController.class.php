<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;
use Admin\Model\AuthGroupModel;

class ModelController extends \Common\Controller\AdminController {
	
	/**
	 * 模型管理首页
	 * @author huajie <banhuajie@163.com>
	 */
	public function index() {
		$map = array('status' => array('gt', -1));
		$list = $this->lists('Model', $map);
		int_to_string($list);
		
		// 记录当前列表页的cookie
		Cookie('__forward__', $_SERVER['REQUEST_URI']);
		
		$this->assign('_list', $list);
		$this->setMeta('模型管理');
		$this->display();
	}
	
	/**
	 * 新增页面初始化
	 * @author huajie <banhuajie@163.com>
	 */
	public function add() {
		
		//获取所有的模型
		$models = M('Model')->where(array('extend' => 0))->field('id,title')->select();
		
		$this->assign('models', $models);
		$this->setMeta('新增模型');
		$this->display();
	}
	
	/**
	 * 编辑页面初始化
	 * @author huajie <banhuajie@163.com>
	 */
	public function edit() {
		$id = I('get.id', '');
		if (empty($id)) {
			$this->error('参数不能为空！');
		}
		
		/*获取一条记录的详细数据*/
		$Model = M('Model');
		$data = $Model->field(true)->find($id);
		if (!$data) {
			$this->error($Model->getError());
		}
		$data['attribute_list'] = empty($data['attribute_list']) ? '' : explode(",", $data['attribute_list']);
		$fields = M('Attribute')->where(array('model_id' => $data['id']))->getField('id,name,title,is_show', true);
		$fields = empty($fields) ? array() : $fields;
		
		// 是否继承了其他模型
		if ($data['extend'] == 1) {
			$extend_fields = M('Attribute')->where(array('model_id' => $data['extend']))->getField('id,name,title,is_show', true);
			$fields+= $extend_fields;
		}
		
		// 梳理属性的可见性
		foreach ($fields as $key => $field) {
			if (!empty($data['attribute_list']) && !in_array($field['id'], $data['attribute_list'])) {
				$fields[$key]['is_show'] = 0;
			}
		}
		
		// 获取模型排序字段
		$field_sort = json_decode($data['field_sort'], true);
		if (!empty($field_sort)) {
			foreach ($field_sort as $group => $ids) {
				foreach ($ids as $key => $value) {
					$fields[$value]['group'] = $group;
					$fields[$value]['sort'] = $key;
				}
			}
		}
		
		// 模型字段列表排序
		$fields = list_sort_by($fields, "sort");
		
		$this->assign('fields', $fields);
		$this->assign('info', $data);
		$this->setMeta('编辑模型');
		$this->display();
	}
	
	/**
	 * 删除一条数据
	 * @author huajie <banhuajie@163.com>
	 */
	public function del() {
		$res = D('Model')->del();
		if (!$res) {
			$this->error(D('Model')->getError());
		} else {
			$this->success('删除模型成功！');
		}
	}
	
	/**
	 * 更新一条数据
	 * @author huajie <banhuajie@163.com>
	 */
	public function update() {
		$res = D('Model')->update();
		if($res['status']){
			$this->success($res['info'] , U('index'));
		}else{
			$this->error($res['info']);
		}
	}
	
	/**
	 * 更新数据
	 * @author colin <colin@tensent.cn>
	 */
	public function status(){
		$map['id'] = I('post.ids') ? I('post.ids') : I('get.ids');
		if(null == $map['id'])$this->error('参数不正确！');

		$data['status'] = I('get.status');
		$model = D('Model');
		if(null == $data['status']){
			//实现单条数据数据修改
			$status = $model->where($map)->field('status')->find();
			$data['status'] = $status['status'] ? 0 : 1;
			$model->where($map)->save($data);
		}else{
			//实现多条数据同时修改
			$map['id'] = array('IN',$map['id']);
			$model->where($map)->save($data);
		}
		$this->success('状态设置成功！');
	}
}
