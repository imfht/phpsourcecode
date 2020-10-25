<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\controller\front;

use think\facade\Db;
use \app\model\Category;
use \app\model\Model;
use \app\model\Content as ContentModel;

class Content extends Base {

	public $modelInfo = [];
	public $model = null;
	
	/**
	 * @title 内容频道页
	 * @return [type] [description]
	 */
	public function index() {
		$param = $this->request->param();
		$this->setModel();
		$order = "id desc";
		$map = [];

		if (isset($param['keyword']) && $param['keyword'] != '') {
			$map[] = ['title', 'LIKE', '%'.$param['keyword'].'%'];
		}

		$category = Category::where('model_id', $this->modelInfo['id'])->column("*", "id");

		$list = $this->model->where($map)->order($order)->paginate($this->request->pageConfig);

		$teamplate = 'front@content/' . $this->modelInfo['name'] . '/index';

		$this->data = [
			'model' => $this->modelInfo,
			'category' => $category,
			'list' => $list,
			'page' => $list->render()
		];

		$this->setSeo($this->modelInfo['title'] . '频道', $this->modelInfo['title'] . '频道', $this->modelInfo['title'] . '频道');
		return $this->fetch($teamplate);
	}

	/**
	 * @title 内容列表
	 * @return [type] [description]
	 */
	public function lists() {
		$param = $this->request->param();
		$this->setModel();
		$order = "id desc";
		$map = [];

		$category = Category::where('model_id', $this->modelInfo['id'])->column("*", "id");

		//当前栏目
		$cate = isset($category[$param['id']]) ? $category[$param['id']] : [];
		if(empty($cate)){
			return $this->error("当前栏目不能为空或无此栏目！");
		}

		$ids = (new \sent\tree\Tree())->getChilds($category, (int) $param['id']);
		array_push($ids, (int) $param['id']);
		
		$map[] = ['category_id', "IN", $ids];
		if (isset($param['keyword']) && $param['keyword'] != '') {
			$map[] = ['title', 'LIKE', '%'.$param['keyword'].'%'];
		}

		$list = $this->model->where($map)->order($order)->paginate($this->request->pageConfig);

		if (isset($cate['template_lists']) && $cate['template_lists']) {
			$teamplate = 'front@content/' . $this->modelInfo['name'] . '/' . $cate['template_lists'];
		} else {
			$teamplate = 'front@content/' . $this->modelInfo['name'] . '/list';
		}

		$this->data = [
			'model' => $this->modelInfo,
			'id' => (int) $param['id'],
			'category' => $category,
			'list' => $list,
			'page' => $list->render()
		];
		$this->setSeo($cate['title'], $cate['title'], $cate['title']);
		return $this->fetch($teamplate);
	}

	/**
	 * @title 内容详情
	 * @return [type] [description]
	 */
	public function detail() {
		$param = $this->request->param();
		$this->setModel();

		$map[] = ['id', "=", $param['id']];
		$detail = $this->model->where($map)->find();

		if (isset($detail['category_id']) && $detail['category_id']) {
			$pmap = [
				['category_id', '=', $detail['category_id']],
				['id', '<', $param['id']]
			];
			$nmap = [
				['category_id', '=', $detail['category_id']],
				['id', '>', $param['id']]
			];
		}else{
			$pmap = [
				['id', '<', $param['id']]
			];
			$nmap = [
				['id', '>', $param['id']]
			];
		}
		$prev = Db::name(ucfirst($this->modelInfo['name']))->where($pmap)->order('id desc')->find();
		$next = Db::name(ucfirst($this->modelInfo['name']))->where($nmap)->order('id asc')->find();

		if (isset($detail['template_detail']) && $detail['template_detail']) {
			$teamplate = 'front@content/' . $this->modelInfo['name'] . '/' . $detail['template_detail'];
		} else {
			$teamplate = 'front@content/' . $this->modelInfo['name'] . '/detail';
		}

		$this->data = [
			'model' => $this->modelInfo,
			'info' => $detail,
			'prev' => $prev,
			'next' => $next
		];
		$this->setSeo($detail['title'], $detail['title'], $detail['title']);
		return $this->fetch($teamplate);
	}

	/**
	 * @title 内容专题
	 * @return [type] [description]
	 */
	public function topic() {
		return $this->fetch();
	}

	/**
	 * @title 内容搜索
	 * @return [type] [description]
	 */
	public function search() {
		$param = $this->request->param();
		$list = [];
		
		if (isset($param['keyword']) && $param['keyword'] != '') {
			$map[] = ['title', 'LIKE', '%'.$param['keyword'].'%'];
		}

		$this->data = [
			"list" => $list
		];
		return $this->fetch();
	}
	
	protected function setModel(){
		$this->modelInfo = Model::where('id', $this->request->param('model_id'))->find()->append(['grid_list', 'attr_group'])->toArray();
		$this->model = Db::name($this->modelInfo['name']);
	}
}
