<?php
/*
 * IKPHP 爱客开源社区 @copyright (c) 2012-3000 IKPHP All Rights Reserved 
 * @author 小麦
 * @Email:810578553@qq.com
 */
namespace Common\Controller;

class FrontendController extends BaseController {
	
	protected $visitor = null;
    /**
     * 网站前端初始化
     * @author 小麦 <810578553@vip.qq.com>
     */	
    public function _initialize() {
        parent::_initialize();
        //网站状态
        //初始化访问者
        $this->_init_visitor();
        $this->app_mod = D('Common/App');
        //第三方登陆模块
        //$this->_assign_oauth();
        //网站导航选中
        //$this->assign('nav_curr', '');
        //判断是否已被卸载
        //$this->_isuninstall($this->app_name);
        //网站导航
        $this->assign('topNav',$this->_topnav());
        $this->assign('arrNav',$this->_nav($this->app_name));
        $this->assign('logo',$this->_navlogo($this->app_name));
    }
    /**
     * 初始化访问者
     */
    private function _init_visitor() {
    	if(is_login()){
    		$this->visitor = $user_auth = session("user_auth");
    		$count_msg_unread = D('Common/Message')->where(array('touserid'=>$user_auth['userid'],'isread'=>0,'isinbox'=>0))->count();
    		$count_new_msg = $count_msg_unread>0 ? $count_msg_unread : 0;
    		$this->assign('count_new_msg', $count_new_msg);
    		$this->assign('visitor', $this->visitor);
    	}
    	//$this->assign('count_online_user', $this->visitor->getOnlineUserCount());
    	$this->assign('count_online_user', rand(1000,9999));
    }
    /**
     * SEO设置
     */
    protected function _config_seo($seo_info = array(), $data = array()) {
    	$page_seo = array(
    			'title' => C('ik_site_title'),
    			'subtitle' => C('ik_site_subtitle'),
    			'keywords' => C('ik_site_keywords'),
    			'description' => C('ik_site_desc')
    	);
    	$page_seo = array_merge($page_seo, $seo_info);
    	//开始替换
    	$searchs = array('{site_name}', '{site_title}', '{site_keywords}', '{site_desc');
    	$replaces = array(C('ik_site_title'), C('ik_site_subtitle'), C('ik_site_keywords'), C('ik_site_desc'));
    	preg_match_all("/\{([a-z0-9_-]+?)\}/", implode(' ', array_values($page_seo)), $pageparams);
    	if ($pageparams) {
    		foreach ($pageparams[1] as $var) {
    			$searchs[] = '{' . $var . '}';
    			$replaces[] = $data[$var] ? strip_tags($data[$var]) : '';
    		}
    		//符号
    		$searchspace = array('((\s*\-\s*)+)', '((\s*\,\s*)+)', '((\s*\|\s*)+)', '((\s*\t\s*)+)', '((\s*_\s*)+)');
    		$replacespace = array('-', ',', '|', ' ', '_');
    		foreach ($page_seo as $key => $val) {
    			$page_seo[$key] = trim(preg_replace($searchspace, $replacespace, str_replace($searchs, $replaces, $val)), ' ,-|_');
    		}
    	}
    	$this->assign('seo', $page_seo);
    }
    /**
     * 前台分页统一
     */
    protected function _pager($count, $pagesize) {
    	$pager = new \Think\Page($count, $pagesize);
    	$pager->rollPage = 5;
    	$pager->setConfig('prev', '<前页');
    	$pager->setConfig('next', '后页>');
    	$pager->setConfig('theme', '%UP_PAGE% %FIRST% %LINK_PAGE% %END% %DOWN_PAGE%');
    	return $pager;
    } 
    // 顶部次导航
    protected  function _topnav(){
    	$arrNav = array ();
		$arrNav['index'] = array('name'=>'首页', 'url'=>C('ik_site_url'));
		$arrApp = $this->app_mod->field('app_name,app_alias,app_entry')->where(array('status'=>'1'))->order(array('display_order asc'))->select();
		foreach($arrApp as $item){
			if(empty($item['app_entry'])){
				$item['app_entry'] = 'index/index';
			}
			$arrNav[$item['app_name']] = array('name'=>$item['app_alias'], 'url'=>U($item['app_name'].'/'.$item['app_entry']));
		}
    	return $arrNav; 	
    }
	// 网站主导航
	protected  function _nav($app_name){
		if (! empty ( $app_name ) && $app_name == 'home') {
			$arrNav = array ();
			$arrNav['index'] = array('name'=>'首页', 'url'=>C('ik_site_url'));
			$arrApp = $this->app_mod->field('app_name,app_alias,app_entry')->where(array('status'=>'1'))->order(array('display_order asc'))->select();
			foreach($arrApp as $item){
				if(empty($item['app_entry'])){
					$item['app_entry'] = 'index/index';
				}
				$arrNav[$item['app_name']] = array('name'=>$item['app_alias'], 'url'=>U($item['app_name'].'/'.$item['app_entry']));
			}
			return $arrNav;
		}		
	}
	// 导航logo
	protected  function _navlogo($app_name){
		if (! empty ( $app_name )) {
			$arrLogo = array ();
			$strApp = $this->app_mod->where(array('app_name'=>$app_name))->find();
			if($strApp){
				$arrLogo = array('name'=>$strApp['app_alias'], 'url'=>U($app_name.'/'.$strApp['app_entry']), 'style'=>'site_logo nav_logo');
			}else{
				$arrLogo = array('name'=>'爱客开源', 'url'=>C('ik_site_url'), 'style'=>'site_logo');
			}
			return $arrLogo;
		}
	}
	// 判断应用是否已被卸载
	protected  function _isuninstall($app_name){
		if (! empty ( $app_name ) && !in_array($app_name, C('DEFAULT_APPS'))) {
			$strApp = $this->app_mod->where(array('app_name'=>$app_name,'status'=>'1'))->find();
			if(!$strApp){
				$this->error('厄，该应用不存在哦！或已被禁用！');
			}
		}
	}
	
