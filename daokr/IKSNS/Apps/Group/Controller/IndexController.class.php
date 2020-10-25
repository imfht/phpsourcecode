<?php
/*
 * IKPHP 爱客开源社区 @copyright (c) 2012-3000 IKPHP All Rights Reserved 
 * @author 小麦
 * @Email:810578553@qq.com
 * @小组应用
 */
namespace Group\Controller;
use Common\Controller\
FrontendController;
use Org\Util\
Input;

class IndexController extends GroupBaseController {
	public function _initialize() {
		parent::_initialize ();
		// 访问者控制
		if (! $this->visitor && in_array ( ACTION_NAME, array (
			'create',
			'edit',
			'group_user_set',
			'join',
			'mine',
			'my_collect_topics',
			'my_group_topics',
			'my_replied_topics',
			'my_topics',
			'quit',
			'update',
		
		 ))) {
			$this->redirect ( 'home/user/login' );
		} else {
			$this->userid = $this->visitor ['userid'];
		}
		//应用所需 mod
		$this->_mod = D ( 'Group' );
		$this->user_mod = D ( 'Common/User' );
		$this->message_mod = D ( 'Common/Message' );
		$this->tag_mod = D ( 'Common/Tag' );
		$this->video_mod = D ( 'Common/Videos' );
		$this->images_mod = D ( 'Common/Images' );
		
		$this->group_users_mod = M ( 'group_users' );
		$this->group_topics_mod = D ( 'GroupTopics' );
		$this->group_topics_collects = D ( 'GroupTopicsCollects' );
		$this->group_topics_comments = M ( 'group_topics_comments' );
		$this->cate_mod = D ( 'GroupCate' );
	
	}
	
