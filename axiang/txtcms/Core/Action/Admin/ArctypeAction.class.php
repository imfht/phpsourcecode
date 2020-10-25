<?php
/**
 * TXTCMS 栏目管理模块
 * @copyright			(C) 2013-2014 TXTCMS
 * @license				http://www.txtcms.com
 * @lastmodify			2014-8-8
 */
class ArctypeAction extends AdminAction {
	public $Article;
	public $Arctype;
	public $Arcbody;
	public function _init(){
		parent::_init();
		$this->Article=DB('Article');
		$this->Arctype=DB('arctype');
		$this->Arcbody=DB('arcbody');
	}
	public function index(){
		$result=$this->Arctype->order('order desc')->select();
		$data['class_list']=class_list_tree(getDataTree($result));
		$this->assign($data);
		$this->display();
	}
	public function edit(){
		$id=isset($_GET['id'])?intval($_GET['id']):'';
		$getpid=isset($_GET['pid'])?intval($_GET['pid']):'';
		if($id<>''){
			$result=$this->Arctype->where('id='.$id)->find();
		}
		$class=$this->Arctype->where('id!='.$id)->select();
		if($class){
			if($getpid==0) $getpid=$result['pid'];
			$class_option=channel_option_tree($class,0,$getpid);
		}
		$this->assign('class_option',$class_option);
		$this->assign($result);
		$this->display();
	}
	public function uporder(){
		$order=$_POST['order'];
		foreach( $order as $k=>$vo ){
			$order[$k]=trim($order[$k]);
			$this->Arctype->where('id='.$k)->data(array('order'=>$vo))->save();
		}
		$this->success('更新成功！',$_SERVER ['HTTP_REFERER']);
	}
	public function update(){
		$config=$_POST['con'];
		foreach( $config as $k=> $v ){
			$config[$k]=get_magic(trim($config[$k]));
		}
		if($config['cname']=='')  $this->ajaxReturn(array('status'=>0,'info'=>'分类名称不能为空！'));
		$where=array('id='.$config['id']);
		if($config['id']>0){
			$result=$this->Arctype->where($where)->data($config)->save();
		}else{
			$result=$this->Arctype->data($config)->add();
		}
		if($result){
			$this->ajaxReturn(array('status'=>1));
		}else{
			$this->ajaxReturn(array('status'=>0,'info'=>'保存失败！'));
		}
	}
	public function del(){
		$id=isset($_GET['id'])?intval($_GET['id']):$this->error('id 不能为空');
		$result=$this->Arctype->where('pid='.$id)->find();
		if($result) $this->error('请先删除其下的子分类！');
		$result=$this->Article->where('cid='.$id)->find();
		if($result) $this->error('请先删除分类下的文章！');

		$result=$this->Arctype->where('id='.$id)->delete();
		if(!$result) $this->error('删除失败！');
		$this->success('删除成功！',url('Admin/Arctype/index'));
	}
}