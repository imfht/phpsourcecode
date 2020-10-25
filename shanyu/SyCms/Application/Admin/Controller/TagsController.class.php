<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

class TagsController extends AdminBaseController {

	public function index(){
		//Ajax批量处理
		$this->_batch('Tag');
		//搜索
		$where=$this->_search();
		//分页
		$limit=$this->_page('Tag',$where);
		//数据
		$list=M('Tag')
			->field('id,title,view,groups,status,sort')
			->limit($limit)
			->order('sort DESC')
			->where($where)
			->select();

		$Builder=A('listBuilder','Event');
		$Builder->addAction('添加标签','add','AjaxHtml btn-green')
			->addSearch('标签查询','title','like','text')
			->addSearch('分组查询','groups','eq','select',C('TAG_GROUPS'))
			->addBatch('排序','sort')
			->addBatch('禁用','disable')
			->addBatch('启用','enable')
			->addBatch('删除','del')
			->addField('批量','batch','batch')
			->addField('排序','sort','sort')
			->addField('序号','id')
			->addField('标签','title')
			->addField('点击次数','view')
			->addField('状态','status','status')
			->addField('分组','groups','cn',C('TAG_GROUPS'))
			->addField('操作','field_action');

		$Builder->addFieldAction('编辑标签','edit','AjaxHtml btn-blue')
				->addFieldAction('删除标签','del','AjaxConfirm btn-red');

		$Builder->dataList($list)
			->display();
	}

	//批量扩展方法
	protected function callBatch($batch,$pk){
		return true;
	}

	public function add(){
		if(IS_POST){$this->addPost();exit;}
		//默认值
		$info=array('sort'=>0,'status'=>1,'groups'=>0);
		$Builder=A('FormBuilder','Event');
		$Builder->addInput('标签','title','input','require','请填写标签名称')
			->addInput('排序','sort','input','','标签排序(数值越小越靠前)')
			->addInput('状态','status','radio','','启用或禁用','1:开启,0:禁用')
			->addInput('分组','groups','select','','请填写分组名称',C('TAG_GROUPS'))
			->dataInfo($info)
			->display();
	}
	private function addPost(){
		$Model=D('Tag');
		$data=$Model->create();
		if(!$data) $this->error($Model->getError());

		$return=$Model->add($data);
		if($return) $this->success('添加成功');
		else $this->error('添加失败');
	}

	public function edit($id){
		if(IS_POST){$this->editPost();exit;}

		$info=M('Tag')->find($id);

		$Builder=A('FormBuilder','Event');
		$Builder->addInput('标签','title','text','require','请填写标签名称')
			->addInput('序号','id','hidden')
			->addInput('排序','sort','text','','标签排序(数值越小越靠前)')
			->addInput('状态','status','radio','','启用或禁用','1:开启,0:禁用')
			->addInput('分组','groups','select','','请填写分组名称',C('TAG_GROUPS'))
			->dataInfo($info)
			->display();
	}
	private function editPost(){
		$Model=D('Tag');
		$data=$Model->create();
		if(!$data) $this->error($Model->getError());

		$return=$Model->save($data);
		if($return) $this->success('修改成功');
		else $this->error('修改失败');
	}

	public function del($id){
		$return=M('Tag')->delete($id);
		if($return) $this->success('删除成功');
		else $this->error('删除失败');
	}



}