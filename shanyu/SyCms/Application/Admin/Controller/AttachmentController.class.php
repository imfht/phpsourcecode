<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

class AttachmentController extends AdminBaseController {

	public $int_type=array(
		1=>'图片',
		2=>'缩略图',
		3=>'文档',
	);

	public function index(){
		//搜索
		$where=$this->_search();

		//分页
		$limit=$this->_page('Attachment',$where);

		//数据
		$list=M('Attachment')
			->limit($limit)
			->where($where)
			->order('id DESC')
			->select();
		$this->assign('list',$list);

		$int_type=$this->int_type;
		$this->assign('int_type',$int_type);

		$this->display();
	}

	public function edit($id){

		if(IS_POST){$this->editPost($id);exit;}
		$info=M('Attachment')->find($id);
		$info['url']='/Uploads/'.$info['path'].'/'.$info['name'].'.'.$info['ext'];
		$this->assign('info',$info);
		$this->display();
	}
	private function editPost($id){
		$Model=D('Common/Attachment');
		$data=$Model->create();
		if(!$data) $this->error($Model->getError());

		$return=$Model->save($data);
		if($return) $this->success('修改成功');
		else $this->error('修改失败');
	}

	public function clear(){
		D('Common/Attachment')->delFile();
		$this->success('沉余文件删除成功');
	}



}