	//小组首页
	public function index() {
		if (is_login ()) {
			//我的小组话题
			$this->redirect ( 'group/index/my_group_topics' );
		} else {
			//我发现小组
			$this->redirect ( 'group/explore/groups' );
		}
	}	
	// 创建小组
	public function create() {
		if (IS_POST) {
			foreach ( $_POST as $key => $val ) {
				$_POST [$key] = Input::deleteHtmlTags ( $val );
			}
			
			if ($_POST ['grp_agreement'] != 1)
				$this->error ( '不同意社区指导原则是不允许创建小组的！' );
			
		//新增小组分类	
			$oneid = $this->_post ( 'oneid', 'trim,intval', 0 );
			$twoid = $this->_post ( 'twoid', 'trim,intval', 0 );
			if ($oneid != 0 && $twoid == 0) {
				$data ['cateid'] = $oneid;
			} elseif ($oneid != 0 && $twoid != 0) {
				$data ['cateid'] = $twoid;
			} else {
				$this->error ( '请选择一个小组分类吧！' );
			}
			$data ['userid'] = $this->userid;
			$data ['groupname'] = ikwords ( $this->_post ( 'groupname' ) );
			$data ['groupdesc'] = ikwords ( $this->_post ( 'groupdesc' ) );
			$data ['tag'] = $this->_post ( 'tag', 'strip_tags,trim' );
			$tags = str_replace ( ' ', ' ', $data ['tag'] );
			$arrtag = explode ( ' ', $tags );
			$data ['isaudit'] = C ( 'ik_group_isaudit' ); //是否要审核 0 不审核 1 审核
			$data ['addtime'] = time ();
			// 小组名唯一性判断
			if ($this->iscreate ( $data ['groupname'] ))
				$this->error ( '小组名称已经存在，请更换其他小组名称！' );
			if (mb_strlen ( $data ['groupname'], 'utf8' ) > 20) {
				$this->error ( '小组名称太长啦，最多20个字...^_^！' );
			
			} elseif (mb_strlen ( $data ['groupname'], 'utf8' ) < 2) {
				
				$this->error ( '小组名称太短啦，必须大于2汉字...^_^！' );
			
			} elseif (mb_strlen ( $data ['groupdesc'], 'utf8' ) > 10000) {
				$this->error ( '写这么多内容干啥，超出1万个字了都^_^' );
			
			} elseif (mb_strlen ( $data ['groupdesc'], 'utf8' ) < 10) {
				$this->error ( '小组描述太少了必须大于10个字，多多益善^_^' );
			
			} elseif (count ( $arrtag ) > 5) {
				$this->error ( '最多 5 个标签，写的太多了...^_^！' );
			}
			for($i = 0; $i < count ( $arrtag ); $i ++) {
				if (mb_strlen ( $arrtag [$i], 'utf8' ) > 8) {
					$this->error ( '小组标签太长啦，最多8个字...^_^！' );
				}
			}
			if (false !== $this->_mod->create ( $data )) {
				$groupid = $this->_mod->add ();
				if ($groupid) {
					// 小组图标
					$groupicon = $_FILES ['picfile'];
					// 上传
					if (! empty ( $groupicon )) {
						//上传头像
						/*$result = savelocalfile($groupicon,'group/icon',
								array('width'=>'48','height'=>'48'),
								array('jpg','jpeg','png'),
								md5($groupid)); */
								
						$result = \Common\Util\Upload::saveLocalFile ( 'group/icon/', array ('width' => '48,60', 'height' => '48,60' ), array ('md5', $groupid ), 1 );
						
						if (! $result ['error']) {
							$data ['groupicon'] = $result ['img_48_48'];
							//更新小组头像
							$this->_mod->where ( 'groupid=' . $groupid )->setField ( 'groupicon', $data ['groupicon'] );
						}
					}
					//添加tag
					$this->tag_mod->addTag ( 'group', 'groupid', $groupid, $tags );
					// 绑定成员
					$group_user_data ['userid'] = $this->userid;
					$group_user_data ['groupid'] = $groupid;
					$group_user_data ['addtime'] = time ();
					$this->group_users_mod->add ( $group_user_data );
					// 更新小组人数
					$this->_mod->where ( 'groupid=' . $groupid )->setField ( 'count_user', 1 );
					$this->redirect ( 'group/index/show', array ('id' => $groupid ) );
				}
			}
		} else {
			//判断权限
			if (C ( 'ik_iscreate' ) == 1)
				$this->error ( '您好，网站暂时关闭创建小组；如有疑问联系站长！' );
			
		//获取该用户已经创建了多少个小组
			$maxgroup = $this->_mod->where ( array ('userid' => $this->userid ) )->count ();
			
			if ($maxgroup >= C ( 'ik_maxgroup' ))
				$this->error ( '您好，您的积分不够，最多只能创建' . $maxgroup . '个小组！' );
			
		//新加分类
			$arrOne = $this->cate_mod->getParentCate ();
			
			$this->assign ( 'arrOne', $arrOne );
			
			$this->_config_seo ( array ('title' => '申请创建小组', 'subtitle' => '小组_' . C ( 'ik_site_title' ), 'keywords' => '', 'description' => '' ) );
			$this->display ();
		}
	}
	
	// 检查小组名称是否存在
	public function iscreate($groupname) {
		if ($groupname) {
			return $this->_mod->email_exists ( $groupname );
		} else {
			$this->error ( '小组名不能为空！' );
		}
	}
	
	//ajax获取子分类
	public function ajax_getcate() {
		$oneid = $this->_post ( 'oneid', 'trim,intval' );
		$arrCate = $this->cate_mod->getReferCate ( $oneid );
		if ($arrCate) {
			echo '<select id="twoid" name="twoid" class="txt">';
			echo '<option value="0">请选择</option>';
			foreach ( $arrCate as $item ) {
				echo '<option value="' . $item ['cateid'] . '">' . $item ['catename'] . '</option>';
			}
			echo "</select>";
		} else {
			echo '';
		}
	}
	
