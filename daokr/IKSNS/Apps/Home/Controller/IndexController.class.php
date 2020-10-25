<?php
/*
 * IKPHP 爱客开源社区 @copyright (c) 2012-3000 IKPHP All Rights Reserved 
 * @author 小麦
 * @Email:810578553@qq.com
 */
namespace Home\Controller;
use Common\Controller\FrontendController;
class IndexController extends FrontendController {
    /**
     * 网站初始化
     * @author 小麦 <810578553@vip.qq.com>
     */	
	public function _initialize() {
		parent::_initialize ();
		
		$this->user_mod = D ( 'Common/User' );
		//$this->group_topic_mod = D ( 'group/group_topics' );
		
		//文章模型
		$this->article_mod = D('Article/Article');
		$this->article_channel_mod = D('Article/ArticleChannel');
		
		//日记
		$this->note_mod = D('Space/UserNote');
		//个人空间相册推荐
		$this->photo_album_mod = D('Space/UserPhotoAlbum');
		
		//小组模型
		$this->group_mod = D ( 'Group/Group' );
	}	
    /**
     * 网站首页
     * @author 小麦 <810578553@vip.qq.com>
     */	
    public function index(){ 
		//活跃会员
		$arrHotUser = $this->user_mod->getHotUser(16);
		$this->assign ( 'arrHotUser', $arrHotUser );
		
		//统计用户数
		$count_user = $this->user_mod->count('*'); 
		$this->assign ( 'count_user', $count_user );
				
		//推荐小组10个
		$arrRecommendGroups = $this->group_mod->getRecommendGroup ( 14 );
		foreach ( $arrRecommendGroups as $key => $item ) {
			$arrRecommendGroup [] = $item;
			$arrRecommendGroup [$key] ['groupdesc'] = sub_str($item['groupdesc'],66);
		}
		$this->assign ( 'arrRecommendGroup', $arrRecommendGroup );	

		//获取推荐照片
		$arrPhoto = $this->photo_album_mod->getRecommendAlbum(4);
		$this->assign ( 'arrPhoto', $arrPhoto );
				
		//获取推荐日记
		$arrNote = $this->note_mod->getRecommendNote(14);
		$this->assign ( 'arrNote', $arrNote );
		

		//文章模块
		$articleChannel = $this->article_channel_mod->getAllChannel();
		$this->assign ( 'articleChannel', $articleChannel );
		//文章列表
		foreach($articleChannel as $key=>$item){
			$arrArticle[$key]['cname'] = $item['name'];
			$arrArticle[$key]['alist'] = $this->article_mod->getArticleByChannel($item['nameid'],5);
		}
		$this->assign ( 'arrArticle', $arrArticle );

		
		
		$this->_config_seo ();    	
		$this->display();
    }
    //风格选择
	/*public function style(){

		$ikTheme = cookie('ikTheme');
		$ikTheme = empty($ikTheme) ? 'blue' : $ikTheme;
		$arrTheme	= ikScanDir('Public/Theme');
		$this->assign('arrTheme',$arrTheme);
		$this->assign('ikTheme',$ikTheme);
		$this->_config_seo ( array (
				'title' => '更换主题风格',
				'keywords' =>'',
				'description'=>'',
		) );
		$this->display();
	}
	*/ 
    
}