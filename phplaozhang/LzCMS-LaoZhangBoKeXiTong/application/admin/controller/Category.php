<?php
namespace app\admin\controller;

/**
* 
*/
class Category extends Init
{
	
	function _initialize()
	{
		parent::_initialize();
		$this->model=model('common/category');
	}

	function index(){
		$category_list = $this->model->get_category_list(0,true);
		return view('list',['categorys'=>$this->model->categorys,'category_list'=>$category_list]);
	}

	function add(){
		if(request()->isPost()){
			$params = input('post.');
			if(!$params['index_template'] && cache('models')[$params['model_id']]['index_template']){
				$params['index_template'] = cache('models')[$params['model_id']]['index_template'];
			}
			if(!$params['list_template'] && cache('models')[$params['model_id']]['list_template']){
				$params['list_template'] = cache('models')[$params['model_id']]['list_template'];
			}
			if(!$params['show_template'] && cache('models')[$params['model_id']]['show_template']){
				$params['show_template'] = cache('models')[$params['model_id']]['show_template'];
			}
			$result = $this->model->add($params);
			if($result){
				return json(array('code'=>200,'msg'=>'添加成功'));
			}else{
				return json(array('code'=>0,'msg'=>'添加失败'));
			}
		}
		$category_select = $this->model->get_category_select(0);
		if(input('?param.model_id') && input('param.model_id') == 0){
			return view('add_link',array('category_select'=>$category_select));
		}
		$model_select = $this->model->get_model_select();
		return view('add',array('category_select'=>$category_select,'model_select'=>$model_select));
	}

	function edit(){
		if(request()->isPost()){
			$params = input('post.');
			$old_category = cache('categorys')[$params['id']];
			if($params['parent_id'] == $params['id'] || in_array($params['parent_id'], $this->model->get_category_ids($params['id']))){
				return json(array('code'=>0,'msg'=>'上级栏目不能是当前栏目或者当前栏目的下级栏目'));
			}
			if($old_category['model_id'] != $params['model_id']){
				$count = model($old_category['model_code'])->where('category_id',$params['id'])->count('id');
				if($count > 0){
					return json(array('code'=>0,'msg'=>'该栏目下还有 '.$count.' 条数据，不能修改模型'));
				}
			}
			if(!$params['index_template'] && cache('models')[$params['model_id']]['index_template']){
				$params['index_template'] = cache('models')[$params['model_id']]['index_template'];
			}
			if(!$params['list_template'] && cache('models')[$params['model_id']]['list_template']){
				$params['list_template'] = cache('models')[$params['model_id']]['list_template'];
			}
			if(!$params['show_template'] && cache('models')[$params['model_id']]['show_template']){
				$params['show_template'] = cache('models')[$params['model_id']]['show_template'];
			}

			$result = $this->model->edit($params);
			if($result){
				return json(array('code'=>200,'msg'=>'修改成功'));
			}else{
				return json(array('code'=>0,'msg'=>'修改失败'));
			}
		}
		$category = $this->model->where('id',input('param.id'))->find();
		$category_select = $this->model->get_category_select(0);
		if($category['model_id'] == 0){
			return view('edit_link',array('category'=>$category->toArray(),'category_select'=>$category_select));
		}
		$model_select = $this->model->get_model_select();
		return view('edit',array('category'=>$category->toArray(),'category_select'=>$category_select,'model_select'=>$model_select));
	}

	function del(){
		$id = input('post.id');
		$child_ids = $this->model->get_category_ids($id);
		if($child_ids){
			return json(array('code'=>0,'msg'=>'请先删除该栏目的下级栏目'));
		}
		$category = cache('categorys')[$id];
		if($category['model_id'] && $category['model_id'] != 1){
			$count = model($category['model_code'])->where('category_id',$id)->count('id');
			if($count > 0){
				return json(array('code'=>0,'msg'=>'请先删除该栏目下的 '.$count.' 条数据'));
			}
		}
		$result = $this->model->destroy($id);
		if($result){
			if($category['model_id'] == 1){model('common/page')->destroy(['category_id' => $id]);}
			$this->model->cache_category();
			return json(array('code'=>200,'msg'=>'删除成功'));
		}else{
			return json(array('code'=>0,'msg'=>'删除失败'));
		}
	}
	//是否导航显示
	function menu_switch(){ 
		$id = input('post.id');
		$data['id'] = $id;
		$data['is_menu'] = array('exp','1-is_menu');
		$result = $this->model->edit($data);
		if($result){
			return json(array('code'=>200,'msg'=>'操作成功'));
		}else{
			return json(array('code'=>0,'msg'=>'操作失败'));
		}
	}
	//内容管理搜索
	function content_manage_search(){
		$category_list = $this->model->get_last_category_list(0,true);
		return view('content_manage_search',['category_list'=>json_encode($category_list)]);
	}
	//分类排序
	function sort(){
		$param = input('post.')['sorts'];
		$result = $this->model->sort($param);
		if($result){
			return json(array('code'=>200,'msg'=>'排序成功'));
		}else{
			return json(array('code'=>0,'msg'=>'排序失败'));
		}
	}
	//获取内容管理树
	function manage_tree(){
		$manage_tree = $this->model->get_manage_tree(0);
		return json($manage_tree);
	}
	//根据分类id获取同模型选项列表
	function model_category_select(){
		$params = input('post.');
		$category_id = $params['category_id'];
		$ids = implode(',', $params['ids']);
		$model_id = $this->model->categorys[$category_id]['model_id'];
		$model_category_select = $this->model->get_model_category_select(0,$model_id,true);
		$select_str = '<form class="layui-form move-form"><div class="layui-form-item"><label class="layui-form-label">移动到</label><div class="layui-input-block"><select name="to_category_id" lay-filter="move"><option value="">请选择栏目</option>';
		foreach ($model_category_select as $k => $v) {
			if($v["disabled"]){
				$select_str .= '<option disabled>'.$v['sep_name'].'</option>';
			}else{
				$select_str .= '<option value="'.$v['id'].'">'.$v['sep_name'].'</option>';
			}
		}
		$select_str .= '</select></div></div><fieldset class="layui-elem-field"><legend>移动数据 ID</legend><div class="layui-field-box">'.$ids.'</div></fieldset></form>';
		echo $select_str;
	}
	//更新内容链接
	function update_content_links(){
		$site_url = model('common/setting')->get_setting('site_url');
		if(empty($site_url)){
			$site_url = request()->domain();
		}
		$models = cache('models');
		$result = true;
		$error_msg = '';
		foreach ($models as $k=>$v) {
			$model = model('common/'.$v['tablename']);
   			$data = $model->order('id desc')->select();
   			$update_data = array();
   			$is_update = false;
	   		foreach ($data as $k => $v1) {
	   			if(!$v1['url']){ continue; }
	   			if(parse_url($v1['url'])['host'] && (parse_url($v1['url'])['host']) != (parse_url($site_url)['host'])){ continue; }
				if(!parse_url($v1['url'])['host']){
					$url = url('index/'.$v['tablename'].'/show',['id'=>$v1['id']]);
					$update_data[] = ['id'=>$v1['id'], 'url'=>$url];
				}
				$is_update = true;
	   		}
	   		if(!empty($update_data)){
	   			$update_result = $model->saveAll($update_data);
	   		}
	   		if(empty($update_result) && $is_update){
	   			$result = false;
	   			$error_msg .= $v['name'].'  ';
	   		}
   		}
   		if($result){
			return json(array('code'=>200,'msg'=>'操作成功'));
		}else{
			return json(array('code'=>0,'msg'=>$error_msg.'更新失败'));
		}
	}
}