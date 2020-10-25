<?php
namespace app\admin\controller;

/**
 * 分类管理控制器
 */
class Category extends Base{
    public function index(){
		$result = model('Category')->lists();
		$this->assign('list',$result);
        return $this->fetch();
    }
	
	/**
	 * 添加分类
	 */
	public function add(){
		if(request()->isPost()){
			$data = input();
			$valid = \think\Loader::validate("Category")->scene('add');
			if(!$valid->check($data)){
				$this->error($valid->getError());
			}else{
				if(model('Category')->insert($data)){
					$this->success('添加成功',url('category/index'));
				}else{
					$this->error('添加失败');
				}
			}
		}else{ 
			$result = model('Category')->lists();
			$this->assign('tree',$result);
			return $this->fetch();
		}
	}
	
	/**
	 * 编辑分类
	 */
	public function edit(){
		if(request()->isPost()){
			$data = input();
			$valid = \think\Loader::validate("Category")->scene('edit');
			if(!$valid->check($data)){
				$this->error($valid->getError());
			}else{
				if(model('Category')->update($data)){
					$this->success('编辑成功',url('category/index'));
				}else{
					$this->error('编辑失败');
				}
			}
		}else{
			$id = input('id');
			if($id){
				$result = model('Category')->lists();
				$find = model('Category')->where(['id'=>$id])->find();
				$this->assign('tree',$result);
				$this->assign('find',$find);
				return $this->fetch();
			}else{
				$this->error('参数错误');
			}
		}
	}
	
	/**
	 * 删除分类
	 */
	public function remove(){
		$data = input();
		if(isset($data['id'])){
			 if(model('Category')->where(['id'=>['in',$data['id']]])->delete()){
				 $this->success('删除成功');
			 }else{
				 $this->error('删除失败');
			 }
		}else{
			$this->error('参数有误');
		}
	}
	
	/**
	 * 启用和禁用分类
	 */
	public function state(){
		$data = input();
		if(isset($data['id']) && isset($data['status'])){
			if(model('Category')->where(['id'=>['in',$data['id']]])->update(['status'=>$data['status']])){
				$this->success('操作成功');
			}else{
				$this->error('操作失败');
			}
		}else{
			$this->error('参数有误');
		}
	}
	
	/**
	 * 分类排序
	 */
	public function catesort(){
		$data = input();
		if($data['id'] && $data['sort']){
			$ret = model('Category')->catesort($data);
			if($ret){
				$this->success('操作成功');
			}else{
				$this->error('操作失败');
			}
		}else{
			$this->error('参数有误');
		}
	}
}
