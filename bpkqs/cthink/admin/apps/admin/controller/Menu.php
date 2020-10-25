<?php
namespace app\admin\controller;

class Menu extends Base
{	
    public function index()
    {
		$map = [];
		$input = input('name');
		$pid = input('pid')?input('pid'):0;
		$map['pid'] = $pid;
		if($input){
			$map['title|id'] = ['like','%'.$input.'%'];
		}
			
		$list = model('Menu')->lists(12,$map);
		$this->assign('list',$list);
		$this->assign('pid',['pid'=>$pid]);
        return $this->fetch();
    }
	
	public function add(){
		$menu = model('Menu');
		$pid = input('pid')?input('pid'):0;
		if(request()->isPost()){
			$data = input();
			$valid = \think\Loader::validate("Menu")->scene('add');
			if(!$valid->check($data)){
				$this->error($valid->getError());
			}else{
				if($menu->addMenu($data)){
					$this->success('添加成功',url('menu/index'));	
				}else{
					$this->error('添加失败');
				}
			}
		}else{
			$tree = $menu->getMenuTree();
			$menus = $menu->toFormatTree($tree);
			$menus = array_merge([0=>['id'=>0,'title_show'=>'顶级菜单']], $menus);
            $this->assign('Menus', $menus);
			$this->assign('pid', $pid);
			return $this->fetch();
		}
	}
	
	public function edit(){
		$menu = model('Menu');
		if(request()->isPost()){
			$data = input();
			$valid = \think\Loader::validate("Menu")->scene('edit');
			if(!$valid->check($data)){
				$this->error($valid->getError());
			}else{
				if($menu->editMenu($data)){
					$this->success('编辑成功',url('menu/index',['pid'=>$data['pid']]));	
				}else{
					$this->error('编辑失败');
				}
			}
		}else{
			$id = input('id');
			$find = $menu->getFindOne($id);
			$tree = $menu->getMenuTree();
			$menus = $menu->toFormatTree($tree);
			$menus = array_merge([0=>['id'=>0,'title_show'=>'顶级菜单']], $menus);
            $this->assign('Menus', $menus);
			$this->assign('find',$find);
			return $this->fetch();
		}
	}
	
	public function state(){
		$id = input('id');
		$status = input('status');
		if($id && $status){
			$map = [
				'id' => ['in',$id],
			];
			if(model('Menu')->stateMenu($map,['status'=>$status])){
				$this->success('设置成功');
			}else{
				$this->error('设置失败');
			}
		}else{
			$this->error('参数有误');
		}
	}
	
	public function remove(){
		$data = input('id');
		if($data){
			$auth_member = model('Menu');
			if($auth_member->removeMenu($data)){
				$this->success('删除成功');
			}else{
				$this->error('删除失败，可能是该菜单下包含子菜单');
			}
		}else{
			$this->error('提交的数据有误');
		}
		
	}	
}