	//小组显示页面
	public function show() {
		$id = $this->_get ( 'id', 'intval' );
		$group = $this->_mod->getOneGroup ( $id );
		// 存在性检查
		! $group && $this->error ( '呃...你想要的东西不在这儿' );
		// 审核
		$user = $this->user_mod->get ();
		if ($group ['isaudit'] == 1 && $group ['userid'] != $user ['userid'])
			$this->error ( '该小组正在审核中，请稍后访问！' );
		
		$strLeader = $this->user_mod->getOneUser ( $group ['userid'] );
		// 是否加入
		$isGroupUser = $this->_mod->isGroupUser ( $this->userid, $id );
		// 获取最新加入会员
		$arrGroupUser = $this->_mod->getNewGroupUser ( $id, 12 );
		// 获取最近小组话题 40 条
		$arrTopics = $this->group_topics_mod->newTopic ( $id, 40 );
		if (is_array ( $arrTopics )) {
			foreach ( $arrTopics as $key => $item ) {
				$arrTopic [] = $item;
				$arrTopic [$key] ['user'] = $this->user_mod->getOneUser ( $item ['userid'] );
			}
		}
		
		$this->assign ( 'arrTopic', $arrTopic );
		$this->assign ( 'isGroupUser', $isGroupUser );
		$this->assign ( 'strGroup', $group );
		$this->assign ( 'strLeader', $strLeader );
		$this->assign ( 'arrGroupUser', $arrGroupUser );
		
		$this->_config_seo ( array ('title' => $group ['groupname'], 'subtitle' => '小组_' . C ( 'ik_site_title' ), 'keywords' => ikscws ( $group ['groupname'] ), 'description' => sub_str ( $group ['groupdesc'], 200 ) ) );
		$this->display ();
	}
	
