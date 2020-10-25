<?php
/*
 * IKPHP 爱客开源社区 @copyright (c) 2012-3000 IKPHP All Rights Reserved 
 * @author 小麦
 * @Email:810578553@qq.com
 * 个人空间 日记
 */
namespace Space\Controller;

class NotesController extends SpaceBaseController {
	public function _initialize() {
		parent::_initialize ();
		if (!is_login () && in_array ( ACTION_NAME, array (
				'create',
				'update',
				'edit',
				'delete',
				'recomment',
				'addcomment',
				'delcomment',
		))) {
			$this->redirect ('home/user/login');
		}else{
			$this->userid = $this->visitor['userid'];
		}
		//应用所需 mod
		$this->user_mod = D('Common/User');
		$this->note_mod = D('UserNote');
		//$this->comment_mod = D('NoteComment');		
	}
	//日记首页
	public function index(){
		$userid = I('get.id',0,'trim,intval');
		$userid > 0 && $user = $this->user_mod->getOneUser($userid);
		if(!empty($user)){
			 if($this->userid == $userid){
			 	$title = '我的日记';
			 }else{
			 	$title = $user['username'].'的日记';
			 }
		}else{
			$this->error('呃...你想访问的页面不存在');
		}
				
		//查询条件 
		$map['userid'] = $userid;
		$map['title'] = array('NEQ','');
		//显示列表
		$pagesize = 20;
		$count = $this->note_mod->where($map)->order('addtime desc')->count('noteid');
		$pager = $this->_pager($count, $pagesize);
		$arrNoteid =  $this->note_mod->field('noteid')->where($map)->order('addtime desc')->limit($pager->firstRow.','.$pager->listRows)->select();
		$arrNote = array();
		if(is_array($arrNoteid)){
			foreach($arrNoteid as $key=>$item){
				$arrNote [] = $this->note_mod->getOneNote(array('noteid'=>$item['noteid']));
				$arrNote [$key]['content'] = ikhtml_text('note', $item['noteid'], $arrNote[$key]['content']);
			}			
		}
		$this->assign('arrNote',$arrNote);
		$this->assign('strUser', $user);
		$this->assign('pageUrl', $pager->show());
		
		//最多阅读
		$hotNotes = $this->note_mod->getHotNotes($userid,8);
		$this->assign('hotNotes', $hotNotes);
		
		$this->_config_seo ( array (
				'title' => $title,
				'subtitle'=> '_日记_'.C('ik_site_title'),
				'keywords' => '网络日记,记事本,日记,日志,相册',
				'description'=> '把生活中的点点滴滴都记录下来吧；分享生活中有意义的事情，留住青春，珍藏您一生的记忆！',
		) );		
		$this->display();
	}
	//新加日记
	public function create(){
		$userid = $this->userid;
		//查询预先数据
		$strNote = $this->note_mod->getOneNote(array('userid'=>$userid,'title'=>''));
		if(!$strNote){
			//新增一条
			$data['userid'] = $userid;
			$data['cateid'] = 0;
			$data['isaudit'] = 0;
			$noteid = $this->note_mod->add($data); 
			$strNote['noteid'] = $noteid;
		}
		//浏览该照片
		$type = 'note';
		$arrPhotos = D('Common/Images')->getImagesByTypeid($type, $strNote['noteid']);
		$this->assign('arrPhotos',$arrPhotos);

		
		$this->assign('strNote',$strNote);
		$this->_config_seo ( array (
				'title' => '新加日记',
				'subtitle'=> '_日记_'.C('ik_site_title'),
				'keywords' => '',
				'description'=> '',
		) );
		$this->display();
	}
	public function update(){
		$userid = $this->userid;
		$noteid = I('post.noteid',0,'trim,intval');
		//查询
		$strNote = $this->note_mod->getOneNote(array('userid'=>$userid,'noteid'=>$noteid));
		//开始添加
		if($strNote){
			$data['title']   = $this->_post ( 'title');
			$data['content'] = $this->_post ( 'content' );
			$data['privacy'] = $this->_post ( 'privacy' ); //隐私
			$data['isreply'] = $this->_post ( 'isreply' ); //是否允许评论
			$data['isaudit'] = 1; //设置为已审核
			
			if(empty($strNote['addtime'])){
				$data['addtime'] = time(); //如果存在时间不更新
			}

			//安全性判断
			if(empty($data ['title']) || empty($data ['content'])){
				$this->error('标题、内容都必须填写！');
			}elseif (mb_strlen($data ['title'],'utf8')>60){
				$this->error('标题太长了，最多60个字！');
			}elseif (mb_strlen($data ['content'],'utf8')>20000){
				$this->error('文章内容太长了！');
			}
			//开始更新
			$this->note_mod->where(array('noteid'=>$noteid))->save($data);
			/////////////执行更新图片信息/////////////
			$arrSeqid = I('post.seqid');
			$arrTitle = I('post.photodesc');
			if(is_array($arrSeqid)){
				foreach($arrSeqid as $key=>$item){
					$seqid = $arrSeqid[$key];
					$imgtitle = empty($arrTitle[$key]) ? '' : $arrTitle[$key];
					$layout = $this->_post ( 'layout_'.$seqid);
					$dataimg = array('title'=>$imgtitle, 'align'=> $layout,'typeid'=>$noteid);
					$where = array('type'=>'note','typeid'=>$noteid,'seqid'=>$seqid);
					D('Common/Images')->updateImage($dataimg,$where);
				}
			}
			/////////////执行更新图片信息结束//////////////////
										
			$this->redirect('space/notes/show',array('id'=>$noteid));
		}else{
			$this->error('呃...你没有权限访问该页面');
		}
	}
	//日记显示页
	public function show(){
		$id = I('get.id',0,'intval');
		$strNote = $this->note_mod->getOneNote(array('noteid'=>$id));

		if(!$strNote){
			$this->error('呃...你想访问的页面不存在');
		}
		$strNote ['content'] = nl2br ( ikhtml('note',$id,$strNote['content'],1));
		
		$strNote ['user'] = $this->user_mod->getOneUser($strNote['userid']);
		
		$arrNotes = $this->note_mod->getNotes(array('userid'=>$strNote['userid']),10);
		
		//浏览数+1
		if($this->userid != $strNote['userid']){
			$this->note_mod->where(array('noteid'=>$id))->setInc('count_view');
		}
		

		//获取评论
		$this->_buildComment($id, 'UserNote', $strNote['userid'], 'space/notes/show');
		//评论list结束	
		
		$this->assign('strNote',$strNote);
		$this->assign('arrNotes',$arrNotes);
		$this->_config_seo ( array (
				'title' => $strNote['title'],
				'subtitle'=> '_日记_'.C('ik_site_title'),
				'keywords' => ikscws($strNote ['title']),
				'description'=> getsubstrutf8(clearText($strNote['content']),0,200),
		) );
		$this->display();
	}
	
	//删除日记
	public function delete(){
		$userid = $this->userid;
		$noteid = I('get.id');
		$strNote = $this->note_mod->getOneNote(array('noteid'=>$noteid,'userid'=>$userid));
		if(!$strNote){$this->error('呃...你没有权限删除该日记');}
		
		$this->note_mod->deleteOneNote($noteid);
		$this->redirect('space/notes/index',array('id'=>$userid));
	}
	//编辑
	public function edit(){
		$userid = $this->userid;
		$noteid = I('get.id');
		//查询预先数据
		$strNote = $this->note_mod->getOneNote(array('noteid'=>$noteid,'userid'=>$userid));
		if(!$strNote){$this->error('呃...你没有权限访问该页面');}
		
		//浏览该照片
		$type = 'note';
		$arrPhotos = D('Common/Images')->getImagesByTypeid($type, $noteid);
		$this->assign('arrPhotos',$arrPhotos);
		
		$this->assign('strNote',$strNote);
		$this->_config_seo ( array (
				'title' => '编辑日记',
				'subtitle'=> '_日记_'.C('ik_site_title'),
				'keywords' => '',
				'description'=> '',
		) );		
		$this->display();
	}		 			
}