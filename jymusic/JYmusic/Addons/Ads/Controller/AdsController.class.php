<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Addons\Ads\Controller;
use Admin\Controller\AddonsController; 

class AdsController extends AddonsController{
	/* 添加 */
	public function add(){
		$current = U('/Admin/Addons/adminList/name/Ads');
		$sing = M('advertising')->where('status = 1')->select();
		$this->meta_title = "添加广告";
		$this->assign('current',$current);
		$this->assign('sing',$sing);
		$this->display(T('Addons://Ads@Ads/edit'));
	}
	
	/* 编辑 */
	public function edit($name=null, $id = 0){
		$id     =   I('get.id','');
		$current = U('/Admin/Addons/adminList/name/Ads');
		$sing = M('advertising')->where('status = 1')->select();
		$detail = D('Addons://Ads/Ads')->detail($id);
		$this->meta_title = "编辑广告";
		$this->assign('info',$detail);
		$this->assign('current',$current);
		$this->assign('sing',$sing);
		$this->display(T('Addons://Ads@Ads/edit'));
	}
	
	/* 禁用 */
	public function forbidden(){
		$id     =   I('get.id','');
		if(D('Addons://Ads/Ads')->forbidden($id)){
			$this->success('成功禁用该广告', Cookie('__forward__'));
		}else{
			$this->error(D('Addons://Advs/Advs')->getError());
		}
	}
	
	/* 启用 */
	public function off(){
		$id     =   I('get.id','');
		if(D('Addons://Ads/Ads')->off($id)){
			$this->success('成功启用该广告',Cookie('__forward__'));
		}else{
			$this->error(D('Addons://Advs/Advs')->getError());
		}
	}
	
	/* 删除 */
	public function del($id=0, $name=null){
		$id     =   I('get.id','');
		if(D('Addons://Ads/Ads')->del($id)){
			$this->success('删除成功', Cookie('__forward__'));
		}else{
			$this->error(D('Addons://Advs/Advs')->getError());
		}
	}
	
	/* 更新 */
	public function update(){
		$res = D('Addons://Ads/Ads')->update();
		if(!$res){
			$this->error(D('Addons://Ads/Ads')->getError());
		}else{
			if($res['id']){
				$this->success('更新成功', Cookie('__forward__'));
			}else{
				$this->success('新增成功', Cookie('__forward__'));
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
				D('Addons://Ads/Ads')->off($id);
			}
			$this->success('成功启用该广告',Cookie('__forward__'));
		}else{
			foreach ($ids as $id)
			{
				D('Addons://Ads/Ads')->forbidden($id);
			}
			$this->success('成功禁用该广告',Cookie('__forward__'));
		}			

	}
	
	
	/*广告位置*/
	public function seat(){
		$list = M('Advertising')->select();
		$typetext = array(1=>'单图',2=>'多图',3=>'文字',4=>'代码');
		$this->meta_title = "广告位列表";
		$this->assign('typetext',$typetext);
		$this->assign('list',$list);
		$this->display(T('Addons://Ads@Ads/seat'));
	}
	
		/*添加广告位置*/
	public function editseat(){
		$id     =   I('get.id','');
		$detail = M('Advertising')->find($id);
		$this->meta_title = "编辑广告位";
		$this->assign('info',$detail);
		$this->display(T('Addons://Ads@Ads/editseat'));
	}
	
	/*添加广告位置*/
	public function addseat(){
		$this->meta_title = "添加广告位";
		$this->display(T('Addons://Ads@Ads/editseat'));
	}
	
	/*广告位置*/
	public function delseat(){
		$id     =   I('get.id','');
		if(M('Advertising')->delete($id)){
			$this->success('删除成功', Cookie('__forward__'));
		}else{
			$this->error('删除失败！');
		}
	}
	
	/* 新增/更新 广告位*/
	public function updateseat(){
		$Adv= M('Advertising');
		$data = $Adv->create();
		if(empty($data)){return false;}		
		/* 添加或新增基础内容 */
		if(empty($data['id'])){ //新增数据
			$id = $Adv->add(); //添加基础内容
			if(!$id){
				$this->error ( '新增广告内容出错！');
			}else{
				$this->success('新增成功', U('/Admin/Addons/adminList/name/Ads/seat'));				
			}
		} else { //更新数据
			$status = $Adv->save(); //更新基础内容
			if(false === $status){
				$this->error ( '更新广告内容出错！');
			}else{
				$this->success('更新成功', U('/Admin/Addons/adminList/name/Ads/seat'));
			}
		}
	}
	
		/**
	 * 批量处理
	 */
	public function seatstatus(){
		$status = I('get.status');
		$ids = I('post.id');
	
		if($status == 1){
			foreach ($ids as $id){
				 M('Advertising')->save(array('id'=>$id,'status'=>'1'));
			}
			$this->success('成功启用该广告位',U('/Admin/Addons/adminList/name/Ads/seat'));
		}else{
			foreach ($ids as $id){
				M('Advertising')->save(array('id'=>$id,'status'=>'0'));
			}
			$this->success('成功禁用该广告位',Cookie('__forward__'));
		}
	
	}	
}
