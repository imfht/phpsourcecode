<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace User\Controller;
class HomeController extends UserController {	

    public function index(){
		$uid = $this->user['uid'];
    	if (UID && $uid != UID ) M('UserSpace')->where(array('uid'=>$uid))->setInc('hits',1); // 用户增加访问数  
    	$this->meat_title = $this->user['title'].' - '.C('WEB_SITE_TITLE');
    	$this->display();   		

    }
    
    //用户歌曲
    public function share(){
    	$uid = $this->user['uid'];
    	$map['up_uid']  = $uid ;
    	$list = $this->lists('Songs',$map,'id desc','id,name,up_uid,up_uname,artist_id,artist_name,album_id,album_name,genre_name,genre_id,listens,rater,add_time');//获取歌曲数据集
    	$this->assign('list', $list);
    	$this->meat_title = $this->user['nickname'].'的分享 - '.$this->user['title'].' - '.C('WEB_SITE_TITLE');
    	$this->assign('type', 'index');
		$this->display();
    }
    
    //用户专辑
    public function album(){
    	$uid = $this->user['uid'];
    	if ($user){
    		$list = $this->lists('Album',array('add_uid' => $id));
    		$this->assign('list',$list); 		
    		$this->assign('user',$user);
    		$this->assign('type','album');
	    	$this->meat_title = $this->user['nickname'].'的专辑 - '.$this->user['title'].' - '.C('WEB_SITE_TITLE');
	    	$this->display('User:resour');   		
    	}else{
    		$this->display('public:error');    		    		
    	}
    }
           
    //用户档案
    public function profile(){
    	$uid = $this->user['uid'];
    	if ($user){
    		$this->assign('user',$user);
    		$this->assign('type','profile');
	    	$this->meat_title = $this->user['nickname'].'个人档案 -' .$this->user['title'].' - '.C('WEB_SITE_TITLE');
	    	$this->display('User:resour');   		
    	}else{
    		$this->display('public:error');    		    		
    	}
    }
    
    //相册
    public function photo(){
    	$uid = $this->user['uid'];
    	if ($user){
    		$this->assign('user',$user);
    		$this->assign('type','photo');
	    	$this->meat_title = $this->user['nickname'].'的相册 -' .$this->user['title'].' - '.C('WEB_SITE_TITLE');
	    	$this->display('User:resour');   		
    	}else{
    		$this->display('public:error');    		    		
    	}
    }
    
   //关注
    public function follow(){
    	$uid = $this->user['uid'];
    	$map['fans_uid']  = $uid;
    	$list = $this->lists('Fans',$map,'id desc','follow_uid,follow_uname');//获取数据集
    	$this->assign('list', $list);
	    $this->meat_title = $this->user['nickname'].'的关注 - '.$this->user['title'].' - '.C('WEB_SITE_TITLE');;
	    $this->display();   		
    }
    
   //粉丝
    public function fans(){
    	$uid = $this->user['uid'];;
    	$map['follow_uid']  = $uid;
    	$list = $this->lists('Fans',$map,'id desc','fans_uid,fans_uname');//获取数据集
    	$this->assign('list', $list);
	    $this->meat_title = $this->user['nickname'].'的粉丝 - '.$this->user['title'].' - '.C('WEB_SITE_TITLE');
	    $this->display(); 
    }
    
    public function dynamic (){    		
    	$uid = $this->user['uid'];
	    $list = get_user_dynamic($uid);
	    $total        =  count($list);//获取总数
	    $listRows = 20;
        $page = new \Think\Page($total, $listRows);
        $page->rollPage = 3;
        if($total>$listRows){
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
            $page->setConfig('prev', '<');
        	$page->setConfig('next', '>');
        }
        $p =$page->show();
        $this->assign('_page', $p? $p: '');
        $this->assign('_total',$total);
        $this->assign('list',array_slice($list,$page->firstRow,$page->listRows));
        $this->meat_title = $this->user['nickname'].'的动态 - '.$this->user['title'].' - '.C('WEB_SITE_TITLE');		
		$this->display();
    }
}