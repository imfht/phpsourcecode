<?php
/*
 * IKPHP 爱客开源社区 @copyright (c) 2012-3000 IKPHP All Rights Reserved 
 * @author 小麦
 * @Email:810578553@qq.com
 * 个人空间APP首页
 */
namespace Article\Controller;

class IndexController extends ArticleBaseController {
	public function _initialize() {
		parent::_initialize ();
		// 访问者控制
		if (! $this->visitor && in_array ( ACTION_NAME, array (
				'add',
				'delete',
				'edit',
				'publish',
				
		) )) {
			$this->redirect ( 'home/user/login' );
		} else {
			$this->userid = $this->visitor['userid'];
		}		
		//应用所需 mod
		$this->mod      	= D ( 'Article' );
		$this->cate_mod 	= D ( 'ArticleCate' );
		$this->item_mod 	= M ( 'ArticleItem' );
		$this->channel_mod  = D ( 'ArticleChannel' );
		$this->comment_mod  = D ( 'ArticleComment' );
		$this->user_mod     = D ( 'Common/User' );

	}
	//首页
	public function index() {
		
		// 获取分类
		$arrCate = $this->cate_mod->getAllCate();		
		
		//查询条件 是否审核
		$map = array('isaudit'=>'0');
		//显示列表
		$pagesize = 30;
		$count = $this->item_mod->where($map)->order('addtime desc')->count('itemid');
		$pager = $this->_pager($count, $pagesize);
		$arrItemid =  $this->item_mod->field('itemid')->where($map)->order('addtime desc')->limit($pager->firstRow.','.$pager->listRows)->select();
		foreach($arrItemid as $key=>$item){
			$arrArticle [] = $this->mod->getOneArticle($item['itemid']); 
		}
			
		$this->assign('pageUrl', $pager->show());		
		
		$this->assign ( 'arrCate', $arrCate );
		$this->assign ( 'arrArticle', $arrArticle );
		
		
		$this->_config_seo ( array (
				'title' => '最新美文',
				'subtitle'=>'阅读_'.C('ik_site_title'),
				'keywords' => '互联网,摄影,动漫,思考,阅读,旅游,时尚,居家,美食,数码,情感',
				'description'=> '可以通过投稿、发布优质文章，还可以关注最新博客日志，提供最新最好的实时动态美文。',
		) );
		$this->display ();	
	
	}
	
