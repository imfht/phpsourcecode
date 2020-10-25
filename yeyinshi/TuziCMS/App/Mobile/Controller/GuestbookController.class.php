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
class GuestbookController extends Controller {
	/**
	 * 留言板展示
	 */
	public function index(){
		import('Class.Common',APP_PATH);//文件在当前项目目录下的class目录
		$id=I('get.id');//类别ID
		//没有栏目id时，定义栏目的名字，用于全局导航
		if ($id==null){
			$this->assign('ifid',not);
			$url='guestbook';//定义url的后缀名字
		}
		//dump($url);
		//exit;
	
		//**获取当前栏目的信息
		$m=D('Column');
		$data['column_ename']=$url;
		$topcate=$m->where($data)->select();
		$this->assign('nav_list',$topcate);	
		//dump($topcate);
		//exit;
		
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
		
		//模板显示文章
		$m=D('Guestbook');
		//$arr=$m->select();
// 		dump($arr);
// 		exit;
    	//**分页实现代码
    	$count=$m->count();// 查询满足要求的总记录数
    	$Page = new \Think\Page($count,3);// 实例化分页类 传入总记录数和每页显示的记录数(25)
    	$show = $Page->show();// 分页显示输出
    	//**分页实现代码
		$arr=$m->where("gb_dell=0")->order('gb_addtime desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		//截取部分标题
		foreach($arr as $k2 => $v2){
			$arr[$k2]['title'] = Common::substr_ext($v2['title'], 0, 20, 'utf-8',"");
		}
		
		foreach($arr as $k2 => $v2){
			$arr[$k2]['content'] = htmlspecialchars_decode($v2['content']);
		}
// 		dump($arr);
// 		exit;
		
		//**分页实现代码
		$this->assign('page',$show);// 赋值分页输出
		//**分页实现代码
		$this->assign('vlist',$arr);
		$this->display();
	}
	
	/**
	 * 提交留言处理
	 */
	public function do_guestbook(){
// 		dump($_POST);
// 		exit;
		//防止外界恶意输入
		$content=I('post.gb_content');
		$verify=I('post.verify');
		if (!$content || !$verify){
			$this->error('表单信息错误！');
		}
		
		$m=D('Guestbook'); //先读取News数据库表模型文件
		if (!$m->create()){
	    		$this->error($m->geterror());
	    }

		//**需要另外添加到数据库的在这里填写
		//注意，非表单自动创建提交的数据，要写在if判断之后才能提交到数据库
		$m->gb_ip=get_client_ip();
		//**上传附件代码  附件保存在网站目录里  附件名保存在数据库里面
		//$m->filename = $info[0]['savename']; // 将附件的名字保存在数据库的filename字段里
		//**上传附件代码  附件保存在网站目录里  附件名保存在数据库里面
		//**需要另外添加到数据库的在这里填写
		$arr=$m->add(); //自动修改 不需要定义id 因为post表单中已经有
		if ($arr){
			$this->success('添加成功');
		}else {
			$this->error('添加失败');
			//$this->error($m->geterror());
		}
	}

    
}
