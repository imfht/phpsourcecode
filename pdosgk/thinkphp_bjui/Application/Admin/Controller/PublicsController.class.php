<?php
namespace Admin\Controller;
use Admin\Controller\AdminController;

/**
 * 后台公共部分
 * @author Lain
 *
 */
class PublicsController extends AdminController {
	private $setting;
	private $randomKey = 'demo';		//登录页加密码
	//基础设置
	public function _initialize(){
		
		//基础设置
		$this->setting = array(
				'enableCodeCheck' 	=>	false,	//开启登录验证码
				'errorTime'			=>	1, 		//错误次数, 达到错误次数后, 则需要验证码
				'errorMaxTime'		=>	2,		//允许的最大错误次数, 达到后, 则需要等待时间后, 才能登录
				'waitTime'			=> 20,		//需要等待的时间(分钟)
		);
	}
	
	//直接登录
	public function login(){
		//加密码- 测试
		$randomKey = $this->randomKey;
		
	    if($_POST){
			$username = I('post.username');
			$passwordhash = I('post.passwordhash');
			//$demo = hash_hmac('sha256',password, username);
			$setting = $this->setting;
			//如果开启验证码, 检测错误次数
			if($setting['enableCodeCheck']){
				$rtime = D('Times')->where(array('username'=>$username,'isadmin'=>1))->find();

				//密码错误10次，则需要等待1小时后，才能再次登录
				if($rtime['times'] >= $setting['errorMaxTime']) {
					$minute = $setting['waitTime'] - floor((NOW_TIME-$rtime['logintime'])/60);
					if($minute > 0) 
						$this->error('请等待'.$minute.'分钟后再次登录');
				}
				//如果登录密码错误次数大于， 则检测验证码
				if($rtime['times'] >= $setting['errorTime']){
					$code = I('post.code');
			    	$verify = new \Think\Verify();
					if(!$code)
						$this->error('请输入验证码');
					if(!$verify->check($code)){
						$this->error('验证码输入有误');
					}
				}
			}
			
			$map['username'] = $username;
			$detail = D('Admin')->where($map)->find();
			if (! $detail)
				$this->error('用户名，密码错误');

			//验证密码

			$ip = get_client_ip(0, true);
			if (hash_hmac('sha256', $detail['password'], $randomKey) != $passwordhash){
				//如果开启验证码, 则增加错误次数
				if($setting['enableCodeCheck']){
					//小于最大允许错误次数, 则增加错误计数
					if($rtime && $rtime['times'] < $setting['errorMaxTime']) {
						$times = 8-intval($rtime['times']);
						$result = D('Times')->where(array('username'=>$username))->save(array('ip'=>$ip,'isadmin'=>1,'times'=>array('exp', 'times+1')));
					} else {
						//如果超过最大计数后, 则重新开始计数
						D('Times')->where(array('username'=>$username,'isadmin'=>1))->delete();
						D('Times')->add(array('username'=>$username,'ip'=>$ip,'isadmin'=>1,'logintime'=>NOW_TIME,'times'=>1));
					}
				}
				$this->error('用户名，密码错误');
			} else{
				//更新登录信息
				D('Admin')->where('userid='.$detail['userid'])->save(array('lastloginip'=>$ip, 'lastlogintime'=>NOW_TIME));

				//删除登录错误次数
				if($setting['enableCodeCheck']){
					D('Times')->where(array('username' => $username,'isadmin' => 1))->delete();
				}
				//记录session
				session('userid',$detail['userid']);
				session('roleid',$detail['roleid']);
				if($detail['roleid'] == '1'){
					session('admin',1);
				}
				//session('pc_hash', random(6,'abcdefghigklmnopqrstuvwxwyABCDEFGHIGKLMNOPQRSTUVWXWY0123456789'));
				//session('AccountInfo',$detail);
					
				$cookie_time = 86400*30;
				cookie('username',$username,$cookie_time);
				cookie('userid', $detail['userid'],$cookie_time);
					
				
					
				redirect('/admin.php');
			}
		}else {
			$this->assign('randomKey', $randomKey);
			$this->display();
		}
	}
	
	//ajax登录
	public function ajax_login(){
		//加密码- 测试
		$randomKey = $this->randomKey;
	
		if($_POST){
		    D('Admin')->setRandomKey($this->randomKey);
		    if(D('Admin')->login()){
		        $this->ajaxReturn(array('flag'=>true));
		    }else{
		        $this->ajaxReturn(array('flag' => false, 'message' => D('Admin')->getError()));
		    }
		}
	}
	
	//会话超时
	public function timeout(){

		/* if ($_SESSION['authId'])
		 redirect('/admin.php'); */
		if($_POST){
			$Admin = D('Admin');
			$username=I('post.username');
			$password=I('post.password');
			$map['username'] = $username;
			$detail = D('Admin')->where($map)->find();
			
			if(!$detail){
				$this->ajaxReturn(array('statusCode'=>300,'message'=>'账户密码错误'));
			}else{
				//判断密码
				if(hash_hmac('sha256', $password, $username) != $detail['password']){
					$this->ajaxReturn(array('statusCode'=>300,'message'=>'账户密码错误'));
				}else{
					//更新登录信息
					D('Admin')->where('userid='.$detail['userid'])->save(array('lastloginip' => get_client_ip(0, true), 'lastlogintime'=>NOW_TIME));
					
					//记录session
					session('userid',$detail['userid']);
					session('roleid',$detail['roleid']);
					if($detail['roleid'] == '1'){
						session('admin',1);
					}
					//session('pc_hash', random(6,'abcdefghigklmnopqrstuvwxwyABCDEFGHIGKLMNOPQRSTUVWXWY0123456789'));
					//session('AccountInfo',$detail);
						
					$cookie_time = 86400*30;
					cookie('username',$username,$cookie_time);
					cookie('userid', $detail['userid'],$cookie_time);
					$this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>'true','message'=>'登录成功'));
				}
			}
			
		}else {
		
			$this->display();
		}
		
	}
	//登录页面的用户名检测, 如果没有次数限制, 则返回真.
	public function ajax_check_username(){
	    
		if($_POST){
			$username = I('post.username');
			if(D('Admin')->check_username_for_code($username)){
				$return['flag'] = false;
			}else{
				$return['flag'] = true;
			}
			$this->ajaxReturn($return);
		}
	}
	
	//退出
	public function logout(){
		session(null); 	// 清空当前的session
		cookie(null);
		redirect(U('Publics/login'));
	}
	
	//获取验证码
	public function verfy(){
		$Verify = new \Think\Verify();
		$Verify->entry();
		
	}
	
	//导入phpcms的new表和new_data表到article_category表
	public function getPhpcmsData(){
		//导入category表
		$category_lists = M()->table('v9_category')->field('catid, type, parentid, arrparentid, child, arrchildid, catname, listorder, letter')->select();
		$category_num = 0;
		foreach ($category_lists as $v){
			$result = D('Category')->add($v);
			if($result)
				$category_num++;
		}
		echo '导入分类'.$category_num.'<br>';
		//取出文章
		$module = 'v9_picture';
		$article_list = M()->table($module)->field('id, catid, title, thumb, keywords, description, url, status, inputtime, updatetime')->select();
		//取出文章详细， 并存储到article表
		$article_num = 0;
		foreach ($article_list as $v){
			$detail_article = M()->table($module.'_data')->field('content')->find();
			$v['content'] = $detail_article['content'];
			$result = D('Content')->add($v);
			if($result)
				$article_num ++;
		}
		echo '导入完成'.$article_num;
	}
	
	public function test(){
	    D('Content')->getTuicoolContent('7jEnAjq');
	}
}