<?php
namespace Addons\SuperLinks\Controller;
use Admin\Controller\AddonsController;
class SuperLinksController extends AddonsController{
	/* 添加友情连接 */
	public function add(){
		$this->meta_title = '添加友情链接';
		$current = U('/admin/addons/adminlist/name/SuperLinks');
		$this->assign('current',$current);
		$this->display(T('Addons://SuperLinks@SuperLinks/edit'));
	}
	
	/* 编辑友情连接 */
	public function edit(){
		$this->meta_title = '修改友情链接';
		$id     =   I('id');
		$current = U('/admin/addons/adminlist/name/SuperLinks');
		$detail = D('Addons://SuperLinks/SuperLinks')->detail($id);
		$this->assign('info',$detail);
		$this->assign('current',$current);
		$this->display(T('Addons://SuperLinks@SuperLinks/edit'));
	}
	
	/* 禁用友情连接 */
	public function forbidden(){
		$this->meta_title = '禁用友情链接';
		$id     =   I('get.id','');
		if(D('Addons://SuperLinks/SuperLinks')->forbidden($id)){
			$this->mtReturn(200, '成功禁用该友情连接','','forward',Cookie('_currentUrl_'));
			
		}else{
			$this->mtReturn(300, D('Addons://SuperLinks/SuperLinks')->getError());
			
		}
	}
	
	/* 启用友情连接 */
	public function off(){
		$this->meta_title = '启用友情链接';
		$id     =   I('get.id','');
		if(D('Addons://SuperLinks/SuperLinks')->off($id)){
			$this->mtReturn(200, '成功启用该友情连接','','forward',Cookie('_currentUrl_'));
			
		}else{
			$this->mtReturn(300, D('Addons://SuperLinks/SuperLinks')->getError());
			
		}
	}
	
	/* 删除友情连接 */
	public function del(){
		$this->meta_title = '删除友情链接';
		$id     =   I('get.id','');
		if(D('Addons://SuperLinks/SuperLinks')->del($id)){
			$this->mtReturn(200, '删除友情链接成功','','forward',Cookie('_currentUrl_'));
			
		}else{
			$this->mtReturn(300, D('Addons://SuperLinks/SuperLinks')->getError());
			
		}
	}
	
	/* 更新友情连接 */
	public function update(){
		$this->meta_title = '更新友情链接';
		$res = D('Addons://SuperLinks/SuperLinks')->update();
		if(!$res){
			$this->mtReturn(300, D('Addons://SuperLinks/SuperLinks')->getError());
			
		}else{
			if($res['id']){
				$this->mtReturn(200, '更新友情链接成功');
				
			}else{
				$this->mtReturn(200, '新增友情链接成功');
				
			}
		}
	}
}
