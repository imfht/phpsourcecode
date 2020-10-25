<?php
/**
 * TXTCMS 友情链接模块
 * @copyright			(C) 2013-2014 TXTCMS
 * @license				http://www.txtcms.com
 * @lastmodify			2014-8-8
 */
class FlinkAction extends AdminAction {
	public $flink;
	public function _init(){
		parent::_init();
		$this->flink=DB('flink');
	}
	public function index(){
		$data=$this->flink->order('order desc')->select();
		$this->assign('list',$data);
		$this->display();
	}
	public function edit(){
		$id=isset($_GET['id'])?intval($_GET['id']):0;
		$result=$this->flink->where('id='.$id)->find();
		$this->assign($result);
		$this->display();
	}
	public function uporder(){
		$order=$_POST['order'];
		foreach( $order as $k=>$vo ){
			$order[$k]=trim($order[$k]);
			$this->flink->where('id='.$k)->data(array('order'=>$vo))->save();
		}
		$this->success('更新成功！',$_SERVER ['HTTP_REFERER']);
	}
	public function update(){
		$id=isset($_GET['id'])?intval($_GET['id']):0;
		$config=$_POST['con'];
		foreach( $config as $k=> $v ){
			$config[$k]=get_magic(trim($config[$k]));
		}
		if($config['title']=='' || $config['url']=='')  $this->ajaxReturn(array('status'=>0,'info'=>'关键项不能为空！'));
		$config['style']=$config['style2'].$config['style1'];
		unset($config['style1']);
		unset($config['style2']);
		$where=array('id='.$id);
		if($id>0){
			$result=$this->flink->where($where)->data($config)->save();
		}else{
			$result=$this->flink->data($config)->add();
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
		$result=$this->flink->where($where)->delete();
		if(!$result) $this->error('删除失败！');
		$this->success('删除成功！',url('Admin/Flink/index'));
	}
}