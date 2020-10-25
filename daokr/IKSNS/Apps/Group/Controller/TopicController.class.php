<?php
/*
 * IKPHP 爱客开源社区 @copyright (c) 2012-3000 IKPHP All Rights Reserved 
 * @author 小麦
 * @Email:810578553@qq.com
 * @小组应用 帖子控制器
 */
namespace Group\Controller;

class TopicController extends GroupBaseController {
	public function _initialize() {
		parent::_initialize ();
		// 访问者控制
		$arr_act = array (
		'add',
		'addcomment',
		'delcomment',
		'delete',
		'edit',
		'isdigest',
		'isshow',
		'istop',
		'like',
		'publish',
		'recommend',
		'recomment',
		'update',
		);
		if (! $this->visitor && in_array ( ACTION_NAME, $arr_act )) 
		{
			$this->redirect ( 'home/user/login' );
		} else {
			$this->userid = $this->visitor ['userid'];
		}
		//应用所需 mod
		$this->_mod = D ( 'Group' );
		$this->user_mod = D ( 'Common/User' );
		$this->message_mod = D ( 'Common/Message' );
		$this->tag_mod = D('Common/Tag');
		$this->video_mod = D ( 'Common/Videos' );
		$this->images_mod = D('Common/Images');
		
		$this->group_topics_mod = D ( 'group_topics' );
		$this->group_topics_collects = D ( 'group_topics_collects' );
		$this->group_topics_comments = M ( 'group_topics_comments' );
	}
	
	// 发布新帖
	public function add() {
		$userid = $this->userid;
		$groupid = $this->_get ( 'id' );
		// 是否加入
		$isGroupUser = $this->_mod->isGroupUser ( $this->userid, $groupid );
		if (! $isGroupUser) {
			$this->error ( '只有小组成员才能发言，请先加入小组!' );
		}
		// 获取小组信息
		$group = $this->_mod->getOneGroup ( $groupid );
		// 审核
		if ($group ['isaudit'] == 1)
			$this->error ( '该小组还在审核中，暂时还不能发帖！' );
		
		// 预先执行添加一条记录
		$strLastTipic = $this->group_topics_mod->where ( array ('userid' => $userid, 'groupid' => 0 ) )->find ();
		if ($strLastTipic ['topicid'] > 0) {
			$topic_id = $strLastTipic ['topicid'];
		
		} else {
			$data = array ('userid' => $userid, 'groupid' => 0, 'title' => '0', 'content' => '0' );
			if (false !== $this->group_topics_mod->create ( $data )) {
				$topic_id = $this->group_topics_mod->add ();
			}
		
		}
		$this->assign ( 'topic_id', $topic_id );
		$this->assign ( 'action', U ( 'group/topic/publish' ) );
		$this->assign ( 'strGroup', $group );
		$this->assign ( 'isGroupUser', $isGroupUser );
		$this->_config_seo ( array ('title' => $group ['groupname'] . '发布帖子', 'subtitle' => '小组' ) );
		
		$this->_config_seo ( array ('title' => $group ['groupname'] . '发布帖子', 'subtitle' => '小组_' . C ( 'ik_site_title' ), 'keywords' => '', 'description' => '' ) );
		$this->display ();
	}
	
