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

class Category extends Base{
	/**
	 * 栏目列表
	 */
	function index(){
		$cates = db('category')->order('sort')->select();
		$this->assign('cates',$cates);
		return $this->fetch();
	}
	/**
	 * 添加栏目
	 */
	function add(){
		if (request()->isPost()) {
			$data=[
				'cname'=>input('cname'),
				'keywords'=>input('keywords'),
				'description'=>input('description'),
				'sort'=>input('sort'),
				'type'=>input('type') ? 1 : 0
			];
			// 验证输入内容
			$rule = [
                ['cname','require','بىكەت نامىنى كىرگۈزڭ !']
            ];
            $validate = new Validate($rule);
            $result   = $validate->check($data);
            // 判断是否通过
			if($result == true){
				db('category')->insert($data);
				$this->success('تۈر قوشۇش مۇۋاپىقيەتلىك بولدى !','index');
			}else{
				// 验证失败 输出错误信息
                $this->error($validate->getError());
			}
		}
		return $this->fetch();
	}
	/**
	 * 编辑栏目
	 */
	function edit(){
		$cid = input('cid');
		$cate = db('category')->find($cid);  
		if(request()->isPost()) {
			$data=[
				'cid'=>input('cid'),
				'cname'=>input('cname'),
				'keywords'=>input('keywords'),
				'description'=>input('description'),
				'sort'=>input('sort'),
				'type'=>input('type') ? 1 : 0
			];
			// 验证输入内容
			$rule = [
                ['cname','require','بىكەت نامىنى كىرگۈزڭ !']
            ];
            $validate = new Validate($rule);
            $result   = $validate->check($data);
            // 判断是否通过
			if($result == true){
				db('category')->update($data);
				$this->success('تۈر تەھرىرلەش مۇۋاپىقيەتلىك بولدى !','index');
			}else{
				// 验证失败 输出错误信息
                $this->error($validate->getError());
			}
		}
		$this->assign('cate',$cate);
		return $this->fetch();
	}
	/**
	 * 删除栏目
	 */
	public function del(){
		$cid = input('cid');
		if($cid != 0){
			$data = db('article')->where('cid',$cid)->count();
			if($data == 0) {
				db('category')->delete($cid);
				$this->success('تۈر ئۆچۈرۈش مۇۋاپىقيەتلىك بولدى!','index');
			}else{
				$this->error('بۇ تۈردە يازما بار، ئۆچۈرۈشكە بولمايدۇ!');
			}
		}else{
			$this->error('سىز ئۆچۈرمەكچى بولغان ئۇچۇر تېپىلمىدى!');
		}
	}
}