	// 加入小组
	public function join() {
		$groupid = $this->_get ( 'id' );
		// 先统计用户有多少个小组了，20个封顶
		$userGroupNum = $this->group_users_mod->where ( array ('userid' => $this->userid ) )->count ( '*' );
		
		if ($userGroupNum >= C ( 'ik_jionmax' ))
			$this->error ( '你加入的小组总数已经到达' . $userGroupNum . '个，不能再加入小组！' );
		
		$groupUserNum = $this->group_users_mod->where ( array ('userid' => $this->userid, 'groupid' => $groupid ) )->count ( '*' );
		
		if ($groupUserNum > 0)
			$this->error ( '你已经加入小组！' );
		
		// 执行加入
		$data = array ('userid' => $this->userid, 'groupid' => $groupid, 'addtime' => time () );
		if (false !== $this->group_users_mod->create ( $data )) {
			$group_users_id = $this->group_users_mod->add ();
			if ($group_users_id) {
				// 更新会员数
				$this->_mod->where ( array ('groupid' => $groupid ) )->setInc ( 'count_user', 1 );
				$this->redirect ( 'group/index/show', array ('id' => $groupid ) );
			}
		}
	
	}
	// 退出小组
	public function quit() {
		$groupid = $this->_get ( 'id' );
		$userid = $this->userid;
		//判断是否是组长，是组长不能退出小组
		$strGroup = $this->_mod->getOneGroup ( $groupid );
		if ($strGroup ['userid'] == $userid) {
			$this->error ( '组长任务艰巨，请坚持到底！' );
		}
		// 删除小组会员
		$this->group_users_mod->where ( array ('userid' => $userid, 'groupid' => $groupid ) )->delete ();
		//计算小组会员数
		$count_user = $this->group_users_mod->where ( array ('groupid' => $groupid ) )->count ( '*' );
		//更新小组成员统计
		$this->_mod->where ( array ('groupid' => $groupid ) )->setField ( array ('count_user' => $count_user ) );
		
		$this->redirect ( 'group/index/show', array ('id' => $groupid ) );
	
	}
	// 编辑小组信息
	public function edit(){
		$userid = $this->userid;
		$type = $this->_get ( 'd', 'trim' );
		$groupid = $this->_get( 'groupid', 'intval');
		//生成菜单
		$menu = array(
				'base' => array('text'=>'基本信息', 'url'=>U('group/index/edit',array('d'=>'base','groupid'=>$groupid))),
				'icon' => array('text'=>'小组图标', 'url'=>U('group/index/edit',array('d'=>'icon','groupid'=>$groupid))),
		);
		if (! empty ( $type ) && $groupid > 0) {
			//小组信息
			$strGroup = $this->_mod->getOneGroup($groupid);
			if($userid != $strGroup['userid']){
				$this->error('您没有权限编辑小组信息！');
			}
			switch ($type) {
				case "base" :
					$arrtags = $this->tag_mod->getObjTagByObjid('group','groupid',$groupid);
					foreach($arrtags as $key=>$item)
					{
						$tags .= $item['tagname'].' '; 
					}
					$strGroup['tags'] = trim($tags);
					
								//新加分类
					$arrOne = $this->cate_mod->getParentCate();
			
					$this->assign('arrOne',$arrOne);
					

					$this->_config_seo ( array (
							'title' => '编辑小组基本信息',
							'subtitle'=> '小组_'.C('ik_site_title'),
							'keywords' => '',
							'description'=> '',
					) );					
					break;
					
				case "icon" :
					
					$this->_config_seo ( array (
							'title' => '修改小组头像',
							'subtitle'=> '小组_'.C('ik_site_title'),
							'keywords' => '',
							'description'=> '',
					) );
					break;					
			}
			$this->assign ( 'menu', $menu );
			$this->assign ( 'type', $type );
			$this->assign ( 'strGroup', $strGroup );
			$this->display('edit_'.$type);
		}else{
			$this->redirect ( 'group/index/index' );
		}
	}
	// 执行更新操作
	public function update(){
		$userid = $this->userid;
		$type = $this->_get ( 'd', 'trim' );
		$groupid = $this->_post( 'groupid', 'intval');
		
		if(IS_POST){
			$strGroup = $this->_mod->getOneGroup($groupid);
			if($userid != $strGroup['userid']){
				$this->error('您没有权限编辑小组信息！');
			}
			switch ($type) {
				case "base" :
					
					//新增小组分类	
					$oneid = $this->_post ( 'oneid', 'trim,intval',0);
					$twoid = $this->_post ( 'twoid', 'trim,intval',0);
					if ($oneid != 0 && $twoid == 0) {
						$data ['cateid'] = $oneid;
					} elseif ($oneid != 0 && $twoid != 0) {
						$data ['cateid'] = $twoid;
					}else{
						$this->error ( '请选择一个小组分类吧！' );
					}
			
					$data ['groupname'] = $this->_post ( 'groupname');
					$data ['groupdesc'] = $this->_post ( 'groupdesc');
					if($data ['groupname']=='' || $data ['groupdesc']=='') $this->error('小组名称和介绍不能为空！');
					$tags = $this->_post( 'tag', 'trim');
					$tags = str_replace(' ',' ',$tags);
					$arrtag = explode(' ',$tags);
					if( mb_strlen($data ['groupname'],'utf8')>20)
					{
						$this->error('小组名称太长啦，最多20个字...^_^！');
							
					}elseif (mb_strlen($data ['groupname'],'utf8')<2){
				
						$this->error ('小组名称太短啦，必须大于2汉字...^_^！');
				
					}else if( mb_strlen($data ['groupdesc'], 'utf8') > 10000)
					{
						$this->error('写这么多内容干啥，超出1万个字了都^_^');
						
					}else if( mb_strlen($data ['groupdesc'], 'utf8') <10){
						
						$this->error('描述写的太少了；必须大于10个字哦^_^');
						
					}else if(count($arrtag)>5)
					{
						$this->error('最多 5 个标签，写的太多了...^_^！');
					}
					for($i=0; $i<count($arrtag); $i++)
					{
					if(mb_strlen($arrtag[$i], 'utf8') > 8)
					{
					$this->error('小组标签太长啦，最多8个字...^_^！');
					}
					}
					//更新tag
					$this->tag_mod->addTag('group','groupid',$groupid,$tags);
					$this->_mod->where(array('groupid'=>$groupid))->save($data);
							$this->success('基本信息修改成功！');
			
					break;
						
					case "icon" :
					// 小组图标
					$groupicon = $_FILES ['picfile'];
					// 上传
					if (! empty ( $groupicon )) {
						//上传头像
					    /* $result = savelocalfile($groupicon,'group/icon',
						array('width'=>'48,60','height'=>'48,60'),
						array('jpg','jpeg','png'),
						md5($groupid));*/
						
						$result = \Common\Util\Upload::saveLocalFile(
				            'group/icon/', 
				            array('width'=>'48,60','height'=>'48,60'),
				            array('md5', $groupid), 1);
						
						if (!$result ['error']) {
							$data ['groupicon'] = $result['img_48_48'];
							//更新小组头像
							$this->_mod->where ( 'groupid=' . $groupid )->setField ( 'groupicon', $data ['groupicon'] );
							$this->success('小组图标修改成功！');
						}
					}
			
					break;
					}			
		}
	}		
	//我管理的小组
	public function mine(){
		$userid = $this->userid;
		// 用户信息
		$strUser = $this->user_mod->getOneUser ( $userid );
		$myGroup = $this->_mod->getUserJoinGroup( $userid );
		//我加入的小组
		if(is_array($myGroup)){
			$count_mygroup = 0;
			foreach($myGroup as $key=>$item){
				$arrMyGroup[] = $this->_mod->getOneGroup($item['groupid']);
				$count_mygroup ++;
			}
		}
		$myCreateGroup = $this->_mod->getUserGroup($userid);
		//我管理的小组
		if(is_array($myCreateGroup)){
			$count_Admingroup = 0;
			foreach($myCreateGroup as $key=>$item){
		
				$arrMyAdminGroup[] = $this->_mod->getOneGroup($item['groupid']);
				$count_Admingroup ++;
		
			}
		}		
		
		$this->assign ( 'strUser', $strUser );
		$this->assign ( 'arrMyGroup', $arrMyGroup );
		$this->assign ( 'count_mygroup', $count_mygroup );
		$this->assign ( 'arrMyAdminGroup', $arrMyAdminGroup );
		$this->assign ( 'count_Admingroup', $count_Admingroup );

		$this->_config_seo ( array (
				'title' => '我管理/加入的小组',
				'subtitle'=>'小组_'.C('ik_site_title'),
				'keywords' =>'',
				'description'=>'',
		) );
		$this->display ();
	}	
	
	
	//我收藏喜欢的帖子
	public function my_collect_topics(){
		$userid = $this->userid;
		// 用户信息
		$strUser = $this->user_mod->getOneUser ( $userid );
		// 我的小组
		$arrCollect = $this->group_topics_collects->getUserCollectTopic($userid,80);
		foreach($arrCollect as $item){
			$strTopic = $this->group_topics_mod->getOneTopic($item['topicid']);
			$arrTopics[] = $strTopic;
		}
		foreach($arrTopics as $key=>$item){
			$arrTopic[] = $item;
			$arrTopic[$key]['user'] = $this->user_mod->getOneUser($item['userid']);
			$arrTopic[$key]['group'] = $this->_mod->getOneGroup($item['groupid']);
		}
		$this->assign ( 'strUser', $strUser );
		$this->assign ( 'arrTopic', $arrTopic );
	
		//我常去的小组 加入的小组
		$myJoinGroup = $this->_mod->getUserJoinGroup( $userid );
		if(is_array($myJoinGroup)){
			foreach($myJoinGroup as $key=>$item){
				$arrMyGroup[] = $this->_mod->getOneGroup($item['groupid']);
			}
			$this->assign('arrMyGroup',$arrMyGroup);//我加入和管理的小组
		}
		
		$this->_config_seo ( array (
				'title' => '我喜欢的话题',
				'subtitle'=>'小组_'.C('ik_site_title'),
				'keywords' =>'',
				'description'=>'',
		) );
		$this->display ();
	}
	// 我发起的话题
	public function my_topics(){
		$userid = $this->userid;
		// 用户信息
		$strUser = $this->user_mod->getOneUser ( $userid );
		//发布的帖子
		$arrMyTopics = $this->group_topics_mod->getUserTopic($userid,80);
		foreach($arrMyTopics as $key=>$item){
			$arrTopic[] = $item;
			$arrTopic[$key]['user'] = $this->user_mod->getOneUser($item['userid']);
			$arrTopic[$key]['group'] = $this->_mod->getOneGroup($item['groupid']);
		}		
		$this->assign ( 'strUser', $strUser );
		$this->assign ( 'arrTopic', $arrTopic );

		//我常去的小组 加入的小组
		$myJoinGroup = $this->_mod->getUserJoinGroup( $userid );
		if(is_array($myJoinGroup)){
			foreach($myJoinGroup as $key=>$item){
				$arrMyGroup[] = $this->_mod->getOneGroup($item['groupid']);
			}
			$this->assign('arrMyGroup',$arrMyGroup);//我加入和管理的小组
		}
		
		$this->_config_seo ( array (
				'title' => '我发起的话题',
				'subtitle'=>'小组_'.C('ik_site_title'),
				'keywords' =>'',
				'description'=>'',
		) );
		$this->display ();
	}
	// 我的小组话题
	public function my_group_topics() {
		$userid = $this->userid;
		// 用户信息
		$strUser = $this->user_mod->getOneUser ( $userid );
		// 我的小组话题
		$myGroup = $this->_mod->getGroupUser ( $userid );

		//我加入的所有小组的话题
		if(is_array($myGroup)){
			foreach($myGroup as $item){
				$arrGroup[] = $item['groupid'];
				$myGroups[] = $this->_mod->getOneGroup($item['groupid']);
			}
		}
		$strGroup = implode(',',$arrGroup);
		
		if($strGroup){
			$arrTopics = $this->group_topics_mod->getTopics($strGroup,80);
			foreach($arrTopics as $key=>$item){
				$arrTopic[] = $item;
				$arrTopic[$key]['user'] = $this->user_mod->getOneUser($item['userid']);
				$arrTopic[$key]['group'] = $this->_mod->getOneGroup($item['groupid']);
			}
		}
		
		//我常去的小组 加入的小组
		$myJoinGroup = $this->_mod->getUserJoinGroup( $userid );
		if(is_array($myJoinGroup)){
			foreach($myJoinGroup as $key=>$item){
				$arrMyGroup[] = $this->_mod->getOneGroup($item['groupid']);
			}
			$this->assign('arrMyGroup',$arrMyGroup);//我加入和管理的小组
		}
		
		
		$this->assign('myGroups',$myGroups);//我加入和管理的小组
		$this->assign ( 'strUser', $strUser );
		$this->assign ( 'arrTopic', $arrTopic );

		$this->_config_seo ( array (
				'title' => '我的小组话题',
				'subtitle'=>'小组_'.C('ik_site_title'),
				'keywords' =>'',
				'description'=>'',
		) );
		$this->display ();
	
	}
	// 我回应的话题
	public function my_replied_topics(){
		$userid = $this->userid;
		// 用户信息
		$strUser = $this->user_mod->getOneUser ( $userid );
		$arrTopics = $this->group_topics_mod->getUserRepliedTopic($userid, 20);
		foreach($arrTopics as $key=>$item){
			$arrTopic[] = $item;
			$arrTopic[$key]['user'] = $this->user_mod->getOneUser($item['userid']);
			$arrTopic[$key]['group'] = $this->_mod->getOneGroup($item['groupid']);
		}
		$this->assign ( 'strUser', $strUser );
		$this->assign ( 'arrTopic', $arrTopic );

		//我常去的小组 加入的小组
		$myJoinGroup = $this->_mod->getUserJoinGroup( $userid );
		if(is_array($myJoinGroup)){
			foreach($myJoinGroup as $key=>$item){
				$arrMyGroup[] = $this->_mod->getOneGroup($item['groupid']);
			}
			$this->assign('arrMyGroup',$arrMyGroup);//我加入和管理的小组
		}
		
		$this->_config_seo ( array (
				'title' => '我回应的话题',
				'subtitle'=>'小组_'.C('ik_site_title'),
				'keywords' =>'',
				'description'=>'',
		) );		
		$this->display ();		
	}
	
