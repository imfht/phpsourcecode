<?php 
namespace app\index\controller;
use app\index\controller\Base;

/**.-------------------------------------------------------------------
 * |    Software: [QeyserBlog]
 * |    Site: www.qeyser.net
 * |-------------------------------------------------------------------
 * |    Author: 凯撒 <125790757@qq.com>
 * |    WeChat: 15999230034
 * |    Copyright (c) 2017-2018, www.qeyser.net . All Rights Reserved.
 * '-------------------------------------------------------------------*/

class Search extends Base{
	/**
	 * 根据标签搜索
	 */
	public function tags(){
		// 根据关键词搜索
		$article = db('article')->where('keywords','like','%'.input('tag').'%')->paginate(3);
		$this->assign('article',$article);
		return $this->fetch('search');
	}

	/**
	 * 根据标题搜索
	 */
	public function keywords(){
		// 根据标题搜索
		$article = db('article')->where('title','like','%'.input('keywords').'%')->paginate(3);
		$this->assign('article',$article);
    	return $this->fetch('search');
	}

}