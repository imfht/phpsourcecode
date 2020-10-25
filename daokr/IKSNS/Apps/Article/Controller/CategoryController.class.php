<?php
/*
 * IKPHP 爱客开源社区 @copyright (c) 2012-3000 IKPHP All Rights Reserved 
 * @author 小麦
 * @Email:810578553@qq.com
 * 文章频道 APP首页
 */
namespace Article\Controller;

class CategoryController extends ArticleBaseController {
	public function _initialize() {
		parent::_initialize ();
		if (is_login()) {
			$this->userid = $this->visitor['userid'];
		}
		//应用所需 mod
		$this->mod      	= D ( 'Article' );
		$this->cate_mod 	= D ( 'ArticleCate' );
		$this->item_mod 	= M ( 'ArticleItem' );
		$this->channel_mod  = D ( 'ArticleChannel' );
		$this->user_mod     = D ( 'Common/User' );

	}
	//分类首页
	public function index(){
		$cateid = $this->_get('cateid','intval');
		$strCate = $this->cate_mod->getOneCate($cateid);
		!$strCate && $this->error ( '呃...你想要的东西不在这儿' );
		$strChannel = $this->channel_mod->where(array('nameid'=>$strCate['nameid']))->find();
		// 获取分类
		$arrCate = $this->cate_mod->getAllCate($strCate['nameid']);
		
		//查询条件 是否审核
		$map['cateid'] = $cateid;
		$map['isaudit'] = 0;
		//显示列表
		$pagesize = 30;
		$count = $this->item_mod->where($map)->order('istop desc,orderid desc')->count('itemid');
		$pager = $this->_pager($count, $pagesize);
		$arrItemid =  $this->item_mod->field('itemid')->where($map)->order('istop desc,orderid desc')->limit($pager->firstRow.','.$pager->listRows)->select();
		foreach($arrItemid as $key=>$item){
			$arrArticle [] = $this->mod->getOneArticle($item['itemid']);
		}
		$this->assign('pageUrl', $pager->show());
		$this->assign ( 'arrArticle', $arrArticle );
		///////////////////////////////
				

		$this->_config_seo ( array (
				'title' => $strChannel['name'].'-'.$strCate['catename'],
				'subtitle'=> '阅读_'.C('ik_site_title'),
				'keywords' => '',
				'description'=> '',
		) );
		$this->assign ( 'arrCate', $arrCate );
		$this->display ();
		
	}	
	
}