	// 浏览所有成员
	public function group_user(){
		$groupid = $this->_get( 'groupid', 'intval');
		$strGroup = $this->_mod->getOneGroup ( $groupid );
		// 存在性检查
		! $strGroup && $this->error ( '呃...你想要的东西不在这儿' );
		
		//小组组长信息
		$leaderId = $strGroup['userid'];
		$strLeader = $this->user_mod->getOneUser($leaderId);
		//管理员信息
		$strAdmin =  $this->group_users_mod->field('userid')->where(array('groupid'=>$groupid,'isadmin'=>'1'))->select();		
		if(is_array($strAdmin)){
			foreach($strAdmin as $item){
				$arrAdmin[] = $this->user_mod->getOneUser($item['userid']);
			}
		}
		//小组会员分页
		$page = $this->_get('p','intval',1);
		//查询条件 是否显示
		$map = array('groupid'=>$groupid);
		//显示列表
		$pagesize = 40;
		$count = $this->group_users_mod->where($map)->count('*');
		$pager = $this->_pager($count, $pagesize);
		$groupUser =  $this->group_users_mod->where($map)->order('userid desc')->limit($pager->firstRow.','.$pager->listRows)->select();
		if(is_array($groupUser)){
			foreach($groupUser as $key=>$item){
				$arrGroupUser[] = $this->user_mod->getOneUser($item['userid']);
				$arrGroupUser[$key]['isadmin'] = $item['isadmin'];
			}
		}
			
		$this->assign('pageUrl', $pager->show());
		$this->assign('arrGroupUser', $arrGroupUser);
		$this->assign('arrAdmin', $arrAdmin);
		$this->assign('strLeader', $strLeader);
		$this->assign('strGroup', $strGroup);
		
		if($page > '1'){
			$titlepage = " - 第".$page."页";
		}else{
			$titlepage='';
		}
		
		
		$this->_config_seo ( array (
				'title' => $strGroup['groupname'].'小组成员'.$titlepage,
				'subtitle'=> '小组_'.C('ik_site_title'),
				'keywords' => '',
				'description'=> '',
		) );
		$this->display();
		
	}	
	
