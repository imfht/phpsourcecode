<?php
class indexController extends adminController{
	protected $layout = 'layout';
	
	public function userlist(){
		$url = url('index/userlist',array(p=>'{page}'));
		$count=$this->model->table('userinfo')->where( array('ppid'=>$this->ppid) )->count();
		$limit=$this->pageLimit($url);
		$this->userlist = model('usercenter')->userlist( array('ppid'=>$this->ppid) ,$limit);
		$this->page = $this->pageShow($count);
		$this->display();
	}
	
	public function grouplist(){
		$this->grouplist = model('usercenter')->grouplist( array('ppid'=>$this->ppid) );
		$this->display();
	}
	
	public function groupaddedit(){
		$id = intval($_GET['id']);
		$this->groupinfo = model('usercenter')->groupinfo(array('id'=>$id));
		if( !$this->isPost() ){
			$this->display();
		}else{
			$data = $_POST;
			$data['ppid'] = $this->ppid;
			if(empty($id)){
			    if( model('usercenter')->groupadd($data)){
				    $this->alert('添加成功', url('index/grouplist'));
			    }else{
				    $this->alert('添加失败');
		   	    }
			}else{
			    if( model('usercenter')->groupupdate( array('id'=>$id) ,$data) ){
				    $this->alert('修改成功', url('index/grouplist'));
			    }else{
				    $this->alert('修改失败');
		   	    }
			}
		}
	}
	
	public function groupdel(){
		$id = intval($_GET['id']);
		$groupinfo = model('usercenter')->groupinfo( array('id'=>$id) );
		if( empty($groupinfo) ){
			$this->alert('该条数据不存在或者已被删除');
		}
		if( model('usercenter')->groupdel( array('id'=>$id) ) ){
			$this->alert('删除成功', url('index/grouplist'));
		}else{
			$this->alert('删除失败');
		}
	}
	
	public function card(){
		$this->cardlist = model('usercenter')->cardlist( array('ppid'=>$this->ppid) );
		$this->display();
	}
	
	public function cardaddedit(){
		$id = intval($_GET['id']);
		$this->info = model('usercenter')->cardinfo(array('id'=>$id));
		if( !$this->isPost() ){
			$this->display();
		}else{
			$data = $_POST;
			$data['ppid'] = $this->ppid;
			if(empty($id)){
			    $data['createtime'] = time();
				if( model('usercenter')->cardadd($data)){
				    $this->alert('添加成功', url('index/card'));
			    }else{
				    $this->alert('添加失败');
		   	    }
			}else{
			    if( model('usercenter')->cardupdate( array('id'=>$id) ,$data) ){
				    $this->alert('修改成功', url('index/card'));
			    }else{
				    $this->alert('修改失败');
		   	    }
			}		
		}
	}
	
	public function carddel(){
		$id = intval($_GET['id']);
		$info = model('usercenter')->cardinfo( array('id'=>$id) );
		if( empty($info) ){
			$this->alert('该条数据不存在或者已被删除');
		}
		if( model('usercenter')->carddel( array('id'=>$id) ) ){
			$this->alert('删除成功', url('index/card'));
		}else{
			$this->alert('删除失败');
		}
	}
	
}