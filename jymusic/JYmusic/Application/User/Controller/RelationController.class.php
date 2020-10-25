<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace User\Controller;
class RelationController extends UserController {
    /**
	* 关注用户
	*/    
    public function index(){
    	$map['fans_uid']  = UID;
    	$list = $this->lists('Fans',$map,'id desc','follow_uid,follow_uname');//获取数据集
    	$this->assign('list', $list);
    	$this->meat_title = '我的关注 - '.C('WEB_SITE_TITLE');
		$this->display();
    }
       
    /**
	* 粉丝
	*/    
    public function fans(){
    	$map['follow_uid']  = UID;
    	$list = $this->lists('Fans',$map,'id desc','fans_uid,fans_uname');//获取数据集
    	$this->assign('list', $list);
		$this->display();
    }
    
}