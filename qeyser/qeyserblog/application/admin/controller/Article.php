<?php
namespace app\admin\controller;
use think\Validate;
use app\admin\controller\Base;

/**.-------------------------------------------------------------------
 * |    Software: [QeyserBlog]
 * |    Site: www.qeyser.net
 * |-------------------------------------------------------------------
 * |    Author: 凯萨尔 <125790757@qq.com>
 * |    WeChat: 15999230034
 * |    Copyright (c) 2017-2018, www.qeyser.net . All Rights Reserved.
 * '-------------------------------------------------------------------*/

class Article extends Base{
	/**
	 * 文章列表
	 */
	public function index(){
		$articles = db('article')->alias('a')->join('category','a.cid=category.cid')->field('a.aid,a.title,a.time,cname')->order('aid desc')->paginate(10);
		$this->assign('articles',$articles);
		return $this->fetch();
	}
	/**
	 *  文章添加
	 */
	public function add(){
		if(request()->isPost()){
			$data = [
				'title' => input('title'),
				'cid' => input('cid'),
				'keywords' => input('keywords'),
				'description' => input('description'),
				'type' => input('type') ? 1 : 0,
				'content' => input('content'),
				'sort' => input('sort'),
				'time' => time(),
				'click' => input('click')
			];
			if($_FILES['pic']['tmp_name']){
				$file = request()->file('pic');
				$info = $file->move(ROOT_PATH . DS . 'uploads/attachment');
				$data['pic'] = '/uploads/attachment/' . $info->getSaveName();
			}
			$result = db('article')->insert($data);
			if($result){
				$this->success('يازما قوشۇش مۇۋاپىقيەتلىك بولدى!','article/index');
			}else{
				$this->error('يازما قوشۇش مەغلۇب بولدى!');
			}
		}
		$cats = db('category')->order('sort asc')->select();
		$this->assign('cats',$cats);
		return $this->fetch();
	}
	/**
	 *  文章编辑
	 */
	public function edit(){
		$aid = input('aid');
		$arts = db('article')->find($aid);
		$pics = ROOT_PATH . $arts['pic'];
		$cats = db('category')->order('sort asc')->select();
		if(request()->isPost()){
			$data = [
				'aid' => input('aid'),
				'title' => input('title'),
				'cid' => input('cid'),
				'keywords' => input('keywords'),
				'description' => input('description'),
				'type' => input('type') ? 1 : 0,
				'content' => input('content'),
				'sort' => input('sort'),
				'time' => time(),
				'click' => input('click')
			];
			if($_FILES['pic']['tmp_name']){
				$file = request()->file('pic');
				$info = $file->move(ROOT_PATH . DS . 'uploads/attachment');
				if($pics != null){
					unlink($pics);
				}
				$data['pic'] = '/uploads/attachment/'. $info->getSaveName();
			}
			$result = db('article')->update($data);
			if($result){
				$this->success('يازما تەھرىرلەش مۇۋاپىقيەتلىك بولدى!','article/index');
			}else{
				$this->error('يازما تەھرىرلەش مەغلۇب بولدى!');
			}
		}
		$this->assign(['arts'=>$arts,'cats'=>$cats]);
		return $this->fetch();
	}
	/**
	 * 删除文章
	 */
	public function del(){
		$aid = input('aid');
		if($aid != 0){
			//删除缩略图
			$file = db('article')->where('aid',$aid)->value('pic');
			if($file != null){
				$file = ROOT_PATH . $file;
				unlink($file);
			}
			//删除文章
			if(db('article')->delete($aid)){
				$this->success('يازما ئۆچۈرۈش مۇۋاپىقيەتلىك بولدى!','article/index');
			}else{
				$this->error('يازما ئۆچۈرۈش مەغلۇب بولدى!');
			}
		}else{
			$this->error('سىز ئۆچۈرمەكچى بولغان يازما تېپىلمىدى!');
		}
	}
}