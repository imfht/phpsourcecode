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
class TeacherController extends ComController {
   
   
	function  index(){
		//phpinfo();
		$te=M('Te');
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
		$list = $te->order('tid desc')->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($show);
		$this->assign('num',$count);
		$this->assign('te',$list);
		$this->assign('page',$show);// 赋值分页输出
		$this->display();
		// $te=M('Te');
		// $t=$te->order('tid desc')->select();
		// $count=count($t);
		// $re=getpage('Te',5);
		// $this->assign('page',$re);
		// $this->assign('te',$t);
		// $this->assign('num',$count);
		// $this->display();
	}
	public function tsearch (){
		$tname=I('post.tname');
		$schoolid=I('post.schoolid');
		if (!empty($tname)) {
			$where['tname'] = array('like',$tname.'%');//模糊查询
		}		
		if (session('type')!=2) {
			$where['schoolid']=session('schoolid');
		}else{
			if (!empty($schoolid)) {
			$where['schoolid'] = array('like',$schoolid.'%');//模糊查询
			}
		}
		$re=M('te')->where($where)->select();
		$this->assign('num',count($re));
		$this->assign('te',$re);
		$this->display('index');
	}
	function user_stop(){
		//$tid=I('get.tid');
		$User = M("Te"); 
		$where['tid']=I('get.tid');
		// 更改用户的status值
		$re=$User-> where($where)->setField('status',0);
		$this->ajaxReturn($re);
	}
	function user_open(){
		//$tid=I('get.tid');
		$User = M("Te"); 
		$where['tid']=I('get.tid');
		// 更改用户的status值
		$re=$User-> where($where)->setField('status',1);
		$this->ajaxReturn($re);
	}
	function  tedit(){
		$where['tid']=(I('get.id'));
		$t=M('te')->where($where)->find();
		$this->assign('te',$t);
		$this->display();
	}
	function do_tedit(){
		$where['tid']=I('post.tid');
		$data['tname']=I('post.tname');
		$data['truename']=I('post.truename');
		$data['type']=I('post.type');
		$data['schoolid']=I('post.schoolid');
		if (is_md5(I('post.pass'))) {
			$data['pass']=I('post.pass');
		}else{
			$data['pass']=I('post.pass','','md5');
		}
		$User = M("Te"); 
		$re=$User-> where($where)->save($data);	 
		$this->ajaxReturn($re);
		$this->display();
	}
	function  tdel(){
		$where['tid']=I('get.tid');
		$res= M("Te")->where($where)->delete(); 
		if ($res=false) {
			$re=0;
		} else {
			$re=1;
		}
		$this->ajaxReturn($re);
		//$this->display();
	}
	public function tadd(){
		$re=I('post.tname');
		//$this->ajaxReturn($re);
		$this->display();
	}
	public function do_tadd(){
		//$re=I('post.tname');
		$data['tname']=I('post.tname');
		$data['truename']=I('post.truename');
		$data['schoolid']=I('post.schoolid');
		$data['type']=I('post.type');
		$data['pass']=I('post.pass','','md5');
		$data['status']=0;
		$data['ctime']=time();
		$User = M("Te"); 
		$res=$User->add($data);
		if ($res=false) {
			$re=0;
		} else {
			$re=1;
		}	 
		$this->ajaxReturn($re);
		//$this->display();
	}
	public function delall(){
		$tid=I('get.tid');
		// if (in_array(13,$tid)) {
		//  	echo 'yes';
		//  } 
		
		  $where = 'tid in('.implode(',',$tid).')';
		 $User = M("Te"); 
		 $num=$User->where($where)->delete();
		 if($num!==false) {
		 	$re=$num;
		 }else{
		  $re=0;
		}
		$this->ajaxReturn($re);
		//$this->display();
	}
}
