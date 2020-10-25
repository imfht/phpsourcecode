<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Addons\Advs\Controller;
use Admin\Controller\AddonsController; 

class AdvsController extends AddonsController{
	/* 添加 */
	public function add(){
		$current = U('/Admin/Addons/adminList/name/Advs');
		$sing = M('advertising')->where('status = 1')->select();
		$this->assign('current',$current);
		$this->assign('sing',$sing);
		$this->display(T('Addons://Advs@Advs/edit'));
	}
	
	/* 编辑 */
	public function edit(){
		$id     =   I('get.id','');
		$current = U('/Admin/Addons/adminList/name/Advs');
		$sing = M('advertising')->where('status = 1')->select();
		$detail = D('Addons://Advs/Advs')->detail($id);
		$this->assign('info',$detail);
		$this->assign('current',$current);
		$this->assign('sing',$sing);
		$this->display(T('Addons://Advs@Advs/edit'));
	}
	
	/* 禁用 */
	public function forbidden(){
		$id     =   I('get.id','');
		if(D('Addons://Advs/Advs')->forbidden($id)){
			$this->mtReturn(200, '成功禁用该广告','','forward',cookie('_currentUrl_'));
			//$this->success('成功禁用该广告', Cookie('_currentUrl_'));
		}else{
			$this->mtReturn(300, D('Addons://Advs/Advs')->getError());
			//$this->error(D('Addons://Advs/Advs')->getError());
		}
	}
	
	/* 启用 */
	public function off(){
		$id     =   I('get.id','');
		if(D('Addons://Advs/Advs')->off($id)){
			$this->mtReturn(200, '成功启用该广告','','forward',cookie('_currentUrl_'));
			//$this->success('成功启用该广告',Cookie('_currentUrl_'));
		}else{
			$this->mtReturn(300, D('Addons://Advs/Advs')->getError());
			//$this->error(D('Addons://Advs/Advs')->getError());
		}
	}
	
	/* 删除 */
	public function del(){
		$id     =   I('get.id','');
		if(D('Addons://Advs/Advs')->del($id)){
			$this->mtReturn(200, '该广告删除成功','','forward',cookie('_currentUrl_'));
			//$this->success('删除成功', Cookie('_currentUrl_'));
		}else{
			$this->mtReturn(300, D('Addons://Advs/Advs')->getError());
			//$this->error(D('Addons://Advs/Advs')->getError());
		}
	}
	
	/* 更新 */
	public function update(){
		$res = D('Addons://Advs/Advs')->update();
		if(!$res){
			//$this->error(D('Addons://Advs/Advs')->getError());
			$this->mtReturn(300, D('Addons://Advs/Advs')->getError());
		}else{
			if($res['id']){
				$this->mtReturn(200, '更新广告成功');
				//$this->success('更新成功', Cookie('_currentUrl_'));
			}else{
				$this->mtReturn(200, '新增广告成功');
				//$this->success('新增成功', Cookie('_currentUrl_'));
			}
		}
	}
	/**
	 * 批量处理
	 */
	public function savestatus(){
		$status = I('get.status');
		$ids = I('post.id');
		
		if($status == 1){
			foreach ($ids as $id)
			{
				D('Addons://Advs/Advs')->off($id);
			}
			$this->mtReturn(200, '成功启用该广告','','forward',Cookie('_currentUrl_'));
			//$this->success('成功启用该广告',Cookie('_currentUrl_'));
		}else{
			foreach ($ids as $id)
			{
				D('Addons://Advs/Advs')->forbidden($id);
			}
			$this->mtReturn(200, '成功禁用该广告','','forward',Cookie('_currentUrl_'));
			//$this->success('成功禁用该广告',Cookie('_currentUrl_'));
		}			

	}
}
