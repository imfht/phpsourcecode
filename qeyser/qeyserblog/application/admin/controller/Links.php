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

class Links extends Base{
	/**
	 * 友情连接列表
	 */
	public function index(){
		$links = db('links')->order('sort')->select();
		$this->assign('links',$links);
		return $this->fetch();
	}
	/**
	 * 添加友情连接
	 */
	public function add(){
		if(request()->isPost()){
			$data = [
				'name' => input('name'),
				'url'  => input('url'),
				'sort' => input('sort'),
				'time' => time(),
				'type' => input('type') ? 1 : 0
			];
			// 验证输入内容
			$rule = [
                ['name','require','بىكەت نامىنى كىرگۈزڭ !'],
                ['url','url','تور ئادىرىسىنى توغرا كىرگۈزۈڭ !'],
            ];
            $validate = new Validate($rule);
            $result   = $validate->check($data);
            // 判断是否通过
			if($result == true){
				db('links')->insert($data);
				$this->success('قوشۇش مۇۋاپىقيەتلىك بولدى !','admin/links/index');
			}else{
				// 验证失败 输出错误信息
                $this->error($validate->getError());
			}
		}
		return $this->fetch();
	}
	/**
	 * 编辑友情连接
	 */
	public function edit(){
		if(request()->isPost()){
			$data = [
				'id'   => input('id'),
				'name' => input('name'),
				'url'  => input('url'),
				'sort' => input('sort'),
				'type' => input('type') ? 1 : 0
			];
			// 验证输入内容
			$rule = [
                ['name','require','بىكەت نامىنى كىرگۈزڭ !'],
                ['url','url','تور ئادىرىسىنى توغرا كىرگۈزۈڭ !']
            ];
            $validate = new Validate($rule);
            $result   = $validate->check($data);
            // 判断是否通过
			if($result == true){
				db('links')->update($data);
				$this->success('تەھرىرلەش مۇۋاپىقيەتلىك بولدى !','admin/links/index');
			}else{
				// 验证失败 输出错误信息
                $this->error($validate->getError());
			}
		}
		$link = db('links')->where('id',input('id'))->find();
		$this->assign('link',$link);
		return $this->fetch();
	}
	/**
	 * 删除管理员
	 */
	public function del(){
		$id = input('id');
		if($id != 0){
			$result = db('links')->delete($id);
			if($result){
				$this->success('ئۆچۈرۈش مۇۋاپىقيەتلىك بولدى！','admin/links/index');
			}else {
	            $this->error('ئۆچۈرۈش مەغلۇپ بولدى！');
	        }
    	}else{
    		$this->error('سىز ئۆچۈرمەكچى بولغان ئۇچۇر تېپىلمىدى!');
    	}
	}	
}