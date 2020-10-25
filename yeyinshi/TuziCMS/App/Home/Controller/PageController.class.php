<?php
/*******************************************************************************
* [TuziCMS] 兔子CMS
* @Copyright (C) 2014-2015  http://tuzicms.com   All rights reserved.
* @Team  Yejiao.net
* @Author: 秦大侠 QQ:176881336
* @Licence http://www.tuzicms.com/license.txt
*******************************************************************************/
namespace Home\Controller;
use Think\Controller;
use Common\Lib\String; //引入类函数
use Common\Lib\Category; //引入类函数
use Common\Lib\Common; //引入类函数
class PageController extends CommonController {
	/**
	 * 单页模型控制器方法
	 */
	public function group(){
		$id=I('get.id');//类别ID
		if (!preg_match('/^\d+$/i', $id)){
			$this->error('url参数错误');
			exit;//仿制用户恶意输出url参数
		}
// 		dump($id);
// 		exit;
		
		//**获取当前栏目的信息
		$topcate=D('Column')->where("id=$id")->order('column_sort')->relation(true)->select();
		foreach($topcate as $k2 => $v2){
			$topcate[$k2]['column_content'] = htmlspecialchars_decode($v2['column_content']);
		}
// 		dump($topcate);
// 		exit;
		
		//获取当前栏目的名称
		$column_name=$topcate[0]['column_name'];
		$this->assign('vcolumn',$column_name);
// 		dump($column_name);
// 		exit;
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
		
		$if=$topcate[0]['f_id'];
		if ($if==0){
			//是父级栏目的情况
			//**获取栏目的下级所有子栏目
			import('Class.Category',APP_PATH);//文件在当前项目目录下的class目录
			$m=D('Column')->order('column_sort ASC')->relation(true)->select();
			$m=Category::getChilds($m,$id);//获取id所有的下级栏目的信息
			
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
// 			var_dump($m);
// 			exit;
			$this->assign('navlist',$m);
		}else {
			//是二级栏目的情况
			
			//**获取栏目的上级所有子栏目
			$m=D('Column')->order('column_sort ASC')->relation(true)->select();
			//$m=Category::getChilds($m,$id);//获取id所有的下级栏目的信息
			$m=Category::getParents($m,$id);//获取id所有的下级栏目的信息
			
			//**获取栏目父级id,并且查询下级所有子栏目
			$cid=$m['0']['id'];
			$m=D('Column')->order('column_sort ASC')->relation(true)->select();
			$m=Category::getChilds($m,$cid);//获取id所有的下级栏目的信息
			
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
// 			var_dump($m);
// 			exit;
			$this->assign('navlist',$m);
		}
		
		//**获取子栏目的上级栏目,全局导航
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
// 		var_dump($m);
// 		exit;
		$this->assign('topnavlist',$m);
		
		//**全局导航 判断设置最后一个没有尖括号
		$last=count($m)-1;
		$this->assign('last',$last);
		//echo $last;
		//exit;

		$this->display();
	}
}
