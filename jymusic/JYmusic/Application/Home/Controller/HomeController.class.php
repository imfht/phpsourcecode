<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 前台公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用分组名称
 */
class HomeController extends Controller {

	/* 空操作，用于输出404页面 */
	public function _empty(){
		$this->redirect('Index/index');
	}

    protected function _initialize(){
        $config =   S('DB_CONFIG_DATA');
        if(!$config){
        	$config =   api('Config/lists');
            S('DB_CONFIG_DATA',$config);
        }
        C($config); //添加配置*/
        define('IS_ROOT', is_administrator());
        if(!IS_ROOT && !C('WEB_SITE_CLOSE')){
            $this->error(C('WEB_OFF_MSG'));
        }
        
        if (!is_login()){
	        //检测自动登录
	        $userkey = cookie('autologin');
	        if (!empty($userkey)){
	        	if ($uid = think_decrypt($userkey,C('DATA_AUTH_KEY')));
	        	//$status = D('Member')->login($uid);
	        }
    	}
        $this->meat_title = C('WEB_SITE_TITLE');
       	$this->meat_keywords = C('WEB_SITE_KEYWORD');
       	$this->meat_description = C('WEB_SITE_DESCRIPTION');
    }

	/* 用户登录检测 */
	protected function login(){
		/* 用户登录检测 */
		is_login() || $this->error('您还没有登录，请先登录！', U('User/login'));
	}
		
	/**
     * 前台音乐数据通用分页列表数据集获取方法
     * @return array|false
     * 返回数据集
     */
    protected function lists ($model,$where=array(), $order="",$field="",$status='1',$total=null){
    	//dump();
    	$where['status']= $status;  
        if(is_string($model)){
        	$model = ucfirst($model);     	
        	if('Songs' == $model ){
        		$songsList=C('SONGS_LIST_ROWS');
            	$listRows = isset($songsList) ? $songsList : 20;
            	$field = is_null($field)? $field : 'description';            	 
        	}elseif('Album' == $model){
        		$albumList=C('ALBUM_LIST_ROWS');
            	$listRows = isset($albumList) ? $albumList : 1;
            	$field = is_null($field)? $field:'company,description,sort,pub_time';
        	}elseif('Artist' == $model){
        		$singerList=C('SINGER_LIST_ROWS');
        		$listRows = isset($singerList) ? $singerList : 15;
        		$field = is_null($field)? $field:'description,sort';
        	}else{
        		$listRows = 20;
        	}       	
            $model  =   M($model);
        }
        $order = !empty($order)? $order:'id DESC';//设置排序
        $total = !empty($total)? $total:$model->where($where)->count();//获取总数
        $page = new \Think\Page($total, $listRows);
        $page->rollPage = 5;
        if($total>$listRows){
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
            $page->setConfig('first','首页');
            //$page->setConfig('last','尾页');
            $page->setConfig('prev', '上页');
        	$page->setConfig('next', '下页');
        }
        $p =$page->show();
        $this->assign('_page', $p? $p: '');
        $this->assign('_total',$total);
        $limit = $page->firstRow.','.$page->listRows;
        return $model->where($where)->field($field,true)->limit($limit)->order($order)->select();
    	
    }
}
