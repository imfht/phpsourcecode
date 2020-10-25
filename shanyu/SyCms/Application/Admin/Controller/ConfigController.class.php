<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

class ConfigController extends AdminBaseController {

	public function index(){
		//搜索
		$where = $this->_search();

		//分组
		$groups=I('groups',0,'intval');
		$this->assign('groups',$groups);
		if($groups) $where['groups']=$groups;

		//分页
		$limit=$this->_page('Config',$where);

		//数据
		$list=M('Config')
			->limit($limit)
			->where($where)
			->order('id DESC')
			->select();
		$this->assign('list',$list);

		$this->display();
	}
	public function add(){
		if(IS_POST){$this->addPost();exit;}

		$module=D('Common/Config')->getModule();
		$this->assign('module',$module);

		$this->display();
	}
	private function addPost(){
		$Model=D('Common/Config');
		$data=$Model->create();
		if(!$data) $this->error($Model->getError());

		$return=$Model->add($data);
		if($return)$this->success('添加成功');
		else $this->error('添加失败');
	}

	public function edit($id){
		if(IS_POST){$this->editPost();exit;}
		$info=M('Config')->find($id);
		$this->assign('info',$info);

		$module=D('Common/Config')->getModule();
		$this->assign('module',$module);

		$this->display();
	}
	private function editPost(){
		$Model=D('Common/Config');
		$data=$Model->create();
		if(!$data) $this->error($Model->getError());

		$return=$Model->save($data);
		if($return)$this->success('修改成功');
		else $this->error('修改失败');
	}

	public function del($id){
		$return=D('Common/Config')->delete($id);
		if($return) $this->success('删除成功');
		else $this->error('删除失败');
	}

	//分组显示
	public function groups($id=1){
		if(IS_POST){$this->groupsPost();exit;}
		$groups=I('get.id','1');
		$this->assign('groups',$groups);

		$where['groups']=$groups;
		$list=M('Config')->where($where)->order('sort ASC,id ASC')->getField('name,title,value,remark,extra,type,status');
		$this->assign('list',$list);

		$tpl=$this->view->parseTemplate('groups-'.$id);
		if(is_file($tpl)) $this->display('groups-'.$id);
		else $this->display('groups');
	}
	private function groupsPost(){
		$post=I('post.','','trim');

        foreach ($post as $name => $value) {
        	if(is_array($value)) $value=arr_str($value);
            $map = array('name' => $name);
            D('Common/Config')->where($map)->setField('value', $value);
        }
        $this->success('保存成功！');
	}
	//上传配置
	public function upload(){
		if(IS_POST){ $this->uploadPost();exit; }
		//公用配置
		$upload=M('Config')->where('groups=11')->order('sort asc')->getField('name,value,extra,type');
		foreach ($upload as $k => &$v) {
			if($v['type']==11) $v['value']=str_arr($v['value']);
			elseif($v['type']==10) $v['extra']=str_arr($v['extra']);
			unset($v['type']);
		}
		$this->assign($upload);
		$this->display();
	}
	private function uploadPost(){
		if($_POST['FILE_UPLOAD_TYPE'] != 'Local'){
			$type_name='UPLOAD_TYPE_CONFIG_'.strtoupper($_POST['FILE_UPLOAD_TYPE']);
			$_POST['UPLOAD_TYPE_CONFIG']=$_POST[$type_name];
		}
		$this->groupsPost();
	}

	//发送测试邮件
	public function emailTest(){
		$post=I('post.');
		$Email=new \Common\Event\EmailEvent();
		$status=$Email->send($post['email'],$post['title'],$post['content']);
		if($status) $this->success('发送测试邮件成功');
		else $this->error($Email->error);
	}

	//发送测试短信
	public function smsTest(){
		$post=I('post.');
		$status=C('SMS_STATUS');
		if($status){
			$host=C('SMS_HOST');
			$Sms=\Common\Api\SmsApi::getInstance($host);
	        $status=$Sms->send($post['mobile'],$post['content']);
	        if($status){
	        	M('Config')->where("name='SMS_NUM'")->setDec('value');
	        	$this->success('对'.$post['mobile'].'发送短信成功');
	        }else{
	        	$this->error($Sms->error);
	        }
		}

	}
	//查询剩余短信数目
	public function smsQuery(){
		$status=C('SMS_STATUS');
		if($status){
			$host=C('SMS_HOST');
			$Sms=\Common\Api\SmsApi::getInstance($host);
	        $num=$Sms->query();
	        if($num){
	        	M('Config')->where("name='SMS_NUM'")->setField('value',$num);
	        	$this->success('查询短信剩余数成功');
	        }else{
	        	$this->error('查询短信剩余数失败');
	        }
		}
	}









}