<?php
/**
	 * CourseSEL  前台
	 * @Author hxb0810(halexcode)
	 * Email: hxb0810@163.com
	 * Tel:   15534378771
	 * Date:  2017-10-07 21:00
	 * @Tool Sublime
	 * 
	 */
// 本类由系统自动生成，仅供测试用途
namespace Index\Controller;
use Think\Controller;
class IndexController extends ComController {
   
   /**
	 *
	 * Enter 导出excel共同方法 ...
	 * @param unknown_type $expTitle
	 * @param unknown_type $expCellName
	 * @param unknown_type $expTableData
	 */
   public function index(){
   	//dump(session());
   	$map['sid']=session('sid');
   	$stu=M('stu')->where($map)->find();
   	$where['year']=$stu['year'];
   	$where['schoolid']=session('schoolid');
   	$sel=M('sel')->where($where)->order('seid desc')->select();
   	//dump($sel);
   	//dump(session());
   	$this->assign('stu',$stu);
   	$this->assign('sel',$sel);
   	$this->display();
   }
    function sel_check(){
    	$seid=I('get.seid');
    	$where['seid']=$seid;
    	$sel_status=M('sel')->where($where)->getField('status');
    	$this->ajaxReturn($sel_status);
    	//$this->display('index');
    }
    public function selection(){
    	$where['seid']=I('get.seid');
    	$sel=M('sel')->where($where)->find();
    	$stusel=M('selstu')->where('sid='.session('sid'))->where($where)->getField('course');
    	//dump(ch2arr($stusel));
    	$this->assign('stusel',ch2arr($stusel));
    	$this->assign('sel',$sel);
    	$this->display();
    }
    public function check_sel(){
    	$seid=I('get.seid');
    	$seldata= I('get.seldata');
    	$count=count($seldata);
    	//dump($seldata);	
    	$sel=M('sel')->where('seid='.$seid)->getField('allowgroup');
    	$stusel=M('selstu')->where('sid='.session('sid'))->getField('course');
    	//dump(in_array(implode('', $seldata), explode(',', $sel)));
    	if ($count!=3) {
    		$data=1;
    	} elseif (in_array(implode('', $seldata), explode(',', $sel))==false) {
    		$data=0;
    	} elseif (implode('', $seldata)==$stusel) {
    		$data=2;
    	}else{
    		$data=3;
    	}
    	$this->ajaxReturn($data);
    	//$this->display('selection');
    }
    public function do_sel(){
    	$data['action']= I('post.action');
    	$map['sid']=$data['sid']=session('sid');
    	$data['seid']=I('post.seid');
    	$data['sname']=session('username');
    	$data['schoolid']=session('schoolid');
    	$data['class']=M('stu')->where('sid='.session('sid'))->getField('class');
    	$data['year']=I('post.year');
    	$data['course']=implode('', I('post.seldata'));
    	$data['stime']=time();
    	$User = M("selstu"); 
    	//$map['sid']=session('sid');$map['seid']=I()
    	if ($data['action']=='提交') {
    		$res=$User->add($data);
    	} else {
    		$res=$User->where($map)->where('seid='.$data['seid'])->field('course')->save($data);
    	}
		if ($res===false) {
			$re=0;
		} else {
			$re=1;
		}	
		$rr=M('sellog')->add($data);
    	$this->ajaxReturn($re);
    }
  public function mysel(){
	  	$where['sid']=session('sid');
	  	$sel=M('sellog')->where($where)->order('id desc')->select();
  		$this->assign('sel',$sel);
  		$this->display();
    }
    public function pass(){
    	$this->display();
    }
    function check_pass(){
    	$pass=I('post.pass','','md5');
    	$stupass=M('stu')->where('sid='.session('sid'))->getField('pass');
    	if ($pass!=$stupass) {
    		$re=0;
    	} else {
    		$re=1;
    	}
    	
    	$this->ajaxReturn($re);
    }
    function do_pass(){
    	$pass=I('post.pass','','md5');
    	$re=M('stu')-> where('sid='.session('sid'))->setField('pass',$pass);
    	$this->ajaxReturn($re);
    }
	function  index00(){
		//phpinfo();
		//dump(session());
		$where['sname']=session('username');
		$where['schoolid']=session('schoolid');
		$where['sid']=session('sid');
		$stusel=M('selstu')->where($where)->select();
		dump($stusel);

		$this->display();
	}
	
}
