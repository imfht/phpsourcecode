<?php
/*******************************************************************************
* [TuziCMS] 兔子CMS
* @Copyright (C) 2014-2015  http://tuzicms.com   All rights reserved.
* @Team  Yejiao.net
* @Author: 秦大侠 QQ:176881336
* @Licence http://www.tuzicms.com/license.txt
*******************************************************************************/
namespace Manage\Controller;
use Think\Controller;
class AdminController extends CommonController {
	/**
	 * 显示后台管理首页
	 */
    public function index(){
    	//显示文章栏目
    	$m=D('Column');
    	$arr=$m->relation(true)->where("column_link=0 AND f_id=0")->order('column_sort')->select();
    	//只显示未被删除news_dell=0的数据

    	foreach($arr as $k3 => $v3){
    		$arr[$k3]['url'] = __APP__.'/'.MODULE_NAME.'/'.$v3['url'].'/'.index.'/'.'id'.'/'.$v3['id'];
    	}
//     	dump($arr);
//     	exit;
    	$this->assign('module',MODULE_NAME);
    	$this->assign('vlist',$arr);
    	
    	//显示广告分类
    	$m=D('Adnav');
    	$arr=$m->relation(true)->order('id')->select();
    	//     	    	dump($arr);
    	//     	    	exit;
    	foreach($arr as $k3 => $v3){
    		$arr[$k3]['url'] = __APP__.'/'.MODULE_NAME.'/'.Advert.'/'.index.'/'.'id'.'/'.$v3['id'];
    	}
    	//只显示未被删除news_dell=0的数据
//     	    	dump($arr);
//     	    	exit;
    	$this->assign('adlist',$arr);
    	
    	//**显示登录管理员信息
    	$id=$_SESSION['id'];
    	//dump($id);
    	//exit;
    	$m=D('Admin');
    	$arr=$m->find($id);
//     	dump($arr);
//     	exit;
    	$this->assign('v',$arr);
    	
    	//查询配置表信息，url标题显示
    	$m=D('Config');
    	$arr=$m->field('config_webname')->select();
    	$arr=$arr[0]['config_webname'];
    	$this->assign('sitename',$arr);
//     	dump($arr);
//     	exit;
    	$this->display();
    	
    }
    
    /**
     * 显示后台右边页面
     */
    public function right(){
    	//**查询admin表的数据
    	//**显示登录用户信息
    	$id=$_SESSION['id'];
//     	dump($id);
//     	exit;
    	$m=D('Admin');
    	$arr=$m->find($id);
    	//var_dump($arr);
    	$this->assign('v',$arr);
    	
    	//显示站点统计
    	$m=D('User');
    	$countUser=$m->count();// 查询满足要求的总记录数
    	$this->assign('countUser',$countUser);
    	
    	$m=D('News');
    	$countNews=$m->count();// 查询满足要求的总记录数
    	$this->assign('countNews',$countNews);
    	
    	$m=D('Guestbook ');
    	$countGuestbook=$m->count();// 查询满足要求的总记录数
    	$this->assign('countGuestbook',$countGuestbook);
    	
    	
    	$m=D('Advert ');
    	$countAdvert=$m->count();// 查询满足要求的总记录数
    	$this->assign('countAdvert',$countAdvert);
    	
    	
    	$m=D('Notice ');
    	$countNotice=$m->count();// 查询满足要求的总记录数
    	$this->assign('countNotice',$countNotice);
    	
    	//数据库大小
    	$dbtables = M()->query('SHOW TABLE STATUS');
    	$total = 0;
    	foreach ($dbtables as $k => $v) {
    		$dbtables[$k]['size'] = get_byte($v['data_length'] + $v['index_length']);
    		$total+=$v['data_length'] + $v['index_length'];
    	}
    	$this->assign('total', get_byte($total));
    	
    	$this->display();
    }
    
    /**
     * login_out方法
     * 后台管理员退出
     */
    public function login_out(){
    	session_start();
    	session_unset();//删除所有session变量
    	session_destroy();//删除session文件
    	$this->success('成功退出',U('login/index'));
    	
    }
    
    /**
     * 模板实例
     */
    public function code(){
    	//header("Content-Type:text/html; charset=utf-8");
    	$this->display();
    }
    
}