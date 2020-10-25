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
namespace Index\Controller;
use Think\Controller;
class LoginController extends Controller {
	//登陆视图
	public function index(){
		$this->display();
	}
	public function ck_te(){
		//session('te',I('post.param'));

		$where['tname']=I('get.tename');
		$where['pass']=I('get.tpass','','md5');
		$re=M('Te')->where($where)->find();
		if ($re) {
			if ($re['status']==1) {
				$data=2;
				session('type',$re['type']);
				session('username',$re['tname']);
				session('schoolid',$re['schoolid']);
			}else{
				$data=1;
			}
		}else{
			$data=0;
		}
		$this->ajaxReturn($data);
	}
	public function ck_stu(){
		//session('te',I('post.param'));
		$where['schoolid']=I('get.schoolid');
		$where['stuid']=I('get.stuid');
		$where['pass']=I('get.spass','','md5');
		$re=M('Stu')->where($where)->find();
		if ($re) {
			if ($re['status']==1) {
				$data=2;
				//session('type',$re['type']);
				session('stuid',$re['stuid']);
				session('schoolid',$re['schoolid']);
				session('sid',$re['sid']);
				session('username',$re['sname']);

			}else{
				$data=1;
			}
		}else{
			$data=0;
		}
		$this->ajaxReturn($data);
	}
	// public function ck_stu(){
	// 	//session('te',I('post.param'));
	// 	$where['tname']=I('post.stuname');
	// 	$where['pass']=I('post.stupass','','md5');
	// 	$re=M('Te')->where($where)->find();
	// 	dump($re);
	// 	dump ($re['status']);
	// 	//$re=$where;
	// 	//$this->ajaxReturn($re);
	// }
	public function ck_uname(){
		session('te',I('post.param'));
		$where['uname']=I('post.param');
		$u=M('User');
		$re=$u->where($where)->find();
			if (!$re) {
				echo '{
					"info":"该用户名未注册！"
		 		}';
				
			} else {
				echo '{
					"info":"用户名正确！",
					"status":"y"
		 		}';
			}
	}
	public function ck_upass(){
		//dump(I('post.'));
		//dump(session('username'));
		$where['uname']=session('u');
		//$where['upass']=I('post.param','','md5');
		$u=M('User');
		$re=$u->where($where)->field('upass')->find();
		//dump($re['upass']);
			if ($re['upass']===I('post.param','','md5')) {
				echo '{
					"info":"密码正确！",
					"status":"y"
		 		}';
				
			} else {
				echo '{
					"info":"密码错误！"
		 		}';
				
			}
	}
	public function ck_code(){
		$code=I('post.param');
					$verify = new \Think\Verify();
					if (!$verify->check($code)) {
						echo '{
						"info":"验证码错误！"
		 				}';
				
					} else {
						echo '{
						"info":"验证码正确！",
						"status":"y"
		 				}';
					}
	}
	//登陆验证码
	public function code(){
		// import('ORG.Util.Image');
		// Image::buildImageVerify(4,1);
		$config =array(
			'fontSize' => 30, // 验证码字体大小
			'length' => 3, // 验证码位数
			 'useNoise' => false, // 关闭验证码杂点
			);
		$Verify = new \Think\Verify();
		$Verify->entry();
	}
	//登陆表单处理
	public function _before_do_login(){ 
		if(session('username')===I('post.uname')) {
            $this->error('该账号已登录，请勿重复登录',U('Login/index'),3);
                    		//$this->redirect('Login/index'); 
	    }   
	}
	public function do_login(){
		if(!IS_POST){
			$this->error('无此页面');
		}else{
	    //         	$code=I('post.code');
					// $verify = new \Think\Verify();
					// if (!$verify->check($code)) {
					// $this->error('验证码错误');
					// }
				// if(I('post.code','0','md5')!=I('session.verify')){
				// 	$this->error('验证码错误');
				// }
				$user=M(User);
				$where['uname']=I('post.uname');
				$where['upass']=I('post.upass','','md5');
				$arr=$user->where($where)->find();
				if(!$arr){
					$this->error('账号或者密码错误');
				}
				$data=array(
					'logintime'=>time(),
					'loginip'=>get_client_ip(),
					'online'=>1
				);
				$user->where($where)->save($data);
				//判断管理员
				$type=M("type");
				$wheret['id']=$arr['tid'];
				$typearr=$type->where($wheret)->find();
				//写如session
				session('type',$typearr['type']);
				session('u',null);
				session('username',I('post.uname'));
				session('password',I('post.upass','','md5'));
				session('uid',$arr['id']);
				session('did',$arr['did']);
				session('logintime',date('Y-m-d H:i:s',$arr['logintime']));
				session('loginip',$arr['loginip']);
				//判断职位
				$position=M("position");
				$wherep['id']=$arr['pid'];
				$positionarr=$position->where($wherep)->find();
				session('position',$positionarr['position']);
				session('pid',$positionarr['id']);
				//redirect(U('Index/index'));
				if (!empty($arr['lasttime'])) {
					$this->success('SikuOA欢迎您！</br>您上次登录时间为：<font color="green">'.date('Y-m-d H:i:s',$arr['logintime']).'</font>',U('Index/index'),3);
				} else {
					$this->success('SikuOA欢迎您！</br><font color="green">新注册会员</font>',U('Index/index'),3);
				}	
		}
					
	}
	//登出处理
	public function out_login(){
		// //更新登录时间
		// $time=M('User');
		// $where['uname']=I('session.username');
		// $logintime=$time->where($where)->getfield('logintime');
		// // $data= array('lasttime' => $arr['logintime']);
		// $time->lasttime=$logintime;
		// $time->online=0;
		// $time->field('lasttime,online')->where($where)->save();
		//注销session
		$_SESSION=array();
		//判断是否保存了cookie
			if(isset($_COOKIE[session_name()])){
				setcookie(session_name(),'',time()-1,'/');
			}		
		session('[destroy]'); //这是一个删除的函数
		//$this->redirect('index');
		$this->success('欢迎您再来！',U('Index/Login/index'),2);
	}		
}
 ?>