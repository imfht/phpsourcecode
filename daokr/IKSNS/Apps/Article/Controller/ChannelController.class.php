<?php
/*
 * IKPHP 爱客开源社区 @copyright (c) 2012-3000 IKPHP All Rights Reserved 
 * @author 小麦
 * @Email:810578553@qq.com
 * 文章频道 APP首页
 */
namespace Article\Controller;

class ChannelController extends ArticleBaseController {
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
	//频道首页
	public function index(){
		$nameid = $this->_get('nameid','trim');
		$strChannel = $this->channel_mod->where(array('nameid'=>$nameid))->find();
		!$strChannel && $this->error ( '呃...你想要的东西不在这儿' );
		// 获取分类
		$arrCate = $this->cate_mod->getAllCate($nameid);
		
		if(is_array($arrCate)){
			foreach($arrCate as $item){
				$arrCates[] = $item['cateid'];
			}
		}
		$strCates = implode(',',$arrCates);
		//查询条件 是否审核
		$map['cateid'] = array('exp',' IN ('.$strCates.') ');
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
		
		$this->assign('arrCate',$arrCate);
		$this->_config_seo ( array (
				'title' => $strChannel['name'],
				'subtitle'=> '阅读_'.C('ik_site_title'),
				'keywords' => '',
				'description'=> '',
		) );
		$this->display ();	
	}	
	
}