<?php
/**
 * TXTCMS 文档属性模块
 * @copyright			(C) 2013-2014 TXTCMS
 * @license				http://www.txtcms.com
 * @lastmodify			2014-8-29
 */
class ArcflagAction extends AdminAction {
	public $Arcflag;
	public function _init(){
		parent::_init();
		$this->Arcflag=DB('Arcflag');
	}
	public function index(){
		$data=$this->Arcflag->order('id desc')->select();
		$this->assign('list',$data);
		$this->display();
	}
	public function edit(){
		$id=isset($_GET['id'])?intval($_GET['id']):0;
		$result=$this->Arcflag->where('id='.$id)->find();
		$this->assign($result);
		$this->display();
	}
	public function update(){
		$id=isset($_GET['id'])?intval($_GET['id']):0;
		$config=$_POST['con'];
		foreach( $config as $k=> $v ){
			$config[$k]=get_magic(trim($config[$k]));
		}
		if($config['en']=='' || $config['cn']=='')  $this->ajaxReturn(array('status'=>0,'info'=>'关键项不能为空！'));
		$where=array('id='.$id);
		if($id>0){
			$result=$this->Arcflag->where($where)->data($config)->save();
		}else{
			$result=$this->Arcflag->data($config)->add();
		}
		if($result){
			$this->ajaxReturn(array('status'=>1));
		}else{
			$this->ajaxReturn(array('status'=>0,'info'=>'保存失败！'));
		}
	}
	public function del(){
		$id=isset($_GET['id'])?intval($_GET['id']):$this->error('id 不能为空');
		$where=array('id='.$id);
		$result=$this->Arcflag->where($where)->delete();
		if(!$result) $this->error('删除失败！');
		$this->success('删除成功！',url('Admin/Arcflag/index'));
	}
}