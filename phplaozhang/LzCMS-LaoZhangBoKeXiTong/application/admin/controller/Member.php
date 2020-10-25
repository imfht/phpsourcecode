<?php
namespace app\admin\controller;

/**
* 会员控制器
*/
class Member extends Init
{
	
	function _initialize()
	{
		parent::_initialize();
		$this->model = model('common/member');
	}

	function index(){
		$params = input('param.');
		$page_size = $params['page_size'];
		$map = array();
		if($params['search'] && is_array($params['search'])){
			foreach ($params['search'] as $k => $v) {
				if($v){
					$map[$k] = array('like','%'.$v.'%'); 
				}
			}
		}
		if($params['order'] && is_array($params['order'])){
			$sort = end($params['order']) ? each($params['order']) : each($params['order']);
			$order[$sort['key']] = $sort['value'];
			$order = array($sort['key']=>$sort['value']);
		}
		$url_params = parse_url(request()->url(true))['query'];
		$members = $this->model->get_list($map,$order,$page_size,2);
		return view('list',['members'=>$members['data'],'total'=>$members['total'],'per_page'=>$members['per_page'],'current_page'=>$members['current_page'],'search'=>$params['search'],'order'=>$order,'url_params'=>$url_params]);
	}

	function add(){
		if(request()->isPost()){
			$params = input('post.');
			$result = $this->model->add($params);
			if($result){
				return json(array('code'=>200,'msg'=>'添加成功'));
			}else{
				return json(array('code'=>0,'msg'=>'添加失败'));
			}
		}
		return view('add');
	}

	function edit(){
		if(request()->isPost()){
			$params = input('post.');
			$result = $this->model->edit($params);
			if($result){
				return json(array('code'=>200,'msg'=>'修改成功'));
			}else{
				return json(array('code'=>0,'msg'=>'修改失败'));
			}
		}
		$member = $this->model->where('id',input('param.id'))->find();
		return view('edit',array('member'=>$member->toArray()));
	}

	function del(){
		$result = $this->model->destroy(input('post.id'));
		if($result){
			return json(array('code'=>200,'msg'=>'删除成功'));
		}else{
			return json(array('code'=>0,'msg'=>'删除失败'));
		}
	}

	//批量删除
	function batches_delete(){
		$params = input('post.');
		$ids = implode(',', $params['ids']);
		$result = $this->model->batches('delete',$ids);
		if($result){
			return json(array('code'=>200,'msg'=>'批量删除成功'));
		}else{
			return json(array('code'=>0,'msg'=>'批量删除失败'));
		}
	}

}