	// 执行发布
	public function publish() {
		
		if (IS_POST) {
			$userid = $this->userid;
			$topic_id = $this->_post ( 'topic_id' );
			$groupid = $this->_post ( 'groupid' );
			
			$title = $this->_post ( 'title' );
			$content = $this->_post ( 'content' );
			$iscomment = $this->_post ( 'iscomment' ); // 是否允许评论
			

			// 是否加入 修改bug漏洞表单提交 2013-6-28
			$isGroupUser = $this->_mod->isGroupUser ( $this->userid, $groupid );
			if (! $isGroupUser) {
				$this->error ( '只有小组成员才能发言，请先加入小组!' );
			}
			//帖子ID
			$strTopic = $this->group_topics_mod->field ( 'topicid' )->where ( array ('groupid' => 0, 'userid' => $userid ) )->find ();
			if ($strTopic ['topicid'] != $topic_id) {
				$this->error ( '非法操作；请不要搞怪!' );
			}
			
			if ($title == '') {
				$this->error ( '不要这么偷懒嘛，多少请写一点内容哦^_^' );
			} else if ($content == '') {
				$this->error ( '没有任何内容是不允许你通过滴^_^' );
			} elseif (mb_strlen ( $content, 'utf8' ) > 20000) {
				$this->error ( '发这么多内容干啥^_^' );
			}
			
			$uptime = time ();
			// 查看是否有视频
			$seqnum = D ( 'Common/Videos' )->countViedeos ( 'topic', $topic_id );
			$seqnum > 0 ? $isvideo = 1 : $isvideo = 0;
			$arrData = array ('groupid' => $groupid, 'title' => ikwords ( htmlspecialchars ( $title ) ), 'content' => ikwords ( $content ), 'isaudit' => C ( 'ik_topic_isaudit' ), //审核
'isvideo' => $isvideo, 'iscomment' => $iscomment, 'addtime' => time (), 'uptime' => $uptime );
			// 执行更新帖子
			$this->group_topics_mod->where ( array ('topicid' => $topic_id ) )->save ( $arrData );
			// 执行更新图片信息
			$arrSeqid = $this->_post ( 'seqid' );
			$arrTitle = $this->_post ( 'photodesc' );
			if (is_array ( $arrSeqid )) {
				foreach ( $arrSeqid as $key => $item ) {
					$seqid = $arrSeqid [$key];
					$imgtitle = empty ( $arrTitle [$key] ) ? '' : $arrTitle [$key];
					$layout = $this->_post ( 'layout_' . $seqid );
					$dataimg = array ('title' => $imgtitle, 'align' => $layout );
					$where = array ('type' => 'topic', 'typeid' => $topic_id, 'seqid' => $seqid );
					$this->images_mod->updateImage ( $dataimg, $where );
				}
			}
			// 统计小组下帖子数并更新
			$count_topic = $this->group_topics_mod->countTopic ( $groupid );
			// 统计今天发布帖子数
			$count_topic_today = $this->group_topics_mod->countTodayTipic ( $groupid );
			// 更新帖子数
			$this->_mod->updateTodayTopic ( $groupid, $count_topic, $count_topic_today );
			// 积分记录
			/*			$tag_arg = array (
					'uid' => $this->userid,
					'uname' => $this->visitor['username'],
					'action' => 'pubtopic',
					'actionname' => '发布帖子'
			);
			tag ( 'pubtopic_end', $tag_arg );*/
			
			$this->redirect ( 'group/topic/show', array ('id' => $topic_id ) );
		
		} else {
			$this->redirect ( 'group/index/index' );
		}
	
	}
	
	// 编辑帖子
	public function edit() {
		
		$topicid = $this->_get ( 'id', 'intval' );
		$userid = $this->userid;
		
		$strTopic = $this->group_topics_mod->where ( array ('topicid' => $topicid ) )->find ();
		//$strTopic['content'] = 
		$strGroup = $this->_mod->getOneGroup ( $strTopic ['groupid'] );
		
		$groupid = $strGroup ['groupid'];
		if ($strTopic ['userid'] == $userid || $strGroup ['userid'] == $userid) {
			// 是否加入
			$isGroupUser = $this->_mod->isGroupUser ( $userid, $groupid );
			//浏览该topic_id下的照片
			$type = 'topic';
			$arrPhotos = D ( 'Common/Images' )->getImagesByTypeid ( $type, $topicid );
			//浏览改topic_id下的视频
			$arrVideos = D ( 'Common/Videos' )->getVideosByTypeid ( $type, $topicid );
			
			$this->assign ( 'action', U ( 'group/topic/update' ) );
			$this->assign ( 'arrPhotos', $arrPhotos );
			$this->assign ( 'arrVideos', $arrVideos );
			$this->assign ( 'isGroupUser', $isGroupUser );
			$this->assign ( 'strTopic', $strTopic );
			$this->assign ( 'strGroup', $strGroup );
			$this->assign ( 'topic_id', $topicid );
			
			$this->_config_seo ( array ('title' => '编辑“' . $strTopic ['title'] . '”', 'subtitle' => '小组_' . C ( 'ik_site_title' ), 'keywords' => '', 'description' => '' ) );
			$this->display ( 'add' );
		} else {
			$this->error ( "您没有权限编辑帖子！" );
		}
	
	}
	