	// 设置成员
	public function group_user_set(){
		$type = $this->_get ( 'd', 'trim' );
		if (! empty ( $type )) {
			switch ($type) {
				// 设置为管理员和取消为管理员
				case "isadmin" :
					
					$userid  = $this->_get( 'userid', 'intval');
					$groupid = $this->_get( 'groupid', 'intval');
					$isadmin = $this->_get( 'isadmin', 'intval');					
					
					if($userid == '' && $groupid=='' && $isadmin=='') $this->error("请不要冒险进入危险境地！");
					
					$strGroup = $this->_mod->getOneGroup ( $groupid );					
					if($this->userid != $strGroup['userid']) $this->error("机房重地，闲人免进！");
					
					$this->group_users_mod->where(array('userid'=>$userid,'groupid'=>$groupid))->save(array('isadmin'=>$isadmin));
					$this->redirect ( 'group/index/group_user', array('groupid'=>$groupid));
				break;
				// 踢出小组成员
				case "isuser" :
					$userid  = $this->_get( 'userid', 'intval');
					$groupid = $this->_get( 'groupid', 'intval');
					$isuser =  $this->_get( 'isuser', 'intval');
					if($userid == '' && $groupid=='' && $isuser=='') $this->error("请不要冒险进入危险境地！");
					$strGroup = $this->_mod->getOneGroup ( $groupid );
					if($this->userid != $strGroup['userid']) $this->error("机房重地，闲人免进！");
					
					$this->group_users_mod->where(array('userid'=>$userid, 'groupid'=>$groupid))->delete();
					
					//计算小组会员数
					$groupUserNum = $this->group_users_mod->where(array('groupid'=>$groupid))->count();
					
					//更新小组成员统计
					$this->_mod->where(array('groupid'=>$groupid))->save(array('count_user'=>$groupUserNum));
					$this->redirect ( 'group/index/group_user', array('groupid'=>$groupid));
				break;	
			}
		}
		
	}
	//rss 订阅
	public function rss(){			
		$groupid = $this->_get('id');
		$strGroup = $this->_mod->getOneGroup($groupid);
		$arrTopics = $this->group_topics_mod->getTopics($groupid,30);
		
		foreach($arrTopics as $key=>$item){
			$arrTopic[] = $item;
			$arrTopic[$key]['content'] = ikhtml_text('topic', $item['topicid'], $item['content']);
		}
		
		$pubdate = time();
		$this->assign('pubdate', $pubdate);
		$this->assign('arrTopic', $arrTopic);
		$this->assign('strGroup', $strGroup);
		$this->assign('xmlheader','<?xml version="1.0" encoding="UTF-8" ?>');
		$this->display('rss','UTF-8','text/xml');
	}	
		
}