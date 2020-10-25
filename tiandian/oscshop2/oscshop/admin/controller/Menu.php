<?php
/**
 * oscshop2 B2C电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015-2016 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 */
namespace osc\admin\controller;
use osc\common\controller\AdminBase;
use think\Db;
class Menu extends AdminBase{
	
	protected function _initialize(){
		parent::_initialize();
		$this->assign('breadcrumb1','系统');
		$this->assign('breadcrumb2','后台菜单管理');
	}
	
    public function index(){
    	
				
		$cate =Db::query('SELECT id,pid,title AS name FROM '.config('database.prefix').'menu ORDER BY sort_order ASC');
		
		$list =list_to_tree($cate);
		
		$this->assign('list',json_encode($list));
		
		return $this->fetch();   
    }
	
	public function add(){
		
		if(request()->isPost()){
			
			$data=input('post.');		
			$data['pid']=$data['id'];
			unset($data['id']);			
			
			$result = $this->validate($data,'Menu');
			
			if($result!==true){
				return ['error'=>$result];
			}
			$id=Db::name('Menu')->insert($data,false,true);
			if($id){
				
				storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'添加了后台菜单，'.$data['title']);	
											
				return ['status'=>'success','id'=>$id,'name'=>$data['title']];
			}else{
				return ['error'=>'新增失败'];
			}
			
		}
	}
	
	function edit(){
		
		if(request()->isPost()){
			
			$data=input('post.');	
			
			$result = $this->validate($data,'Menu');
			
			if($result!==true){
				return ['error'=>$result];
			}
			
			$r=Db::name('Menu')->update($data);
			
			if($r){
				
				storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'修改了后台菜单，'.$data['title']);
				
				return ['success'=>'修改成功','name'=>$data['title']];				
			
			}else{
								
				return ['error'=>'修改失败'];
			}
		}
	}
	function del(){
		
		if(request()->isPost()){
			$id=(int)input('post.id');
			
			if(Db::name('Menu')->where('pid',$id)->find()){				
				return ['error'=>'请先删除子节点！！'];
			}

			if(Db::name('Menu')->where('id',$id)->delete()){
				
				storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'删除了后台菜单，id='.$id);
								
				return ['success'=>'删除成功'];
			}
		}		
	}
	function get_info(){
		
		if(request()->isPost()){
			$id=(int)input('id');
			$d=Db::name('Menu')->find($id);
			
			return ['title'=>$d['title'],'url'=>$d['url'],'type'=>$d['type'],'icon'=>$d['icon'],'module'=>$d['module'],'sort_order'=>$d['sort_order']] ;
		}
	}
	
	
}
