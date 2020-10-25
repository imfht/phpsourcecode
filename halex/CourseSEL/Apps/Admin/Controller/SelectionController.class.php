<?php
/**
	 * CourseSEL   后台
	 * @Author hxb0810(halexcode)
	 * Email: hxb0810@163.com
	 * Tel:   15534378771
	 * Date:  2017-10-07 21:00
	 * @Tool Sublime
	 * 
	 */
// 本类由系统自动生成，仅供测试用途
namespace Admin\Controller;
use Think\Controller;
class SelectionController extends ComController {   
	public	function  index($seid=1,$schoolid=4){
		// //phpinfo();
		//echo round(0.684,2);
		//echo (2/3)*100;
		$te=D('sel');
			$count      = $te->count();
			$Page       = new \Think\Page($count,5);
			$Page->setConfig('prev',  '<span >上一页</span>');//上一页
			$Page->setConfig('next',  '<span >下一页</span>');//下一页
			$Page->setConfig('first', '<span >首页</span>');//第一页
			$Page->setConfig('last',  '<span >尾页</span>');//最后一页
		$show       = $Page->show();// 分页显示输出
		if (session('type')!=2) {
			//echo session('type');
			$where['schoolid']=session('schoolid');
		}
		$list = $te->order('seid desc')->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($list);
		$this->assign('num',$count);
		$this->assign('sel',$list);
		$this->assign('page',$show);// 赋值分页输出
		 $this->display();
	}
	public function seladd(){
		$this->display();
	}
	public function close(){
		$this->display();
	}
	public function do_seladd(){
		//$re=I('post.tname');
		$data['title']=I('post.title');
		$data['schoolid']=I('post.schoolid');
		$data['creator']=session('username');
		$data['ctime']=time();
		$data['year']=I('post.year');
		$data['description']=I('post.description','&nbsp;','');
		// foreach (I('post.allowgroup') as $key => $value) {
		// 	$allowgroup.= "$value,";
		// }

		$data['allowgroup']=implode(',', I('post.allowgroup')) ;
		//dump($data);
		//$re=$data['sname'];
		$User = M("sel"); 
		$res=$User->add($data);
		if ($res==false) {
			$this->error('添加失败',U('close'),1);
		} else {
			$this->success('添加成功',U('close'),1);
			
		}	 
		//$this->ajaxReturn($data['allowgroup']);
		//$this->display();
	}
	public function seledit(){
		$where['seid']=I('get.id');
		$sel= M("sel")->where($where)->find();
		$allowgroup=explode(',', $sel['allowgroup']);
		// dump($sel);
		// dump($allowgroup);
		$this->assign('sel',$sel);
		$this->assign('allowgroup',$allowgroup);
		$this->display();
	}
	public function do_seledit(){
		$where['seid']=I('get.id');
		$data['title']=I('post.title');
		$data['schoolid']=I('post.schoolid');
		$data['year']=I('post.year');
		$data['creator']=session('username');
		$data['ctime']=time();
		$data['description']=I('post.description','&nbsp;','');
		// foreach (I('post.allowgroup') as $key => $value) {
		// 	$allowgroup.= "$value,";
		// }

		$data['allowgroup']=implode(',', I('post.allowgroup')) ;
		//dump($data);
		$User = M("sel"); 
		$res=$User->where($where)->save($data);
		if ($res!==false) {
			$this->success('修改成功',U('close'),1);	
		} else {
			$this->error('修改失败',U('close'),1);
			
		}	 
		//$this->ajaxReturn($data['allowgroup']);
		//$this->display();
	}
	function  seldel(){
		$where['seid']=I('get.id');
		$map['seid'] =I('get.id');
		$res= M("sel")->where($where)->delete();
		$restusel=M("selstu")->where($map)->delete();
		if ($res==false) {
			$re=0;
		} else {
			$re=1;
		}
		$this->ajaxReturn($re);
		//$this->display();
	}
	public function delallsel(){
		$seid=I('get.id');
		 $where = 'seid in('.implode(',',$seid).')';
		// $map= 'seid in('.implode(',',$seid).')';
		 $User = M("sel"); 
		 $num=$User->where($where)->delete();
		 $numstusel=M("selstu")->where($where)->delete();
		 if($num!==false) {
		 	$re=$num;
		 }else{
		  $re=0;
		}
		$this->ajaxReturn($re);
		//$this->display();
	}
	function sel_stop(){
		//$tid=I('get.tid');
		$User = M("sel"); 
		$where['seid']=I('get.id');
		// 更改用户的status值
		//$map['inid']=I('get.id');
		$re=$User-> where($where)->setField('status',0);
		//$restusel=M('selstu')->where($where)->setField('status',0);
		$this->ajaxReturn($re);
	}
	function sel_open(){
		//$tid=I('get.tid');
		$User = M("sel"); 
		$where['seid']=I('get.id');
		// 更改用户的status值
		//$map['inid']=I('get.id');
		$re=$User-> where($where)->setField('status',1);
		//$restu=M('selstu')->where($where)->setField('status',1);
		$this->ajaxReturn($re);
	}
	public function selsearch (){
		$title=I('post.title');
		$schoolid=I('post.schoolid');
		$where['title'] = array('like','%'.$title.'%');//模糊查询	
		if (session('type')!=2) {
			$where['schoolid']=session('schoolid');
		}else{
			if (!empty($schoolid)) {
			$where['schoolid'] = $schoolid;//模糊查询
			}
		}
		$re=D('sel')->order('seid desc')->where($where)->select();
		$this->assign('num',count($re));
		$this->assign('sel',$re);
		$this->display('index');
	}
}