	// 执行更新帖子
	public function update() {
		if (IS_POST) {
			$userid = $this->userid;
			$topic_id = $this->_post ( 'topic_id' );
			$groupid = $this->_post ( 'groupid' );
			
			$title = $this->_post ( 'title' );
			$content = $this->_post ( 'content' );
			$iscomment = $this->_post ( 'iscomment' ); // 是否允许评论
			

			$strTopic = $this->group_topics_mod->getOneTopic ( $topic_id );
			$strGroup = $this->_mod->getOneGroup ( $groupid );
			// 只有小组管理员 或 帖子所有者 可以编辑
			if ($strTopic ['userid'] == $userid || $strGroup ['userid'] == $userid) {
				
				$uptime = time ();
				$arrData = array ('groupid' => $groupid, 'title' => ikwords ( htmlspecialchars ( $title ) ), 'content' => ikwords ( $content ), 'iscomment' => $iscomment, 'uptime' => $uptime );
				// 执行更新帖子
				$this->group_topics_mod->where ( array ('topicid' => $topic_id ) )->save ( $arrData );
				// 执行更新图片信息
				$arrSeqid = $this->_post ( 'seqid' );
				$arrTitle = $this->_post ( 'photodesc' );
				if (is_array ( $arrSeqid )) {
					foreach ( $arrSeqid as $key => $item ) {
						$seqid = $arrSeqid [$key];
						$imgtitle = empty ( $arrTitle [$key] ) ? '' : $arrTitle [$key];
						$layout = $this->_post ( 'layout_' . $seqid );
						$dataimg = array ('title' => $imgtitle, 'align' => $layout );
						$where = array ('type' => 'topic', 'typeid' => $topic_id, 'seqid' => $seqid );
						D ( 'Common/Images' )->updateImage ( $dataimg, $where );
					}
				}
				$this->redirect ( 'group/topic/show', array ('id' => $topic_id ) );
			
			} else {
				$this->redirect ( 'group/index/index' );
			}
		} else {
			$this->redirect ( 'group/index/index' );
		}
	}
	
