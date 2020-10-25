<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\controller\api;

use think\facade\Db;
use app\model\Category;
use app\model\Model;
use app\model\Attribute;

/**
 * @title 内容管理
 */
class Content extends Base {

	public $middleware = [
		'\app\http\middleware\Validate',
		'\app\http\middleware\ApiAuth' => ['except' => ['lists', 'detail', 'category']],
		'\app\http\middleware\Api',
	];

	public $modelInfo = [];
	public $model = null;
	
	public function initialize() {
		parent::initialize();
		$this->modelInfo = Model::where('name', $this->request->param('name'))->find()->append(['grid_list', 'attr_group'])->toArray();
		$this->model = Db::name($this->modelInfo['name']);
	}

	/**
	 * @title 内容列表
	 * @method GET
	 * @param  Category $category [description]
	 * @return [json]
	 */
	public function lists(Category $category){
		$param = $this->request->param();
		$order = "id desc";
		$map = [];

		if (isset($param['keyword']) && $param['keyword'] != '') {
			$map[] = ['title', 'LIKE', '%'.$param['keyword'].'%'];
		}
		if (isset($param['category_id']) && $param['category_id']) {
			$map[] = ['category_id', '=', $param['category_id']];
		}

		$list = $this->model->where($map)->order($order)->paginate($this->request->pageConfig)->each(function($item) {
			if(isset($item['cover_id'])){
				$item['cover'] = get_attach($item['cover_id'], 'url');
				return $item;
			}
		});
		$this->data['code'] = 1;
		$this->data['data'] = $list;
		return $this->data;
	}

	/**
	 * @title 内容详情
	 * @method GET
	 * @return [json]
	 */
	public function detail(){
		$id = $this->request->param('id');
		if (!$id) {
			return $this->error("非法操作！");
		}
		$info = $this->model->find($id);
		$this->data['code'] = 1;
		$this->data['data'] = $info;
		return $this->data;
	}

	/**
	 * @title 栏目列表
	 * @method GET
	 * @return [json]
	 */
	public function category(){
		$param = $this->request->param();
		$map = [];

		$map[] = ['model_id', '=', $this->modelInfo['id']];
		if(isset($param['pid']) && $param['pid']){
			$map[] = ['pid', '=', $param['pid']];
		}

		$list = Category::where($map)->select();

		$this->data['code'] = 1;
		$this->data['data'] = $list;
		return $this->data;
	}

	/**
	 * @title 添加内容
	 * @method POST
	 * @return [json]
	 */
	public function add(){
		$data = $this->request->post();
		$data['create_time'] = time();
		$data['update_time'] = time();
		$data['uid'] = $this->request->user['uid'];

		$result = $this->model->save($data);
		if(false !== $result){
			$this->data['code'] = 1;
		}else{
			$this->data['code'] = 0;
			$this->data['msg'] = "添加失败！";
		}
		
		return $this->data;
	}

	/**
	 * @title 修改内容
	 * @method POST
	 * @return [json]
	 */
	public function edit(){
		$data = $this->request->post();
		$data['update_time'] = time();

		$result = $this->model->save($data);
		if(false !== $result){
			$this->data['code'] = 1;
		}else{
			$this->data['code'] = 0;
			$this->data['msg'] = "修改失败！";
		}
		
		return $this->data;
	}

	/**
	 * @title 删除内容
	 * @method POST
	 * @return [json]
	 */
	public function delete(){
		$id = $this->request->param('id', '');

		$map = [];
		if (!$id) {
			return $this->error('请选择要操作的数据!');
		}
		if (is_array($id)) {
			$map[] = ['id', 'IN', $id];
		}else{
			$map[] = ['id', '=', $id];
		}

		$result = $this->model->where($map)->delete();
		if(false !== $result){
			$this->data['code'] = 1;
		}else{
			$this->data['code'] = 0;
			$this->data['msg'] = "删除失败！";
		}
		
		return $this->data;
	}
}