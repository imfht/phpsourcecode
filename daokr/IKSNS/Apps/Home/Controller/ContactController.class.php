<?php
/*
 * IKPHP 爱客开源社区 @copyright (c) 2012-3000 IKPHP All Rights Reserved 
 * @author 小麦
 * @Email:810578553@qq.com
 * @我关注的人 关注我的人 邀请好友 下期开发
 */
namespace Home\Controller;
use Common\Controller\FrontendController;
class ContactController extends FrontendController {
	public function _initialize() {
		parent::_initialize ();
		$this->user_mod = D ('Common/User');
			// 访问者控制
		if (!is_login() && in_array ( ACTION_NAME, array (
				'follow','followed','invite','unfollow','userfollow',
		) )) {
			$this->redirect ( 'home/user/login' );
		} else {
			$this->userid = $this->visitor['userid'];
		}
	}	
	//关注我的人
	public function followed(){
		$userid = $this->_get('userid');
		$strUser = $this->user_mod->getOneUser($userid);
		if(!empty($strUser['userid'])){
			//关注我的人
			$arrFollowedUsers = $this->user_mod->getUserFollow($userid);
			foreach($arrFollowedUsers as $key=>$item){
				$arrFollowedUser[$key] = $item;
				$isfollow = $this->user_mod->isFollow($this->userid,$item['userid']);
				$arrFollowedUser[$key]['isfollow'] = empty($isfollow) ? 0 : 1; //我是否已经关注过他  0表示没关注 1 关注了
			}
			if($userid == $this->userid)
			{
				$title = '关注我的人';
			}else{
				$title = '关注'.$strUser['username'].'的人';
			}
		}else{
			$this->error('您访问的页面不存在！');
		}
		
		$this->assign ( 'strUser', $strUser );
		$this->assign ( 'arrFollowedUser', $arrFollowedUser );		
		$this->_config_seo ( array (
				'title' => $title,
				'subtitle' => '用户'
		) );
		$this->display ();
	}
	// 关注某人
	public function userfollow(){
		$userid = $this->userid;
		$userid_follow = $this->_get('userid');//要关注人的id
		if(empty($userid_follow)){ $this->error('操作错误！');}
		$isuser = $this->user_mod->isUser($userid_follow);
		if(!$isuser){
			$this->error('不存在该用户！');
		}
		$isFollow = $this->user_mod->isFollow($userid,$userid_follow);
		if($isFollow){
			$this->error("请不要重复关注同一用户！");
		}else{
			//执行关注
			$data = array('userid'=>$userid, 'userid_follow'=>$userid_follow, 'addtime'=>time());
			$this->user_mod->follow_user($userid, $userid_follow);			
			//发送消息 
			//下次开发这个功能
			
			$doname = $this->user_mod->where(array('userid'=>$userid_follow))->getField('userid');
			// 来路
			if(isset ( $_SERVER ['HTTP_REFERER'] )) {
				$this->redirect($_SERVER ['HTTP_REFERER']);
			}else{
				$this->redirect ( 'space/index/index', array('id'=>$doname));
			}
		}
	}
	// 取消关注某人
	public function unfollow(){
		$type = $this->_get ( 'd', 'trim' );
		
		$userid = $this->userid;	
		if (! empty ( $type )) {
			switch ($type) {
				// ajax 取消
				case "user_nofollow_ajax" :
					$userid_follow = $this->_post('userid'); //要取消关注人的id
					$isunFollow = $this->user_mod->isunFollow($userid,$userid_follow);
					//执行取消关注
					if($isunFollow){
						$this->user_mod->unfollow_user($userid, $userid_follow);
						$cout_follow = $this->user_mod->field('count_follow')->where(array('userid'=>$userid))->find();
						$arrJson = array('r'=>1, 'num'=>$cout_follow['count_follow']);
					}else{
						$cout_follow = $this->user_mod->field('count_follow')->where(array('userid'=>$userid))->find();
						$arrJson = array('r'=>0, 'num'=>$cout_follow['count_follow']);
					}
					header("Content-Type: application/json", true);
					echo json_encode($arrJson);
					break;			
			}
		
		} else {
			$userid_follow = $this->_get('userid'); //要取消关注人的id
			$isunFollow = $this->user_mod->isunFollow($userid,$userid_follow);			
			if(!$isunFollow){
				$this->error("已经取消关注该用户了！");
			}
			if(empty($userid_follow)){
				$this->error('操作错误！');
			}
			$isuser = $this->user_mod->isUser($userid_follow);
			if(!$isuser){
				$this->error('不存在该用户！');
			}
			//执行取消关注
			$this->user_mod->unfollow_user($userid, $userid_follow);
	
			$doname = $this->user_mod->where(array('userid'=>$userid_follow))->getField('userid');
			$this->redirect ( 'space/index/index', array('id'=>$doname));
		}
	}	
	// 我关注的人
	public function follow(){
		$userid = $this->_get('userid');
		$strUser = $this->user_mod->getOneUser($userid);
		if(!empty($strUser['userid'])){
			//我关注的人
			$arrFollowUser = $this->user_mod->getfollow_user($userid);
			if($userid == $this->userid)
			{
				$title = '我关注的人';
			}else{
				$title = $strUser['username'].'关注的人';
			}
		}else{
			$this->error('您访问的页面不存在！');
		}
		
		$this->assign ( 'strUser', $strUser );
		$this->assign ( 'arrFollowUser', $arrFollowUser );
		$this->_config_seo ( array (
				'title' => $title,
				'subtitle' => '用户'
		) );
		$this->display ();
	}
	//邀请好友加入爱客网
	public function invite(){
		$this->error("页面还是开发中");
	}		
}