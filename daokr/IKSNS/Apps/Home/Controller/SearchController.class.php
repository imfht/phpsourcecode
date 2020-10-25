<?php
/*
 * IKPHP 爱客开源社区 @copyright (c) 2012-3000 IKPHP All Rights Reserved 
 * @author 小麦
 * @Email:810578553@qq.com
 * @爱客网 全站搜索 二期再搞下 现在先这样
 */
namespace Home\Controller;
use Common\Controller\FrontendController;

class SearchController extends FrontendController {
	public function _initialize() {
		parent::_initialize ();
		
		$this->group_topics_mod = D ( 'Group/GroupTopics' );
		$this->group_mod = D ( 'Group/Group' );
		$this->user_mod = D ( 'Common/User' );
	}
	public function index(){
		$type = I('get.type','all','trim');
		$kw   = I('post.q','','trim,clearText');

		$this->assign('kw',$kw);
		$this->assign('type',$type);
		$this->assign('menu',$this->getsmenu($kw));
		if(IS_POST){
			switch($type){
				case "all":

		  
					$sql = "select groupid as id,'group' as type from ".C('DB_PREFIX')."group where groupname like '%$kw%' or groupdesc like '%$kw%' and isopen=0 and isaudit=0  
							union select topicid as id,'topic' as type from ".C('DB_PREFIX')."group_topics WHERE title like '%$kw%' and isshow=0 and isaudit=0 and groupid>0 
							union select userid as id,'user' as type from ".C('DB_PREFIX')."user where username like '%$kw%' and status=0 ";
					$countnum = M('')->query($sql);
					
					//显示列表
					$pagesize = 20;
					$count = count($countnum);
					$pager = $this->_pager($count, $pagesize);

					$sql = "select groupid as id,'group' as type from ".C('DB_PREFIX')."group where groupname like '%$kw%' or groupdesc like '%$kw%' and isopen=0 and isaudit=0
					union select topicid as id,'topic' as type from ".C('DB_PREFIX')."group_topics WHERE title like '%$kw%' and isshow=0 and isaudit=0 and groupid>0
					union select userid as id,'user' as type from ".C('DB_PREFIX')."user where username like '%$kw%' and status=0  limit ".$pager->firstRow.",".$pager->listRows;
					$arrLists = M('')->query($sql);		

					foreach($arrLists as $key=>$item){
						if($item['type']=='group'){
							$arrGroup[$key] = $this->group_mod->getOneGroup($item['id']);
							$arrGroup[$key]['groupdesc'] = getsubstrutf8(clearText($arrGroup[$key]['groupdesc']),0,200);
						}elseif($item['type']=='topic'){
							$arrTopic[$key] = $this->group_topics_mod->getOneTopic($item['id']);
							$arrTopic[$key]['user'] = $this->user_mod->getOneUser($arrTopic[$key]['userid']);
							$arrTopic[$key]['content'] = getsubstrutf8(clearText($arrTopic[$key]['content']),0,200);
						}elseif($item['type']=='user'){
							$arrUser[$key] = $this->user_mod->getOneUser($item['id']);
						}
					}

					$this->assign('arrGroup',$arrGroup);
					$this->assign('arrTopic',$arrTopic);
					$this->assign('arrUser',$arrUser);
					$this->assign('total',$count);
					$this->_config_seo (array('subtitle'=>'搜索全部：'.$kw));
					$this->assign('pageUrl', $pager->show());
					$this->display('s_'.$type);
					break;
				case "group":
					//查询
					$map['isopen'] = 0; //开放公开
					$map['isaudit'] = 0;//通过审核
					$map['groupname|groupdesc'] =  array('like','%'.$kw.'%');
					//显示列表
					$pagesize = 20;
					$count = $this->group_mod->field('groupid')->where($map)->order('addtime DESC')->count('groupid');  
					$pager = $this->_pager($count, $pagesize);
					$arrGroups =  $this->group_mod->field('groupid')->where($map)->order('addtime DESC')->limit($pager->firstRow.','.$pager->listRows)->select();
					
					foreach($arrGroups as $key=>$item){
						$arrData[] = $this->group_mod->getOneGroup($item['groupid']);
					}
					foreach($arrData as $key=>$item){
						$arrGroup[] =  $item;
						$arrGroup[$key]['groupdesc'] = getsubstrutf8(clearText($item['groupdesc']),0,200);
					}
					
					$this->assign('arrGroup',$arrGroup);
					$this->assign('total',$count);
					$this->_config_seo (array('subtitle'=>'搜索小组：'.$kw));
					$this->assign('pageUrl', $pager->show());
					$this->display('s_'.$type);
					break;
				case "topic":

					//查询是否显示
					$map['ishow']  = '0';
					$map['isaudit']  = '0';
					$map['groupid'] =  array('gt',0);
					$map['title|content'] =  array('like','%'.$kw.'%');
					//显示列表
					$pagesize = 20;
					$count = $this->group_topics_mod->where($map)->order('addtime DESC')->count('topicid');
					$pager = $this->_pager($count, $pagesize);
					$arrTopics =  $this->group_topics_mod->where($map)->order('addtime DESC')->limit($pager->firstRow.','.$pager->listRows)->select();
						
					foreach($arrTopics as $key=>$item){
						$arrTopic[] = $item;
						$arrTopic[$key]['group'] = $this->group_mod->getOneGroup($item['groupid']);
						$arrTopic[$key]['user'] = $this->user_mod->getOneUser($item['userid']);
						$arrTopic[$key]['content'] = getsubstrutf8(clearText($item['content']),0,200);
					}
					
					$this->assign('arrTopic',$arrTopic);
					$this->assign('total',$count);
					$this->_config_seo (array('subtitle'=>'搜索帖子：'.$kw));
					$this->assign('pageUrl', $pager->show());
					$this->display('s_'.$type);
										
					break;
				case "user":
					//查询是否显示
					$map['isenable']  = '0';
					$map['username'] =  array('like','%'.$kw.'%');
					//显示列表
					$pagesize = 20;
					$count = $this->user_mod->field('userid')->where($map)->order('addtime DESC')->count();
					$pager = $this->_pager($count, $pagesize);
					$arrUsers =  $this->user_mod->field('userid')->where($map)->order('addtime DESC')->limit($pager->firstRow.','.$pager->listRows)->select();
					
					foreach($arrUsers as $key=>$item){
						$arrUser[] = $this->user_mod->getOneUser($item['userid']);
					}
						
					$this->assign('arrUser',$arrUser);
					$this->assign('total',$count);
					$this->_config_seo (array('subtitle'=>'搜索用户：'.$kw));
					$this->assign('pageUrl', $pager->show());
					$this->display('s_'.$type);							
					break;
			}
		}else{
			$this->display('s_'.$type);
		}
	}
	function getsmenu($kw){
		$menu = array(
					'all'   => array('url'=>U('home/search/index',array('type'=>'all')),'text'=>'全部'),
					'group' => array('url'=>U('home/search/index',array('type'=>'group')),'text'=>'小组'),
					'topic' => array('url'=>U('home/search/index',array('type'=>'topic')),'text'=>'帖子'),
					'user'  => array('url'=>U('home/search/index',array('type'=>'user')),'text'=>'用户'),
				);
		return $menu;
	}
	
}