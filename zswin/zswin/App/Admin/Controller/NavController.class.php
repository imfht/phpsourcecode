<?php
namespace Admin\Controller;
class NavController extends CommonController {
	
	public function _before_add(){
		$map['status']=1;
		$catelist=D('cate')->where($map)->select();
		$this->assign('group',C('CATE_TYPE'));
		
		$this->assign('catelist',$catelist);
	}
	public function _before_edit(){
		$map['status']=1;
		$catelist=D('cate')->where($map)->select();
		$this->assign('group',C('CATE_TYPE'));
	
		$this->assign('catelist',$catelist);
	}
	public function before_insert($data){
		
		
		
		
		if($data['type']==0){
		
			$data['name']=get_cate_nameByid($data['cid']);
			
			$data['url']='ZSU("/artlist/"'.$data['cid'].',"Index/artlist",array("cid"=>'.$data['cid'].'))';
		}
		if($data['type']==1){
			$group=C('CATE_TYPE');
		
		
			$data['name']=$group[$data['gid']];
			switch ($data['gid']){
				
				case 1:
					$data['url']='ZSU("/artlist/all","Index/artlist")';
					break;
				case 2:
					$data['url']='ZSU("/musiclist/all","Index/musiclist")';
					break;
				case 3:
					$data['url']='ZSU("/grouplist/all","Index/grouplist")';
					break;
				
				
				
			}
			
		}
		if($data['type']==2){
				
			$data['url']='U("'.$data['controll'].'/'.$data['action'].'")';
		}
		
		
		
		return $data;
	}
	public function before_update($data){
	
	
	
	
		if($data['type']==0){
	
			$data['name']=get_cate_nameByid($data['cid']);
			$data['url']='ZSU("/artlist/"'.$data['cid'].',"Index/artlist",array("cid"=>'.$data['cid'].'))';
		}
		if($data['type']==1){
			$group=C('CATE_TYPE');
			
			$data['name']=$group[$data['gid']];
			switch ($data['gid']){
	
				case 1:
					$data['url']='ZSU("/artlist/all","Index/artlist")';
					break;
				case 2:
					$data['url']='ZSU("/musiclist/all","Index/musiclist")';
					break;
				case 3:
					$data['url']='ZSU("/grouplist/all","Index/grouplist")';
					break;
	
	
	
			}
				
		}
		if($data['type']==2){
	
			$data['url']='U('.$data['controll'].'/'.$data['action'].')';
		}
	
	
	
		return $data;
	}
	
	
}

?>