	// 发表文章
	public function add() { 
		$userid = $this->userid;
		// 获取资讯分类
		$arrChannel = $this->channel_mod->getAllChannel(array('isnav'=>'0'));
		$arrCate = ''; // 初始化下拉列表
		$arrCatename = array ();
		foreach ( $arrChannel as $key => $item ) {
			$arrCatename = $this->cate_mod->getCateByNameid ( $item ['nameid'] );  
			$arrCate .= '<optgroup label="' . $item ['name'] . '">';
			foreach ( $arrCatename as $key1 => $item1 ) {
				$arrCate .= '<option  value="' . $item1 ['cateid'] . '" >' . $item1 ['catename'] . '</option>';
			}
			$arrCate .= '</optgroup>';
		}
		
		$this->assign ( 'arrCate', $arrCate );
		
		$this->_config_seo ( array (
				'title' => '发表新文章',
				'subtitle'=>'阅读_'.C('ik_site_title'),
				'keywords' => '',
				'description'=> '',
		) );
		$this->display ();
	}
	// 保存更新文章
	public function publish() {
		if (IS_POST) {
			$userid = $this->userid;
			$id = $this->_post ( 'id' );
			
			$item ['userid'] = $userid;
			$item ['cateid'] = $this->_post ( 'cateid', 'intval' );
			$item ['title']  = $this->_post ( 'title');
			$item ['addtime'] = time ();
				
			$data ['content'] = $this->_post ( 'content' );
			$data ['postip'] = get_client_ip ();
			$data ['newsauthor'] = $this->user_mod->get ( 'username' );
			$data ['newsfrom'] = $this->_post ( 'newsfrom', 'trim', '' );
			$data ['newsfromurl'] = $this->_post ( 'newsfromurl', 'trim', '' );	

			//安全性判断
			if(empty($item ['title']) || empty($item ['cateid']) || empty($data ['content'])){
				$this->error('标题、分类、内容都必须填写！');
			}elseif (mb_strlen($item ['title'],'utf8')>50){
				$this->error('标题太长了！');
			}elseif (mb_strlen($data ['content'],'utf8')>20000){
				$this->error('文章内容太长了！');
			}
				
			if (empty ( $id )) {
				// 新增
				if (false !== $this->item_mod->create ( $item )) {
					$itemid = $data ['itemid'] = $this->item_mod->add ();
					if ($itemid > 0) {
						// 保存article
						if (false !== $this->mod->create ( $data )) {
							$id = $this->mod->add ();
							/////////////执行更新图片信息/////////////
							$arrSeqid = $this->_post ( 'seqid');
							$arrTitle = $this->_post ( 'photodesc');
							if(is_array($arrSeqid)){
								foreach($arrSeqid as $key=>$item){
									$seqid = $arrSeqid[$key];
									$imgtitle = empty($arrTitle[$key]) ? '' : $arrTitle[$key];
									$layout = $this->_post ( 'layout_'.$seqid);
									$dataimg = array('title'=>$imgtitle, 'align'=> $layout,'typeid'=>$id);
									$where = array('type'=>'article','typeid'=>'0','seqid'=>$seqid);
									D('Common/Images')->updateImage($dataimg,$where);

									// 更新 isphoto
									$this->item_mod->where(array('itemid'=>$itemid))->save(array('isphoto'=>1));
								}
							}
							/////////////执行更新图片信息结束//////////////////
							//执行更新视频信息
							$arrVideoseqid = $this->_post ( 'videoseqid');
							if(is_array($arrVideoseqid)){
								foreach($arrVideoseqid as $key=>$item){
									$seqid = $arrVideoseqid[$key];
									$title = $this->_post ( 'video_'.$seqid.'_title','trim','');
									$datavideo = array('title'=>$title, 'typeid'=>$id);
									$where = array('type'=>'article','typeid'=>'0','seqid'=>$seqid);
									D('Common/Videos')->updateVideo($datavideo,$where);
								}
							}
							
						}
					}
				}
			} else {
				// 更新
				$itemuserid = $this->item_mod->field('userid')->where ( array ('itemid' => $id) )->getField('userid');				
				if($itemuserid!=$userid){
					$this->error('非法操作！');
				}
				$this->mod->where ( array ('aid' => $id) )->save ( $data );
				$this->item_mod->where ( array ('itemid' => $id) )->save ( $item );
				// 执行更新图片信息
				$arrSeqid = $this->_post ( 'seqid');
				$arrTitle = $this->_post ( 'photodesc');
				if(is_array($arrSeqid)){
					foreach($arrSeqid as $key=>$item){
						$seqid = $arrSeqid[$key];
						$imgtitle = empty($arrTitle[$key]) ? '' : $arrTitle[$key];
						$layout = $this->_post ( 'layout_'.$seqid);
						$dataimg = array('title'=>$imgtitle, 'align'=> $layout,'typeid'=>$id);
						$where = array('type'=>'article','typeid'=>$id,'seqid'=>$seqid);
						D('Common/Images')->updateImage($dataimg,$where);
						// 更新 isphoto
						$this->item_mod->where(array('itemid'=>$id))->save(array('isphoto'=>1));
					}
				}
				/////////////执行更新图片信息结束//////////////////
				//执行更新视频信息
				$arrVideoseqid = $this->_post ( 'videoseqid' );
				if(is_array($arrVideoseqid)){
					foreach($arrVideoseqid as $key=>$item){
						$seqid = $arrVideoseqid[$key];
						$title = $this->_post ( 'video_'.$seqid.'_title','trim','');
						$datavideo = array('title'=>$title, 'typeid'=>$id);
						$where = array('type'=>'article','typeid'=>'0','seqid'=>$seqid);
						D('Common/Videos')->updateVideo($datavideo,$where);
					}
				}
				
			}
			
			$this->redirect ( 'article/index/show', array (
					'id' => $id 
			) );
		} else {
			$this->redirect ( 'article/index' );
		}
	}
	// 编辑文章
	public function edit(){
		$user = $this->user_mod->get ();
		$id = $this->_get ( 'id' ); //文章id
		// 根据id获取内容
		$strArticle = $this->mod->where(array('aid' => $id))->find();
		if(is_array($strArticle)){
			$articleItem = $this->item_mod->where(array('itemid'=>$strArticle['itemid']))->find();
			//array_merge() 函数把两个或多个数组合并为一个数组
			$strArticle = array_merge($articleItem, $strArticle);
		}
		if($strArticle['userid']!=$user['userid']) $this->error('您没有权限编辑该文章');
		// 获取资讯分类
		$arrChannel = $this->channel_mod->select ();
		$arrCate = ''; // 初始化下拉列表
		$arrCatename = array ();
		foreach ( $arrChannel as $key => $item ) {
			$arrCatename = $this->cate_mod->getCateByNameid ( $item ['nameid'] );
			$arrCate .= '<optgroup label="' . $item ['name'] . '">';
			foreach ( $arrCatename as $key1 => $item1 ) {
				if($item1 ['cateid'] == $strArticle['cateid']){
					$arrCate .= '<option  value="' . $item1 ['cateid'] . '" selected>' . $item1 ['catename'] . '</option>';
				}else{
					$arrCate .= '<option  value="' . $item1 ['cateid'] . '" >' . $item1 ['catename'] . '</option>';
				}
			}
			$arrCate .= '</optgroup>';
		}
		//浏览该照片
		$type = 'article';
		$arrPhotos = D('Common/Images')->getImagesByTypeid($type, $id);
		//浏览改topic_id下的视频
		$arrVideos = D('Common/Videos')->getVideosByTypeid($type, $id);
		
		$this->assign ( 'arrCate', $arrCate );
		$this->assign ( 'strArticle', $strArticle );
		$this->assign ( 'arrPhotos', $arrPhotos );
		$this->assign ( 'arrVideos', $arrVideos );

		$this->_config_seo ( array (
				'title' => '编辑“'.$strArticle['title'].'”',
				'subtitle'=>'阅读_'.C('ik_site_title'),
				'keywords' => '',
				'description'=> '',
		) );
		$this->display ('edit');
	}
	// 编辑文章
	public function delete(){
		$userid = $this->userid;
		$id = $this->_get ( 'id' , 'intval'); //文章id
		// 根据id获取内容
		$strArticle = $this->mod->getOneArticle($id);
		if($strArticle['userid'] != $userid) $this->error('您没有权限删除该文章');
		// 执行删除
		$this->mod->delOneArticle($id);
		$this->success('删除成功！',U('article/index/index'));
	}
	// 文章详情页
	public function show() {
		$userid = $this->userid; 
		$id = I('get.id');
		// 根据id获取内容
		$strArticle = $this->mod->getOneArticle ( $id ); 
		! $strArticle && $this->error ( '呃...你想要的东西不在这儿' );
		
		// 浏览量加 +1
		if($strArticle ['userid']!=$userid){
			$this->item_mod->where(array('itemid'=>$strArticle['itemid']))->setInc('count_view');
		}
		//上一篇帖子
		$upArticle = $this->mod->getOneArticle($id-1);
			
		//下一篇帖子
		$downArticle = $this->mod->getOneArticle($id+1);
		
		//获取评论
		$this->_buildComment($id, 'ArticleItem', $strArticle['userid'], 'article/index/show');
		
		//获取最新的 8文章
		$arrNewArticle = $this->mod->getArticleItemByMap('count_view desc','10');
		$this->assign ( 'arrNewArticle', $arrNewArticle );

		$this->assign ( 'strArticle', $strArticle );
		$this->assign ( 'upArticle', $upArticle );
		$this->assign ( 'downArticle', $downArticle );
		$this->assign ( 'strUser', $strArticle ['user'] );

		$this->_config_seo ( array (
				'title' => $strArticle ['title'],
				'subtitle'=> '阅读_'.C('ik_site_title'),
				'keywords' => ikscws($strArticle ['title']),
				'description'=> getsubstrutf8(clearText($strArticle['content']),0,200),
		) );
					
		$this->display ();
	}
	//我的文章
	public function my_article(){
		$this->error("还在开发中！");
	}

}