	//喜欢帖子
	public function like() {
		$topicid = $this->_post ( 'tid' );
		$groupid = $this->_post ( 'tkind' );
		if (empty ( $topicid )) {
			$this->error ( "非法操作！" );
		}
		$arrJson = $this->group_topics_collects->collectTopic ( $this->userid, $topicid );
		header ( "Content-Type: application/json", true );
		echo json_encode ( $arrJson );
	}
	//显示
	public function show() {
		$user = $this->user_mod->get ();
		$topic_id = $this->_get ( 'id' );
		$strTopic = $this->group_topics_mod->getOneTopic ( $topic_id );
		! $strTopic && $this->error ( '呃...你想要的东西不在这儿' );
		//审核
		if ($strTopic ['isaudit'] == 1 && $strTopic ['userid'] != $user ['userid'] && $_SESSION ['admin'] ['userid'] != 1)
			$this->error ( '该帖子正在审核中，请稍后访问！' );
		$strTopic ['user'] = $this->user_mod->getOneUser ( $strTopic ['userid'] );
		$strTopic ['user'] ['signed'] = hview ( $strTopic ['user'] ['signed'] );
		
		//1.5.4 版本去掉小组帖子 tag功能
		//$strTopic ['tags'] = $this->tag_mod->getObjTagByObjid ( 'topic', 'topicid', $topic_id );
		
		// 小组信息
		$strGroup = $this->_mod->getOneGroup ( $strTopic ['groupid'] );
		
		// 是否已经加入
		$isGroupUser = $this->_mod->isGroupUser ( $this->userid, $strTopic ['groupid'] );
		
		// 最新话题
		$newTopic = $this->group_topics_mod->newTopic ( $strTopic ['groupid'], 6 );
		//帖子浏览量加 +1
		if ($strTopic ['userid'] != $user ['userid']) {
			$this->group_topics_mod->where ( array ('topicid' => $topic_id ) )->setInc ( 'count_view' );
		}
		// 喜欢收藏的人数
		$likenum = $this->group_topics_collects->countLike ( $topic_id );
		$is_Like = $this->group_topics_collects->isLike ( $user ['userid'], $topic_id );
		$strTopic ['islike'] = $is_Like;
		$strTopic ['likenum'] = $likenum;
		
		// 操作
		$action ['istop'] = $strTopic ['istop'] == 0 ? '置顶' : '取消置顶';
		$action ['isdigest'] = $strTopic ['isdigest'] == 0 ? '精华' : '取消精华';
		$action ['isshow'] = $strTopic ['isshow'] == 0 ? '隐藏' : '显示';
	
		
		// 喜欢该帖子的用户
		$arrCollectUser = $this->group_topics_collects->likeTopicUser ( $topic_id );
		
		//上一篇帖子
		$upTopic = $this->group_topics_mod->where ( array ('topicid' => array ('lt', $topic_id ), 'groupid' => $strTopic ['groupid'] ) )->find ();
		
		//下一篇帖子
		$downTopic = $this->group_topics_mod->where ( array ('topicid' => array ('gt', $topic_id ), 'groupid' => $strTopic ['groupid'] ) )->find ();
		
		
		//获取评论
		$page = $this->_get ( 'p', 'intval', 1 );
		$sc = $this->_get ( 'sc', 'trim', 'asc' );
		$isauthor = $this->_get ( 'isauthor', 'trim', '0' );
		
		//查询条件 是否显示
		$map ['topicid'] = $strTopic ['topicid'];
		if ($isauthor) {
			$map ['userid'] = $strTopic ['userid'];
			$author = array ('isauthor' => 0, 'text' => '查看所有回应' );
		} else {
			$author = array ('isauthor' => 1, 'text' => '只看楼主' );
		}
		//显示列表
		$pagesize = 30;
		$count = $this->group_topics_comments->where($map)->order('addtime '.$sc)->count('topicid');
		$pager = $this->_pager($count, $pagesize);
		$arrComment =  $this->group_topics_comments->where($map)->order('addtime '.$sc)->limit($pager->firstRow.','.$pager->listRows)->select();
		foreach($arrComment as $key=>$item){
			$arrTopicComment[] = $item;
			$arrTopicComment[$key]['user'] = $this->user_mod->getOneUser($item['userid']); 
			$arrTopicComment[$key]['content'] = h($item['content']);
			$recomment = $this->group_topics_mod->recomment($item['referid']);
			$arrTopicComment[$key]['recomment'] = $recomment;
		}

		$this->assign('pageUrl', $pager->show());
		$this->assign('arrTopicComment', $arrTopicComment);
		
		$this->assign ( 'user', $user );
		$this->assign ( 'page', $page );
		$this->assign ( 'sc', $sc );
		$this->assign ( 'author', $author );
		$this->assign ( 'isauthor', $isauthor );
		$this->assign ( 'upTopic', $upTopic );
		$this->assign ( 'downTopic', $downTopic );
		$this->assign ( 'strTopic', $strTopic );
		$this->assign ( 'newTopic', $newTopic );
		$this->assign ( 'strGroup', $strGroup );
		$this->assign ( 'action', $action );
		$this->assign ( 'isGroupUser', $isGroupUser );
		$this->assign ( 'arrCollectUser', $arrCollectUser );
		
		$this->_config_seo ( array ('title' => $strTopic ['title'], 'subtitle' => $strGroup ['groupname'] . '_' . C ( 'ik_site_title' ), 'keywords' => ikscws ( $strTopic ['title'] ), 'description' => getsubstrutf8 ( clearText ( $strTopic ['content'] ), 0, 200 ) ) );
		$this->display ();
	}
	
