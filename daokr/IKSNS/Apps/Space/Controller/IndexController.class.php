<?php
/*
 * IKPHP 爱客开源社区 @copyright (c) 2012-3000 IKPHP All Rights Reserved 
 * @author 小麦
 * @Email:810578553@qq.com
 * 个人空间APP首页
 */
namespace Space\Controller;
use User\Api\UserApi;

class IndexController extends SpaceBaseController {
	public function _initialize() {
		parent::_initialize ();
		if (is_login()) {
			$this->userid = $this->visitor['userid'];
		}else{
			$this->userid = 0;
		}
		//应用所需 mod
		$this->group_mod = D ( 'Group/Group' );
		$this->user_mod = D ( 'Common/User' );
		$this->group_users_mod = M ( 'group_users' );
		$this->group_topics_mod = D ( 'Group/GroupTopics' );
		$this->group_topics_collects = D ( 'Group/GroupTopicsCollects' );
		$this->group_topics_comments = M ( 'group_topics_comments' );
		//相册
		$this->album_mod = D('UserPhotoAlbum');
		
	}
	public function index() {	
		$doname = I('id');
		$user_mod = new UserApi;
		if(empty($doname)) $this->error ( '呃...你想要的东西不在这儿' );
		//根据ID获取uid; 判断是否是uid 还是doname
		if(is_numeric($doname)){ 
			$userid = $doname;
		}else if(is_string($doname)){
			$userInfo = $user_mod->info($doname,true);
			if($userInfo<0){
				$this->error ( '呃...你想要的东西不在这儿' );
			}else{
				$userid = $userInfo[0];
			}
		}
		

		$strUser = $this->user_mod->getOneUser ( $userid );
		$strUser['isfollow'] = $this->user_mod->isFollow($this->userid, $userid);
		//他的角色
		$strUser['rolename'] = $this->user_mod->getRole($strUser['count_score']);
		//他关注的用户
		$strUser['followUser'] = $this->user_mod->getfollow_user($userid, 8);
		// 发布的帖子
		$arrMyTopic = $this->group_topics_mod->getUserTopic ( $userid, 10 );
		// 用户喜欢的帖子
		$MyCollects = $this->group_topics_collects->getUserCollectTopic ( $userid, 10 );
		if (is_array ( $MyCollects )) {
			foreach ( $MyCollects as $key => $item ) {
				$arrMyCollect [$key] = $this->group_topics_mod->getOneTopic ( $item ['topicid'] );
			}
		}

		// 回复的帖子
		$arrComments = $this->group_topics_comments->field ( 'topicid' )->where ( array (
				'userid' => $userid 
		) )->group ( 'topicid' )->order ( 'addtime DESC' )->limit(10)->select ();

		if (is_array ( $arrComments )) {
			foreach ( $arrComments as $item ) {
				$oneTopic = $this->group_topics_mod->getOneTopic($item ['topicid']);
				if ($oneTopic ['userid'] != $userid) {
					$arrMyComment [] = $oneTopic;
				}
			}
		}
		// 我加入的小组
		$myGroup = $this->group_mod->getUserJoinGroup( $userid );
		if(is_array($myGroup)){
			foreach($myGroup as $key=>$item){
				$arrMyGroup[] = $this->group_mod->getOneGroup($item['groupid']);
			}
		}
		//我的相册
		$map['privacy'] = 1; //公开
		$map['userid'] = $userid;
		$arrAlbum = $this->album_mod->getAlbums($map,'uptime desc',4);
		
		
		$this->assign('arrAlbum',$arrAlbum);
		$this->assign ( 'strUser', $strUser );
		$this->assign ( 'arrMyTopic', $arrMyTopic );
		$this->assign ( 'arrMyCollect', $arrMyCollect );
		$this->assign ( 'arrMyComment', $arrMyComment );
		$this->assign ( 'arrMyGroup', $arrMyGroup );		

		
		//配置seo
		$this->_config_seo ( array (
				'title' => $strUser ['username'],
				'subtitle' => '个人主页' 
		) );
		if($strUser['about']){
			$seodesc = $strUser['about'];
		}else{
			$seodesc = '提供免费个人日记，个人空间，个人相册等。您可以玩装扮、上传照片、写广播、写日志、听音乐，和朋友一起享受生活，分享欢笑！';
		}
		$this->_config_seo ( array (
				'title' => $strUser ['username'],
				'subtitle'=> '个人空间_'.C('ik_site_title'),
				'keywords' => '个人空间,个人相册,个人日记,免费空间,分享日志,关注好友,友邻广播',
				'description'=> $seodesc,
		) );
		$this->display ();
	}
}