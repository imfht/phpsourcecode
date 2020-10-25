<?php
namespace app\admin\controller;
use app\admin\controller\Base;

/**.-------------------------------------------------------------------
 * |    Software: [QeyserBlog]
 * |    Site: www.qeyser.net
 * |-------------------------------------------------------------------
 * |    Author: 凯萨尔 <125790757@qq.com>
 * |    WeChat: 15999230034
 * |    Copyright (c) 2017-2018, www.qeyser.net . All Rights Reserved.
 * '-------------------------------------------------------------------*/

class Comment extends Base{
	/**
	 * 评论列表
	 */
	public function index(){
		$articles = db('comment')->alias('c')->join('article','c.aid=article.aid')->field('id,c.aid,title,add_time,c.content,status')->order('add_time desc')->paginate(10);
		$this->assign('articles',$articles);
		return $this->fetch();
	}
	/*
     * 评论审核/取消审核
     */
	public function state()
	{
		$id=input('id');
		//判断当前状态情况
		$status=db('comment')->where(array('id'=>$id))->value('status');
		if($status==1){
			$statedata = array('status'=>0);
			db('comment')->where(array('id'=>$id))->setField($statedata);
			$this->success('تەستىقلاش بىكار قىلىندى!');
		}else{
			$statedata = array('status'=>1);
			db('comment')->where(array('id'=>$id))->setField($statedata);
			$this->success('تەستىقلاندى، تېما ئاستىدا كۆرۈلىدۇ!');
		}
	}
	/**
	 * 删除文章
	 */
	public function del(){
		$id = input('id');
		//删除文章
		if(db('comment')->delete($id)){
			$this->success('سۆز ئۆچۈرۈش مۇۋاپىقيەتلىك بولدى!','comment/index');
		}else{
			$this->error('سۆز ئۆچۈرۈش مەغلۇب بولدى!');
		}

	}
}