	//删除帖子
	public function delete() {
		$topicid = $this->_get ( 'id', 'intval' );
		$user = $this->user_mod->get ();
		
		$strTopic = $this->group_topics_mod->getOneTopic ( $topicid );
		
		$strGroup = $this->_mod->getOneGroup ( $strTopic ['groupid'] );
		
		// 发帖人 小组组长 管理员 可以删除 其他权限不允许删除
		if ($strTopic ['userid'] == $user ['userid'] || $strGroup ['userid'] == $user ['userid'] || $user ['isadmin'] == 1) {
			$this->group_topics_mod->delTopic ( $topicid );
			// 积分记录
			/*	$tag_arg = array (
					'uid' => $this->userid,
					'uname' => $this->visitor['username'],
					'action' => 'deltopic',
					'actionname' => '删除帖子'
			);
			tag ( 'deltopic_end', $tag_arg );*/
			
			$this->redirect ( 'group/index/show', array ('id' => $strGroup ['groupid'] ) );
		} else {
			$this->error ( '没有帖子可以删除，别瞎搞！' );
		}
	}
	
	//推荐帖子
	public function recommend() {
		$topicid = $this->_post ( 'tid' );
		$groupid = $this->_post ( 'tkind' );
		$content = $this->_post ( 'content' ); //推荐的话
		

		if (empty ( $topicid )) {
			$this->error ( "非法操作！" );
		}
		
		$recommendNum = M ( 'group_topics_recommend' )->where ( array ('topicid' => $topicid ) )->count ();
		
		$is_rec = M ( 'group_topics_recommend' )->where ( array ('userid' => $this->userid, 'topicid' => $topicid ) )->count ();
		
		if ($is_rec > 0) {
			//已经推荐过了
			$arrJson = array ('r' => 1, 'html' => '你已经推荐过该帖子了！' );
		} else {
			//执行
			$arrData = array ('userid' => $this->userid, 'topicid' => $topicid, 'content' => $content, 'addtime' => time () );
			if (false !== M ( 'group_topics_recommend' )->create ( $arrData )) {
				M ( 'group_topics_recommend' )->add ();
				//帖子推荐数加1
				$this->group_topics_mod->where ( array ('topicid' => $topicid ) )->setInc ( 'count_recommend' );
				$arrJson = array ('r' => 0, 'num' => $recommendNum + 1 );
			}
		}
		
		header ( "Content-Type: application/json", true );
		echo json_encode ( $arrJson );
	}
	
