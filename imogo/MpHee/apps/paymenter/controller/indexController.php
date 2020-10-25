<?php
class indexController extends adminController{
	protected $layout = 'layout';
	
	public function paymenterlist(){
		$this->paymenterlist = model('paymenter')->paymenterlist(array('ppid'=>$this->ppid));
		$this->display();
	}
	
	public function paymenteraddlist(){
		$this->paymenteraddlist = model('paymenter')->paymenteraddlist();
		$this->display();
	}
	
	public function paymenteradd(){
		if( !$this->isPost() ){
			$this->display();
		}else{
			$data = $_POST;
			$data['paytype'] = $_GET['paytype'];
			$data['ppid'] = $this->ppid;
			$data['createtime'] = time();
			if( model('paymenter')->paymenteradd($data)){
				$this->alert('添加成功', url('index/paymenterlist'));
			}else{
				$this->alert('添加失败');
			}		
		}
	}
	
	public function paymenteredit(){
		$id = intval($_GET['id']);
		if( !$this->isPost() ){
			$paymenterinfo = model('paymenter')->paymenterinfo( array('id'=>$id) );
			if( empty($paymenterinfo) ){
				$this->alert('该条数据不存在或者已被删除');
			}
			$this->paymenterinfo = $paymenterinfo;
			$this->display();
		}else{
			$data = $_POST;
			if( model('paymenter')->paymenterupdate(array('id'=>$id), $data) ){
				$this->alert('修改成功', url('index/paymenterlist'));
			}else{
				$this->alert('修改失败');
			}
		}
	}
	
	public function paymenterdel(){
		$id = intval($_GET['id']);
		$paymenterinfo = model('paymenter')->find( array('id'=>$id) );
		if( empty($paymenterinfo) ){
			$this->alert('该条数据不存在或者已被删除');
		}
		if( model('paymenter')->paymenterdelete( array('id'=>$id) ) ){
			$this->alert('删除成功', url('index/paymenterlist'));
		}else{
			$this->alert('删除失败');
		}
	}
	
}