<?php
namespace app\admin\controller;

/**
* 下载控制器
*/
class Download extends Init
{
	
	function _initialize()
	{
		parent::_initialize();
		$this->model = model('common/download');
		$this->category_model = model('common/category');
	}

	function index(){
		$params = input('param.');
		$category_id = $params['category_id'];
		$page_size = $params['page_size'];
		$map = array('category_id'=>$category_id);
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
		$downloads = $this->model->get_list($map,$order,$page_size,2);
		return view('list',['category_id'=>$category_id,'downloads'=>$downloads['data'],'total'=>$downloads['total'],'per_page'=>$downloads['per_page'],'current_page'=>$downloads['current_page'],'search'=>$params['search'],'order'=>$order,'url_params'=>$url_params]);
	}

	function add(){
		if(request()->isPost()){
			$params = input('post.');
			if(!trim($params['url'])){
				unset($params['url']);
			}
			$result = $this->model->add($params);
			if($result){
				if(!trim($params['url'])){
					$this->model->edit(array('url'=>url('index/download/show',['id'=>$this->model->id]),'id'=>$this->model->id));
				}
				return json(array('code'=>200,'msg'=>'添加成功'));
			}else{
				return json(array('code'=>0,'msg'=>'添加失败'));
			}
		}
		$category_id = input('param.category_id');
		$model_category_select_option = $this->category_model->get_model_category_select_no_option($category_id);
		return view('add',['model_category_select_option'=>$model_category_select_option]);
	}

	function edit(){
		if(request()->isPost()){
			$params = input('post.');
			if(!trim($params['url'])){
				$params['url'] = url('index/download/show',['id'=>$params['id']]);
			}
			$result = $this->model->edit($params);
			if($result){
				return json(array('code'=>200,'msg'=>'修改成功'));
			}else{
				return json(array('code'=>0,'msg'=>'修改失败'));
			}
		}
		$download = $this->model->where('id',input('param.id'))->find();
		$model_category_select_option = $this->category_model->get_model_category_select_no_option($download['category_id']);
		return view('edit',array('download'=>$download->toArray(),'model_category_select_option'=>$model_category_select_option));
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

	//批量移动
	function batches_move(){
		$params = input('post.');
		$params['ids'] = implode(',', $params['ids']);
		$result = $this->model->batches('move',$params);
		if($result){
			return json(array('code'=>200,'msg'=>'批量移动成功'));
		}else{
			return json(array('code'=>0,'msg'=>'批量移动失败'));
		}
	} 

	//置顶
	function to_top(){ 
		$id = input('post.id');
		$data['id'] = $id;
		$data['is_top'] = array('exp','1-is_top');
		$result = $this->model->edit($data);
		if($result){
			return json(array('code'=>200,'msg'=>'操作成功'));
		}else{
			return json(array('code'=>0,'msg'=>'操作失败'));
		}
	}
	//推荐
	function to_recommend(){ 
		$id = input('post.id');
		$data['id'] = $id;
		$data['is_recommend'] = array('exp','1-is_recommend');
		$result = $this->model->edit($data);
		if($result){
			return json(array('code'=>200,'msg'=>'操作成功'));
		}else{
			return json(array('code'=>0,'msg'=>'操作失败'));
		}
	}

}