	// 置顶帖子
	public function istop() {
		$topicid = $this->_get ( 'id', 'intval' );
		$userid = $this->userid;
		
		$strTopic = $this->group_topics_mod->getOneTopic ( $topicid );
		
		$istop = $strTopic ['istop'];
		
		$istop == 0 ? $istop = 1 : $istop = 0;
		
		$strGroup = $this->_mod->getOneGroup ( $strTopic ['groupid'] );
		//只有组长可以置顶帖子
		if ($userid == $strGroup ['userid']) {
			$this->group_topics_mod->where ( array ('topicid' => $topicid ) )->setField ( 'istop', $istop );
			$this->redirect ( 'group/topic/show', array ('id' => $topicid ) );
		} else {
			$this->error ( "只有小组组长才能置顶帖子！" );
		}
	}
	// 精华帖子
	public function isdigest() {
		
		$topicid = $this->_get ( 'id', 'intval' );
		$userid = $this->userid;
		
		$strTopic = $this->group_topics_mod->getOneTopic ( $topicid );
		
		$isdigest = $strTopic ['isdigest'];
		
		$isdigest == 0 ? $isdigest = 1 : $isdigest = 0;
		
		$strGroup = $this->_mod->getOneGroup ( $strTopic ['groupid'] );
		//只有组长可以精华帖子
		if ($userid == $strGroup ['userid']) {
			$this->group_topics_mod->where ( array ('topicid' => $topicid ) )->setField ( 'isdigest', $isdigest );
			$this->redirect ( 'group/topic/show', array ('id' => $topicid ) );
		} else {
			$this->error ( "只有小组组长才可以设置为精华帖！" );
		}
	}
	// 隐藏帖子
	public function isshow() {
		
		$topicid = $this->_get ( 'id', 'intval' );
		$userid = $this->userid;
		
		$strTopic = $this->group_topics_mod->getOneTopic ( $topicid );
		
		$isshow = $strTopic ['isshow'];
		
		$isshow == 0 ? $isshow = 1 : $isshow = 0;
		
		$strGroup = $this->_mod->getOneGroup ( $strTopic ['groupid'] );
		//只有组长可以精华帖子
		if ($userid == $strGroup ['userid']) {
			$this->group_topics_mod->where ( array ('topicid' => $topicid ) )->setField ( 'isshow', $isshow );
			$this->redirect ( 'group/topic/show', array ('id' => $topicid ) );
		} else {
			$this->error ( "只有小组组长才可以设置为隐藏帖！" );
		}
	}
	
	// 添加评论
	public function addcomment() {
		$topicid = $this->_post ( 'topicid', 'intval' );
		$content = $this->_post ( 'content' );
		$page = $this->_post ( 'p', 'intval' );
		
		if (empty ( $content )) {
			
			$this->error ( '没有任何内容是不允许你通过滴^_^' );
		
		}elseif (mb_strlen ( $content, 'utf8' ) < 5){
			$this->error ( '添加评论的内容太少了^_^' );
		} elseif (mb_strlen ( $content, 'utf8' ) > 10000) {
			
			$this->error ( '发这么多内容干啥,最多只能写1000千个字^_^,回去重写哇！' );
		
		} else {
			//执行添加
			$data = array ('topicid' => $topicid, 'userid' => $this->userid, 'content' => ikwords ( $content ), 'addtime' => time () );
			if (false !== $this->group_topics_comments->create ( $data )) {
				$commentid = $this->group_topics_comments->add ();
			}
			if ($commentid) {
				//统计评论数
				$count_comment = $this->group_topics_comments->where ( array ('topicid' => $topicid ) )->count ( '*' );
				//更新帖子最后回应时间和评论数
				$uptime = time ();
				$data = array ('uptime' => $uptime, //暂时这样
'count_comment' => $count_comment );
				$this->group_topics_mod->where ( array ('topicid' => $topicid ) )->save ( $data );
				// 积分记录
				$tag_arg = array ('uid' => $this->userid, 'uname' => $this->visitor ['username'], 'action' => 'pubcmt', 'actionname' => '发布评论' );
				tag ( 'pubcmt_end', $tag_arg );
				//发送系统消息(通知楼主有人回复他的帖子啦) 钩子
				$strTopic = $this->group_topics_mod->getOneTopic ( $topicid );
				if ($strTopic ['userid'] != $this->userid) {
					$topicurl = C ( 'ik_site_url' ) . U ( 'group/topic/show', array ('id' => $topicid ) );
					
					$msg_userid = '0';
					$msg_touserid = $strTopic ['userid'];
					$msg_title = '你的帖子：《' . $strTopic ['title'] . '》新增一条评论，快去看看给个回复吧';
					$msg_content = '你的帖子：《' . $strTopic ['title'] . '》新增一条评论，快去看看给个回复吧^_^ <br /><a href="' . $topicurl . '">' . $topicurl . '</a>';
					$this->message_mod->sendMessage ( $msg_userid, $msg_touserid, $msg_title, $msg_content );
				
				}
				//feed开始
				$this->redirect ( 'group/topic/show', array ('id' => $topicid, 'p' => $page ) );
			}
		
		}
	
	}
	
