<?php
/**
 * TXTCMS 广告模块
 * @copyright			(C) 2013-2014 TXTCMS
 * @license				http://www.txtcms.com
 * @lastmodify			2014-8-8
 */
class AdAction extends AdminAction {
	public $ad;
	public function _init(){
		parent::_init();
		$this->ad=DB('myad');
	}
	public function index(){
		$data=$this->ad->select();
		$this->assign('list',$data);
		$this->display();
	}
	public function edit(){
		$id=isset($_GET['id'])?intval($_GET['id']):0;
		$result=$this->ad->where('id='.$id)->find();
		$this->assign($result);
		$this->display();
	}
	public function preview(){
		$id=isset($_GET['id'])?$_GET['id']:$this->error('id 不能为空');
		$result=$this->ad->where('id='.$id)->find();
		if($result){
			header("Content-type: text/html; charset=utf-8");
			echo '<head><title>'.$result['name'].' 广告预览</title></head><body style="font-size:12px"><p>标识符：'.$result['mark'].'</p><p>广告说明：'.$result['name'].'</p><p>调用标签：<font color="#990000">{$myad.'.$result['mark'].'}</font></p><p>以下为广告预览：</p><hr size=1>'.$result['code'].'</body>';
		}
	}
	public function update(){
		$id=isset($_GET['id'])?intval($_GET['id']):0;
		$config=$_POST['con'];
		foreach( $config as $k=> $v ){
			$config[$k]=get_magic(trim($config[$k]));
		}
		if($config['mark']=='' || $config['name']=='')  $this->ajaxReturn(array('status'=>0,'info'=>'关键项不能为空！'));
		$where=array('id='.$id);
		if($id>0){
			$result=$this->ad->where($where)->data($config)->save();
		}else{
			$result=$this->ad->data($config)->add();
		}
		if($result){
			$this->ajaxReturn(array('status'=>1));
		}else{
			$this->ajaxReturn(array('status'=>0,'info'=>'保存失败！'));
		}
	}
	public function del(){
		$id=isset($_GET['id'])?intval($_GET['id']):$this->error('id 不能为空');
		$where='id='.$id;
		$result=$this->ad->where($where)->delete();
		if(!$result) $this->error('删除失败！');
		$this->success('删除成功！',url('Admin/Ad/index'));
	}
}