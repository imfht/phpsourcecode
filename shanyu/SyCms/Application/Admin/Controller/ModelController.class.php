<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

class ModelController extends AdminBaseController {

	public function index(){

		//分页
		$limit=$this->_page('Model');

		//数据
		$list=M('Model')
			->limit($limit)
			->where($where)
			->select();
		$this->assign('list',$list);

		$this->display();
	}
	public function add(){
		if(IS_POST){$this->addPost();exit;}

		$theme=M('Config')->where("name='DEFAULT_THEME'")->getField('value');
		$view_path = APP_PATH."Home/View/".$theme."/";
        $view_ext='.html';
        $replace=array($view_path,$view_ext);
        $tpl['list']=str_replace($replace,'',glob($view_path . 'List_*'));
        $tpl['show']=str_replace($replace,'',glob($view_path . 'Show_*'));
		$this->assign('tpl',$tpl);

		$this->display();
	}
	private function addPost(){
		$Model=D('Model');
		$data=$Model->create();
		if(!$data) $this->error($Model->getError());

		$return=$Model->add($data);
		if($return) $this->success('添加成功');
		else $this->error('添加失败');
	}

	public function edit($id){
		if(IS_POST){$this->editPost();exit;}

		$theme=M('Config')->where("name='DEFAULT_THEME'")->getField('value');
		$view_path = APP_PATH."Home/View/".$theme."/";
        $view_ext='.html';
        $replace=array($view_path,$view_ext);
        $tpl['list']=str_replace($replace,'',glob($view_path . 'List_*'));
        $tpl['show']=str_replace($replace,'',glob($view_path . 'Show_*'));
		$this->assign('tpl',$tpl);

		$info=M('Model')->find($id);
		$this->assign('info',$info);
		$this->display();
	}
	private function editPost(){
		$Model=D('Model');
		$data=$Model->create();
		if(!$data) $this->error($Model->getError());

		$return=$Model->save($data);
		if($return) $this->success('修改成功');
		else $this->error('修改失败');
	}

	public function del($id){
		$return=M('Model')->delete($id);
		if($return) $this->success('删除成功');
		else $this->error('删除失败');
	}




}