	// 回复评论
	public function recomment(){
		$topicid = $this->_post('topicid');
		$referid = $this->_post('referid');
		$content = $this->_post('content');		
		//安全性检查
		if( mb_strlen($content, 'utf8') > 10000)
		{
			
			return $this->ajaxReturn(array('status'=>1, 'msg'=>'回复的内容太多了'));
			
		}elseif (mb_strlen ( $content, 'utf8' ) < 5){

			return $this->ajaxReturn(array('status'=>1, 'msg'=>'回复的内容太少了'));
		}
		//执行添加
		$data = array(
				'topicid'	=> $topicid,
				'userid'	=> $this->userid,
				'referid'	=> $referid,
				'content'	=> ikwords(htmlspecialchars_decode($content)), // ajax 提交过来数据的转一下
				'addtime'	=> time(),
		);
		if (false !== $this->group_topics_comments->create ( $data )) {
			$commentid = $this->group_topics_comments->add ();
		}
		if($commentid){
			//统计评论数
			$count_comment = $this->group_topics_comments->where(array('topicid'=>$topicid))->count('*');
			//更新帖子最后回应时间和评论数
			$uptime = time();
			$data = array(
					'uptime'	=> $uptime, //暂时这样
					'count_comment'	=> $count_comment,
			);
			$this->group_topics_mod->where(array('topicid'=>$topicid))->save($data);

			//发送系统消息(通知楼主有人回复他的帖子啦) 钩子
			$strTopic = $this->group_topics_mod->getOneTopic($topicid);
			$strComment = $this->group_topics_comments->where(array('commentid'=>$referid))->find();
			$topicurl = C('ik_site_url').U('group/topic/show',array('id'=>$topicid));
			if($topicid && $strTopic['userid'] != $this->userid){
				$msg_userid = '0';
				$msg_touserid = $strTopic['userid'];
				$msg_title = '你的帖子：《'.$strTopic['title'].'》新增一条评论，快去看看给个回复吧';
				$msg_content = '你的帖子：《'.$strTopic['title'].'》新增一条评论，快去看看给个回复吧^_^ <br /><a href="'.$topicurl.'">'.$topicurl.'</a>';
				$this->message_mod->sendMessage($msg_userid,$msg_touserid,$msg_title,$msg_content);
			}
			if($referid && $strComment['userid'] != $this->userid){
				$msg_userid = '0';
				$msg_touserid = $strComment['userid'];
				$msg_title = '有人评论了你在帖子：《'.$strTopic['title'].'》中的回复，快去看看给个回复吧';
				$msg_content = '有人评论了你在帖子：《'.$strTopic['title'].'》中的回复，快去看看给个回复吧^_^ <br /><a href="'.$topicurl.'">'.$topicurl.'</a>';
				$this->message_mod->sendMessage($msg_userid,$msg_touserid,$msg_title,$msg_content);
			}

			return $this->ajaxReturn(array('status'=>0, 'msg'=>'成功回复'));
		}
		
	}	
	
	// 删除帖子的某条评论
	public function delcomment(){
		$commentid = $this->_get('cid','intval');
		$userid = $this->userid;
		$strComment = M('group_topics_comments')->where(array('commentid'=>$commentid))->find();
		$strTopic = $this->group_topics_mod->getOneTopic($strComment['topicid']);
		$strGroup = $this->_mod->getOneGroup($strTopic['groupid']);
		
		// 发帖人 小组组长 管理员 可以删除 其他权限不允许删除
		if($strTopic['userid']==$userid || $strGroup['userid']==$userid ){
			$this->group_topics_mod->delComment($commentid);
			// 积分记录
			$tag_arg = array (
					'uid' => $this->userid,
					'uname' => $this->visitor['username'],
					'action' => 'delcmt',
					'actionname' => '删除评论'
			);
			tag ( 'delcmt_end', $tag_arg );
			
			$this->redirect ( 'group/topic/show#comment', array (
					'id' => $strComment['topicid'],
			) );			
		}

	}	

}