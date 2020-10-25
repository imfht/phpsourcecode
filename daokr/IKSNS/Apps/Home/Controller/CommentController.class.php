<?php
/*
 * IKPHP 爱客开源社区 @copyright (c) 2012-3000 IKPHP All Rights Reserved 
 * @author 小麦
 * @Email:810578553@qq.com
 * @公共评论控制器 1.5.4 新增
 */
namespace Home\Controller;
use Common\Controller\FrontendController;

class CommentController extends FrontendController {
	public function _initialize() {
		parent::_initialize ();
		// 访问者控制
		if (!is_login() && in_array ( ACTION_NAME, array (
				'add',
				'delete',
				'recomment'
		) )) {
			$this->redirect ( 'home/user/login' );
		} else {
			$this->userid = is_login();
		}
		$this->mod = D('Common/Comment');		
	}
	// 添加评论
	public function add() {
		$content	= I('post.content', '','trim,htmlspecialchars');		
        $ik_comment		= I('post.ik_comment');
        
		$userid = $this->userid; //发布者
		if(IS_POST){
			if($content==''){
				$this->error('没有任何内容是不允许你通过滴^_^');
			}elseif(mb_strlen($content,'utf8')>10000){
				$this->error('发这么多内容干啥,最多只能写1000千个字^_^,回去重写吧！');
			}elseif(empty($ik_comment)){
				$this->error('你可以去火星了！');
			}			
		}else{
			$this->error('没有可以提交的数据！');
		}
        
        // 分解数据
        $arr_post = unserialize(base64_decode($ik_comment));
       	$page		= $arr_post['page'];
		$typeid		= $arr_post['typeid'];
		$type		= $arr_post['type'];
        $action_url	= $arr_post['cb_url'];
        
		//先查寻判断是否存在该模型
		$type_mod = M($type);
		$pkey = $type_mod->getPk();//主键
		$is_have = $type_mod->where(array($pkey=>$typeid))->getField($pkey);
		empty($is_have) && $this->error('你赶快去外星球吧！');
		
		// 组织数据
		$arrData = array (
				'typeid'	=> $typeid,
				'type'		=> $type,
				'userid'	=> $this->userid,
				'content'	=> ikwords($content),
				'addtime'	=> time(),
		);
		//添加
		if (false !== $this->mod->create ( $arrData )) {
			$commentid = $this->mod->add ();
		}
		if($commentid){
			//统计评论数
			$count_comment = $this->mod->where(array('typeid'=>$typeid))->count('*');
			//更新帖子最后回应时间和评论数
			$data = array(
					'count_comment'	=> $count_comment,
			);
			$type_mod->where(array($pkey=>$typeid))->save($data);
			
			// 积分记录钩子
			// 站内消息钩子
			
            //跳转还待优化
			$this->redirect ($action_url, array (
					'id' => $typeid,
					'p'  => $page,
			) );			
		}
	}
	// 删除评论
	public function delete(){ 
		$commentid = I('get.cid','0','intval,trim'); 
		$userid = $this->userid;
		$strComment = $this->mod->where(array('cid'=>$commentid))->find();
		empty($strComment) && $this->error("没有要删除的评论/回复");
		$type_mod = M($strComment['type']);
		$pkey = $type_mod->getPk();
		
		$strMod = $type_mod->where(array($pkey=>$strComment['typeid']))->find();
	
		// 只有应用发布人 可以删除 或 自己发的 其他权限不允许删除
		if($strMod['userid']== $userid || $strComment['userid'] == $userid){
			$is_del = $this->mod->delComment($commentid);
			if($is_del){
				$this->success("删除成功！");
			}
		}else{
			$this->error("你没有删除权限！");
		}
	}
	// 回复评论 Ajax 回复
	public function recomment(){
		$typeid  = I('post.typeid');
		$type    = I('post.type');
		$referid = I('post.referid');
		$content = $content	= I('post.content', '','trim,htmlspecialchars');
		//安全性检查
		if( mb_strlen($content, 'utf8') > 10000)
		{
			echo 1;//内容太多
			exit ();
		}elseif(empty($typeid) || empty($type) || empty($referid) || empty($content))
		{
			echo 2;//数据失败
			exit ();
		}
		//先查寻判断是否存在该模型
		$type_mod = M($type);
		$pkey = $type_mod->getPk();//主键
		$is_have = $type_mod->where(array($pkey=>$typeid))->getField($pkey);
		if(empty($is_have))	{
			echo 2;//数据失败
			exit ();			
		}
		// 组织数据
		$arrData = array (
				'typeid'	=> $typeid,
				'type'		=> $type,
				'referid'	=> $referid,		
				'userid'	=> $this->userid,
				'content'	=> ikwords($content),
				'addtime'	=> time(),
		);		
		//开始添加
		if (false !== $this->mod->create ( $arrData )) {
			$commentid = $this->mod->add ();
		}
		//成功后
		if($commentid){
			//统计评论数
			$count_comment = $this->mod->where(array('typeid'=>$typeid))->count('*');
			//更新帖子最后回应时间和评论数
			$data = array(
					'count_comment'	=> $count_comment,
			);
			$type_mod->where(array($pkey=>$typeid))->save($data);
			
			// 积分记录钩子
			// 站内消息钩子
			
            //跳转
            echo 0; exit();			
		}				
	}
	

}
