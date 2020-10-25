<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

class CategoryController extends AdminBaseController {

	public function index(){
		//批量处理
		$this->_batch('Category');

		//数据
		$list=M('category')->order('sort ASC')->select();
		$list=\Lib\ArrayTree::listLevel($list);

		$model=M('Model')->getField('id,model_name');
		$model[0]='独立模型';
		$this->assign('model',$model);

		$this->assign('list',$list);
		$this->display();
	}

	public function add(){
		if(IS_POST){$this->addPost();exit;}
		$pid=I('pid','0');
		$this->assign('pid',$pid);
		$mid=I('mid','1');
		$this->assign('mid',$mid);

		$this->initVar();

		$this->display();
	}
	private function addPost(){
		$Model=D('Category');
		$data=$Model->create();
		if(!$data) $this->error($Model->getError());

		$id=$Model->add($data);
		if($id) $this->success('添加成功');
		else $this->error('添加失败');
	}

	public function edit($id){
		if(IS_POST){$this->editPost($id);exit;}

		$this->initVar();

		$info=M('Category')->find($id);
		$setting=unserialize($info['setting']);
		$this->assign('setting',$setting);
		$this->assign('info',$info);

		$this->display();
	}
	private function editPost($id){
		$Model=D('Category');
		$data=$Model->create();
		if(!$data) $this->error($Model->getError());
		$return = $Model->where("id={$id}")->save($data);

		if($return)$this->success('修改成功');
		else $this->error('修改失败');
	}

	//初始化模板变量
	private function initVar(){
		$model=M('Model')->getField('id,model_name');
		$model[0]='独立模型';
		$this->assign('model',$model);

		$cate=M('Category')->where('is_menu=1')->getField('id,title');
		$this->assign('cate',$cate);

		//获取模板
		$theme=M('Config')->where("name='DEFAULT_THEME'")->getField('value');
		$view_path = APP_PATH."Home/View/".$theme."/";
        $view_ext='.html';
        $replace=array($view_path,$view_ext);
        $tpl['list']=str_replace($replace,'',glob($view_path . 'List_*'));
        $tpl['show']=str_replace($replace,'',glob($view_path . 'Show_*'));
        $tpl['page']=str_replace($replace,'',glob($view_path . 'Page_*'));
		$this->assign('tpl',$tpl);
	}

	public function del($id){

		$child=M('Category')->where("pid={$id}")->count();
		if($child) $this->error("请先删除当前栏目的子栏目");

		$return=D('Category')->delete($id);
		if($return) $this->success('删除成功');
		else $this->error('删除失败');
	}




}