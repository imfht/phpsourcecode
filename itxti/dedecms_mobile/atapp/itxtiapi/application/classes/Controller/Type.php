<?php defined('SYSPATH') or die('No direct script access.');
//文章api
class Controller_Type extends Controller_Common {
	public function before()
	{
		parent::before();
		$this->model['type'] = Model::factory('Type');
	}
	/**
	 * 栏目列表
	 * @access   public
	 * @param string $typeid
	 */
	public function action_gettypelist()
	{
		$typeid = $this->request->query('typeid');
		$lists = $this->model['type']->get_typelist($typeid);
		if($lists){
			$this->success($lists);
		}else{
			$this->error('栏目获取失败');
		}
	}

	/**
	 * 取栏目信息
	 * @access   public
	 * @param string $typeid
	 */
	public function action_typeinfo()
	{
		$typeid = $this->request->query('typeid');
		$typeinfo = $this->model['type']->get_typeinfo($typeid);
		if($typeinfo){
			$this->success($typeinfo);
		}else{
			$this->error('栏目获取失败');
		}
	}






	
}
