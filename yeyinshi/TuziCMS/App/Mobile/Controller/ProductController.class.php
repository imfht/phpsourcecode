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
class ProductController extends CommonController {
	/**
	 * 产品模型控制器方法
	 */
	public function group(){
		//**字符截取函数
		import('Class.String',APP_PATH);//文件在当前项目目录下的class目录
		$id=I('get.id');//类别ID
		if (!preg_match('/^\d+$/i', $id)){
			$this->error('url参数错误');
			exit;//仿制用户恶意输出url参数
		}
		//dump($id);
		//exit;
			
		$m=D('Column')->order('column_sort ASC')->relation(true)->find($id);
		//获取当前栏目的名称
		$column_name=$m['column_name'];
		$this->assign('vcolumn',$column_name);
// 		dump($m);
// 		exit;
		if ($m['f_id']==0){
			//**获取栏目的下级所有子栏目
			$m=D('Column')->order('column_sort ASC')->relation(true)->select();
			$m=Category::getChilds($m,$id);//获取id所有的下级栏目的信息
		}else {
			
			//**获取栏目的上级所有子栏目
			$m=D('Column')->order('column_sort ASC')->relation(true)->select();
			//$m=Category::getChilds($m,$id);//获取id所有的下级栏目的信息
			$m=Category::getParents($m,$id);//获取id所有的下级栏目的信息
			
			//**获取栏目父级id,并且查询下级所有子栏目
			$cid=$m['0']['id'];
			$m=D('Column')->order('column_sort ASC')->relation(true)->select();
			$m=Category::getChilds($m,$cid);//获取id所有的下级栏目的信息
		}

// 		dump($m);
// 		exit;

		//二级导航栏目的url，可根据手机站或pc站自动适配url
		$modlu=__ACTION__ ;
		strpos($modlu, "mobile");
		if (strpos($modlu, "mobile")==''){//如果url中不存在mobile（不区分大小写）
			//栏目url
			foreach($m as $k3 => $v3){
				$m[$k3]['url'] = __APP__.'/'.$v3['url'].'/'.group.'/'.'id'.'/'.$v3['id'];
			}
		}else {
			//栏目url
			foreach($m as $k3 => $v3){
				$m[$k3]['url'] = __APP__.'/'.'mobile'.'/'.$v3['url'].'/'.group.'/'.'id'.'/'.$v3['id'];
			}
		}
		$this->assign('navlist',$m);
		//var_dump($m);
		//exit;
				
		//**获取子栏目的上级栏目
		import('Class.Category',APP_PATH);//文件在当前项目目录下的class目录
		$m=D('Column')->order('column_sort ASC')->relation(true)->select();
		$m=Category::getParents($m,$id);//获取nv_id所有的上级栏目的信息
		
		//全局导航栏目的url，可根据手机站或者pc站石洞适配url
		$modlu=__ACTION__ ;
		strpos($modlu, "mobile");
		if (strpos($modlu, "mobile")==''){//如果url中不存在mobile（不区分大小写）
			//栏目url
			foreach($m as $k3 => $v3){
				$m[$k3]['url'] = __APP__.'/'.$v3['url'].'/'.group.'/'.'id'.'/'.$v3['id'];
			}
		}else {
			//栏目url
			foreach($m as $k3 => $v3){
				$m[$k3]['url'] = __APP__.'/'.'mobile'.'/'.$v3['url'].'/'.group.'/'.'id'.'/'.$v3['id'];
			}
		}
		//var_dump($m);
		//exit;
		$this->assign('topnavlist',$m);
		
		//**全局导航 判断设置最后一个没有尖括号
		$last=count($m)-1;
		$this->assign('last',$last);
		//echo $last;
		//exit;
			
		//**获取当前栏目的信息
		$topcate=D('Column')->where("id=$id")->order('column_sort')->relation(true)->select();
		$this->assign('nav_list',$topcate);	
		//dump($topcate);
		//exit;
		if ($topcate==null){
			$this->error('参数错误');
		}
		$this->assign('blist',$topcate);
		
		//****SEO信息
		$title=$topcate[0]['column_name'];
		$m=M('Config');
		$data=$m->field('config_webname')->find();
		//dump($data);
		//exit;
		$title=$title.' - '.$data['config_webname'];
		//dump($title);
		//exit;
		$keywords=$topcate[0]['column_keyw'];
		$description=$topcate[0]['column_descr'];
		$this->assign('title',$title);
		$this->assign('keywords',$keywords);
		$this->assign('description',$description);
		//dump($title);
		//exit;
	
		//**获取所有栏目的信息
		import('Class.Category', APP_PATH);
		$m=D('Column')->order('column_sort')->relation(true)->select();//查询所有栏目的信息
		//dump($m);
		//exit;
			
		//****查询指定id的栏目下的所有子栏目文章
		foreach ($topcate as $k => $v){
			$cids=Category::getChildsId($m, $v['id']);//传递一个父级分类ID返回所有子分类ID
			$cids[]=$v['id'];//将父级id也压进来赋值给$cids
			//dump($cids);
			//exit;
			$where=array('nv_id'=>array('IN', $cids));//查询新闻表nv_id字段和$cids相等时的数据
			$topcate[$k]['news']=D('News')->where($where)->where("news_dell=0")->relation(true)->select();
			$result=$topcate[$k]['news'];
			//查询新闻表下的所有文章   查询新闻数据赋值给字段news
			
		    //**分页实现代码
    		$count = count($result);// 查询满足要求的总记录数
    		$Page = new \Think\Page($count,C('PAGE_PRODUCT__HOME'));// 实例化分页类 传入总记录数和每页显示的记录数(25)
    		$show = $Page->show();// 分页显示输出
		    //**分页实现代码
	
    		//查询数据，实现分页
    		$where=array('nv_id'=>array('IN', $cids));//查询新闻表nv_id字段和$cids相等时的数据
    		
    		$m=D('News');
    		$field='g.id,g.news_content,g.news_title,g.news_pic,g.news_sort';
    		$topcate[$k]['news']=$m->alias('g')->join('LEFT JOIN tuzi_attr i ON i.id = g.news_type')->join('LEFT JOIN tuzi_column c ON c.id = g.nv_id')->field($field)->where($where)->where("news_dell=0")->order('news_sort,id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
    		$result=$topcate[$k]['news'];
    		//查询新闻表下的所有文章   查询新闻数据赋值给字段news
//     		dump($result);
//     		exit;
		}
		//循环过滤html src标签和截取中文函数  用于摘要简介  substr_ext函数写在commonaction.class.php中
		foreach($result as $k2 => $v2){
			$result[$k2]['news_content'] = $this->substr_ext($v2['news_content'], 0, 160, 'utf-8',"");
		}
		foreach($result as $k2 => $v2){
			$result[$k2]['news_title'] = $this->substr_ext($v2['news_title'], 0, 220, 'utf-8',"");
		}
		foreach($result as $k2 => $v2){
			$result[$k2]['news_pic'] = __ROOT__.$v2['news_pic'];
		}
		//全局导航栏目的url，可根据手机站或者pc站石洞适配url
		$modlu=__ACTION__ ;
		strpos($modlu, "mobile");
		if (strpos($modlu, "mobile")==''){//如果url中不存在mobile（不区分大小写）
			//文章url
			foreach($result as $k3 => $v3){
				$result[$k3]['url'] = __APP__.'/'.CONTROLLER_NAME.'/'.detail.'/'.'id'.'/'.$v3['id'];
			}
		}else {
			//文章url
			foreach($result as $k3 => $v3){
				$result[$k3]['url'] = __APP__.'/'.'mobile'.'/'.CONTROLLER_NAME.'/'.detail.'/'.'id'.'/'.$v3['id'];
			}
		}
// 		dump($result);
// 		exit;

		//**分页实现代码
		$this->assign('page',$show);// 赋值分页输出
		//**分页实现代码
		$this->assign('vlist',$result);
		//dump($topcate);
		//dump($result);
		//exit;
			
		$this->display();
	}
}
