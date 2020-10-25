<?php
/*
 * IKPHP 爱客开源社区 @copyright (c) 2012-3000 IKPHP All Rights Reserved 
 * @author 小麦
 * @Email:810578553@qq.com
 * @小组应用 发现
 */
namespace Group\Controller;

class ExploreController extends GroupBaseController {

	public function _initialize() {
			parent::_initialize ();
		// 访问者控制
		$this->userid = $this->visitor ['userid']>0 ? $this->visitor ['userid'] : 0;
		//应用所需 mod
		$this->_mod = D ( 'Group' );
		$this->user_mod = D ( 'Common/User' );

		$this->tag_mod = D('Common/Tag');

		
		$this->group_topics_mod = D ( 'group_topics' );
		$this->cate_mod = D ( 'GroupCate' );
	}
	
	// 发现小组
	public function groups(){
		$tag = $this->_get('tag', 'trim,urldecode','');
		if(!empty($tag)){ 
			$strTag = $this->tag_mod->getOneTagByName($tag);
			//查询
			$map = array('tagid'=>$strTag['tagid']); 
			$arrGroupid = M('tag_group_index')->field('groupid')->where($map)->order('groupid DESC')->select();
			foreach ($arrGroupid as $item){
				$groupid[] = $item['groupid']; 
			}
			$groupid = implode(',',$groupid);
			$where['groupid'] = array('exp',' IN ('.$groupid.') ');
			//显示列表
			$pagesize = 40;
			$count = $this->_mod->where($where)->order('isrecommend DESC')->count('groupid');
			$pager = $this->_pager($count, $pagesize);
			$arrGroups =  $this->_mod->where($where)->order('isrecommend DESC')->limit($pager->firstRow.','.$pager->listRows)->select();
			
			$this->_config_seo ( array (
					'title' => $tag.'相关的小组',
					'subtitle'=> '小组_'.C('ik_site_title'),
					'keywords' => '',
					'description'=> '',
			) );
		}else{
			//查询
			$map['isopen'] = 0; //开放公开
			$map['isaudit'] = 0;//通过审核
			//显示列表
			$pagesize = 40;
			$count = $this->_mod->where($map)->order('isrecommend DESC')->count('groupid');
			$pager = $this->_pager($count, $pagesize);
			$arrGroups =  $this->_mod->where($map)->order('isrecommend DESC')->limit($pager->firstRow.','.$pager->listRows)->select();
		
			$this->_config_seo ( array (
					'title' => '发现小组',
					'subtitle'=> '小组_'.C('ik_site_title'),
					'keywords' => '官方小组,科技,智趣,生活,情感,开源,APP下载,案例展示,二次开发,艺术,闲聊,情趣,兴趣,风格模板,php讨论',
					'description'=> '',
			) );
		}
		
		foreach($arrGroups as $key=>$item){
			$arrData[] = $this->_mod->getOneGroup($item['groupid']);
		}
		foreach($arrData as $key=>$item){
			$exploreGroup[] =  $item;
			$exploreGroup[$key]['groupname'] = sub_str($item[groupname], 14);
			$exploreGroup[$key]['groupdesc'] = sub_str($item['groupdesc'], 45);
			if($this->userid > 0){
				$exploreGroup[$key]['isGroupUser'] = $this->_mod->isGroupUser ( $this->userid, $item['groupid'] );
			}
		}

		//小组分类
		$this->groupcate();
		
		$this->assign('pageUrl', $pager->show());
		$this->assign('list', $exploreGroup);
		
		$this->display ();

	}
	//小组分类
	public function groupcate(){
		$arrParentCate = $this->cate_mod->getParentCate();
		foreach($arrParentCate as $key=>$v){
			$arrGroupCate[$key]['pcate'] = $v; 
			$arrGroupCate[$key]['childCate'] = $this->cate_mod->getReferCate($v['cateid']);
		}
		$this->assign('arrGroupCate', $arrGroupCate);
	}

	// 发现话题
	public function topics(){
		$tag = $this->_get('tag', 'trim,urldecode','');
		if(!empty($tag)){
			$strTag = $this->tag_mod->getOneTagByName($tag);
			//查询
			$map = array('tagid'=>$strTag['tagid']);
			$arrID = M('tag_topic_index')->field('topicid')->where($map)->order('topicid DESC')->select();
			foreach ($arrID as $item){
				$topicid[] = $item['topicid'];
			}
			$topicid = implode(',',$topicid);
			$where['topicid'] = array('exp',' IN ('.$topicid.') ');
			//显示列表
			$pagesize = 40;
			$count = $this->group_topics_mod->where($where)->order('addtime DESC')->count('topicid');
			$pager = $this->_pager($count, $pagesize);
			$arrTopics =  $this->group_topics_mod->where($where)->order('addtime DESC')->limit($pager->firstRow.','.$pager->listRows)->select();
			

			$this->_config_seo ( array (
					'title' => $tag.'相关的话题',
					'subtitle'=> '小组_'.C('ik_site_title'),
					'keywords' => '',
					'description'=> '',
			) );
		}else{
			//查询是否显示
			$map['ishow']  = '0';
			$map['isaudit']  = '0';
			$map['groupid'] =  array('gt',0);
			//显示列表
			$pagesize = 20;
			$count = $this->group_topics_mod->where($map)->order('addtime DESC')->count('topicid');
			$pager = $this->_pager($count, $pagesize);
			$arrTopics =  $this->group_topics_mod->where($map)->order('addtime DESC')->limit($pager->firstRow.','.$pager->listRows)->select();
	
			$this->_config_seo ( array (
					'title' => '发现话题',
					'subtitle'=> '小组_'.C('ik_site_title'),
					'keywords' => '',
					'description'=> '',
			) );
		}

		foreach($arrTopics as $key=>$item){
			$list[] = $item;
			$list[$key]['content'] = ikhtml_text('topicd', $item['topicid'], $item['content']);
			$list[$key]['group'] = $this->_mod->getOneGroup($item['groupid']);
		}

		//小组分类
		$this->groupcate();
		
		$this->assign('pageUrl', $pager->show());
		$this->assign('list', $list);
		$this->display ();		
	}	
}