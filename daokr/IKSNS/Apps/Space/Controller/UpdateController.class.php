<?php
/*
 * IKPHP爱客网 安装程序 @copyright (c) 2012-3000 IKPHP All Rights Reserved @author 小麦
 * 邮箱:ikphp@sina.cn 微博 空间动态广播
 * 开发时间 2014.3.28 作者：小麦
 */
namespace Space\Controller;

class UpdateController extends SpaceBaseController {
	public function _initialize() {
		parent::_initialize ();
		if(!is_login()) {
			$this->redirect ( 'home/user/login' );
		}else{
			$this->userid = $this->visitor['userid'];
		}
		//应用所需 mod
		$this->user_mod = D('Common/User');
		$this->feed_mod = D('Feed');
		$this->ftopic_mod = D('FeedTopic');
		$this->feed_img = M('FeedImages');
		
		$this->user_follow = M('user_follow');
	}
	//动态广播首页
	public function index(){
		if(!is_login() ) $this->redirect ( 'home/user/login' );
		$userid =  $this->visitor['userid'];
		$user = $this->user_mod->getOneUser($userid);
		
		//获取feed
		$arrUserid = $this->user_follow->field('userid_follow')->where(array('userid'=>$userid))->order('addtime desc')->select();
		$user_floows = $userid;
		foreach ($arrUserid as $uid){
			$user_floows .= ','.$uid['userid_follow'];
		}

		//查询条件 是否审核 是否已经删除
		$map['isaudit'] = 1;
		$map['is_del'] = 0;
		$map['userid'] = array('exp',' IN ('.$user_floows.') ');

		//显示列表
		$pagesize = 30;
		$count = $this->feed_mod->field('feedid')->where($map)->order('addtime desc')->count('feedid'); 
		$pager = $this->_pager($count, $pagesize);
		$arrItemid =  $this->feed_mod->where($map)->order('addtime desc')->limit($pager->firstRow.','.$pager->listRows)->select();
		foreach($arrItemid as $k=>$item){
			$arrFeed[] = $item;
			$strData = M('feed_data')->where(array('feedid'=>$item['feedid']))->find();
			$feeddata = unserialize(stripslashes($strData['feeddata']));
			if(is_array($feeddata)){
				foreach($feeddata as $key=>$itemTmp){
					$tmpkey = '{'.$key.'}'; 
					if($tmpkey == '{comment}'){
						$tmpdata[$tmpkey] = replaceTheme($itemTmp);	
					}else{
						$tmpdata[$tmpkey] = $itemTmp;	
					}
									
				}
			}
			$arrFeed[$k]['user'] = $this->user_mod->getOneUser($item['userid']);
			$arrFeed[$k]['content'] = strtr($strData['template'],$tmpdata);
		}

		$this->assign('pageUrl', $pager->show());
		$this->assign ( 'arrFeed', $arrFeed );
		
		//他关注的用户
		$strUser['followUser'] = $this->user_mod->getfollow_user($userid, 8);
		
		$this->assign('strUser',$strUser);
		
		$this->_config_seo ( array (
				'title' => '我的动态广播',
				'keywords' => '分享日记,分享动态信息,日记,宝贝,照片,最新动态',
				'description'=> '把生活中的点点滴滴都记录下来吧；提供图书、电影、音乐唱片的推荐、评论和价格比较，以及城市独特的文化生活！',
		) );		
		$this->display();
	}
	//发布
	public function publish(){
		if(!is_login() ) $this->redirect ( 'home/user/login' );
		$type = I('post.type','topic','trim');// 默认是说说
		$userid =  $this->visitor['userid'];
		$username = $this->visitor['username'];
		$doname = $this->visitor['doname'];
		$share_link = $this->_post('share_link'); // 分享链接
		$share_name = $this->_post('share_name'); // 名称
		$photo_name = $this->_post('photo_name');
		$comment = $this->_post('comment'); // 150个字最多
		
		if(mb_strlen($comment,'utf8')>150){
			$this->error('输入的广播字数太多了；请不要超过150个字！');
		}
		
		$comment = parse_comment($comment);
		preg_match_all("/#([^#]*[^#^\s][^#]*)#/is",$comment,$arr);
		$arr = array_unique($arr[1]);
		
		//判断是有话题 添加话题
		if(!empty($arr)){
			foreach ($arr as $v){
				$dataTopic = array('topicname'=>clearText($v));
				$topicid[] = $this->ftopic_mod->addTopic($dataTopic);
			}
			$topicid = implode(',', $topicid);
		}
		
		//feed数据
		$dataFeed = array(
				'userid' => $userid,
				'type'	 => 'post',
				'share_link' => $share_link,
				'share_name' => $share_name,
				'topicid' => $topicid,
				);
		//$dataTpl
		$spaceUrl = U('space/index/index',array('id'=>$doname));
		$dataTpl = '<p class="text">{userinfo}{actname}{actinfo}</p><blockquote><p>{comment}</p></blockquote><div class="attachments">{attachments}</div>';
		
		if($type=='topic'){
			//随便说
			$tplData['actname'] = ' 说：';
			$tplData['actinfo'] = '';
			$tplData['userinfo'] = '<a href="'.$spaceUrl.'">'.$username.'</a>';
			$tplData['comment'] = htmlspecialchars($comment);
			
			$feedid = $this->feed_mod->addFeed($dataFeed);
			//有图片则更新图片
			if($feedid && !empty($photo_name)){
				foreach ($photo_name as $item){
					$path = 'feed/photo/'.$userid.'/';
					$dataImg = array('userid'=>$userid,'feedid'=>$feedid,'name'=>base64_decode($item),'path'=>$path);
					$this->feed_img->add($dataImg);
					//附件
					$ext =  explode ( '.', base64_decode($item));
					//图片大小
					$simg =  attach($path.$ext[0].'_130_130.jpg');
					$bimg =  attach($path.$ext[0].'_500_500.jpg');
					//附件图片
					$strimgs .= '<a class="upload-pic-wrapper" href="javascript:;"><img class="upload-pic big" src="'.$simg.'"  small-src="'.$simg.'" data-src="'.$bimg.'"></a>';
				}
				$this->feed_mod->where(array('feedid'=>$feedid))->setField('is_image', '1');
			}
			//模版附件
			$tplData['attachments'] = $strimgs;
						
		}elseif($type=='sharesite'){
			//分享网站
			$tplData['actname'] = ' 推荐网址  ';
			$tplData['actinfo'] = '<a href="'.$share_link.'" target="_top">'.$share_name.'</a>';
			$tplData['userinfo'] = '<a href="'.$spaceUrl.'">'.$username.'</a>';
			$tplData['comment'] = htmlspecialchars($comment);
			$tplData['attachments'] = '';
			
			$feedid = $this->feed_mod->addFeed($dataFeed);
		}
		
		//添加模版数据
		$tplData = array_merge($dataFeed,$tplData);
		$this->feed_mod->addFeedData($feedid,$dataTpl, $tplData);
		$this->redirect('space/update/index');
	}
	public function uploadimg(){ 
		$userid =  $this->visitor['userid'];
		$image = $_FILES ['image'];
		if(!empty($image['name']) && $userid){
			//传image
			/* $result = savelocalfile($image,'feed/photo/'.$userid,
					array('width'=>'130,500','height'=>'130,500'),
					array('jpg','jpeg','png','gif'));
			*/		

			$result = \Common\Util\Upload::saveLocalFile(
				'feed/photo/'.$userid.'/', 
				array('width'=>'130,500','height'=>'130,500')
			);					
			if (!$result ['error']) {
				$arrJson = array(
						'photo_url'=>  attach($result['img_130_130']),
						'photo_name'=> base64_encode($result['savename']),
				);
				echo json_encode(array('r'=>1,'html'=>$arrJson));
				return ;
			}else{
				$arrJson = array('r'=>0, 'html'=> $result ['error']);
				echo json_encode($arrJson);
				return ;
			}			
		}
	}
	//ajax删除个人话题
	public function delete(){
		$userid =  $this->visitor['userid'];
		$feedid = $this->_post('feedid','trim,intval');
		$strFeed = $this->feed_mod->getOneFeed($feedid,$userid);
		if($strFeed){
			$res = $this->feed_mod->deleteFeed($feedid);
			if($res){
				$arrJson = array('r'=>1, 'html'=> 'ok');
				echo json_encode($arrJson);
				return ;
			}else{
				$arrJson = array('r'=>0, 'html'=> 'error');
				echo json_encode($arrJson);
				return ;
			}	
		}
	}
	//话题页面
	public function topic(){
		$name = $this->_get('name','trim');
		$strTopic = $this->ftopic_mod->getOneTopic(array('topicname'=>$name));
		if($strTopic){
			
		}else{
			$this->error('您所查看的话题不存在！');
		}
	}	
	//抓取网站
	public function sharesite(){
		$url = I('post.url','0','trim');
		$userid =  $this->visitor['userid'];
		if(!empty($url) && $userid){
			
			$strdesc = $this->geturlfile($url);
			preg_match("/\<title\>([\s\S]*)\<\/title\>/is", $strdesc, $arr);
			
			if($arr[1]){
				$arrSite = array('title'=>$arr[1],'url'=>$url);
				echo json_encode(array('r'=>1,'html'=>$arrSite));
				return ;
			}else {
				echo json_encode(array('r'=>0,'html'=>''));
				return ;				
			}

		}
	}
	//抓取地址解析
	function geturlfile($url, $encode = 1, $charset='UTF-8') {
		$text = '';
		if (! empty ( $url )) {
			if (function_exists ( 'file_get_contents' )) {
				@$text = file_get_contents ( $url );
				
			} else {
				@$carr = file ( $url );
				if (! empty ( $carr ) && is_array ( $carr )) {
					$text = implode ( '', $carr );
				}
			}
		}
		$text = str_replace ( '·', '', $text );
		if (! empty ( $charset ) && $encode == 1) {
			if (function_exists ( 'iconv' )) {
				$text = iconv ( $charset, C ( 'ik_charset' ), $text );
			} else {
				$text = encodeconvert ( $charset, $text );
			}
		}
		return $text;
	}

	
	
}