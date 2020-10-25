<?php
/*******************************************************************************
* [TuziCMS] 兔子CMS
* @Copyright (C) 2014-2015  http://tuzicms.com   All rights reserved.
* @Team  Yejiao.net
* @Author: 秦大侠 QQ:176881336
* @Licence http://www.tuzicms.com/license.txt
*******************************************************************************/
namespace Mobile\Controller;
use Think\Controller;
use Common\Lib\String; //引入类函数
use Common\Lib\Category; //引入类函数
use Common\Lib\Common; //引入类函数
class SearchController extends CommonController {
	/**
	 * 搜索首页
	 */
    public function index(){
    	$this->display();
    }
    
    /**
     * 查询数据表单处理类文件
     */
    public function result(){
    	//判断存在id
    	if ($id==null){
    		$this->assign('ifid',not);
    	}
    	$keyword=I('get.keyword');
//     	dump($keyword);
//     	exit;
    	if ($keyword==null){
    		$this->error('请输入搜索关键字！');
    	}
    	 
    	$m=D('News');
    	$data['news_title']=array('like',"%{$keyword}%");
    	$field='g.id,g.news_title,g.news_content,g.news_hits,g.news_author,g.news_addtime,g.news_dell,i.column_name,r.model_table';
    	$i=$m->alias('g')->join('LEFT JOIN tuzi_column i ON i.id = g.nv_id')->join('LEFT JOIN tuzi_model r ON r.id = i.column_type')->field($field)->where($data)->select();
        
        if ($i['0']['news_dell']==1){
    		$this->error('该文章已经被删除','__APP__');
    	}
//     	dump($i);
//     	exit;

    	//计算总共多少条记录
    	$num=count($i);
    	$this->assign('num',$num);// 赋值分页输出
    	//获取搜索关键词
    	$keyword=I('get.keyword');
    	$this->assign('keyword',$keyword);// 赋值分页输出
    	
    	$m=D('News');
    	$data['news_title']=array('like',"%{$keyword}%");
    	
    	//**分页实现代码
    	$count = count($i);// 查询满足要求的总记录数
    	$Page = new \Think\Page($count,6);// 实例化分页类 传入总记录数和每页显示的记录数(25)
    	$show = $Page->show();// 分页显示输出
    	//**分页实现代码
    	
    	$m=D('News');
    	$data['news_title']=array('like',"%{$keyword}%");
    	$field='g.id,g.news_title,g.news_content,g.news_hits,g.news_author,g.news_addtime,i.column_name,r.model_table';
    	$i=$m->alias('g')->join('LEFT JOIN tuzi_column i ON i.id = g.nv_id')->join('LEFT JOIN tuzi_model r ON r.id = i.column_type')->field($field)->where($data)->where('news_dell=0')->limit($Page->firstRow.','.$Page->listRows)->select();
    	//dump($i);
    	//exit;
    	
    	//循环过滤html src标签和截取中文函数  substr_ext函数写在commonaction.class.php中
    	foreach($i as $k2 => $v2){
    		$i[$k2]['news_title'] = $this->substr_ext($v2['news_title'], 0, 40, 'utf-8',"");
    	}
    	
    	//循环过滤html src标签和截取中文函数  substr_ext函数写在commonaction.class.php中
    	foreach($i as $k2 => $v2){
    		$i[$k2]['news_content'] = $this->substr_ext($v2['news_content'], 0, 190, 'utf-8',"");
    	}
    	
    	//二级导航栏目的url，可根据手机站或pc站自动适配url
    	$modlu=__ACTION__ ;
    	strpos($modlu, "mobile");
    	if (strpos($modlu, "mobile")==''){//如果url中不存在mobile（不区分大小写）
	    	//文章url
	    	foreach($i as $k3 => $v3){
	    		$i[$k3]['url'] = __APP__.'/'.$v3['model_table'].'/'.detail.'/'.'id'.'/'.$v3['id'];
	    	}
    	}else {
    		//文章url
    		foreach($i as $k3 => $v3){
    			$i[$k3]['url'] = __APP__.'/'.'mobile'.'/'.$v3['model_table'].'/'.detail.'/'.'id'.'/'.$v3['id'];
    		}
    	}
    	
    	
//     	dump($i);
//     	exit;
    	
    	if ($i==null){
    		$this->error('不存在该信息');
    
    	}else {
    		//**分页实现代码
    		$this->assign('page',$show);// 赋值分页输出
    		//**分页实现代码
    		$this->assign('vlist',$i); //在新查询到的数据再分配给前台模板显示
    		$this->display('result'); //指定模板
    	}
    
    }

}
