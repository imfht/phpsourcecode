<?php
/*
 * IKPHP 爱客开源社区 @copyright (c) 2012-3000 IKPHP All Rights Reserved 
 * @author 小麦
 * @Email:810578553@qq.com
 * 个人空间 日记
 */
namespace Space\Controller;

class PhotosController extends SpaceBaseController {
	public function _initialize() {
		parent::_initialize ();
		if (!is_login () && in_array ( ACTION_NAME, array (
				'ajaxupload',
				'create',
				'delphoto',
				'editalbum',
				'editphoto',
				'info',
				'uploadPhoto',
		))) {
			$this->redirect ('home/user/login');
		}else{
			$this->userid = $this->visitor['userid'];
		}
		//应用所需 mod
		$this->user_mod    = D('Common/User');
		$this->album_mod   = D('UserPhotoAlbum');
		$this->photo_mod   = D('UserPhoto');	
	}
	//相册首页
	public function index(){
		$userid = $this->_get('id','trim,intval');
		$userid > 0 && $user = $this->user_mod->getOneUser($userid);
		if(!empty($user)){
			$title = $user['username'].'的相册';
		}else{
			$this->error('呃...你想访问的页面不存在');
		}
		//获取相册列表
		$arrAlbum = $this->album_mod->getAlbums(array('userid'=>$userid));
		
		//获取最新的评论
		$all_photos = $this->photo_mod->field('photoid')->where(array('userid'=>$userid))->select();
		$str_pid ='';
		foreach ($all_photos as $ap){
			if(empty($str_pid)){
				$str_pid .= $ap['photoid'];
			}else{
				$str_pid .= ','. $ap['photoid'];
			}
		}
		$where_newcomment['type'] = 'UserPhoto';
		$where_newcomment['typeid'] = array('exp',' IN ('.$str_pid.') ');
		$arrNewComment = D('Common/Comment')->getNewComment($where_newcomment, 8);
		$this->assign('arrNewComment',$arrNewComment);
		
		$this->assign('arrAlbum',$arrAlbum);
		$this->assign('user',$user);
		
		
		$this->_config_seo ( array (
				'title' => $title,
				'subtitle'=> '_相册_'.C('ik_site_title'),
				'keywords' => '网络相册,免费相册,相片,照片,相册',
				'description'=> '分享生活中的照片，爱客相册，留住青春，珍藏您一生的记忆！',
		) );		
		$this->display();
	}
	//相册
	public function album(){
		$type = I('get.d', 0, 'trim');
		
		if(empty($type)){
			//相册显示页面
			$albumid  =  I('get.id', 0, 'trim,intval');
			$strAlbum = $this->album_mod->getOneAlbum($albumid);
			if($strAlbum){
				$this->albumList($albumid);
			}else{
				$this->error('呃...你想访问的相册不存在');
			}
		}else{
			switch ($type) {
				case "create" :
					$this->create();
					break;
				case "upload" :
					$this->uploadPhoto();
					break;
				case "ajaxupload" :
					$this->ajaxupload();
					break;	
				case "info" :
					$this->info();
					break;
				case "edit" :
					$this->editalbum();
					break;	
				default:
					$this->error('呃...你想访问的页面不存在');
			}			
		}
	}
	//创建相册
	public function create(){
		if(!is_login()) $this->redirect ( 'home/user/login' );
		if(IS_POST){
			$data['userid'] = $this->userid;
			$data['albumname'] = $this->_post('albumname');
			$data['albumdesc'] = $this->_post('albumdesc');
			$data['orderid'] = $this->_post('orderid','trim','');
			$data['privacy'] = $this->_post('privacy','intval','');
			$data['isreply'] = $this->_post('isreply','trim','1'); // 1表示允许回复
			//录入检查
			if( mb_strlen($data ['albumname'],'utf8')>20)
			{
				$this->error ('相册名太长啦，最多20个字...^_^！');
				
			}else if( mb_strlen($data ['albumdesc'],'utf8')>120){
				
				$this->error ('相册描述太多了，最多120个字...^_^！');
			}
			
			//开始创建
			if (false === $this->album_mod->create ($data)) {
				$this->error ( $this->album_mod->getError () );
			}
			// 保存当前数据对象
			$albumid = $this->album_mod->add ();
			if ($albumid !== false) { // 保存成功
				$this->redirect('space/photos/album',array('d'=>'upload','id'=>$albumid));
			} else {
				// 失败提示
				$this->error ( '创建相册失败!' );
			}
			
		}else{

			$this->_config_seo ( array (
				'title' => '创建新相册',
				'subtitle'=> '相册_'.C('ik_site_title'),
				'keywords' => '',
				'description'=> '',
			) );
			$this->display('create');
		}	
	}
	public function editalbum(){
		$albumid = $this->_get('id','trim,intval');
		$strAlbum = $this->album_mod->getOneAlbum($albumid);
		if($strAlbum['userid']==$this->userid){
			if(IS_POST){
				
				$data['albumname'] = $this->_post('albumname');
				$data['albumdesc'] = $this->_post('albumdesc');
				$data['orderid'] = $this->_post('orderid','trim','');
				$data['privacy'] = $this->_post('privacy','intval','');
				$data['isreply'] = $this->_post('isreply','trim','1'); // 1表示允许回复
				//录入检查
				if( mb_strlen($data ['albumname'],'utf8')>20)
				{
					$this->error ('相册名太长啦，最多20个字...^_^！');
				
				}else if( mb_strlen($data ['albumdesc'],'utf8')>120){
				
					$this->error ('相册描述太多了，最多120个字...^_^！');
				}
				
				//开始保存
				if (false === $this->album_mod->where(array('albumid'=>$albumid))->save($data)) {
					$this->error ( $this->album_mod->getError () );
				}else{
					$this->redirect('space/photos/album',array('id'=>$albumid));
				}
				
			}else{
				$this->_config_seo ( array (
						'title' => '修改相册属性 - '.$strAlbum['albumname'],
						'subtitle'=> '相册_'.C('ik_site_title'),
						'keywords' => '',
						'description'=> '',
				) );
				$this->assign('strAlbum',$strAlbum);
				$this->display('editalbum');
			}
			
		}else{
			$this->error('你没有权限更新！');
		}
	}
	//上传照片 普通上传
	public function uploadPhoto(){
		if(!is_login() ) $this->redirect ( 'home/user/login' );
		$albumid = I('get.id',0 , 'trim,intval');
		$type = I('get.type');
		if(!empty($albumid)){
			//获取相册信息
			$strAlbum = $this->album_mod->getOneAlbum($albumid);
			if($strAlbum['userid']==$this->userid){
				
				if(IS_POST){
					$smalltime = $this->_get('t','trim');
					//上传
					$arrUpload = $this->photo_mod->addPhoto($_FILES['picfile'],$this->userid,$albumid);
					
					//如果是单个上传
					if(!empty($arrUpload['savename']) && $arrUpload){
						$arrData = array(
								'userid'	=> $this->userid,
								'albumid'	=> $albumid,
								'photopath' => $arrUpload['savepath'],
								'photoname' => $arrUpload['savename']
						);
						//插入
						if(!false == $this->photo_mod->create ($arrData)){
							$photoid = $this->photo_mod->add();
						}		
										
					}elseif(empty($arrUpload['savename']) && $arrUpload){
						//多个上传
						foreach ($arrUpload as $k=>$v){
							$arrData = array(
									'userid'	=> $this->userid,
									'albumid'	=> $albumid,
									'photopath' => $v['savepath'],
									'photoname' => $v['savename']
							);
							//插入
							if(!false == $this->photo_mod->create ($arrData)){
								$photoid = $this->photo_mod->add();
							}
						}
					}
					
					$this->redirect('space/photos/album',array('d'=>'info','id'=>$albumid,'t'=>$smalltime));
					
				}else{
					$this->assign('type',$type);
					$this->assign('smalltime',time());
					$this->assign('strAlbum',$strAlbum);
					$this->_config_seo ( array (
							'title' => '上传照片 - '.$strAlbum['albumname']
					) );
					
					$this->_config_seo ( array (
						'title' => '上传照片 - '.$strAlbum['albumname'],
						'subtitle'=> '相册_'.C('ik_site_title'),
						'keywords' => '',
						'description'=> '',
					) );
					$this->display('upload');
				}
				
			}else{
				$this->error('你没有权限更新照片！');
			}
		}
	}
	//ajax上传照片
	public function ajaxupload(){
		if(IS_POST){
			$userid = $this->_post('userid','intval');
			if(empty($userid) && $userid != $this->userid) exit;
			
			$albumid = $this->_post('albumid','intval');
			$timestamp = I('post.timestamp');
			$verifyToken = md5('unique_salt' . $timestamp);
			
			if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
				//上传
				$arrUpload = $this->photo_mod->addPhoto($_FILES['Filedata'],$userid,$albumid);
				//成功
				//如果是单个上传
				if(!empty($arrUpload['savename']) && $arrUpload){
					$arrData = array(
							'userid'	=> $userid,
							'albumid'	=> $albumid,
							'photopath' => $arrUpload['savepath'],
							'photoname' => $arrUpload['savename']
					);
					//插入
					if(!false == $this->photo_mod->create ($arrData)){
						$photoid = $this->photo_mod->add();
					}		
									
				}elseif(empty($arrUpload['savename']) && $arrUpload){
					//多个上传
					foreach ($arrUpload as $k=>$v){
						$arrData = array(
								'userid'	=> $userid,
								'albumid'	=> $albumid,
								'photopath' => $v['savepath'],
								'photoname' => $v['savename']
						);
						//插入
						if(!false == $this->photo_mod->create ($arrData)){
							$photoid = $this->photo_mod->add();
						}
					}
				}
				
				echo '1';
			}
			
		}else{
			echo '0';
		}
	}
	//上传完成
	public function info(){
		$albumid = $this->_get('id','intval');
		$userid = $this->userid;
		
		!empty($albumid) && $strAlbum = $this->album_mod->getOneAlbum($albumid);
		
		if($strAlbum && $strAlbum['userid'] == $userid){
			if(IS_POST){
				$pid = $this->_post('albumface','trim,intval','0');
				$arrphotoid = $this->_post('photoid');
				$arrphotodesc = $this->_post('photodesc');

				
				foreach($arrphotodesc as $key => $item){
					if($item){
						$photoid = $arrphotoid[$key];
						$this->photo_mod->where(array('photoid'=>$photoid))->setField('photodesc',h($item));
					}	
				}
				
				if($pid>0){
					$albumface = $this->photo_mod->getOnePhoto($pid);
					
					$this->album_mod->where(array('albumid'=>$albumid))->setField(array('path'=>$albumface['photopath'],'albumface'=>$albumface['photoname']));
				}
				$this->redirect('space/photos/album',array('id'=>$albumid));
				
			}else{
				$smalltime = $this->_get('t','trim');
				if(!empty($smalltime)){
					$map['addtime'] = array('gt',$smalltime);
					$map['userid'] = $userid;
					$map['albumid'] = $albumid;
					$arrPhoto = $this->photo_mod->getPhotos($map);
					//empty($arrPhoto) && $this->error('呃...你想访问的页面不存在');
					$title = '完成上传！添加描述 - '.$strAlbum['albumname'];
				}else{
					$arrPhoto = $this->photo_mod->getPhotos(array('userid'=>$userid,'albumid'=>$albumid));
					$title = '批量修改 - '.$strAlbum['albumname'];
				}
				$this->assign('strAlbum',$strAlbum);
				$this->assign('arrPhoto',$arrPhoto);
				
				$this->_config_seo ( array (
						'title' => $title,
						'subtitle'=> '相册_'.C('ik_site_title'),
						'keywords' => '',
						'description'=> '',
				) );
				$this->display('complete');
			}
		}else{
			$this->error('呃...你无权访问此页面');
		}
	}
	//相册显示
	public function albumList($albumid){
		$strAlbum = $this->album_mod->getOneAlbum($albumid);
		$user = $this->user_mod->getOneUser($strAlbum['userid']);
		
		$page_max = 100; //发现页面最多显示页数
		$where = array('albumid'=>$albumid);
		$this->waterfall($where, 'photoid DESC', $page_max);
		
		//最新回应
		$all_photos = $this->photo_mod->field('photoid')->where(array('userid'=>$strAlbum['userid']))->select();
		$str_pid ='';
		foreach ($all_photos as $ap){
			if(empty($str_pid)){
				$str_pid .= $ap['photoid'];
			}else{
				$str_pid .= ','. $ap['photoid'];
			}
		}
		$where_newcomment['type'] = 'UserPhoto';
		$where_newcomment['typeid'] = array('exp',' IN ('.$str_pid.') ');
		$arrNewComment = D('Common/Comment')->getNewComment($where_newcomment, 8);
		$this->assign('arrNewComment',$arrNewComment);
		
		
		$this->assign('strAlbum',$strAlbum);
		$this->assign('user',$user);

		$this->_config_seo ( array (
				'title' => $user['username'].'的相册',
				'subtitle'=> $strAlbum['albumname'].'_相册_'.C('ik_site_title'),
				'keywords' => ikscws($strAlbum['albumname']),
				'description'=> $strAlbum['albumdesc'],
		) );
		$this->display('album');
	}
	public function index_ajax() {
		$albumid = $this->_get('albumid','intval');
		$where = array('albumid'=>$albumid);
		$this->wall_ajax($where);
	}
	//照片显示
	public function show(){
		$pid = $this->_get('id','trim,intval');
		!empty($pid) && $strPhoto = $this->photo_mod->getOnePhoto($pid);
		if($strPhoto){
			$strAlbum = $this->album_mod->field('albumname,orderid')->where(array('albumid'=>$strPhoto['albumid']))->find();
			$order = 'addtime '.$strAlbum['orderid'];
			$arrPhotoIds = $this->photo_mod->field('photoid')->where(array('albumid'=>$strPhoto['albumid']))->order($order)->select();
			
			$arrPhotoId = array();
			foreach($arrPhotoIds as $item){
				$arrPhotoId[] = $item['photoid'];
			}
			rsort($arrPhotoId);
			$nowkey = array_search($pid,$arrPhotoId);
			$nowPage =  $nowkey+1 ;
			$countPage = count($arrPhotoId);
			$prev = $arrPhotoId[$nowkey - 1];
			$next = $arrPhotoId[$nowkey +1];

			$strPhoto['nexturl'] = U('space/photos/show',array('id'=>$next));
			$strPhoto['prevturl'] = U('space/photos/show',array('id'=>$prev));
			$strPhoto['nowPage'] = $nowPage;
			$strPhoto['countPage'] = $countPage;
			
			$user = $this->user_mod->getOneUser($strPhoto['userid']);
			
			//获取评论
			$this->_buildComment($pid, 'UserPhoto', $strPhoto['userid'], 'space/photos/show');
			//评论list结束					
			
			//我的相册
			$map['privacy'] = 1; //公开
			$map['userid'] = $user['userid'];
			$arrAlbum = $this->album_mod->getAlbums($map,'uptime desc',4);
			$this->assign('arrAlbum',$arrAlbum);
			
			
			$this->assign('strPhoto',$strPhoto);
			if($this->userid == $strPhoto['userid']){
				$title = '我的相册 - '.$strAlbum['albumname'];
			}else{
				//浏览量+1
				$this->photo_mod->where(array('photoid'=>$pid))->setInc('count_view',1);
				$title = $user['username'].'的相册 - '.$strAlbum['albumname'];
			}
			$this->_config_seo ( array (
					'title' => $title,
					'subtitle'=> '_相册_'.C('ik_site_title'),
					'keywords' => ikscws($strAlbum['albumname']),
					'description'=> $strPhoto['photodesc'],
			) );
			$this->display();
		}else{
			$this->error('呃...你想访问的页面不存在');
		}
	}
	
	//编辑照片描述
	public function editphoto(){
		$pid = $this->_post('photoid','intval');
		$pinfo = $this->_post('photodesc');
		$userid = $this->userid;
		!empty($pid) && $strPhoto = $this->photo_mod->getOnePhoto($pid);
		if($userid>0 || $strPhoto['userid'] ==$userid){
			$this->photo_mod->where(array('photoid'=>$pid))->setField('photodesc', $pinfo);
			$this->ajaxReturn(array('r'=>1,'html'=>$pinfo));
		}else{
			$this->ajaxReturn(array('r'=>0,'error'=>'无权更新'));
		}
	}
	//删除照片
	public function delphoto(){
		$pid = $this->_get('id','trim,intval');
		$userid = $this->userid;
		!empty($pid) && $strPhoto = $this->photo_mod->getOnePhoto($pid);
		if($userid>0 || $strPhoto['userid'] ==$userid){
			if(!false == $this->photo_mod->delPhoto($pid)){
				$this->redirect('space/photos/album',array('id'=>$strPhoto['albumid']));
			}else{
				$this->error($this->photo_mod->getError());
			}
			
		}else{
			$this->error('你没有删除权限');
		}
	}

}