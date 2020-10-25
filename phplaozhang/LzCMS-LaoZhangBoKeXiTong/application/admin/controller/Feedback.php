<?php
namespace app\admin\controller;

/**
* 留言控制器
*/
class Feedback extends Init
{
	
	function _initialize()
	{
		parent::_initialize();
		$this->model = model('common/feedback');
	}

	function index(){
		$params = input('param.');
		$page_size = $params['page_size'];
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
		$feedbacks = $this->model->get_list($map,$order,$page_size,2);
		return view('list',['feedbacks'=>$feedbacks['data'],'total'=>$feedbacks['total'],'per_page'=>$feedbacks['per_page'],'current_page'=>$feedbacks['current_page'],'search'=>$params['search'],'order'=>$order,'url_params'=>$url_params]);
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
		$feedback = $this->model->where('id',input('param.id'))->find();
		$feedback = $feedback->toArray();
		//$feedback['content'] = html_entity_decode($feedback['content']);
		return view('edit',array('feedback'=>$feedback));
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

	//获取留言内容
	function get_content(){
		$result = $this->model->get(input('post.id'));
		if($result){
			return json(array('code'=>200,'msg'=>'操作成功','content'=>$result->content));
		}else{
			return json(array('code'=>0,'msg'=>'操作失败'));
		}
	}

	//留言回复
	function reply(){
		if(request()->isPost()){
			$params = input('post.');
			$params['admin_name'] = session('admin_user')['name'];
			$params['admin_avatar'] = session('admin_user')['avatar'];
			$result = $this->model->edit($params);
			if($result){
				return json(array('code'=>200,'msg'=>'回复成功'));
			}else{
				return json(array('code'=>0,'msg'=>'回复失败'));
			}
		}
		$feedback = $this->model->where('id',input('param.id'))->find();
		return view('reply',array('feedback'=>$feedback->toArray()));
	}

}