	/*
	* 组装公共评论
	* @param $typeid app应用id
	* @param $type app应用model 对应数据表模型
	* @param $userid 评论者userid
	*/ 
    protected function _buildComment($typeid, $type, $userid, $callback_url){ 
    	$comment_mod = D('Common/Comment');
		//组合公共评论所需数组
        $call_str = array();
		$strObj = array();
		$call_str['cb_url'] = $strObj['cb_url'] = $callback_url;
		$call_str['type'] = $strObj['type'] = $type;
		$call_str['typeid'] = $strObj['typeid'] = $typeid;
		//获取评论
		$page = $strObj['page'] = I('get.p', 1, 'intval'); 
		$sc = $strObj['sc'] 	  = I('get.sc', 'asc', 'trim');
		$isauthor = $strObj['isauthor'] =  I('get.isauthor', 0, 'trim');
		
        $call_str['page'] = $page;
		//查询条件 是否显示
		$map['typeid'] = $typeid;
		$map['type'] = $type;
		if($isauthor){
			$map['userid']  = $userid;
			$strObj['author'] = array('isauthor'=>0,'text'=>'查看所有回应');
		}else{
			$strObj['author'] = array('isauthor'=>1,'text'=>'只看楼主');
		}
		//显示列表
		$pagesize = 30;
		$count = $strObj['count_comment'] = $comment_mod->where($map)->order('addtime '.$sc)->count();
		$pager = $this->_pager($count, $pagesize);
		$arrComment =  $comment_mod->where($map)->order('addtime '.$sc)->limit($pager->firstRow.','.$pager->listRows)->select();
		foreach($arrComment as $key=>$item){
			$commentList[] = $item;
			$commentList[$key]['user'] = D('Common/User')->getOneUser($item['userid']);
			$commentList[$key]['content'] = hview($item['content']);
			if($item['referid']>0){
				$recomment = $comment_mod->recomment($item['referid']);
				$commentList[$key]['recomment'] = $recomment;
			}
		}
        //回调变量
        $ik_commment = base64_encode(serialize($call_str));
        
		$this->assign('pageUrl', $pager->show());
		$this->assign('commentList', $commentList);
		$this->assign ( 'strObj', $strObj );
		$this->assign ( 'ik_comment', $ik_commment );
		$this->assign ( 'page', $page );
		//评论list结束	
	}
    
}